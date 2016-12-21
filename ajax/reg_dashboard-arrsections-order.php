<?php

include_once("../db.php");
include_once("../general_header.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

parse_str($_POST['pages'], $pageOrder);

//print_r($pagesOrder);

$array = array();

$cubogroup = $_SESSION['cubogroup'];

foreach ($pageOrder['content'] as $key => $value) {
	$q = "UPDATE section SET section.order = '" . $key . "' WHERE section.id = '" . $value . "'";
    mysql_query($q);
}

function chapters_sections_editable_output() {
	global $cubogroup;
	$q_get_chapters = "SELECT * FROM `section` WHERE `parent` = -1 AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
	$result = mysql_query($q_get_chapters);
	$s = "";
	
	$s.= '<ul class="regular-ul">';
	while ($row = mysql_fetch_assoc($result)) {
		$ch_id = $row['id'];
		$ch_desc = $row['description'];
		$s.= '<li>';
		$s.= '<div class="edit-dash-arrsections" id="' . $ch_id . '">' . $ch_desc . '</div>';
		$s.= '<ul>';
		$get_ch_sections = "SELECT * FROM `section` WHERE `parent` = " . $ch_id . " AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
		$s_results = mysql_query($get_ch_sections);
		while ($srow = mysql_fetch_assoc($s_results)) {
			$section_id = $srow['id'];
			$section_desc = $srow['description'];
			$s.= '<li>';
			$s.= '<div class="edit-dash-arrsections" id="' . $section_id . '">' . $section_desc . '</div>';
			$s.= '<ul>';
			$get_sections4section = "SELECT * FROM `section` WHERE `parent` = " . $section_id . " AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
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

function chapters_sections_sortable_ch_output($mode="all") {
	global $cubogroup;

	$q_get_chapters = "SELECT * FROM `section` WHERE `parent` = -1 AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
	$result = mysql_query($q_get_chapters);
	$s = "";
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
		$get_ch_sections = "SELECT * FROM `section` WHERE `parent` = " . $ch_id . " AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
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
			$get_sections4section = "SELECT * FROM `section` WHERE `parent` = " . $section_id . " AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
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
	
	return $s;
}


function add_chapters_sections() {
	global $cubogroup;

	$s='';
	
	$q_get_chapters = "SELECT * FROM `section` WHERE `parent` = -1 AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
	$result = mysql_query($q_get_chapters);	
	$s.= '<ul class="regular-ul" id="add-ul-section">';
	while ($row = mysql_fetch_assoc($result)) {
		$ch_id = $row['id'];
		$ch_desc = $row['description'];
		$s.= '<li>';
		$s.= '<div class="add-click" id="add_' . $ch_id . '">' . $ch_desc . '</div>';
		$s.= '<ul>';
		$get_ch_sections = "SELECT * FROM `section` WHERE `parent` = " . $ch_id . " AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
		$s_results = mysql_query($get_ch_sections);
		while ($srow = mysql_fetch_assoc($s_results)) {
			$section_id = $srow['id'];
			$section_desc = $srow['description'];
			$s.= '<li>';
			$s.= '<div class="add-click" id="add_' . $section_id . '">' . $section_desc . '</div>';
			$s.= '<ul>';
			$get_sections4section = "SELECT * FROM `section` WHERE `parent` = " . $section_id . " AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
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

function remove_chapters_sections() {
	global $cubogroup;

	$q_get_chapters = "SELECT * FROM `section` WHERE `parent` = -1 AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
	$result = mysql_query($q_get_chapters);
	$s = "";
	
	$s.= '<ul class="regular-ul">';
	while ($row = mysql_fetch_assoc($result)) {
		$ch_id = $row['id'];
		$ch_desc = $row['description'];
		$s.= '<li>';
		$s.= '<div class="remove-click" id="remove_' . $ch_id . '">' . $ch_desc . '</div>';
		$s.= '<ul>';
		$get_ch_sections = "SELECT * FROM `section` WHERE `parent` = " . $ch_id . " AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
		$s_results = mysql_query($get_ch_sections);
		while ($srow = mysql_fetch_assoc($s_results)) {
			$section_id = $srow['id'];
			$section_desc = $srow['description'];
			$s.= '<li>';
			$s.= '<div class="remove-click" id="remove_' . $section_id . '">' . $section_desc . '</div>';
			$s.= '<ul>';
			$get_sections4section = "SELECT * FROM `section` WHERE `parent` = " . $section_id . " AND `cubogroup` = " . $cubogroup . " ORDER BY `order`, `id`";
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


function check_depth($leaf) {
	global $cubogroup;

	// if leaf is already two levels deep return false otherwise return true
	$count_depth = 0;

	$q_getParent = "SELECT `parent` FROM `section` WHERE `id` = " . $leaf . " AND `cubogroup` = " . $cubogroup;
	$result_getParent = mysql_query($q_getParent);
	$row_getParent = mysql_fetch_assoc($result_getParent);

	if ($row_getParent['parent'] == -1) {
		return True;
	} else {
		$q = "SELECT `parent` FROM `section` WHERE `id` = " . $row_getParent['parent'] . " AND `cubogroup` = " . $cubogroup;
		$result = mysql_query($q);
		$row = mysql_fetch_assoc($result);

		if ($row['parent'] == -1) {
			return True;
		} else {
			return False;
		}
	}


}



if (stripslashes(strip_tags($_POST['updated'])) == 'sortable') {
	echo chapters_sections_editable_output();
	
} else if (stripslashes(strip_tags($_POST['updated'])) == 'editable') {
	echo chapters_sections_sortable_ch_output();
} else if (stripslashes(strip_tags($_POST['updated'])) == 'removed') {
	$element_id = stripslashes(strip_tags($_POST['element_id']));
	$array = explode('_',$element_id);
	$id = $array[1];
	
	// we also need to decide if element has any children to also remove
	
	$q = "SELECT * FROM `section` WHERE `parent` = " . $id . " AND `cubogroup` = " . $cubogroup;
	$result = mysql_query($q);
	$q_remove_itself = "DELETE FROM `section` WHERE `id` = " . $id;
	
	if (mysql_num_rows($result) == 0) {
		// this item doesn't have any children
		$q_remove_children = "DELETE FROM `section` WHERE parent = " . $id;
		
		mysql_query($q_remove_children);
		mysql_query($q_remove_itself);
	
	
	} else {
		// this item has one or more children
		mysql_query($q_remove_itself);
	}
		
	
		
	echo "item has been removed";
} else if (stripslashes(strip_tags($_POST['updated'])) == 'added') {
	$ch = stripslashes(strip_tags($_POST['chapter']));
	$q = "INSERT INTO `section` (`id`, `cubogroup`, `parent`, `order`, `description`) VALUES (NULL, '" . $_SESSION['cubogroup'] . "', '-1', '1000000', '" . $ch . "');";
	$result = mysql_query($q);
	return;
} else if (stripslashes(strip_tags($_POST['updated'])) == 'added-section') {
	$section_name = stripslashes(strip_tags($_POST['section']));
	$add_to = stripslashes(strip_tags($_POST['to']));
	$array = explode('_',$add_to);
	$to_id = $array[1];
	// we need to only add the item if depth is not greater than 2. 
	
	$depth_flag = false;
	$depth_flag = check_depth($to_id);
	
	if ($depth_flag) {
		$q = "INSERT INTO `section` (`id`, `cubogroup`, `parent`, `order`, `description`) VALUES (NULL, '" . $_SESSION['cubogroup'] . "', '" . $to_id . "', '1000000', '" . $section_name . "');";
		$result = mysql_query($q);
		echo add_chapters_sections();
		return;
	} else { // don't add new item
		echo add_chapters_sections();
		return;
	}
} else if (stripslashes(strip_tags($_POST['updated'])) == 'added-chapter') {
	$ch_name = stripslashes(strip_tags($_POST['chapter']));
	$q = "INSERT INTO `section` (`id`, `cubogroup`, `parent`, `order`, `description`) VALUES (NULL, '" . $_SESSION['cubogroup'] . "', -1, '1000000', '" . $ch_name . "');";
	$result = mysql_query($q);
	echo add_chapters_sections();
	return;
} else if (stripslashes(strip_tags($_POST['get'])) == 'add') {
	echo add_chapters_sections();
	return;
} else if (stripslashes(strip_tags($_POST['get'])) == 'sortable') {
	echo chapters_sections_sortable_ch_output();
	return;
} else if (stripslashes(strip_tags($_POST['get'])) == 'editable') {
	echo chapters_sections_editable_output();
	return;
} else if (stripslashes(strip_tags($_POST['get'])) == 'removable') {
	echo remove_chapters_sections();
	return;
}




//'updated=added-chapter&chapter=' + chInput;
//var stringData ='updated=added-section&section=' + chInput + '&to=' + add_element_id;

mysql_close($con);


?>