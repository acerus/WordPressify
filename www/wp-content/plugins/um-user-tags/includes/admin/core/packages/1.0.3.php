<?php
if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;

$tags_set = get_option( 'um_user_tags_filters' );

if ( ! empty( $tags_set ) && is_array( $tags_set ) ) {
	$new_tags_set = array_flip( $tags_set );

	$forms = get_posts( array(
	    'post_type' => 'um_form',
	    'numberposts' => -1,
	    'fields' => 'ids'
	) );

	foreach( $forms as $form_id ) {
	    $custom_fields = get_post_meta( $form_id, '_um_custom_fields', true );

	    if ( is_array( $custom_fields ) ) {
	        foreach ( $custom_fields as $key => $field_data ) {
	            if ( 'user_tags' == $field_data['type'] && ! in_array( $field_data['metakey'], array_keys( $new_tags_set ) ) ) {
	                $new_tags_set[ $field_data['metakey'] ] = $field_data['tag_source'];
	            }
	        }
	    }
	}

	update_option( 'um_user_tags_filters', $new_tags_set );




	$um_user_tags = get_terms( 'um_user_tag', array( 'hide_empty' => false ) );
	$arr_user_tags = array();
	foreach ( $um_user_tags as $key => $um_tags ) {
		if ( ! isset( $arr_user_tags[ $um_tags->slug ] ) ) {
			$arr_user_tags[ $um_tags->slug ] = $um_tags->term_id;
		}
	}

	foreach ( $new_tags_set as $tag => $id ) {

		$user_tag_values = $wpdb->get_results( $wpdb->prepare(
			"SELECT user_id,
                    meta_value 
            FROM $wpdb->usermeta 
            WHERE meta_key = %s",
			$tag
		) );

		foreach ( $user_tag_values as $meta ) {
			$utags = unserialize( $meta->meta_value );

			if ( isset( $utags ) ) {
				$arr_update_tags = array();
				foreach ( $utags as $utag ) {
					if ( isset( $arr_user_tags[ $utag ] ) && ! is_int( $utag ) ) {
						$arr_update_tags[] = $arr_user_tags[ $utag ];
					}
				}
				if ( ! empty( $arr_update_tags ) ) {
					update_user_meta( $meta->user_id, $tag, $arr_update_tags );
				}
			}
		}

	}
}