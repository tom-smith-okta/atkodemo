<script>

	$("#widget").hide();

	var oktaSignIn = new OktaSignIn({
		baseUrl: '%--oktaBaseURL--%',
		logo: '%--logo--%',
		features: {
			multiOptionalFactorEnroll: true,
			smsRecovery: true
		},
	  
		// OIDC options
		clientId: '%--clientId--%',
		redirectUri: '%--redirectUri--%',

		authScheme: 'OAUTH2',
		authParams: {
			responseType: 'id_token',
			responseMode: 'okta_post_message',
			scopes: [
				'openid',
				'email',
				'profile',
				'address',
				'phone'
			]
		},

		%--idpJS--%

	});

	</script>