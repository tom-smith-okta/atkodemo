
// gets the user's first name and the appropriate HTML
// for the top nav menu based on their session state
function getMenu(isSession) {

	if (isSession) {

		var baseUrl = 'https://tomco.okta.com';

		$.ajax({
		  url: baseUrl + '/api/v1/users/me',
		  type: 'GET',
		  xhrFields: { withCredentials: true },
		  accept: 'application/json'
		}).done(function(user) {

			firstName = user.profile.firstName;
			
			console.log("the user's first name is: " + firstName);

			$("#topMenu").html("\n<li><a href = 'https://tomco.okta.com/home/salesforce/0oapq5e1G3yk5Syeg1t5/46' target = '_blank'>Chatter</a></li>\n<li>Welcome, " + firstName + "!</li>");

		})
		.fail(function(xhr, textStatus, error) {
		  var title, message;
		  switch (xhr.status) {
		    case 403 :
		      title = xhr.responseJSON.errorSummary;
		      // message = 'Please login to your Okta organization before running the test';
		      message = 'Something went wrong with the Ajax call.';
		      break;
		    default :
		      title = 'Invalid URL or Cross-Origin Request Blocked';
		      message = 'You must explictly add this site (' + window.location.origin + ') to the list of allowed websites in your Okta Admin Dashboard';
		      break;
		  }
		  alert(title + ': ' + message);
		});
	}

	else {
		$("#topMenu").html("\n<li><a href = 'login.php'>Log In</a></li>\n<li><a href = 'register.php'>Register</a></li>\n");
	}
}

window.onload = isActiveSession;