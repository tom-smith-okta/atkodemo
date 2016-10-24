<script>

function renderWidgetOIDC() {

    $("#oktaWidget").hide();

	oktaSignIn.renderEl(
		{ el: '#oktaWidget'},
	  	function (res) {

	  		console.log("the res.status is: " + res.status);

	  		if (res.status == "SUCCESS") {

	  			$("#oktaWidget").hide();
	  			
	  			console.log("authentication successful.");
	  			console.log("user now has an active session.");
	  			console.log("id_token:" + res.idToken);
	  			console.log("claims:");
	  			console.dir(res.claims);

	  			localStorage.setItem("given_name", res.claims.given_name);

	  			setMenu("authenticated", res.claims.sub);

	  		}
	  		else {
	  			console.log("the user was not authenticated.");
	  			console.log("the error is: " + res.status);
	  		}
		}
	);
}

function showWidget() {

    $("#oktaWidget").show();

    $("#login").attr("onclick", "hideWidget()");

}

function hideWidget() {
	$("#oktaWidget").hide();

	$("#login").attr("onclick", "showWidget()");

}

window.onload = function() {
	getDate();
	renderWidgetOIDC();
}

</script>