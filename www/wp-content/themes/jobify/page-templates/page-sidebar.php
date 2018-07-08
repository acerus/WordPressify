<?php
/**
 * Template Name: Layout: Sidebar
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

	<?php if ( Jobify_Page_Header::show_page_header() ) : ?>
	<header class="page-header">
		<h2 class="page-title"><?php the_title(); ?></h2>
	</header>
	<?php endif; ?>

	<div id="primary" class="content-area container" role="main">
		<div class="page-with-sidebar row">
			<div class="col-md-<?php echo is_active_sidebar( 'sidebar-blog' ) ? '9' : '12'; ?> col-xs-12">
				<?php if ( jobify()->get( 'woocommerce' ) ) : ?>
					<?php wc_print_notices(); ?>
				<?php endif; ?>

				<?php get_template_part( 'content', 'page' ); ?>
				<?php comments_template(); ?>
			</div>

			<?php get_sidebar(); ?>
		</div>

		<?php do_action( 'jobify_loop_after' ); ?>
	</div><!-- #primary -->

	<?php endwhile; ?>

<?php get_footer(); ?>
