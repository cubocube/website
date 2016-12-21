<?php

// confirm the registration

include("../db.php"); 
include("../general_header.php");
include("../lib/pseudorandom.php");

global $pr_bits; // randomly generated salt from ../lib/pseudorandom.php

/// FUNCTIONS

function get_ip_address() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }
}

/// END FUNCTIONS

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);
//$url = "http://localhost:8888/playground/ebookwiki/";
$client_ip = get_ip_address(); // get the ip address of the client

$error_msg = "Invalid request.";
$mode = stripslashes(strip_tags($_POST['mode']));

if ($mode == "submit") {

	$first_name = stripslashes(strip_tags($_POST['firstname'])); 
	$last_name = stripslashes(strip_tags($_POST['lastname']));
	$username = stripslashes(strip_tags($_POST['username']));
	$password = stripslashes(strip_tags($_POST['password']));
	$s = $_POST['secret'];
	$secret = md5(substr($username, 3).substr($password, -3)."cubocubed1234");
	
	if ($secret == $s) {
		$q_get_id = "SELECT * FROM users WHERE username = '" . $username . "'";
		$result = mysql_query($q_get_id); 
		$row = mysql_fetch_assoc($result);
		$user_id = $row['id']; 
		$email = $row['email'];
		
		$is_verified = $row['verified'];
		
		if ($is_verified == 1) {
			echo "Your account has already been verified.";
		} else {
			$q_update = "UPDATE  users SET  verified =  '1', first_name =  '" . $first_name . "', last_name =  '" . $last_name . "', ip = '". $client_ip . "' WHERE  users.id = " . $user_id;
			mysql_query($q_update);
			
			$email_txt = "Dear " . $first_name . " " . $last_name . ": \n\nYour registration has been completed successfully!";
			$email_txt .= "\n\nSignup details:\n";
			$email_txt .= "Username: " . $username . "\n";
			$email_txt .= "Password: " . "password you entered during registration\n";//$password . "\n"; 
			$email_txt .= "Email: " . $email . "\n\n";  
			$email_txt .= $url ."\n"; 
			$to = $email; 
			$subject = 'CuboCube: Registration Completed';
			$headers = 'From: no-reply@cubocube.com' . "\r\n" .
				'Reply-To: webmaster@cubocube.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
	
			mail($to, $subject, $email_txt, $headers);
			
			
			echo "Account registration successful!"; 
		
		}
		
	} else {
		echo "Invalid username and/or password.";
		//echo "Your code: " . $secret;
		//echo "The original: " . $s; 
	}
}


?>