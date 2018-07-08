<?php
/**
 * Search
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */

get_header(); ?>

	<header class="page-header">
		<h2 class="page-title">
			<?php echo get_search_query(); ?>
		</h2>
	</header>

	<div id="primary" class="content-area">
		<div id="content" class="container" role="main">

			<div class="blog-archive row">
				<div class="col-md-<?php echo is_active_sidebar( 'sidebar-blog' ) ? '9' : '12'; ?> col-xs-12">
					<?php if ( have_posts() ) : ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'content', get_post_format() ); ?>
						<?php endwhile; ?>
					<?php else : ?>
						<?php get_template_part( 'content', 'none' ); ?>
					<?php endif; ?>
				</div>

				<?php get_sidebar(); ?>
			</div>

		</div><!-- #content -->

		<?php do_action( 'jobify_loop_after' ); ?>
	</div><!-- #primary -->

<?php get_footer(); ?>
