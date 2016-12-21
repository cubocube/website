$(document).ready(function (){

	var serverPath = "ajax/";
	var dashboardcbAjaxPath = serverPath + "reg_dashboard-cb.php";

	$("#drop-cb-dash").hide();
	$("#new-cb-dash").hide();

	$("#side-dash-mycb").click(function() {
		$("#drop-cb-dash").hide();
		$("#new-cb-dash").hide();
		$("#up-cb-dash").show();
	});

	$("#side-dash-findcb").click(function() {
		$("#up-cb-dash").hide();
		$("#new-cb-dash").hide();
		$("#drop-cb-dash").show();
	});

	$("#side-dash-newcb").click(function() {
		$("#up-cb-dash").hide();
		$("#drop-cb-dash").hide();
		$("#new-cb-dash").show();
	});

	$("#btn-new-cubobook-submit").click(function() {
		$(".new-cubobook-error").hide();
		var cubobook_name = $("#simple-input-new-cubobook-name").val();
		var cubobook_description = $("#simple-input-new-cubobook-description").val();

		$.post(dashboardcbAjaxPath,
			{ "mode" : "new_cb",
			  "cubobook_name" : cubobook_name,
			  "cubobook_description" : cubobook_description},
			function(response) {
				if (response.status === "invalid_cubobook_name") {
					$("#new-cubobook-name-error").fadeIn();
				} else if (response.status === "invalid_cubobook_description") {
					$("#new-cubobook-description-error").fadeIn();
				} else if (response.status === "duplicate_cubobook_name") {
					$("#new-cubobook-duplicate-name-error").fadeIn();
				} else if (response.status === "success") {
					window.location = "dashboard.php?c=" + response.c;
				}
			},
			"json"
		);
		console.log("clicked");
		return false;
	});

});