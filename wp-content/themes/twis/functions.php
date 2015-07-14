<?php
/**

 *
 * @package WordPress
 * @subpackage memosweb
 * @since Memosweb 1.0
 */


	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'twentyten' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );



	register_sidebar( array(
		'name' => __( 'Primary Widget Area EN' ),
		'id' => 'primary-widget-area-en',
		'description' => __( 'The primary widget area  EN' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	//case studies
	register_sidebar( array(
		'name' => __( 'Case studies sidebar'),
		'id' => 'case-study-widget-area',
		'description' => __( 'Case studies widget area'),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );


	// testimonials 
	register_sidebar( array(
		'name' => __( 'Testimonials sidebar'),
		'id' => 'testimonials-widget-area',
		'description' => __( 'Testimonials widget area'),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );


	// referencies
	register_sidebar( array(
		'name' => __( 'Referencies sidebar'),
		'id' => 'referencies-widget-area',
		'description' => __( 'Referencies widget area'),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );



	// homepage 
	register_sidebar( array(
		'name' => __( 'Homepage casestudies'),
		'id' => 'homepage-second-widget-area',
		'description' => __( 'Homepage casestudies widget area'),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// homepage 
	register_sidebar( array(
		'name' => __( 'Homepage last line'),
		'id' => 'homepage-third-widget-area',
		'description' => __( 'Homepage last line widget area'),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );


	// homepage 
	register_sidebar( array(
		'name' => __( 'Homepage casestudies EN'),
		'id' => 'homepage-second-widget-area-en',
		'description' => __( 'Homepage casestudies widget area'),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// homepage 
	register_sidebar( array(
		'name' => __( 'Homepage last line EN'),
		'id' => 'homepage-third-widget-area-en',
		'description' => __( 'Homepage last line widget area EN'),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );




remove_action('wp_head', 'wp_generator');
