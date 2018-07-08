<?php
/**
 * Job Listing Content
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */
?>
<li id="job_listing-<?php the_ID(); ?>" <?php jobify_listing_html_class(); ?> <?php echo apply_filters( 'jobify_listing_data', '' ); ?>>
	<a href="<?php jobify_listing_permalink(); ?>" class="job_listing-clickbox"></a>

	<div class="job_listing-logo">
		<?php jobify_the_company_logo( 'thumbnail' ); ?>
	</div><div class="job_listing-about">

		<div class="job_listing-position job_listing__column">
			<h3 class="job_listing-title"><?php the_title(); ?></h3>

			<div class="job_listing-company">
				<?php jobify_the_company_name( '<strong>', '</strong> ' ); ?>
				<?php jobify_the_company_tagline( '<span class="job_listing-company-tagline">', '</span>' ); ?>
			</div>
		</div>

		<div class="job_listing-location job_listing__column">
			<?php echo jobify_get_formatted_address(); ?>
		</div>

		<ul class="job_listing-meta job_listing__column">
			<?php do_action( 'job_listing_meta_start' ); ?>

			<?php foreach( jobify_get_the_job_types() as $type ) : ?>
				<li class="job_listing-type job-type <?php echo esc_attr( sanitize_title( $type ? $type->slug : '' ) ); ?>"><?php echo $type->name; ?></li>
			<?php endforeach; ?>

			<li class="job_listing-date"><?php echo jobify_get_posted_date(); ?></li>

			<?php do_action( 'job_listing_meta_end' ); ?>
		</ul>

	</div>
</li>
