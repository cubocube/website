<?php

include_once("../db.php");
include_once("../general_header.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);


$oldpassword = stripslashes(strip_tags($_POST['oldpassword']));
$newpassword1= stripslashes(strip_tags($_POST['newpassword1']));
$newpassword2= stripslashes(strip_tags($_POST['newpassword2']));
$username = $_SESSION["logged"];


if ($username == "") {
	return; 
}

if ($newpassword1 != $newpassword2) {
	echo "Two entered passwords do not match.";
	return;
}


$q = "SELECT * FROM users WHERE username = '" . $username . "'";
$result = mysql_query($q);
$row = mysql_fetch_assoc($result);

$salt = $row['salt'];


// now we need to check the current password entered

$oldpassword_hash = hash("sha512",$oldpassword.$salt);


$password_hashdb = $row['password'];


if ($oldpassword_hash != $password_hashdb) {
	echo "The current password you entered is incorrect.";
	return;
} else {
	$q = "UPDATE users SET  password =  '" . hash("sha512",$newpassword1.$salt) . "' WHERE  username = '" . $username . "'";
	mysql_query($q);
	echo "Your password has been updated.";
	echo "<meta http-equiv=\"REFRESH\" content=\"1;url=login.php\">";
}


session_destroy();
//header("Location: login.php");

mysql_close($con);

?>