<?php

include_once("general_header.php");


ob_start();
include('./template/register.html');

$js_array = array(
	'jquery',
	'functions.js',
	'register.js'
	);

$str = ob_get_contents();

$s = stripslashes(strip_tags($_GET['s']));

ob_end_clean();

if ($s == "") {
	$verify_form = "<p>Invalid request.</p>";
} else {

$verify_form = '
	<form action="" method="post" id="verify-form">

		<fieldset>
			<h1>Complete your Registration</h1>
		
			<p>
				<label  for="first_name">First Name</label>
				<input type="text" id="first-name" name="first_name" class="round full-size-input"  autofocus />
			</p>
		
			<p>
				<label  for="last_name">Last Name</label>
				<input type="text" id="last-name" name="last_name" class="round full-size-input" />
			</p>
		
			<p>
				<label  for="register-username">Username</label>
				<input type="text" id="register-username" name="username" class="round full-size-input" />
			</p>
		
			<p>
				<label  for="register-password">Confirm Password</label>
				<input type="password" id="register-password" name="password" class="round full-size-input" />
			</p>
		
			<p>
				<input type="hidden" id="secret" name="secret" value="'. $s . '" />
			</p>
				
			<input type="submit" value"Login" class="button round blue text-upper image-right ic-right-arrow" id="btn-verify" />
			
			<p>
				<div class="error-box round" id="firstname-error">Please fill out your first name.</div>
			</p>
			
			<p>
				<div class="error-box round" id="lastname-error">Please fill out your last name.</div>
			</p>
			
			<p>
				<div class="error-box round" id="email-error">Invalid email address.</div>
			</p>		

			<p>
				<div class="error-box round" id="username-error">Invalid username. Your username should be between 4 to 20 characters.</div>
			</p>

			<p>
				<div class="error-box round" id="password-error">Invalid password. Your password should be between 4 to 20 characters.</div>
			</p>
				
			<p>
				<div class="information-box round" id="registration-response">Once you fill out the form, you will receive a confirmation email.</div>
			</p>
			
			<p>
				<div class="error-box round" id="registration-response-error">Error.</div>
			</p>
					
		</fieldset>
	
	</form>';

}

$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url.'template/', $str);
$str = str_replace('{REGISTER-FORM-HERE}', $verify_form, $str);

if ($_GET['do'] == 'notverified') {
	$str = str_replace('Invalid request.', 'Your account is NOT verified. Please check your email.', $str);
}

$str = top_bar($str);
echo $str; 


?>