<?php

/*
  reg_dashboard.php requires that the user is currently logged. 
  Otherwise, we die with a 'logged_out' response. 
*/

include_once("../db.php"); 
include_once("../general_header.php");
include_once("../functions.php");
$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

/*
  Calling check_logged() should automatically renew the 
  session variable if the user is logged in.
  By default, the session variable lasts for 24 minutes.
*/
if (! check_logged()) {
    die('logged_out');
}

$mode = stripslashes(strip_tags(@$_POST['mode']));

if ($mode == 'new_cb') {
    $cubobook_name = mysql_escape_string(@$_POST['cubobook_name']);
    $cubobook_description = mysql_escape_string(@$_POST['cubobook_description']);
    $cubobook_creator_uid = mysql_escape_string($user_id);

    $cubobook_name_strlen = strlen($cubobook_name);
    $cubobook_description_strlen = strlen($cubobook_description);

    if ($cubobook_name_strlen < 3 || $cubobook_name_strlen > 30) {
        die(json_encode(array('status' => 'invalid_cubobook_name')));
    }

    if ($cubobook_description_strlen < 3 || $cubobook_description_strlen > 200) {
        die(json_encode(array('status' => 'invalid_cubobook_description')));
    }

    $q = "select * from `groups` where `name` = '" . $cubobook_name . "'";
    $result = mysql_query($q);
    if (mysql_num_rows($result) > 0) {
        die(json_encode(array('status' => 'duplicate_cubobook_name')));
    }

    $q = "insert into `groups` (`name`, `description`, `type`, `access_type`, `uid`) 
        values ('{$cubobook_name}', '{$cubobook_description}', '0', '0', '{$cubobook_creator_uid}');";
    mysql_query($q);

    // the cubobook id, for the book just created
    $cid = mysql_insert_id();

    $q = "insert into `registered_groups` (`userId`, `groupId`, `role`) 
        values ('{$cubobook_creator_uid}', '{$cid}', 'instructor');";
    mysql_query($q);

    $response = array('status' => 'success', 'c' => $cid);
    die(json_encode($response));
}

?>