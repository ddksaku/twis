<?php
/**
 * @package WordPress
 * @subpackage memosweb
 * @since Memosweb 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	wp_title( '|', true, 'right' );
	bloginfo('name');
?></title>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<?php
	$go=false;
	$desc="";
	if($post->post_type=="reference"){
		$go=true;
		$custom = get_post_custom($post->ID);
		$desc= __("Company").": ". $custom["company"][0]." ". __("Url").": ". $custom["url"][0]."";
	}elseif($post->post_type=="testimonials"){
		$go=true;
		$desc= __("Company").": ". $custom["company"][0]." ". __("Url").": ". $custom["url"][0]." " .__("Name").": ". $custom["name"][0];
	}
	if($go){
		echo "<meta name=\"description\" content=\"".$desc."\" />";
	}

	wp_head();
?>
</head>
<body <?php body_class(); ?>>
<div id="wrapper" class="hfeed">
<div id="header">
<div id="access" role="navigation">
<?php 
	global $page_lang;


	if($page_lang=="en"){
		$menu=5;
	}else{
		$menu=3;
	}

	wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' , 'menu' =>  $menu) );
?>
</div><!-- #access -->
</div><!-- #header --><div id="main">