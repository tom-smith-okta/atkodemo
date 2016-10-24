<script>

function showWidget() {

	// alert("clicked show widget");

    $("#oktaWidget").show();

    $("#login").attr("onclick", "hideWidget()");

}

function hideWidget() {
	$("#oktaWidget").hide();

	$("#login").attr("onclick", "showWidget()");

}

window.onload = function() {

	loadWidget();

	// checkForSession();

	// hideWidget();

	// showWidget();
	// $("#oktaWidget").hide();

	// $("#login").attr("onclick", "showWidget()");


	// alert("in the onload function");

	getDate();
	// displayWidget();
}
</script>