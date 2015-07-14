<?php
/*
Plugin Name: MEMOS Referencies
Description: Referencies post type for WordPress 3.0 and above.
Author: Slavomír Krivák
Version: 1.0
Author URI: http://www.memos.cz
*/
include_once(WP_PLUGIN_DIR."/memos-post-types/functions.php");
class Reference {
	var $meta_fields = array("testimonial");
	var $name="reference";
	
	function Reference()
	{
		// Register custom post types
		register_post_type($this->name, array(
			'public' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array("slug" => $this->name), // Permalinks
			'query_var' =>$this->name, // This goes to the WP_Query schema
			'labels' => array(	'name' => __( 'Referencies' ),
						'singular_name' => __( 'Reference' ),
						'add_new' => __( 'Add New' ),
						'add_new_item' => __( 'Add New Reference' ),
						'edit' => __( 'Edit' ),
						'edit_item' => __( 'Edit Reference' ),
						'new_item' => __( 'New Reference' ),
						'view' => __( 'View Reference' ),
						'view_item' => __( 'View Reference' ),
						'search_items' => __( 'Search Referencey' ),
						'not_found' => __( 'No Reference found' ),
						'not_found_in_trash' => __( 'No Reference found in Trash' ),
						'parent' => __( 'Parent Reference' )
					 ),

			'supports' => array('title','author', 'excerpt', 'editor','revisions' /*,'custom-fields'*/) // Let's use custom fields for debugging purposes only
		));
		
		add_filter("manage_edit-".$this->name."_columns", array(&$this, "edit_columns"));
		add_action("manage_posts_custom_column", array(&$this, "custom_columns"));
		


		// Admin interface init
		add_action("admin_init", array(&$this, "admin_init"));
		add_action("template_redirect", array(&$this, 'template_redirect'));
		
		// Insert post hook
		add_action("wp_insert_post", array(&$this, "wp_insert_post"), 10, 2);
	}
	
	function edit_columns($columns)
	{
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Reference Title",
			$this->name."-description" => "Description",
			$this->name."-company" => "Company",
			$this->name."-url" => "Url",

		);
		
		return $columns;
	}
	
	function custom_columns($column)
	{
		global $post;
		switch ($column)
		{
			case $this->name."-description":
				the_excerpt();
				break;
			case $this->name."-company":
				$custom = get_post_custom();
				echo $custom["Company"][0];
				break;
			case $this->name."-url":
				$custom = get_post_custom();
				echo $custom["Url"][0];
				break;

		}
	}
	
	// Template selection
	function template_redirect()
	{
		global $wp;
		if ($wp->query_vars["post_type"] == $this->name)
		{
			include(TEMPLATEPATH . "/".$this->name.".php");
			die();
		}
	}
	
	// When a post is inserted or updated
	function wp_insert_post($post_id, $post = null)
	{
		if ($post->post_type == $this->name)
		{
			// Loop through the POST data
			foreach ($this->meta_fields as $key)
			{
				$value = @$_POST[$key];
				if (empty($value))
				{
					delete_post_meta($post_id, $key);
					continue;
				}

				// If value is a string it should be unique
				if (!is_array($value))
				{
					// Update meta
					if (!update_post_meta($post_id, $key, $value))
					{
						// Or add the meta data
						add_post_meta($post_id, $key, $value);
					}
				}
				else
				{
					// If passed along is an array, we should remove all previous data
					delete_post_meta($post_id, $key);
					
					// Loop through the array adding new values to the post meta as different entries with the same name
					foreach ($value as $entry)
						add_post_meta($post_id, $key, $entry);
				}
			}
		}
	}
	
	function admin_init() 
	{
		// Custom meta boxes for the edit podcast screen
		add_meta_box($this->name."-relations", "Relations", array(&$this, "meta_options"), $this->name, "side", "low");
	}
	
	// Admin post meta contents
	function meta_options()
	{
		global $post;
		$custom = get_post_custom($post->ID);
		$testimonial = $custom["testimonial"][0];
		get_testimonials($testimonial);
	}
}

// Initiate the plugin
add_action("init", "ReferenceInit");
function ReferenceInit() { global $memReference; $memReference = new Reference(); }