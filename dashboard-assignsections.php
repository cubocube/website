<?php

// arrange chapters/sections

include_once("general_header.php");
include_once("db.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

ob_start();
include_once('./template/dashboard_main.html');
$str = ob_get_contents();
ob_end_clean();

/*
	The Get variable c determines which book/group they are referencing. 
	If it's not set, then display the book selection page.
*/

$book_group = intval($_GET['c']);

if ($book_group <= 0){
	header('Location:  dashboard-cb.php');
	exit();
}

// Make sure that this user has a role sufficient for this page.
$user_role = requireNonStudent($user_id, $book_group);

$_SESSION['cubogroup'] = $book_group;

// JS Files needed

$js_array = array(
	'jquery',
	'functions.js',
	'ui/jquery.ui.core.js',
	'ui/jquery.ui.widget.js',
	'ui/jquery.ui.position.js',
	'ui/jquery.ui.autocomplete.js',
	'dashboard-assignsections.js'
	);


$register_form = '
		<style type="text/css">
            .unassignThis {
                background: #fcfcfc;
                border: 1px solid #ddd;
                cursor: pointer;
                display: inline-block;
                margin: 0 3px;
                padding: 3px 5px;
            }
        </style>
        
		<div class="content-module" id="drop-user-preview">
				<div class="content-module-heading cf">
					<h3 class="fl">User Preview</h3>
					<span class="fr expand-collapse-text">Click to collapse</span>				
					<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
				</div>
				<div class="content-module-main" id="assign-preview-box">
					
					<table>
						<thead>
							<tr>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Username</th>
								<th>Student #</th>
								<th>Email</th>
								<th>Role</th>
							</tr>
						</thead> 
						<tfoot>
							<tr>
								<td colspan="6" class="table-footer">
								</td>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<td id="selected-firstname">{SELECTED-FIRST-NAME-HERE}</td>
								<td id="selected-lastname">{SELECTED-LAST-NAME-HERE}</td>
								<td id="selected-username">{SELECTED-USERNAME-HERE}</td>
								<td id="selected-studentnum">{SELECTED-STUDENT-ID-HERE}</td>
								<td id="selected-email">{SELECTED-EMAIL-HERE}</td>
								<td id="selected-user-role">{SELECTED-USER-ROLE-HERE}</td>
							</tr>
							
						</tbody>
					</table>
					<br />
					<table>
						<thead>
							<tr>
								<th>Assigned To</th>
							</tr>
						</thead> 
						<tfoot>
							<tr>
								<td colspan="6" class="table-footer">
								</td>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<td id="selected-username">{SELECTED-ASSIGNEDTO-HERE}</td>
							</tr>
							
						</tbody>
					</table> 
					<p>You have not selected a user to preview.</p>
				</div> 
				
		</div> <!-- end content-module -->		

	
		<div class="content-module">			
			<div class="content-module-heading cf">
				<h3 class="fl">Section Assignment</h3>
				<span class="fr expand-collapse-text">Click to collapse</span>				
				<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
			</div>
			
			<div class="content-module-main cf">
				<p>This tool can be used to assign users with the ability to modify and edit certain sections. Please note that if you assign a user to a parent section that user will also have access to all the child sections (assignment is inherited). This feature is used for users with the role of \'student\', as users with roles of \'instructor\' or \'admin\' can edit any of the sections. Once you designate a user to a section they will be notified of their assignment.</p>
				<div class="half-size-column fl">
					<form action="#" id="tags-form" class="">
						<fieldset>
						
							<p>
								<label for="select-search-by">Search using:</label>
								<select name="simple-select" id="select-search-by">
									<option value="uname">Username</option>
									<option value="firstname">First Name</option>
									<option value="lastname">Last Name</option>
								</select>
							</p>
							<!-- <input type="hidden" value="submit" /> -->
							<p class="auto-complete">
								<form>
									<input type="text" class="round default-size-input" id="tags-simple-input" />
									<input type="submit" style="display: none;" id="tags-input-sub" />
								</form>								
							</p>
							
						</fieldset>
					</form>
				</div>
				
				<div class="half-size-column fr">
					<p>Choose the chapter or section you want to assign a selected user. You can click an assignment of theirs listed in the above table to remove them from it.</p>
					{SECTIONS-CHAPTERS-ASSIGN-HERE}
				</div>
				
				<!-- <label for="tags-username">Find Username: </label>
				<input id="tags-username" /> -->
			</div> 
		</div>
		
	

	';
	
	
function chapters_sections_sortable_ch_output($mode="all") {
	global $book_group;

	$q_get_chapters = "SELECT * FROM `section` WHERE `parent` = -1 AND `cubogroup` = " . $book_group . " ORDER BY `order`";
	$result = mysql_query($q_get_chapters);
	$s = "";
	//$s.= "<div id=\"container-for-sortable\">";
	if (($mode == "chapters" )|| ($mode == "all") ){
		$s.= '<ul class="regular-ul">';
	} else {
		$s.= '<ul class="regular-ul">';
	}
	while ($row = mysql_fetch_assoc($result)) {
		$ch_id = $row['id'];
		$ch_desc = $row['description'];
		$s.= '<li>';
		$s.= '<div class="assignThis" id="assignThis_'.$ch_id.'">'.$ch_desc.'</div>';
		if (($mode == "sections") || ($mode == "all")) {
			$s.= '<ul>';
		} else {
			$s.= '<ul>';
		}
		$get_ch_sections = "SELECT * FROM `section` WHERE `parent` = " . $ch_id . " AND `cubogroup` = " . $book_group . " ORDER BY `order`";
		$s_results = mysql_query($get_ch_sections);
		while ($srow = mysql_fetch_assoc($s_results)) {
			$section_id = $srow['id'];
			$section_desc = $srow['description'];
			$s.= '<li>';
			$s.= '<div class="assignThis" id="assignThis_'.$section_id.'">'.$section_desc.'</div>';
			if (($mode == "sections4section") || ($mode == "all")) {
				$s.= '<ul>';
			} else {
				$s.= '<ul>';
			}
			$get_sections4section = "SELECT * FROM `section` WHERE `parent` = " . $section_id . " AND `cubogroup` = " . $book_group . " ORDER BY `order`";
			$s4s_results = mysql_query($get_sections4section);
			while ($s4srow = mysql_fetch_assoc($s4s_results)) {
				$s4s_id = $s4srow['id'];
				$s4s_desc = $s4srow['description'];
				$s.= '<li>';
				$s.= '<div class="assignThis" id="assignThis_'.$s4s_id.'">'.$s4s_desc.'</div>';
				$s.= '</li>';
			}
			$s.= '</ul>';
			$s.= '</li>';
		}
		$s.= '</ul>';
		$s.= '</li>';
	}
	$s.= '</ul>';
	//$s.= '</div>';
	
	return $s;
}


$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url.'template/', $str);
$str =  str_replace('{REGISTER-FORM-HERE}', $register_form, $str);

$first_last = $first_name . " " . $last_name;

$sections = chapters_sections_sortable_ch_output();

$str =  str_replace('{YOUR-NAME-HERE}', $first_last, $str);
$str =  str_replace('{SELECTED-FIRST-NAME-HERE}', '-', $str);
$str =  str_replace('{SELECTED-LAST-NAME-HERE}', '-', $str);
$str =  str_replace('{SELECTED-USERNAME-HERE}', '-', $str);
$str =  str_replace('{SELECTED-STUDENT-ID-HERE}', '-', $str);
$str =  str_replace('{SELECTED-EMAIL-HERE}', '-', $str);
$str =  str_replace('{SELECTED-USER-ROLE-HERE}', '-', $str);
$str =  str_replace('{SELECTED-ASSIGNEDTO-HERE}', '-', $str);
$str =  str_replace('{SECTIONS-CHAPTERS-ASSIGN-HERE}', $sections, $str);
$str = top_bar($str);
$str = str_replace('{CUBOCUBE-BOOK-ID}', $book_group, $str);


echo $str;



?>

