<?php



include("../db.php");
include("../general_header.php");
$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

$email = stripslashes(strip_tags($_POST['email']));
$mode = stripslashes(strip_tags($_POST['mode']));

if ($mode == "sendforgotpasswordemail") {

	if ($email != "") {

		$q = "SELECT `username`, `first_name`, `last_name`, `salt` FROM `users` WHERE `email` = '" . $email . "'";
		$result = mysql_query($q);
		$row = mysql_fetch_assoc($result);


		if (mysql_num_rows($result) != 0) {
			$fwurl = $url."forgotpassword.php?do=reset&e=" . $email . "&s=" . hash("sha512", $email.date("Y-m-d-h").$row['salt']);

			$to = $email;
			$email_txt .= "Hello " . $row['first_name'] . " " . $row['last_name'] . ":\n\n";
			$email_txt .= "We have received a request to reset your CuboCube.com account password. This email address is associated with the username: " . $row['username'] . ".\n\n";
			$email_txt .= "Please follow the link below to continue. If you have not requested your password to be reset, please ignore this email.\n";
			$email_txt .= "\n" . $fwurl . "\n\n";
			$email_txt .= "Your password reset link will expire in 1 hour.\n\n";
			$email_txt .= "CuboCube.com\n";
			$to = $email; 
			$subject = 'CuboCube: Password Reset';
			$headers = 'From: no-reply@cubocube.com' . "\r\n" .
					'Reply-To: webmaster@cubocube.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $email_txt, $headers);

			echo "true";
		
		} else {

			echo "false";

		}

		
	}

} else if ($mode == "verifyforgotpasswordemail") {
	// along with email we are going to get date, password, and hash. 

	$q = "SELECT `username`, `first_name`, `last_name`, `salt` FROM `users` WHERE `email` = '" . $email . "'";
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);

	$hash = stripslashes(strip_tags($_POST['hash']));
	$newPassword = stripslashes(strip_tags($_POST['pass']));

	if ((hash("sha512", $email.date("Y-m-d-h").$row['salt']) == $hash) || (hash("sha512", $email.date("Y-m-d-h", strtotime("-1 hour")).$row['salt']) == $hash)) { // this is the correct hash, verify

		if (mysql_num_rows($result) == 0) {
			echo "false";
		} else {

			$newPasswordHash = hash("sha512", $newPassword.$row['salt']);

			// update db.

			$q = "UPDATE `users` SET `password` = '" . $newPasswordHash . "' WHERE `email` = '" . $email . "'";
			mysql_query($q);

			$to = $email;
			$email_txt .= "Hello " . $row['first_name'] . " " . $row['last_name'] . ":\n\n";
			$email_txt .= "Your account password has succesfully been changed through our 'forgot password form'. \n\nThis email address is associated with the username: " . $row['username'] . ".\n\n";
			$email_txt .= "CuboCube.com\n";
			$to = $email; 
			$subject = 'CuboCube: Password Reset Completed';
			$headers = 'From: no-reply@cubocube.com' . "\r\n" .
					'Reply-To: webmaster@cubocube.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $email_txt, $headers);

			echo "true";
		}

		

	} else {
		echo "false";
	}

}

?>