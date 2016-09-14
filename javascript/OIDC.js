
<script>

	function setMenu(authState, userID) {

		var menu;

		if (authState == "authenticated") {
			menu = "<li><a href = '#' onclick = 'signout()'>Log out</a></li>";
		
			menu += "<li><a href='%salesforce%' target = '_blank'>Chatter</a></li>";

			$.ajax({
	            type: "GET",
	            dataType: 'json',
	            url: "%apiHome%/users/" + userID + "/appLinks",

	            xhrFields: {
	                withCredentials: true
	            },
	            success: function (data) {
	            	console.log("the apps object is: ");
	            	console.dir(data);

	            	var apps = "";

	            	for (var i = 0, len = data.length; i < len; i++) {
  						console.log("the value of i is: " + i);
  						console.log("the app name is: " + data[i].appName);

  						apps += "<li><a href='" + data[i].linkUrl + "' target = '_blank'>" + data[i].appName + "</a></li>"; 
					}
					$("#appLinks").html(apps);

	            },
	            error: function (textStatus, errorThrown) {
	                console.log('error retrieving session: ' + JSON.stringify(textStatus));
	                console.log(errorThrown);
	            },
	            async: true
        	});

        	console.log("outside the function, the value of data is: ");
        	console.dir(data);

			if (localStorage.getItem("given_name")) {
				menu += "<li><a href='#'>Welcome, " + localStorage.getItem("given_name") + "!</a></li>";
			}

		}
		else {
			menu = "<li><a href = '#' id = 'login' onclick = 'showWidget()'>Log in</a></li>";
			menu += "<li><a href = 'register.php'>Register</a></li>";
		}

		$("#authLinks").html(menu);

	}

	function displayWidget() {

		$("#widget").hide();

		var oktaSignIn = new OktaSignIn({
			baseUrl: '%oktaBaseURL%',
			logo: '%logo%',
			features: {
				multiOptionalFactorEnroll: true,
				smsRecovery: true
			},

			labels: {
				'primaryauth.password.tooltip': 'passwords must have at least 8 characters, a lowercase letter, an uppercase letter, a number, no parts of your username'
			},
		  
			// OIDC options
			clientId: '%clientId%',
			redirectUri: '%redirectURL%',

			authScheme: 'OAUTH2',
			authParams: {
				responseType: 'id_token',
				responseMode: 'okta_post_message',
				scope: [
					'openid',
					'email',
					'profile',
					'address',
					'phone'
				]
			},
			idpDisplay: 'PRIMARY',

			idps: %idps%
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
			            	console.log("the given_name is: " + data.profile.firstName);

			                localStorage.setItem("given_name", data.profile.firstName);

			                setMenu("authenticated", res.userId);


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
			{ el: '#widget'},
		  	function (res) {

		  		console.log("the res.status is: " + res.status);

		  		if (res.status == "SUCCESS") {

		  			$("#widget").hide();
		  			
		  			console.log("authentication successful.");
		  			console.log("user now has an active session.");
		  			console.log("id_token:" + res.idToken);
		  			console.log("claims:");
		  			console.dir(res.claims);

		  			localStorage.setItem("given_name", res.claims.given_name);

		  			setMenu("authenticated");

		  		}
		  		else {
		  			console.log("the user was not authenticated.");
		  			console.log("the error is: " + res.status);
		  		}
			}
		);
	}

    function showWidget() {

        $("#widget").show();

        $("#login").attr("onclick", "hideWidget()");

    }

    function hideWidget() {
    	$("#widget").hide();

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
    	// setMenu();
		getDate();
		displayWidget();

	}

</script>