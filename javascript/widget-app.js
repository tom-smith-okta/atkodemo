requirejs.config({
    // "baseUrl": "js",
    "baseUrl": "javascript",
    "paths": {
        "jquery": "//ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min",
        "okta-config": "config",
        "okta-signin": "//ok1static.oktacdn.com/js/sdk/okta-signin-widget/1.3.3/js/okta-sign-in-1.3.3.min"
    }
});

define(["jquery", "okta-signin", "okta-config"], function ($, OktaSignIn, OktaConfig) {

    // Set initial config options for widget
    var oktaSignIn = new OktaSignIn({
        baseUrl: OktaConfig.orgUrl,
        clientId: OktaConfig.clientId,
        logo: '/images/acme_logo.png',
        redirectUri: OktaConfig.redirectUri,

        features: {
            securityImage: false,
            rememberMe: true,
            smsRecovery: true,
            selfServiceUnlock: true,
            multiOptionalFactorEnroll: true
        },

        helpLinks: {
            help: 'http://example.com/custom/help/page',

            // Override default recovery flows with custom links
            // forgotPassword: 'http://example.com/custom/forgot/pass/page',
            // unlock: 'http://example.com/custom/unlock/page',

            // Add additional links to widget
            custom: [
              { text: 'custom link text 1', href: 'http://example.com/custom/link1' },
              { text: 'custom link text 2', href: 'http://example.com/custom/link2' }
            ]
        },

        // See dictionary of labels
        labels: {
            "primaryauth.title": "Acme Partner Login",
            "primaryauth.username": "Partner ID",
            "primaryauth.username.tooltip": "Enter your @example.com username",
            "primaryauth.password": "Password",
            "primaryauth.password.tooltip": "Super secret password"
        }
    });

    var sessionTokenKey = 'sessionToken';

    // var showOktaWidget = function () {

    // }

    var renderOktaWidget = function () {
        oktaSessionsMe(function (authenticated) {

            $('#widget').hide();

            if (authenticated) {
                console.log("authenticated user - hiding widget");
            }

            // showAuthUI(authenticated);
            // if (!authenticated) {
            else {

                console.log("user is not authenticated");

                // var loginLink = "<li><a href = '#' onclick = 'alert(\"working\")'>Login link</a>";

                var loginLink = "<li><a href = '#' onclick = 'showWidget()'>Login link</a>";

                $("#login").html(loginLink);

                // showAuthUI(false);

                // $('#widget').show();

                oktaSignIn.renderEl(
                {
                    el: '#widget',
                            authParams: {
                                scope: OktaConfig.scope
                            }
                        },
                        // Success function - called at terminal states like authStatus SUCCESS or
                        // when the recovery emails are sent (forgot password and unlock)
                      function (res) {
                          if (res.status === 'SUCCESS') {
                              console.log('User %s successfully authenticated %o', res.claims.email, res);
                              showAuthUI(true);
                              $('#claims').append('<pre><code class="json">' +
                                JSON.stringify(res.claims, null, '  ') + '</code></pre>');
                              $('pre code').each(function (i, block) {
                                  hljs.highlightBlock(block);
                              });

                          }

                          else if (res.status === 'FORGOT_PASSWORD_EMAIL_SENT') {
                              // res.username - value user entered in the forgot password form
                              console.log('User %s sent recovery code via email to reset password', res.username);
                          }

                          else if (res.status === 'UNLOCK_ACCOUNT_EMAIL_SENT') {
                              // res.username - value user entered in the unlock account form
                              console.log('User %s sent recovery code via email to unlock account', res.username);
                          }
                      },
                function (err) { console.log('Unexpected error authenticating user: %o', err); }
                );
            }
        });
    };

    var showWidget = function() {

        $('#widget').show();

    }

    var showAuthUI = function (isAuthenticated) {
        
        $('#widget').hide();

        if (isAuthenticated) {
            console.log("authenticated user - hiding widget");
            // $("#apicall-buttons").show();
            $('#widget').hide();
        }
        else {
            console.log("anonymous user - showing widget");
            $("#apicall-buttons").hide();
            // $('#widget').show();

            // var onclick = document.getElementById(boxid).style.visibility="visible";

            // var fCall = document.getElementById('widget').style.visibility='visible'";
            var authLinks = "";

            authLinks += '<li><a href = "#" id = "login" onclick = "document.getElementById(\'widget\').style.visibility=\'visible\';">Log in</a></li>';
            authLinks += '<li><a href = "register.php">Register</a></li>';

            $("#authLinks").html(authLinks);

        }
    };

    // var showWidget = function(true) {
    //             $('#widget').hide();

    // }

    var callSessionsMe = function () {
        oktaSessionsMe(function (authenticated) {
            console.log('Is user authenticated? ' + authenticated);
            return authenticated;
        });
    };

    var oktaSessionsMe = function (callBack) {
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: OktaConfig.orgUrl + "/api/v1/sessions/me",
            xhrFields: {
                withCredentials: true
            },
            success: function (data) {
                console.log("My session: ");
                console.log(data);
                sessionStorage.setItem(sessionTokenKey, JSON.stringify(data));
                return callBack(true);
            },
            error: function (textStatus, errorThrown) {
                console.log('No session is present');
                return callBack(false);
            },
            async: true
        });
    };

    var closeSession = function (callback) {
        $.ajax({
            type: "DELETE",
            dataType: 'json',
            url: OktaConfig.orgUrl + "/api/v1/sessions/me",
            xhrFields: {
                withCredentials: true
            },
            success: function (data) {
                console.log('success deleting session');
                sessionStorage.removeItem(sessionTokenKey);
                return callback(true);
            },
            error: function (textStatus, errorThrown) {
                console.log('error deleting session: ' + JSON.stringify(textStatus));
                console.log(errorThrown);
                return callback(false);
            },
            async: true
        });
    };

    $('#btnSignOut').click(function () {
        console.log('signing out');
        oktaSessionsMe(function (authenticated) {
            if (authenticated) {
                var sessionToken;
                var sessionTokenString = sessionStorage.getItem(sessionTokenKey);
                if (sessionTokenString) {
                    sessionToken = JSON.parse(sessionTokenString);
                    var sessionId = sessionToken.id;
                    console.log('closing session ' + sessionId);
                    closeSession(function (success) {
                        console.log('Is session closed? ' + success);
                        if (success) {
                            //showAuthUI(false);
                            location.reload(false);
                            //$('#claims').hide();
                            
                        }
                    })
                }
            }
        });

    });

    $('#btnRenewIDToken').click(function () {
        oktaSignIn.idToken.refresh(null, function (res) {
            console.log('New ID token: ', res);
            //$('#claims').
            $('#claims').html('<pre><code class="json">' +
  JSON.stringify(res.claims, null, '  ') + '</code></pre>');
            $('pre code').each(function (i, block) {
                hljs.highlightBlock(block);
            });
            return res;
        }, {
            scope: OktaConfig.scope
        });
    });

    renderOktaWidget();
});
