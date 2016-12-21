$(document).ready(function() {

	var serverPath = "ajax/";

	$("#btn-submit").click(function () {
		var fullname = $("#tos-firstlast").val();
		var date = $("#tos-date").val();
		var bookID = $("#tos-bookID").val();
		var userDecision = $("#agreedisagree").val();

		$("#name-error").hide();
		$("#date-error").hide();


		var stringData = 'mode=tos-permissions-form&bookid=' + bookID + '&fullname=' + fullname + '&date=' + date + '&userDecision=' + userDecision;

		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard.php",
				data: stringData, 
				success: function(data) {
					if (data == "success") {
						window.location = "dashboard.php?c=" + bookID;
						return false;
					} else if (data == "invalidName") {
						$("#name-error").fadeIn();
						return false;
					} else if (data == "invalidTodayDate") {
						$("#date-error").fadeIn();
						return false;
					}
				},
				error: function(data) {
					alert("An error occurred.");
				}			
		});


		return false;
	});




});