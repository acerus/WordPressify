<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@delete multiselect fields
	***/
	add_action('um_admin_before_saving_role_meta', 'um_followers_multi_choice_keys');
	function um_followers_multi_choice_keys( $post_id ){
		delete_post_meta( $post_id, '_um_can_follow_roles' );
	}



    /***
     ***	@creates options in Role page
     ***/
    add_filter( 'um_admin_role_metaboxes', 'um_followers_add_role_metabox', 10, 1 );
    function um_followers_add_role_metabox( $roles_metaboxes ) {

        $roles_metaboxes[] = array(
            'id'        => "um-admin-form-followers{" . um_followers_path . "}",
            'title'     => __( 'Followers', 'um-followers' ),
            'callback'  => array( UM()->metabox(), 'load_metabox_role' ),
            'screen'    => 'um_role_meta',
            'context'   => 'normal',
            'priority'  => 'default'
        );

        return $roles_metaboxes;
    }


/**
 * When user is removed all their following data should be removed
 *
 * @param $user_id
 */
function um_followers_delete_user_data( $user_id ) {
	global $wpdb;

	$wpdb->query( $wpdb->prepare(
		"DELETE 
		FROM {$wpdb->prefix}um_followers 
		WHERE user_id1 = %d OR 
			  user_id2 = %d",
		$user_id,
		$user_id
	) );
}
add_action( 'um_delete_user', 'um_followers_delete_user_data', 10, 1 );


	/***
	***	@sort by highest rated
	***/
	add_filter( 'um_admin_directory_sort_users_select', 'um_followers_sort_user_option', 10, 1 );
	function um_followers_sort_user_option( $options ) {
		$options['most_followed'] = __( 'Most followed', 'um-followers' );
		$options['least_followed'] = __( 'Least followed', 'um-followers' );

		return $options;
	}
