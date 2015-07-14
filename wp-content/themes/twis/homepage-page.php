<?php
/**
 * Template Name: Hompage
 *
 * A custom page template without sidebar.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 * @package WordPress
 * @subpackage Memosweb
 * @since memosweb 1.0
 */

get_header();
	
dynamic_sidebar( 'homepage-second-widget-area' );
dynamic_sidebar( 'homepage-third-widget-area' );
	
get_footer(); ?>
