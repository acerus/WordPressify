<?php
/**
 * Job Content
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */

global $post;

$info         = get_theme_mod( 'job-display-sidebar', 'top' );
$col_overview = 'top' == $info ? '12' : ( ! jobify_get_the_company_description() ? '10' : '6' );
$col_company  = 'top' == $info ? '12' : '4';
?>

<div class="single_job_listing">

	<div class="page-header">
		<h1 class="page-title">
			<?php the_title(); ?>
		</h1>
		<h3 class="page-subtitle">
			<?php do_action( 'single_job_listing_start' ); ?>
		</h3>
	</div>

	<div id="content" class="container content-area" role="main">

		<?php if ( get_option( 'job_manager_hide_expired_content', 1 ) && 'expired' === $post->post_status ) : ?>
			<div class="job-manager-info"><?php _e( 'This job listing has expired', 'jobify' ); ?></div>
		<?php else : ?>

			<?php locate_template( array( 'sidebar-single-job_listing-top.php' ), true, false ); ?>

			<div class="job-overview-content row">
				<div class="job_listing-description job-overview col-md-<?php echo $col_overview; ?> col-sm-12">
					<h2 class="widget-title widget-title--job_listing-top job-overview-title"><?php _e( 'Overview', 'jobify' ); ?></h2>
					<?php echo apply_filters( 'the_job_description', get_the_content() ); ?>

					<?php if ( candidates_can_apply() && apply_filters( 'jobify_single_job_listing_apply_button', true ) ) : ?>
						<?php get_job_manager_template( 'job-application.php' ); ?>
					<?php endif; ?>
				</div>

				<?php if ( jobify_get_the_company_description() ) : ?>
				<div class="job_listing-company-description job-company-about col-md-<?php echo $col_company; ?> <?php echo 'top' == $info ? 'col-md-12' : 'col-sm-6 col-xs-12'; ?>">
					<h2 class="widget-title widget-title--job_listing-top job-overview-title"><?php printf( __( 'About %s', 'jobify' ), jobify_get_the_company_name() ); ?></h2>
					<?php jobify_the_company_description(); ?>
				</div>
				<?php endif; ?>

				<?php locate_template( array( 'sidebar-single-job_listing.php' ), true, false ); ?>
			</div>

			<?php do_action( 'single_job_listing_end' ); ?>

		<?php endif; ?>
	</div>

	<?php get_template_part( 'content-single-job', 'related' ); ?>

</div>
