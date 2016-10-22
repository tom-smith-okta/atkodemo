<script>

	function setMenu(authState, userID) {

		var menu = "";

		if (authState == "authenticated") {
			menu = "<li><a href = '#' onclick = 'signout()'>Log out</a></li>";
		
			$.ajax({
	            type: "GET",
	            dataType: 'json',
	            url: "%apiHome%/users/" + userID + "/appLinks",

	            xhrFields: {
	                withCredentials: true
	            },
	            success: function (data) {

	            	var whitelist = %appsWhitelist%;

	            	var apps = "";

	            	for (var i = 0, len = data.length; i < len; i++) {
	            		console.log("found an app: " + data[i].appName);
  						if (whitelist.indexOf(data[i].appName) != -1) {
  							apps += "<li><a href='" + data[i].linkUrl + "' target = '_blank'>" + data[i].appName + "</a></li>";
  						}
					}

					menu += apps;

					$("#authLinks").html(menu);
 
	            },
	            error: function (textStatus, errorThrown) {
	                console.log('error retrieving session: ' + JSON.stringify(textStatus));
	                console.log(errorThrown);
	            },
	            async: true
        	});

			if (localStorage.getItem("given_name")) {
				menu += "<li><a href='#'>Welcome, " + localStorage.getItem("given_name") + "!</a></li>";
			}

		}
		else {
			if (window.location.href.indexOf("login.php") == -1) {
				menu += "<li><a href = '#' id = 'login' onclick = 'showWidget()'>Log in (OIDC)</a></li>";
			}
			menu += "<li><a href = 'login.php'>Log in (basic)</a></li>";
			menu += "<li><a href = 'register.php'>Register</a></li>";
		}

		$("#authLinks").html(menu);

	}

</script>