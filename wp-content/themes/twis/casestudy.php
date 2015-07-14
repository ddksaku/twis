<?php
/**
 * Case studies
 *
 * @package WordPress
 * @subpackage Memosweb
 * @since Memosweb 1.0
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>



				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>



					<div class="entry-content">
						<?php
							$custom = get_post_custom($post->ID);
							$image = wp_get_attachment_image_src($custom["image"][0], $size='thumbnail', $icon = false); 
							if(!empty($image)){
								echo "<a href=\"".get_permalink($post->ID)."\"  title=\"\">".$post->post_title."</a>";
								echo "<img src=\"".$image[0]."\" alt=\"".$custom["name"][0]."\"/>";
							}
						?>
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->

				</div><!-- #post-## -->

<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar("casestudy"); ?>
<?php get_footer(); ?>
