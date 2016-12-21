$(document).ready(function () {

	var serverPath = "ajax/";
	
	$(".error-box").hide();
	$(".information-box").hide();

	$("#btn-contact").click(function() {

		$("#email-contact-error").hide();
		$("#name-contact-error").hide();
		$("#message-contact-error").hide();

		var fullname = $("#contact-name").val();
		var email = $("#contact-email").val();
		var message = $("#simple-textarea").val();

		if (fullname == "") {
			$("#contact-name").focus();
			$("#email-contact-error").hide();
			$("#name-contact-error").fadeIn();
			$("#message-contact-error").hide();
			return false;
		} 

		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

		if (!re.test(email)) {
			$("#email-contact-error").fadeIn();
			$("#name-contact-error").hide();
			$("#message-contact-error").hide();
      		$("#contact-email").focus();
      		return false;	
		}

		if (message == "") {
			$("#simple-textarea").focus();
			$("#email-contact-error").hide();
			$("#name-contact-error").hide();
			$("#message-contact-error").fadeIn();
			return false;
		}

		$("#email-contact-error").hide();
		$("#name-contact-error").hide();
		$("#message-contact-error").hide();

		var dataString = 'fullname='+ fullname + '&email=' + email + '&message=' + message ;
		
		$.ajax({
			type: "POST",
			url: serverPath+"reg_contactus.php",
			data: dataString,
			success: function(data) {
				$("#contact-sent-info").fadeIn();
      		},
      		error: function(data) {
      		}
     	});


		return false;

	});

	$("#btn-reset-pass").click(function() {

		$("#email-contact-error").hide();
		$("#reset-pass-info").hide();

		var email = $("#contact-email").val();
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

		if (!re.test(email)) {
			$("#email-contact-error").fadeIn();
			$("#name-contact-error").hide();
			$("#message-contact-error").hide();
      		$("#contact-email").focus();
      		return false;	
		}

		var dataString = 'mode=sendforgotpasswordemail&email=' + email;
		
		$.ajax({
			type: "POST",
			url: serverPath+"reg_forgotpassword.php",
			data: dataString,
			success: function(data) {
				if (data == "true") {
					$("#email-contact-error").hide();
					$("#reset-pass-info").fadeIn();
				} else {
					$("#email-contact-error").fadeIn();
					$("#reset-pass-info").hide();
				}
      		},
      		error: function(data) {
      		}
     	});


		return false;


	});


	$("#btn-verify-reset-pass").click(function() {
		$("#reset-pass-info").hide();
		$("#link-contact-error").hide();
		$("#email-contact-error").hide();
		$("#password-contact-error").hide();

		var email = $("#contact-email").val();
		var password = $("#contact-password").val();
		var hash = $("#hash").val();


		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

		if (!re.test(email)) {
			$("#email-contact-error").fadeIn();
			$("#password-contact-error").hide();
      		$("#contact-email").focus();
      		return false;	
		}

		if ((password.length <= 4) || (password.length >= 20)) {
			$("#email-contact-error").hide();
			$("#password-contact-error").fadeIn();
      		$("#contact-password").focus();
      		return false;			
		}


		var dataString = 'mode=verifyforgotpasswordemail&email=' + email + '&pass=' + password + '&hash=' + hash;

		$.ajax({
			type: "POST",
			url: serverPath+"reg_forgotpassword.php",
			data: dataString,
			success: function(data) {
				if (data == "true") {
					$("#reset-pass-info").fadeIn();
					$("#link-contact-error").hide();
					$("#email-contact-error").hide();
					$("#password-contact-error").hide();
				} else {
					$("#link-contact-error").fadeIn();
					$("#reset-pass-info").hide();
					$("#email-contact-error").hide();
					$("#password-contact-error").hide();
				}
      		},
      		error: function(data) {
      		}
     	});

		return false;



	});



});


