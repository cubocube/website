<?php

include_once("general_header.php");
include_once("db.php");
include_once("functions.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);


ob_start();
include('./template/dashboard.html');
$str_register = ob_get_contents();
ob_end_clean();

/*
	The Get variable c determines which book/group they are referencing. 
	If it's not set, then display the book selection page.
*/

$book_group = intval(@$_GET['c']);


if ($book_group <= 0){
	header('Location:  dashboard-cb.php');
	exit();
} 

/*
	Before the user continues check to see that user has agreed to terms of service.
	Note: we only check this for user's that are logged in.
*/
if ($val_log == true && @$_SESSION['tos-'.$book_group] != 1) {
	$q_tos_permissions = "select `id` from `tos_entries` where `user_id` = {$user_id} and `cubocubeID` = {$book_group}";
	$result_tos_permissions = mysql_query($q_tos_permissions);
	if (mysql_num_rows($result_tos_permissions) == 0) {
		header("Location: dashboard_tos_permissions.php?c={$book_group}");
		exit();
	} else {
		// use a session variable so we don't have to query db on every load...
		$_SESSION['tos-'.$book_group] = 1;
	}
}


// Get whatever role the user has in this current book.
$user_role = userRoleInGroup(@$user_id, $book_group);

// This is displayed in the footer, informing the user their role in the cubobook.
$cubobook_user_role_footer = $val_log && $user_role ? "<p>Your user role is {$user_role} in this CuboBook.</p>" : "";


$_SESSION['cubogroup'] = $book_group;


// Check to see if user is trying to register for a book

if (isset($_GET['join'])) {
	$hash = md5($_SESSION["logged"]."join");

    // Note: This method of joining gives them a student role.
	if ($_GET['join'] == $hash) {
		$q = "INSERT INTO `registered_groups`
                (`userId`, `groupId`, `role`)
              VALUES ($user_id, $book_group, '".STUDENT_ROLE."')";
		mysql_query($q);
	}
}



$register_form = '';


$chapters = '';
$q = "SELECT * FROM `section` WHERE `parent` = -1  AND `cubogroup` = " . $book_group . " ORDER BY `order`";
$result = mysql_query($q);

$id = (isset($_GET['a']) and ctype_digit($_GET['a'])) ? intval($_GET['a']) : 0;

if ($id <= 0) {
	$q_id = "SELECT * FROM `section`
             WHERE `parent`    = -1
               AND `cubogroup` = $book_group
             ORDER BY `order` ASC";
	$result_id = mysql_query($q_id);
    if (!$result_id or mysql_num_rows($result_id) == 0)
        $id = 0;
    else {
        $row_id = mysql_fetch_assoc($result_id);
        $id = intval($row_id['id']);
    }
}

if ($id > 0) {
    while($row = mysql_fetch_assoc($result)) {
        if ($row['id'] == $id) {
            $chapters.= '<li><a href="?a=' . $row['id'] . '&c='. $book_group . '" class="active-tab">' . $row['description'] . '</a></li>';
        } else {
            $chapters.= '<li><a href="?a=' . $row['id'] . '&c='. $book_group . '">' . $row['description'] . '</a></li>';
        }
    }


    $q = "SELECT * FROM `section`
          WHERE `parent`    = $id
            AND `cubogroup` = $book_group
          ORDER BY `order` ASC";
    $result = mysql_query($q);
    $sections = '';
    while ($row = mysql_fetch_assoc($result)) {
        $sections.= '<li><a href="?a=' . $id . '&b=' . $row['id'] . '&c=' . $book_group . '">' . $row['description'] . '</a></li>';
    }
}
else
    $sections = '<li style="background: #fff; font-size: 13px; padding: 8px;">None</li>';


$sid = (isset($_GET['b']) and ctype_digit($_GET['b'])) ? intval($_GET['b']) : 0;

if ($sid !== 0) {
	$q = "SELECT * FROM `section`
          WHERE `id`        = $sid
            AND `cubogroup` = $book_group
          ORDER BY `order` ASC";
}
else {
    $q = "SELECT * FROM `section`
          WHERE `parent`    = $id
            AND `cubogroup` = $book_group
          ORDER BY `order` ASC";
}
$result = mysql_query($q);
$row = mysql_fetch_assoc($result);
$sid = intval($row['id']);

$q_s = "SELECT * FROM `section` WHERE `parent` = " . $sid . "  AND `cubogroup` = " . $book_group . " ORDER BY `order`";
$result_s = mysql_query($q_s);
$dashboard_main = '';
$array_origins = array();
while ($row_s = mysql_fetch_assoc($result_s)) {
    $flag_assigned = userInSection(@$user_id, $row_s['id']);
	
	$dashboard_main.= '
	
	<div class="content-module dashmain" id="content-module-' . $row_s['id'] . '">
		<div class="content-module-heading cf">
			<h3 class="fl">'.htmlentities($row_s['description']).'</h3>
			<span class="fr expand-collapse-text">Click to collapse</span>				
			<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
		</div>
							
		<div class="content-module-main">';
		
		if(($flag_assigned) || (($user_role == ADMIN_ROLE) || ($user_role == INSTRUCTOR_ROLE) || ($book_group == 1))) {
			$dashboard_main.='<h5 class="fl save-warning" id="save-warning-' . $row_s['id'] . '"></h5>';
			$dashboard_main.='<h5 class="fl conflict-warning" id="conflict-warning-' . $row_s['id'] . '"></h5>';
			$dashboard_main.='<h5 class="fr"><a href="javascript:void(0);" class="destroy-edit-section" id="main-' . $row_s['id'] . '">Main</a> | <span class="edit-save" id="edit-save-"' . $row_s['id'] . '><a href="javascript:void(0);" class="save-section" id="save-section-' . $row_s['id'] . '">Save</a> <a href="javascript:void(0);" class="edit-section" id="edit-section-' . $row_s['id'] . '">Edit</a> |</span> <a class="discussion-dash-section" id="discussion-dash-section-' . $row_s['id'] . '" href="javascript:void(0);">Discussion</a> | 
				<a href="javascript:void(0);" class="history-section" id="history-' . $row_s['id'] . '">History</a>';
			$dashboard_main.=' | {CUBE-PLACEHOLDER-HERE} </h5>';
			
		} else {
			$dashboard_main.='<h5 class="fl save-warning" id="save-warning-' . $row_s['id'] . '"></h5>';
			$dashboard_main.='<h5 class="fr"><a href="javscript:void(0)" class="destroy-edit-section" id="main-' . $row_s['id'] . '">Main</a> | <a class="discussion-dash-section" id="discussion-dash-section-' . $row_s['id'] . '" href="javascript:void(0);">Discussion</a>';
			$dashboard_main.=' | {CUBE-PLACEHOLDER-HERE} </h5>';
			
		}
		
		$dashboard_main.='</h5>';
	
	$dashboard_main.=
			   '<div class="stripe-separator"></div>
				<div style="line-height: 1.2em; font-family: sans-serif; font-size: 1.2em; " 
					class="content-section" id="content-section-' . $row_s['id'] . '">{DESCRIPTION-BOX-' . $row_s['id'] . '}
				</div>
				<!-- <p>The unique id of origin is (for dev purposes): {CONTENT-PIECE-ID-HERE} </p> -->
				<div style="display: none;" class="origin-dash" id="origin-dash-' . $row_s['id'] . '">{CONTENT-PIECE-ID-HERE}</div>
				<div class="history" id="history-box-'.$row_s['id'].'"></div>
				<div class="discussion" id="discussion-box-'.$row_s['id'].'">
				
					<div class="comments-container">

						<ul class="nested-comments nostyle">
							<li>';
	// if user is logged in
	if ($val_log) {
		$dashboard_main.=
							'<div class="comment">
								<p><a href="javascript:void(0);" class="author">' . $first_last . '</a></p>
								<form action="javascript:void(0);" class="form-discussion-main" id="form-discussion-main-' . $row_s['id'] . '">
									<fieldset>
					 					<p>
	                						<input type="text" class="full-size-input2" id="input-discussion-main-' . $row_s['id'] . '" placeholder="Comment..." />
	            						</p>
	            					</fieldset>
	            				</form>
							 </div>';
	} else {
		$dashboard_main.=
							'<div class="comment">
								<form action="javascript:void(0);" class="form-discussion-main" id="form-discussion-main-' . $row_s['id'] . '">
									<fieldset>
					 					<a href="login.php">Login to add a new comment</a>
	            					</fieldset>
	            				</form>
							 </div>';
	} 

	$dashboard_main.='</li>
					</ul>

					<div class="inner-comments" id="inner-comments-' . $row_s['id'] . '">
					</div> <!-- END INNER COMMENTS -->

				</div>
			
			</div>
			<!-- <div class="stripe-separator"></div> -->
			<!-- <h5 class="generate-permalink fr" id="generate-permalink-' . $row_s['id'] . '">#</h5> -->
			<div class="stripe-separator"></div>
			<div class="fr">{VIEW-CUBES-HERE}{PERMALINK-PIN-HERE}</div><br />
		</div>
	</div> <!-- end content-module -->
	
	';
	
	$dashboard_main.='	
	';
	
	
	$q_get_desc = "SELECT * FROM `content_piece` WHERE `section_ch_id` = " . $row_s['id'] . "  AND `cubogroup` = " . $book_group . " ORDER BY `time_modified` DESC";
	//echo $q_get_desc;
	$result_get_desc = mysql_query($q_get_desc);
	$row_get_desc = mysql_fetch_assoc($result_get_desc);
	$dashboard_main = str_replace("{DESCRIPTION-BOX-$row_s[id]}", $row_get_desc['content'], $dashboard_main);
	$dashboard_main = str_replace('{CONTENT-PIECE-ID-HERE}', $row_get_desc['unique_id'], $dashboard_main);

	// origin array key = section id value = origin content id

	$array_origins[$row_s['id']] = $row_get_desc['unique_id'];



	// we need to check to see if there are any content pieces for this particular section
	// because if no edits have been made i.e. no content then there is no point in cubing
	// and we will encounter some problems

	$q = "SELECT * FROM `content_piece` WHERE `section_ch_id` = " . $row_s['id'] . " AND `cubogroup` = " . $book_group;;
	$result = mysql_query($q);

	if (mysql_num_rows($result) != 0) {

		if ($val_log) {
			$q = "SELECT * FROM `cubes` WHERE `contentid` = " . $row_get_desc['unique_id'] . " AND `userid` = " . $user_id;
			$result = mysql_query($q);
			$numrows = mysql_num_rows($result);
			if ($numrows == 0) { // user has not cubed yet
				$cubeplaceholder = '<a class="cube-this" id="cube-this-' . $row_get_desc['unique_id'] . '" href="javascript:void(0);">Cube (<span id="cube-this-num-' . $row_get_desc['unique_id'] . '">{NUM-CUBES-HERE}</span>)</a>';
				$cubeplaceholder.= '<a class="cube-this-already hide" id="cube-this-already-' . $row_get_desc['unique_id'] . '" href="javascript:void(0);">Cubed (<span id="cube-num-already-' . $row_get_desc['unique_id'] . '">{NUM-CUBES-HERE}</span>)</a>';
			} else {
				$cubeplaceholder = '<a class="cube-this hide" id="cube-this-' . $row_get_desc['unique_id'] . '" href="javascript:void(0);">Cube (<span id="cube-this-num-' . $row_get_desc['unique_id'] . '">{NUM-CUBES-HERE}</span>)</a>';
				$cubeplaceholder.= '<a class="cube-this-already" id="cube-this-already-' . $row_get_desc['unique_id'] . '" href="javascript:void(0);">Cubed (<span id="cube-num-already-' . $row_get_desc['unique_id'] . '">{NUM-CUBES-HERE}</span>)</a>';
			}
		} else {
			$cubeplaceholder = '<a href="login.php">Cube (<span id="cube-this-num-' . $row_get_desc['unique_id'] . '">{NUM-CUBES-HERE}</span>)</a>';
		}

		$q = "SELECT * FROM `cubes` WHERE `contentid` = " . $row_get_desc['unique_id'];
		$result = mysql_query($q);
		$numcubes = mysql_num_rows($result);
		$cubetext = str_replace('{NUM-CUBES-HERE}', $numcubes, $cubeplaceholder);

		$dashboard_main = str_replace('{CUBE-PLACEHOLDER-HERE}', $cubetext, $dashboard_main);

	} else {
		$cubetext = '<a href="javascript:void(0);">Cubing Unavailable</a>';
		$dashboard_main = str_replace('{CUBE-PLACEHOLDER-HERE}', $cubetext, $dashboard_main);
	}

	$viewcubes = '<img class="view-cubes pointer" id="view-cubes-' . $row_get_desc['unique_id'] . '" src="template/img/ic_dashboard.png" />';
	$dashboard_main = str_replace('{VIEW-CUBES-HERE}', $viewcubes , $dashboard_main);

	$permatext = '<img class="generate-permalink pointer" id="generate-permalink-' . $row_s['id'] . '" src="template/img/ic_pin.png" />';
	$dashboard_main = str_replace('{PERMALINK-PIN-HERE}', $permatext, $dashboard_main);
}

if ($id <= 0) {
    $dashboard_main =
        '<div class="content-module dashmain">
           <div class="content-module-main">
             This book has no sections yet. ';
    if ($user_role == ADMIN_ROLE or $user_role == INSTRUCTOR_ROLE) {
        $dashboard_main .=
            'You can help by <a href="dashboard-arrsections.php?c='.$book_group.'">adding some</a>.<br/><br/>';
        $dashboard_main .= 'Be sure to also add a license section to your CuboBook to clarify content restrictions for your readers. You can see an <a href="/dashboard.php?a=1178&b=1192&c=103#content-module-1194">example here</a>.';
    }
    $dashboard_main .=
        '  </div>
         </div>';
}

// Using session to store array origins
$_SESSION['array_origins'] = $array_origins;


$register_page = str_replace('{URL-BASE}', $url . "template/", $str_register);

$str =  str_replace('{REGISTER-FORM-HERE}', $register_form, $register_page);
$str =  str_replace('{CHAPTERS-HERE}', $chapters, $str);
$str =  str_replace('{SECTIONS-HERE}', $sections, $str);
$str =  str_replace('{DASHBOARD-MAIN-HERE}', $dashboard_main, $str);
$str = top_bar($str);
$str = str_replace('{CUBOCUBE-BOOK-ID}', $book_group, $str);
$str = str_replace('{CUBOBOOK-USER-ROLE-INFO}', $cubobook_user_role_footer, $str);

// Update Page Title - based on current book being viewed.
$q = "SELECT `id`,`name` FROM `groups` WHERE `id` = '" . $book_group . "'";
$result = mysql_query($q);
$row = mysql_fetch_assoc($result);
$str = str_replace('{TOP-TITLE-PLACEHOLDER}', $row['name'] . " - CuboCube", $str);
// replace footer placeholders
$str = str_replace('{CUBOBOOK-NAME}', $row['name'], $str);
$str = str_replace('{CUBOBOOK-ID}', $row['id'], $str);


if ($id <= 0) {
    $str = str_replace('</body>',
                       '<script type="text/javascript">
                          $("#chapter-control-container").hide();
                       </script>
                       </body>',
                       $str);
}

echo $str; 



?>


