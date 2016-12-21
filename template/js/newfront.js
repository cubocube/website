function anim_bulb()
{
	$("#lightbulb-signup").animate({
	 	top: 10,
	 	opacity: 1
	}, 500, anim_arrow);

}

function anim_arrow()
{
	$("#arrow-signup").animate({
	 	opacity: 1,
		left: -230,
		top: 410
	}, 500, function() {});
	if ($(document).width() > 1500)
	{
	$("#arrow-signup-mirror").animate({
	 	opacity: 1,
		left: -770,
		top: 410
		}, 500, function() {});
	}
}

function child_height(obj)
{
 	return $($(obj).children()[0]).height();
}

function flash(obj)
{
	$(obj).stop(true).addClass('login-red').delay(250).queue(function(next){
	 	$(this).removeClass('login-red');
		next();
	});
}

$(document).ready(function() {
	var serverPath = "ajax/";
	$("#book-signup").animate({
	 	opacity: 1
	}, 500, anim_bulb);

	oldObj = "#fragment-1";
	$(oldObj).show()
 	$("#module-container").css("height", child_height(oldObj));
	$("ul#tab-front li a").click(function(e) {
	 	e.preventDefault();
	 	$(".selected").removeClass("selected");
		$(this).addClass("selected");
		$("#tab-indicator").css("left", ($(this).position().left+15)+"px");

		myObj = $(this).attr('href');

		scopeOldObj = oldObj;

		if (oldObj != myObj)
		{
			$(oldObj).animate({opacity: 0}, 200, function() {
				$(scopeOldObj).css("opacity", "1");
				$(scopeOldObj).hide()
				$(myObj).css("opacity", "0");
			 	$(myObj).show()
				$(myObj).animate({opacity: 1}, 200, function() {});
			});
		
			$(myObj).show();
			myHeight = child_height(myObj);
			$(myObj).hide();
			$("#module-container").animate({height: myHeight}, 500, function() {});
			oldObj = myObj;
		}
	});


	/*$("#signup-text").click(function() {
		$.colorbox({inline:true, href:"#signup-dialog",
			initialWidth: 1,
			initialHeight: 1
		});
	});*/


	$("#login-front").submit(function(e) {
	 	e.preventDefault();

		var username = $('input[name$="login"]').val();
		var password = $('input[name$="password"]').val();
		var dataString = 'username='+ username + '&password=' + password;


		// input checking
		$('input[name$="login"]').removeClass('login-red');
		$('input[name$="password"]').removeClass('login-red');


		if ((username.length <= 4) || (username.length >= 20)) {
			flash('input[name$="login"]');
     	$('input[name$="login"]').focus();
     	return false;
    }

    if ((password.length <= 4) || (password.length >= 20)) {
			flash('input[name$="password"]');
     	$('input[name$="password"]').focus();
     	return false;			
		} 

		
		$.ajax({
			type: "POST",
			url: serverPath+"reg_login.php",
			data: dataString,
			success: function(login) {

					if (login.search("true") != -1) { // login was successful
						window.location = "dashboard.php";
					} else if (login.search("false") != -1) {
						$.cookie("login", "invalidLogin");
						var usernameInvalid = $("#username-login-frontpage").val();
						$.cookie("loginUsername", usernameInvalid);
					 	window.location = "login.php";

					} else if (login.search("beta") != -1) {
						$.cookie("login", "invalidLoginBeta");
						var usernameInvalid = $("#username-login-frontpage").val();
						$.cookie("loginUsername", usernameInvalid);
					 	window.location = "login.php";
					} else if (login.search("notverified") != -1) {
						$.cookie("login", "invalidLoginNotVerified");
						var usernameInvalid = $("#username-login-frontpage").val();
						$.cookie("loginUsername", usernameInvalid);
					 	window.location = "login.php";
					}
      			},
      			error: function(data) {
					$.cookie("login", "invalidLogin");
					var usernameInvalid = $("#username-login-frontpage").val();
					$.cookie("loginUsername", usernameInvalid);
				 	window.location = "login.php";
      			}
		});

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
