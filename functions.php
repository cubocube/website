<?php

include_once("db.php");
include_once("general_header.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);


function isParent($parent_id, $kid_id) {
	// $parent_id = parent section id
	// $kid_id = kid section id
	$q = "SELECT * FROM `section` WHERE `id` = " . $kid_id;
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);
	if ($row['parent'] == -1) {
		return false;
	} else {
		$q = "SELECT * FROM `section` WHERE `id` = " . $row['parent'];
		$result = mysql_query($q);
		$row = mysql_fetch_assoc($result);
		
		if ($row['id'] == $parent_id) {
			return true;
		} else {
			if ($row['parent'] == -1) {
				return false;
			} else {
				$q = "SELECT * FROM `section` WHERE `id` = " . $row['parent'];
				$result = mysql_query($q);
				$row = mysql_fetch_assoc($result);
				
				if ($row['id'] == $parent_id) {
					return true;
				} else {
					return false;
				}
			}
		}
	}
}

function getFirstLast($uid) {
	$q = "SELECT * FROM `users` WHERE `id` = " . $uid;
	$result = mysql_query($q);
	$row = mysql_fetch_assoc($result);
	return $row['first_name'] . " " . $row['last_name'];
}

function ago($time) {
   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
   $lengths = array("60","60","24","7","4.35","12","10");

   $now = time();

       $difference     = $now - $time;
       $tense         = "ago";

   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
       $difference /= $lengths[$j];
   }

   $difference = round($difference);

   if($difference != 1) {
       $periods[$j].= "s";
   }

   return "$difference $periods[$j] ago ";
}

function cube($userid, $type, $contentid) {
	// a generic function to cube anything
	// type 0: for section / chapters
	// type 1: for posts / comments / discussions
	$q = "INSERT INTO `cubes` (`id`, `type`, `contentid`, `userid`, `datetime`) VALUES (NULL, '".$type."', '".$contentid."', '".$user_id."', '".time()."')";
	$result = mysql_query($q);
}

function frontJoinedBookLinks($userid) {
    $userid = (int) $userid;  // Ensure for injection purposes.
	$q = "SELECT g.`id`, g.`name` FROM `registered_groups` AS r
              JOIN `groups` AS g ON g.`id` = r.`groupId`
		  WHERE r.`userId` = $userid";

 	$result = mysql_query($q);
 	$str = "";

 	while ($row = mysql_fetch_assoc($result)) {
 		$str .= "<li>".
                    "<a href=\"dashboard.php?c=$row[id]\">$row[name]</a>".
 		        "</li>";
 	}

 	return $str;
}


/* Given a user ID and a section ID, confirm the user has been assigned to that
 * section, or some parent of it. Bad section IDs just result in 'false'.
 */
function userInSection($user_id, $sec_id) {
    $user_id = (int) $user_id;
    $sec_id  = (int) $sec_id;
    
    // Fetch the parents of this section, failing if no such section exists.
    $sql = "SELECT x.`parent` AS `parent`,
                   IF (y.`parent` IS NULL, -1, y.`parent`) AS `grandparent`
                FROM `section` AS x
                LEFT JOIN `section` AS y ON y.`id` = x.`parent`
            WHERE x.`id` = $sec_id";
    $res = mysql_query($sql);
    if (!$res or mysql_num_rows($res) == 0)
        return false;
    $sec = mysql_fetch_assoc($res);
    
    // Fetch any assignments that this user already has covering this section.
    $sql = "SELECT * FROM `section_assignments`
            WHERE `user_id`    = $user_id
              AND `section_id` IN ($sec_id, $sec[parent], $sec[grandparent])";
    $res = mysql_query($sql);
    return ($res and mysql_num_rows($res) > 0);
}


/* Given a user ID and a group ID, return the role of the user in that group, as
 * one of ADMIN_ROLE, INSTRUCTOR_ROLE, or STUDENT_ROLE. If they are not in the
 * given group, then NULL is returned instead.
 */
function userRoleInGroup($user_id, $group_id) {
    $user_id  = (int) $user_id;
    $group_id = (int) $group_id;
    
    $sql = "SELECT `role` FROM `registered_groups`
            WHERE `userId`  = $user_id
              AND `groupId` = $group_id";
    $res = mysql_query($sql);
    if ($res and mysql_num_rows($res) > 0) {
        $row = mysql_fetch_assoc($res);
        return $row['role'];
    }

    return null;
}


/* Given a user ID and a group ID, redirect to the dashboard page if the user
 * does not have admin- or instructor-level permissions for this group. In the
 * case that they have clearance, return their role type as ADMIN_ROLE or
 * INSTRUCTOR_ROLE.
 */
function requireNonStudent($user_id, $group_id) {
    $role = userRoleInGroup($user_id, $group_id);
    if ($role != ADMIN_ROLE and $role != INSTRUCTOR_ROLE) {
        header('Location: '.$url.'dashboard.php');
        die();
    }
    return $role;
}

/* Simply rename the MySQL string-escaping function since it has a truly awful
 * name -- yay PHP!
 */
function db_escape($str) {
  return mysql_real_escape_string($str);
}

/**
* Any new messages for user? 
* Returns number of new messages
**/
function any_new_messages($user_id) {
  $user_id_escaped = db_escape($user_id);
  $q = "select 1 from `messages` where `to_user_id` = {$user_id_escaped} and `read` = 'unread'";
  $result = mysql_query($q);
  return mysql_num_rows($result);
}

function formatted_books_user_part_of($user_id) {
  $escaped_user_id = mysql_real_escape_string($user_id);
  $q = "select `id`,`name` 
          from `groups` 
          where `groups`.`id` 
            in 
              (select `groupId` 
                from `registered_groups` 
                where `userId` = {$escaped_user_id})";
  $result = mysql_query($q);
  $num_rows = mysql_num_rows($result);
  if ($num_rows == 0) {
    return "This user has not joined any CuboBooks.";
  } else {
    $str = "Part of ";
    $row_num = 0;
    while ($row = mysql_fetch_assoc($result)) {
      if ($row_num == 0) {
        $str .= '<a href="dashboard.php?c=' . $row['id'] . '">' . strval($row['name']) . '</a>';
      } else if ($row_num + 1 == $num_rows) { // last book
        $str .= ' and ' . '<a href="dashboard.php?c=' . $row['id'] . '">' . strval($row['name']) . '</a>';
      } else {
        $str .= ", " . '<a href="dashboard.php?c=' . $row['id'] . '">' . strval($row['name'])  . '</a>';
      }
      $row_num += 1;
    }
    return $str;
  }
}

?>