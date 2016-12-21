<?php

include_once("../db.php");
include_once("../general_header.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);
    
/* If something goes wrong or is missing, halt with a 500 code and message.
 */
function fail($reason='') {
    header('HTTP/1.1 500 Internal Server Error');
    die($reason);
}

if (! check_logged()) {
  fail();
}

$mode = @$_POST['mode'];
if ($mode == 'sendMessage') {
  $to = stripslashes(strip_tags($_POST['to']));
  $from = stripslashes(strip_tags($user_id));
  $message = db_escape(stripslashes(strip_tags($_POST['message'])));
  $sql_send_message = "insert into `messages` (`read`,`from_user_id`, `to_user_id`,`msg`) 
  	values ('unread',{$user_id},{$to},'{$message}')";
  echo $sql_send_message;
  mysql_query($sql_send_message);
  echo "DFSDF";
} else if ($mode == 'sendReply') {
  // i.e. the parent id
  $messages_id = stripslashes(strip_tags($_POST['messages_id']));
  $message_text = stripslashes(strip_tags($_POST['message_text']));
  $sql_get_to_user_id = "select `from_user_id` 
                          from `messages` 
                          where `parent` = '{$messages_id}' or `messages_id` = '{$messages_id}' 
                          order by `whence` desc limit 1";
  $result_get_to_user_id = mysql_query($sql_get_to_user_id);
  $row_get_to_user_id = mysql_fetch_assoc($result_get_to_user_id);
  $to_user_id = $row_get_to_user_id['from_user_id'];
  $read_status = $to_user_id == $user_id ? 'read' : 'unread';
  $sql_insert_message = "insert into `messages` (`parent`, `read`, `from_user_id`, `to_user_id`, `msg`) 
    values ('{$messages_id}', '{$read_status}', '{$user_id}','{$to_user_id}','{$message_text}')";
  mysql_query($sql_insert_message);
} else if ($mode == 'getUsersToMessage') {
  $sql_get_users = "select `id`,`username`,`first_name`, `last_name` from `users` where `id` != '".$user_id."'";
  $result_get_users = mysql_query($sql_get_users);
  $full_users_array = array();
  $username_array = array();
  while ($row = mysql_fetch_assoc($result_get_users)) {
    array_push($full_users_array, $row['first_name'] . " " . $row['last_name'] . " (" . $row['username'] . ")");
    array_push($username_array, array($row['username'] => $row['id']));
  }
  die(json_encode(array('full_users_array' => $full_users_array, 'username_array' => $username_array)));
}

?>