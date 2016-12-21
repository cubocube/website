<?php

	include_once("general_header.php");
	include_once("db.php");
	include_once("functions.php");

	$con = mysql_connect($host,$user,$pass);
	mysql_select_db($main_db, $con);


	ob_start();
	include('./template/register.html');
	// JS Files needed

	$js_array = array('jquery','functions.js', 'dashboard_tos_permissions.js');

	$str = ob_get_contents();
	ob_end_clean();

	/*
		The Get variable c determines which book/group they are referencing. 
		If it's not set, then display the book selection page.
	*/

	$book_group = intval(@$_GET['c']);

	if ($book_group <= 0){
		header('Location:  dashboard-cb.php');
		exit();
	} 

	$q_tos_permissions = "select `id` from `tos_entries` where `user_id` = {$user_id} and `cubocubeID` = {$book_group}";
	$result_tos_permissions = mysql_query($q_tos_permissions);
	if (mysql_num_rows($result_tos_permissions) != 0) {
		header("Location: dashboard.php?c={$book_group}");
		exit();
	}


	$q = "select * from `tos_permissions` where `cubocubeID` = {$book_group}";
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);

	$tos_text = $row['text'];
	$date_time = time();

	$date_time_formatted = getdate($date_time);
	$date_time_formatted = $date_time_formatted['weekday'] . ", " . $date_time_formatted['month'] . " " . $date_time_formatted['mday'] . ", " . $date_time_formatted['year'];


	$register_form = '
	<form action="" method="post" id="register-form">

			<fieldset>
				<p><span class="header-text-3">Usage Agreements</span></p>

				<p>
					Before you continue please read the following terms.
				</p>

				<p>
					<label  for="name">Name</label>
					<input type="text" id="tos-firstlast" name="username" class="round full-size-input" value="' . $first_last . '" />
				</p>

				<p>
					<label  for="date">Today\'s Date</label>
					<input type="text" id="tos-date" name="date" class="round full-size-input" value="' . $date_time_formatted . '" />
				</p>

				<p style="display:none;">
					<label  for="tos-bookID">Book ID</label>
					<input type="text" id="tos-bookID" name="date" class="round full-size-input" value="' . $book_group . '" />
				</p>



				'.$tos_text.'

				<p>
					<select id="agreedisagree">
					  <option value="agree">I agree to the terms above.</option>
					  <option value="disagree">I don\'t agree to the terms above.</option>
					</select>
				</p>

				<input type="submit" value="Submit" class="button round blue-arrow text-upper image-right ic-right-arrow" id="btn-submit" />

				<p>
					<div class="error-box round" id="name-error" style="display: none;">Invalid name. Your name must match with what is associated with your user account.</div>
				</p>		

				<p>
					<div class="error-box round" id="date-error" style="display: none;">Invalid date. The date entered for today is not valid.</div>
				</p>

                        
			</fieldset>

		</form>';









	$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
	$str = str_replace('{URL-BASE}', $url.'template/', $str);
	$str =  str_replace('{REGISTER-FORM-HERE}', $register_form, $str);
	$str = top_bar($str);

	// Update Page Title - based on current book being viewed.

	$q = "SELECT `name` FROM `groups` WHERE `id` = '" . $book_group . "'";
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);
	$str = str_replace('{TOP-TITLE-PLACEHOLDER}', $row['name'] . " - CuboCube", $str);
	if ($id <= 0) {
	    $str = str_replace('</body>',
	                       '<script type="text/javascript">
	                          $("#chapter-control-container").hide();
	                       </script>
	                       </body>',
	                       $str);
	}

	echo $str;

?>