<?php
include("wpf_define.php");
require_once( 'bbcode.php' );
//require_once( 'BBCodeParser/BBCodeParser.php' );
@ob_start();

if(!class_exists('vasthtml')){
class vasthtml{

	function vasthtml(){
		add_action("admin_menu", array(&$this,"add_admin_pages"));
		add_action("admin_head", array(&$this, "admin_header"));
		add_action("wp_head", array(&$this, "setup_header"));
		add_action("plugins_loaded", array(&$this, "wpf_load_widget"));
		add_action("wp_footer", array(&$this, "wpf_footer"));
		
		$this->init(); 
		
	}
	
	// !Member variables
	var $table_prefix 	= "";
	var $page_id		= "";
	var $path 			= "";
	var $reg_link 		= "";
	var $login_link 	= "";
	var $profile_link 	= "";
	var $logout_link 	= "";
	var $home_url 		= "";
	var $forum_link		= "";
	var $group_link		= "";
	var $thread_link	= "";

	var $profil_link 	= "";
	var $search_link	= "";
	var $add_topic_link = "";
	// DB tables
	var $t_groups 	= "";
	var $t_forums 	= "";
	var $t_threads 	= "";
	var $t_posts 	= "";
	var $t_captcha 	= "";
	var $_usergroups = "";
	var $t_usergroup2user = "";
	var $o = "";
	
	var $current_group = "";
	var $current_forum = "";
	var $current_thread = "";
	var $notify_msg = "";
	var $current_view = "";
	var $opt = array();
	var $base_url = "";
	var $skin_url = "";
	var $curr_page = "";
	var $last_visit = "";
	var $user_options = array();
	
	// Initialize varables
	function init(){
		global $table_prefix, $user_ID;
		
		$this->page_id			= $this->get_pageid();
		$this->path 			= get_bloginfo('siteurl');
		$this->reg_link 		= $this->path."/wp-register.php?redirect_to=";
		$this->login_link 		= $this->path."/wp-login.php?redirect_to=".PHP_SELF."";
		$this->profile_link 	= $this->path."/wp-admin/profile.php";
		
		$this->thread_link		= $this->path."/?page_id=$this->page_id&amp;vasthtmlaction=showthread&thread";
		
		$this->profil_link 		= $this->path."/?page_id=$this->page_id&amp;vasthtmlaction=showprofile&amp;user";
		$this->search_link		= $this->path."/?page_id=$this->page_id&amp;vasthtmlaction=search&";
		$this->grouplogin_link	= $this->path."/?page_id=$this->page_id&amp;vasthtmlaction=grouplogin&group";
		
		$this->t_groups 		= $table_prefix."forum_groups";
		$this->t_forums 		= $table_prefix."forum_forums";
		$this->t_threads 		= $table_prefix."forum_threads";
		$this->t_posts 			= $table_prefix."forum_posts";
		$this->t_captcha 		= $table_prefix."forum_captcha";
		$this->t_usergroups 	= $table_prefix."forum_usergroups";//! check this later
		$this->t_usergroup2user = $table_prefix."forum_usergroup2user"; //x testing
		
		$this->current_forum 	= false;
		$this->current_group 	= false;
		$this->current_thread 	= false;
		
		$this->curr_page 		= 0;			
		

		// !Forum options
		$this->options = array( 'forum_posts_per_page' 			=> 10,
								'forum_threads_per_page' 		=> 20, 
								'forum_require_registration' 	=> true,
								'forum_date_format' 			=> "F j, Y, H:i",
								'forum_use_gravatar' 			=> true,
								'forum_skin'					=> "default",
								'forum_allow_post_in_solved' 	=> true,
								'set_sort' 						=> "DESC",
								'forum_use_spam' 				=> false,
								'forum_use_bbcode' 				=> true,
								'forum_captcha' 				=> true,
								'hot_topic'						=> 15,
								'veryhot_topic'					=> 25
								); 
						
		$this->user_options = array(
									'allow_profile' => true,
									'notify' 		=> true,
									'notify_topics' => ""
									);
								

		// No options yet?	
		add_option('vasthtml_options', $this->options);
		
		// Get the options
		$this->opt = get_option('vasthtml_options');
		$this->skin_url = WPFURL."skins/".$this->opt['forum_skin'];				
	}
	
	// Add admin pages
	function add_admin_pages(){
		add_menu_page('Forum Server', 'Forum Server', 8, 'forum-server/fs-admin/fs-admin.php', '', WPFURL."images/logo.png");	
		add_submenu_page('forum-server/fs-admin/fs-admin.php', 'Skins', 'Skins', 8,"admin.php?page=forum-server/fs-admin/fs-admin.php&amp;vasthtml_action=skins"); 
		add_submenu_page('forum-server/fs-admin/fs-admin.php', 'Categories & Forums', 'Categories & Forums', 8, "admin.php?page=forum-server/fs-admin/fs-admin.php&amp;vasthtml_action=structure");
		add_submenu_page('forum-server/fs-admin/fs-admin.php', 'Moderators', 'Moderators', 8, "admin.php?page=forum-server/fs-admin/fs-admin.php&amp;vasthtml_action=moderators");
		add_submenu_page('forum-server/fs-admin/fs-admin.php', 'User Groups', 'User Groups', 8, "admin.php?page=forum-server/fs-admin/fs-admin.php&amp;vasthtml_action=usergroups");
		add_submenu_page('forum-server/fs-admin/fs-admin.php', 'About', 'About', 8, "admin.php?page=forum-server/fs-admin/fs-admin.php&amp;vasthtml_action=about");
	}
	
	// ... and some styling and meta
	function admin_header(){
		echo "<link rel='stylesheet' href='".get_bloginfo('wpurl')."/wp-content/plugins/".WPFPLUGIN."/wpf_admin.css' type='text/css' media='screen'  />"; 
		?><script language="JavaScript" type="text/javascript" src="<?php echo WPFURL."js/script.js"?>"></script><?php

	}
	
	function wpf_load_widget() {
		if (!function_exists('register_sidebar_widget')) {
			return;
		}
		
		//$widget_ops = array('classname' => 'widget_fs_vasthtml', 'description' => __( "Display latest activity in the forum") );
		
		register_sidebar_widget(__("Forums Latest Activity", "vasthtml"), array(&$this, "widget"));
    	register_widget_control("Forums Latest Activity", array(&$this, "widget_wpf_control"));

	}
	function widget($args){
		global $wpdb;
		$this->setup_links();
		$widget_option = get_option("wpf_widget");
		
		$posts = $wpdb->get_results("SELECT * FROM $this->t_posts ORDER BY `date` DESC LIMIT ".$widget_option["wpf_num"]);
		echo $args['before_widget'];
		echo $args['before_title'] . $widget_option["wpf_title"] . $args['after_title'];
		
		echo "<ul>";
		foreach($posts as $post){
			$user = get_userdata($post->author_id);
			echo "<li><a href='".$this->thread_link."$post->parent_id.0'>".$this->output_filter($post->subject)."</a> ".__("by:", "vasthtml")." ".$this->profile_link($post->author_id)."<br /><small>".$this->format_date($post->date)."</small></li>";
		}
		echo "</ul>";
		echo $args['after_widget'];
	}
	
	function latest_activity($num = 5, $ul = true){
		global $wpdb;
		$posts = $wpdb->get_results("SELECT * FROM $this->t_posts ORDER BY `date` DESC LIMIT $num");
		if($ul) echo "<ul class='forumtwo'>";
		foreach($posts as $post){
			$user = get_userdata($post->author_id);
			echo "<li class='forum'><a href='".$this->thread_link."$post->parent_id.0'>".$this->output_filter($post->subject)."</a><br /> ".__("", "vasthtml")." ".$this->profile_link($post->author_id)." <small>".$this->format_date($post->date)."</small></li>";
		}
		if($ul)echo "</ul>";
	}
	
	function widget_wpf_control(){
		if ( $_POST["wpf_submit"] ) {
		
    		$name = strip_tags(stripslashes($_POST["wpf_title"]));
    		$num = strip_tags(stripslashes($_POST["wpf_num"]));
    		
    		$widget_option["wpf_title"] = $name;
			$widget_option["wpf_num"] = $num;
    		update_option("wpf_widget", $widget_option);
 		}
 			$widget_option = get_option("wpf_widget");
 			
		echo "<p><label for='wpf_title'>".__("Title to display in the sidebar:", "vasthtml")."
				<input style='width: 250px;' id='wpf_title' name='wpf_title' type='text' value='{$widget_option['wpf_title']}' /></label></p>";
			
			
		echo "<p><label for='wpf_num'>".__("How many items would you like to display?", "vasthtml");
		echo "<select name='wpf_num'>";
		for($i = 1; $i < 21; ++$i){
			if($widget_option["wpf_num"] == $i)
				$selected = "selected = 'selected'";
			else
				$selected = "";
			echo "<option value='$i' $selected>$i</option>";
		}
		echo "</select>";
			echo "</label></p>
				<input type='hidden' id='wpf_submit' name='wpf_submit' value='1' />";
	}
	
	function wpf_footer(){?>
		<script type="text/javascript" >	
			
			<?php echo "var skinurl = '$this->skin_url';";?>
			fold();
		function notify(){
				
			var answer = confirm ('<?php echo $this->notify_msg;?>');
			if (!answer)
				return false;
			else
				return true;
		}

		</script>
	<?php  } 
	
	function setup_links(){
	global $wp_rewrite;
		if($wp_rewrite->using_permalinks())
			$delim = "?";
		else
			$delim = "&amp;";
		$perm = get_permalink($this->page_id);
		
		$this->forum_link 		= $perm.$delim."vasthtmlaction=viewforum&amp;f=";
		$this->group_link 		= $perm.$delim."vasthtmlaction=vforum&amp;g=";
		$this->thread_link 		= $perm.$delim."vasthtmlaction=viewtopic&amp;t=";
		$this->add_topic_link 	= $perm.$delim."vasthtmlaction=addtopic&amp;forum=$this->current_forum";
		$this->post_reply_link 	= $perm.$delim."vasthtmlaction=postreply&amp;thread=$this->current_thread";
		$this->base_url			= $perm.$delim."vasthtmlaction=";
		$this->reg_link 		= $this->path."/wp-register.php?redirect_to=";
		$this->topic_feed_url	= WPFURL."feed.php?topic=";
		$this->global_feed_url	= WPFURL."feed.php?topic=all";
		$this->home_url 		= $perm;
		$this->logout_link 		= $this->path."/wp-login.php?action=logout";

	}
	function setup_linksdk($perm){
	global $wp_rewrite;
		if($wp_rewrite->using_permalinks())
			$delim = "?";
		else
			$delim = "&amp;";
		
		
		$this->forum_link 		= $perm.$delim."vasthtmlaction=viewforum&amp;f=";
		$this->group_link 		= $perm.$delim."vasthtmlaction=vforum&amp;g=";
		$this->thread_link 		= $perm.$delim."vasthtmlaction=viewtopic&amp;t=";
		$this->add_topic_link 	= $perm.$delim."vasthtmlaction=addtopic&amp;forum=$this->current_forum";
		$this->post_reply_link 	= $perm.$delim."vasthtmlaction=postreply&amp;thread=$this->current_thread";
		$this->base_url			= $perm.$delim."vasthtmlaction=";
		$this->reg_link 		= $this->path."/wp-register.php?redirect_to=";
		$this->topic_feed_url	= WPFURL."feed.php?topic=";
		$this->global_feed_url	= WPFURL."feed.php?topic=all";
		$this->home_url 		= $perm;
		$this->logout_link 		= $this->path."/wp-login.php?action=logout";

	}
	function get_addtopic_link(){
		return $this->add_topic_link.".$this->curr_page";
	}
		function get_post_reply_link(){
		return $this->post_reply_link.".$this->curr_page";
	}
	function get_forumlink($id){
		return $this->forum_link.$id.".$this->curr_page";
	}
	function get_grouplink($id){
		return $this->group_link.$id.".$this->curr_page";
	}
	function get_threadlink($id){
		return $this->thread_link.$id.".$this->curr_page";
	}
	function get_pageid(){
		global $wpdb;
		return $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_content  LIKE '%<!--VASTHTML-->%' AND post_status = 'publish' AND post_type = 'page'");
	}
	function get_groups($id = ''){
		global $wpdb;
		$cond = "";
		if($id)
			$cond = "WHERE id = $id";
		return $wpdb->get_results("SELECT * FROM $this->t_groups $cond ORDER BY sort ".SORT_ORDER); 
	}
	function get_forums($id = ''){
		global $wpdb;
		if($id){
			$forums = $wpdb->get_results("SELECT * FROM $this->t_forums WHERE parent_id = $id ORDER BY SORT ".SORT_ORDER);
			return $forums;
		}
		else 
			return $wpdb->get_results("SELECT * FROM $this->t_forums ORDER BY sort ".SORT_ORDER);
	}
	function get_threads($id = ''){
		global $wpdb;

		$start = $this->curr_page*$this->opt['forum_posts_per_page'];
		$end = $this->opt['forum_posts_per_page'];
		$limit = "$start, $end";

		if($id){
			$threads = $wpdb->get_results("SELECT * FROM $this->t_threads WHERE parent_id = $id AND status='open' ORDER BY last_post ".SORT_ORDER." LIMIT $limit");
			return $threads;
		}
		else
			return $wpdb->get_results("SELECT * FROM $this->t_threads ORDER BY `date` ".SORT_ORDER);
	}
	
	function get_sticky_threads($id){
		global $wpdb;

		if($id){
			$threads = $wpdb->get_results("SELECT * FROM $this->t_threads WHERE parent_id = $id AND status='sticky' ORDER BY last_post ".SORT_ORDER);
			return $threads;
		}
	}

	function get_posts($thread_id){
		global $wpdb;
		
		$start = $this->curr_page*$this->opt['forum_posts_per_page'];
		$end = $this->opt['forum_posts_per_page'];
		$limit = "$start, $end";

		//print_r($limit);
		if($thread_id){
			$posts = $wpdb->get_results("SELECT * FROM $this->t_posts WHERE parent_id = $thread_id ORDER BY `date` ASC LIMIT $limit");
			return $posts;
		}else{
			//return $wpdb->get_results("SELECT * FROM $this->t_posts ORDER BY `date` ".SORT_ORDER);
			return false;
		}
	}

	function get_groupname($id){
		global $wpdb;
		return $this->output_filter($wpdb->get_var("SELECT name FROM $this->t_groups WHERE id = $id"));
	}
	function get_forumname($id){
		global $wpdb;
		return $this->output_filter($wpdb->get_var("SELECT name FROM $this->t_forums WHERE id = $id"));
	}
	function get_threadname($id){
		global $wpdb;
		return $this->output_filter($wpdb->get_var("SELECT subject FROM $this->t_threads WHERE id = $id"));
	}
	function get_postname($id){
		global $wpdb;
		return $this->output_filter($wpdb->get_var("SELECT subject FROM $this->t_posts WHERE id = $id"));

	}

	function get_group_description($id){
		global $wpdb;
		return $wpdb->get_var("SELECT description FROM $this->t_groups WHERE id = $id");
	}
	function get_forum_description($id){
		global $wpdb;
		return $wpdb->get_var("SELECT description FROM $this->t_forums WHERE id = $id");
	}

	function current_group(){
		return $this->current_group;
	}
	function current_forum(){
		return $this->current_forum;
	}
	function current_thread(){
		return $this->current_thread;
	}
	function check_parms($parm){
		//if (!preg_match("/^[0-9]{1,20}$/", $parm)) 
		$regexp = "/^([+-]?((([0-9]+(\.)?)|([0-9]*\.[0-9]+))([eE][+-]?[0-9]+)?))$/";
		if (!preg_match($regexp, $parm)){
			@ob_end_clean();
			wp_die("Bad request, please re-enter.");
		}
			
		$p = explode(".", $parm);
		
		$this->curr_page = $p[1];
		return $p[0];
	}
	
	function go($content){
		$start_time = microtime(true);

		global $user_ID;
		if(!preg_match('|<!--VASTHTML-->|', $content))	
			return $content;
		get_currentuserinfo();
		if($user_ID){
			if(get_usermeta($user_ID, 'wpf_useroptions') == ''){
				update_usermeta($user_ID, 'wpf_useroptions', $this->user_options);
			}
		}


		$action = $_GET['vasthtmlaction'];
		
		if($action){
			switch($action){
				case 'viewforum': 
						$this->current_view = FORUM;
						$this->showforum($this->check_parms($_GET['f']));break;
				case 'viewtopic': 
						$this->current_view = THREAD;
						$this->showthread($this->check_parms($_GET['t']));break;
				case 'addtopic': include(WPFPATH.'wpf-thread.php');break;
				case 'postreply':
					if($this->is_closed($_GET['thread'])){
						@ob_end_clean();
						wp_die(__("Cheating, are we?", "vasthtml"));
					}else{
						include(WPFPATH.'wpf-post.php');
					}
					break;
				case 'shownew' : $this->show_new(); break;
				case 'editpost' : include(WPFPATH.'wpf-post.php');break;
				case 'profile' : $this->view_profile(); break;
				case 'search' : $this->search_results(); break;
				case 'editprofile' : include(WPFPATH.'wpf-edit-profile.php');break;
				case 'vforum' : $this->vforum($this->check_parms($_GET['g']));break;
				
			}
		}
		else{
			$this->current_view = MAIN;
			$this->mydefault();
		}
		
		$end_time = microtime(true);
		$load =  __("Page loaded in:", "vasthtml")." ".round($end_time-$start_time, 3)." ".__("seconds.", "vasthtml")."";

		$this->o .= "<div id='wpf-info'><small>
			".__("WP Forum Server by ", "vasthtml")."<a href='http://www.vasthtml.com'>VastHTML</a> | <a href='http://www.lucidcrew.com' title='austin website design'>LucidCrew</a> <br /> 
			".__("Version:", "vasthtml").$this->get_version()."; 
			$load</small>
		</div>";
		
		return preg_replace('|<!--VASTHTML-->|', "<div id='wpf-wrapper'>".$this->o."</div>", $content);

	}
	function get_version(){
	$plugin_data = implode('', file(ABSPATH."wp-content/plugins/".WPFPLUGIN."/wpf-main.php"));
	if (preg_match("|Version:(.*)|i", $plugin_data, $version)) {
		$version = $version[1];
	}
	return $version;
}

	function get_userdata($user_id, $data){
		global $wpdb;
		
		$user = get_userdata($user_id);
		if(!$user)
			return __("Guest", "vasthtml");
			
		return $user->$data;
	}
	
	function get_lastpost($thread_id){
		global $wpdb;
		$post = $wpdb->get_row("select `date`, author_id, id from $this->t_posts where parent_id = $thread_id order by `date` DESC limit 1");
		
		return __("Latest Post by", "vasthtml")." ".$this->profile_link($post->author_id)."<br />".__("on", "vasthtml")." ".date($this->opt['forum_date_format'], strtotime($post->date));
	}
	function get_lastpost_all(){
		global $wpdb;
		$post = $wpdb->get_row("select `date`, author_id, id from $this->t_posts order by `date` DESC limit 1");
		
		return __("Latest Post by", "vasthtml")." ".$this->profile_link($post->author_id)."<br />".__("on", "vasthtml")." ".date($this->opt['forum_date_format'], strtotime($post->date));
	}

	function showforum($forum_id){
		global $user_ID, $wpdb;
		
		if(isset($_GET['delete_topic']))
			$this->remove_topic();

		if(isset($_GET['move_topic']))
			$this->move_topic();

		if(!empty($forum_id)){

			$threads = $this->get_threads($forum_id);
			$sticky_threads = $this->get_sticky_threads($forum_id);

			$t = $sticky_threads + $threads;
			
			$this->current_group = $this->get_parent_id(FORUM, $forum_id);
			$this->current_forum = $forum_id;
					

			$this->header();
			
			if(isset($_GET['getNewForumID'])){
				$out .= $this->getNewForumID();
			}else{
				if(!$this->have_access($this->current_group)){
					@ob_end_clean();
					wp_die(__("Sorry, but you don't have access to this forum", "vasthtml"));
				}
				
				$out .= "<table cellpadding='0' cellspacing='0'>
							<tr>
								<td width='100%'>".$this->thread_pageing($forum_id)."</td>
								<td>".$this->forum_menu($this->current_group)."</td>
							</tr>
						</table>";
				$out .= "<div class='wpf'><table class='wpf-table' id='topicTable'>
								<tr>
									<th width='6%' class='forumIcon'></th>
									<th>".__("Topic", "vasthtml")."</th>
									<th width='11%' nowrap='nowrap'>".__("Started by", "vasthtml")."</th>
									<th width='4%'>".__("Replies", "vasthtml")."</th>
									<th width='4%'>".__("Views", "vasthtml")."</th>
									<th width='22%'>".__("Last post", "vasthtml")."</th>
								</tr>";
		/***************************************************************************************/
			if($sticky_threads){
				$out .= "<tr><th class='wpf-bright' colspan='6'>".__("Sticky Topics", "vasthtml")."</th></tr>";
				foreach($sticky_threads as $thread){
					
					if($this->is_moderator($user_ID, $this->current_forum)){
						$strCommands	= "<a href='".$this->get_forumlink($this->current_forum)."&getNewForumID&topic=$thread->id'>".__("Move Topic", "vasthtml")."</a> | <a href='".$this->get_forumlink($this->current_forum)."&delete_topic&topic=$thread->id'>".__("Delete Topic", "vasthtml")."</a>";
						$del			= "<small>($strCommands)</small>";
					}

					if($user_ID){
						$image = "";
						$poster_id = $this->last_posterid_thread($thread->id); // date and author_id
						if($user_ID != $poster_id){
							$lp = strtotime($this->last_poster_in_thread($thread->id)); // date
							$lv = $this->last_visit();
							if($lp > $lv)
								$image = "<img src='$this->skin_url/images/new.gif' alt='".__("New posts since last visit", "vasthtml")."'>";
						}
					}
					
					
					$sticky_img = "<img alt='' src='$this->skin_url/images/topic/normal_post_sticky.gif'/>";
					$out .= "<tr>
									<td class='forumIcon' align='center'>$sticky_img</td>
									<td class='wpf-alt sticky'><span class='topicTitle'><a href='"
										.$this->get_threadlink($thread->id)."'>"
										.$this->output_filter($thread->subject)."</a>".$this->get_pagelinks($thread->id)."&nbsp;&nbsp;$image</span> $del
									</td>
									<td>".$this->profile_link($thread->starter)."</td>
									<td class='wpf-alt $sticky' align='center'>".$this->num_posts($thread->id)."</td>
									<td class='wpf-alt $sticky' align='center'>".$thread->views."</td>
									<td><small>".$this->get_lastpost($thread->id)."</small></td>
								</tr>";
					}
		/********************************************************************************************************/						
								
				$out .= "<tr><th class='wpf-bright forumTopics' colspan='6'>".__("Forum Topics", "vasthtml")."</th></tr>";
				}
				foreach($threads as $thread){
					$alt=($alt=="alt even")?"odd":"alt even";
					if($user_ID){
					$image = "";
						$poster_id = $this->last_posterid_thread($thread->id); // date and author_id
						if($user_ID != $poster_id){
							$lp = strtotime($this->last_poster_in_thread($thread->id)); // date
							$lv = $this->last_visit();
							if($lp > $lv)
								$image = "<img src='$this->skin_url/images/new.gif' alt='".__("New posts since last visit", "vasthtml")."'>";
						}
					}

					if($this->is_moderator($user_ID, $this->current_forum)){
						$strCommands	= "<a href='".$this->get_forumlink($this->current_forum)."&getNewForumID&topic=$thread->id'>".__("Move Topic", "vasthtml")."</a> | <a href='".$this->get_forumlink($this->current_forum)."&delete_topic&topic=$thread->id'>".__("Delete Topic", "vasthtml")."</a>";
						$del			= "<small class='adminActions'>$strCommands</small>";
					}
					$out .= "<tr class='$alt'>
									<td class='forumIcon' align='center'>".$this->get_topic_image($thread->id)."</td>
									<td class='wpf-alt'><span class='topicTitle'><a href='"
										.$this->get_threadlink($thread->id)."'>"
										.$this->output_filter($thread->subject)."</a>".$this->get_pagelinks($thread->id)."&nbsp;&nbsp;$image</span> $del
									</td>
									
									<td>".$this->profile_link($thread->starter)."</td>
									<td class='wpf-alt $sticky' align='center'>".$this->num_posts($thread->id)."</td>
									<td class='wpf-alt $sticky' align='center'>".$thread->views."</td>
									<td><small>".$this->get_lastpost($thread->id)."</small></td>
								</tr>";
					}
					$out .= "</table></div>";
				$out .= "<table cellpadding='0' cellspacing='0'>
							<tr>
								<td width='100%'>".$this->thread_pageing($forum_id)."</td>
								<td>".$this->forum_menu($this->current_group, "bottom")."</td>
							</tr>
						</table>";

			}
			$this->o .= $out;
			$this->footer();
		}
	}
	function get_subject($id){
		global $wpdb;
		return $this->output_filter($wpdb->get_var("SELECT subject FROM $this->t_threads WHERE id = $id"));
	}

	function showthread($thread_id){
			
		global $wpdb, $user_ID;

		$this->current_group = $this->forum_get_group_from_post($thread_id);
		$this->current_forum = $this->get_parent_id(THREAD, $thread_id);
		$this->current_thread = $thread_id;
		$this->header();
		
		if(isset($_GET['remove_post']))
			$this->remove_post();

		if(isset($_GET['sticky']))
			$this->sticky_post();
			
		if(isset($_GET['notify']))
			$this->notify_post();
			
		if($posts = $this->get_posts($thread_id)){


			if($user_ID){
				$op = get_usermeta($user_ID, "wpf_useroptions");
				if($this->array_search($this->current_thread, (array)$op["notify_topics"], true))
					$this->notify_msg = __("Remove this topic from your email notifications?", "vasthtml");
				else
					$this->notify_msg = __("Add this topic to your email notifications?", "vasthtml");
			}
				
			$wpdb->query("UPDATE $this->t_threads SET views = views+1 WHERE id = $thread_id");
			if($this->is_sticky())
				$image = "normal_post_sticky.gif";
			else
				$image = "normal_post.gif";
				
			if(!$this->have_access($this->current_group)){
				@ob_end_clean();
				wp_die(__("Sorry, but you don't have access to this forum", "vasthtml"));
			}

			$out .= "<table cellpadding='0' cellspacing='0'>
						<tr>
							<td width='100%'>".$this->post_pageing($thread_id)."</td>
							<td>".$this->topic_menu($thread_id)."
							</td>
						</tr>
					</table>";
			
			
			$out .= "<div class='wpf'>
						<table class='wpf-table' width='100%'>
						<tr>
							<th width='12%'><img src='$this->skin_url/images/topic/$image' align='left'/> ".__("Author", "vasthtml")."</th>
							<th>".__("Topic: ", "vasthtml").$this->get_subject($thread_id)."</th>
						</tr>
					</table>";
			$out .= "</div>";

			foreach($posts as $post){
					$class = ($class == "wpf-alt")?"":"wpf-alt";
				$user = get_userdata($post->author_id);
				$out .= "<table class='wpf-post-table' width='100%' id='postid-$post->id'>
						<tr class='$class'>
							<td valign='top' width='12%'>".
								$this->profile_link($post->author_id)."
								<div class='wpf-small'>";
									if($post->author_id != 0){
										$out .= $this->get_userrole($post->author_id)."<br />";
										$out .=__("Posts:", "vasthtml")." ".$this->get_userposts_num($post->author_id)."<br />";
										
										if($this->opt["forum_use_gravatar"])
											$out .= $this->get_avatar($post->author_id);
									}
								
							$out .= "</div></td>

							<td valign='top'>
								<table width='100%' cellspacing='0' cellpadding='0' class='wpf-meta-table' >
									<tr>
										<td class='wpf-meta' valign='top'>".$this->get_postmeta($post->id, $post->author_id)."</td>
									</tr>
									<tr>
										<td valign='top' colspan='2' class='topic_text'>".apply_filters('comment_text', $this->output_filter($post->text))."</td>
									</tr>";
									if($user->description){
										$out .= "<tr><td class='user_desc'><small>".apply_filters('comment_text', $this->output_filter($user->description))."</small></td></tr>";
									}
								$out .= "</table>
							</td>
						</tr>";
				$out .= "</table>";
			}
			$out .= "<table cellpadding='0' cellspacing='0'>
						<tr>
							<td width='100%'>".$this->post_pageing($thread_id)."</td>
							<td>".$this->topic_menu($thread_id, "bottom")."
							</td>
						</tr>
					</table>";
			
			$this->o .= $out;
			$this->footer();
		}
	}

	function get_postmeta($post_id, $author_id){
	global $user_ID;
		$image = "<img align='left' src='$this->skin_url/images/post/xx.gif' alt='".__("Post", "vasthtml")."' style='padding-right:10px;'/>";
		$o = "<table width='100% cellspacing='0' cellpadding='0' style='margin:0; padding:0; border-collapse:collapse:' border='0'>
				<tr>
					<td>$image <strong>".$this->get_postname($post_id)."</strong><br /><small><strong>on: </strong>".$this->get_postdate($post_id)."</small></td>";
					
					if(is_user_logged_in())
						 $o .= "<td nowrap='nowrap' width='10%'><img src='$this->skin_url/images/buttons/quote.gif' alt='' align='left'><a href='$this->post_reply_link&amp;quote=$post_id.$this->curr_page'> ".__("Quote", "vasthtml")."</a></td>";
						
					if($this->is_moderator($user_ID, $this->current_forum) || $user_ID == $author_id)
						 $o .= "<td nowrap='nowrap' width='10%'><img src='$this->skin_url/images/buttons/delete.gif' alt='' align='left'><a onclick=\"return wpf_confirm();\" href='".$this->get_threadlink($this->current_thread)."&amp;remove_post&amp;id=$post_id'> ".__("Remove", "vasthtml")."</a></td>
								<td nowrap='nowrap' width='10%'><img src='$this->skin_url/images/buttons/modify.gif' alt='' align='left'><a href='".$this->base_url."editpost&amp;id=$post_id&amp;t=$this->current_thread.0'>" .__("Edit", "vasthtml")."</a></td>";
				$o .= "</tr>
			</table>";
		
		return $o;
	}
	function get_postdate($post){
		global $wpdb;
		return $this->format_date($wpdb->get_var("select `date` from $this->t_posts where id = $post"));
	}
	function format_date($date){
		if($date)
			return date($this->opt['forum_date_format'], strtotime($date));
		else
			return false;
	}
	function get_userposts_num($id){
		global $wpdb;
		return $wpdb->get_var("select count(*) from $this->t_posts where author_id = $id");
	}
	function mydefault(){
		global $user_ID;

//<a name='$g->id' href='http://mac/smf/index.php?action=collapse;c=1;sa=collapse;#1'>General Category</a>"
				
				
		$grs = $this->get_groups();

		$this->header();
		
		foreach($grs as $g){
			if($this->have_access($g->id)){
			

				$this->o .= "<div class='wpf'><table width='100%' class='wpf-table forumsList'>";
				$this->o .= "<tr><th colspan='4'><a href='".$this->get_grouplink($g->id)."'>".$this->output_filter($g->name)."</a></th></tr>";
				$frs = $this->get_forums($g->id);
				//if($frs)
					//$this->o .= "<tr>";
				foreach($frs as $f){
				$alt=($alt=="alt even")?"odd":"alt even";
					$this->o .= "<tr class='$alt'>";
					$image = "off.gif";
					if($user_ID){
					$lpif = $this->last_poster_in_forum($f->id, true);
						$last_posterid = $this->last_posterid($f->id);
						if($last_posterid != $user_ID){
							$lp = strtotime($lpif); // date
							$lv = $this->last_visit();
						
						if($lv < $lp)
							$image = "on.gif";
						else
							$image = "off.gif";
						}
					}
					$this->o .= "
							<td class='wpf-alt forumIcon' width='6%' align='center'><img alt='' src='$this->skin_url/images/$image' /></td>
							<td valign='top'><strong><a href='".$this->get_forumlink($f->id)."'>"
								.$this->output_filter($f->name)."</a></strong><br />"
								.$this->output_filter($f->description);
								if($f->description != "")$this->o .= "<br />";
								$this->o .= $this->get_forum_moderators($f->id)
							."</td>";
					
					$this->o .= "<td nowrap='nowrap' width='11%' align='left' class='wpf-alt'><small>".__("Topics: ", "vasthtml")."".$this->num_threads($f->id)."<br />".__("Posts: ", "vasthtml").$this->num_posts_forum($f->id)."</small></td>";
					
					$this->o .= "<td  width='28%' ><small>".$this->last_poster_in_forum($f->id)."</small></td>";
					$this->o .= "</tr>";
				}
			$this->o .= "</table>
				
			</div><br class='clear'/>";
			}
			
		}
		$this->o .= "<table>
					<tr>
						<td><small><img alt='' align='top' src='$this->skin_url/images/new_some.gif' /> ".__("New posts", "vasthtml")." <img alt='' align='top' src='$this->skin_url/images/new_none.gif' /> ".__("No new posts", "vasthtml")."</small></td>
					</tr>
				</table><br class='clear'/>";
		$this->footer();

	}
	function vforum($groupid){
		global $user_ID;

//<a name='$g->id' href='http://mac/smf/index.php?action=collapse;c=1;sa=collapse;#1'>General Category</a>"
				
				
		$grs = $this->get_groups($groupid);
		$this->current_group = $groupid;
		$this->header();
		
		foreach($grs as $g){
			if($this->have_access($g->id)){
			

				$this->o .= "<div class='wpf'><table width='100%' class='wpf-table'>";
				$this->o .= "<tr><th colspan='4'><a href='".$this->get_grouplink($g->id)."'>".$this->output_filter($g->name)."</a></th></tr>";
				$frs = $this->get_forums($g->id);
				//if($frs)
					//$this->o .= "<tr>";
				foreach($frs as $f){
				$alt=($alt=="alt even")?"odd":"alt even";
					$this->o .= "<tr class='$alt'>";
					$image = "off.gif";
					if($user_ID){
					$lpif = $this->last_poster_in_forum($f->id, true);
						$last_posterid = $this->last_posterid($f->id);
						if($last_posterid != $user_ID){
							$lp = strtotime($lpif); // date
							$lv = $this->last_visit();
						
						if($lv < $lp)
							$image = "on.gif";
						else
							$image = "off.gif";
						}
					}
					$this->o .= "
							<td class='wpf-alt forumIcon' width='6%' align='center'><img alt='' src='$this->skin_url/images/$image' /></td>
							<td valign='top'><strong><a href='".$this->get_forumlink($f->id)."'>"
								.$this->output_filter($f->name)."</a></strong><br />"
								.$this->output_filter($f->description);
								if($f->description != "")$this->o .= "<br />";
								$this->o .= $this->get_forum_moderators($f->id)
							."</td>";
					
					$this->o .= "<td nowrap='nowrap' width='11%' align='left' class='wpf-alt'><small>".__("Topics: ", "vasthtml")."".$this->num_threads($f->id)."<br />".__("Posts: ", "vasthtml").$this->num_posts_forum($f->id)."</small></td>";
					
					$this->o .= "<td  width='28%' ><small>".$this->last_poster_in_forum($f->id)."</small></td>";
					$this->o .= "</tr>";
				}
			$this->o .= "</table>
				
			</div><br class='clear'/>";
			}
			
		}
		$this->o .= "<table>
					<tr>
						<td><small><img alt='' align='top' src='$this->skin_url/images/new_some.gif' /> ".__("New posts", "vasthtml")." <img alt='' align='top' src='$this->skin_url/images/new_none.gif' /> ".__("No new posts", "vasthtml")."</small></td>
					</tr>
				</table><br class='clear'/>";
		$this->footer();

	}
	// TODO
	function output_filter($string){
	
		return stripslashes(PP_BBCode($string));
	}
	function input_filter($string){
		global $wpdb;
		return strip_tags($wpdb->escape($string));
	}
	function last_posterid($forum){
		global $wpdb;
		return $wpdb->get_var("SELECT $this->t_posts.author_id FROM $this->t_posts INNER JOIN $this->t_threads ON $this->t_posts.parent_id=$this->t_threads.id WHERE $this->t_threads.parent_id = $forum ORDER BY $this->t_posts.date DESC");

	}
	function last_posterid_thread($thread_id){
		global $wpdb;
		return $wpdb->get_var("SELECT $this->t_posts.author_id FROM $this->t_posts INNER JOIN $this->t_threads ON $this->t_posts.parent_id=$this->t_threads.id WHERE $this->t_posts.parent_id = $thread_id ORDER BY $this->t_posts.date DESC");
	}
	
	function num_threads($forum){
		global $wpdb;
		return $wpdb->get_var("select count(id) from $this->t_threads where parent_id = $forum");
	}
	
	function num_posts_forum($forum){
		global $wpdb;
		
		return $wpdb->get_var("SELECT count($this->t_posts.id) FROM $this->t_posts INNER JOIN $this->t_threads ON $this->t_posts.parent_id=$this->t_threads.id WHERE $this->t_threads.parent_id = $forum ORDER BY $this->t_posts.date DESC");

	}
	
	function num_posts_total(){
		global $wpdb;
		return $wpdb->get_var("select count(id) from $this->t_posts");
	}
	
	function num_posts($thread_id){
		global $wpdb;
		return $wpdb->get_var("select count(id) from $this->t_posts where parent_id = $thread_id");
	}

	function num_threads_total(){
		global $wpdb;
		return $wpdb->get_var("select count(id) from $this->t_threads");
	}
	
	function last_poster_in_forum($forum, $post_date = false){
		global  $wpdb, $table_posts, $profile, $table_threads;

		$date = $wpdb->get_row("SELECT $this->t_posts.date, $this->t_posts.id, $this->t_posts.parent_id, $this->t_posts.author_id FROM $this->t_posts INNER JOIN $this->t_threads ON $this->t_posts.parent_id=$this->t_threads.id WHERE $this->t_threads.parent_id = $forum ORDER BY $this->t_posts.date DESC");
		
		if($post_date)
			return $date->date;
		if(!$date)
			return __("No topics yet", "vasthtml");
		$user = $this->get_userdata($date->author_id, USER);
		$d =  date($this->opt['forum_date_format'], strtotime($date->date));
		
		return "<strong>".__("Last post", "vasthtml")."</strong> ".__("by", "vasthtml")." ".$this->profile_link($date->author_id)
		."<br />".__("in", "vasthtml")." <a href='".$this->get_threadlink($date->parent_id)."#postid-$date->id'>".$this->get_postname($date->id)."</a><br />".__("on", "vasthtml")." $d";
		
	}
	
	function last_poster_in_thread($thread_id){
		global $wpdb;
		return $wpdb->get_var("select `date` from $this->t_posts where parent_id = $thread_id order by `date` DESC");
	}
	
	function have_access($groupid){
		global $wpdb, $user_ID, $user_level;
		if($user_level > 8)
			return true;
		$user_groups = $wpdb->get_var("select usergroups from $this->t_groups where id = $groupid");
		$user_groups = maybe_unserialize($user_groups);

		if(!$user_groups)
			return true;
			
			foreach($user_groups as $user_group){
	 			if($this->is_user_ingroup($user_ID, $user_group))
	 				return true;
			}
		return false;
	}
	
	function get_usergroups(){
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM $this->t_usergroups");
		
	}
	
	function get_members($usergroup){
		global $wpdb, $table_prefix;
		return $wpdb->get_results("SELECT user_id FROM $this->t_usergroup2user WHERE `group` = $usergroup");
	}
	
	function is_user_ingroup($user_id = "0", $user_group_id){
		global $wpdb;
		if(!$user_id)
			return false;
		$id = $wpdb->get_var("select user_id from $this->t_usergroup2user where user_id = $user_id and `group` = $user_group_id");
		if($id != "")
			return true;
			
		return false;
	}
		
	
	// TODO
	function setup_header(){
		$this->setup_links();
		global $user_ID;
		
		?>
		<link rel='alternate' type='application/rss+xml' title="<?php echo __("Forums RSS", "vasthtml"); ?>" href="<?php echo $this->global_feed_url;?>" />
		<link rel='stylesheet' type='text/css' href="<?php echo "$this->skin_url/style.css";?>"  />
      
						
		
		<script language="JavaScript" type="text/javascript" src="<?php echo WPFURL."js/script.js"?>"></script>

	<script language="JavaScript" type="text/javascript">
		function wpf_confirm(){
			var answer = confirm ('<?php echo __("Remove this post?", "vasthtml");?>');
			if (!answer)
				return false;
			else
				return true;
		}

		</script> 


		
	<?php  }
	// Some SEO friendly stuff
	function get_pagetitle($bef_title){
	global $wpdb;
		$default_title = " &raquo; ";
				

		switch($_GET['vasthtmlaction']){
			case "viewforum": 
				$title = $default_title.$this->get_groupname($this->get_parent_id(FORUM, $this->check_parms($_GET['f'])))." &raquo; ".$this->get_forumname($this->check_parms($_GET['f']));
				break;
			case "viewtopic": 
				$group = $this->get_groupname($this->get_parent_id(FORUM, $this->get_parent_id(THREAD, $this->check_parms($_GET['t']))));
				$title = $default_title.$group." &raquo; ".$this->get_forumname($this->get_parent_id(THREAD, $this->check_parms($_GET['t'])))." &raquo; ".$this->get_threadname($this->check_parms($_GET['t']));
				break;
			case "search": 
				$terms = $wpdb->escape($_POST['wpf_search_string']);
				$title = $default_title.__("Search Results", "vasthtml"). " &raquo; $terms";
				break;
			case "profile": 
				$title = $default_title.__("Profile", "vasthtml")."";
				break;
			case "editpost": 
				$title = $default_title.__("Edit Post", "vasthtml")."";
				break;
			case "postreply": 
				$title = $default_title.__("Post Reply", "vasthtml")."";
				break;
			case "addtopic": 
				$title = $default_title.__("New Topic", "vasthtml")."";
				break;

			//default: $title = $default_title.__("View Categories", "vasthtml");

		}
		return $bef_title.$title;
	}
	
	function set_pagetitle($title){
		return $this->get_pagetitle($title);
	}
	function array_search( $needle, $haystack, $strict = FALSE ){
       	if( !is_array($haystack) )return false;
       		foreach($haystack as $key => $val){
           		if(   (  ( $strict ) && ( $needle === $val )  ) || (  ( !$strict ) && ( $needle == $val )  )   )return $val;
        		}
        return false;
	}
	
	function get_usergroup_name($usergroup_id){
		global $wpdb, $table_prefix;
		return $wpdb->get_var("SELECT name FROM $this->t_usergroups WHERE id = $usergroup_id");
	}
	
	function get_usergroup_description($usergroup_id){
		global $wpdb, $table_prefix;
		return $wpdb->get_var("SELECT description FROM $this->t_usergroups WHERE id = $usergroup_id");
	}
	
	function is_moderator($user_id, $forum_id = ''){
		$data = get_userdata($user_id);
	
		if($data->user_level > 8)
			return true;
		$forums = get_usermeta($user_id, 'wpf_moderator');
			
		if(!$forum_id)
			return $forums;
		if($forums == "mod_global")
			return true;
		return $this->array_search( $forum_id, $forums );
	}
	
	function get_users(){
		global $wpdb, $table_prefix;
		return $wpdb->get_results("SELECT user_login, ID FROM  $wpdb->users ORDER BY user_login ASC");	
	}
	
	function get_moderators(){
		global $wpdb, $table_prefix;
		
		return $wpdb->get_results("
						select $wpdb->usermeta.user_id, $wpdb->users.user_login 
						from 
						$wpdb->usermeta 
						inner join 
						$wpdb->users on $wpdb->usermeta.user_id = $wpdb->users.ID 
						where 
						$wpdb->usermeta.meta_key = 'wpf_moderator' ORDER BY $wpdb->users.user_login ASC"); // phew
	}
	
	function get_forum_moderators($forum_id){
		global $wpdb;
		$mods = $wpdb->get_results("SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = 'wpf_moderator'");

		//$this->pre($mods);
		foreach($mods as $mod){
			if($this->is_moderator($mod->user_id, $forum_id)){
				$out .= $this->profile_link($mod->user_id).", ";
			}
		}
		$out = substr($out, 0, strlen($out)-2);
		return "<small><i>".__("Moderators:", "vasthtml")." $out</i></small>";
	}
	
	function wp_forum_install(){
	
		global $table_prefix, $wpdb, $user_level, $wpforumadmin;
		$table_threads = $table_prefix."forum_threads";
		$table_posts = $table_prefix."forum_posts";
		$table_forums = $table_prefix."forum_forums";
		$table_groups = $table_prefix."forum_groups";	
		$table_captcha = $table_prefix."forum_captcha";	
		$table_usergroup2user = $table_prefix."forum_usergroup2user"; 
		$table_usergroups = $table_prefix."forum_usergroups"; 

		get_currentuserinfo();

			
			$sql1 = "
			CREATE TABLE ". $table_forums." (
			  id int(11) NOT NULL auto_increment,
			  `name` varchar(255) NOT NULL default '',
			  parent_id int(11) NOT NULL default '0',
			  description varchar(255) NOT NULL default '',
			  views int(11) NOT NULL default '0',
			  PRIMARY KEY  (id)
			);";
	
			$sql2 = "
			CREATE TABLE ". $table_groups." (
			  id int(11) NOT NULL auto_increment,
			  `name` varchar(255) NOT NULL default '',
			  `description` varchar(255) default '',
			  `usergroups` varchar(255) default '',
			  PRIMARY KEY  (id)
			);";
	
			$sql3 = "
			CREATE TABLE ". $table_posts." (
			  id int(11) NOT NULL auto_increment,
			  `text` longtext,
			  parent_id int(11) NOT NULL default '0',
			  `date` datetime NOT NULL default '0000-00-00 00:00:00',
			  author_id int(11) NOT NULL default '0',
			  `subject` varchar(255) NOT NULL default '',
			  views int(11) NOT NULL default '0',
			  PRIMARY KEY  (id)
			);";
	
	
			$sql4 = "
			CREATE TABLE ". $table_threads." (
			  id int(11) NOT NULL auto_increment,
			  parent_id int(11) NOT NULL default '0',
			  views int(11) NOT NULL default '0',
			  `subject` varchar(255) NOT NULL default '',
			  `date` datetime NOT NULL default '0000-00-00 00:00:00',
			  `status` varchar(20) NOT NULL default 'open',
			  starter int(11) NOT NULL,
			  PRIMARY KEY  (id)
			);";
			
			// 1.7.7
			/*$sql5 = "
			CREATE TABLE ". $table_captcha." (
			  id int(11) NOT NULL auto_increment,
			  `ip` varchar(20) NOT NULL default '',
			  `code` varchar(20) NOT NULL default '',
			  PRIMARY KEY  (id)
			);";*/
			// 2.0
			$sql6 = "
				CREATE TABLE ". $table_usergroup2user." (
			  `id` int(11) NOT NULL auto_increment,
			  `user_id` int(11) NOT NULL,
			  `group` varchar(255) NOT NULL,
			  PRIMARY KEY  (`id`)
			);";
			
			$sql7 = 
				"CREATE TABLE ". $table_usergroups." (
				  `id` int(11) NOT NULL auto_increment,
				  `name` varchar(255) NOT NULL,
				  `description` varchar(255) default NULL,
				  `leaders` varchar(255) default NULL,
				  PRIMARY KEY  (`id`)
				);";
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			dbDelta($sql1);
			dbDelta($sql2);
			dbDelta($sql3);
			dbDelta($sql4);
			//dbDelta($sql5);
			dbDelta($sql6);
			dbDelta($sql7);
	
			$xyquery1="ALTER TABLE ".$table_groups." ADD sort int( 11 ) NOT NULL;";
			$xyquery2="ALTER TABLE ".$table_forums." ADD sort int( 11 ) NOT NULL;";
			$xyquery3="ALTER TABLE ".$table_threads." ADD last_post datetime NOT NULL;";
			$xyquery4="ALTER TABLE ".$table_groups." ADD description varchar(255;)";
			
			$xyquery5="ALTER TABLE ".$table_groups." ADD usergroups varchar(255);";
			$xyquery6="ALTER TABLE ".$table_threads." CHANGE forum_id parent_id int(11);";
			$xyquery7="ALTER TABLE ".$table_posts." CHANGE thread_id parent_id int(11);";
			$xyquery8="ALTER TABLE '".$table_posts."' ADD FULLTEXT ( `text` );";
			
			// 1.7.3
			maybe_add_column($table_groups, sort, $xyquery1);
			maybe_add_column($table_forums, sort,$xyquery2);
			
			// 1.7.5
			maybe_add_column($table_threads, last_post, $xyquery3);
			
			// 2.0
			maybe_add_column($table_groups, description, $xyquery4);
			maybe_add_column($table_groups, usergroups, $xyquery5);
			maybe_add_column($table_groups, parent_id, $xyquery6);
			maybe_add_column($table_posts,  parent_id, $xyquery7);
			$wpdb->query($xyquery8);

			$this->convert_moderators();
			
		
	}
		
		function forum_menu($group, $pos = "top"){
			global $user_ID;
			if($user_ID || $this->allow_unreg()){	
				if($pos == "top")
					$class = "mirrortab";
				else
					$class= "maintab";
		
				$menu .= "<table cellpadding='0' cellspacing='0' style='margin-right:10px;' id='forummenu'>";
				$menu .= "<tr>
								<td class='".$class."_first'>&nbsp;</td>
								<td valign='top' class='".$class."_back' nowrap='nowrap'><a href='".$this->get_addtopic_link()."'>".__("New Topic", "vasthtml")."</a></td>
								<td valign='top' class='".$class."_last'>&nbsp;&nbsp;</td>
						</tr>
						</table>";
			}
			return $menu;
		}		
		
		function topic_menu($thread, $pos = "top"){
			global $user_ID;
			if($user_ID || $this->allow_unreg()){	
				if($pos == "top"){
					$class = "mirrortab";
				}else{
					$class = "maintab";
				}
				if($this->is_moderator($user_ID, $this->current_forum)){
					if($this->is_sticky()){
						$stick = "<td class='".$class."_back' nowrap='nowrap'><a href='".$this->get_threadlink($this->current_thread)."&amp;sticky&amp;id=$this->current_thread'>".__("Unmake Sticky", "vasthtml")."</a></td>";
					}else{
						$stick = "<td class='".$class."_back' nowrap='nowrap'><a href='".$this->get_threadlink($this->current_thread)."&amp;sticky&amp;id=$this->current_thread'>".__("Make sticky", "vasthtml")."</a></td>";
					}
					if($this->is_closed()){
						$closed = "<td class='".$class."_back' nowrap='nowrap'><a href='".$this->get_threadlink($this->current_thread)."&amp;closed=0&amp;id=$this->current_thread'>Re-open</a></td>";
					}else{
						$closed = "<td class='".$class."_back' nowrap='nowrap'><a href='".$this->get_threadlink($this->current_thread)."&amp;closed=1&amp;id=$this->current_thread'>Close</a></td>";
					}
				}
				$menu .= "<table cellpadding='0' cellspacing='0' style='margin-right:10px;' id='topicmenu'>";
				$menu .= "<tr><td class='".$class."_first'>&nbsp;</td>";
				if(!$this->is_closed()){
					$menu .= "<td valign='top' class='".$class."_back' nowrap='nowrap'><a href='".$this->get_post_reply_link()."'>".__("Reply", "vasthtml")."</a></td>";
				}
				$menu .= "<td class='".$class."_back' nowrap='nowrap'><a onclick='return notify();' href='".$this->get_threadlink($this->current_thread)."&amp;notify&amp;id=$this->current_thread'>".__("Notify", "vasthtml")."</a></td>
				<td class='".$class."_back' nowrap='nowrap'><a href='$this->topic_feed_url"."$this->current_thread'>".__("RSS feed", "vasthtml")."</a></td>
				".$stick.$closed."
				<td valign='top' class='".$class."_last'>&nbsp;&nbsp;</td>
				</tr></table>";
			}
			return $menu;
		}

		function setup_menu(){
			global $user_ID;
			$this->setup_links();

			if(isset($_GET['closed']))
				$this->closed_post();
			
			$link = "<a id='user_button' href='".$this->base_url."profile&amp;id=$user_ID' title='".__("My profile", "vasthtml")."'>".__("My Profile", "vasthtml")."</a>";

			$menuitems = array(	
							"home" 	    => "<a id='home_button' href='".$this->home_url."'>".__("Forum Home", "vasthtml")."</a>", 
							"logout" 	=> "<a href='$this->logout_link'>".__("Log out", "vasthtml")."</a>",
							"profile" 	=> $link,
							"search" 	=> "<a id='search_button' href='$this->base_url"."search'>".__("Search", "vasthtml")."</a>",
							"reply" 	=> "<a id='reply_button' href='".$this->get_post_reply_link()."'>".__("Reply", "vasthtml")."</a>",
							"new_topic" => "<a href='".$this->get_addtopic_link()."'>".__("New Topic", "vasthtml")."</a>",
							"feed" 		=> "<a id='rss_button' href='$this->topic_feed_url"."$this->current_thread'>".__("Feed", "vasthtml")."</a>",
							"sticky" 	=> "<a href='".$this->get_threadlink($this->current_thread)."&amp;sticky&amp;id=$this->current_thread'>".__("Make sticky", "vasthtml")."</a>",
							"unsticky" 	=> "<a href='".$this->get_threadlink($this->current_thread)."&amp;sticky&amp;id=$this->current_thread'>".__("Unmake sticky", "vasthtml")."</a>",
							"closed" 	=> "<a id='close_button' href='".$this->get_threadlink($this->current_thread)."&amp;closed=1&amp;id=$this->current_thread'>Close</a>",
							"unclosed" 	=> "<a href='".$this->get_threadlink($this->current_thread)."&amp;closed=0&amp;id=$this->current_thread'>Re-open</a>",
							"move" 		=> "<a href='".$this->get_forumlink($this->current_forum)."&getNewForumID&topic=$this->current_thread'>Move Topic</a>"
						);
				
				if($user_ID || $this->allow_unreg()){
				
				$menu = "<table cellpadding='0' cellspacing='5' id='mainmenu'><tr>";
				$logged = "";
					
$menu .= "<td valign='top' class='menu_sub'>{$menuitems['home']}</td>";
						if($user_ID)
							$menu .= "<td valign='top' class='menu_sub'>{$menuitems['profile']}</td>";
						$menu .= "<td valign='top' class='menu_sub'>{$menuitems['search']}</td>";
				
				switch($this->current_view){
					case FORUM: $menu .= "	<td valign='top' class='menu_sub'>{$menuitems['new_topic']}</td>
											";
						break;
					case THREAD:
											if(!$this->is_closed()){
												$menu .= "<td valign='top' class='menu_sub'>{$menuitems['reply']}</td>";
											}
											
											if($user_ID)
												$menu .= "<td valign='top' class='menu_sub'>{$menuitems['feed']}</td>";
											
											if($this->is_moderator($user_ID, $this->current_forum)){
												$menu .= "<td valign='top' class='menu_sub'>{$menuitems['move']}</td>";
												if($this->is_sticky()){
													$menu .= "<td valign='top' class='menu_sub'>{$menuitems['unsticky']}</td>";
												}else{
													$menu .= "<td valign='top' class='menu_sub'>{$menuitems['sticky']}</td>";
												}
												if($this->is_closed()){
													$menu .= "<td valign='top' class='menu_sub'>{$menuitems['unclosed']}</td>";
												}else{
													$menu .= "<td valign='top' class='menu_sub'>{$menuitems['closed']}</td>";
												}
											}
											

				}
				$menu .= "</tr></table>";
				}
				return $menu;

						
		}
	
		function convert_moderators(){
			global $wpdb, $table_prefix;
			if(!get_option('wpf_mod_option_vers')){
				$mods = $wpdb->get_results("SELECT user_id, user_login, meta_value FROM $wpdb->usermeta 
					INNER JOIN $wpdb->users ON $wpdb->usermeta.user_id=$wpdb->users.ID WHERE meta_key = 'moderator' AND meta_value <> ''");

				foreach($mods as $mod){
					$string = explode(",", substr_replace($mod->meta_value, "", 0, 1));
				
					update_usermeta($mod->user_id, 'wpf_moderator', maybe_serialize($string));
				}
				update_option('wpf_mod_option_vers', '2');	
			}		
		}
		
		function login_form(){
			global $user_ID;
			$user = get_userdata($user_ID);

			if(!is_user_logged_in()){
				return "<form action='".get_bloginfo('url')."/wp-login.php' method='post'>
					<p>
					<label for='log'>".__("Username: ", "vasthtml")."<input type='text' name='log' id='log' value='".wp_specialchars(stripslashes($user_login), 1)."' size='12' /> </label>
					<label for='pwd'>".__("Password: ", "vasthtml")."<input type='password' name='pwd' id='pwd' size='12' /> 
					<input type='submit' name='submit' value='Login' class='button' /></label>
					<label for='rememberme'><input name='rememberme' id='rememberme' type='checkbox' checked='checked' value='forever' /> ".__("Remember", "vasthtml")."</label>
					</p>
					<input type='hidden' name='redirect_to' value='".$_SERVER['REQUEST_URI']."'/>
				</form>";
			}
			else
				return "<p>You are logged in as $user->user_login</p>";
		}

		function pre($array){
			echo "<pre>";
			print_r($array);
			echo "</pre";
		}

		function print_curr(){
			$this->o .= "<p>Group: $this->current_group<br>
					Forum: $this->current_forum<br>
					Thread: $this->current_thread</p>";
		}
		function get_parent_id($type, $id){
			global $wpdb;
			switch($type){
				case FORUM:
					return $wpdb->get_var("select parent_id from $this->t_forums where id = $id"); 
					break;
				case THREAD:
					return $wpdb->get_var("select parent_id from $this->t_threads where id = $id"); 
					break;
			
			}
		}
		// TODO
		function get_userrole($user_id){
		
			$user = get_userdata($user_id);
			if($user->user_level > 8)
				return __("Administrator", "vasthtml");
			if(!$user_id)
				return __("Guest", "vasthtml");
			if($this->is_moderator($user_id, $this->current_forum))
				return __("Moderator", "vasthtml");
			else
				return __("Member", "vasthtml");
		}
		
/**************************************************/
function forum_get_group_id($group){
	global $wpdb, $table_groups;
	return $wpdb->get_var("SELECT id FROM $this->t_groups WHERE id = $group");
}
function forum_get_parent($forum){
	global $wpdb, $table_forums;
	return $wpdb->get_var("SELECT parent_id FROM $this->t_forums WHERE id = $forum");
}
function forum_get_forum_from_post($thread){
	global $wpdb, $table_threads;
	return $wpdb->get_var("SELECT parent_id FROM $this->t_threads WHERE id = $thread");
}
function forum_get_group_from_post($thread_id){
	
	return $this->forum_get_group_id($this->forum_get_parent($this->forum_get_forum_from_post($thread_id)));
}



/****************************************************/

	function trail(){
	global $wpdb;
		$this->setup_links();
		
		$trail = "<a href='".get_permalink($this->page_id)."'>Forum</a>";

		if($this->current_group)
			$trail .= " <strong>&raquo;</strong> <a href='$this->base_url"."vforum&amp;g=$this->current_group.0'>".$this->get_groupname($this->current_group)."</a>";

		if($this->current_forum)
			$trail .= " <strong>&raquo;</strong> <a href='$this->base_url"."viewforum&amp;f=$this->current_forum.0'>".$this->get_forumname($this->current_forum)."</a>";
			
		if($this->current_thread)
			$trail .= " <strong>&raquo;</strong> <a href='$this->base_url"."viewtopic&amp;t=$this->current_thread.$this->curr_page'>".$this->get_threadname($this->current_thread)."</a>";
		
		if($this->current_view == NEWTOPICS)
			$trail .= " <strong>&raquo;</strong> ".__("New Topics since last visit", "vasthtml");
			
		if($this->current_view == SEARCH){
			$terms = $wpdb->escape($_POST['wpf_search_string']);
			$trail .= " <strong>&raquo;</strong> ".__("Search Results", "vasthtml")." &raquo; $terms";
		}
			
		if($this->current_view == PROFILE)
			$trail .= " <strong>&raquo;</strong> ".__("Profile Info", "vasthtml");
			
		if($this->current_view == POSTREPLY)
			$trail .= " <strong>&raquo;</strong> ".__("Post Reply", "vasthtml");
			
		if($this->current_view == EDITPOST)
			$trail .= " <strong>&raquo;</strong> ".__("Edit Post", "vasthtml") ;
			
		if($this->current_view == NEWTOPIC)
			$trail .= " <strong>&raquo;</strong> ".__("New Topic", "vasthtml") ;

		return "<p id='trail' class='breadcrumbs'>$trail</p>";

	}
	

	function last_visit($format = ''){
		global $user_ID;
		
		if($format)	
			return @date($this->opt["forum_date_format"], get_usermeta($user_ID, "lastvisit"));
			
		return get_usermeta($user_ID, "lastvisit");
	}
	
	function set_cookie(){
		global $user_ID;
		if(!isset($_COOKIE['wpfsession'])){
			update_usermeta( $user_ID, 'lastvisit', time() );
		}		
		if($user_ID)
			setcookie("wpfsession", time(), 0, "/");
	}

	function get_avatar($user_id, $size = 60){
		
		if($this->opt['forum_use_gravatar'] == 'true')
			return get_avatar($user_id, 60);
		else
			return "";
	}
	
	
	function header(){
		global $user_ID, $user_login;
		$this->setup_links();
		if($user_ID){ 
			$welcome = __("Welcome", "vasthtml"). " <strong>$user_login</strong>";
			$meta .= "".__("<div style='float:left'>Your last visit was:", "vasthtml")." ".$this->last_visit(true)."<br />";
			$meta .= "<a href='".$this->base_url."shownew'>".__("Show new topics since your last visit.", "vasthtml")."</a><br />";
			//$meta .= "<a href='".wp_logout_url()."'>".__("Log out", "vasthtml")."</a>";
			$meta .= "<a href='".wp_nonce_url( site_url("wp-login.php?action=logout$redirect", 'login'), 'log-out' )."'>".__("Log out", "vasthtml")."</a></div>";
			$avatar = "<td class='wpf-alt' width='6%'>".$this->get_avatar($user_ID, 60)."</td>";
			$colspan = "colspan = '2'";

		}
		else{
			$meta = "".__("Welcome Guest, please login or", "vasthtml")." <a href='$this->reg_link'>".__("register.", "vasthtml")."</a><br />".$this->login_form();
			$welcome = __("Guest", "vasthtml"). " <strong>$user_login</strong>";
			$colspan = "";
		}
		if(!$user_ID && !$this->allow_unreg()){
			$meta = __("<div id='forumLogin'><p>Welcome Guest, posting in this forum requires", "vasthtml")." <a href='$this->reg_link'>".__("registration.", "vasthtml")."</a></p>".$this->login_form();
			$colspan = "";
		}
		$o = "<div class='wpf'>
				
				<table width='100%' class='wpf-table' id='profileHeader'>
					<tr>
						<th $colspan ><h4 style='float:left;'>$welcome&nbsp;</h4>
						<a style='float:right;' href='#' onclick='shrinkHeader(!current_header); return false;'>
							<img id='upshrink'  src='$this->skin_url/images/upshrink.png' alt='".__("Show or hide header", "vasthtml")."'/></a>
						</th>
					</tr>
			
					<tr id='upshrinkHeader'>
						$avatar
						<td valign='top'>$meta</td>
					</tr>
					
					<tr id='upshrinkHeader2' >
						<th class='wpf-bright right' $colspan= align='right'>
							<div>
								<form name='wpf_search_form' method='post' action='$this->base_url"."search'>
									<input type='text' name='search_words' />
									<input type='submit' name='search_submit' value='".__("Search forums", "vasthtml")."' />
								</form>
							</div>
						</th>
					</tr>
				</table>
			</div>";
		$o .= $this->setup_menu();
		$o .= $this->trail();


		$this->o .= $o;
	
	}
	function get_pagelinks($thread_id){
		global $wpdb;
		
		$pages = $wpdb->get_results("SELECT * FROM $this->t_posts WHERE parent_id = $thread_id");
		
		if(count($pages) > $this->opt['forum_posts_per_page']){
			$num_pages = ceil(count($pages)/$this->opt['forum_posts_per_page']);
			
			for($i = 0; $i < $num_pages; ++$i){
				$out .= " <a href='".$this->thread_link.$thread_id.".".$i."'>".($i+1)."</a>";
			}
			return " &laquo; $out &raquo;";
		}
		else
			return "";
	}
	function post_pageing($thread_id){
		global $wpdb;
		$out .=  __("Pages:", "vasthtml");
		
		$count = $wpdb->get_var("SELECT count(*) FROM $this->t_posts WHERE parent_id = $thread_id");
		$num_pages = ceil($count/$this->opt['forum_posts_per_page']);
		
			
		for($i = 0; $i < $num_pages; ++$i){
			if($i ==  $this->curr_page)
				$out .= " [<strong>".($i+1)."</strong>]";
			else
				$out .= " <a href='".$this->thread_link.$this->current_thread.".".$i."'>".($i+1)."</a>";
		}
		return "<span class='wpf-pages'>$out</span>";
	}
	
	
		function thread_pageing($forum_id){
		global $wpdb;
		$out .= __("Pages:", "vasthtml");
		
		$count = $wpdb->get_var("SELECT count(*) FROM $this->t_threads WHERE parent_id = $forum_id");
		$num_pages = ceil($count/$this->opt['forum_threads_per_page']);
		
			
		for($i = 0; $i < $num_pages; ++$i){
			if($i ==  $this->curr_page)
				$out .= " [<strong>".($i+1)."</strong>]";
			else
				$out .= " <a href='".$this->forum_link.$this->current_forum.".".$i."'>".($i+1)."</a>";
		}
		return "<span class='wpf-pages'>$out</span>";
	}
	
	function remove_topic(){
		global $user_level, $user_ID, $wpdb;
		$topic = $_GET['topic'];
		if($this->is_moderator($user_ID, $this->current_forum)){
			$wpdb->query("DELETE FROM $this->t_posts WHERE parent_id = $topic");
			$wpdb->query("DELETE FROM $this->t_threads WHERE id = $topic");
		}else{
			@ob_end_clean();
			wp_die(__("Cheating, are we?", "vasthtml"));
		}
		
	}
	
	function getNewForumID(){
		global $user_level, $user_ID, $wpdb;
		$topic = !empty($_GET['topic']) ? (int)$_GET['topic'] : 0;
		$topic = !empty($_GET['t']) ? (int)$_GET['t'] : $topic;
		if($this->is_moderator($user_ID, $this->current_forum)){
			// move topic html!?!
			$currentForumID = $this->check_parms($_GET['f']);
			$strOUT = '
			<form id="" method="post" action="?vasthtmlaction=viewforum&f='.$currentForumID.'&move_topic&topic='.$topic.'">
			Move "<strong>'.$this->get_subject($topic).'</strong>" to new forum: <select id="newForumID" name="newForumID" onchange="location=\'?vasthtmlaction=viewforum&f='.$currentForumID.'&move_topic&topic='.$topic.'&g='.$g.'&newForumID=\'+this.options[this.selectedIndex].value">';
			$frs	= $this->get_forums($g);
			foreach($frs as $f){
				$strOUT .= '
				<option value="'.$f->id.'"'.($f->id==$currentForumID ? ' selected="selected"' : '').'>'.$f->name.'</option>';
			}
			$strOUT .= '
			</select>
			<noscript><input type="submit" value="Go!" /></noscript>
			</form>';

			return $strOUT;
		}else{
			@ob_end_clean();
			wp_die(__("Cheating, are we?", "vasthtml"));
		}
		
	}
	
	function move_topic(){
		global $user_level, $user_ID, $wpdb;
		$topic = $_GET['topic'];
		$currentForumID = $this->check_parms($_GET['f']);
		$newForumID = !empty($_GET['newForumID']) ? (int)$_GET['newForumID'] : 0;
		$newForumID = !empty($_POST['newForumID']) ? (int)$_POST['newForumID'] : $newForumID;
		if($this->is_moderator($user_ID, $this->current_forum)){
			$strSQL = "UPDATE $this->t_threads SET parent_id = $newForumID WHERE id = $topic";
			//echo "strSQL=$strSQL<br />\n";
			$wpdb->query($strSQL);
			@ob_end_clean();
			@header("location: ?vasthtmlaction=viewforum&f=".$newForumID);
			@exit;
		}else{
			@ob_end_clean();
			wp_die(__("Cheating, are we?", "vasthtml"));
		}
		
	}

	function remove_post(){
		global $user_level, $user_ID, $wpdb;
		$id = $_GET['id'];
		$author = $wpdb->get_var("SELECT author_id from $this->t_posts where id = $id");
		
		$del = "fail";
		if($user_level > 8)
			$del = "ok";
		if($this->is_moderator($user_ID, $this->current_forum))
			$del = "ok";
		if($user_ID ==  $author)
			$del = "ok";
			
		if($del == "ok"){
			$wpdb->query("DELETE FROM $this->t_posts WHERE id = $id");
			$this->o .= "<div class='updated'>".__("Post deleted", "vasthtml")."</div>";		
		}else{
			@ob_end_clean();
			wp_die(__("Cheating, are we?", "vasthtml"));
		}

	}
	function sticky_post(){
		global $user_level, $user_ID, $wpdb;
		if(!$this->is_moderator($user_ID, $this->current_forum) || $user_level < 8){
			@ob_end_clean();
			wp_die(__("Cheating, are we?", "vasthtml"));
		}
		$id = $_GET['id'];
		$status = $wpdb->get_var("select status from $this->t_threads where id = $id");
		
		switch($status){
			case 'sticky': 
				$wpdb->query("update $this->t_threads set status = 'open' where id = $id");
				break;
			case 'open': 
				$wpdb->query("update $this->t_threads set status = 'sticky' where id = $id");
				break;
		}
	}
	function notify_post(){
		global $wpdb, $user_ID;
		$id = $_GET['id'];
		$op = get_usermeta($user_ID, "wpf_useroptions");
		$topics = $op['notify_topics'];
		
		// Add topic
		if(!$this->array_search($id, $topics, TRUE)){
			$topics[] = $id;	
		}
		
		// Remove topic
		else{
			$key = array_search($id, $topics, TRUE);
   			unset($topics[$key]);
		}
		// Build array
		$op = array(	"allow_profile" => $op['allow_profile'], 
						"notify" => $op['notify'], 
						"notify_topics" => $topics
					);
					
		// Update meta
		update_usermeta($user_ID, "wpf_useroptions", $op);
	}
	function is_sticky($thread_id = ''){
		global $wpdb;
		if($thread_id)
			$id = $thread_id;
		else 
			$id = $this->current_thread;
		$status = $wpdb->get_var("select status from $this->t_threads where where id = $id");
		if($status == "sticky")
		 	return true;
		 return false;

	}
	function closed_post(){
		global $user_level, $user_ID, $wpdb;
		if(!$this->is_moderator($user_ID, $this->current_forum) || $user_level < 8){
			@ob_end_clean();
			wp_die(__("Cheating, are we?", "vasthtml"));
		}
		$strSQL = "update $this->t_threads set closed = '".$_GET['closed']."' where id = ".$_GET['id'];
		//echo "strSQL=$strSQL<br />";
		$wpdb->query($strSQL);
	}
	function is_closed($thread_id = ''){
		global $wpdb;
		if($thread_id){
			$id = $thread_id;
		}else{
			$id = $this->current_thread;
		}
		$strSQL = "select closed from $this->t_threads where id = $id";
		//echo "strSQL=$strSQL<br />";
		$closed = $wpdb->get_var($strSQL);
		//echo "closed=$closed<br />";
		if($closed){
			return true;
		}else{
			return false;
		}
	}
	function allow_unreg(){
		if($this->opt['forum_require_registration'] == false)
			return true;
		return false;
	}
	
	function profile_link($user_id){
		$user = $this->get_userdata($user_id, USER);

		if($user == __("Guest", "vasthtml"))
			return $user;

		$user_op = get_usermeta($user_id, "wpf_useroptions");
		if($user_op)
			if($user_op['allow_profile'] == false)
				return $user;

		$link = "<a href='".$this->base_url."profile&amp;id=$user_id' title='".__("View profile", "vasthtml")."'>$user</a>";
		return $link;
	}
	
	function form_buttons(){
			
		$button = '
	<a title="'.__("Bold", "vasthtml").'" href="javascript:void(0);" onclick=\'surroundText("[b]", "[/b]", document.forms.addform.message); return false;\'><img src="'.$this->skin_url.'/images/bbc/b.png" /></a>
	<a title="'.__("Italic", "vasthtml").'" href="javascript:void(0);" onclick=\'surroundText("[i]", "[/i]", document.forms.addform.message); return false;\'><img src="'.$this->skin_url.'/images/bbc/i.png" /></a>
	<a title="'.__("Underline", "vasthtml").'" href="javascript:void(0);" onclick=\'surroundText("[u]", "[/u]", document.forms.addform.message); return false;\'><img src="'.$this->skin_url.'/images/bbc/u.png" /></a>
	<a title="'.__("Code", "vasthtml").'" href="javascript:void(0);" onclick=\'surroundText("[code]", "[/code]", document.forms.addform.message); return false;\'><img src="'.$this->skin_url.'/images/bbc/code.png" /></a>
	<a title="'.__("Quote", "vasthtml").'" href="javascript:void(0);" onclick=\'surroundText("[quote]", "[/quote]", document.forms.addform.message); return false;\'><img src="'.$this->skin_url.'/images/bbc/quote.png" /></a>
	<a title="'.__("List", "vasthtml").'" href="javascript:void(0);" onclick=\'surroundText("[list]", "[/list]", document.forms.addform.message); return false;\'><img src="'.$this->skin_url.'/images/bbc/list.png" /></a>
	<a title="'.__("List item", "vasthtml").'" href="javascript:void(0);" onclick=\'surroundText("[*]", "", document.forms.addform.message); return false;\'><img src="'.$this->skin_url.'/images/bbc/li.png" /></a>
	<a title="'.__("Link", "vasthtml").'" href="javascript:void(0);" onclick=\'surroundText("[url]", "[/url]", document.forms.addform.message); return false;\'><img src="'.$this->skin_url.'/images/bbc/url.png" /></a>
	<a title="'.__("Image", "vasthtml").'" href="javascript:void(0);" onclick=\'surroundText("[img]", "[/img]", document.forms.addform.message); return false;\'><img src="'.$this->skin_url.'/images/bbc/img.png" /></a>
	<a title="'.__("Email", "vasthtml").'" href="javascript:void(0);" onclick=\'surroundText("[email]", "[/email]", document.forms.addform.message); return false;\'><img src="'.$this->skin_url.'/images/bbc/email.png" /></a>';

		return $button;
	}
	
	function footer(){
		switch($this->current_view){
			case MAIN: 
				$o = "<div class='wpf'>";

				$o .= "<table class='wpf-table' width='100%' cellspacing='0' cellpadding='0'>";
						$o .= "<tr>
									<th align='center' colspan='2'>".__("Info Center", "vasthtml")."</th>
								</tr>
								<tr>
								</tr>
								<tr>
									<td width='3%' class='forumIcon' align='center'><img alt='' src='$this->skin_url/images/icons/info.gif' /></td>
									<td>
										".$this->num_posts_total()." ".__("Posts", "vasthtml")." ".__("in", "vasthtml")." ".$this->num_threads_total()." ".__("Topics ".__("Made by", "vasthtml")."", "vasthtml")." ".count($this->get_users())." ".__("Members", "vasthtml").". ".__("Latest Member:", "vasthtml")." ".$this->profile_link($this->latest_member())."
										<br />".$this->get_lastpost_all()."
									</td>
								</tr>
						</table>";
								$o .= "</div>";

				break;
			case FORUM: break;
			case THREAD: break;
		}
		$this->o .= $o;
	}
	
	function latest_member(){
		global $wpdb;
		
		return $wpdb->get_var("select ID from $wpdb->users order by user_registered DESC limit 1");
	}
	
	
	function show_new(){
	$this->current_view = NEWTOPICS;
		global $wpdb;
		$this->header();
		$lastvisit = @date("Y-m-d H:i:s", $this->last_visit());

		//$posts = $wpdb->get_results("SELECT * FROM $this->t_posts WHERE `date` > '$lastvisit' ORDER BY `date` DESC");
		
		
		//$posts = $wpdb->get_results("select $this->t_posts.id as postid, $this->t_posts.date, $this->t_posts.author_id, $this->t_threads.starter, $this->t_threads.views, $this->t_threads.subject, $this->t_threads.id as threadid from $this->t_posts inner join $this->t_threads on $this->t_posts.parent_id = $this->t_threads.id where $this->t_posts.date > '$lastvisit' order by $this->t_posts.date desc");
		$threads = $wpdb->get_results("select distinct($this->t_threads.id) from $this->t_posts inner join $this->t_threads on $this->t_posts.parent_id = $this->t_threads.id where $this->t_posts.date > '$lastvisit' order by $this->t_posts.date desc");

		
			$o .= "<div class='wpf'><table class='wpf-table' cellpadding='0' cellspacing='0'>
							<tr>
							<th colspan='5' class='wpf-bright'>".__("New topics since your last visit", "vasthtml")."</th>
						</tr>
						<tr>
							<th width='6%'></th>
							<th>".__("Topic Title", "vasthtml")."</th>
							<th width='11%' nowrap='nowrap'>".__("Started by", "vasthtml")."</th>
							<th width='4%'>".__("Replies", "vasthtml")."</th>
							<th width='22%'>".__("Last post", "vasthtml")."</th>
						</tr>";
											
				foreach($threads as $thread){						
							
							$starter_id = $wpdb->get_var("SELECT starter FROM $this->t_threads WHERE id = $thread->id");
							
							$o .= "<tr>
							<td align='center' class='forumIcon'>".$this->get_topic_image($thread->id)."</td>
							<td class='wpf-alt $sticky' align='top'><a href='"
								.$this->get_threadlink($thread->id)."'>"
								.$this->output_filter($this->get_threadname($thread->id))."</a> $page
							</td>
							<td>".$this->profile_link($starter_id)."</td>
							<td class='wpf-alt $sticky' align='center'>".$this->num_posts($thread->id)."</td>
							<td><small>".$this->get_lastpost($thread->id)."</small></td>
						</tr>";

				}
				
		$o .= "</table></div>";
		$this->o .= $o;
		$this->footer();
	}
	function num_post_user($user){
		global $wpdb;
		return $wpdb->get_var("SELECT count(author_id) FROM $this->t_posts WHERE author_id = $user");
	}
	function view_profile(){
		global $wpdb, $user_ID, $user_level;
		$this->current_view = PROFILE;
		$user_id = $_GET['id'];
		$user = get_userdata($user_id);
		$this->header();
		if($user_ID == $user_id || $user_level > 8)
			$editlink = "<tr><td><a href='$this->base_url"."editprofile&user_id=$user_id'>".__("Edit forum options", "vasthtml")."</a>
					<a href='$this->profile_link'> | ".__("Edit personal options", "vasthtml")."</a></td></tr>";
		$o .= "<div class='wpf'>
				<table class='wpf-table' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
						<th class='wpf-bright'>".__("Summary", "vasthtml")." - $user->user_login</th>
					</tr>
					
						$editlink
					
					<tr>	
						<td>
							<table class='wpf-table' cellpadding='0' cellspacing='0' width='100%'>
								<tr>
									<td width='20%'><strong>".__("Name:", "vasthtml")."</strong></td>
									<td>$user->first_name $user->last_name</td>
									<td rowspan='9' valign='top' width='1%'>".$this->get_avatar($user_id, 60)."</td>

								</tr>
								<tr>
									<td><strong>".__("Registered:", "vasthtml")."</strong></td>
									<td>".$this->format_date($user->user_registered)."</td>
								</tr>
								<tr>
									<td><strong>".__("Posts:", "vasthtml")."</strong></td>
									<td>".$this->num_post_user($user_id)."</td>
								</tr>
								<tr>
									<td><strong>".__("Position:", "vasthtml")."</strong></td>
									<td>".$this->get_userrole($user_id)."</td></tr>
								<tr>
									<td><strong>".__("Website:", "vasthtml")."</strong></td>
									<td><a href='$user->user_url'>$user->user_url</a></td>
								</tr>
								<tr>
									<td><strong>".__("AIM:", "vasthtml")."</strong></td>
									<td>$user->aim</td>
								</tr>
								<tr>
									<td><strong>".__("Yahoo:", "vasthtml")."</strong></td>
									<td>$user->yim</td></tr>
								<tr>
									<td><strong>".__("Jabber/google Talk:", "vasthtml")."</strong></td>
									<td>$user->jabber</td>
								</tr>
								<tr>
									<td valign='top'><strong>".__("Biographical Info:", "vasthtml")."$user->bio</strong></td>
									<td valign='top'>".$this->output_filter(apply_filters('comment_text', $user->description))."</td>
								</tr>
							</table>
						</td>
					</tr>
				</table></div>";
		
		$this->o .= $o;
		$this->footer();
	}
	
	function search_results(){
		global $wpdb;
		$this->current_view = SEARCH;
		$this->header();
		
		if(!isset($_POST['search_submit'])){
		$groups = $this->get_groups();

			$o .= "<div class='wpf' style='margin:0 auto;'><form name='wpf_searchform' method='post' action=''>
					<table class='wpf-table search' cellspacing='0' cellpadding='0' width='100%'>
						<tr>
							<th colspan='2' class='wpf-bright'>".__("Search", "vasthtml")."</th>
						</tr>
						<tr>
							<td>
								<strong>".__("Search for:", "vasthtml")."</strong><br />
								<input type='text' name='search_words' size='30'/>
							</td>
							<td>
								<strong>".__("By user:", "vasthtml")."</strong><br />
								<input type='text' name='search_user' value='*' size='30' />
							</td>
						</tr>
							<td colspan='2'>
								<strong>".__("Message Age:", "vasthtml")."</strong><br />
								".__("Between", "vasthtml")." <input type='text' size='5' value='0' name='search_min'/> ".__("and", "vasthtml"). "<input type='text' size='5' value='9999' name='search_max'/> ".__("days", "vasthtml")."
							</td>
						<tr>
						<tr>
							<td colspan='2'>
								<div style='padding:10px;' >
									 <a href='javascript:void(0);' onclick='expandCollapseBoards(); return false;'><img alt='' src='$this->skin_url/images/upshrink2.png' id='search_coll'/><b> ".__("Choose a forum to search in, or search all", "vasthtml")."</b></a><br />";
								$o.= "<table cellspacing='0' cellpadding='0' width='100%' id='searchBoardsExpand' style='display:none'>";
									$i = 0;
									
											
							foreach($groups as $group){
								$forums = $this->get_forums($group->id);
								$frs = "";
								foreach($forums as $f)
									$frs .= $f->id.",";
								
								$p = substr($frs, 0, strlen($frs)-1);

	
								if($i == 0)
									$o .= "<tr>";
									
								$o .= "<td valign='top'><a href='javascript:void(0);' onclick='selectBoards([$p]); return false;' style='text-decoration: underline;'>$group->name</a>";

								foreach($forums as $forum)
									$o .=  "<br /><input type='checkbox' checked='checked' id='forum"."$forum->id' name='forum[$forum->id]' value='$forum->id' /> $forum->name";
									
								$o .=  "</td>";
								
								++$i;
								if($i == 2){
									$i = 0;
									$o .= "</tr>";
								}
							}		
							$o .= "</table>
									<input type='checkbox' id='check_all' name='check_all' checked='checked' onclick='invertAll(this, this.form, \"forum\");' /> ".__("Check all", "vasthtml")."
								</div>
							</td>
						</tr>
						</tr>
							<td colspan='2' align='center'><input type='submit' name='search_submit' value='".__("Start Search", "vasthtml")."'/></td>
						</tr>";
			
			$o .= "</table></form></div>";
		}
		
		else{
			$search_string = $wpdb->escape($_POST['search_words']);
			$option_topics_only = $_POST['topics_only'];
			$option_show_as_post = $_POST['show_messages'];
			$option_user = $_POST['search_user'];
			$option_min_days = $_POST['search_min'];
			$option_max_days = $_POST['search_max'];
			$option_forums = $_POST['forum'];
			if(!$option_max_days)
				 $option_max_days = 9999;
			$op .= " AND $this->t_posts.`date` > SUBDATE(CURDATE(), INTERVAL $option_max_days DAY) ";
			
			if($user = get_userdata($option_user))
				$op .= " AND author_id = '$user->ID' ";
			
			if($option_topics_only)
				$what = "subject";
			else
				$what = "text";

			foreach((array)$option_forums as $f)
				$a .= $f.",";
				
			$a = substr($a, 0, strlen($a)-1 );
			if(!$a)
				$w = "";
			else
				$w = "IN($a)";
				
			$sql = "SELECT $this->t_threads.parent_id as pt, $this->t_posts.id, text, $this->t_posts.subject, $this->t_posts.parent_id, $this->t_posts.`date`, MATCH ($what) AGAINST ('$search_string') AS score 
			FROM $this->t_posts inner join $this->t_threads on $this->t_posts.parent_id = $this->t_threads.id 
			WHERE $this->t_threads.parent_id  $w
			AND MATCH (text) AGAINST ('$search_string') $op";
			
			 //$this->pre($sql);

			$results = $wpdb->get_results($sql);
			$max = 0;
			foreach($results as $result)
				if($result->score > $max)	
					$max = $result->score;
			if($results)
				$const = 100/$max;
			
			$o .= "<table class='wpf-table' cellspacing='0' cellpadding='0' width='100%'>
					<tr>
						<th width='5%'></th>
						<th width='100%'>".__("Subject", "vasthtml")."</th>
						<th>".__("Relevance", "vasthtml")."</th>
						<th>".__("Started by", "vasthtml")."</th>
						<th>".__("Posted", "vasthtml")."</th>
					</tr>";
						
			//$this->pre($results);

			foreach($results as $result){
									
				if($this->have_access($this->forum_get_group_from_post($result->parent_id))){
				$starter = $wpdb->get_var("select starter from $this->t_threads where id = $result->parent_id");
				
					$o .= "<tr>
								<td valign='top' align='center'>".$this->get_topic_image($result->parent_id)."</td>
								<td valign='top' class='wpf-alt'><a href='".$this->get_threadlink($result->parent_id)."'>".stripslashes($result->subject)."</a>
								</td>
								<td valign='top'><small>".round($result->score*$const, 1)."%</small></td>
								<td valign='top' nowrap='nowrap' class='wpf-alt'>".$this->profile_link($starter)."</td>
								<td valign='top' class='wpf-alt' nowrap='nowrap'>".$this->format_date($result->date)."</td>
							</tr>";
				}
			}
			$o .= "</table>";
		}
		
		
		$this->o .= $o;
		$this->footer();
	}
	function ext_str_ireplace($findme, $replacewith, $subject){
 	 	// Replaces $findme in $subject with $replacewith
 	 	// Ignores the case and do keep the original capitalization by using $1 in $replacewith
 	 	// Required: PHP 5
 
 	 	return substr($subject, 0, stripos($subject, $findme)).
 	 	       str_replace('$1', substr($subject, stripos($subject, $findme), strlen($findme)), $replacewith).
 	 	       substr($subject, stripos($subject, $findme)+strlen($findme));
	}

	function cuttext($value, $length){    
		if(is_array($value)) list($string, $match_to) = $value;
		else { $string = $value; $match_to = $value{0}; }
	
		$match_start = stristr($string, $match_to);
		$match_compute = strlen($string) - strlen($match_start);
	
		if (strlen($string) > $length)
		{
			if ($match_compute < ($length - strlen($match_to)))
			{
				$pre_string = substr($string, 0, $length);
				$pos_end = strrpos($pre_string, " ");
				if($pos_end === false) $string = $pre_string."...";
				else $string = substr($pre_string, 0, $pos_end)."...";
			}
			else if ($match_compute > (strlen($string) - ($length - strlen($match_to))))
			{
				$pre_string = substr($string, (strlen($string) - ($length - strlen($match_to))));
				$pos_start = strpos($pre_string, " ");
				$string = "...".substr($pre_string, $pos_start);
				if($pos_start === false) $string = "...".$pre_string;
				else $string = "...".substr($pre_string, $pos_start);
			}
			else
			{        
				$pre_string = substr($string, ($match_compute - round(($length / 3))), $length);
				$pos_start = strpos($pre_string, " "); $pos_end = strrpos($pre_string, " ");
				$string = "...".substr($pre_string, $pos_start, $pos_end)."...";
				if($pos_start === false && $pos_end === false) $string = "...".$pre_string."...";
				else $string = "...".substr($pre_string, $pos_start, $pos_end)."...";
			}
	
			$match_start = stristr($string, $match_to);
			$match_compute = strlen($string) - strlen($match_start);
		}
		
		return $string;
	}
	
	function get_topic_image($thread){
	
		$post_count = $this->num_posts($thread);
		if($post_count <= $this->opt['hot_topic']){
			return "<img src='$this->skin_url/images/topic/normal_post.gif' alt='".__("Normal topic", "vasthtml")."' title='".__("Normal topic", "vasthtml")."'>";
		}
		if($post_count > $this->opt['veryhot_topic']){
			return "<img src='$this->skin_url/images/topic/my_hot_post.gif' alt='".__("Hot topic", "vasthtml")."' title='".__("Hot topic", "vasthtml")."'>";
		}
		if($post_count > $this->opt['hot_topic']){
			return "<img src='$this->skin_url/images/topic/hot_post.gif' alt='".__("Very Hot topic", "vasthtml")."' title='".__("Very Hot topic", "vasthtml")."'>";
		}

	}
	function get_captcha(){
		global $user_ID;
		$out = "";
		if(!$user_ID && $this->opt['forum_captcha'])
			$out .= "<tr>
						<td><img alt='' src='".WPFURL."captcha/captcha_images.php' /></td>
						<td>".__("Security Code:", "vasthtml")."<input id='security_code' name='security_code' type='text' /></td>
					</tr>";
		return $out;
	}
	
	
	function notify_starter($thread, $subject, $content, $date){
		global $wpdb;
		$users = $wpdb->get_results("SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = 'wpf_useroptions'");
			
			$sender = get_bloginfo("name");
			$subject = __("New reply on topic:", "vasthtml")." '$subject'.";
			$meta = __("Posted on: ", "vasthtml")." ".$this->format_date($date);
			$message = wordwrap($this->get_threadlink($thread)."0\n\n$content\n\n$meta", 70);
			$replyto = $sender;
			$headers = "MIME-Version: 1.0\r\n" .
				"From: $sender\n" . 
				"Reply-To: $replyto" . "\r\n" .		
				"Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\r\n";    		  						

		foreach($users as $u){
			$p = unserialize($u->meta_value);
			if($this->array_search($thread, $p['notify_topics'])){

				$user = get_userdata($u->user_id);
				$to = $user->user_email;
				wp_mail($to, $subject, stripslashes($message), $headers);
			}
		}
	}

	
	
	
	
	
	
} // End class
} // End
?>