<?php

	include_once("general_header.php");
	include_once("db.php");
	include_once("functions.php");

	$con = mysql_connect($host,$user,$pass);
	mysql_select_db($main_db, $con);

	$book_group = 1;

	$sql_fetch_contributors = "select `first_name`,`last_name` from `users` where `id` in 
		(select distinct `created_by` from `content_piece` where `cubogroup` = '{$book_group}')";
	$result_fetch_contributors = mysql_query($sql_fetch_contributors);
	while ($row_fetch_contributors = mysql_fetch_assoc($result_fetch_contributors)) {
		echo $row_fetch_contributors['first_name'] . " " . $row_fetch_contributors['last_name']  . "<br />";
	}

?>