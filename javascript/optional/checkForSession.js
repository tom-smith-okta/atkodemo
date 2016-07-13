function checkForSession() {
	var baseUrl = 'https://tomco.okta.com';

	var oktaSignIn = new OktaSignIn({baseUrl: baseUrl});

	oktaSignIn.session.exists(function (exists) {

		if (exists) {
	  		// There is an active session, at least according to session.exists
	  		console.log("there is an active session - according to session.exists()");

	  		oktaSignIn.session.get(function (res) {
	  			
				if (res.status !== 'INACTIVE') {

					console.log("there is an active session - according to session.get()");

					console.log("this is the session object:");

					console.dir(res);

					// put the okta session ID in a local cookie in case
					// userId too, for UI purposes
					document.cookie = "oktaCookieSessionID=" + res.id;
					document.cookie = "oktaUserID" + res.userId;

		  		} else {
		    		console.log("there is *no* active session - according to session.get()");
		  		}
			});
	  	} else {
	    	// No active session found
	    	console.log("there is *no* active session - according to session.exists()");
	  	}
	});
}

window.onload = checkForSession;