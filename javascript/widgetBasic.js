<script>

	function setMenu(authState, userID) {

		var menu;

		if (authState == "authenticated") {
			menu = "<li><a href = '#' onclick = 'signout()'>Log out</a></li>";
		
			$.ajax({
	            type: "GET",
	            dataType: 'json',
	            url: "%apiHome%/users/" + userID + "/appLinks",

	            xhrFields: {
	                withCredentials: true
	            },
	            success: function (data) {

	            	var whitelist = %appsWhitelist%;

	            	var apps = "";

	            	for (var i = 0, len = data.length; i < len; i++) {
	            		console.log("found an app: " + data[i].appName);
  						if (whitelist.indexOf(data[i].appName) != -1) {
  							apps += "<li><a href='" + data[i].linkUrl + "' target = '_blank'>" + data[i].appName + "</a></li>";
  						}
					}

					menu += apps;

					$("#authLinks").html(menu);
 
	            },
	            error: function (textStatus, errorThrown) {
	                console.log('error retrieving session: ' + JSON.stringify(textStatus));
	                console.log(errorThrown);
	            },
	            async: true
        	});

			if (localStorage.getItem("given_name")) {
				menu += "<li><a href='#'>Welcome, " + localStorage.getItem("given_name") + "!</a></li>";
			}

		}
		else {
			menu = "<li><a href = '#' id = 'login' onclick = 'showWidget()'>Log in (OIDC)</a></li>";
			menu += "<li><a href = 'login.php'>Log in (basic)</a></li>";
			menu += "<li><a href = 'register.php'>Register</a></li>";
		}

		$("#authLinks").html(menu);

	}

	function displayWidget() {

		var oktaSignIn = new OktaSignIn({
			baseUrl: '%oktaBaseURL%',
			logo: '%logo%',
			features: {
				multiOptionalFactorEnroll: true,
				smsRecovery: true
			},
		  
			redirectUri: '%redirectURL%',

		});

		oktaSignIn.session.exists(function (exists) {
			if (exists) {
				console.log("there is an active session.");
				console.log("---------------------------");

				// getting the fname from the server instead of local storage
				// to accomodate the use-case where a new user registers and
				// then gets redirected to this page. There's probably a better
				// way.
				oktaSignIn.session.get(function (res) {
		  			$.ajax({
			            type: "GET",
			            dataType: 'json',
			            url: "%apiHome%/users/" + res.userId,

			            xhrFields: {
			                withCredentials: true
			            },
			            success: function (data) {

			            	console.dir(data);

			            	console.log("the given_name is: " + data.profile.firstName);

			                localStorage.setItem("given_name", data.profile.firstName);

			                setMenu("authenticated", data.id);

			            },
			            error: function (textStatus, errorThrown) {
			                console.log('error retrieving session: ' + JSON.stringify(textStatus));
			                console.log(errorThrown);
			            },
			            async: true
		        	});
		  		});
			}
			else {
				console.log("there is not an active session.");
				console.log("-------------------------------");
				setMenu("anon");
			}
		});

		oktaSignIn.renderEl(
			{ el: '#widgetBasic'},
		  	function (res) {

		  		console.log("the res.status is: " + res.status);

		  		if (res.status == "SUCCESS") {

		  			res.session.setCookieAndRedirect('%redirectURL%');

		  		}
		  		else {
		  			console.log("the user was not authenticated.");
		  			console.log("the error is: " + res.status);
		  		}
			}
		);
	}

    function showWidget() {

        $("#widgetBasic").show();

        $("#login").attr("onclick", "hideWidget()");

    }

    function hideWidget() {
    	$("#widgetBasic").hide();

    	$("#login").attr("onclick", "showWidget()");

    }

    function signout() {

    	console.log("attempting to sign out...");

        $.ajax({
            type: "DELETE",
            dataType: 'json',
            url: "%apiHome%/sessions/me",

            xhrFields: {
                withCredentials: true
            },
            success: function (data) {
                console.log('success deleting session');
                sessionStorage.removeItem('sessionToken');
            },
            error: function (textStatus, errorThrown) {
                console.log('error deleting session: ' + JSON.stringify(textStatus));
                console.log(errorThrown);
            },
            async: true
        });

		setMenu("anon");
    }

    window.onload = function() {
		getDate();
		displayWidget();
	}

</script>