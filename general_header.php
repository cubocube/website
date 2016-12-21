<?php
/**
 * Some global defines here which we should start using, replacing old constants
 * with them as we go:
 */

define('PUBLIC_BOOK',  0);
define('PRIVATE_BOOK', 1);

define('ADMIN_ROLE',      'admin');
define('INSTRUCTOR_ROLE', 'instructor');
define('STUDENT_ROLE',    'student');



// general header

@session_start();
include_once("db.php");
include_once("functions.php");
$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

$relative_path = '';

function check_logged() {
	global $_SESSION;
	if (isset($_SESSION["logged"])) {
		$logged = $_SESSION["logged"];
		$q_is_user = "SELECT * FROM users WHERE username = '" . $logged . "'";
		$result = mysql_query($q_is_user);
		if (mysql_num_rows($result) != 0) {
			return true;  // a matching user was found in db
		} else {
			return false;
		}
	} else {
		return false;
	}	
}


$val_log = check_logged();

if ($val_log) { // if the user is already logged in redirect
	$con = mysql_connect($host,$user,$pass);
	mysql_select_db($main_db, $con);
	
	$username = $_SESSION["logged"];
	$q = "SELECT * FROM users WHERE username = '" . $username . "'";
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);
	
	$first_name = $row['first_name'];
	$last_name = $row['last_name'];
	$first_last = $first_name . " " . $last_name;
	$user_id = intval($row['id']);
	$verified = $row['verified'];
	$password_hash_db = $row['password'];
	$user_email = $row['email'];
	$logged_ip = $row['IP'];
	$user_student_id = intval($row['studentid']);	
	
	$path_string = $_SERVER['REQUEST_URI'];
	$pos  = strpos($path_string, "dashboard");
	$pos2 = strpos($path_string, "index");
    $pos3 = strpos($path_string, "contactus");
	if (($pos === false) && ($pos2 === false) && ($pos3 === false) &&
        $verified && $path_string != $relative_path) {
		header('Location: '.$url.'dashboard.php'); // we can redirect because we aren't at dashboard.php yet!
	}
	
} else {  // User is not logged in.
    // Only redirect to the login page if we're aiming for the dashboard.
	$uri= $_SERVER['REQUEST_URI'];
	// we allow the public to access a stripped down version of dashboard.php
	// anything else that includes dashboard should be accessible only by logged in users
	if (strpos($uri, 'dashboard') !== false && strpos($uri, 'dashboard.php') === false) {
        // Put the current path in the session so that the login page knows
        // where to redirect the user to upon a successful login.
        $_SESSION['redirect-to'] = $uri;
        header('Location: '.$url.'login.php');
    }
}


if ($val_log) { // if the user is logged in and not verified...and is trying to access dashboard
	$path_string = $_SERVER['REQUEST_URI'];
	if ($verified == 0) {
		$pos = strpos($path_string, "verify.php");
		if ($pos === false) {
			header('Location: '.$url.'verify.php?do=notverified'); // redirect user that is not logged in and is trying to access dashboard.php
		} 
	}
}

// GENERAL TOP-BAR - TWO CASES


function top_bar($template) {
	global $val_log, $first_last, $user_id;
	// $val_log == true when user is logged in
	if (!$val_log) {
		$string_top_bar = '
				<div class="almost-full-width cf">
				<a id="homebutton" href="index.php"><img src="template/img/logo-white-small.png"/></a>
				<!-- <form action="#" onsubmit="return search()" id="search-form" class="fr">
					<fieldset>
						<input type="text" id="search-keyword" placeholder="Search..." class="button round dark image-right ic-search" />
						<input type="hidden" value="submit" />
					</fieldset>
				</form> -->
			</div> <!-- end page-full-width -->
		
		';
		$user_info_box = '';
	} else if ($val_log) { // the user is logged in, at this case
		$num_new_messages = any_new_messages($user_id);
		if ($num_new_messages > 0) {
			if ($num_new_messages == 1)
				$messages_button = '<li class="v-sep"><a href="dashboard-messages.php?mode=view" class="button round dark image-left ic-menu-notification">1 new message</a></li>';	
			else
				$messages_button = '<li class="v-sep"><a href="dashboard-messages.php?mode=view" class="button round dark image-left ic-menu-notification">'.$num_new_messages.' new messages</a></li>';	
		} else {
			$messages_button = '<li class="v-sep"><a href="dashboard-messages.php?mode=view" class="button round dark image-left ic-menu-notification">0 new messages</a></li>';
		}

		$string_top_bar = '

			<div class="page-full-width cf">
				<a id="homebutton" href="index.php"><img src="template/img/logo-white-small.png"/></a>
				<ul id="nav" class="fl">
					<li class="v-sep"><a href="dashboard-cb.php" class="button round dark image-left ic-menu-books">CuboBooks</a></li>
					'.$messages_button.'
					{CUBOBOOK-SETTINGS-PLACEHOLDER}
					<li class="v-sep">
						<a href="javascript:void(0);" class="button round dark image-left ic-menu-user">Logged in as <strong>{YOUR-NAME-HERE}</strong></a>
						<ul>
							<li><a href="dashboard-uprofile.php">User profile</a></li>
							<li><a href="dashboard-chpass.php">Change password</a></li>
							<li><a href="login.php?do=logout">Log out</a></li>
						</ul>
					</li>
				</ul>
				
				<!--<form action="#" id="search-form" class="fr">
					<fieldset>
						<input type="text" id="search-keyword" placeholder="Search..." class="button round dark image-right ic-search" />
						<input type="hidden" value="submit" />
					</fieldset>
				</form>-->
			</div> <!-- end page-full-width -->

		';	

			
		
		$dash_manage_cubobook_settings = '
		
			
			<li class="v-sep">
				<a href="#" class="button round dark image-left ic-menu-settings">Manage CuboBook Settings</a>
				<ul>
					<li><a href="dashboard-arrsections.php?c={CUBOCUBE-BOOK-ID}">Manage Sections</a></li>
					<li><a href="dashboard-manageusers.php?c={CUBOCUBE-BOOK-ID}">Manage Users</a></li>
					<li><a href="dashboard-assignsections.php?c={CUBOCUBE-BOOK-ID}">Assign Sections</a></li>
				</ul>
			</li>
		
		';

		$user_info_box = '<div id="user-intro"><img src="template/img/user-icon.png"/><div id="user-intro-name">{YOUR-NAME-HERE}</div>
		 	<div id="user-intro-link"><a href="dashboard-uprofile.php">Edit Profile</a></div>
		 </div>
		 ';
		$user_info_box = str_replace('{YOUR-NAME-HERE}', $first_last, $user_info_box);
		
		global $user_role;
		// NOTE: user permission system needs to be re-written
		if (($user_role == ADMIN_ROLE) || ($user_role == INSTRUCTOR_ROLE)) {
			$arr = array("dashboard.php", "dashboard-arrsections.php", "dashboard-manageusers.php", "dashboard-assignsections.php");
			$path_string = $_SERVER['REQUEST_URI']; 

			$cb_flag = false;

			foreach ($arr as $check) {
				$pos = strpos($path_string, $check);

				if ($pos !== false) { // was found.
					$cb_flag = true;
					break;
				}
			}


			if ($cb_flag == true) {
				$string_top_bar = str_replace("{CUBOBOOK-SETTINGS-PLACEHOLDER}", $dash_manage_cubobook_settings, $string_top_bar);
			} else {
				$string_top_bar = str_replace("{CUBOBOOK-SETTINGS-PLACEHOLDER}", "", $string_top_bar);
			}

		} else {
			$string_top_bar = str_replace("{CUBOBOOK-SETTINGS-PLACEHOLDER}", "", $string_top_bar);
		}

	}
	
	$temp = str_replace('{YOUR-NAME-HERE}', $first_last, $string_top_bar);
	$str = str_replace('{TOP-BAR-HERE}', $temp, $template);
	$str = str_replace('{USER-INFO-HERE}', $user_info_box, $str);
	return $str;
}

function load_js($js_array) {
	$str = '';
	foreach ($js_array as $js) {
		if ($js == 'jquery') {
			$str.= '<script src="{URL-BASE}js/jquery-1.7.2.js"></script>';
		} else {
			$str.= '<script src="{URL-BASE}js/' . $js . '"></script>';
		}
	}
	return $str;
}



?>
