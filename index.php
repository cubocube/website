<?php

// homepage


include("general_header.php");


if(isset($_SESSION["logged"])) {
	header("Location: dashboard-cb.php");
} else {
	ob_start();
	include_once('functions.php');
	include_once('./template/newfront.htm');
	$str = ob_get_contents();
	ob_end_clean();

	echo $str;
}

?>