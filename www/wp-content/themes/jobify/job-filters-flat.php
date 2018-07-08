<?php
/**
 * Job Filters Flat
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */
?>
<?php
$atts = apply_filters( 'job_manager_ouput_jobs_default', array(
	'per_page' => get_option( 'job_manager_per_page' ),
	'orderby' => 'featured',
	'order' => 'DESC',
	'show_categories' => true,
	'categories' => true,
	'selected_category' => false,
	'job_types' => false,
	'location' => false,
	'keywords' => false,
	'selected_job_types' => false,
	'show_category_multiselect' => false,
	'selected_region' => false,
) );

global $is_flat;
?>

<?php do_action( 'job_manager_job_filters_before', $atts ); ?>

<form class="job_search_form<?php if ( $is_flat ) : ?> job_search_form--flat<?php endif; ?>" action="<?php echo jobify_get_listing_page_permalink() ? jobify_get_listing_page_permalink() : get_post_type_archive_link( 'job_listing' ); ?>" method="GET">
	<?php do_action( 'job_manager_job_filters_start', $atts ); ?>

	<div class="search_jobs">
		<?php do_action( 'job_manager_job_filters_search_jobs_start', $atts ); ?>

		<div class="search_keywords">
			<label for="search_keywords"><?php _e( 'Keywords', 'jobify' ); ?></label>
			<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php esc_attr_e( 'Keywords', 'jobify' ); ?>" />
		</div>

		<div class="search_location">
			<label for="search_location"><?php _e( 'Location', 'jobify' ); ?></label>
			<input type="text" name="search_location" id="search_location" placeholder="<?php esc_attr_e( 'Location', 'jobify' ); ?>" />
		</div>

		<?php if ( get_option( 'job_manager_enable_categories' ) ) : ?>

		<div class="search_categories">
			<label for="search_categories"><?php _e( 'Category', 'jobify' ); ?></label>
			<?php job_manager_dropdown_categories( array(
				'taxonomy' => 'job_listing_category',
				'hierarchical' => 1,
				'show_option_all' => __( 'Any category', 'jobify' ),
				'name' => 'search_categories',
				'orderby' => 'name',
				'multiple' => false,
			) ); ?>
		</div>

		<?php endif; ?>

		<?php do_action( 'job_manager_job_filters_search_jobs_end', $atts ); ?>
	</div>

	<?php do_action( 'job_manager_job_filters_end', $atts ); ?>
</form>

<?php do_action( 'job_manager_job_filters_after', $atts ); ?>
