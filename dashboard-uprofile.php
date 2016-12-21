<?php

include_once("general_header.php");

ob_start();
include_once('./template/dashboard_main.html');


// JS Files needed

$js_array = array(
	'jquery',
	'functions.js',
	'dashboard-uprofile.js',
	);

$str = ob_get_contents();
ob_end_clean();





$register_form = '
	<div class="content-module">			
		<div class="content-module-heading cf">
			<h3 class="fl">Account Information</h3>
			<span class="fr expand-collapse-text">Click to collapse</span>				
			<span class="fr expand-collapse-text initial-expand">Click to expand</span>				
		</div>
		
		<div class="content-module-main">
			<table>
				<thead>
					<tr>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Username</th>
						<th>Student #</th>
						<th>Email</th>
					</tr>
				</thead> 
				<tfoot>
					<tr>
						<td colspan="6" class="table-footer">
							<div id="select_what_edit">
							
								<!-- ><label for="table-footer-actions">Edit your account</label> -->
								<select name="table-footer-actions" id="table-footer-uprofile">
									<option value="firstname">First Name</option>
									<option value="lastname">Last Name</option>
									<option value="studentnum">Student #</option>
									<option value="email">Email</option>
								</select>
								
								<a href="#" class="round button blue text-upper" id="dashboard-uprofile-edit">Edit</a>
							
							</div>
								<form action="#" id="edit-dashboard-uprofile">
					    		<fieldset>
									<p id="input-uprofile-normal">
										<label for=input-dashboard-uprofile">Edit Selected Field</label>
					        			<input type="text" class="round" id="input-dashboard-uprofile" />
					        		</p>
					        		
					        		<p class="form-error" id="input-uprofile-error">
					                    <label for="input-dashboard-uprofile-error">Edit Selected Field</label>
					                    <input type="text" class="round error-input" id="input-dashboard-uprofile-error" />
					                    <em>There was an error.</em>
					                </p>
					        	</fieldset>
					        	
					        	<input type="submit" value="Submit" class="button round blue text-upper image-right ic-right-arrow" id="submit-dashboard-uprofile" />
													
					        </form>

						</td>
					</tr>
				</tfoot>
				<tbody>
					<tr>
						<td id="td-firstname">{FIRST-NAME-HERE}</td>
						<td id="td-lastname">{LAST-NAME-HERE}</td>
						<td>{USERNAME-HERE}</td>
						<td id="td-studentnum">{STUDENT-ID-HERE}</td>
						<td id="td-email">{EMAIL-HERE}</td>
					</tr>
					
				</tbody>
			</table>
		</div> 
	</div> <!-- end content-module -->
	
	';

$str =  str_replace('{JS-AREA-HERE}', load_js($js_array), $str);
$str = str_replace('{URL-BASE}', $url.'template/', $str);
$str =  str_replace('{REGISTER-FORM-HERE}', $register_form, $str);

$first_last = $first_name . " " . $last_name;


$str =  str_replace('{YOUR-NAME-HERE}', $first_last, $str);
$str =  str_replace('{FIRST-NAME-HERE}', $first_name, $str);
$str =  str_replace('{LAST-NAME-HERE}', $last_name, $str);
$str =  str_replace('{USERNAME-HERE}', $username, $str);
$str =  str_replace('{STUDENT-ID-HERE}', $user_student_id, $str);
$str =  str_replace('{EMAIL-HERE}', $user_email, $str);
$str = top_bar($str);
$str = str_replace('{CUBOCUBE-BOOK-ID}', $_SESSION['cubogroup'], $str);



echo $str; 

//echo $register_form; 
//echo $str_register;

?>

