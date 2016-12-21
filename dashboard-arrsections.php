<?php

// arrange chapters/sections

include_once("general_header.php");
include_once("db.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

ob_start();
include_once('./template/dashboard_main.html');

// JS Files needed

$js_array = array(
	'jquery',
	'functions.js',
	'jquery.jeditable.js',
	'ui/jquery.ui.core.js',
	'ui/jquery.ui.widget.js',
	'ui/jquery.ui.mouse.js',
	'ui/jquery.ui.sortable.js',
	'dashboard-arrsections.js'
	);
//{JS-AREA-HERE}
//print_r ($js_array);

$str = ob_get_contents();
ob_end_clean();


$book_group = intval(@$_GET['c']);


if ($book_group <= 0) {
	echo "No group found.";
	echo "<br />This will contain book discovery page. Where they can join prexisting groups, or navigate to one they've already joined. Need good user interface for this page.";
	return;
}

// Make sure that this user has a role sufficient for this page.
$user_role = requireNonStudent($user_id, $book_group);

$_SESSION['cubogroup'] = $book_group;


$register_form = '
	
	<div class="side-menu fl">

	<h3>Manage Sections</h3>
	<ul>
		<li><a href="#" id="side-change-order">Change Ordering</a></li>
		<li><a href="#" id="side-change-name">Change Names</a></li>
		<li><a href="#" id="side-add-section">Add Sections</a></li>
		<li><a href="#" id="side-remove-section">Remove Sections</a></li>
	</ul>

	</div> <!-- end side-menu -->
	
	<div class="side-content fr">

		<div class="content-module" id="organize-chapters-sections">			
			<div class="content-module-heading cf">
				<h3 class="fl">Organize Order of Chapters & Sections</h3>
				<span class="fr expand-collapse-text">Click to collapse</span>				
				<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
			</div>
	
			<div class="content-module-main">
				<p>Below you can organize the order in which chapters and sections appear.</p>
					<div id="sortable-box">{CHAPTERS-SECTIONS-HERE}</div>
			</div> 
		</div> <!-- end content-module -->
	
		<div class="content-module" id="edit-names-chapters-sections">			
			<div class="content-module-heading cf">
				<h3 class="fl">Edit Names of Chapters & Sections</h3>
				<span class="fr expand-collapse-text">Click to collapse</span>				
				<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
			</div>
	
			<div class="content-module-main">
				<p>Below you can edit the names of chapters and sections. To do so, just click on the appropriate chapter or section.</p>
					<div id="editable-box">{CHAPTERS-SECTIONS-EDITABLE-HERE}</div>
			</div> 
		</div> <!-- end content-module -->
	
		<div class="content-module" id="add-chapters-sections">			
			<div class="content-module-heading cf">
				<h3 class="fl">Add Chapters & Sections</h3>
				<span class="fr expand-collapse-text">Click to collapse</span>				
				<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
			</div>
	
			<div class="content-module-main">
				<p></p>
					<div class="cf" id="">{ADD-CHAPTERS-SECTIONS-HERE}
						<div class="add-information">
							<div class="information-box round fl" id="">Information.</div>
							<a href="#" class="button round blue image-right text-upper ic-add fr" id="btn-add-sch">Add Section</a>
						</div>
					</div>
			</div> 
		</div> <!-- end content-module -->			

		<div class="content-module" id="remove-chapters-sections">			
			<div class="content-module-heading cf">
				<h3 class="fl">Remove Chapters & Sections</h3>
				<span class="fr expand-collapse-text">Click to collapse</span>				
				<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
			</div>
	
			<div class="content-module-main">
				<p>Click on the item that you want removed.</p>
					<div class="cf" id="">
						<div id="removable-box">{REMOVE-CHAPTERS-SECTIONS-HERE}</div>
						<div class="remove-warning">
							<div class="warning-box round" id="warning-msg-remove">Warning message.</div>
							<a href="#" class="button round blue image-right text-upper ic-delete fl" id="btn-delete-sch">Delete</a>
						</div>
					</div>
			</div> 
		</div> <!-- end content-module -->	
	</div> <!-- end side-content fr -->

	';

$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url.'template/', $str);

$str =  str_replace('{REGISTER-FORM-HERE}', $register_form, $str);

$first_last = $first_name . " " . $last_name;


$str =  str_replace('{YOUR-NAME-HERE}', $first_last, $str);
$str =  str_replace('{FIRST-NAME-HERE}', $first_name, $str);
$str =  str_replace('{LAST-NAME-HERE}', $last_name, $str);
$str =  str_replace('{USERNAME-HERE}', $username, $str);
$str =  str_replace('{STUDENT-ID-HERE}', $user_student_id, $str);
$str =  str_replace('{EMAIL-HERE}', $user_email, $str);
$str =  str_replace('{USER-ROLE-HERE}', $user_role, $str);


//echo $str; 

// let's get the chapter + sections

function get_chapters() {
	global $book_group;
	$q_get_chapters = "SELECT * FROM section WHERE parent = -1 AND cubogroup = " . $book_group;
	$result = mysql_query($q_get_chapters);
	
	while ($row = mysql_fetch_assoc($result)) {
		echo $row['description'];
	}
}

function get_chapters_array() {
	global $book_group;
	$q_get_chapters = "SELECT * FROM section WHERE parent = -1 AND cubogroup = " . $book_group;
	$result = mysql_query($q_get_chapters);
	//$return_array = array();
	
	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$return_array[$i] = $row['description'];
		
		$i++;
	}
	
	return $return_array;
}

function get_chapters_assocarray() {
	global $book_group;
	$q_get_chapters = "SELECT * FROM section WHERE parent = -1 AND cubogroup = " . $book_group;
	$result = mysql_query($q_get_chapters);
	
	//$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$id = $row['id'];
		$return_array[$id] = $row['description'];
		
		//$i++;
	}
	
	return $return_array;
}

function get_sections4chapter($chapter_id) {
	global $book_group;
	$q_get_sections = "SELECT * FROM section where parent = " . $chapter_id . " AND cubogroup = " . $book_group;
	$result = mysql_query($q_get_sections);
	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$return_array[$i] = $row['description'];
		$i++;
	}
	return $return_array;

}

function get_sections4chapter_assoc($chapter_id) {
	global $book_group;
	$q_get_sections = "SELECT * FROM section where parent = " . $chapter_id . " AND cubogroup = " . $book_group;
	$result = mysql_query($q_get_sections);

	while ($row = mysql_fetch_assoc($result)) {
		$id = $row['id'];
		$return_array[$id] = $row['description'];
	}
	return $return_array;

}

function sections4section_assoc($section_id) {
	global $book_group;
	$q_get_sections = "SELECT * FROM section where parent = " . $section_id . " AND cubogroup = " . $book_group;
	$result = mysql_query($q_get_sections);

	while ($row = mysql_fetch_assoc($result)) {
		$id = $row['id'];
		$return_array[$id] = $row['description'];
	}
	return $return_array;
}

function chapters_sections_sortable_ch_output($mode="all") {
	global $book_group;

	$q_get_chapters = "SELECT * FROM `section` WHERE `parent` = -1 AND `cubogroup` = " . $book_group . " ORDER BY `order`";
	$result = mysql_query($q_get_chapters);
	$s = "";
	//$s.= "<div id=\"container-for-sortable\">";
	if (($mode == "chapters" )|| ($mode == "all") ){
		$s.= '<ul class="regular-ul sortable1">';
	} else {
		$s.= '<ul class="regular-ul">';
	}
	while ($row = mysql_fetch_assoc($result)) {
		$ch_id = $row['id'];
		$ch_desc = $row['description'];
		$s.= '<li id="content_'. $ch_id . '">';
		$s.= $ch_desc;
		if (($mode == "sections") || ($mode == "all")) {
			$s.= '<ul class="sortable2">';
		} else {
			$s.= '<ul>';
		}
		$get_ch_sections = "SELECT * FROM `section` WHERE `parent` = " . $ch_id . " AND `cubogroup` = " . $book_group . " ORDER BY `order`";
		$s_results = mysql_query($get_ch_sections);
		while ($srow = mysql_fetch_assoc($s_results)) {
			$section_id = $srow['id'];
			$section_desc = $srow['description'];
			$s.= '<li id="content_' . $section_id . '">';
			$s.= $section_desc;
			if (($mode == "sections4section") || ($mode == "all")) {
				$s.= '<ul class="sortable3">';
			} else {
				$s.= '<ul>';
			}
			$get_sections4section = "SELECT * FROM `section` WHERE `parent` = " . $section_id . " AND `cubogroup` = " . $book_group . " ORDER BY `order`";
			$s4s_results = mysql_query($get_sections4section);
			while ($s4srow = mysql_fetch_assoc($s4s_results)) {
				$s4s_id = $s4srow['id'];
				$s4s_desc = $s4srow['description'];
				$s.= '<li id="content_' . $s4s_id . '">';
				$s.= $s4s_desc;
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


function chapters_sections_editable_output() {
	global $book_group;
	$q_get_chapters = "SELECT * FROM `section` WHERE `parent` = -1 AND `cubogroup` = " . $book_group . " ORDER BY `order`";
	$result = mysql_query($q_get_chapters);
	$s = "";
	
	$s.= '<ul class="regular-ul">';
	while ($row = mysql_fetch_assoc($result)) {
		$ch_id = $row['id'];
		$ch_desc = $row['description'];
		$s.= '<li>';
		$s.= '<div class="edit-dash-arrsections" id="' . $ch_id . '">' . $ch_desc . '</div>';
		$s.= '<ul>';
		$get_ch_sections = "SELECT * FROM `section` WHERE `parent` = " . $ch_id . " AND `cubogroup` = " . $book_group . " ORDER BY `order`";
		$s_results = mysql_query($get_ch_sections);
		while ($srow = mysql_fetch_assoc($s_results)) {
			$section_id = $srow['id'];
			$section_desc = $srow['description'];
			$s.= '<li>';
			$s.= '<div class="edit-dash-arrsections" id="' . $section_id . '">' . $section_desc . '</div>';
			$s.= '<ul>';
			$get_sections4section = "SELECT * FROM `section` WHERE `parent` = " . $section_id . " AND `cubogroup` = " . $book_group . " ORDER BY `order`";
			$s4s_results = mysql_query($get_sections4section);
			while ($s4srow = mysql_fetch_assoc($s4s_results)) {
				$s4s_id = $s4srow['id'];
				$s4s_desc = $s4srow['description'];
				$s.= '<li>';
				$s.= '<div class="edit-dash-arrsections" id= "'. $s4s_id . '">' . $s4s_desc . '</div>';
				$s.= '</li>';
			}
			$s.= '</ul>';
			$s.= '</li>';
		}
		$s.= '</ul>';
		$s.= '</li>';
	}
	$s.= '</ul>';
	
	return $s;
}

function remove_chapters_sections() {
	global $book_group;
	$q_get_chapters = "SELECT * FROM `section` WHERE `parent` = -1 AND `cubogroup` = " . $book_group . " ORDER BY `order`";
	$result = mysql_query($q_get_chapters);
	$s = "";
	
	$s.= '<ul class="regular-ul">';
	while ($row = mysql_fetch_assoc($result)) {
		$ch_id = $row['id'];
		$ch_desc = $row['description'];
		$s.= '<li>';
		$s.= '<div class="remove-click" id="remove_' . $ch_id . '">' . $ch_desc . '</div>';
		$s.= '<ul>';
		$get_ch_sections = "SELECT * FROM `section` WHERE `parent` = " . $ch_id . " AND `cubogroup` = " . $book_group . " ORDER BY `order`";
		$s_results = mysql_query($get_ch_sections);
		while ($srow = mysql_fetch_assoc($s_results)) {
			$section_id = $srow['id'];
			$section_desc = $srow['description'];
			$s.= '<li>';
			$s.= '<div class="remove-click" id="remove_' . $section_id . '">' . $section_desc . '</div>';
			$s.= '<ul>';
			$get_sections4section = "SELECT * FROM `section` WHERE `parent` = " . $section_id . " AND `cubogroup` = " . $book_group . " ORDER BY `order`";
			$s4s_results = mysql_query($get_sections4section);
			while ($s4srow = mysql_fetch_assoc($s4s_results)) {
				$s4s_id = $s4srow['id'];
				$s4s_desc = $s4srow['description'];
				$s.= '<li>';
				$s.= '<div class="remove-click" id= "remove_'. $s4s_id . '">' . $s4s_desc . '</div>';
				$s.= '</li>';
			}
			$s.= '</ul>';
			$s.= '</li>';
		}
		$s.= '</ul>';
		$s.= '</li>';
	}
	$s.= '</ul>';
	
	return $s;
}

function add_chapters_sections() {
	global $book_group;

	
	$s='';
	$s.= '
		<form class="cf" onsubmit="return false">
			<fieldset>
			<p>
				<label  id="label-add" for="simple-input-add-chapter">Add Chapter to Root</label>
				<input type="text" class="round default-size-input" id="simple-input-add-chapter" />
			</p>
			
				<input type="submit" class="btn-add-chapter button round blue image-right text-upper ic-add" />
				<a href="#" class="button round blue text-upper image-left ic-left-arrow btn-return-add-chapter">Return</a></li>
				<input type="submit" class="btn-add-section button round blue image-right text-upper ic-add" />
			
			</fieldset>
		</form>
		
	';
	
	$q_get_chapters = "SELECT * FROM `section` WHERE `parent` = -1 AND `cubogroup` = " . $book_group . " ORDER BY `order`";
	$result = mysql_query($q_get_chapters);
	
	$s.= '<br />';
	$s.= '<p>Click on a parent item below where you want to add a child (sections can only go two levels deep).';
	$s.= '<div id="container-for-add">';
	$s.= '<ul class="regular-ul" id="add-ul-section">';
	while ($row = mysql_fetch_assoc($result)) {
		$ch_id = $row['id'];
		$ch_desc = $row['description'];
		$s.= '<li>';
		$s.= '<div class="add-click" id="add_' . $ch_id . '">' . $ch_desc . '</div>';
		$s.= '<ul>';
		$get_ch_sections = "SELECT * FROM `section` WHERE `parent` = " . $ch_id . " AND `cubogroup` = " . $book_group . " ORDER BY `order`";
		$s_results = mysql_query($get_ch_sections);
		while ($srow = mysql_fetch_assoc($s_results)) {
			$section_id = $srow['id'];
			$section_desc = $srow['description'];
			$s.= '<li>';
			$s.= '<div class="add-click" id="add_' . $section_id . '">' . $section_desc . '</div>';
			$s.= '<ul>';
			$get_sections4section = "SELECT * FROM `section` WHERE `parent` = " . $section_id . " AND `cubogroup` = " . $book_group . " ORDER BY `order`";
			$s4s_results = mysql_query($get_sections4section);
			while ($s4srow = mysql_fetch_assoc($s4s_results)) {
				$s4s_id = $s4srow['id'];
				$s4s_desc = $s4srow['description'];
				$s.= '<li>';
				$s.= '<div id= "'. $s4s_id . '">' . $s4s_desc . '</div>';
				$s.= '</li>';
			}
			$s.= '</ul>';
			$s.= '</li>';
		}
		$s.= '</ul>';
		$s.= '</li>';
	}
	$s.= '</ul>';
	$s.= '</div>';
	$s.= '</p>';	
	
	return $s;
}

/*
$arr = get_chapters_assocarray();
foreach ($arr as $id => $chapter) {
	echo $chapter;
	$sections = get_sections4chapter_assoc($id);	
	foreach ($sections as $sid => $section) {
		echo $section;
		$sections4section = sections4section_assoc($sid);
		foreach ($sections4section as $id => $section) {
			echo $section;
		}
	}
	echo "<br></br>";
}*/


$ch_sec_output = chapters_sections_sortable_ch_output();
$ch_sec_editable_output = chapters_sections_editable_output();
$ch_sec_remove_output = remove_chapters_sections();
$ch_sec_add_output = add_chapters_sections();
$str =  str_replace('{CHAPTERS-SECTIONS-HERE}', $ch_sec_output, $str);
$str =  str_replace('{CHAPTERS-SECTIONS-EDITABLE-HERE}', $ch_sec_editable_output, $str);
$str =  str_replace('{REMOVE-CHAPTERS-SECTIONS-HERE}', $ch_sec_remove_output, $str);
$str =  str_replace('{ADD-CHAPTERS-SECTIONS-HERE}', $ch_sec_add_output, $str);
$str = top_bar($str);
$str = str_replace('{CUBOCUBE-BOOK-ID}', $book_group, $str);


echo $str;

?>

