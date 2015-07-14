<?php
/**
 * Template Name: services
 *
 * @package WordPress
 * @subpackage memosweb
 * @since Memosweb 1.0
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if ( is_front_page() ) { ?>
						<h2 class="entry-title"><?php the_title(); ?></h2>
					<?php } else { ?>	
						<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php } ?>				

					<?php

						wp_list_pages("title_li=&child_of=171&depth=1&exclude=185");


						
					?>
<h2>Produkty</h2>
					<?php

						wp_list_pages("title_li=&child_of=185&depth=1");


						
					?>

					
				</div><!-- #post-## -->


<?php endwhile; ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
