$(document).ready(function () {
	var serverPath = "ajax/";
	$("#messages-error").hide();
	$(".timeago").timeago();

	$.ajax({
		type: "POST",
		url: serverPath+"reg_dashboard-messages.php",
		dataType: "json",
		data: "mode=getUsersToMessage",
		success: function (resultData) {
			var full_users_array = resultData.full_users_array;
			var username_array   = resultData.username_array;
			$("#compose-new-message").autocomplete({
				source: full_users_array,
				minLength: 2,
				select: function (event, ui) {
					var selectedObj = ui.item;
					var username = extract_username_from_string(selectedObj.value);
					var user_id = get_user_id(username_array, username); // int
					window.location = "dashboard-messages.php?mode=send&to="+user_id.toString();
				}
			});
		},
		error: function (resultData) {
			alert("An error occurred.");
		}
	});

	function get_user_id (username_array, username) {
		var username_array_itr = 0;
		while (username_array_itr < username_array.length) {
			if (username_array[username_array_itr].hasOwnProperty(username)) {
				return username_array[username_array_itr][username];
			}
			username_array_itr += 1;
		}
		return -1;
	}

	// extracts username from string input
	// first_name last_name (username)
	function extract_username_from_string(string_input) {
		return string_input.substring(string_input.indexOf("(")+1,string_input.indexOf(")"));
	}

	$(".form-replytomessage").submit(function() {
		var event_target_id = event.target.id;
		var actual_id = event_target_id.split("-")[2];
		var message_to_send = $("#replytomessage-"+actual_id).val();
		$("#replytomessage-"+actual_id).val("");
		$.ajax({
			type: "POST",
			url: serverPath+"reg_dashboard-messages.php",
			data: "mode=sendReply&messages_id="+actual_id+"&message_text="+message_to_send,
			success: function (data) {
				window.location = "dashboard-messages.php?mode=reply&a="+actual_id;
			}, 
			error: function (data) {
				alert("An error occurred.");
			}
		});
		return false;
	});

	$("#btn-submit-message").click(function () {
		var to = $("#recipient").val();
		var msg = $("#message-body").val();
		if (msg.trim() != "") {
			$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard-messages.php",
				data: "mode=sendMessage&to="+to+"&message="+msg, 
				success: function(data) {
					window.location = "dashboard-messages.php?mode=view";
				},
				error: function(data) {
					$("#messages-error").show();
				}			
			});
		}
		return false;
	});
});