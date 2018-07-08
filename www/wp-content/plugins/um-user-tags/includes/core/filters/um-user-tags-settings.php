<?php if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Extend settings
 *
 * @param $settings
 *
 * @return mixed
 */
function um_user_tags_settings( $settings ) {
	$settings['licenses']['fields'][] = array(
		'id'        => 'um_user_tags_license_key',
		'label'     => __( 'User Tags License Key', 'um-user-tags' ),
		'item_name' => 'User Tags',
		'author'    => 'Ultimate Member',
		'version'   => um_user_tags_version,
	);

	$key = ! empty( $settings['extensions']['sections'] ) ? 'user_tags' : '';
	$settings['extensions']['sections'][ $key ] = array(
		'title'     => __( 'User Tags', 'um-user-tags' ),
		'fields'    => array(
			array(
				'id'            => 'user_tags_max_num',
				'type'          => 'text',
				'label'         => __( 'Maximum number of tags to display in user profile','um-user-tags' ),
				'validate'      => 'numeric',
				'descriptions'  => __('Remaining tags will appear by clicking on a link','um-user-tags'),
				'size'          => 'small'
			),
		)
	);

	return $settings;
}
add_filter( 'um_settings_structure', 'um_user_tags_settings', 10, 1 );