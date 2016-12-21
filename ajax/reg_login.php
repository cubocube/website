<?php

// this is where we set the session variable
// when the user logs in successfully
// if we are not successful then we
// will return false otherwise true

include("../db.php");
include("../general_header.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

$username = db_escape($_POST['username']);
$password = db_escape($_POST['password']);

if (($username !== "") && ($password !== "")) {
	// first let's check to see if the username exists
	$q_is_user = "SELECT * FROM users WHERE username = '" . $username . "'";
	$result = mysql_query($q_is_user);
	$row = mysql_fetch_array($result);
	
	$password_hash = hash('sha512', $password.$row['salt']); // let's hash the pass!

	if (mysql_num_rows($result) == 0) {
		echo "false";
	} else {
		$password_db_hash = $row['password'];
		
		if ($password_db_hash == $password_hash) {
			if ($row['verified'] == 0) {
				echo "notverified";
			} else if ($row['beta'] == 0) {
				$_SESSION["logged"] = $username; // we need to set the cookie!
            
                // Remove the post-login URI now, after deciding where to redirect.
                $uri = 'dashboard.php';
                if (isset($_SESSION['redirect-to'])) {
                  $uri = $_SESSION['redirect-to'];
                  unset($_SESSION['redirect-to']);
                }
				echo "true\n$uri";
			} else if ($row['beta'] == 1) {
				echo "beta";
			}
		} else {
			echo "false";
		}
	}
	
} else {
	echo "false"; 
}


?>
