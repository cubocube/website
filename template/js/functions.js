$(document).ready(function (){
	//hide the expand text
	$(".initial-expand").hide(); 
	
	$("div.content-module-heading").click(function(){
		$(this).next("div.content-module-main").slideToggle(); 
		
		$(this).children(".expand-collapse-text").toggle();
	});
	
});