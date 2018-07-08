<?php
/**
 * Single Page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

	<?php if ( Jobify_Page_Header::show_page_header() ) : ?>
	<header class="page-header">
		<h2 class="page-title"><?php the_title(); ?></h2>
	</header>
	<?php endif; ?>

	<div id="primary" class="content-area container" role="main">
		<?php if ( jobify()->get( 'woocommerce' ) ) : ?>
			<?php wc_print_notices(); ?>
		<?php endif; ?>

		<?php get_template_part( 'content', 'page' ); ?>

		<?php do_action( 'jobify_loop_after' ); ?>
	</div><!-- #primary -->

	<?php endwhile; ?>

<?php get_footer(); ?>
