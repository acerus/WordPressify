<?php if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;

$terms = get_terms( 'um_user_tag', array(
	'hide_empty' => 0,
) );
$tags = get_option( 'um_user_tags_filters' );
$tags_names = array_keys( $tags );

foreach ( $terms as $term ) {
	$meta_query = array(
		'relation' => 'OR',
	);

	foreach ( $tags_names as $tag ) {
		$meta_query[] = array(
			'key'     => $tag,
			'compare' => 'LIKE',
			'value'   => ':"' . $term->term_id . '";'
		);
	}

	$users = get_users( array(
		'meta_query' => $meta_query,
		'fields'     => 'ids'
	) );
	$count = count( $users );

	$wpdb->update(
		$wpdb->term_taxonomy,
		array( 'count' => $count ),
		array( 'term_taxonomy_id' => $term->term_id )
	);
}