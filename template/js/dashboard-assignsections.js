var serverPath = "ajax/";


$(document).ready(function() {

	var user_selected_flag = false;
	var user_selected = "";
	
	$("#drop-user-preview").hide();
	
	$("input#tags-simple-input").focus();
	var whichtypesearch = $('#select-search-by').val();
	var stringData ='geti=uname';
	
	$('#select-search-by').change(function() {
		whichtypesearch = $('#select-search-by').val();
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
		$("input#tags-simple-input").val("");
		$("input#tags-simple-input").focus();
	});
    
    // Submitting the form should try to do the same as autocomplete.
    $('#tags-form').submit(function() {
        whichtypesearch = $('#select-search-by option:selected').val();
        submitAssignUser($('#tags-simple-input').val());
        $('#tags-simple-input').autocomplete('close');
        $('#tags-simple-input')[0].blur();
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
						$( "#tags-simple-input" ).autocomplete({
						source: data,
						minLength: 0,
						select: function(event,ui) {
							var selectedObj = ui.item;
							submitAssignUser(selectedObj.value); 
						}
					});
				},
				error: function(data) {
					alert("An error occurred in getResultsAssign.");
				}			
			});
	}
	
	function submitAssignUser(data) {
		user_selected = data;
        $.post(
            serverPath + "reg_dashboard-populate-assign.php",
            {mode:  "preview",
             have:  whichtypesearch,
             value: data},
            function(data) {
                $("#assign-preview-box").html(data);
                bindUnassignments();
                user_selected_flag = true;
                $("#drop-user-preview").slideDown();
            }
        );
	}
	
    // Assign the selected user to the clicked section.
	$(".assignThis").click(function(event) {
        if (user_selected_flag == true) {
			
			var sel_name  = $("#selected-username").text();
			var trig_id   = event.target.id;
			var trig_name = $("#" + trig_id).text();
            var sec_id    = trig_id.replace('assignThis_', '');
            
            $.post(
                serverPath + "reg_dashboard-populate-assign.php",
                {mode:     "assign",
                 username: sel_name,
                 to:       sec_id},
                function(data) {
                    $("#assign-preview-box").html(data);
                    bindUnassignments();
                }
            );
	 	}
	
	});

});


/* Bind to each section listed in the selected user's assignments, so that
 * when they are clicked, that section is removed. As such, this should be
 * called only and every time the selected user's block is reset.
 */
function bindUnassignments() {
    // Un-assign the selected user from the clicked section.
	$(".unassignThis").click(function(event) {
        var sel_name = $("#selected-username").text();
        if (sel_name != '') {
            var sec_id = $(this).attr('rel');
            
            $.post(
                serverPath + "reg_dashboard-populate-assign.php",
                {mode:     "remove",
                 username: sel_name,
                 from:     sec_id},
                function(data) {
                    $("#assign-preview-box").html(data);
                    bindUnassignments();
                }
            );
	 	}
	});
}
