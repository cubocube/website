<?php

include_once("../db.php");
include_once("../general_header.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

if (($user_role != 0) && ($user_role != 1)) {
	header('Location: dashboard.php');
}

$sid = 152;
// section id that we want to get the history of 

$q = "SELECT * FROM `content_piece` WHERE `section_ch_id` = " . $sid . " ORDER BY `time_modified` DESC";
$result = mysql_query($q);

while ($row = mysql_fetch_assoc($result)) {
	//echo $row['content'];
	//echo '<input type="button" onclick="myFunction( \'' . $row['content'] . '\')" value="Show alert box" />';
}


$table .= '



<table>
							<thead>
								<tr>
									<th><input type="checkbox" /></th>
									<th>Name</th>
									<th>Username</th>
									<th>Date/Time</th>
									<th>Content</th>
									<th>User Role</th>
									<th>Instructor Approval</th>
								</tr>
							</thead> 
							<tfoot>
								<tr>
									<td colspan="5" class="table-footer">
									
										<label for="table-footer-actions">With selected</label>
										<select name="table-footer-actions" id="table-footer-actions">
											<option value="op1">Delete</option>
											<option value="op2">Archive</option>
											<option value="op3">Export</option>
										</select>
										<a href="#" class="round button blue text-upper">Apply to selected</a>
									</td>
								</tr>
							</tfoot>
							<tbody>
								<tr>
									<td><input type="checkbox" /></td>
									<td>Puya SK</td>
									<td>puyask</td>
									<td><a href="#">pseidkarbasi@gmail.com</a></td>
									<td>
										<a href="#" class="table-actions-button ic-table-edit"</a>
										<a href="#" class="table-actions-button ic-table-delete"</a>
									</td>
								</tr>
								<tr>
									<td><input type="checkbox" /></td>
									<td>Puya SK</td>
									<td>puyask</td>
									<td><a href="#">pseidkarbasi@gmail.com</a></td>
									<td>
										<a href="#" class="table-actions-button ic-table-edit"</a>
										<a href="#" class="table-actions-button ic-table-delete"</a>
									</td>
								</tr>
								<tr>
									<td><input type="checkbox" /></td>
									<td>Puya SK</td>
									<td>puyask</td>
									<td><a href="#">pseidkarbasi@gmail.com</a></td>
									<td>
										<a href="#" class="table-actions-button ic-table-edit"</a>
										<a href="#" class="table-actions-button ic-table-delete"</a>
									</td>
								</tr>
							</tbody>
						</table>

';

echo $table;


?>
