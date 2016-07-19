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
		redirectUri: '%sessionManager%',

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

					window.location = "%sessionManager%";

				}
			}
		);
	}

	window.onload = displayWidget;

</script>