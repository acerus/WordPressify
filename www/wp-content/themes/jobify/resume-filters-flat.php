<?php
/**
 * Resume Filters Flat
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */
?>
<?php
$atts = apply_filters( 'jobify_resume_filters_flat', array() );
?>

<?php do_action( 'resume_manager_resume_filters_before', $atts ); ?>

<form class="resume_search_form resume_search_form--flat" action="<?php echo resume_manager_get_permalink( 'resumes' ) ? resume_manager_get_permalink( 'resumes' ) : get_post_type_archive_link( 'resume' ); ?>" action="GET">

	<div class="search_resumes">
		<?php do_action( 'resume_manager_resume_filters_search_resumes_start', $atts ); ?>

		<div class="search_keywords resume-filter">
			<label for="search_keywords"><?php _e( 'Keywords', 'jobify' ); ?></label>
			<input type="text" name="search_keywords" id="search_keywords" placeholder="<?php _e( 'All Resumes', 'jobify' ); ?>" />
		</div>

		<div class="search_location resume-filter">
			<label for="search_location"><?php _e( 'Location', 'jobify' ); ?></label>
			<input type="text" name="search_location" id="search_location" placeholder="<?php _e( 'Any Location', 'jobify' ); ?>" />
		</div>

		<?php if ( get_option( 'resume_manager_enable_categories' ) ) : ?>

		<div class="search_categories resume-filter">
			<label for="search_categories"><?php _e( 'Category', 'jobify' ); ?></label>
				<?php wp_dropdown_categories( array(
					'taxonomy' => 'resume_category',
					'hierarchical' => 1,
					'show_option_all' => __( 'Any category', 'jobify' ),
					'name' => 'search_categories',
					'orderby' => 'name',
				) ); ?>
		</div>

		<?php endif; ?>

		<?php do_action( 'resume_manager_resume_filters_search_resumes_end', $atts ); ?>
	</div>
</form>

<?php do_action( 'resume_manager_resume_filters_after', $atts ); ?>
