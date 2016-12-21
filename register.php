<?php

include_once("general_header.php");

ob_start();
include('./template/register.html');

// JS Files needed

$js_array = array(
	'jquery',
	'functions.js',
	'register.js'
	);
//{JS-AREA-HERE}
//print_r ($js_array);

$str = ob_get_contents();
ob_end_clean();


if (isset($_GET['s']) && $_GET['s'] == "cc") {
	$register_form = '
	<form action="" method="post" id="register-form">

			<fieldset>
				<p><span class="header-text-3">Register:</span></p>
				<p>
					<label  for="register-username">Username</label>
					<input type="text" id="register-username" name="username" class="round full-size-input" autofocus />
				</p>

				<p>
					<label  for="register-password">Password</label>
					<input type="password" id="register-password" name="password" class="round full-size-input" />
				</p>

				<p>
					<label  for="register-confirm-password">Confirm Password</label>
					<input type="password" id="register-confirm-password" name="password" class="round full-size-input" />
				</p>

				<p>
					<label  for="register-email">Email</label>
					<input type="text" id="register-email" name="email" class="round full-size-input" />
				</p>

				<p>Already have an account? <a href="login.php">Login here!</a></p>

				<input type="submit" value="Register" class="button round blue-arrow text-upper image-right ic-right-arrow" id="btn-register"/>

				<p>
					<div class="error-box round" id="email-error" style="display: none;">Invalid email address.</div>
				</p>		

				<p>
					<div class="error-box round" id="username-error" style="display: none;">Invalid username. Your username should be between 4 to 20 characters.</div>
				</p>

				<p>
					<div class="error-box round" id="password-error" style="display: none;">Invalid password. Your password should be between 4 to 20 characters.</div>
				</p>

				<p>
					<div class="error-box round" id="password-confirm-error" style="display: none;">Your two entered passwords do not match.</div>
				</p>
				
				<p>
					<div class="information-box round" id="registration-response" style="display: none;">Once you fill out the form, you will receive a confirmation email.</div>
				</p>
				
				<p>
					<div class="error-box round" id="registration-response-error" style="display: none;">Error.</div>
				</p>
                        
			</fieldset>

		</form>';

} else { // beta signup
	$register_form = '
	<form action="" method="post" id="register-form">

			<fieldset>
				<p><span class="header-text-3">Register:</span></p>
				<p>CuboCube is currently in closed beta. Use the form below to register for our beta list. We regularly release CuboCube to a limited number of accounts.</p>
				<p>
					<label  for="register-username">Username</label>
					<input type="text" id="register-username" name="username" class="round full-size-input" autofocus />
				</p>

				<p>
					<label  for="register-password">Password</label>
					<input type="password" id="register-password" name="password" class="round full-size-input" />
				</p>

				<p>
					<label  for="register-confirm-password">Confirm Password</label>
					<input type="password" id="register-confirm-password" name="password" class="round full-size-input" />
				</p>

				<p>
					<label  for="register-email">Email</label>
					<input type="text" id="register-email" name="email" class="round full-size-input" />
				</p>

				<p>Already have an account? <a href="login.php">Login here!</a></p>

				<input type="submit" value="Register" class="button round blue-arrow text-upper image-right ic-right-arrow" id="btn-beta-register"/>

				<p>
					<div class="error-box round" id="email-error" style="display: none;">Invalid email address.</div>
				</p>		

				<p>
					<div class="error-box round" id="username-error" style="display: none;">Invalid username. Your username should be between 4 to 20 characters.</div>
				</p>

				<p>
					<div class="error-box round" id="password-error" style="display: none;">Invalid password. Your password should be between 4 to 20 characters.</div>
				</p>

				<p>
					<div class="error-box round" id="password-confirm-error" style="display: none;">Your two entered passwords do not match.</div>
				</p>
				
				<p>
					<div class="information-box round" id="registration-response" style="display: none;">Once you fill out the form, you will receive a confirmation email.</div>
				</p>
				
				<p>
					<div class="error-box round" id="registration-response-error" style="display: none;">Error.</div>
				</p>
                        
			</fieldset>

		</form>';
}





$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url.'template/', $str);
$str =  str_replace('{REGISTER-FORM-HERE}', $register_form, $str);
$str = top_bar($str);

echo $str;

//echo $register_form; 
//echo $str_register;

?>

