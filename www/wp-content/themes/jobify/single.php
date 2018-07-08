<?php
/**
 * Single Post
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

	<div id="content" class="container content-area" role="main">

		<div class="row">
			<div class="col-md-<?php echo is_active_sidebar( 'sidebar-blog' ) ? '9' : '12'; ?> col-xs-12">
				<?php get_template_part( 'content', 'single' ); ?>
				<?php comments_template(); ?>
			</div>

			<?php get_sidebar(); ?>
		</div>

	</div><!-- #content -->

	<?php do_action( 'jobify_loop_after' ); ?>

	<?php endwhile; ?>

<?php get_footer(); ?>
