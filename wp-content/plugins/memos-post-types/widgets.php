<?php
/*
Plugin Name: MEMOS widgets
Description: Widgets for Testimonials and Referencies
Author: Slavomír Krivák
Version: 1.0
*/
 
class Widget_Testimonials extends WP_Widget {
	function Widget_Testimonials() {
		$widget_ops = array( 'description' => 'Show a testimonial' );
		$this->WP_Widget('widget_testimonials', 'Memos Testimonials', $widget_ops);
	}
 
	function widget($args, $instance) {
		global $wpdb;

		extract($args, EXTR_SKIP);
 
		$custom = get_post_custom($post->ID);
		$testimonial = $custom["testimonial"][0];
		if($testimonial!="none"){
			
			if($testimonial=="random" || empty($testimonial)){
				$testimonial= $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type=\"testimonials\" AND post_status = 'publish' ORDER BY RAND() LIMIT 1;"));
			}

			$post_t = get_post($testimonial); 
			
			if(!empty($post_t->ID)){
				echo $before_widget;

				if(!empty($instance['title'])){
					$title=$instance['title'];
				}else{
					$title= __("Testimonials");
				}

				echo $before_title .$title . $after_title;

				echo "<b>".$post_t->post_title."</b>"."<br/>";
				echo $post_t-> post_content."<br/>";
				unset($custom);
				$custom = get_post_custom($post_t->ID);

				echo "<b>".__("Company").":</b> ". $custom["company"][0]."<br/>";
				echo "<b>".__("Name").":</b> ". $custom["name"][0]."<br/>";
				echo "<b>".__("Phone").":</b> ". $custom["phone"][0]."<br/>";
				echo "<b>".__("Email").":</b> ". $custom["email"][0]."<br/>";
				echo "<b>".__("Url").":</b> ". $custom["url"][0]."<br/>";

				$image = wp_get_attachment_image_src($custom["image"][0], $size='thumbnail', $icon = false); 
				echo "<b>".__("Image").":</b> "."<img src=\"".$image[0]."\" alt=\"".$custom["name"][0]."\"/>" ."<br/>";

				echo $after_widget;
			}
		}
	}


	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

 
		return $instance;
	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);

?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
	}

 

}
add_action('widgets_init', 'testimonials_init');
function testimonials_init() {
	register_widget('Widget_Testimonials');
}

class Widget_Referencies extends WP_Widget {
	function Widget_Referencies() {
		$widget_ops = array( 'description' => 'Show a reference' );
		$this->WP_Widget('widget_referencies', 'Memos Referencies', $widget_ops);
	}
 
	function widget($args, $instance) {
		global $wpdb;

		extract($args, EXTR_SKIP);
 
		$custom = get_post_custom($post->ID);
		$reference = $custom["reference"][0];
		if($reference!="none"){
			
			if($reference=="random" || empty($reference)){
				$reference= $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type=\"reference\" AND post_status = 'publish' ORDER BY RAND() LIMIT 1;"));
			}

			$post_t = get_post($reference); 
			
			if(!empty($post_t->ID)){
				echo $before_widget;
				
				if(!empty($instance['title'])){
					$title=$instance['title'];
				}else{
					$title= __("Referencies");
				}

				echo $before_title .$title . $after_title;

				echo "<b>".$post_t->post_title."</b>"."<br/>";
				echo $post_t-> post_content."<br/>";
				unset($custom);
				$custom = get_post_custom($post_t->ID);

				echo "<b>".__("Company").":</b> ". $custom["company"][0]."<br/>";
				echo "<b>".__("Url").":</b> ". $custom["url"][0]."<br/>";

				$image = wp_get_attachment_image_src($custom["image"][0], $size='thumbnail', $icon = false); 
				echo "<b>".__("Image").":</b> "."<img src=\"".$image[0]."\" alt=\"".$custom["name"][0]."\"/>" ."<br/>";

				echo $after_widget;
			}
		}
	}


	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

 
		return $instance;
	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);

?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
	}
 

}
add_action('widgets_init', 'referencies_init');
function referencies_init() {
	register_widget('Widget_Referencies');
}

class Widget_CaseStudiesList extends WP_Widget {
	function Widget_CaseStudiesList() {
		$widget_ops = array( 'description' => 'Show a list of Case studies' );
		$this->WP_Widget('Widget_CaseStudiesList', 'Memos Case Studies', $widget_ops);
	}
 
	function widget($args, $instance) {
		global $wpdb;

		extract($args, EXTR_SKIP);
 
		$posts= $wpdb->get_results("SELECT ID,post_title FROM $wpdb->posts WHERE post_type=\"casestudy\" AND post_status = 'publish' ORDER BY post_date DESC;");
		
		echo $before_widget;
		
		if(!empty($instance['title'])){
			$title=$instance['title'];
		}else{
			$title=__("Case Studies") ;
		}
		
		echo $before_title .$title . $after_title;

	
		if ($posts) {
			echo "<ul>";
			foreach ($posts as $post) {
				$custom = get_post_custom($post->ID);
				if($custom["homepage"][0]=="true" && (($instance['english']=="on" && $custom["language"][0]=="english") || ($instance['english']!="on" && $custom["language"][0]!="english"))){
					$image = wp_get_attachment_image_src($custom["image"][0], $size='thumbnail', $icon = false); 
				
					echo "<li><a href=\"".get_permalink($post->ID)."\"  title=\"\">".$post->post_title."</a>";
					if(!empty($image)){
						echo "<img src=\"".$image[0]."\" alt=\"".$custom["name"][0]."\"/>" ;
					}
						echo "</li>";
				}
			}
			echo "</ul>";
		}

		echo $after_widget;
		
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['english'] = strip_tags($new_instance['english']);
 
		return $instance;
	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','english'=>'' ) );
		$title = strip_tags($instance['title']);
		$english = strip_tags($instance['english']);
	


?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('english'); ?>">English: <input class="widefat" id="<?php echo $this->get_field_id('english'); ?>" name="<?php echo $this->get_field_name('english'); ?>" type="checkbox" <?=($english=="on")?"checked=\"checked\"":"" ?> /></label></p>
<?php
	}
}
add_action('widgets_init', 'caseStudiesList_init');
function caseStudiesList_init() {
	register_widget('Widget_CaseStudiesList');
}


class Widget_wwha extends WP_Widget {
	function Widget_wwha() {
		$widget_ops = array( 'description' => 'Show what we have acheieved' );
		$this->WP_Widget('Widget_wwha', 'Memos What we have achieved', $widget_ops);
	}
 
	function widget($args, $instance) {
		global $wpdb;

		extract($args, EXTR_SKIP);

	
		$wwha= $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type=\"wwha\" AND post_status = 'publish' ORDER BY RAND() LIMIT 1;"));
			
		$post_t = get_post($wwha); 
			
		if(!empty($post_t->ID)){
				echo $before_widget;

				if(!empty($instance['title'])){
					$title=$instance['title'];
				}else{
					$title=__("What we have achieved") ;
				}
				echo $before_title .$title . $after_title;
				$custom = get_post_custom($post_t->ID);
				$image = wp_get_attachment_image_src($custom["image"][0], $size='thumbnail', $icon = false); 
				
				if(!empty($image)){
					echo "<img src=\"".$image[0]."\" alt=\"".$custom["name"][0]."\"/>" ;
				}
				echo "<b>".$post_t->post_title."</b>"."<br/>";
				echo $post_t-> post_content."<br/>";
				echo $after_widget;
		}
		
		
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

 
		return $instance;
	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);

?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
	}
 
}
add_action('widgets_init', 'wwha_init');
function wwha_init() {
	register_widget('Widget_wwha');
}


class Widget_memosMenu extends WP_Widget {
	function Widget_memosMenu() {
		$widget_ops = array( 'description' => 'Show sub menu' );
		$this->WP_Widget('Widget_memosMenu', 'Memos menu', $widget_ops);
	}
 
	function widget($args, $instance) {
		global $wpdb,$post;

		extract($args, EXTR_SKIP);

		$custom = get_post_custom($post->ID);
		if($custom["showmenu"][0]=="yes"){

			echo $before_widget;
			if(!empty($instance['title'])){
					$title=$instance['title'];
			}else{
				$title=__("Menu");
			}
			echo $before_title . $title . $after_title;
			wp_list_pages("title_li=&child_of=".$post->ID."&depth=1");
			echo $after_widget;
		}
	}


	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

 
		return $instance;
	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);

?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
	}
}
add_action('widgets_init', 'memosMenu_init');
function memosMenu_init() {
	register_widget('Widget_memosMenu');
}



class Widget_CaseStudy extends WP_Widget {
	function Widget_CaseStudy() {
		$widget_ops = array( 'description' => 'Show a single casestudy' );
		$this->WP_Widget('widget_casestudy', 'Memos single Casestudy', $widget_ops);
	}
 
	function widget($args, $instance) {
		global $wpdb;

		extract($args, EXTR_SKIP);
 
		$custom = get_post_custom($post->ID);
		$casestudy = $custom["casestudy"][0];
		if($casestudy!="none" && !empty($casestudy)){
			
			if($casestudy=="random" ){
				$casestudy= $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type=\"casestudy\" AND post_status = 'publish' ORDER BY RAND() LIMIT 1;"));
			}

			$post_t = get_post($casestudy); 
			
			if(!empty($post_t->ID)){
				echo $before_widget;

				if(!empty($instance['title'])){
					$title=$instance['title'];
				}else{
					$title= __("Case study");
				}

				echo $before_title .$title . $after_title;

				echo "<b>".$post_t->post_title."</b>"."<br/>";
				echo $post_t-> post_content."<br/>";
				unset($custom);
				$custom = get_post_custom($post_t->ID);

		

				$image = wp_get_attachment_image_src($custom["image"][0], $size='thumbnail', $icon = false); 
				echo "<b>".__("Image").":</b> "."<img src=\"".$image[0]."\" alt=\"".$custom["name"][0]."\"/>" ."<br/>";

				echo $after_widget;
			}
		}
	}


	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

 
		return $instance;
	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);

?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
	}

 

}
add_action('widgets_init', 'casestudy_init');
function casestudy_init() {
	register_widget('Widget_CaseStudy');
}
?>