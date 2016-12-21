<?php

include_once("general_header.php");

ob_start();
include_once('./template/dashboard_main.html');
$str = ob_get_contents();

// JS Files needed

$js_array = array(
	'jquery',
	'functions.js',
	'ui/jquery.ui.core.js',
	'ui/jquery.ui.widget.js',
	'ui/jquery.ui.position.js',
	'ui/jquery.ui.autocomplete.js',
	'dashboard-manageusers.js'
	);

ob_end_clean();

/*
	The Get variable c determines which book/group they are referencing. 
	If it's not set, then display the book selection page.
*/

$book_group = intval(@$_GET['c']);


if ($book_group <= 0) {
	header('Location: '.$url.'dashboard-cb.php');
	exit();
}

// Make sure that this user has a role sufficient for this page.
$user_role = requireNonStudent($user_id, $book_group);

$_SESSION['cubogroup'] = $book_group;



$register_form = '

	<div class="content-module" id="drop-preview-box">			
		<div class="content-module-heading cf">
			<h3 class="fl">Account Information</h3>
			<span class="fr expand-collapse-text">Click to collapse</span>				
			<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
		</div>
		
		<div class="content-module-main" id="manageuserscontent">
			
		</div> <!-- end content-module -->
	
	</div> 

<div class="content-module">			
		<div class="content-module-heading cf">
			<h3 class="fl">User Lookup</h3>
			<span class="fr expand-collapse-text">Click to collapse</span>				
			<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
		</div>
		
		<div class="content-module-main">
			<p>Use the search form below to lookup a user. You can also <a href="dashboard-download_db_users_csv.php">download</a> a spreadsheet of the user database exported in .csv format.</p>
			<form action="#" id="tags-form-manage" class="">
				<fieldset>
				
					<p>
						<label for="select-search-by-manage">Search using:</label>
						<select name="simple-select" id="select-search-by-manage">
							<option value="uname">Username</option>
							<option value="firstname">First Name</option>
							<option value="lastname">Last Name</option>
						</select>
					</p>
					<!-- <input type="hidden" value="submit" /> -->
					<p class="auto-complete-manage">
						<form>
							<input type="text" class="round default-size-input" id="tags-simple-input-manage" />
							<input type="submit" style="display: none;" id="tags-input-sub-manage" />
						</form>								
					</p>
					
				</fieldset>
			</form>
		</div> 
	</div> <!-- end content-module -->
	
	
	';

$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url.'template/', $str);
$str =  str_replace('{REGISTER-FORM-HERE}', $register_form, $str);
$str = top_bar($str);
$str = str_replace('{CUBOCUBE-BOOK-ID}', $book_group, $str);

echo $str; 

//echo $register_form; 
//echo $str_register;

?>

