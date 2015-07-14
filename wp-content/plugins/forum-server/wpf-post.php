<?php
$quote = "";
global $wpdb, $vasthtml;

if($user_ID || $this->allow_unreg()){

if(isset($_GET['quote'])){
	$quote_id = $this->check_parms($_GET['quote']);
	$text = $wpdb->get_row("SELECT text, author_id, `date` FROM $this->t_posts WHERE id = $quote_id");
	
	$user = get_userdata($text->author_id);
	$q = "[quote][b]".__("Quote from", "vasthtml")." $user->user_login ".__("on", "vasthtml")." ".$vasthtml->format_date($text->date)."[/b]\n" .htmlentities($text->text, ENT_QUOTES, get_bloginfo('charset'))."[/quote]";
}
if(($_GET['vasthtmlaction'] == "postreply")){

	$options = get_option("vasthtml_options");
	$this->current_view = POSTREPLY;
	$thread = $this->check_parms($_GET['thread']);
		$out .= $this->header();

	$out .= "<form action='".WPFURL."wpf-insert.php' name='addform' method='post'>";
	$out .= "<table class='wpf-table' width='100%'>
			<tr>
				<th colspan='2'>".__("Post Reply", "vasthtml")."</th>
			</tr>
			<tr>	
				<td>".__("Subject:", "vasthtml")."</tf>
				<td><input type='text' name='add_post_subject' value='Re: ".$this->get_subject($thread)."'/></td>
			</tr>
			<tr>	
				<td valign='top'>".__("Message:", "vasthtml")."</td>
				<td>";
						$out .= $this->form_buttons();

					$out .= "<br /><textarea ".ROW_COL." name='message' >".stripslashes($q)."</textarea>";
				$out .= "</td>
			</tr>";
			
				$out .= $this->get_captcha();
			
			$out .= "<tr>
				<td></td>
				<td><input type='submit' name='add_post_submit' value='".__("Submit", "vasthtml")."' /></td>
				<input type='hidden' name='add_post_forumid' value='".$this->check_parms($thread)."'/>
				<input type='hidden' name='add_topic_plink' value='".get_permalink($this->page_id)."'/>

			</tr>

			</table></form>";
		$this->o .= $out;
	}


if(($_GET['vasthtmlaction'] == "editpost")){

	$this->current_view = EDITPOST;

	$id = $_GET['id'];
	$thread = $this->check_parms($_GET['t']);

		$out .= $this->header();

	$post = $wpdb->get_row("SELECT * FROM $vasthtml->t_posts WHERE id = $id");
	
	$out .= "<form action='".WPFURL."wpf-insert.php' name='addform' method='post'>";
	$out .= "<table class='wpf-table' width='100%'>
			<tr>
				<th colspan='2'>".__("Edit Post", "vasthtml")."</th>
			</tr>
			<tr>	
				<td>".__("Subject:", "vasthtml")."</tf>
				<td><input type='text' name='edit_post_subject' value='".stripslashes($post->subject)."'/></td>
			</tr>
			<tr>	
				<td valign='top'>".__("Message:", "vasthtml")."</td>
				<td>";
						$out .= $vasthtml->form_buttons();

					$out .= "<br /><textarea ".ROW_COL." name='message' >".stripslashes($post->text)."</textarea>";
				$out .= "</td>
			</tr>
			<tr>
				<td></td>
				<td><input type='submit' name='edit_post_submit' value='".__("Save Post", "vasthtml")."' /></td>
				<input type='hidden' name='edit_post_id' value='".$post->id."'/>
				<input type='hidden' name='thread_id' value='".$thread."'/>
				<input type='hidden' name='add_topic_plink' value='".get_permalink($this->page_id)."'/>

			</tr>

			</table></form>";
		$this->o .= $out;
	}
}


























	else
		wp_die(__("Sorry. you don't have permission to post.", "vasthtml"));

?>

