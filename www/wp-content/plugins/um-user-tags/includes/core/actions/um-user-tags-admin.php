<?php if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Creates options in Role page
 *
 * @param $roles_metaboxes
 *
 * @return array
 */
function um_user_tags_add_role_metabox( $roles_metaboxes ) {

	$roles_metaboxes[] = array(
		'id'        => "um-admin-form-user-tags{" . um_user_tags_path . "}",
		'title'     => __( 'User Tags','um-user-tags' ),
		'callback'  => array( UM()->metabox(), 'load_metabox_role' ),
		'screen'    => 'um_role_meta',
		'context'   => 'normal',
		'priority'  => 'default'
	);

	return $roles_metaboxes;
}
add_filter( 'um_admin_role_metaboxes', 'um_user_tags_add_role_metabox', 10, 1 );