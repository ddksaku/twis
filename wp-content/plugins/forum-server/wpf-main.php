<?php
/*
	Plugin Name: WP Forum Server
	Plugin Author: VastHTML
	Author URI: http://lucidcrew.com/
    Plugin URI: http://vasthtml.com/js/wordpress-forum-server/
	Version: 1.4
*/

//$plugin_dir = basename(dirname(__FILE__)); 
//load_plugin_textdomain( 'vasthtml', ABSPATH.'wp-content/plugins/'. $plugin_dir.'/', $plugin_dir.'/' ); 
include_once("wpf.class.php");

// Short and sweet :)
$vasthtml = new vasthtml();

// Activating?
register_activation_hook(__FILE__ ,array(&$vasthtml,'wp_forum_install'));

add_action("the_content", array(&$vasthtml, "go"));
add_action('init', array(&$vasthtml,'set_cookie'));
add_filter("wp_title", array(&$vasthtml, "set_pagetitle"));
function latest_activity($num = 5){
	global $vasthtml;
	return $vasthtml->latest_activity($num);
}

?>