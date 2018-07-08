<?php
/**
 * Resume Content
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */
?>
<li id="resume-<?php the_ID(); ?>" <?php resume_class(); ?> <?php echo apply_filters( 'jobify_listing_data', '' ); ?>>
	<a href="<?php the_resume_permalink(); ?>" class="resume-clickbox"></a>

	<div class="resume-logo">
		<?php the_candidate_photo( 'large' ); ?>
	</div><div class="resume-about">
		<div class="resume-candidate resume__column">
			<h3 class="resume-title"><?php the_title(); ?></h3>

			<div class="resume-candidate-title">
				<?php the_candidate_title( '<strong>', '</strong> ' ); ?>
			</div>
		</div>

		<div class="resume-location resume__column">
			<?php the_candidate_location( false ); ?>
		</div>

		<ul class="resume-meta resume__column">
			<li class="resume-category"><?php the_resume_category(); ?></li>
			<li class="resume-date"><?php printf( __( 'Updated %s ago', 'jobify' ), human_time_diff( get_the_modified_time( 'U' ), current_time( 'timestamp' ) ) ); ?></li>
		</ul>
	</div>
</li>
