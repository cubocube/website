<?php


include_once("general_header.php");
include_once("db.php");

// download the `users` database in .csv format (Spreadsheet)

if (($user_role != 0) AND ($user_role != 1)) {
	header("Location: dashboard.php");
}


// Connect to the database
$link = mysql_connect($host, $user, $pass);
mysql_select_db($db);
 
require 'lib/exportcsv.inc.php';
 
$table="users"; // this is the tablename that you want to export to csv from mysql.
 
exportMysqlToCsv($table);



?>