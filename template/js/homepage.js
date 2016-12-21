$(document).ready(function (){
	//hide login-form on page load
	$("#login-form").hide(); 
	
	$("#login-btn").click(function(){
		$("#front-module").hide(); 
		$("#login-form").show(); 
	});
	$("#return-home").click(function(){
		$("#front-module").show(); 
		$("#login-form").hide(); 
	});
	
	

});

function submitForm() {
    	$.ajax({type:'POST', url: 'submit-login.php', data:$('#login-form').serialize(), success: function(response) {
       	 $('#login-form').find('#form-result').html(response);
    	}});
    	return false;
}