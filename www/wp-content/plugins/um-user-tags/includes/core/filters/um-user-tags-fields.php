<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Change how multiselect keys are treated
 *
 * @param $value
 * @param $field_type
 *
 * @return int
 */
function um_user_tags_multiselect_options( $value, $field_type ) {
	if ( $field_type == 'user_tags' ) {
		return 1;
	}

	return 0;
}
add_filter( 'um_multiselect_option_value', 'um_user_tags_multiselect_options', 10, 2 );


/**
 * @param $selected
 * @param $filter
 * @param $val
 * @return bool
 */
function um_selected_if_in_query_params( $selected, $filter, $val ) {
	$query = UM()->permalinks()->get_query_array();

	if ( isset( $query[ $filter ] ) && ! is_numeric( $query[ $filter ] ) ) {
		$tags = get_option( 'um_user_tags_filters' );

		if ( $tags ) {
			$tags = array_unique( array_values( $tags ) );
			if ( in_array( $filter, $tags ) ) {
				$term = get_term_by( 'slug', $query[ $filter ], 'um_user_tag' );
				if ( ! is_wp_error( $term ) ) {
					if ( isset( $query[ $filter ] ) && $val == $term->term_id ) {
						$selected = true;
					}
				}
			}
		}
	}

	return $selected;
}
add_filter( 'um_selected_if_in_query_params', 'um_selected_if_in_query_params', 10, 3 );


/**
 * @param $type
 * @param $attrs
 * @return bool
 */
function um_search_field_type( $type, $attrs ) {
	if ( isset( $attrs['type'] ) && 'user_tags' == $attrs['type'] ) {
		$type = 'select';
	}

	return $type;
}
add_filter( 'um_search_field_type', 'um_search_field_type', 10, 2 );


/**
 * @param $attrs
 * @return bool
 */
function um_user_tags_search_fields( $attrs ) {
	if ( isset( $attrs['type'] ) && 'user_tags' == $attrs['type'] ) {
		$attrs['options'] = apply_filters( 'um_multiselect_options_user_tags', array(), $attrs );
		$attrs['custom']  = 1;
	}

	return $attrs;
}
add_filter( 'um_search_fields', 'um_user_tags_search_fields', 10, 1 );


/**
 * Save our user tags filters
 *
 * @param $args
 * @return mixed
 */
function um_user_tags_assign_new_tags_field( $args ) {

	if ( $args['type'] == 'user_tags' ) {
		$store = get_option( 'um_user_tags_filters' );
		$store = empty( $store ) ? array() : $store;

		$store[ $args['metakey'] ] = $args['tag_source'];
		update_option( 'um_user_tags_filters', $store );
	}

	return $args;
}
add_filter( 'um_admin_pre_save_field_to_form', 'um_user_tags_assign_new_tags_field' );


/**
 * Modify query for filtering
 *
 * @param $query_args
 * @return mixed
 */
function um_user_tags_filter( $query_args ) {
	$tags = get_option( 'um_user_tags_filters' );
	if ( ! $tags )
		return $query_args;

	$tags = array_keys( $tags );

	$i = 0;
	foreach ( $tags as $metakey ) {
		if (isset($_REQUEST[$metakey]) && sanitize_key($_REQUEST[$metakey]) && isset($_REQUEST['um_search'])) {
			$term_id = $_REQUEST[$metakey];
			$term_field = is_numeric($term_id) ? 'id' : 'slug';
			$term = get_term_by($term_field, $term_id, 'um_user_tag');
			$term_slug = $term->term_id;
			if (!is_numeric($term_id)) {
				foreach ($query_args['meta_query'] as $key => $val) {
					if (!empty($val[0]['key']) && $val[0]['key'] == $metakey) {
						$query_args['meta_query'][$key] = array(
							'relation' => 'OR',
							array(
								'key'     => $metakey,
								'value'   => $term_slug,
								'compare' => '=',
							),
							array(
								'key'     => $metakey,
								'value'   => serialize(strval($term_slug)),
								'compare' => 'LIKE',
							),
							array(
								'key'     => $metakey,
								'value'   => $term_slug,
								'compare' => 'LIKE',
							)
						);
					}
				}
			}


			$i++;
			UM()->User_Tags_API()->filters[$metakey] = sanitize_key($term_id);

		}
	}

	if ( $i > 0 ) {
		UM()->is_filtering = 1;
	}
	return $query_args;
}
add_filter( 'um_query_args_filter', 'um_user_tags_filter' );


/**
 * @param $arr_field_types
 *
 * @return array
 */
function um_usertags_search_filter_field_types( $arr_field_types ) {
	$arr_field_types[] = 'user_tags';
	return $arr_field_types;
}
add_filter( 'um_search_filter_field_types', 'um_usertags_search_filter_field_types', 10, 1 );


/**
 * Outputs user tags
 *
 * @param $value
 * @param $data
 *
 * @return string
 */
function um_profile_field_filter_hook__user_tags( $value, $data ) {
	$metakey = $data['metakey'];
	$value = UM()->User_Tags_API()->get_tags( um_user( 'ID' ), $metakey );
	return $value;
}
add_filter( 'um_profile_field_filter_hook__user_tags', 'um_profile_field_filter_hook__user_tags', 99, 2 );


/**
 * Dynamically change field type
 *
 * @param $type
 *
 * @return string
 */
function um_hook_for_field_user_tags( $type ) {
	return 'multiselect';
}
add_filter( 'um_hook_for_field_user_tags', 'um_hook_for_field_user_tags' );


/**
 * Get custom user tags
 *
 * @param $options
 * @param $data
 *
 * @return array
 */
function um_multiselect_options_user_tags( $options, $data ) {

	$tag_source = $data['tag_source'];

	$tags = get_terms( 'um_user_tag', array(
		'hide_empty' => 0,
		'child_of'   => $tag_source
	) );

	if ( ! $tags )
		return array('');

	$options = array();

	foreach ( $tags as $term ) {
		$id = $term->term_id;
		$options[ $id ] = $term->name;
	}
	return $options;
}
add_filter( 'um_multiselect_options_user_tags', 'um_multiselect_options_user_tags', 100, 2 );


/**
 * @param $use_keyword
 * @param $type
 *
 * @return bool
 */
function um_multiselect_option_value_user_tags( $use_keyword, $type ) {

	if ( $type == 'user_tags' ) {
		return true;
	}

	return $use_keyword;
}
add_filter( 'um_multiselect_option_value', 'um_multiselect_option_value_user_tags', 10, 2 );


/**
 * Extend core fields
 *
 * @param $fields
 *
 * @return mixed
 */
function um_user_tags_add_field( $fields ) {

	$fields['user_tags'] = array(
		'name'     => __( 'User Tags', 'um-user-tags' ),
		'col1'     => array( '_title', '_metakey', '_help', '_visibility', '_public', '_roles' ),
		'col2'     => array( '_label', '_max_selections', '_tag_source' ),
		'col3'     => array( '_required', '_editable', '_icon' ),
		'validate' => array(
			'_title'   => array(
				'mode'  => 'required',
				'error' => __( 'You must provide a title', 'um-user-tags' )
			),
			'_metakey' => array(
				'mode' => 'unique',
			),
		)
	);

	return $fields;

}
add_filter( "um_core_fields_hook", 'um_user_tags_add_field', 10 );


/**
 * Do not require a metakey
 *
 * @param $array
 *
 * @return array
 */
function um_user_tags_requires_no_metakey( $array ) {
	$array[] = 'user_tags';

	return $array;
}
add_filter( 'um_fields_without_metakey', 'um_user_tags_requires_no_metakey' );


/**
 * Update user tag's count
 *
 * @param $results
 *
 * @return mixed
 */
/*function um_update_user_tags_count( $results ) {
	global $wpdb;

	if ( UM()->User_Tags_API()->filters ) {
		foreach ( UM()->User_Tags_API()->filters as $metakey => $term_id ) {
			$term = get_term_by( 'id', $term_id, 'um_user_tag' );
			if ( isset( $term->term_id ) ) {
				$wpdb->update(
					$wpdb->term_taxonomy,
					array( 'count' => $results['total_users'] ),
					array( 'term_taxonomy_id' => $term->term_id )
				);
			}
		}
	}

	return $results;
}
add_filter( 'um_prepare_user_results_array', 'um_update_user_tags_count' );*/