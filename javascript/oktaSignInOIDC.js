<script>

	function displayWidget() {

		var oktaSignIn = new OktaSignIn({
			baseUrl: '%oktaBaseURL%',
			logo: '%logo%',
			features: {
				rememberDevice: false,
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

		oktaSignIn.renderEl(
			{ el: '#okta-login-container' },
		  	function (res) {
		    	if (res.status === 'SUCCESS') { 

					console.log("the OIDC token is: ");

					console.dir(res);

					// window.location = "http://localhost:8888/atkodemo/";
					// doesn't seem like I should need to do this bc I 
					// have already defined redirectURI above.
					// worth some experimenting.
					window.location = "%redirectURL%";

				}
				else {
					console.log("something went wrong.");

					console.dir(res);
				}
			}
		);
	}

	window.onload = displayWidget;

</script>