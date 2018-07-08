<?php
/**
 * Template Name: Page: Widgetized
 *
 * @package Jobify
 * @since Jobify 3.0.0
 */

get_header(); ?>

	<div id="primary" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<div class="content-area content-area--squash">

				<?php if ( jobify()->get( 'woocommerce' ) ) : ?>
					<?php wc_print_notices(); ?>
				<?php endif; ?>

				<?php if ( '' != get_the_content() ) : ?>
					<?php get_template_part( 'content', 'page' ); ?>
				<?php endif; ?>

				<?php dynamic_sidebar( 'widget-area-page-' . get_the_ID() ); ?>

			</div>

		<?php endwhile; ?>

	</div><!-- #primary -->

<?php get_footer(); ?>
