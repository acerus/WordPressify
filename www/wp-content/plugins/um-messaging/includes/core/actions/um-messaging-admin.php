<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@creates options in Role page
	***/
add_filter( 'um_admin_role_metaboxes', 'um_messaging_add_role_metabox', 10, 1 );
function um_messaging_add_role_metabox( $roles_metaboxes ) {

    $roles_metaboxes[] = array(
        'id'        => "um-admin-form-messaging{" . um_messaging_path . "}",
        'title'     => __('Private Messages','um-messaging'),
        'callback'  => array( UM()->metabox(), 'load_metabox_role' ),
        'screen'    => 'um_role_meta',
        'context'   => 'normal',
        'priority'  => 'default'
    );

    return $roles_metaboxes;
}

	
	/***
	***	@admin options in directory
	***/
	add_filter( 'um_admin_extend_directory_options_general', 'um_messaging_admin_directory' );
	function um_messaging_admin_directory( $fields ) {
		$additional_fields = array(
			array(
				'id'		    => '_um_show_pm_button',
				'type'		    => 'checkbox',
				'label'		    => __( 'Show message button in directory?', 'um-messaging' ),
				'value'		    => UM()->query()->get_meta_value( '_um_show_pm_button', null, 1 ),
			)
		);

		return array_merge( $fields, $additional_fields );
	}