<?php

/*
  reg_dashboard.php requires that the user is currently logged. 
  Otherwise, we die with a 'logged_out' response. 
*/

include_once("../db.php"); 
include_once("../general_header.php");
include_once("../functions.php");
include_once("../model/comment.class.php");
include_once('../lib/htmLawed.php');
$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);


function get_ip_address() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }
}

/*
  Calling check_logged() should automatically renew the 
  session variable if the user is logged in.
  By default, the session variable lasts for 24 minutes.
*/
if (! check_logged()) {
	die('logged_out');
} 

$mode = stripslashes(strip_tags(@$_POST['mode']));

if ($mode == 'save') {
	$sid    = intval($_POST['id']);
    if (get_magic_quotes_gpc())
        $text = sanitizeHTML(stripslashes($_POST['text']));
    else
        $text = sanitizeHTML($_POST['text']);
	$origin = intval($_SESSION['array_origins'][$sid]);
	
	$cubogroup = $_SESSION['cubogroup'];
    $user_role = userRoleInGroup($user_id, $cubogroup);
    
    // Check that this user has permission to edit this section.
    if ($user_role == 0 or $user_role == INSTRUCTOR_ROLE or
        userInSection($user_id, $sid)) {
        $q = "INSERT INTO `content_piece` (`unique_id`, `cubogroup`, `section_ch_id`, `origin`, `content`, `active`, `created_by`, `date_modified`, `time_modified`, `ip_of_modifier`) VALUES (NULL, '" . $cubogroup . "', '" . $sid . "', '" . $origin . "', ' {TEXT-HERE} ', '0', '" . $user_id . "', '" . date("Y-m-d") . "', '" . time() . "', '" . get_ip_address() . "')";
        $q = str_replace("{TEXT-HERE}",mysql_real_escape_string($text),$q);
        $result = mysql_query($q);

        // need to echo back the unique id that was just created
        // so it can be updated in the ui (hidden), so that 
        // we can set the origin properly next time we save!
        // LAST_INSERT_ID() gets the last inserted row on 
        // THIS connection so we don't have to worry about
        // other simultaneous connections... phew

        $q = "SELECT * FROM `content_piece` WHERE `unique_id` = LAST_INSERT_ID()";
        $result = mysql_query($q);
        $row = mysql_fetch_assoc($result);

        $_SESSION['array_origins'][$sid] = $row['unique_id'];

        $q = "SELECT * FROM `content_piece` WHERE `origin` = " . $origin; 
        $result = mysql_query($q); 
        $num = mysql_num_rows($result);

        if ($num >= 2) {
            $q = "UPDATE `content_piece` SET `conflictflag` =  '1' WHERE `unique_id` = LAST_INSERT_ID()";
            $result = mysql_query($q);

            // we also need to update the conflict flag of other edits 
            // that have the same origin

            // let's looooop

            $q = "SELECT * FROM `content_piece` WHERE `origin` = " . $origin;
            $result = mysql_query($q);

            while ($row = mysql_fetch_assoc($result)) {
                $qupdate = "UPDATE `content_piece` SET `conflictflag` =  '1' WHERE `unique_id` = " . $row['unique_id'];
                $result = mysql_query($qupdate);
            }

            echo 'conflict';
        }
    }
    else
        echo 'denied';
	
} else if ($mode == 'history') {
	$id = stripslashes(strip_tags($_POST['id']));
	$num_times = stripslashes(strip_tags($_POST['numtimes']));
	$num10 = $num_times * 10;
	$important = stripslashes(strip_tags($_POST['important']));

	$table = '

	<table class="table-history" id="table-history-' . $id . '">
		<thead>
			<tr>
				<th><input class="main-checkbox" id="main-checkbox-' . $id . '" type="checkbox" /></th>
				<th>ID</th>
				<th style="display:none;">Origin</th>
				<th>Name</th>
				<th class="username-history">Username</th>
				<th class="date-time-history">Date & Time</th>
				<th>Content</th>
				<th class="user-role-history">User Role</th>
				<th>Important</th>
				<th>Cubes</th>
				<th><!-- Conflict --><center><img src="'.$url.'template/img/ic_documents.png" /></center></th>

			</tr>
		</thead> 
		<tfoot>
			<tr>
				<td colspan="10" class="table-footer">
					<form action="javascript:void(0);">
						<fieldset>
							<label for="table-footer-actions">With selected</label>
							<select name="table-footer-actions" class="table-footer-actions-history" id="table-footer-actions-history-' . $id . '">
								<option value="op1">Mark as Important</option>
								<option value="op2">Mark as Not Important</option>
							</select>
							<div class="important-container-history" id="important-container-history-' . $id . '">
								<br /><br />
								<input type="text" class="round default-size-input important-input-history" id="important-input-history-' . $id . '" placeholder="Enter optional message." /><br /><br />
							</div>
							<a href="javascript:void(0);" type="submit" class="button round blue text-upper apply-to-selected-history" id="apply-to-selected-history-' . $id . '">Apply to selected</a>
						</fieldset>
						<br />
						<br /><br />
						<div class="cf">
							<p class="fl">
								<a href="javascript:void(0);" class="text-upper next-load-history" id="next-load-history-' . $id . '">Load more edits</a> |
								<a href="javascript:void(0);" class="text-upper all-load-history" id="all-load-history-' . $id . '">Show All</a> |
								<a href="javascript:void(0);" class="text-upper important-load-history" id="important-load-history-' . $id . '">Show Important</a>
							</p>
							
							<p class="fr text-upper">
								Total edits: <b id="num-total-edits-' . $id . '">{NUM-EDITS-HERE}</b> | Important edits: <b id="num-important-edits-' . $id . '">{NUM-IMPORTANT-EDITS-HERE}</b>
							</p>
						</div>
					</form>
				</td>
			</tr>
		</tfoot>
		<tbody>
			{TABLE-ELEMENTS-HERE}
		</tbody>
	</table>
	<br />
	
	';
	
	$q = "SELECT * FROM `content_piece` WHERE `section_ch_id` = " . $id . " ORDER BY `time_modified` DESC LIMIT " . $num10;
		if($important) {
			$q = "SELECT * FROM `content_piece` WHERE `section_ch_id` = " . $id . " AND `active` = 1 ORDER BY `time_modified` DESC LIMIT " . $num10;
		}

	$result = mysql_query($q);
	$str = '';
	$number_field = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$qh = "SELECT u.*, r.`role` FROM `users` AS u
                   JOIN `registered_groups` AS r ON r.`userId` = u.`id`
               WHERE u.`id`      = $row[created_by]
                 AND r.`groupId` = $row[cubogroup]";
		$resulth = mysql_query($qh);
		$rowh = mysql_fetch_assoc($resulth);
		
		$idh = $row['unique_id'];
		$nameh = $rowh['first_name'] . " " . $rowh['last_name'];
		$usernameh = $rowh['username'];
		$datetimeh = $row['time_modified']; //need to format in unix timestamp
			$datetimeh = date('Y-m-d (h:i:s A)',$datetimeh);
		$contenth = $row['content'];
		$userroleh = $rowh['role'];

		$originh = $row['origin'];
		$markedimportanth = $row['active'];
			/*
				The show and hide markup refers to the view when the buttons are INITALLY loaded.
			*/


			if ($markedimportanth == 1) {
				$markedimportanth = '<center><img class="get-important-show pointer" id="get-important-show-' . $idh . '" src ="'.$url.'template/img/ic_msg_confirmation.png" /></center>';
				$markedimportanth.= '<center><img class="get-not-important-hide" id="get-not-important-hide-' . $idh . '" src="'.$url.'template/img/ic_msg_error.png" /></center>';
			} else {
				$markedimportanth = '<center><img class="get-important-hide pointer" id="get-important-hide-' . $idh . '" src ="'.$url.'template/img/ic_msg_confirmation.png" /></center>';
				$markedimportanth.= '<center><img class="get-not-important-show" id="get-not-important-show-' . $idh . '" src="'.$url.'template/img/ic_msg_error.png" /></center>';
			}
		// $instructorapph = $row['approval'];
		// 	if ($instructorapph == '') {
		// 		$instructorapph = 'None yet';
		// 	}

		$qcubes = "SELECT * FROM `cubes` WHERE `contentid` = " . $row['unique_id'];
		$resultcubes = mysql_query($qcubes);
		$numcubes = mysql_num_rows($resultcubes);

		$number_field = $number_field + 1;

		$origin = $row['origin'];
		$conflictflag = $row['conflictflag'];
		// the origin field in the database is used to determine conflicts
		// when two or more edits originate from the same origin then 
		// that could potentially cause some problems. so we display the 
		// conflict in the user interface
		// informing them that their changes although were saved may not be 
		// displayed

		$ic_no_conflict = '<center><img src="template/img/ic_msg_confirmation.png" /></center>';
		$ic_conflict = '<center><img class="conflict-history pointer" id="conflict-history-' . $idh . '" src="template/img/ic_msg_error.png" /></center>';

		if ($conflictflag == 1){
			$origplace = $ic_conflict;
		} else if ($conflictflag == 0) {
			$origplace = $ic_no_conflict; 
		}
		

		
		$str.='
	
			<tr>
				<td><input class="sub-checkbox sub-checkbox-'.$id.'" id="sub-checkbox-'.$idh.'" type="checkbox" /></td>
				<td class="id-history" id="id-history-' . $idh . '">' . $number_field . '</td>
				<td style="display: none;" class="origin-history" id="origin-history-' . $idh . '">' . $origin . '</td>
				<td class="revision-name" id="revision-name-'.$idh.'">' . $nameh . '</td>
				<td class="revision-uname username-history" id="revision-uname-'.$idh.'">' . $usernameh . '</td>
				<td class="date-time-history" id="revision-date-'.$idh.'">' . $datetimeh . '</td>
				<td>
					<center><img class="get-revision pointer" id="get-revision-'.$idh.'" src="'.$url.'template/img/ic_zoom.png" /></center>
					<a href="dashboard_diff.php?a='.$idh.'" target="blank" class="diff-link">Diff</a>
				</td>
				<td class="userroleh user-role-history" id="userroleh-"' . $idh . '>' . $userroleh . '</td>
				<td id="revision-important-'.$idh.'">' . $markedimportanth . '</td>
				<td>' . $numcubes . ' cubes</td>
				<td>' . $origplace . '</td>
			</tr>
			';
				
	}
	
	
	$table =  str_replace('{TABLE-ELEMENTS-HERE}', $str, $table);


	// number of edits & number of important edits

	$q = "SELECT * FROM `content_piece` WHERE `section_ch_id` = " . $id . "";
	$result = mysql_query($q);
	$numrows = mysql_num_rows($result);
	$q = "SELECT * FROM `content_piece` WHERE `section_ch_id` = " . $id . " AND `active` = 1";
	$result = mysql_query($q);
	$numimportant = mysql_num_rows($result);

	$table = str_replace('{NUM-EDITS-HERE}', $numrows, $table);
	$table = str_replace('{NUM-IMPORTANT-EDITS-HERE}', $numimportant, $table);
	
	echo $table;
} else if ($mode == 'getRevisionContent') {
//var stringData = 'mode=getRevisionContent&id=' + id;
	$id = stripslashes(strip_tags($_POST['id']));
	
	$q = "SELECT * FROM `content_piece` WHERE `unique_id` = " . $id;
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);
	
	echo $row['content'];

} else if ($mode == 'updateNotImportant') {
	$string = stripslashes(strip_tags($_POST['array']));
	$arr = explode(",",$string);
	
	foreach ($arr as $a) {
		$q = "UPDATE `content_piece` SET  `active` =  '0' WHERE `unique_id` = ".$a;
		$result = mysql_query($q);
	}
	
	// so from the first entry we get the section id then we can return number of edits and 
	// number of important edits so those counters are updated on the page
	
	$q = "SELECT * FROM `content_piece` WHERE `unique_id` = " . $arr[0];
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);
	$secid = $row['section_ch_id'];
	$q = "SELECT * FROM `content_piece` WHERE `section_ch_id` = " . $secid . " AND `active` = 1";
	$result = mysql_query($q);
	$numimportant = mysql_num_rows($result);

	$q = "SELECT * FROM `content_piece` WHERE `section_ch_id` = " . $secid;
	$result = mysql_query($q);
	$numedits = mysql_num_rows($result);

	echo $numedits . "-" . $numimportant;

} else if ($mode == 'updateImportant') {
	//var stringData = 'mode=updateImportant&array=' + str_array;
	$string = stripslashes(strip_tags($_POST['array']));
	$msg = stripslashes(strip_tags($_POST['msg']));
	$arr = explode(",",$string);
	
	foreach ($arr as $a) {
		$q = "UPDATE `content_piece` SET  `active` =  '1', `msg` = '" . $msg . "' WHERE `unique_id` = ".$a;
		$result = mysql_query($q);
	}

	// so from the first entry we get the section id then we can return number of edits and 
	// number of important edits so those counters are updated on the page
	
	$q = "SELECT * FROM `content_piece` WHERE `unique_id` = " . $arr[0];
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);
	$secid = $row['section_ch_id'];
	$q = "SELECT * FROM `content_piece` WHERE `section_ch_id` = " . $secid . " AND `active` = 1";
	$result = mysql_query($q);
	$numimportant = mysql_num_rows($result);

	$q = "SELECT * FROM `content_piece` WHERE `section_ch_id` = " . $secid;
	$result = mysql_query($q);
	$numedits = mysql_num_rows($result);

	echo $numedits . "-" . $numimportant; 
	
} else if ($mode == 'getPermalink') {
	//"mode=getPermalink&id="+id, 
	$id = stripslashes(strip_tags($_POST['id']));
	$q = "SELECT * FROM `section` WHERE `id` = ".$id;
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);	
	$parent_b = $row['parent'];

	$q = "SELECT * FROM `section` WHERE `id` = ".$parent_b;
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);
	$parent_a = $row['parent'];

	$parent_c = $row['cubogroup'];

	echo $url."dashboard.php?a=".$parent_a."&b=".$parent_b."&c=".$parent_c."#content-module-".$id;
} else if ($mode == 'getImportantContent') {
	//var stringData = 'mode=getImportantContent&id=' + id;
	$id = stripslashes(strip_tags($_POST['id']));
	$q = "SELECT * FROM `content_piece` WHERE `unique_id` = " . $id;
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);


	if ($row['msg'] == '') {
		echo "<p>A message has not been entered.</p>";
	} else {
		echo "<p>" . $row['msg'] . "</p>";
	}
} else if ($mode == 'cubed') {
	//var stringData = 'mode=cubed&id=' + id;
	$contentid = stripslashes(strip_tags($_POST['id']));
	$type = stripslashes(strip_tags($_POST['type']));

	if ($type != 1) {
		$type = 0; 
	}

	$q = "SELECT * FROM `cubes` WHERE `contentid` = " . $contentid . " AND `userid` = ". $user_id . " AND `type` = " . $type;
	$result = mysql_query($q);

	if (mysql_num_rows($result) == 0) { 
		$q = "INSERT INTO `cubes` (`id`, `type`, `contentid`, `userid`, `datetime`) VALUES (NULL, '" . $type . "', '" . $contentid . "', '" . $user_id . "', '" . time() . "')";
		$result = mysql_query($q);
	}

	$q = "SELECT * FROM `cubes` WHERE `contentid` = " . $contentid . " AND `type` = " . $type;
	//echo $q; echo $type;
	$result = mysql_query($q);

	echo mysql_num_rows($result);

} else if ($mode == 'getcubes') {
	$contentid = stripslashes(strip_tags($_POST['id']));
	$type = stripslashes(strip_tags($_POST['type']));

	if ($type != 1) {
		$type = 0; 
	}

	$q = "SELECT c.*, p.`cubogroup` FROM `cubes` AS c
              JOIN `content_piece` AS p ON p.`unique_id` = c.`contentid`
          WHERE c.`contentid` = $contentid
            AND c.`type`      = $type
          ORDER BY `datetime` DESC";
	$result = mysql_query($q);
	while ($row = mysql_fetch_assoc($result)) {
		// get username from userid
		$qgetuser = "SELECT u.*, r.`role` FROM `users` AS u
                         JOIN `registered_groups` AS r ON r.`userId` = u.`id`
                     WHERE u.`id`      = $row[userid]
                       AND r.`groupId` = $row[cubogroup]";
		$resultgetuser = mysql_query($qgetuser);
		$rowgetuser = mysql_fetch_assoc($resultgetuser);
		$fullname = $rowgetuser['first_name'] . " " . $rowgetuser['last_name'];
		// initialize the string we are going to return
		$str = "";
		$str.= '<p><b>' . $fullname . '</b> (' . $rowgetuser['username'] . ')<br />';
		$str.= '<i>' . date('Y-m-d (h:i:s A)', $row['datetime']) . '</i><br />';

		$str.= '<i>' . $rowgetuser['role'] . '</i></p>';
	}

	if (mysql_num_rows($result) == 0) {
		$str = '<p>No one has Cubed this yet.</p>';
	}

	echo $str;

} else if ($mode == 'uncube') {
	//var stringData = 'mode=uncube&id=' + id;
	//DELETE FROM `cubes` WHERE `contentid` = already158
	$contentid = stripslashes(strip_tags($_POST['id']));
	$type = stripslashes(strip_tags($_POST['type']));

	if ($type != 1) {
		$type = 0; 
	}

	$q = "DELETE FROM `cubes` WHERE `contentid` = " . $contentid . " AND `userid` = ". $user_id . " AND `type` = " . $type;
	$result = mysql_query($q);

	$q = "SELECT * FROM `cubes` WHERE `contentid` = " . $contentid . " AND `type` = " . $type;
	$result = mysql_query($q);

	echo mysql_num_rows($result);
} else if ($mode == 'getConflict') {
	$contentid = stripslashes(strip_tags($_POST['id']));

	$q = "SELECT * FROM `content_piece` WHERE `unique_id` = " . $contentid;
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);

	$q2 = "SELECT * FROM `content_piece` WHERE `origin` = " . $row['origin'];
	$result2 = mysql_query($q2);

	$i = 0;
	$str = "";
	while ($row2 = mysql_fetch_assoc($result2)) {
		$str.= $row2['unique_id'] . ",";
		$i = $i + 1;
	}

	$str = $i . "," . $str;

	echo $str;

	echo $row['origin'];

} else if ($mode == 'newMainComment') {
	$contentid = intval($_POST['id']);
	$post      = sanitizeHTML($_POST['post'], FOR_COMMENT);
    
    // Create a new comment object, populate what we need, and save it.
    $comment = new Comment();
    $comment->setGroupId($contentid);
    $comment->setCreatorId($user_id);
    $comment->setContent($post);
    $comment->setLoggedIn($val_log);
    $comment->save();
    
    // Return its formatted HTML.
    echo $comment->format($user_id);
    
} else if ($mode == 'getDiscussionPosts') {
	$contentid = intval($_POST['id']);

    // Fetch all top-level comments in one query.
	$sql = "SELECT * FROM `discussion`
            WHERE `groupid` = $contentid
              AND `parent`  = -1
            ORDER BY `childtime` DESC";
	$res = mysql_query($sql);

    // Create comment objects from each result row, formatting them.
	$str = '';
	while ($row = mysql_fetch_assoc($res)) {
        $comment  = Comment::fromData($row, $val_log);
        $str     .= $comment->format(@$user_id, Comment::FETCH_CHILDREN);
	}

	echo $str;

} else if ($mode == 'commentOnPost') {
	$parentid = intval($_POST['id']); // Parent comment's ID
	$post     = sanitizeHTML($_POST['post'], FOR_COMMENT);
    
    // Create a new subcomment object, populate what we need, and save it.
    $subcmnt = new Comment();
    $subcmnt->setParentId($parentid);
    $subcmnt->setGroupId($parentid);
    $subcmnt->setCreatorId($user_id);
    $subcmnt->setContent($post);
    $subcmnt->setLoggedIn($val_log);
    $subcmnt->save();
    
    // Return its formatted HTML.
    echo $subcmnt->format($user_id);

} else if ($mode == 'deleteDiscussion') {
	$contentid = intval($_POST['id']);

	// need to check is person author of content?
	// only if they have created the post should they be able to delete it

	$q = "SELECT * FROM `discussion` WHERE `id` = " . $contentid;
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);

	if ($row['created_by'] != $user_id) {
		return;
	} else {
		if ($row['parent'] == '-1') {
			$q = "SELECT * FROM `discussion` WHERE `parent` = " . $row['id'];
			$result = mysql_query($q);

			while ($row = mysql_fetch_assoc($result)) {
				$q = "DELETE FROM `discussion` WHERE `id` = " . $row['id'];
				mysql_query($q);

				$q = "DELETE FROM `cubes` WHERE `contentid` = " . $row['id'];
				mysql_query($q);
			}
		}

		// now let's delete original parent

		$q = "DELETE FROM `discussion` WHERE `id` = " . $contentid;
		mysql_query($q); 

		// we also need to delete the cube associated with this post.

		$q = "DELETE FROM `cubes` WHERE `contentid` = " . $contentid;
		mysql_query($q);
	}

} else if (@$_GET['mode'] == 'pollDiscussions') {

/*
 * Outline of events:
 * 1. Client makes connection with IDs of what they are watching for, and what
 *    the last-checked UTC timestamp for each ID is. Optionally, the IDs of
 *    comments whose existence they wish to check may also be given.
 * 2. Server fetches any comments on those IDs since the specified last time.
 * 3. If asked, server constructs [non-strict] subset of IDs of comments that
 *    still exist from the given list of ones to check.
 * 3. Client gets a JSON object of the form:
 *    ON ERROR: {"error": <MESSAGE>}
 *    ELSE:     {"new": [{"id":     <COMMENT ID>,
 *                        "parent": <PARENT ID>,
 *                        "html":   <FORMATTED HTML},
 *                       ...],
 *               "remaining_ids": [<ID>, ...]}
 */

$raw_ids  = @$_GET['content_ids'];
$raw_utcs = @$_GET['content_utcs'];

// Split the incoming comma-separated lists if IDs and UTC timestamps,
// filtering out elements that are not representations of natural numbers.
$id_list  = array_filter(explode(',', $raw_ids), 'ctype_digit');
$utc_list = array_filter(explode(',', $raw_utcs), 'ctype_digit');

// Confirm that there are an equal number of IDs and UTCs.
if (count($id_list) != count($utc_list))
    die(json_encode(array('error' => 'Unequal numbers of IDs and UTCs')));

// Just stop early if no valid IDs are requested.
if (count($id_list) === 0)
    die(json_encode(array('error' => 'No section IDs to check')));

// Generate all the 'where' conditions for the SQL.
$conds = array();
for ($i = 0; $i < count($id_list); $i++)
    $conds[] = "((d.`groupid` = $id_list[$i] OR e.`groupid` = $id_list[$i]) AND
                 d.`datetime` >= $utc_list[$i])";
$conditions = join(' OR ', $conds);

// Find any "new" comments under the requested IDs.
$sql    = "SELECT d.*,
               IF(e.`groupid` IS NULL, d.`groupid`, e.`groupid`) AS `masterid`,
               u.`first_name` AS firstname, u.`last_name` AS lastname,
               (SELECT COUNT(*) FROM `cubes` AS c
                WHERE `type`      = 1
                  AND `contentid` = d.`id`) AS `cubecount`
           FROM `discussion` AS d
               LEFT JOIN `discussion` AS e ON e.`id` = d.`parent`
               JOIN `users` AS u ON u.`id` = d.`created_by`
           WHERE $conditions
           ORDER BY e.`childtime` DESC, d.`childtime`";
$db_res = mysql_query($sql);

// Populate an ID => comment-list mapping. The `masterid` value is the ID of
// the top-level comment a row belongs to (its own ID if it's a top-level).
$new = array();
while ($row = mysql_fetch_assoc($db_res)) {
    $cmnt = Comment::fromData($row, $val_log);
    $row['html'] = $cmnt->format($user_id);
    $new[] = $row;
}
$result = array('new' => $new);

// Check the existence of some comment IDs, if applicable.
$id_list = join(',', array_filter(explode(',', @$_GET['check_ids']),
                                  'ctype_digit'));
if (strlen($id_list) > 0) {
    $sql = "SELECT `id` FROM `discussion`
            WHERE `id` IN ($id_list)
            ORDER BY `id` ASC";
    $res = mysql_query($sql);
    $result['remaining_ids'] = array();
    while ($row = mysql_fetch_row($res))
        $result['remaining_ids'][] = intval($row[0]);
}

// Display the results to the user.
die(json_encode($result));
// END $mode == 'pollDiscussions'
}  else if ($mode == "tos-permissions-form") {
	// check full name

	$date_time = time();

	$date_time_formatted = getdate($date_time);
	$date_time_formatted = $date_time_formatted['weekday'] . ", " . $date_time_formatted['month'] . " " . $date_time_formatted['mday'] . ", " . $date_time_formatted['year'];


	if ($_POST['fullname'] != $first_last) {
		echo "invalidName";
	} else if ($_POST['date'] != $date_time_formatted) {
		echo "invalidTodayDate";
	} else {
		$q = "insert into `tos_entries` (`cubocubeID`, `user_id`, `name`, `datetime`, `decision`) values({$_POST['bookid']}, {$user_id}, '{$first_last}', {$date_time}, '{$_POST['userDecision']}')";
		mysql_query($q);
		echo "success";
	}

	//$_POST['bookID']

} else if ($mode == "get-book-contributors") {
	$book_group = intval(stripslashes(strip_tags($_POST['book_group'])));
	$sql_fetch_contributors = "select `first_name`,`last_name` from `users` where `id` in 
		(select distinct `created_by` from `content_piece` where `cubogroup` = '{$book_group}')";
	$result_fetch_contributors = mysql_query($sql_fetch_contributors);
	$return_html = "";
	while ($row_fetch_contributors = mysql_fetch_assoc($result_fetch_contributors)) {
		$return_html .= $row_fetch_contributors['first_name'] . " " . $row_fetch_contributors['last_name']  . "<br />";
	}
	die($return_html);
}

?>
