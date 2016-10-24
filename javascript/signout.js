<script>

function signout() {

	console.log("attempting to sign out...");

    $.ajax({
        type: "DELETE",
        dataType: 'json',
        url: "%apiHome%/sessions/me",

        xhrFields: {
            withCredentials: true
        },
        success: function (data) {
            console.log('success deleting session');
            sessionStorage.removeItem('sessionToken');
        },
        error: function (textStatus, errorThrown) {
            console.log('error deleting session: ' + JSON.stringify(textStatus));
            console.log(errorThrown);
        },
        async: true
    });

	setMenu("anon");
}

</script>