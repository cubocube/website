<?php

include_once("general_header.php");

ob_start();
include_once('./template/dashboard_main.html');


// JS Files needed

$js_array = array(
	'jquery',
	'ui/jquery.ui.core.js',
	'ui/jquery.ui.widget.js',
	'ui/jquery.ui.mouse.js',
	'ui/jquery.ui.draggable.js',
	'ui/jquery.ui.position.js',
	'ui/jquery.ui.resizable.js',
	'ui/jquery.ui.dialog.js',
	'functions.js',
	'dashboard-cb.js',
	);

$str = ob_get_contents();
ob_end_clean();

function retToJoin($userid) {
	// only show public books
	$q = "SELECT `id`,`name`,`description` FROM `groups` 
		  WHERE `id` NOT IN
              (SELECT `groupid` FROM `registered_groups`
               WHERE `userid` = $userid)
            AND `type` = ".PUBLIC_BOOK;

 	$result = mysql_query($q);

	$ret = '';
 	if (mysql_num_rows($result) == 0) {
 		$ret = "<p>There are no CuboBooks for you to join at this time.</p>";
 	} else {
 		while($row = mysql_fetch_assoc($result)) {
 			$hash = md5($_SESSION["logged"] . "join");

 			$ret .= '<p><a href="dashboard.php?c=' . $row['id'] . '&join=' . $hash . '">' . $row['name'] . '</a>: ';
 			$ret .= $row['description'] . " ";
 			$ret .= '</p>';
 		}
 	}

	return $ret;
}


$main_content = '

	<div class="side-menu fl">

	<h3>Menu</h3>
	<ul>
		<li><a href="javascript:void(0);" id="side-dash-mycb">My CuboBooks</a></li>
		<li><a href="javascript:void(0);" id="side-dash-findcb">Find CuboBooks</a></li>
		<li><a href="javascript:void(0);" id="side-dash-newcb">Create a new CuboBook</a></li>
	</ul>

	</div> <!-- end side-menu -->

	<div class="side-content fr">

		<div class="content-module" id="drop-cb-dash" style="display:none;">			
			<div class="content-module-heading cf">
				<h3 class="fl">CuboBooks To Join</h3>
				<span class="fr expand-collapse-text">Click to collapse</span>				
				<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
			</div>
			
			<div class="content-module-main">
				<p>Click on the name of the CuboBook you are interested to join.</p>
				{CUBOBOOKS-TO-JOIN}
			</div> 

		</div> <!-- end content-module -->


		<div class="content-module" id="up-cb-dash">			
			<div class="content-module-heading cf">
				<h3 class="fl">My CuboBooks</h3>
				<span class="fr expand-collapse-text">Click to collapse</span>				
				<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
			</div>
			
			<div class="content-module-main">
				<p>CuboBooks are social books that you can collaboratively curate and read with your peers.</p>
				{PLACEHOLDER-CB-TEXT}
				<ul class="regular-ul">
	                {CUBOBOOKS-YOU-HAVE-JOINED}
	            </ul>

			</div> 

		</div> <!-- end content-module -->

		<div class="content-module" id="new-cb-dash" style="display:none;">			
			<div class="content-module-heading cf">
				<h3 class="fl">Create a new CuboBook</h3>
				<span class="fr expand-collapse-text">Click to collapse</span>				
				<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
			</div>
			
			<div class="content-module-main">
				<form>
					<p>
						<label id="label-add" for="simple-input-new-cubobook-name">CuboBook Name</label>
						<input type="text" class="round default-size-input" id="simple-input-new-cubobook-name" />
					</p>
					<p>
						<label id="label-add" for="simple-input-new-cubobook-description">CuboBook Description</label>
						<textarea class="round default-size-input" id="simple-input-new-cubobook-description"></textarea>
					</p>
					<input type="submit" class="button round blue text-upper image-right ic-right-arrow" id="btn-new-cubobook-submit">
					<p>
						<div class="error-box round default-size-input new-cubobook-error" id="new-cubobook-name-error" style="display:none;">
							Invalid CuboBook name. Your CuboBook name should be between 3 and 30 characters.
						</div>
						<div class="error-box round default-size-input new-cubobook-error" id="new-cubobook-description-error" style="display:none;">
							Invalid CuboBook description. Your CuboBook description should be between 3 and 200 characters.
						</div>
						<div class="error-box round default-size-input new-cubobook-error" id="new-cubobook-duplicate-name-error" style="display:none;">
							Invalid CuboBook name. Another CuboBook with this name already exists.
						</div>
					</p>
				</form>			
			</div> 

		</div> <!-- end content-module -->
	</div>
	
	';

$user_id = (int) $user_id;
$q = "SELECT `name` FROM `groups` AS g
          JOIN `registered_groups` AS r ON r.`groupId` = g.`id`
      WHERE r.`userId` = $user_id";
$result = mysql_query($q);

if (mysql_num_rows($result) == 0) {
 	$cb_placeholder = "<p>You have not joined any CuboBooks yet.</p>";
 	$main_content = str_replace("{CUBOBOOKS-YOU-HAVE-JOINED}", "", $main_content);
} else {
	$cb_placeholder = "<p>Looks like you've joined the following CuboBooks.</p>";
	$main_content = str_replace("{CUBOBOOKS-YOU-HAVE-JOINED}", frontJoinedBookLinks($user_id), $main_content);	
}

$main_content = str_replace("{PLACEHOLDER-CB-TEXT}", $cb_placeholder, $main_content);

$main_content = str_replace("{CUBOBOOKS-TO-JOIN}", retToJoin($user_id), $main_content);







$str = str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url.'template/', $str);
$str = str_replace('{REGISTER-FORM-HERE}', $main_content, $str);


$str = top_bar($str);
echo $str; 

//echo $register_form; 
//echo $str_register;

?>

