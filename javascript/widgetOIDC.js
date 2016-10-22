<script>

	function displayWidget() {

		$("#widget").hide();

		var oktaSignIn = new OktaSignIn({
			baseUrl: '%oktaBaseURL%',
			logo: '%logo%',
			features: {
				multiOptionalFactorEnroll: true,
				smsRecovery: true
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

		  			setMenu("authenticated", res.claims.sub);

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

    window.onload = function() {
		getDate();
		displayWidget();
	}

</script>