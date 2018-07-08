<?php
/**
 * Sidebar Single Job Listing Top
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */
if ( 'side' == esc_attr( get_theme_mod( 'job-display-sidebar', 'top' ) ) ) {
	return;
}

$args    = array(
	'before_widget' => '<aside class="widget widget--job_listing widget--job_listing-top">',
	'after_widget'  => '</aside>',
	'before_title'  => '<h3 class="widget-title widget-title--job_listing widget-title--job_listing-top">',
	'after_title'   => '</h3>',
);

$count = get_theme_mod( 'job-display-sidebar-columns', 3 );
$columns = floor( 12 / $count );
?>

<div class="job-meta-top row">

	<?php do_action( 'single_job_listing_info_before' ); ?>

	<div class="col-md-<?php echo 3 == $count ? '3' : $columns; ?> col-sm-6 col-xs-12">
		<?php
		if ( ! is_active_sidebar( 'single-job_listing-top-1' ) ) {
			the_widget( 'Jobify_Widget_Job_Company_Logo', array(), $args );
		} else {
			dynamic_sidebar( 'single-job_listing-top-1' );
		}
		?>
	</div>

	<?php if ( $count > 1 ) : ?>

	<div class="col-md-<?php echo 3 == $count ? '4' : $columns; ?> col-sm-6 col-xs-12">
		<?php
		if ( ! is_active_sidebar( 'single-job_listing-top-2' ) ) {
			the_widget( 'Jobify_Widget_Job_Categories', array(
				'title' => __( 'Category', 'jobify' ),
			), $args );

			if ( class_exists( 'WP_Job_Manager_Job_Tags' ) ) {
				the_widget( 'Jobify_Widget_Job_Tags', array(
					'title' => __( 'Tags', 'jobify' ),
				), $args );
			}

			if ( jobify()->get( 'jetpack' ) ) {
				the_widget( 'Jobify_Widget_Job_Share', array(
					'title' => __( 'Share This', 'jobify' ),
				), $args );
			}
		} else {
			dynamic_sidebar( 'single-job_listing-top-2' );
		}
		?>
	</div>

	<?php endif; if ( $count > 2 ) : ?>

	<div class="col-md-<?php echo 3 == $count ? '5' : $columns; ?> col-sm-6 col-xs-12">
		<?php
		if ( ! is_active_sidebar( 'single-job_listing-top-3' ) ) {
			the_widget( 'Jobify_Widget_Job_Company_Social', array(
				'title' => __( 'Company Details', 'jobify' ),
			), $args );
			the_widget( 'Jobify_Widget_Job_Apply', array(), $args );
		} else {
			dynamic_sidebar( 'single-job_listing-top-3' );
		}
		?>
	</div>

	<?php endif; if ( $count > 3 ) : ?>
		
	<div class="col-md-<?php echo 3 == $count ? '5' : $columns; ?> col-sm-6 col-xs-12">
		<?php dynamic_sidebar( 'single-job_listing-top-4' ); ?>
	</div>

	<?php endif; ?>

	<?php do_action( 'single_job_listing_info_after' ); ?>

</div>
