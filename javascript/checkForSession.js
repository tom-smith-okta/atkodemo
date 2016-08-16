<script>
function checkForSession() {
	// var baseUrl = 'https://tomco.okta.com';

	var baseUrl = "%oktaBaseURL%";

	var oktaSignIn = new OktaSignIn({baseUrl: baseUrl});

	oktaSignIn.session.exists(function (exists) {

		if (exists) {
	  		// There is an active session, according to session.exists
	  		console.log("there is an active session - according to session.exists()");

	  		oktaSignIn.session.get(function (res) {
	  			
				if (res.status !== 'INACTIVE') {

					console.log("there is an active session - according to session.get()");

					console.log("this is the session object:");

					console.dir(res);

					console.log("this is the okta session ID: " + res.id);
					console.log("this is the user ID: " + res.userId);

					// window.location = "/atkodemo/home.php?oktaCookieSessionID=" + res.id + "&oktaUserID=" + res.userId;
					window.location = "%homePage%" + "?oktaCookieSessionID=" + res.id + "&oktaUserID=" + res.userId;

		  		} else {
		    		console.log("there is *no* active session - according to session.get()");

		    		// window.location = "/atkodemo/home.php";
					window.location = "%homePage%";

		  		}
			});
	  	} else {

	  		// No active session found
	    	console.log("there is *no* active session - according to session.exists()");

	  		oktaSignIn.session.get(function (res) { 

	  			if (res.status == 'MFA_ENROLL') {
	  				console.log ("the status is MFA_ENROLL");

	  				console.log("the result is: ");

	  				console.dir(res);

	  				window.location = "/atkodemo/login.php";
	  			}
	  			else { console.log("can't figure out the user's status."); }

	  		});

	    	// window.location = "/atkodemo/home.php";
	    	window.location = "%homePage%";
	  	}
	});
}

window.onload = checkForSession;

</script>