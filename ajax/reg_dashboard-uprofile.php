<?php

include_once("../db.php");
include_once("../general_header.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

// FUNCTIONS

function check_email_address($email) {
  // First, we check that there's one @ symbol, 
  // and that the lengths are right.
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
    // Email invalid because wrong number of characters 
    // in one section or wrong number of @ symbols.
    return false;
  }
  // Split it into sections to make life easier
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
    if
(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
$local_array[$i])) {
      return false;
    }
  }
  // Check if domain is IP. If not, 
  // it should be valid domain name
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) {
        return false; // Not enough parts to domain
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if
(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
↪([A-Za-z0-9]+))$",
$domain_array[$i])) {
        return false;
      }
    }
  }
  return true;
}

function send_confirmation($username, $password, $email) {
	$secret = md5(substr($username, 3).substr($password, -3)."cubocubed1234");
	global $url;
	$full_url = $url . "verify.php?s=" . $secret;
	
	$email_txt = "Thanks for registering for CuboCube. \n\nTo finish your registration please follow the link: " . $full_url;
	$email_txt .= "\n\nSignup details:\n";
	$email_txt .= "Username: " . $username . "\n";
	//$email_txt .= "Password: " . $password . "\n"; 
	$email_txt .= "Password: password you entered during registration\n";
	$email_txt .= "Email: " . $email . "\n";  
	$to = $email; 
	$subject = 'CuboCube: Continue Registration';
	$headers = 'From: no-reply@cubocube.com' . "\r\n" .
	    'Reply-To: webmaster@cubocube.com' . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $email_txt, $headers);
}

// END FUNCTIONS

$change_what = stripslashes(strip_tags($_POST['changed']));
$value = stripslashes(strip_tags($_POST['value']));
$username = $_SESSION["logged"];

//echo $username;
//echo $change_what;

if ($username == "") {
	return; 
}

if ($change_what == "firstname") {
	// the first name of the user needs to be updated
	echo "hesfsd";
	$q = "UPDATE users SET first_name = '" . $value . "' WHERE username = '" . $username . "'";
	$result = mysql_query($q);
} else if ($change_what == "lastname") {
	$q = "UPDATE users SET last_name = '" . $value . "' WHERE username = '" . $username . "'";
	$result = mysql_query($q);
} else if ($change_what == "studentnum") {
	$q = "UPDATE users SET studentid = '" . $value . "' WHERE username = '" . $username . "'";
	echo $q;
	$result = mysql_query($q);
} else if ($change_what == "email") {
	if (check_email_address($value) == true) {
		$q = "UPDATE users SET email = '" . $value . "' WHERE username = '" . $username . "'";
		$result = mysql_query($q);
	}
}
///UPDATE users SET verified = '0' WHERE username =16

mysql_close($con);

?>