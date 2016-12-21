<?php

include("general_header.php");

ob_start();
include('./template/dashboard_main.html');
$js_array = array (
	'jquery',
	'ui/jquery.ui.core.js',
	'ui/jquery.ui.widget.js',
	'ui/jquery.ui.mouse.js',
	'ui/jquery.ui.draggable.js',
	'ui/jquery.ui.position.js',
	'ui/jquery.ui.resizable.js',
	'ui/jquery.ui.dialog.js',
	'ui/jquery.ui.autocomplete.js',
	'dashboard-messages.js',
	'jquery.timeago.js'
	);
	
$str = ob_get_contents();
ob_end_clean();

$mode = @$_GET['mode'];
if ($mode == "") {
	die();
} else if ($mode == "send") {
	$to = db_escape($_GET['to']);
	$sql_get_name = "select `first_name`,`last_name` from `users` where `id` = {$to}";
	$result_get_name = mysql_query($sql_get_name);
	if (mysql_num_rows($result_get_name) < 1) {
		die();
	}
	$row_get_name = mysql_fetch_assoc($result_get_name);

	$register_form = '
	<form action="" method="post" id="register-form">

			<fieldset>
				<h1>Send a new message</h1>
				<input style="display:none;" type="text" id="recipient" value="'.$to.'" />
				<p>
					You are sending a message to '.$row_get_name['first_name'] . ' ' . $row_get_name['last_name'] .'.
				</p>

				<p>
					<label for="message-body">Message</label>
					<textarea id="message-body" class="round full-size-input"> </textarea>
				</p>
			
				
				<input type="submit" class="button round blue text-upper image-right ic-right-arrow" id="btn-submit-message"/>	

				<p>
					<div class="error-box round" id="messages-error">An error occurred..</div>
				</p>
				
                        
			</fieldset>

		</form>';
} else if ($mode == "view") {
	$user_id_db_escape = db_escape($user_id);
	$sql_get_messages = "select * from `messages` where (`to_user_id` = '{$user_id_db_escape}' or `from_user_id` = '{$user_id_db_escape}') and `parent` = -1 order by `whence` desc";
	$result_get_messages = mysql_query($sql_get_messages);
	$messages_array = array();
	while ($row = mysql_fetch_assoc($result_get_messages)) {
		$sql_get_latest_message_for_parent = "select * from `messages` where `parent` = '".$row['messages_id']."' order by `whence` desc";
		$result_get_latest_message_for_parent = mysql_query($sql_get_latest_message_for_parent);
		if (mysql_num_rows($result_get_latest_message_for_parent) == 0) {
			array_push($messages_array, array('name' => get_full_name($row['to_user_id']),
											  'from_name' => get_full_name($row['from_user_id']),
											  'preview' => substr($row['msg'],0,100),
											  'status' => $row['read'],
											  'datetime' => $row['whence'],
											  'message_id' => $row['messages_id'] ));
		} else {
			while ($row_child = mysql_fetch_assoc($result_get_latest_message_for_parent)) {
				// we just care about the latest one
				array_push($messages_array, array('name' => get_full_name($row_child['to_user_id']),
												  'from_name' => get_full_name($row_child['from_user_id']),
												  'preview' => substr($row_child['msg'],0,100), 
												  'status' => $row_child['read'], 
												  'datetime' => $row_child['whence'],
												  'message_id' =>  $row['messages_id'] ));
				break;
			}
		}
	}
	// order array by date time
	usort($messages_array,"cmp");

	$register_form = '
		<div class="content-module" id="up-cb-dash">			
				<div class="content-module-heading cf">
					<h3 class="fl">Messages</h3>			
				</div>
				<div class="content-module-main">
					<table class="table-history" id="table-history-395">
			<thead>
				<tr>
					<th>Private Message Preview</th>
					<th>To</th>
					<th>Status</th>
					<th class="date-time-history">Last Updated</th>
				</tr>
			</thead> 
			<tfoot>
				<tr>
					<td colspan="10" class="table-footer">
						<form action="javascript:void(0);">
							<br>
							<br><br>
							<div class="cf">
								<form>
									<fieldset>
										<label for="compose-new-message">Compose a new message</label>
										<p class="autocomplete">
											<input type="text" class="round default-size-input" id="compose-new-message" />
											<input type="submit" style="display: none;" id="compose-new-message-sub" />
										</p>
									</fieldset>
								</form>
							</div>
						</form>
					</td>
				</tr>
			</tfoot>
			<tbody>';

			foreach ($messages_array as $message) {
				$register_form .=
					'<tr>
						<td><a href="dashboard-messages.php?mode=reply&a='.$message['message_id'].'">'.$message['from_name'].': '.$message['preview'].'</a></td>
						<td>'.$message['name'].'</td>
						<td>'.$message['status'].'</td>
						<td><abbr class="timeago" title="'.$message['datetime'].'">'.$message['datetime'].'</abbr></td>
					</tr>';
			}

	$register_form .=
			'</tbody>
		</table>
				</div> 

		</div> <!-- end content-module -->
	';
} else if ($mode == 'reply') {
	$message_id_to_fetch = $_GET['a']; // parent id
	$sql_get_message = "select * from `messages` where `messages_id` = '".$message_id_to_fetch."'";
	$result_get_message = mysql_query($sql_get_message);
	if (mysql_num_rows($result_get_message) < 1) {
		die();
	}
	$row_get_message = mysql_fetch_assoc($result_get_message);
	$sql_get_children = "select * from `messages` where `parent` = '".$message_id_to_fetch."' order by `whence` asc";
	$result_get_children = mysql_query($sql_get_children);
	$messages_array = array();
	array_push($messages_array, array('name' => get_full_name($row_get_message['from_user_id']), 
									 'message' => $row_get_message['msg'],
									 'datetime' => $row_get_message['whence'],
									 'readstatus' => $row_get_message['read'], 
									 'to_user_id' => $row_get_message['to_user_id'],
									 'reply_id' => $message_id_to_fetch,
									 'messages_id' => $row_get_message['messages_id'] ));
	while ($row = mysql_fetch_assoc($result_get_children)) {
		array_push($messages_array, array('name' => get_full_name($row['from_user_id']),
										 'message' => $row['msg'], 
										 'datetime' => $row['whence'], 
										 'readstatus' => $row['read'],
										 'to_user_id' => $row['to_user_id'],
										 'reply_id' => $message_id_to_fetch,
										 'messages_id' => $row['messages_id'] ));
	}
	mark_as_read($messages_array, $user_id);
	$register_form = '
		<div class="content-module" id="up-cb-dash">			
			<div class="content-module-heading cf">
				<h3 class="fl">Viewing Message</h3>			
			</div>
			<div class="content-module-main">
				<div class="comments-container">
					<div class="inner-comments" id="inner-comments-395">
			            <ul class="nested-comments nostyle" id="inner-comment-item-603">
			              <li>';
	foreach ($messages_array as $message) {
		$register_form .=
			                '<div class="comment">
			                  <p>
			                  	<a href="javascript:void(0);" id="author-name-'.$message['messages_id'].'" class="author">'.$message['name'].'</a>
			                  </p>
			                  <h5><i><abbr class="timeago" title="'.$message['datetime'].'">'.$message['datetime'].'</abbr></i></h5>
			                  <p class="comment-paragraph">'.$message['message'].'</p>
			                  <div class="cf">
			                	<p class="fl"><a href="javascript:void(0);">
			                		'.$message['readstatus'].'
			               		</a></p>
			               	  </div>
			                </div>';
	}

	$register_form .= '
			                <ul class="nostyle">
			                    <li class="inner">
			                    <div class="comment">
			                      <form action="javascript:void(0);" class="form-replytomessage" id="form-replytomessage-'.$message['reply_id'].'">
			                        <fieldset>
			                          <p>
			                            <input type="text" class="full-size-input2 replytomessage" id="replytomessage-'.$message['reply_id'].'" placeholder="Reply...">
			                          </p>
			                        </fieldset>
			                      </form>
			                    </div>
			                  </li>      
			                </ul>
			              </li>
			            </ul></div> <!-- END INNER COMMENTS -->
					</div>
				</div>

	';
} else {
	die();
}



$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url . 'template/', $str);
$str = str_replace('{REGISTER-FORM-HERE}', $register_form, $str);
$str = top_bar($str);
$str = str_replace('{CUBOCUBE-BOOK-ID}', $_SESSION['cubogroup'], $str);

echo $str; 

/**********************************************/
/** functions *******/
function mark_as_read($messages_array, $user_id) {
	foreach ($messages_array as $message) {
		if ($message['to_user_id'] == $user_id && $message['readstatus'] != 'read') {
			$sql_update = "update `messages` set `read` = 'read' where `messages_id` = '".$message['messages_id']."'";
			mysql_query($sql_update);
		}
	}
}

function get_full_name($user_id) {
	$sql_get_full_name = "select `first_name`,`last_name` from `users` where `id` = '".$user_id."'";
	$result_get_full_name = mysql_query($sql_get_full_name);
	$row_get_full_name = mysql_fetch_assoc($result_get_full_name);
	return $row_get_full_name['first_name'] . " " . $row_get_full_name['last_name'];
}

function cmp($a, $b) {
	if (strtotime($a['datetime']) == strtotime($b['datetime'])) {
		return 0;
	}
	return (strtotime($a['datetime']) < strtotime($b['datetime'])) ? 1 : -1;
}

/** end functions **/

?>

