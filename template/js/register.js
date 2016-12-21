$(document).ready(function (){

	var serverPath = "ajax/";

	$(".error-box").hide(); 
	$(".information-box").hide(); 
	$("#registration-response").hide(); 
	$("#change-password-response").hide(); 


	$("input#register-username").bind("change paste input", function() {
		var username = $("input#register-username").val();
		$("input#register-username").val(username.toLowerCase().replace(" ", ""));
	});

	
	$("#btn-register").click(function() {
		$(".error-box").hide();
		$("#registration-response").hide(); 
		var username = $("input#register-username").val();
		//alert(username.length);
		if ((username.length <= 4) || (username.length >= 20)) {
			$("#username-error").fadeIn();
      		$("input#register-username").focus();
      		return false;
    	}

    	var confirmPassword = $("input#register-confirm-password").val();
		var password = $("input#register-password").val();

		if (confirmPassword != password) {
			$("#password-confirm-error").fadeIn();
      		$("input#register-password").focus();
      		return false;	
		}


		if ((password.length <= 4) || (password.length >= 20)) {
			$("#password-error").fadeIn();
      		$("input#register-password").focus();
      		return false;			
		} 

		var email = $("input#register-email").val(); 
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

		if (!re.test(email)) {
			$("#email-error").fadeIn();
      		$("input#register-email").focus();
      		return false;	
		}
		
		var dataString = 'username='+ username + '&email=' + email + '&password=' + password + '&beta=falseSalt';
		
		$.ajax({
			type: "POST",
			url: serverPath+"reg.php",
			data: dataString,
			success: function(data) {
				$("#registration-response").html(data).fadeIn();
      		},
      		error: function(data) {
				//alert("An error occurred.");
      		}
     	});
   		return false;
	});

	$("#btn-beta-register").click(function() {
		$(".error-box").hide();
		$("#registration-response").hide(); 
		var username = $("input#register-username").val();
		//alert(username.length);
		if ((username.length <= 4) || (username.length >= 20)) {
			$("#username-error").fadeIn();
      		$("input#register-username").focus();
      		return false;
    	}

		var confirmPassword = $("input#register-confirm-password").val();
		var password = $("input#register-password").val();

		if (confirmPassword != password) {
			$("#password-confirm-error").fadeIn();
      		$("input#register-password").focus();
      		return false;	
		}

		if ((password.length <= 4) || (password.length >= 20)) {
			$("#password-error").fadeIn();
      		$("input#register-password").focus();
      		return false;			
		} 
	

		var email = $("input#register-email").val(); 
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

		if (!re.test(email)) {
			$("#email-error").fadeIn();
      		$("input#register-email").focus();
      		return false;	
		}
		
		var dataString = 'username='+ username + '&email=' + email + '&password=' + password + '&beta=1';
		
		$.ajax({
			type: "POST",
			url: serverPath+"reg.php",
			data: dataString,
			success: function(data) {
				$("#registration-response").html(data).fadeIn();
      		},
      		error: function(data) {
				//alert("An error occurred.");
      		}
     	});
   		return false;
	});
	
	$("#btn-verify").click(function() {
		$(".error-box").hide(); 
		$("#registration-response").hide(); 

		
		var first_name = $("input#first-name").val();
		if ((first_name == "") || (first_name.length == 0)) {
			$("#firstname-error").fadeIn();
			$("input#first-name").focus();
			return false;
		}
		var last_name = $("input#last-name").val();
		if ((last_name == "") || (last_name.length == 0)) {
			$("#lastname-error").fadeIn();
			$("input#last-name").focus();
			return false;
		}
		var username = $("input#register-username").val();
		//alert(username.length);
		if ((username.length <= 4) || (username.length >= 20)) {
			$("#username-error").fadeIn();
      		$("input#register-username").focus();
      		return false;
    	}
		var password = $("input#register-password").val();
		if ((password.length <= 4) || (password.length >= 20)) {
			$("#password-error").fadeIn();
      		$("input#register-password").focus();
      		return false;			
		} 
		
		var secret = $("input#secret").val();
		
		var dataString = 'mode=submit&firstname=' + first_name + '&lastname=' + last_name + '&username='+ username + '&password=' + password + '&secret=' + secret;
		//alert(dataString); 
		
		$.ajax({
			type: "POST",
			url: serverPath+"reg_verify.php",
			data: dataString,
			success: function(data) {
				$("#registration-response").html(data).fadeIn();
      		},
      		error: function(data) {
				alert("An error occurred.");
      		}
     	});
   		return false;
	});
	
	$("#btn-login").click(function() {
		$(".error-box").hide(); 
		$(".information-box").hide();
		$("#login-response-error").hide(); 

		var username = $("input#register-username").val();
		if ((username.length <= 4) || (username.length >= 20)) {
			$("#login-response-error").hide();
			$("#username-error").fadeIn();
			$("#login-beta-info").hide();
      		$("input#register-username").focus();
      		return false;
    	}
		var password = $("input#register-password").val();
		if ((password.length <= 4) || (password.length >= 20)) {
			$("#login-response-error").hide();
			$("#password-error").fadeIn();
			$("#login-beta-info").hide();
      		$("input#register-password").focus();
      		return false;			
		} 
				
		var dataString = 'username='+ username + '&password=' + password;
		
		$.ajax({
			type: "POST",
			url: serverPath+"reg_login.php",
			data: dataString,
			success: function(login) {
				if (login.substring(0, 4) == "true") { // login was successful
                    var lines = login.split("\n");
                    var uri = (lines.length > 1 ? lines[1] : 'dashboard.php');
					window.location = uri;
				} else if (login == "notverified") {
					$("#username-error").hide();
					$("#password-error").hide();
					$("#login-response-error").hide();
					$("#login-beta-info").hide();
					$("#login-notverified-error").fadeIn();
				} else if (login == "false") {
					$("#username-error").hide();
					$("#password-error").hide();
					$("#login-response-error").fadeIn();
					$("#login-beta-info").hide();
					$("#login-notverified-error").hide();

				} else if (login == "beta") {
					$("#username-error").hide();
					$("#password-error").hide();
					$("#login-response-error").hide();
					$("#login-beta-info").fadeIn();
					$("#login-notverified-error").hide();
				}
      		},
      		error: function(data) {
				//alert("An error occurred.");
      		}
     	});
   		return false;
	});
	

	// checking possible cookies that may have been set in the front page
	// when the user was logging in
	// (newfront.js)
	if ($.cookie("login") == "invalidLogin") {
		$("input#register-username").val($.cookie("loginUsername"));
		$("input#register-username").focus();
		$("#login-response-error").fadeIn();
		$.cookie("login", "");
		$.cookie("loginUsername", "");
	} else if ($.cookie("login") == "invalidLoginBeta") {
		$("input#register-username").val($.cookie("loginUsername"));
		$("input#register-username").focus();
		$("#login-beta-info").fadeIn();
		$.cookie("login", "");
		$.cookie("loginUsername", "");
	} else if ($.cookie("login") == "invalidLoginNotVerified") {
		$("input#register-username").val($.cookie("loginUsername"));
		$("input#register-username").focus();
		$("#login-notverified-error").fadeIn();
		$.cookie("login", "");
		$.cookie("loginUsername", "");
	}
	
	$("#btn-change-password").click(function() {
		$(".error-box").hide();
		$("#change-password-response").hide(); 
		var current_password = $("input#change-password-current").val();
		var confirm_password1 = $("input#change-password-new1").val();
		var confirm_password2 = $("input#change-password-new2").val();
		
		//alert(current_password);
		//alert(confirm_password1);
		//alert(confirm_password2);
		
		if ((current_password.length <= 4) || (current_password.length >= 20)) {
			$("#change-password-error").fadeIn();
      		$("input#change-password-current").focus();
      		return false;			
		} 
		if ((confirm_password1.length <= 4) || (confirm_password1.length >= 20)) {
			$("#change-password-error").fadeIn();
      		$("input#change-password-new1").focus();
      		return false;			
		} 
		if ((confirm_password2.length <= 4) || (confirm_password2.length >= 20)) {
			$("#change-password-error").fadeIn();
      		$("input#change-password-new2").focus();
      		return false;			
		} 
		
		
		if (confirm_password1 != confirm_password2) {
			$("#change-password-confirmation-error").fadeIn();
      		$("input#change-password-new1").focus();
			return false;
		}
		
		var dataString = 'oldpassword='+ current_password + '&newpassword1=' + confirm_password1 + '&newpassword2=' + confirm_password2;
		
		$.ajax({
			type: "POST",
			url: serverPath+"reg_dashboard-chpass.php",
			data: dataString,
			success: function(data) {
				$("#change-password-response").html(data).fadeIn();
      		},
      		error: function(data) {
				alert("An error occurred.");
      		}
     	});
   		return false;
	});	
	
		
});
/*!
 * jQuery Cookie Plugin v1.3
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function ($, document, undefined) {

	var pluses = /\+/g;

	function raw(s) {
		return s;
	}

	function decoded(s) {
		return decodeURIComponent(s.replace(pluses, ' '));
	}

	var config = $.cookie = function (key, value, options) {

		// write
		if (value !== undefined) {
			options = $.extend({}, config.defaults, options);

			if (value === null) {
				options.expires = -1;
			}

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			value = config.json ? JSON.stringify(value) : String(value);

			return (document.cookie = [
				encodeURIComponent(key), '=', config.raw ? value : encodeURIComponent(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// read
		var decode = config.raw ? raw : decoded;
		var cookies = document.cookie.split('; ');
		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			if (decode(parts.shift()) === key) {
				var cookie = decode(parts.join('='));
				return config.json ? JSON.parse(cookie) : cookie;
			}
		}

		return null;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) !== null) {
			$.cookie(key, null, options);
			return true;
		}
		return false;
	};

})(jQuery, document);
