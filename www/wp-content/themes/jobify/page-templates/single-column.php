<?php
/**
 * Template Name: Layout: Single Column
 *
 * @package Jobify
 * @since Jobify 3.0.0
 */

get_header(); ?>

	<?php if ( Jobify_Page_Header::show_page_header() ) : ?>
	<header class="page-header">
		<h2 class="page-title"><?php the_post();
		the_title();
		rewind_posts(); ?></h2>
	</header>
	<?php endif; ?>

	<div id="primary" class="content-area container" role="main">
		<div class="row">
			<div class="<?php echo apply_filters( 'jobify_single_column_spans', 'col-sm-12 col-md-10 col-md-offset-1' ); ?>">
				<?php if ( jobify()->get( 'woocommerce' ) ) : ?>
					<?php wc_print_notices(); ?>
				<?php endif; ?>

				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', 'page' ); ?>
				<?php endwhile; ?>
			</div>
		</div>

		<?php do_action( 'jobify_loop_after' ); ?>
	</div><!-- #primary -->

<?php get_footer(); ?>
