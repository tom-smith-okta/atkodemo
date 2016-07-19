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
			}
		);
	}

	window.onload = displayWidget;

</script>