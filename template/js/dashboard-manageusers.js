$(document).ready(function() {

	var serverPath = "ajax/";

	var user_selected_flag = false;
	var user_selected = "";
	var whichtypesearch = "";
	
	var change_what = ""; // enumeration: firstname, lastname, studentnum, email
	var input_val = ""; // what was entered in the input box
	var email_flag = false; // when email is invalid set flag to true
	var studentnum_flag = false;
	var role_flag = false;
	
	$( "#tags-simple-input-manage" ).focus();
	
	$("#drop-preview-box").hide();
	
	
	$("input#tags-simple-input-manage").focus();
	var whichtypesearch = $('#select-search-by-manage').val();
	var stringData ='geti=uname';
	
	$('#select-search-by-manage').change(function() {
		whichtypesearch = $('#select-search-by-manage').val();
		if (whichtypesearch == 'uname') {
			stringData ='geti=uname';
			getResultsAssign();	
		} else if (whichtypesearch == 'firstname') {
			stringData ='geti=firstname';
			getResultsAssign();	
		} else if (whichtypesearch == 'lastname') {
			stringData ='geti=lastname';
			getResultsAssign();	
		}
		$("input#tags-simple-input-manage").val("");
		$("input#tags-simple-input-manage").focus();
	});
    
    $('#tags-form-manage').submit(function() {
        whichtypesearch = $('#select-search-by-manage option:selected').val();
        submitManageUser($('#tags-simple-input-manage').val());
        $('#tags-simple-input-manage').autocomplete('close');
        $('#tags-simple-input-manage')[0].blur();
        return false;
    });
	
	getResultsAssign();	
	
	function getResultsAssign() {
			$.ajax({
				type: "GET",
				url: serverPath+"reg_dashboard-populate-assign.php",
				dataType: "json",
				data: stringData, 
				success: function(data) {
						$( "#tags-simple-input-manage" ).autocomplete({
						source: data,
						minLength: 0,
						//delay: 500,
						select: function(event,ui) {
							var selectedObj = ui.item;
							submitManageUser(selectedObj.value); 
						}
					});
				},
				error: function(data) {
					alert("An error occurred in getResultsAssign.");
				}			
			});
	}
	
	function submitManageUser(data) {
		//alert(whichtypesearch);
		var stringData ='mode=preview&have='+whichtypesearch+'&value='+data;
		user_selected = data;
		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard-manageusers.php",
				data: stringData, 
				success: function(data) {
					var obj = $("#manageuserscontent").html(data);
					
					obj.find("#input-manageusers-normal").hide();
					obj.find("#input-manageusers-error").hide();
					obj.find("#submit-dashboard-manageusers").hide();
					obj.find("#dashboard-manage-edit").click(function() {
						$("#input-manageusers-normal").show();
						$("#input-manageusers-error").hide();
						$("#select_what_edit").hide();
						$("#submit-dashboard-manageusers").show();
						$("#input-dashboard-manageusers-error").val(""); 
						$("#input-dashboard-manageusers").val("");
						$("#input-dashboard-manageusers").focus();
						change_what = $("#table-footer-manageu").val();
						
						//$("#input-dashboard-manageusers").val();
						
						if (change_what == "firstname") {
							var vali = $("#td-firstname-manage").text();
							$("#input-dashboard-manageusers").val(vali);
						} else if (change_what == "lastname") {
							var vali = $("#td-lastname-manage").text();
							$("#input-dashboard-manageusers").val(vali);
						} else if (change_what == "email") {
							var vali = $("#td-email-manage").text();
							$("#input-dashboard-manageusers").val(vali);
						} else if (change_what == "studentnum") {
							var vali = $("#td-studentnum-manage").text();
							$("#input-dashboard-manageusers").val(vali);
						} else if (change_what == "role") {
							var vali = $("#td-user-role-manage").text();
							$("#input-dashboard-manageusers").val(vali);
						}
						
						return false;
						
						});
					obj.find("#submit-dashboard-manageusers").click(function() {
						
						// let's get what type of data the user is entering
						// in other words what option the user has selected
						// from the dropdown menu
						
					
						
						//$("#submit-dashboard-manageusers").live("click", function() {
														
							if (change_what == "firstname") {
								//alert("first");
								input_val = $("#input-dashboard-manageusers").val();
								$("#td-firstname-manage").text(input_val);
								//alert(input_val);
								$("#input-manageusers-normal").hide();
								$("#submit-dashboard-manageusers").hide();
								$("#select_what_edit").show();
								
								var stringData = 'changed=' + change_what + '&value=' + input_val;
								saveAjax(stringData);
								
								return false;
							} else if (change_what == "lastname") {
								//alert("last");
								input_val = $("#input-dashboard-manageusers").val();
								$("#td-lastname-manage").text(input_val);
								//alert(input_val);
								$("#input-manageusers-normal").hide();
								$("#submit-dashboard-manageusers").hide();
								$("#select_what_edit").show();
								
								var stringData = 'changed=' + change_what + '&value=' + input_val;
								saveAjax(stringData);
								
								return false;
							} else if (change_what == "studentnum") {
								//alert("stdnum");
								
								if (!studentnum_flag) {
									input_val = $("#input-dashboard-manageusers").val();
								} else {
									input_val = $("#input-dashboard-manageusers-error").val();
								}
								var re = /^\d+$/;
								//alert(input_val);
								
								if (!re.test(input_val)) {
									//alert("HI");
									$("#input-manageusers-error").show();
									$("#input-dashboard-manageusers-error").val(input_val);
									$("#input-dashboard-manageusers-error").focus();
									$("#input-manageusers-normal").hide();
									studentnum_flag = true;
									//alert("test failed");
									return false;
								} else {
									//alert("this");
									$("#input-manageusers-error").hide();
									$("#td-studentnum-manage").text(input_val);
									$("#submit-dashboard-manageusers").hide();
									$("#input-manageusers-normal").hide();
									$("#select_what_edit").show();
									studentnum_flag = false;
									
									var stringData = 'changed=' + change_what + '&value=' + input_val;
									saveAjax(stringData);
									
									return false;
								
								}
							} else if (change_what == "email") {
								//alert("ema");
								input_val = $("#input-dashboard-manageusers").val();
								
								if (!email_flag) {
									input_val = $("#input-dashboard-manageusers").val();
								} else {
									input_val = $("#input-dashboard-manageusers-error").val();
								}
								
								var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
								
								if (!re.test(input_val)) {
									$("#input-manageusers-error").show();
									$("#input-dashboard-manageusers-error").val(input_val);
									$("#input-dashboard-manageusers-error").focus();
									$("#input-manageusers-normal").hide();									
									email_flag = true;
									return false;								
								} else {
									$("#input-manageusers-error").hide();
									$("#td-email-manage").text(input_val);
									$("#submit-dashboard-manageusers").hide();
									$("#input-manageusers-normal").hide();
									$("#select_what_edit").show();
									email_flag = false;
									
									var stringData = 'changed=' + change_what + '&value=' + input_val;
									saveAjax(stringData);
									
									return false;								
								}
								
							} else if (change_what == "role") {
								//alert("role");
								input_val = $("#input-dashboard-manageusers").val();
								
								if (!role_flag) {
									input_val = $("#input-dashboard-manageusers").val();
								} else {
									input_val = $("#input-dashboard-manageusers-error").val();
								}
								
								if ((input_val != "admin") && (input_val != "instructor") && (input_val != "student")) {
									$("#input-manageusers-error").show();
									$("#input-dashboard-manageusers-error").val(input_val);
									$("#input-dashboard-manageusers-error").focus();
									$("#input-manageusers-normal").hide();									
									role_flag = true;
									return false;									
								} else {
									$("#input-manageusers-error").hide();
									$("#td-user-role-manage").text(input_val);
									$("#submit-dashboard-manageusers").hide();
									$("#input-manageusers-normal").hide();
									$("#select_what_edit").show();
									role_flag = false;
									
									var stringData = 'changed=' + change_what + '&value=' + input_val;
									saveAjax(stringData);
									
									return false;
								}
								
							}
							
							
							
						});
					
					
					
					user_selected_flag = true;
					$("#drop-preview-box").slideDown();
				},
				error: function(data) {
					alert("An error occurred in submitAssignUser.");
				}	
				
			});
	}
	
	function saveAjax(dataString) {
	//alert(dataString); 
	//alert(user_selected);
	//alert(whichtypesearch);
	dataString = 'mode=update&' + dataString + '&whichtypesearch=' + whichtypesearch + '&user_selected=' + user_selected;
	
	$.ajax({
		type: "POST",
		url: serverPath+"reg_dashboard-manageusers.php",
		data: dataString,
		success: function(data) {
			//$("#registration-response").html(data).fadeIn();
			//alert("change was successful");
			//alert(data);
		},
		error: function(data) {
			//alert("An error occurred.");
		}
	});				
				
	}		
	

	

	

 });
