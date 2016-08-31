function getDate() {
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth();

	var yyyy = today.getFullYear();

	var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

	var monthName = months[mm];

	var dateString = monthName + " " + dd + ", " + yyyy;

	$(".published").html(dateString);

}