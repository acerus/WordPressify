<?php if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add possible user tags
 *
 * @param $user_id
 * @param $args
 */
function um_user_tags_display( $user_id, $args ) {
	if ( UM()->fields()->editing == 1 ) {
		return;
	}

	if ( ! um_user( 'show_user_tags' ) ) {
		return;
	}

	$metakey = um_user( 'user_tags_metakey' );
	if ( ! $metakey ) {
		return;
	}

	$value = UM()->User_Tags_API()->get_tags( $user_id, $metakey );
	echo $value;
}
add_action( 'um_after_header_meta', 'um_user_tags_display', 30, 2 );


/**
 * Add possible user tags
 *
 * @param $array
 * @param $key
 * @param $args
 */
function um_add_error_on_form_submit_validation( $array, $key, $args ) {
	if ( isset( $array['type'] ) && $array['type'] == 'user_tags' && isset( $array['required'] ) && $array['required'] == 1 && ! isset( $args[ $key ] ) ) {
		UM()->form()->add_error( $key, sprintf( __( '%s is required.', 'um-user-tags' ), $array['title'] ) );
	}
}
add_action( 'um_add_error_on_form_submit_validation', 'um_add_error_on_form_submit_validation', 10, 3 );


/**
 * Save user tags to profile and update tag count
 *
 * @param $to_update
 */
function um_user_tags_sync_user( $to_update ) {
	global $wpdb;
	$form_id = $_REQUEST['form_id'];

	$filters = get_option( 'um_user_tags_filters' );
	$fields = UM()->query()->get_attr( 'custom_fields', $form_id );

	$user_tags_metakeys = array();
	foreach ( $fields as $metakey => $value ) {
		if ( $value['type'] == 'user_tags' && in_array( $metakey, array_keys( $filters ) ) ) {
			$user_tags_metakeys[ $metakey ] = $filters[ $metakey ];
		}
	}

	if ( empty( $user_tags_metakeys ) )
		return;

	$before_tag_ids = array();
	$after_tag_ids = array();
	foreach ( $user_tags_metakeys as $metakey => $term_id ) {
		$userdata = get_user_meta( um_user( 'ID' ), $metakey, true );
		$userdata = ! $userdata ? array() : $userdata;
		$before_tag_ids = array_merge( $before_tag_ids, $userdata );

		if ( ! empty( $to_update[ $metakey ] ) ) {
			$after_tag_ids = array_merge( $after_tag_ids, $to_update[ $metakey ] );
		} else {
			delete_user_meta( um_user( 'ID' ), $metakey );
		}
	}

	$before_tag_ids = array_unique( $before_tag_ids );
	$after_tag_ids = array_unique( $after_tag_ids );

	$removed_tags = array_diff( $before_tag_ids, $after_tag_ids );
	$added_tags = array_diff( $after_tag_ids, $before_tag_ids );

	foreach ( $removed_tags as $value ) {
		$term = get_term_by( 'id', $value, 'um_user_tag' );
		$wpdb->update(
			$wpdb->term_taxonomy,
			array( 'count' => $term->count - 1 ),
			array( 'term_taxonomy_id' => $term->term_id )
		);
	}

	foreach ( $added_tags as $value ) {
		$term = get_term_by( 'id', $value, 'um_user_tag' );
		$wpdb->update(
			$wpdb->term_taxonomy,
			array( 'count' => $term->count + 1 ),
			array( 'term_taxonomy_id' => $term->term_id )
		);
	}
}
add_action( 'um_user_pre_updating_profile', 'um_user_tags_sync_user', 39 );


/**
 * Update tag count after delete user
 *
 * @param $user_id
 */
function um_tags_on_user_delete( $user_id ) {
	global $wpdb;
	$filters = get_option( 'um_user_tags_filters' );
	$user_meta_keys = array_keys( $filters );

	foreach ( $user_meta_keys as $user_meta_key ) {
		$termsIds = get_user_meta( $user_id, $user_meta_key, true );
		if ( ! empty( $termsIds ) ) {
			foreach ( $termsIds as $id ) {
				$term = get_term_by( 'id', $id, 'um_user_tag' );
				$wpdb->update(
					$wpdb->term_taxonomy,
					array( 'count' => $term->count - 1 ),
					array( 'term_taxonomy_id' => $term->term_id )
				);
			}
		}
	}
}
add_action( 'um_delete_user', 'um_tags_on_user_delete', 10, 1 );