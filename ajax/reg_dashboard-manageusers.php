<?php

include_once("../db.php");
include_once("../general_header.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

// The book that this 'transaction' pertains to.
$cubogroup = intval(@$_SESSION['cubogroup']);

// Confirm this user has admin- or instructor-level privileges.
$user_role = requireNonStudent($user_id, $cubogroup);
    
/* If something goes wrong or is missing, halt with a 500 code and message.
 */
function fail($reason='') {
    header('HTTP/1.1 500 Internal Server Error');
    die($reason);
}

$mode = @$_POST['mode'];

/* Get the formatted HTML for the client, for the given user's info., showing their
 */
if ($mode === 'preview') {
    
    /* Fetch the current assignments of some user, who is determined by a
     * combination of a value, and an attribute to check that value as.
     */
    
	$which = @$_POST['have'];
	$value = mysql_real_escape_string(@$_POST['value']);
    
    // Determine which column we'll check the value of.
    if ($which == 'uname')
        $column = 'username';
    else if ($which == 'firstname')
        $column = 'first_name';
    else if ($which == 'lastname')
        $column = 'last_name';

    $sql  = "SELECT u.*, r.`role` FROM `users` AS u
                 JOIN `registered_groups` AS r ON r.`userId` = u.`id`
             WHERE u.`$column` = '$value'
               AND r.`groupId` = $cubogroup";
    $res  = mysql_query($sql);
    if (!$res or mysql_num_rows($res) == 0)
        fail('No user found!');
    $user = mysql_fetch_assoc($res);

    // Create the "selected student view" HTML to send back to the client.
	echo '
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
      <div id="select_what_edit">
        
        <label for="table-footer-actions"></label>
        <select name="table-footer-actions" id="table-footer-manageu">
          <option value="firstname">First Name</option>
          <option value="lastname">Last Name</option>
          <option value="studentnum">Student #</option>
          <option value="email">Email</option>
          <option value="role">Role</option>
        </select>
        
        <a href="#" class="round button blue text-upper" id="dashboard-manage-edit">Edit</a>
        
      </div>
      <form action="#" id="edit-dashboard-manageusers">
        <fieldset>
          <p id="input-manageusers-normal">
            <label for="input-dashboard-manageusers">Edit Selected Field</label>
            <input type="text" class="round" id="input-dashboard-manageusers" />
          </p>
          
          <p class="form-error" id="input-manageusers-error">
            <label for="input-dashboard-manageusers-error">Edit Selected Field</label>
            <input type="text" class="round error-input" id="input-dashboard-manageusers-error" />
            <em>There was an error.</em>
          </p>
        </fieldset>
        
        <input type="submit" value="Submit"
               class="button round blue text-upper image-right ic-right-arrow"
               id="submit-dashboard-manageusers" />
      </form>

    </td>
  </tr>
</tfoot>
            <tbody>
                <tr>
                    <td id="selected-firstname">'.$user['first_name'].'</td>
                    <td id="selected-lastname">'.$user['last_name'].'</td>
                    <td id="selected-username">'.$user['username'].'</td>
                    <td id="selected-studentnum">'.$user['studentid'].'</td>
                    <td id="selected-email">'.$user['email'].'</td>
                    <td id="selected-user-role">'.$user['role'].'</td>
                </tr>
            </tbody>
        </table>';
}

else if ($mode === 'update') {
	//'changed=' + change_what + '&value=' + input_val;
	// dataString = 'mode=update&' + dataString + '&whichtypesearch=' + whichtypesearch + '&user_selected=' + user_selected;
	$change_what = db_escape($_POST['changed']);
	$value = db_escape($_POST['value']);
	$whichtypesearch = db_escape($_POST['whichtypesearch']);
	$user_selected = db_escape($_POST['user_selected']);
	
    if ($whichtypesearch == 'uname') {
		if ($change_what == 'firstname') {
			$q = "UPDATE `users` SET  `first_name` =  '" . $value . "' WHERE  `username` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'lastname') {
			$q = "UPDATE `users` SET  `last_name` =  '" . $value . "' WHERE  `username` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'studentnum') {
			$q = "UPDATE `users` SET  `studentid` =  '" . $value . "' WHERE  `username` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'email') {
			$q = "UPDATE `users` SET  `email` =  '" . $value . "' WHERE  `username` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'role') {
			$q = "UPDATE `users` SET  `role` =  '" . $value . "' WHERE  `username` ='" . $user_selected . "'";
			$result = mysql_query($q);
		}
	} else if ($whichtypesearch == 'firstname') {
		
		if ($change_what == 'firstname') {
			$q = "UPDATE `users` SET  `first_name` =  '" . $value . "' WHERE  `first_name` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'lastname') {
			$q = "UPDATE `users` SET  `last_name` =  '" . $value . "' WHERE  `first_name` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'studentnum') {
			$q = "UPDATE `users` SET  `studentid` =  '" . $value . "' WHERE  `first_name` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'email') {
			$q = "UPDATE `users` SET  `email` =  '" . $value . "' WHERE  `first_name` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'role') {
			$q = "UPDATE `users` SET  `role` =  '" . $value . "' WHERE  `first_name` ='" . $user_selected . "'";
			$result = mysql_query($q);
		}
	} else if ($whichtypesearch == 'lastname') {
		if ($change_what == 'firstname') {
			$q = "UPDATE `users` SET  `first_name` =  '" . $value . "' WHERE  `last_name` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'lastname') {
			$q = "UPDATE `users` SET  `last_name` =  '" . $value . "' WHERE  `last_name` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'studentnum') {
			$q = "UPDATE `users` SET  `studentid` =  '" . $value . "' WHERE  `last_name` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'email') {
			$q = "UPDATE `users` SET  `email` =  '" . $value . "' WHERE  `last_name` ='" . $user_selected . "'";
			$result = mysql_query($q);
		} else if ($change_what == 'role') {
			$q = "UPDATE `users` SET  `role` =  '" . $value . "' WHERE  `last_name` ='" . $user_selected . "'";
			$result = mysql_query($q);
		}
	}
	
		
}

?>