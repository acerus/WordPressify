<?php
/**
 * Sidebar Single Job Listing
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */

if ( 'top' == get_theme_mod( 'job-display-sidebar', 'top' ) ) {
	return;
}

$args = array(
	'before_widget' => '<aside class="widget widget--job_listing">',
	'after_widget'  => '</aside>',
	'before_title'  => '<h3 class="widget-title widget-title--job_listing">',
	'after_title'   => '</h3>',
);
?>

<?php do_action( 'single_job_listing_info_start' ); ?>

<div class="job-meta col-md-2 col-sm-6 col-xs-12">

	<?php do_action( 'single_job_listing_info_before' ); ?>

	<?php if ( ! is_active_sidebar( 'sidebar-single-job_listing' ) ) : ?>

		<?php
			the_widget( 'Jobify_Widget_Job_Company_Logo', array(), $args );
			the_widget( 'Jobify_Widget_Job_Type', array(), $args );
			the_widget( 'Jobify_Widget_Job_Apply', array(), $args );
			the_widget( 'Jobify_Widget_Job_Company_Social', array(
				'title' => __( 'Company Social', 'jobify' ),
			), $args );
			the_widget( 'Jobify_Widget_Job_Categories', array(), $args );
		?>

	<?php else : ?>
		<?php dynamic_sidebar( 'sidebar-single-job_listing' ); ?>
	<?php endif; ?>

	<?php do_action( 'single_job_listing_info_after' ); ?>

</div>

<?php do_action( 'single_job_listing_info_end' ); ?>
