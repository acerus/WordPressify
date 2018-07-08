<?php
/**
 * 404
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */

get_header(); ?>

	<header class="page-header">
		<h2 class="page-title"><?php _e( 'Page Not Found', 'jobify' ); ?></h2>
	</header>

	<div id="primary" class="content-area container" role="main">
		<div class="blog-archive">
			<?php get_template_part( 'content', 'none' ); ?>
		</div>

		<?php do_action( 'jobify_loop_after' ); ?>
	</div><!-- #primary -->

<?php get_footer(); ?>
