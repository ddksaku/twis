<?php
/**
 * Template Name: Hompage en
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
$page_lang="en";
global $page_lang;

get_header();

dynamic_sidebar( 'homepage-second-widget-area-en' );
dynamic_sidebar( 'homepage-third-widget-area-en' );
	
get_footer();
 ?>
