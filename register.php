<?php

include "includes/includes.php";

$thisPage = new htmlPage($config);

$thisPage->setTitle($config["name"] . " - Register");

if (empty($_GET["regType"])) { $regType = "default"; }
else { $regType = $_GET["regType"]; }

$elements = [
	"mainCSS",
	"oktaWidgetCSScore",
	"oktaWidgetCSStheme",
	"oktaWidgetCSSlocal",
	"jquery"
];

$thisPage->addElements($elements);

$thisPage->setConfigValue("regForm", getRegForm($regType)); 

$thisPage->loadBody("register", ["name", "webHome", "logo", "regForm"]);

$thisPage->display();

function getRegForm($regType) {

	$regForm = file_get_contents("html/regFormTemplate.html");

	$formFieldTemplate = file_get_contents("html/regFormFieldTemplate.html");

	$fieldsHTML = "";

	// First name
	$fields["firstName"]["type"] = "text";
	$fields["firstName"]["placeholder"] = "First name";

	// Last name
	$fields["lastName"]["type"] = "text";
	$fields["lastName"]["placeholder"] = "Last name";

	// email
	$fields["email"]["type"] = "text";
	$fields["email"]["placeholder"] = "email";

	$fields["password"]["type"] = "password";
	$fields["password"]["placeholder"] = "password"; 

	foreach ($fields as $fieldName => $properties) {

		$formField = $formFieldTemplate;

		$formField = str_replace("%name%", $fieldName, $formField);
		$formField = str_replace("%type%", $properties["type"], $formField);
		$formField = str_replace("%placeholder%", $properties["placeholder"], $formField);

		$fieldsHTML .= $formField;
	}

	$regForm = str_replace("%fields%", $fieldsHTML, $regForm);

	return $regForm;

}


// 								<div data-se="o-form-fieldset" class="o-form-fieldset o-form-label-top">
// 									<div data-se="o-form-input-container" class="o-form-input">
// 										<span data-se="o-form-input-password" class="okta-form-input-field input-fix o-form-control">
// 											<span class="input-tooltip icon form-help-16" aria-describedby="qtip-1" data-hasqtip="1">
// 											</span>
// 											<span class="icon input-icon remote-lock-16"></span>
// 											<input type="password" autocomplete="off" value="" id="input32" name="password" placeholder="Password">
// 										</span>
// 									</div>
// 								</div>

// 								<!-- Remember Me checkbox -->
// <!--  								<div data-se="o-form-fieldset" class="o-form-fieldset o-form-label-top margin-btm-0">
// 									<div data-se="o-form-input-container" class="o-form-input">
// 										<span data-se="o-form-input-remember">
// 										<div class="custom-checkbox">
// 											<input type="checkbox" id="input39" name="remember">
// 											<label data-se-for-name="remember" for="input39" class="">Remember me</label>
// 											<input type="checkbox" id="input39" name="remember">
// 											<label data-se-for-name="remember" for="input39" class="">Register as admin (okta users only)</label>
// 										</div>
// 										</span>
// 									</div>
// 								</div> -->

// 								<!-- Remember Me checkbox -->
// 								<!-- This version is fixed, kind of -->
// <!--  								<div data-se="o-form-fieldset" class="o-form-fieldset o-form-label-top margin-btm-0">
// 									<div data-se="o-form-input-container" class="o-form-input">
// 										<span data-se="o-form-input-remember">
// 										<div class="custom-checkbox">
// 											<input type="checkbox" id="input39" name="wantsAdmin">
// 											<label data-se-for-name="remember" for="input39" class="">Register as admin (okta users only)</label>
// 										</div>
// 										</span>
// 									</div>
// 								</div> -->

// 							</div>
// 						</div>
// 						<div class="o-form-button-bar">
// 							<input type="submit" class="button button-primary" value="Sign Up" data-type="save">
// 						</div>
// 					</form>
