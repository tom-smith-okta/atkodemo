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