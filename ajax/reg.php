<?php

include("../db.php");
include("../general_header.php");
include("../lib/pseudorandom.php");

global $pr_bits; // randomly generated salt from ../lib/pseudorandom.php

// WARNING
// Make sure not to echo any sensitive information such as queries, salt, etc.

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

function valid_username($username) {
	// checks for valid username length
	$p = (20 > strlen($username)) && (strlen($username) > 4);
	
 	return $p;
}

function userNameTaken($username) {
	$q_user = "SELECT * FROM users WHERE username = '" . $username . "'";
	$result = mysql_query($q_user);
	return mysql_num_rows($result) != 0; 
}

function valid_pass($password) {
	return (strlen($password) > 4) && (strlen($password) < 20);
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

function email_available($email) {
	$qFindUserwEmail = "SELECT `id` FROM `users` WHERE `email` = '" . $email . "'";
	$result = mysql_query($qFindUserwEmail);

	// if num rows == 0 then the email IS available
	return (mysql_num_rows($result) == 0); 
}

// END FUNCTIONS

$username = strtolower(stripslashes(strip_tags($_POST['username'])));
$username = str_replace(" ", "", $username); // remove all whitespace within the string
$password = stripslashes(strip_tags($_POST['password']));
$email = stripslashes(strip_tags($_POST['email']));
$beta = stripslashes(strip_tags($_POST['beta']));
$pass_hash = hash('sha512', $password.$pr_bits);

if (!check_email_address($email)) {
	echo "Invalid email address.";
} elseif (!email_available($email)) {
	echo "Your email address is already in use.";
} elseif(!valid_username($username)) {
	echo "Invalid username. Your username should be between 5 to 19 characters.";
} elseif(userNameTaken($username)) {
	echo "This username is already taken. Please try registering with another username.";
} elseif(!valid_pass($password)) {
	echo "Invalid password.";
} else {
	if ($beta == "falseSalt") {
		$q_create = "INSERT INTO users (id, username, verified, beta, password, salt, first_name, last_name, email) VALUES (NULL, '".$username."', '0', '0', '".$pass_hash."', '" . $pr_bits . "', '', '', '".$email."')";
	} else if ($beta == 1) {
		$q_create = "INSERT INTO users (id, username, verified, beta, password, salt, first_name, last_name, email) VALUES (NULL, '".$username."', '0', '1', '".$pass_hash."', '" . $pr_bits . "', '', '', '".$email."')";
	}

	mysql_query($q_create);
	
	send_confirmation($username, $password, $email);
	
	echo "Please follow the instructions in your email to finish registration.";
}


mysql_close($con);

?>