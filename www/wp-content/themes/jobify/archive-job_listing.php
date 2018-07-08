<?php
/**
 * Job Archives
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */

get_header(); ?>

	<header class="page-header">
		<h2 class="page-title"><?php echo apply_filters( 'jobify_job_archives_title', __( 'All Jobs', 'jobify' ) ); ?></h2>
	</header>

	<div id="primary" class="content-area">
		<div id="content" class="container" role="main">
			<div class="entry-content">
				<?php do_action( 'jobify_output_job_results' ); ?>
			</div>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>
