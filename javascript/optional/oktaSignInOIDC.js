function checkOktaSession() {

	var oktaSignIn = new OktaSignIn({
		baseUrl: 'https://tomco.okta.com',
		features: {
			rememberDevice: false,
		},
	  
	// OIDC options
	clientId: 'YYUAPHIAj3JPPO6yJans',
	redirectUri: 'http://localhost:8888/atkotravel/temp.html',
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
	idps: [{
		'type': 'FACEBOOK',
		'id': '0oa1w1pmezuPUbhoE1t6'
		}]
	});

	oktaSignIn.renderEl(
		{ el: '#okta-login-container' },
	  	function (res) {
	    	if (res.status === 'SUCCESS') { 

				console.log("the OIDC token is: ");

				console.dir(res);

				window.location = "http://localhost:8888/atkotravel/index.html";

			}
		}
	);
}

window.onload = checkOktaSession;