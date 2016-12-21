<?php

// arrange chapters/sections

include_once("general_header.php");
include_once("db.php");

$con = mysql_connect($host,$user,$pass);
mysql_select_db($main_db, $con);

ob_start();
include_once('./template/dashboard_main.html');

// JS Files needed

$js_array = array(
	'jquery',
	'functions.js',
	'jquery.jeditable.js',
	'ui/jquery.ui.core.js',
	'ui/jquery.ui.widget.js',
	'ui/jquery.ui.mouse.js',
	'ui/jquery.ui.sortable.js'
);
//{JS-AREA-HERE}
//print_r ($js_array);

$str = ob_get_contents();
ob_end_clean();


$register_form = '
	
	<div class="side-menu fl">

	<h3>Groups Navigation</h3>
	<ul>
		<li><a href="#" id="side-registered-groups">Registered Groups</a></li>
		<li><a href="#" id="side-find-groups">Find Groups</a></li>
	</ul>

	</div> <!-- end side-menu -->


	
	<div class="side-content fr">

		<div class="content-module">	  					    

				<div class="half-size-column fl">
				   <div class="content-module">			
					    <div class="content-module-heading cf">
						    <h3 class="fl">A half size box</h3>
                            <span class="fr expand-collapse-text">Click to collapse</span>				
                            <span class="fr expand-collapse-text initial-expand">Click to expand</span>						    </div>
                        
				    
                        <div class="content-module-main">

                        </div>
				    </div>
				</div>
				
				<div class="half-size-column fr">
				    <div class="content-module">	
					    <div class="content-module-heading cf">
						    <h3 class="fl">Another half size box</h3>
                            <span class="fr expand-collapse-text">Click to collapse</span>				
                            <span class="fr expand-collapse-text initial-expand">Click to expand</span>						    </div>
                        
                        <div class="content-module-main cf">

                        </div>
				    </div>
				</div>

		</div>
	';

$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url.'template/', $str);

$str =  str_replace('{REGISTER-FORM-HERE}', $register_form, $str);









$str = top_bar($str);
echo $str;

?>

