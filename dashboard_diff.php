<?php

include_once("general_header.php");
include_once("db.php");
include_once("functions.php");
require_once 'class.Diff.php';

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);


$selected_content_id = stripslashes(strip_tags($_GET['a']));


// get selected content
$q_get_selected = "SELECT * FROM `content_piece` WHERE `unique_id` = {$selected_content_id}";
$result_get_selected = mysql_query($q_get_selected);
$row_get_selected = mysql_fetch_assoc($result_get_selected);

//echo $row_get_selected['content'];

// get parent id
// parent id = $row_get_selected['origin']

// get parent content
$q_parent = "SELECT * FROM `content_piece` WHERE `unique_id` = {$row_get_selected['origin']}";
$result_parent = mysql_query($q_parent);
$row_parent = mysql_fetch_assoc($result_parent);

//echo $row_parent['content'];


// output diff
$diff = Diff::compare($row_parent['content'], $row_get_selected['content']);
echo "<!DOCTYPE html><html><head><title>Viewing changes</title>
    <style>
    .new-img img {
        border: 10px solid #4f4;
    }
    .del-img img {
        opacity: 0.3;
    }
    </style>
    </head><body>";
echo Diff::toString($diff);
echo "</body></html>";

?>
