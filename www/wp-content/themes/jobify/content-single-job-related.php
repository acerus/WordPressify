<?php
/**
 * Single Job Related Content
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.1
 */

global $post;

$tax = jobify()->get( 'wp-job-manager-tags' ) ? 'job_listing_tag' : 'job_listing_category';
$tags = get_the_terms( $post->ID, $tax );

if ( ! $tags || is_wp_error( $tags ) || ! is_array( $tags ) ) {
	return;
}

$tags = wp_list_pluck( $tags, 'term_id' );

$related_args = array(
	'post_type' => 'job_listing',
	'orderby'   => 'rand',
	'posts_per_page' => 3,
	'post_status' => 'publish',
	'post__not_in' => array( $post->ID ),
	'tax_query' => array(
		array(
			'taxonomy' => $tax,
			'field'    => 'id',
			'terms'    => $tags,
		),
	),
);
if ( 1 === absint( get_option( 'job_manager_hide_filled_positions' ) ) ) {
	$related_args['meta_query'][] = array(
		'key'     => '_filled',
		'value'   => '1',
		'compare' => '!=',
	);
}

$related = new WP_Query( apply_filters( 'jobify_related_job_args', $related_args ) );

if ( ! $related->have_posts() ) {
	return;
}
?>

<div class="related-jobs container">

	<h3 class="widget-title widget--title-job_listing-top"><?php _e( 'Related Jobs', 'jobify' ); ?></h2>

	<ul class="job_listings related">

		<?php while ( $related->have_posts() ) : $related->the_post(); ?>

			<?php get_job_manager_template_part( 'content', 'job_listing' ); ?>

		<?php endwhile; ?>

	</ul>

</div>

<?php wp_reset_query(); ?>
