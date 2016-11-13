<?php

$config["regFlow"]["basic"]["title"] = "Basic registration flow";
$config["regFlow"]["basic"]["desc"] = "A basic user record will be created in the Okta universal directory. The user will be authenticated immediately.";

$config["regFlow"]["sfChatter"]["title"] = "Registration with Salesforce provisioning";
$config["regFlow"]["sfChatter"]["desc"] = "A user record will be created in the Okta universal directory, and the user will be provisioned to Salesforce Chatter. User will be authenticated immediately.";
$config["regFlow"]["sfChatter"]["shortDesc"] = "Provision a Salesforce Chatter user";

$config["regFlow"]["withMFA"]["title"] = "MFA registration flow";
$config["regFlow"]["withMFA"]["desc"] = "A user record will be created in the Okta universal directory. An activation email will be sent to the user. The user must use a 2nd factor when they authenticate.";
$config["regFlow"]["withMFA"]["shortDesc"] = "User must enroll in MFA";

$config["regFlow"]["withEmail"]["title"] = "Email verification user flow";
$config["regFlow"]["withEmail"]["desc"] = "A user record will be created in the Okta universal directory. The user must verify their email address before they can authenticate.";
$config["regFlow"]["withEmail"]["shortDesc"] = "User must verify their email address";

$config["regFlow"]["provisional"]["title"] = "Provisional registration";
$config["regFlow"]["provisional"]["desc"] = "A user will be created in an inactive state in the Okta universal directory. An admin must review the user record and manually activate (invite) the user.";
$config["regFlow"]["provisional"]["shortDesc"] = "User must be approved by admin";

$config["regFlow"]["okta"]["title"] = "Okta admin registration";
$config["regFlow"]["okta"]["desc"] = "An Okta employee can register and get admin access (read-only) to the demo tenant. An Okta email address is required. MFA is also enforced for authentication.";
$config["regFlow"]["okta"]["shortDesc"] = "Okta users can register as an admin";