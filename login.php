<?php

// login script

include("general_header.php");

$do = @$_GET['do'];
if ($do == "logout") {
	session_destroy();
	header('Location: '.$url.'login.php');
	return;
}


ob_start();
include('./template/register.html');

// JS Files needed

$js_array = array(
	'jquery',
	'functions.js',
	'register.js'
	);

$str = ob_get_contents();
ob_end_clean();

$register_form = '
<form action="" method="post" id="register-form">

	<fieldset>
		<p><span class="header-text-3">Log In:</span></p>
		<p>
			<label  for="register-username">Username</label>
			<input type="text" id="register-username" name="username" class="round full-size-input" autofocus />
		</p>

		<p>
			<label  for="register-password">Password</label>
			<input type="password" id="register-password" name="password" class="round full-size-input" />
		</p>

		<p>I\'ve <a href="forgotpassword.php">lost my password</a>.</p>
		<p>Don\'t have an account? <a href="register.php?s=cc">Register here!</a></p>

		<input type="submit" value="Login" class="button round blue-arrow text-upper image-right ic-right-arrow" id="btn-login"/>		

		<p>
			<div class="error-box round" id="username-error">Invalid username.</div>
		</p>

		<p>
			<div class="error-box round" id="password-error">Invalid password.</div>
		</p>
	
		<p>
			<div class="error-box round" id="login-response-error">Incorrect username and/or password.</div>
		</p>

		<p>
			<div class="error-box round" id="login-response-error">Incorrect username and/or password.</div>
		</p>

		<p>
			<div class="information-box round" id="login-beta-info">CuboCube is currently in closed beta. You will receive a notification once your account has been activated. Thanks for expressing interest.</div>
		</p>

		<p>
			<div class="error-box round" id="login-notverified-error">Your account has not been verified. Please check your email.</div>
		</p>
               
	</fieldset>

</form>';





$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url.'template/', $str);
$str = str_replace('{REGISTER-FORM-HERE}', $register_form, $str);

$str = top_bar($str);

echo $str;


?>
