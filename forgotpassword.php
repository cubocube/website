<?php

// login script

include("./general_header.php");
ob_start();
include('./template/register.html');


$str = ob_get_contents();
ob_end_clean(); 


// JS Files needed

$js_array = array(
	'jquery',
	'contactus.js'
);
//{JS-AREA-HERE}
if (!isset($_GET['do'])) {

	$register_form = '
	<form action="#" method="post" id="register-form">

		<fieldset>
			<p><span class="header-text-3">I forgot my password...</span></p>

			<p>
				<label  for="email">Your Email</label>
				<input type="text" id="contact-email" name="email" class="round full-size-input" placeholder="Enter your email address." autofocus />
			</p>

			<input type="submit" value="Reset" class="button round blue-arrow text-upper image-right ic-right-arrow" id="btn-reset-pass"/>		

			<p>
				<div class="error-box round" id="email-contact-error" style="display:none;">Invalid Email.</div>
			</p>

			<p>
				<div class="information-box round" id="reset-pass-info" style="display:none;">Please check your email to reset your password.</div>
			</p>
	               
		</fieldset>

	</form>';


} else if ($_GET['do'] == "reset") {
	$register_form = '
	<form action="#" method="post" id="register-form">

		<fieldset>
			<p><span class="header-text-3">I forgot my password...</span></p>

			<p>
				<label  for="email">Your Email</label>
				<input type="text" id="contact-email" name="email" class="round full-size-input" value="'.$_GET['e'].'" />
			</p>

			<p>
				<label  for="password">Your New Password</label>
				<input type="password" id="contact-password" name="password" class="round full-size-input" autofocus />
			</p>

			<p style="display: none;">
				<input type="text" id="hash" value="' . $_GET['s'] . '" />
			</p>

			<input type="submit" value="Reset" class="button round blue-arrow text-upper image-right ic-right-arrow" id="btn-verify-reset-pass"/>		

			<p>
				<div class="error-box round" id="email-contact-error" style="display:none;">Invalid Email.</div>
			</p>

			<p>
				<div class="error-box round" id="password-contact-error" style="display:none;">Invalid Password: your password needs to be between 5 to 20 characters.</div>
			</p>

			<p>
				<div class="error-box round" id="link-contact-error" style="display:none;">The link you have used to reset your account password is invalid.</div>
			</p>

			<p>
				<div class="information-box round" id="reset-pass-info" style="display:none;">Your account password has successfully been reset. Please navigate to the <a href="login.php">login page</a>.</div>
			</p>
	               
		</fieldset>

	</form>';

}


$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url.'template/', $str);
$str = str_replace('{REGISTER-FORM-HERE}', $register_form, $str);

$str = top_bar($str);

echo $str;


?>
