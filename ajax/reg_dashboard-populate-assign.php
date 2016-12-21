<?php

include_once("../db.php");
include_once("../general_header.php");
include_once("../functions.php");

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


/* Get the formatted HTML for the client, for the given user, showing their
 * info and what sections they are currently assigned to.
 */
function assignmentBlock($user) {
    global $cubogroup;
    
    // Get all the user's assignments' names in one go.
    $sql = "SELECT s.* FROM `section_assignments` AS a
                JOIN `section` AS s ON s.`id` = a.`section_id`
            WHERE a.`user_id`   = $user[id]
              AND s.`cubogroup` = $cubogroup";
    $res = mysql_query($sql);
    
    // Format the sections' names into a single string.
    $assigned_to = '';
    while ($row = mysql_fetch_assoc($res)) {
        $assigned_to .=
            '<span class="unassignThis" rel="'.$row['id'].'">'.
                $row['description'].
            '</span>';
    }
	if ($assigned_to === '')
		$assigned_to = 'Nothing yet';
	
    // Get the "nice" representation of the user's role.
    // This really should be an ENUM in the DB! Then we can also avoid this.
	$role = intval($user['role']);
	if ($role === 0)
		$role = 'admin';
	else if ($role === 1)
		$role = 'instructor';
	else if ($role === 2)
		$role = 'student';
    
    // Create the "selected student view" HTML to send back to the client.
	return '
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
                    <td id="selected-firstname">'.$user['first_name'].'</td>
                    <td id="selected-lastname">'.$user['last_name'].'</td>
                    <td id="selected-username">'.$user['username'].'</td>
                    <td id="selected-studentnum">'.$user['studentid'].'</td>
                    <td id="selected-email">'.$user['email'].'</td>
                    <td id="selected-user-role">'.$role.'</td>
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
                    <td id="selected-username">'.$assigned_to.'</td>
                </tr>
                
            </tbody>
        </table>';
}



if (isset($_GET['geti'])) {
    
    /* The client wants a list of values present in the database for some
     * column in the Users table.
     */
    
    $which = $_GET['geti'];
    
    // Determine which column we'll select.
    if ($which == 'uname')
        $column = 'username';
    else if ($which == 'firstname')
        $column = 'first_name';
    else if ($which == 'lastname')
        $column = 'last_name';
    else
        fail('Invalid preview-by type');
    
    // Select all usernames attached to this book.
    $sql = "SELECT DISTINCT `$column` FROM `users` AS u
                JOIN registered_groups AS r ON r.`userId` = u.`id`
            WHERE r.`groupId` = $cubogroup";
	$res = mysql_query($sql);
    
    // Extract all results and send them back JSON-encoded.
    $values = array();
    while ($row = mysql_fetch_row($res))
        $values[] = $row[0];
    die(json_encode($values));
}


$mode = @$_POST['mode'];

if ($mode == 'preview') {
    
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
    else
        fail('Invalid preview-by type');
    
    // Select the user, failing if they do not exist.
    $sql  = "SELECT * FROM `users`
             WHERE `$column` = '$value'
             LIMIT 1";
	$res  = mysql_query($sql);
    if (!$res or mysql_num_rows($res) == 0)
        fail('Invalid user');
	$user = mysql_fetch_assoc($res);
    
    // Send back this user's section HTML block.
    echo assignmentBlock($user);

} else if ($mode == 'assign') {
    
    /* Assign some user to a section, given by their username and the ID of the
     * section. If they are already in the section, or in a parent of it, this
     * is effectively a no-op.
     */

    // Extract given inputs.
	$name = mysql_real_escape_string(@$_POST['username']);
	$to   = intval(@$_POST['to']);
    
    // Fetch the requested user, failing if they don't exist.
    $sql  = "SELECT * FROM `users`  
             WHERE `username` = '$name'";
	$res  = mysql_query($sql);
    if (mysql_num_rows($res) == 0)
        fail('Invalid user');
    $user = mysql_fetch_assoc($res);
    
    if (!userInSection($user_id, $to)) {
        // Add this new assignment into the database.
        $sql = "INSERT INTO `section_assignments` (`user_id`, `section_id`)
                VALUES ($user[id], $to)";
        mysql_query($sql);
        
        // Remove any assignments for this user to children of this section.
        $sql = "DELETE FROM `section_assignments`
                WHERE EXISTS
                    (SELECT * FROM `section` AS x
                         LEFT JOIN `section` AS y ON y.`id` = x.`parent`
                     WHERE x.`id` = `section_id`
                       AND (x.`parent` = $to OR y.`parent` = $to))";
        mysql_query($sql);
    }
    
    // Send back this user's section HTML block.
    echo assignmentBlock($user);
	
} else if ($mode == 'remove') {
    
    /* Remove some user from a section, given by their username and the ID of
     * the section. If they are not in the section, this is effectively a
     * no-op.
     */

    // Extract given inputs.
	$name   = mysql_real_escape_string(@$_POST['username']);
	$remove = intval(@$_POST['from']);
    
    // Fetch the requested user, failing if they don't exist.
    $sql  = "SELECT * FROM `users`  
             WHERE `username` = '$name'";
	$res  = mysql_query($sql);
    if (mysql_num_rows($res) == 0)
        fail('Invalid user');
    $user = mysql_fetch_assoc($res);
    
    // Remove any (user, section) assignment that exists.
    $sql = "DELETE FROM `section_assignments`
            WHERE `user_id`    = $user[id]
              AND `section_id` = $remove";
    mysql_query($sql);
    
    // Send back this user's section HTML block.
    echo assignmentBlock($user);
    
}



mysql_close($con);

?>