<?php

include("general_header.php");

ob_start();
include('./template/register.html');
$js_array = array (
	'jquery',
	'register.js'
	);
	
$str = ob_get_contents();
ob_end_clean();


$register_form = '
	<form action="" method="post" id="register-form">

			<fieldset>
				<h1>Change account password</h1>

				<p>
					<label  for="change-password-current">Current Password</label>
					<input type="password" id="change-password-current" name="password" class="round full-size-input" />
				</p>
				
				<p>
					<label  for="change-password-confirm">New Password</label>
					<input type="password" id="change-password-new1" name="password" class="round full-size-input" />
				</p>
				
				<p>
					<label  for="change-password-new">Confirm New Password</label>
					<input type="password" id="change-password-new2" name="password" class="round full-size-input" />
				</p>
				
				<input type="submit" value"Login" class="button round blue text-upper image-right ic-right-arrow" id="btn-change-password"/>	

				<p>
					<div class="error-box round" id="change-password-error">Invalid password. Your password should be between 4 to 20 characters.</div>
				</p>
				
				<p>
					<div class="error-box round" id="change-password-confirmation-error">Confirmation error. Your inputted passwords do not match.</div>
				</p>
				
				<p>
					<div class="error-box round" id="change-password-current-error">Your current password is not correct.</div>
				</p>
				
				<p>
					<div class="information-box round" id="change-password-response">Password.</div>
				</p>
                        
			</fieldset>

		</form>';

$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url . 'template/', $str);
$str = str_replace('{REGISTER-FORM-HERE}', $register_form, $str);
$str = top_bar($str);
$str = str_replace('{CUBOCUBE-BOOK-ID}', $_SESSION['cubogroup'], $str);

echo $str; 

//echo $register_form; 
//echo $str_register;

?>

