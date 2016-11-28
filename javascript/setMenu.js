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

	            	var whitelist = %--appsWhitelist--%;

	            	var apps = "";

	            	var appName;

	            	// This could be a lot more efficient
	            	for (var i = 0, len = data.length; i < len; i++) {

	            		appName = data[i].appName;

	            		console.log("found an app: " + appName);

  						for (var myAppName in whitelist) {
							if (whitelist.hasOwnProperty(myAppName)) {

    							if (myAppName == appName) {
    								console.log("found a match between " + myAppName + " and " + appName);
    								apps += "<li><a href='" + data[i].linkUrl + "' target = '_blank'>" + whitelist[myAppName] + "</a></li>";
    							}

  							}
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
			// if (window.location.href.indexOf("login.php") > 0 || window.location.href.indexOf("register.php") > 0) {
			// }
			// else {
			// 	menu += "<li><a href = '#' id = 'login' onclick = 'showWidget()'>Log in (OIDC)</a></li>";				
			// }
			// menu += "<li><a href = 'login.php'>Log in (basic)</a></li>";
			// // menu += "<li><a href = '#menu'>Registration options</a></li>";
			// menu += "%regOptionsLink%";

			menu += "%--loginAndReg--%";
		}

		$("#authLinks").html(menu);

	}

</script>