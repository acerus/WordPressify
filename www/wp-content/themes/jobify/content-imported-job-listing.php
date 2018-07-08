<?php
/**
 * Imported Job Listing
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */
?>
<li id="job_listing-<?php the_ID(); ?>" class="job_listing" data-longitude="<?php echo esc_attr( $job->longitude ); ?>" data-latitude="<?php echo esc_attr( $job->latitude ); ?>" data-title="<?php echo $job->title; ?>" data-href="<?php echo $job->url; ?>">

	<a href="<?php echo esc_url( $job->url ); ?>" target="_blank" <?php echo $link_attributes; ?> class="job_listing-clickbox"></a>

	<div class="job_listing-logo">
		<img class="company_logo" src="<?php echo esc_url( $job->logo ); ?>" alt="" />
	</div><div class="job_listing-about">

		<div class="job_listing-position job_listing__column">
			<h3 class="job_listing-title"><?php echo esc_html( $job->title ); ?></h3>

			<div class="job_listing-company">
				<strong><?php echo esc_html( $job->company ); ?></strong>
				<span class="job_listing-company-tagline"><?php echo esc_html( $job->tagline ); ?></span>
			</div>
		</div>

		<div class="job_listing-location job_listing__column">
			<?php echo esc_html( $job->location ); ?>
		</div>

		<ul class="job_listing-meta job_listing__column">
			<?php if ( $job->type ) : ?>
			<li class="job_listing-type job-type <?php echo esc_attr( $job->type_slug ); ?>"><?php echo esc_html( $job->type ); ?></li>
			<?php endif; ?>
			<li class="job_listing-date"><?php echo jobify_get_posted_date( $job->timestamp ); ?></li>
		</ul>

	</div>

</li>
