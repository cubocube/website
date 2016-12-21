<?php

include_once("../db.php");
include_once("../general_header.php");
include_once('../lib/htmLawed.php');


$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

$element_id = stripslashes(strip_tags($_POST['id']));
$element_value = mysql_real_escape_string($_POST['value']); 
$element_value = htmlentities($element_value, ENT_QUOTES);


$q_update = "UPDATE  section SET  description =  '{$element_value}' WHERE  id = " . $element_id;
$result = mysql_query($q_update);

//echo $q_update;


echo $element_value;

mysql_close($con);




?>