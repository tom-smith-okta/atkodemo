<script>

/**************************/
// render the Okta widget
/**************************/

function renderWidget() {

	oktaSignIn.renderEl(
		{ el: '#oktaWidgetBasic'},
//		{ el: '#oktaWidget'},

		function (res) {

			console.log("the res.status is: " + res.status);

			if (res.status == "SUCCESS") {

				res.session.setCookieAndRedirect('%--redirectUri--%');

			}
			else {
				console.log("the user was not authenticated.");
				console.log("the error is: " + res.status);
			}
		}
	);
}

window.onload = function() {

	var pageName = location.pathname.substring(location.pathname.lastIndexOf("/") + 1);

	console.log("the page name is: " + pageName);

	if (pageName == "login.php") {
		renderWidget();
	}
}

// window.onload = function() {
// 	renderWidget();
// }

</script>