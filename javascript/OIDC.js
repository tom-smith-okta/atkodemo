
<script>

	function setMenu(authState) {

		var menu;

		if (authState == "authenticated") {
			menu = "<li><a href = '#' onclick = 'signout()'>Log out</a></li>";
		
			menu += "<li><a href='%salesforce%' target = '_blank'>Chatter</a></li>";

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

function checkForSession() {
	var baseUrl = 'https://tomco.okta.com';

	// var baseUrl = "%oktaBaseURL%";

	var oktaSignIn = new OktaSignIn({baseUrl: baseUrl});

	oktaSignIn.session.exists(function (exists) {

		if (exists) {
	  		// There is an active session, according to session.exists
	  		console.log("there is an active session - according to session.exists()");

	  		oktaSignIn.session.get(function (res) {

	  			console.log("the raw return value is: ");

	  			console.dir(res);

	  			// console.log("the raw value for '_links' is: ");

	  			// console.log(res._links);

	  			$.ajax({
		            type: "GET",
		            dataType: 'json',
		            url: "%apiHome%/users/" + res.userId,

		            xhrFields: {
		                withCredentials: true
		            },
		            success: function (data) {
		                console.log('success getting user object');
		                console.log("the data is: ");
		                console.dir(data);
		                // sessionStorage.removeItem('sessionToken');
		            },
		            error: function (textStatus, errorThrown) {
		                console.log('error retrieving session: ' + JSON.stringify(textStatus));
		                console.log(errorThrown);
		            },
		            async: true
	        	});

	  			// var userObj = JSON.parse(res.user);

	  			// console.log(userObj);

	  			// var obj = JSON.parse(res);

	  			// console.dir(obj);

	  	// 		var jsonObj = JSON.stringify(res);

	  	// 		console.dir(jsonObj);

	  	// 		console.dir(res);

				// console.log("this is the okta session ID: " + res.id);
				// console.log("this is the user ID: " + res.userId);
			});
	  	}
	 });
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
			                console.dir(data);

			                localStorage.setItem("given_name", data.profile.firstName);

			            },
			            error: function (textStatus, errorThrown) {
			                console.log('error retrieving session: ' + JSON.stringify(textStatus));
			                console.log(errorThrown);
			            },
			            async: true
		        	});
		  		});
				setMenu("authenticated");
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
		getDate();
		// checkForSession();
		displayWidget();

	}

</script>

<script src = 'javascript/dates.js'></script>