<?php

// media library

include_once("general_header.php");
include_once("db.php");
include_once("functions.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

ob_start();
include_once('./template/dashboard_main.html');

$js_array = array(
	'jquery',
	'functions.js',
	'dashboard-media-library.js',
	);

$str = ob_get_contents();
ob_end_clean();


$content = 's';

$str =  str_replace('{DASHBOARD-MAIN-HERE}', $content, $str);


$str = top_bar($str);

echo $str; 





?>