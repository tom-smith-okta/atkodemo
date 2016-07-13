function checkOktaSession() {

	var baseUrl = 'https://tomco.okta.com';

	var oktaSignIn = new OktaSignIn({baseUrl: baseUrl});
	
	oktaSignIn.renderEl(
		{ el: '#okta-login-container' },
	  	function (res) {
	    	if (res.status === 'SUCCESS') { 

				console.log("the session token is: " + res.session.token);

				// document.cookie = "oktaCookieSessionID=" + res.id;
				// document.cookie = "oktaUserID=" + res.userId;

				// res.session.setCookieAndRedirect('http://localhost:8888/atkotravel/setCookie.php');

				res.session.setCookieAndRedirect('http://localhost:8888/atkotravel/index.html');
			}
		}
	);
}

window.onload = checkOktaSession;