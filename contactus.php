<?php

// login script

include("./general_header.php");
ob_start();
include('./template/register.html');


$str = ob_get_contents();
ob_end_clean(); 


if (@$_SESSION["contactus"] == 1) {

	$register_form = '
		<form id="register-form">
			<fieldset>
				<p><span class="header-text-3">Contact Us:</span></p>
				<p>
					<div class="information-box round" id="contact-sent-info">You have already submitted a contact form. 
						Although we read a great percentage of requests we may not have a chance to reply to all messages on an individual basis.</div>
				</p>
			</fieldset>
		</form>
	';

} else {

	// JS Files needed

	$js_array = array(
		'jquery',
		'functions.js',
		'contactus.js'
	);
	//{JS-AREA-HERE}

	$register_form = '
	<form action="#" method="post" id="register-form">

		<fieldset>
			<p><span class="header-text-3">Contact Us:</span></p>
			<p>
				<label  for="name">Name</label>
				<input type="text" id="contact-name" name="name" class="round full-size-input" autofocus />
			</p>

			<p>
				<label  for="email">Email</label>
				<input type="text" id="contact-email" name="email" class="round full-size-input" />
			</p>

			<p>
	        	<label for="simple-textarea">Your Message</label>
	            <textarea name="simple-textarea" id="simple-textarea" class="round" cols="42" rows="10"></textarea>
	        </p>

			<input type="submit" value="Submit" class="button round blue-arrow text-upper image-right ic-right-arrow" id="btn-contact"/>		

			<p>
				<div class="error-box round" id="name-contact-error" style="display:none;">Invalid Name</div>
			</p>

			<p>
				<div class="error-box round" id="email-contact-error" style="display:none;">Invalid Email.</div>
			</p>

			<p>
				<div class="error-box round" id="message-contact-error" style="display:none;">Invalid Message.</div>
			</p>

			<p>
				<div class="information-box round" id="contact-sent-info" style="display:none;">Your message was sent. Thank you.</div>
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
