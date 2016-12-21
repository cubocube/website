$(document).ready(function() {

	var serverPath = "ajax/";

	$("#organize-chapters-sections").show();
	$("#edit-names-chapters-sections").hide();
	$("#add-chapters-sections").hide();
	$("#remove-chapters-sections").hide();


	$(".remove-warning").hide();
	$(".add-information").hide();
	$(".btn-return-add-chapter").hide();
	$(".btn-add-section").hide();
	
	var remove_element_id = -1000;
	var add_element_id = -1000;
	
	var editableOption = 
	
	{
		callback: function(value, settings) {
        	//alert("callback");
        	
        	$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard-arrsections-order.php",
				data: 'updated=editable',
				success: function(data) {
					$("#sortable-box").html(data);
				},
				error: function(data) {
					alert("An error occurred.");
				}
			});
     	}
	};
	
	

	$('.edit-dash-arrsections').editable(serverPath+'reg_dashboard-arrsections.php', editableOption);
	
		var sortableOptions1 = 
		{
			update: function(event, ui) {
				
				
					$.ajax({
					type: "POST",
					url: serverPath+"reg_dashboard-arrsections-order.php",
					data: { pages: $(this).sortable('serialize')}, 
					success: function() {
					
						$.ajax({
							type: "POST",
							url: serverPath+"reg_dashboard-arrsections-order.php",
							data: 'updated=sortable',
							success: function(data) {
								//$("#registration-response").html(data).fadeIn();
								//alert("change was successful");
								$("#editable-box").html(data);
								$('.edit-dash-arrsections').editable(serverPath+'reg_dashboard-arrsections.php');
							},
							error: function(data) {
								alert("An error occurred.");
							}
						});	
					
					},
					error: function(data) {
						alert("An error occurred.");
					}			
				});		
			}
		};
		
		var sortableOptions2 = 
		
		{
			update: function(event, ui) {
				//$.post("reg_dashboard-arrsections-order.php", 
				//{ pages: $(this).sortable('serialize')});
				
				$.ajax({
					type: "POST",
					url: serverPath+"reg_dashboard-arrsections-order.php",
					data: { pages: $(this).sortable('serialize')}, 
					success: function() {
					
						$.ajax({
							type: "POST",
							url: serverPath+"reg_dashboard-arrsections-order.php",
							data: 'updated=sortable',
							success: function(data) {
								//$("#registration-response").html(data).fadeIn();
								//alert("change was successful");
								$("#editable-box").html(data);
								$('.edit-dash-arrsections').editable(serverPath+'reg_dashboard-arrsections.php');
							},
							error: function(data) {
								alert("An error occurred.");
							}
						});	
					
					},
					error: function(data) {
						alert("An error occurred.");
					}			
				});		
			}
		};
		
		var sortableOptions3 = 
		
		{
			update: function(event, ui) {
				//$.post("reg_dashboard-arrsections-order.php", 
				//{ pages: $(this).sortable('serialize')});
				
					$.ajax({
					type: "POST",
					url: serverPath+"reg_dashboard-arrsections-order.php",
					data: { pages: $(this).sortable('serialize')}, 
					success: function() {
					
						$.ajax({
							type: "POST",
							url: serverPath+"reg_dashboard-arrsections-order.php",
							data: 'updated=sortable',
							success: function(data) {
								//$("#registration-response").html(data).fadeIn();
								//alert("change was successful");
								$("#editable-box").html(data);
								$('.edit-dash-arrsections').editable(serverPath+'reg_dashboard-arrsections.php');
							},
							error: function(data) {
								alert("An error occurred.");
							}
						});	
					
					},
					error: function(data) {
						alert("An error occurred.");
					}			
				});
				
			}
		};
		
		
	
		$( ".sortable1" ).sortable(sortableOptions1);
		$( ".sortable2" ).sortable(sortableOptions2);
		$( ".sortable3" ).sortable(sortableOptions3);
		
		$( ".sortable1" ).disableSelection();
		$( ".sortable2" ).disableSelection();
		$( ".sortable3" ).disableSelection();


	
	
	$(".remove-click").live("click", function (event) { 
		remove_element_id = event.target.id;
		var element_val = $("#"+remove_element_id).text();
		//alert(element_id); 
		//alert(element_val);
		var txt = "Do you want to delete the selected item: " + element_val + "? Please note that any children of the selected item will also be removed.";
		//var response = confirm(txt);
		
		$("#warning-msg-remove").html(txt);
		$(".remove-warning").show();
		
			
	});
	
	$("#btn-delete-sch").live("click", function (event) { 
		$(".remove-warning").hide();
		var sel = "#"+remove_element_id+"";
		$(sel).parent().remove();
		
		var stringData ='updated=removed&element_id=' + remove_element_id;
		
		$.ajax({
				type: "POST",
				url: serverPath+"reg_dashboard-arrsections-order.php",
				data: stringData, 
				success: function() {
					//do nothing
				},
				error: function(data) {
					alert("An error occurred.");
				}			
		});
		
		return false;
	});
	
	
	
	$(".btn-add-chapter").live("click", function (event) { 
		$(".add-information").hide();
		var chInput = $("#simple-input-add-chapter").val();
		$("#simple-input-add-chapter").val("");
		
		if (chInput != "") {
			var stringData ='updated=added-chapter&chapter=' + chInput;
			$.ajax({
					type: "POST",
					url: serverPath+"reg_dashboard-arrsections-order.php",
					data: stringData, 
					success: function(data) {
						$("#container-for-add").html(data);
					},
					error: function(data) {
						alert("An error occurred.");
					}			
			});
			
		}
		return false;
	});
	
	
	$(".add-click").live("click", function(event) { 
		add_element_id = event.target.id;
		//alert(add_element_id);		
		//$(".add-information").show();
		$("#simple-input-add-chapter").val("");
		$("#simple-input-add-chapter").focus();
		$(".btn-add-chapter").hide();
		$(".btn-add-section").show();
		$(".btn-return-add-chapter").show();
		var element_txt = $("#"+add_element_id).text();
		$("#label-add").text("Add Section to " + element_txt);
	}); 	
	
	$(".btn-return-add-chapter").live("click", function(event) {
		$(".btn-add-chapter").show();
		$(".btn-add-section").hide();
		$(".btn-return-add-chapter").hide();
		$("#label-add").text("Add Chapter to Root");
		$("#simple-input-add-chapter").val("");
		$("#simple-input-add-chapter").focus();
		return false;	
	});
	
	$(".btn-add-section").live("click", function(event) { 
		$(".add-information").hide();
		var chInput = $("#simple-input-add-chapter").val();
		$("#simple-input-add-chapter").val("");
		
		if (chInput != "") {
			var stringData ='updated=added-section&section=' + chInput + '&to=' + add_element_id;
			$.ajax({
					type: "POST",
					url: serverPath+"reg_dashboard-arrsections-order.php",
					data: stringData, 
					success: function(data) {
						$("#container-for-add").html(data);
					},
					error: function(data) {
						alert("An error occurred.");
					}			
			});		

			
		}
		
		return false;
	});
	
	$("#side-change-order").click(function(){
		$("#organize-chapters-sections").show();
		$("#edit-names-chapters-sections").hide();
		$("#add-chapters-sections").hide();
		$("#remove-chapters-sections").hide();
		
		var stringData ='get=sortable';
		$.ajax({
			type: "POST",
			url: serverPath+"reg_dashboard-arrsections-order.php",
			data: stringData, 
			success: function(data) {
				$("#sortable-box").html(data).find('.sortable1').sortable(sortableOptions1).find('.sortable2').sortable(sortableOptions2).find('.sortable3').sortable(sortableOptions3);
			},
			error: function(data) {
				alert("An error occurred.");
			}			
		});
		

	});

	$("#side-change-name").click(function(){
		$("#organize-chapters-sections").hide();
		$("#edit-names-chapters-sections").show();
		$("#add-chapters-sections").hide();
		$("#remove-chapters-sections").hide();
		
		var stringData ='get=editable';
		$.ajax({
			type: "POST",
			url: serverPath+"reg_dashboard-arrsections-order.php",
			data: stringData, 
			success: function(data) {
				$("#editable-box").html(data).find('.edit-dash-arrsections').editable(serverPath+'reg_dashboard-arrsections.php', editableOption);
			},
			error: function(data) {
				alert("An error occurred.");
			}			
		});		
		
	});	
	
	$("#side-add-section").click(function(){
		$("#organize-chapters-sections").hide();
		$("#edit-names-chapters-sections").hide();
		$("#add-chapters-sections").show();
		$("#remove-chapters-sections").hide();
		
		var stringData ='get=add';
		
		$.ajax({
			type: "POST",
			url: serverPath+"reg_dashboard-arrsections-order.php",
			data: stringData, 
			success: function(data) {
				$("#container-for-add").html(data);
			},
			error: function(data) {
				alert("An error occurred.");
			}			
		});
		
	});
	
	$("#side-remove-section").click(function(){
		$("#organize-chapters-sections").hide();
		$("#edit-names-chapters-sections").hide();
		$("#add-chapters-sections").hide();
		$("#remove-chapters-sections").show();
	
		var stringData ='get=removable';
		$.ajax({
			type: "POST",
			url: serverPath+"reg_dashboard-arrsections-order.php",
			data: stringData, 
			success: function(data) {
				$("#removable-box").html(data);
			},
			error: function(data) {
				alert("An error occurred.");
			}			
		});		
		
		
	});
	
 });
 
 