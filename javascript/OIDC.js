
<script>

	var anonMenu = "<li id = 'loginItem'><a href = '#' id = 'login' onclick = 'showWidget()'>Log in</a></li>";
		anonMenu += "<li><a href = 'register.php'>Register</a></li>";

	var authenticatedMenu = "<li id = 'loginItem'><a href = '#' id = 'btnSignOut' onclick = 'signout()'>Log out</a></li>";

	function getMenu(given_name) {
		var authenticatedMenu = "<li id = 'loginItem'><a href = '#' id = 'btnSignOut' onclick = 'signout()'>Log out</a></li>";
		
		authenticatedMenu += "<li><a href='%salesforce%' target = '_blank'>Chatter</a></li>";

		authenticatedMenu += "<li><a href='#'>Welcome, " + given_name + "!</a></li>";

		return authenticatedMenu;
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
				$("#authLinks").html(authenticatedMenu);
			}
			else {
				console.log("there is not an active session.");
				console.log("-------------------------------");
				$("#authLinks").html(anonMenu);
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

		  			var menu = getMenu(res.claims.given_name);

		  			$("#authLinks").html(menu);

		  		}
			}
		);
	}

</script>

<script>

    function showWidget() {

        $("#widget").show();

        var loginLink = "<a href = '#' id = 'login' onclick = 'hideWidget()'>Log in</a>";

        $("#loginItem").html(loginLink);

    }

    function hideWidget() {
    	$("#widget").hide();

    	var loginLink = "<a href = '#' id = 'login' onclick = 'showWidget()'>Log in</a>";

        $("#loginItem").html(loginLink);

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

		$("#authLinks").html("<li id = 'loginItem'><a href = '#' id = 'login' onclick = 'showWidget()'>Log in</a></li>");
    }

</script>

<script src = 'javascript/dates.js'></script>

<script>
	window.onload = function() {
		getDate();
		displayWidget();
	}
</script>