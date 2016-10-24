<script>

/**************************/
// render the Okta widget
/**************************/

function renderWidget() {

	oktaSignIn.renderEl(
		{ el: '#oktaWidget'},
	  	function (res) {

	  		console.log("the res.status is: " + res.status);

	  		if (res.status == "SUCCESS") {

	  			res.session.setCookieAndRedirect('%redirectURL%');

	  		}
	  		else {
	  			console.log("the user was not authenticated.");
	  			console.log("the error is: " + res.status);
	  		}
		}
	);
}

window.onload = function() {
	renderWidget();
}

</script>