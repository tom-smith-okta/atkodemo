<script>

	function displayWidget() {

		var oktaSignIn = new OktaSignIn({
			baseUrl: '%oktaBaseURL%',
			logo: '%logo%',
			features: {
				multiOptionalFactorEnroll: true,
				smsRecovery: true
			},
		  
			redirectUri: '%redirectURL%',

		});

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

		oktaSignIn.renderEl(
			{ el: '#widgetBasic'},
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
		displayWidget();
	}

</script>