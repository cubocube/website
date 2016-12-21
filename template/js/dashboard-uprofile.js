$(document).ready(function (){

	var serverPath = "ajax/";

	$("#edit-dashboard-uprofile").hide(); // hide the input originally
	var change_what = ""; // enumeration: firstname, lastname, studentnum, email
	var input_val = ""; // what was entered in the input box
	var email_flag = false; // when email is invalid set flag to true
	var studentnum_flag = false;

	
	$("#dashboard-uprofile-edit").click(function() {
		$("#edit-dashboard-uprofile").show();
		$("#input-uprofile-error").hide();
		$("#input-uprofile-normal").show();
		$("#select_what_edit").hide();
		change_what = $("#table-footer-uprofile").find("option:selected").val();
		//alert(change_what);
		// need to populate input with current value
		input_val = $("#td-" + change_what).text();
		//alert(input_val);
		$("#input-dashboard-uprofile").val(input_val);
	});
	
	$("#submit-dashboard-uprofile").click(function() {
	
		if (change_what == "email") {
			if (email_flag) {
				var email = $("#input-dashboard-uprofile-error").val();
			} else {
				var email = $("#input-dashboard-uprofile").val();
			}
			input_val = email;
			var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

			if (!re.test(email)) {
				$("#input-dashboard-uprofile-error").val(email); 
				$("#input-uprofile-error").show();
				$("#input-uprofile-normal").hide();
				email_flag = true;	
				return false;
			} else {
				email_flag = false;
			}
			
			//alert("email is being changed");
			//alert(input_val);
		} else if (change_what == "studentnum") {
			if (studentnum_flag) {
				var studentnum = $("#input-dashboard-uprofile-error").val();
			} else {
				var studentnum = $("#input-dashboard-uprofile").val();
			}
			input_val = studentnum;
			var re = /^\d+$/;
			
			if (!re.test(studentnum)) {
				$("#input-dashboard-uprofile-error").val(studentnum); 
				$("#input-uprofile-error").show();
				$("#input-uprofile-normal").hide();
				studentnum_flag = true;	
				return false;
			} else {
				studentnum_flag = false;
			}
		} else {
			//alert("student number is being changed");
			input_val = $("#input-dashboard-uprofile").val(); 
		}
		
		$("#edit-dashboard-uprofile").hide(); // hide the input
		$("#edit-dashboard-uprofile-error").hide(); // hide possible error input
		$("#select_what_edit").show(); // show the dropdown again
		$("#td-" + change_what).text(input_val); // change the values in the table 
		
		// next we need to save the changed info to the database
		
		var dataString = 'changed=' + change_what + '&value=' + input_val;
		//alert(dataString); 

		$.ajax({
			type: "POST",
			url: serverPath+"reg_dashboard-uprofile.php",
			data: dataString,
			success: function(data) {
				//$("#registration-response").html(data).fadeIn();
				//alert("change was successful");
				//alert(data);
      		},
      		error: function(data) {
				alert("An error occurred.");
      		}
     	});
		
		return false;
	});
		
});