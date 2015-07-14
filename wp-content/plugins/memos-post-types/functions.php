<?php

function get_testimonials($testimonial){ 	

	global $wpdb;

	$ts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type='testimonials'");
		
	$vals[]=array(__("Random"),"random");
	$vals[]=array(__("None"),"none");
	foreach ($ts as $t) {
		$vals[]= array($t->post_title,$t->ID);
	}
	$testimonials_options="";
	foreach($vals as $val){
		$sel=($val[1]==$testimonial)?"selected=\"selected\"":"";
		$testimonials_options.="<option value=\"".$val[1]."\" ".$sel.">".$val[0]."</option>";
	}

	echo "<label>Testimonial:</label><select name=\"testimonial\"  >".$testimonials_options."</select><br/>";
}


function get_reference($reference){ 	

	global $wpdb;

	$ts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type='reference'");
		
	$vals[]=array(__("Random"),"random");
	$vals[]=array(__("None"),"none");
	foreach ($ts as $t) {
		$vals[]= array($t->post_title,$t->ID);
	}
	$reference_options="";
	foreach($vals as $val){
		$sel=($val[1]==$reference)?"selected=\"selected\"":"";
		$reference_options.="<option value=\"".$val[1]."\" ".$sel.">".$val[0]."</option>";
	}

	echo "<label>Reference:&nbsp;&nbsp;</label><select name=\"reference\"  >".$reference_options."</select><br/>";
}

function get_casestudy($casestudy){ 	

	global $wpdb;

	$ts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type='casestudy'");
		
	$vals[]=array(__("Random"),"random");
	$vals[]=array(__("None"),"none");
	foreach ($ts as $t) {
		$vals[]= array($t->post_title,$t->ID);
	}
	$casestudy_options="";
	foreach($vals as $val){
		$sel=($val[1]==$casestudy)?"selected=\"selected\"":"";
		$casestudy_options.="<option value=\"".$val[1]."\" ".$sel.">".$val[0]."</option>";
	}

	echo "<label>Casestudy:&nbsp;&nbsp;</label><select name=\"casestudy\"  >".$casestudy_options."</select><br/>";
}

function get_settings_cs($language, $homepage){ 	

	if($language=="english"){
		$chl="checked=\"checked\"";
	}else{
		$chl="";
	}
		
	if($homepage=="true"){
		$chh="checked=\"checked\"";
	}else {
		$chh="";
	}

	echo "<br/><label>Hompage:</label><input type=\"checkbox\" name=\"homepage\" value=\"true\" $chh> <br/>";
	echo "<label>Language:</label><input type=\"checkbox\" name=\"language\" value=\"english\" $chl> english";
	
}

?>