
// This code is not live in the demo.
// Keeping it around for reference.
// it is the non-OIDC version of the okta sign-in widget

function checkOktaSession() {

	var baseUrl = 'https://tomco.okta.com';

	var oktaSignIn = new OktaSignIn({baseUrl: baseUrl});
	
	oktaSignIn.renderEl(
		{ el: '#okta-login-container' },
	  	function (res) {
	    	if (res.status === 'SUCCESS') { 

				console.log("the session token is: " + res.session.token);

				res.session.setCookieAndRedirect('http://localhost:8888/atkotravel/index.html');
			}
		}
	);
}

window.onload = checkOktaSession;