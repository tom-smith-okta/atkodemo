<script>

// function checkForSession() {
/************************************/
// Check for an Okta session
// and update menu accordingly
/************************************/

oktaSignIn.session.exists(function (exists) {
	if (exists) {
		console.log("there is an active session.");
		console.log("---------------------------");

		// getting the fname from the server instead of local storage
		// to accomodate the use-case where a new user registers and
		// then gets redirected to this page. There's probably a better
		// way.
		oktaSignIn.session.get(function (res) {
  			$.ajax({
	            type: "GET",
	            dataType: 'json',
	            url: "%apiHome%/users/" + res.userId,

	            xhrFields: {
	                withCredentials: true
	            },
	            success: function (data) {

	            	console.dir(data);

	            	console.log("the given_name is: " + data.profile.firstName);

	                localStorage.setItem("given_name", data.profile.firstName);

	                setMenu("authenticated", data.id);

	            },
	            error: function (textStatus, errorThrown) {
	                console.log('error retrieving session: ' + JSON.stringify(textStatus));
	                console.log(errorThrown);
	            },
	            async: true
        	});
  		});
	}
	else {
		console.log("there is not an active session.");
		console.log("-------------------------------");
		setMenu("anon");
	}
});

// }

</script>