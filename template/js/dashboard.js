$(document).ready(function() {

	var serverPath = "ajax/";

	var editor;
	$(".save-section").hide();
	$(".save-warning").hide();
	//$('.lightbox').lightBox();
	//$('img').wrap('<a href=""></a>')
	//alert($('img').attr('src'));
	//target = $('img').get((0));
	$(".discussion").hide();
	//$(".hide").live("hide");
	
	function imgLight() {
          $('img').each(function(index) {
            var image = $('img')[index];
            var source = image.src;
            if (($(image).parent().hasClass("caption_container"))){
              var captionTextId = $(image).parent().children()[1].id;
              var captionText = $("#"+captionTextId).text();
              $(image).wrap('<a class="lightbox" href="' + source + '" title="' + captionText + '"/>');
            }
          });
          $('a.lightbox').lightBox();
        }

	function stripslashes(str) {
		str=str.replace(/\\'/g,'\'');
		str=str.replace(/\\"/g,'"');
		str=str.replace(/\\0/g,'\0');
		str=str.replace(/\\\\/g,'\\');
		return str;
	}
	
	var save_flag = false; // if true there are changes made that haven't been saved.
	
	toolbar1 = 
		[
			{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','-','RemoveFormat' ] },
			{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-' ] },
			{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
			{ name: 'editing', items : [ 'Find','Replace','-','SelectAll'] },
			{ name: 'insert', items : [ 'Image','Table','HorizontalRule',] },
            { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
			{ name: 'tools', items : [ 'Maximize','-', 'Source'] }
		];
	
	var chapter_animate;
	var anim_time;
	var update_rate = 5;

	function chapter_left_clean(newLeft)
	{
		minAllowed = -$("#tab-scroll-wrap").width()+$("#tab-inner-container").width();
		if (newLeft < minAllowed)
		{
			newLeft = minAllowed;
		}
		if (newLeft > 0)
		{
			newLeft = 0;
		}
		return newLeft;
	}
	function set_chapters_left(newLeft)
	{
		newLeft = chapter_left_clean(newLeft);
		$("#tab-scroll-window").css("left",newLeft+"px");
	}
	function move_chapters(amount)
	{
		var newLeft = parseInt($("#tab-scroll-window").css("left"));
		var dt = new Date().getTime() - anim_time;		
		newLeft += amount*dt/update_rate;
		anim_time = anim_time + dt;
		set_chapters_left(newLeft);
	}
	function start_animate(speed)
	{
		anim_time = new Date().getTime();
		chapter_animate = setInterval(function(){
			move_chapters(speed);
		}, update_rate);

	}
	$("#right-arrow-button").mousedown(function(event) {
		clearInterval(chapter_animate);
		start_animate(-5);
		return false;
	});
	$("#left-arrow-button").mousedown(function(event) {
		clearInterval(chapter_animate);
		start_animate(5);
		return false;
	});
	$(document).mouseup(function(event) {
		clearInterval(chapter_animate);
		var newLeft = parseInt($("#tab-scroll-window").css("left"));
		$.cookie("chapter_left", newLeft);
		return false;
	});
	minChapterPos = -$(".active-tab").position().left;
	maxChapterPos = minChapterPos + $("#tab-inner-container").width() - $(".active-tab").width()-22;
	cookieChapterPos = $.cookie("chapter_left");
	if(cookieChapterPos)
	{
		if (cookieChapterPos < minChapterPos)
		{
			cookieChapterPos = minChapterPos;
		}
		if (cookieChapterPos > maxChapterPos)
		{
			cookieChapterPos = maxChapterPos;
		}
		set_chapters_left(cookieChapterPos);
	}
	else
	{
		set_chapters_left(minChapterPos);
	}
	

	/* View book contributors */
	$(".view-book-contributors").click(function(event) {
		var $dialog = $('<div></div>').html("loading...")
					.dialog({
						autoOpen: false,
						title: "View Book Contributors",
						minWidth: 200,
						maxHeight:600,
						width:400,
						height:400		
					});
		$dialog.dialog('open');
		var arr = event.target.id.split("-");
		var book_group_id = arr[3];
		var stringData = 'mode=get-book-contributors&book_group=' + book_group_id;
		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard.php",
				data: stringData, 
				success: function(data) {
					if (data.trim() == "logged_out") {
						$dialog.html("Please login to view book contributors.");
					} else {
						$dialog.html(data);
					}
				},
				error: function(data) {
					alert("An error occurred.");
				}			
		});
		return false;
	});

	$(".edit-section").click(function(event) {
		//alert('test');
		arr = event.target.id.split("-")
		currentIDbox = arr[2];
		//alert(event.target.id);
		//$( "#content-module-152" ).slideUp(300).delay(800);
		//$( "#content-module-152" ).slideDown();
        uncaptionSection("content-section-" + currentIDbox);
		editor = $("#content-section-" + currentIDbox).ckeditor(
            // watchWordCount,
			{
				startupFocus : true,
				toolbar      : toolbar1,
                
			});

        
        // Start tracking the word count for this editor.
        watchWordCount(editor);
        
		//alert(editor);
		
		//$("#content-section-" + currentIDbox ).ckeditorGet().focus();
	
		$("#save-section-" + currentIDbox).show();
		$("#"+event.target.id).hide();
		//$("#save-warning-"+currentIDbox).hide();
		$("#history-box-"+currentIDbox).hide();
		$("#discussion-box-"+currentIDbox).hide();

		//$("#content-section-" + currentIDbox ).append('testing');
	});

	
	$(".destroy-edit-section").click(function(event) {	// main button
		var arr = event.target.id.split("-");
		var id = arr[1];	
		var editor = CKEDITOR.instances["content-section-" + id];
		if (editor) {
			editor_text = $("#content-section-" + id ).ckeditorGet().getData();
			editor_dirty = $("#content-section-" + id ).ckeditorGet().checkDirty();
			$("#content-section-" + id ).ckeditorGet().destroy();
			
			if ((editor_dirty == true) || (save_flag == true)) {
				//alert("Note: Your changes were not saved. Click on 'Edit' then 'Save' to save your changes.");
				$("#save-warning-"+id).fadeIn().html("Note: Your changes were not saved. Click on \'Edit\' then \'Save\' to save your changes.");
				save_flag = true;
			}
		}
		//alert( editor.checkDirty() );	
		
		$("#save-section-"+id).hide();
		$("#history-box-"+id).hide();
		$("#discussion-box-"+id).hide();
		$("#edit-section-"+id).show();
		$("#content-section-"+id).show();

		
		
		imgLight();
	});


	var numtimes = 1;
	$(".next-load-history").live("click", function loadHist(event) {
		arr = event.target.id.split("-");
		id = arr[3];
		numtimes = numtimes + 1; 
		
		var stringData = 'mode=history&id=' + id + '&numtimes=' + numtimes;
		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard.php",
				data: stringData, 
				success: function(data) {
					var a = $("#history-box-"+id).html(data);
					a.find(".get-important-show").show();
					a.find(".get-not-important-hide").hide();
					a.find(".get-important-hide").hide();
					a.find(".get-not-important-show").show();
					
				},
				error: function(data) {
					alert("An error occurred.");
				}			
		});

		return false;

	});

	$(".all-load-history").live("click", function(event) {
		arr = event.target.id.split("-");
		id = arr[3];
		numtimes = 1000000;		
		var stringData = 'mode=history&id=' + id + '&numtimes=' + numtimes;
		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard.php",
				data: stringData, 
				success: function(data) {
					var a = $("#history-box-"+id).html(data);
					a.find(".get-important-show").show();
					a.find(".get-not-important-hide").hide();
					a.find(".get-important-hide").hide();
					a.find(".get-not-important-show").show();
					
				},
				error: function(data) {
					alert("An error occurred.");
				}			
		});

		return false;

	});
	
	$(".important-load-history").live("click", function(event) {
		arr = event.target.id.split("-");
		id = arr[3];
		var stringData = 'mode=history&id=' + id + '&numtimes=' + numtimes + '&important=1';
		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard.php",
				data: stringData, 
				success: function(data) {
					var a = $("#history-box-"+id).html(data);
					a.find(".get-important-show").show();
					a.find(".get-not-important-hide").hide();
					a.find(".get-important-hide").hide();
					a.find(".get-not-important-show").show();
					
				},
				error: function(data) {
					alert("An error occurred.");
				}			
		});

		return false;

	});


	$(".history-section").click(function(event) {	
		var arr = event.target.id.split("-");
		var id = arr[1];
		var editor = CKEDITOR.instances["content-section-" + id];
		if (editor) {
			editor_text = $("#content-section-" + id ).ckeditorGet().getData();
			editor_dirty = $("#content-section-" + id ).ckeditorGet().checkDirty();
			$("#content-section-" + id ).ckeditorGet().destroy();	
			
			if ((editor_dirty == true) || (save_flag == true)) {
				//alert("Note: Your changes were not saved. Click on 'Edit' then 'Save' to save your changes.");
				$("#save-warning-"+id).fadeIn().html("Note: Your changes were not saved. Click on \'Edit\' then \'Save\' to save your changes.");
				save_flag = true;
			}
		}
		
		$("#save-section-"+id).hide();
		$("#edit-section-"+id).show();
		$("#content-section-"+id).hide();
		$("#discussion-box-"+id).hide();
		$("#history-box-"+id).show();
		$("#conflict-warning-"+id).hide();

		
		var stringData = 'mode=history&id=' + id + '&numtimes=1';
		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard.php",
				data: stringData, 
				success: function(data) {
					var a = $("#history-box-"+id).html(data);
					a.find(".get-important-show").show();
					a.find(".get-not-important-hide").hide();
					a.find(".get-important-hide").hide();
					a.find(".get-not-important-show").show();
					
				},
				error: function(data) {
					alert("An error occurred, while retrieving history.");
				}			
		});
		

		imgLight();


		// var w = $(window); //Saving the context to a shorthand variable.
		// w.scroll(function(){ //When we scroll
		// 	if (w.scrollTop() + w.height() == $(document).height()){ //Check if the position is the bottom of the page
		// 		loadHist(id); // If so, then we will load more data.
		// 	}
		// });


		

		$(window).resize(function() {
			if ($(window).width() < 960) {
				$(".username-history").hide();

				if ($(window).width() < 820) {
					$(".user-role-history").hide();

					if ($(window).width() < 740) {
						$(".date-time-history").hide();
					}
				}
			} else if ($(window).width() > 740) {
				$(".date-time-history").show();
				
				if ($(window).width() > 820) {
					$(".user-role-history").show();

					if ($(window).width() > 960) {
						$(".username-history").show();
					}
				}
			}
		});

	});
		
	$(".conflict-warning").hide();

	$(".save-section").click(function(event) {
		arr = event.target.id.split("-");
		id = arr[2];
		//alert($("#origin-dash-"+id).text());
		origin = $("#origin-dash-"+id).text();
		$("#save-section-"+id).hide();
		$("#edit-section-"+id).show();
		
		editor_text = $("#content-section-" + id).ckeditorGet().getData();
		editor_dirty = $("#content-section-" + id).ckeditorGet().checkDirty();
		$("#content-section-" + id).ckeditorGet().destroy();
        captionImages();
		//alert( editor.checkDirty() );\
		
		// if editor is dirty (content has been changed since load save in db ajax)
		if ((editor_dirty == true) || (save_flag == true)) {
			//saveContent(id, editor_text, origin);
			saveContent(id, editor_text);
		}
		$("#save-warning-"+id).hide();
		imgLight();
		
	});
	
	function addslashes(str) {
		str=str.replace(/\\/g,'\\\\');
		str=str.replace(/\'/g,'\\\'');
		str=str.replace(/\"/g,'\\"');
		str=str.replace(/\0/g,'\\0');
		return str;
	}
	
	function saveContent(id, text, origin) {
		save_flag = false;
        $.post(
            serverPath + 'reg_dashboard.php',
            {mode: 'save', id: id, text: text},
            function(data) {
                if (data == 'conflict') {
                    $("#conflict-warning-"+id).fadeIn().html("Warning: Your submission conflicts with another user. Click on \'History\' for more information.");
                }
        });
	}
	


	$(".get-revision").live("click", function(event) {
		var arr = event.target.id.split("-"); 
		var id = arr[2];
		//alert(id);
		//$('.history-section').dialog();
		
		var stringData = 'mode=getRevisionContent&id=' + id;
		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard.php",
				data: stringData, 
				success: function(data) {
					//alert(data);
					var $dialog = $('<div></div>').html(stripslashes(data))
						.dialog({
							autoOpen: false,
							title: $('#revision-name-'+id).text() + "(" + $('#revision-uname-'+id).text() + "), " + $('#revision-date-'+id).text(),
							minWidth: 200,
							maxHeight:600,
							width:400,
							height:400
							
					});
					
					$dialog.dialog('open');
					return false;


				},
				error: function(data) {
					alert("An error occurred.");
				}			
		});
	});
	


	checkedAll = false;
	$(".main-checkbox").live('click', function() {
		
		if (checkedAll == false) {
			$(".sub-checkbox").each(function(index) {
				var elem = $(".sub-checkbox")[index].checked = true;
				checkedAll = true;
			});
		} else {
			$(".sub-checkbox").each(function(index) {
				var elem = $(".sub-checkbox")[index].checked = false;
				checkedAll = false;
			});
		}
	});	
		
	$(".apply-to-selected-history").live('click', function(event) {
		arr = event.target.id.split("-");
		id = arr[4];
		var array_checked = new Array();
		check_flag = false;
		$("#main-checkbox-"+id).attr('checked', false);
		checkedAll = false;

		$(".sub-checkbox-"+id).each(function(index) {
			var checked = $(".sub-checkbox-"+id)[index].checked;
			var check_id_array = $(".sub-checkbox-"+id)[index].id.split("-");
			var check_id = check_id_array[2];
			if (checked) {
				array_checked.push(check_id);
				$("#sub-checkbox-"+check_id).attr('checked', false);
				check_flag = true;
			}

		});
		
		var str_array = array_checked.join();
		
		//alert(check_flag);	
		// let's get the option whether it is mark as important or mark as NOT important
		var val = $("#table-footer-actions-history-"+id).val();
		
		if (val == 'op1') { // mark as important
			var msg = $("#important-input-history-"+id).val();
			$("#important-input-history-"+id).val('');

			
			var stringData = 'mode=updateImportant&array=' + str_array + '&msg=' + msg;
			$.ajax({
					type: "POST",
					url: serverPath+"reg_dashboard.php",
					data: stringData, 
					success: function(data) {
						//alert(data);
						//alert(check_flag);
						if(check_flag) { // so only update counters if something was checked
							arr = data.split('-');
							numedits = arr[0];
							numimportant = arr[1];
							$("#num-total-edits-"+id).html(numedits);
							$("#num-important-edits-"+id).html(numimportant);
						}
					},
					error: function(data) {
						alert("An error occurred.");
					}			
			});
			
			for (i=0; i < array_checked.length; i++) {
				//var a = $("#marked-important-history-"+array_checked[i]).html('<center><img src ="./template/img/ic_msg_confirmation.png" /></center>');
				$("#get-important-show-"+array_checked[i]).show();
				$("#get-not-important-hide-"+array_checked[i]).hide();
				$("#get-important-hide-"+array_checked[i]).show();
				$("#get-not-important-show-"+array_checked[i]).hide();
			}


			//alert(array_checked.length);
		} else if (val == 'op2') { // mark as NOT important
			var stringData = 'mode=updateNotImportant&array=' + str_array;
			$.ajax({
					type: "POST",
					url: serverPath+"reg_dashboard.php",
					data: stringData, 
					success: function(data) {
						//alert(data);
						if(check_flag) { // so only update counters if something was checked
							var arr = data.split('-');
							numedits = arr[0];
							numimportant = arr[1];
							$("#num-total-edits-"+id).html(numedits);
							$("#num-important-edits-"+id).html(numimportant);
						}
					},
					error: function(data) {
						alert("An error occurred.");
					}			
			});
			
			for (i=0; i < array_checked.length; i++) {
				//$("#marked-important-history-"+array_checked[i]).html('<center><img src ="./template/img/ic_msg_error.png" /></center>');
				$("#get-important-show-"+array_checked[i]).hide();
				$("#get-not-important-hide-"+array_checked[i]).show();
				$("#get-important-hide-"+array_checked[i]).hide();
				$("#get-not-important-show-"+array_checked[i]).show();
			}
		}
		

		
		return false;
	});

	$(".important-input-history").submit(function(){
		return false;
	});

	$(".table-footer-actions-history").live("change", function(event) {
		arr = event.target.id.split("-");
		id = arr[4];

		// let's get the option whether it is mark as important or mark as NOT important
		var val = $("#table-footer-actions-history-"+id).val();
		
		if (val == 'op1') { // mark as important
			$("#important-container-history-"+id).show();
		} else if (val == 'op2') { // mark as NOT important
			$("#important-container-history-"+id).hide();
		}

		return false;
	});
	
	$(".generate-permalink").click(function(event) {
		var arr = event.target.id.split("-");
		var id = arr[2];
		$.ajax({
					type: "POST",
					url: serverPath+"reg_dashboard.php",
					data: "mode=getPermalink&id="+id, 
					success: function(data) {
						//$("#"+event.target.id).html('<a href="'+data+'">'+data+'</a>').hide().fadeIn();
						data = '<p>Use the following pin to link to this section: <br /><br /><i><a href="' + data + '">' + data + '</a></i></p>';

						$dialogcube = $('<div></div>').html(data)
							.dialog({
								autoOpen: false,
								title: 'Permalink',
								minWidth: 200,
								maxHeight:600,
								width:400,
								height:400,
								modal: true,	
							});
									
							$dialogcube.dialog('open');
					},
					error: function(data) {
						//alert("An error occurred.");
					}			
			});
		
	});


	$(".get-important-show").live("click", function(event) {
		var arr = event.target.id.split("-"); 
		var id = arr[3];
		//alert(event.target.id);
		//$('.history-section').dialog();
		
		var stringData = 'mode=getImportantContent&id=' + id;
		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard.php",
				data: stringData, 
				success: function(data) {
					//alert(data);
					var $dialog = $('<div></div>').html(stripslashes(data))
						.dialog({
							autoOpen: false,
							title: $('#revision-name-'+id).text() + "(" + $('#revision-uname-'+id).text() + "), " + $('#revision-date-'+id).text(),
							minWidth: 200,
							maxHeight:600,
							width:400,
							height:400		
					});
					
					$dialog.dialog('open');
					return false;


				},
				error: function(data) {
					alert("An error occurred.");
				}			
		});
	});

	$(".get-important-hide").live("click", function(event) {
		var arr = event.target.id.split("-"); 
		var id = arr[3];
		//alert(event.target.id);
		//$('.history-section').dialog();
		
		var stringData = 'mode=getImportantContent&id=' + id;
		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard.php",
				data: stringData, 
				success: function(data) {
					//alert(data);
					var $dialog = $('<div></div>').html(stripslashes(data))
						.dialog({
							autoOpen: false,
							title: $('#revision-name-'+id).text() + "(" + $('#revision-uname-'+id).text() + "), " + $('#revision-date-'+id).text(),
							minWidth: 200,
							maxHeight:600,
							width:400,
							height:400		
					});
					
					$dialog.dialog('open');
					return false;


				},
				error: function(data) {
					alert("An error occurred.");
				}			
		});
	});

	$(".hide").hide();
	var cubeflag = false;
	$(".cube-this").live("click", function(event) {

		arr = event.target.id.split("-"); 
		id = arr[2];
		type = arr[3];

		$("#cube-this-"+id).hide();
		$("#cube-this-already-"+id).show();
		$("#cube-this-"+id+'-'+type).hide();
		$("#cube-this-already-"+id+'-'+type).show();

		//alert(id);

		var stringData = 'mode=cubed&id=' + id + '&type=' + type;

		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard.php",
				data: stringData, 
				success: function(data) {
					$("#cube-this-num-"+id).text(data);
					$("#cube-num-already-"+id).text(data);
					//$("#cube-this-"+id).text("Cubed (" + ourintcubes + ")");
				},
				error: function(data) {
					alert("An error occurred.");
				}			
		});

		cubeflag = true;

	
	});

	$(".cube-this-already").live("click", function(event) {

		arr = event.target.id.split("-"); 
		id = arr[3];
		type = arr[4];

		$("#cube-this-"+id).show();
		$("#cube-this-already-"+id).hide();
		$("#cube-this-"+id+'-'+type).show();
		$("#cube-this-already-"+id+'-'+type).hide();

		//alert(id);
		var stringData = 'mode=uncube&id=' + id + '&type=' + type;
		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard.php",
				data: stringData, 
				success: function(data) {
					$("#cube-num-already-"+id).text(data);
					$("#cube-this-num-"+id).text(data);
					//$("#cube-this-"+id).text("Cubed (" + ourintcubes + ")");
				},
				error: function(data) {
					alert("An error occurred.");
				}			
		});

		cubeflag = false;

		
	});

 
 	$(".view-cubes").live("click", function(event) {
		arr = event.target.id.split("-"); 
		id = arr[2];
		type = arr[3];


		var stringData = 'mode=getcubes&id=' + id + '&type=' + type;
			$.ajax({
					type: "POST",
					url: serverPath+"reg_dashboard.php",
					data: stringData, 
					success: function(data) {

						$dialogcube = $('<div></div>').html(data)
							.dialog({
								autoOpen: false,
								title: 'Cube Views',
								minWidth: 200,
								maxHeight:600,
								width:400,
								height:400,
								modal: true,	
							});
									
							$dialogcube.dialog('open');
						
					},
					error: function(data) {
						alert("An error occurred.");
					}			
			});

	});

	$('.ui-widget-overlay').live("click", function() {
    	$dialogcube.dialog( "close" );
	});


	$(".conflict-history").live("click", function(event) {
		var arr = event.target.id.split("-"); 
		var id = arr[2];
		
		var origin = $("#origin-history-"+id).text();
		//alert(origin);
		//console.log($(".table-history tr td:nth-child(3)"));

		var col_array = $(".table-history tr td:nth-child(3)");
		var conflicts = [];

		for (i = 0; i < col_array.length; i++) {
			var tempid = col_array[i].id;
			var temptxt = $(col_array[i]).text();

			if (temptxt == origin) {
				conflicts.push(tempid.split("-")[2]);
			}
		}

		//alert(conflicts);

		if (conflicts.length == 1) {
			text+= "<p>Load more entries so we know what this edit conflicts with.</p>";
		}

		var text = "<p>This edit was submitted by " + $('#revision-name-'+id).text() + "(" + $('#revision-uname-'+id).text() + ") on " + $('#revision-date-'+id).text() + ".</p>";
		
		for (i = 0; i < conflicts.length; i++) {
			if (id != conflicts[i]) {
				var tempuiid = $("#id-history-"+conflicts[i]).text();
				var tempname = $("#revision-name-"+conflicts[i]).text();
				var tempdate = $("#revision-date-"+conflicts[i]).text();
				if ((tempuiid == "") && (tempname == "") && (tempdate == "")) {
					text+= "<p>Load more entries to determine all the edits that this edit you are viewing conflicts with.</p>";
				} else {
					text+= "<p>It conflicts with the edit submitted by " + tempname + " on " + tempdate + " with ID: " + tempuiid + ".</p>";
				}
			}
		}

		var originuiid = $("#id-history-"+origin).text();
		var originname = $("#revision-name-"+origin).text();
		var origindate = $("#revision-date-"+origin).text();


		if ((originuiid == "") && (originname == "") && (origindate == "")) {
			text+= "<p>Load more entries so we can determine where all these edits originate from.</p>";
		} else {
			text+= "<p>These all originate from the edit submitted by " + originname + " on " + origindate  + " with ID: " + originuiid + ".</p>";
		}
		text+= "<p>A conflict occurs when two or more users load the latest revision and attempt to edit and save their revisions. No conflict is issued when the first user saves their revision. However, when the next user saves an edit originating from the same parent we issue a conflict. We mark all edits that originate from the same parent as a conflict.</p>";

		text+= "<p>Note that we only show conflicts for the loaded entries. If you would like to see possible conflicts with other entries you may load more entries.</p>";

		var $dialog = $('<div></div>').html(stripslashes(text))
			.dialog({
				autoOpen: false,
				title: 'View Conflict - ID: ' + $('#id-history-'+id).text(),
				minWidth: 200,
				maxHeight:600,
				width:400,
				height:400		
			});
		
		$dialog.dialog('open');
		return false;

	});

	$(".form-discussion-main").submit(function(event) {
		var arg = event.target.id;
		var id = arg.split("-")[3];
		var val = $("#input-discussion-main-"+id).val();
		//alert(val);
		//alert(arg);
		$("#input-discussion-main-"+id).val('');

		if (val != '') {
			// do an ajax call to send post to server
            $.post(
                serverPath + "reg_dashboard.php",
                {mode: "newMainComment", id: id, post: val},
                function (data) {
                    var a = $("#inner-comments-"+id).prepend(data);
                    $(a).find("abbr.timeago").timeago();
                    $(a).find(".hide").hide();
                    $(a).find(".hide").removeClass("hide");
                });
		}

	});

	$(".discussion-dash-section").click(function(event) {	// main button
		var arr = event.target.id.split("-");
		var id = arr[3];	
		var editor = CKEDITOR.instances["content-section-" + id];
		if (editor) {
			editor_text = $("#content-section-" + id ).ckeditorGet().getData();
			editor_dirty = $("#content-section-" + id ).ckeditorGet().checkDirty();
			$("#content-section-" + id ).ckeditorGet().destroy();
			
			if ((editor_dirty == true) || (save_flag == true)) {
				//alert("Note: Your changes were not saved. Click on 'Edit' then 'Save' to save your changes.");
				$("#save-warning-"+id).fadeIn().html("Note: Your changes were not saved. Click on \'Edit\' then \'Save\' to save your changes.");
				save_flag = true;
			}
		}
		//alert( editor.checkDirty() );	
		
		$("#save-section-"+id).hide();
		$("#history-box-"+id).hide();
		$("#edit-section-"+id).show();
		$("#content-section-"+id).hide();
		$("#discussion-box-"+id).show();
		
		
		imgLight();


		var stringData = 'mode=getDiscussionPosts&id=' + id;
			$.ajax({
					type: "POST",
					url: serverPath+"reg_dashboard.php",
					data: stringData, 
					success: function(data) {
						var a = $("#inner-comments-"+id).html(data);
						$(a).find("abbr.timeago").timeago();
						$(a).find(".hide").hide();
						$(a).find(".hide").removeClass("hide");
					},
					error: function(data) {
						alert("An error occurred.");
					}			
			});

	});

	$(".form-commentoncomment").live("submit", function(event) {
		var arr = event.target.id.split("-");
		var id = arr[2];
		var val = $("#commentoncomment-"+id).val();
		$("#commentoncomment-"+id).val('');
		//alert(val);

		if (val != '') {
			// var stringData = 'mode=commentOnPost&id=' + id + '&post='+val;
				// $.ajax({
						// type: "POST",
						// url: serverPath+"reg_dashboard.php",
						// data: stringData, 
						// success: function(data) {
							// var a = $("#sub-comments-"+id).append(data);
							// $(a).find("abbr.timeago").timeago();
							// $(a).find(".hide").hide();
							// $(a).find(".hide").removeClass("hide");
						// },
						// error: function(data) {
							// alert("An error occurred.");
						// }			
				// });
            $.post(
                serverPath + "reg_dashboard.php",
                {mode: "commentOnPost", id: id, post: val},
                function(data) {
                    var a = $("#sub-comments-"+id).append(data);
                    $(a).find("abbr.timeago").timeago();
                    $(a).find(".hide").hide();
                    $(a).find(".hide").removeClass("hide");
                });
		}

	});

	$(".delete-discussion").live("click", function(event) {
		var arr = event.target.id.split("-");
		var idparent = arr[2];
		var idself = arr[3];

		// send an ajax call to remove item

		var stringData = 'mode=deleteDiscussion&id=' + idself;
			$.ajax({
					type: "POST",
					url: serverPath+"reg_dashboard.php",
					data: stringData, 
					success: function(data) {
						$("#inner-comment-item-"+idself).remove();
						
					},
					error: function(data) {
						alert("An error occurred.");
					}			
			});
	});

    
    // Set up the list of last pollings to initially be "now-ish".
    var lastPollings = [];
    var now = Math.floor(new Date().getTime() / 1000) - 2;
    $('.content-module-main .discussion').each(function() {
        var id = $(this).attr('id').replace('discussion-box-', '');
        lastPollings[id] = now;
    });
    
    // Use a boolean to check deletions only on every other poll.
    var checkedDeletions = true;
    
    // Start polling for any opened discussion views every several seconds. Any
    // new comments received are placed in their proper spots.
    setInterval(function() {
        // Determine what section(s) have the discussions visible.
        var discussions = $('.content-module-main .discussion')
            .filter(function() {
                return ($(this).css('display') === 'block');
            });
        
        if (discussions.length > 0) {
            // Fetch each ID and its last-update timestamp.
            var ids = [], utcs = [];
            discussions.each(function() {
                var id = $(this).attr('id').replace('discussion-box-', '');
                ids.push(id);
                utcs.push(lastPollings[id]);
            });
            
            // Construct the data to send to the server.
            var data = {mode: 'pollDiscussions',
                        content_ids:  ids.join(','),
                        content_utcs: utcs.join(',')};
            
            // If we didn't last time: get the IDs of all the visible comments.
            var check_ids = [];
            if (!checkedDeletions) {
                discussions.find('ul.nested-comments[id!=""],li.inner[id!=""]')
                    .each(function() {
                        var id = $(this).attr('id');
                        if (id !== undefined)
                            check_ids.push(parseInt(
                                id.replace('inner-comment-item-', '')));
                    });
                check_ids.sort();
                data['check_ids'] = check_ids.join(',');
            }
            checkedDeletions = !checkedDeletions;
            
            // Request new comments, and place them appropriately.
            $.getJSON(
                serverPath + "reg_dashboard.php",
                data,
                function(data) { onPollComments(data, check_ids); });
            
            // Update the last-update times for those we just polled.
            var now = Math.floor(new Date().getTime() / 1000);
            for (var i = 0; i < ids.length; i++)
                lastPollings[ids[i]] = now;
        }
    }, 5000);

    captionImages();
    imgLight();
}); //end document ready


/* Given a mapping of new comments from the server, attempt to place them in
 * the page in their correct spots. Also remove any deleted comments, if
 * applicable, by comparing the server result to the list of what it checked.
 */
function onPollComments(data, check) {
    // Check that the response is not erroneous.
    if (data.hasOwnProperty('error'))
        return;
    
    // Inset all new comments at the right spots.
    var news = data['new'];
    for (var i = 0; i < news.length; i++) {
        cmnt = news[i];
        if ($('#inner-comment-item-' + cmnt.id).length == 0) {
            // Attach the HTML for the comment where it needs to go.
            if (cmnt.parent == -1)
                $('#inner-comments-' + cmnt.masterid).prepend(cmnt.html);
            else
                $('#sub-comments-' + cmnt.parent).append(cmnt.html);
            
            // Fade the comment background from yellow for a nice effect.
            $('#inner-comment-item-' + cmnt.id + ' .comment').first()
                .css('background-color', '#ffffbe')
                .animate({backgroundColor: '#f8f9fa'}, 1500);
        }
    }
    
    if (data.hasOwnProperty('remaining_ids')) {
        // Remove comments in `check` that are not in `remaining_ids`. The key
        // here is that they are both sorted, and check is a superset.
        var remaining = data.remaining_ids;
        for (var i = 0, j = 0; i < check.length; i++) {
            if (check[i] !== remaining[j])
                $('#inner-comment-item-' + check[i]).remove();
            else
                j++;
        }
    }
    
    // Change timestamp notation and check hiddenness on new tags.
    $('.comment abbr.timeago').timeago();
    $('a.hide').hide().removeClass('hide');
}


/* For the CKEditor inside of the given container, track and display the word
 * count in its status bar. NOTE: If any reference is held to the CKEditor
 * object by a variable (ie not as a temporary value) then this will break the
 * editor for subsequent edits/saves on this page load.
 */
function watchWordCount(container) {
    var editor  = '#cke_' + container.attr('id'),
        counter = /\S+/g,
        lbl     = null,
        timer   = setInterval(
        function() {
            if (lbl === null) {
                lbl = $(editor).find('.cke_bottom')
                    .append('<span class="word_count" style="color: #555; '+
                                'float: right; position: relative; top: 4px; '+
                                'padding-right: 20px;">100 words</span>')
                    .find('.word_count');
            }
            try {
                var plainText = $(container.ckeditorGet().getData()).text();
                var res = plainText.match(counter);
                lbl.text((res ? res : []).length + ' words');
            } catch (e) {
                clearInterval(timer);
            }
    }, 500);
}

/* Function that captions all images in the content with an "alt" tag */
var captionCount = 0;
function captionImages() {
    $(".content-section img").each(function(){
        old_img_html = $(this).clone().wrap('<p>').parent().html();
        caption_text = $(this).attr("alt");
        if (caption_text)
        {
            captionCount += 1;
            $(this).attr("alt", "");
            img_html = $(this).clone().wrap('<p>').parent().html();
            div_id = "caption_contain" + captionCount
            new_html = "<div style=\"margin: 0 140px; font-size: 16px;\" class=\"caption_container\" id=\"" + div_id + "\">"+img_html;
                new_html += "<p id=\"caption"+captionCount+"\" style=\"text-align: justify\">"+caption_text+"</p></div>";
            $(this).replaceWith(new_html);
            $("#"+div_id).data("oldhtml", old_img_html); 
            if ($("#caption"+captionCount).height() < 30)
            {
                $("#caption"+captionCount).css("text-align", "center");
            }
        }
    });
}

/* Remove all captions made by captionImages */
function uncaptionImages() {
    $(".caption_container").each(function(){
        $(this).replaceWith($(this).data("oldhtml"));
    });
    captionCount = 0;
}

/* Remove all captions that are children of given id */
function uncaptionSection(sec_id) {
    $("#" + sec_id + " .caption_container").each(function(){
        $(this).replaceWith($(this).data("oldhtml"));
    });
}

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

