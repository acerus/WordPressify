<?php
/**
 * Single Job Listing Meta
 *
 * Hooked into single_job_listing_start priority 20
 *
 * @package Jobify
 * @since  3.0.0
 * @version 3.8.0
 */
global $post;

do_action( 'single_job_listing_meta_before' ); ?>

<ul class="job-listing-meta meta">
	<?php do_action( 'single_job_listing_meta_start' ); ?>

	<?php foreach( jobify_get_the_job_types() as $type ) : ?>
		<li class="job-type <?php echo esc_attr( sanitize_title( $type ? $type->slug : '' ) ); ?>"><?php echo $type->name; ?></li>
	<?php endforeach; ?>

	<li class="location"><?php echo jobify_get_formatted_address(); ?></li>

	<li class="date-posted"><?php echo jobify_get_posted_date(); ?></li>

	<?php if ( jobify_is_listing_position_filled() ) : ?>
		<li class="position-filled"><?php _e( 'This position has been filled', 'jobify' ); ?></li>
	<?php elseif ( ! candidates_can_apply() && 'preview' !== $post->post_status ) : ?>
		<li class="listing-expired"><?php _e( 'Applications have closed', 'jobify' ); ?></li>
	<?php endif; ?>

	<?php do_action( 'single_job_listing_meta_end' ); ?>
</ul>

<?php do_action( 'single_job_listing_meta_after' ); ?>
