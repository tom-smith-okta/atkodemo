<script>

/************************************/
// Instantiate the Okta sign-in widget
/************************************/

var oktaSignIn = new OktaSignIn({
	baseUrl: '%oktaBaseURL%',
	logo: '%logo%',
	features: {
		multiOptionalFactorEnroll: true,
		smsRecovery: true
	},
  
	redirectUri: '%redirectURL%'

});

</script>