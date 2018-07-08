<?php
/**
 * Template Name: Jobs: Map & Results
 *
 * @package Jobify
 * @since Jobify 1.0
 */

get_header(); ?>

	<div id="primary" role="main">
		<?php do_action( 'jobify_output_map' ); ?>

		<div class="container content-area">
			<?php if ( jobify()->get( 'woocommerce' ) ) : ?>
				<?php wc_print_notices(); ?>
			<?php endif; ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<div class="entry-content">
					<?php
					if ( '' == get_post()->post_content ) :
						echo do_shortcode( '[jobs]' );
						else :
							the_content();
						endif;
					?>
				</div>

			<?php endwhile; ?>
		</div>

	</div><!-- #primary -->

<?php get_footer(); ?>
