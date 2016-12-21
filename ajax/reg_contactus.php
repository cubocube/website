<?php



include("../db.php");
include("../general_header.php");
$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);


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

$fullname = stripslashes(strip_tags($_POST['fullname']));
$email = stripslashes(strip_tags($_POST['email']));
$message = stripslashes(strip_tags($_POST['message']));

if (($fullname != "") && ($email != "") && ($message != "")) {


	$to = "your_email@email_provider.com";
	$email_txt = "We have a new contactus.php form filled (CuboCube.com)";
	$email_txt .= "\n\nFull Name: " . $fullname . "\n";
	$email_txt .= "Email: " . $email . "\n";
	$email_txt .= "Message: \n\n";
	$email_txt .= $message . "\n\n"; 
	$email_txt .= "IP of sender: " . get_ip_address() . "\n";
	$subject = 'CuboCube: New Contact form filled - ' . $fullname;
	$headers = 'From: '. $email . "\r\n" .
		'Reply-To: ' . $email . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $email_txt, $headers);

	$_SESSION["contactus"] = 1;

	echo $email_txt;
}


?>
