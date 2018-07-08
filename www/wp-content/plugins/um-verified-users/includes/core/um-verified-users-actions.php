<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Bulk verify
 *
 * @param $user_id
 */
function um_admin_custom_hook_um_verify_accounts( $user_id ) {
	if ( ! UM()->Verified_Users_API()->api()->is_verified( $user_id ) ) {
		UM()->Verified_Users_API()->api()->verify( $user_id, true );
	}
}
add_action( 'um_admin_custom_hook_um_verify_accounts', 'um_admin_custom_hook_um_verify_accounts', 10 );


/**
 * Bulk unverify
 *
 * @param $user_id
 */
function um_admin_custom_hook_um_unverify_accounts( $user_id ) {
	UM()->Verified_Users_API()->api()->unverify( $user_id );
}
add_action( 'um_admin_custom_hook_um_unverify_accounts', 'um_admin_custom_hook_um_unverify_accounts', 10 );


/**
 * Verify user in backend
 *
 * @param $action
 */
function um_admin_do_action__verify_user( $action ) {
	if (!is_admin() || !current_user_can( 'edit_user' )) die();
	if (!isset( $_REQUEST['uid'] ) || !is_numeric( $_REQUEST['uid'] )) die();

	$user_id = (int)$_REQUEST['uid'];
	UM()->Verified_Users_API()->api()->verify( $user_id, true );

	exit( wp_redirect( admin_url( 'users.php?update=users_updated' ) ) );
}
add_action( 'um_admin_do_action__verify_user', 'um_admin_do_action__verify_user' );


/**
 * Unverify user in backend
 *
 * @param $action
 */
function um_admin_do_action__unverify_user( $action ) {
	if (!is_admin() || !current_user_can( 'edit_user' )) die();
	if (!isset( $_REQUEST['uid'] ) || !is_numeric( $_REQUEST['uid'] )) die();

	$user_id = (int)$_REQUEST['uid'];
	UM()->Verified_Users_API()->api()->unverify( $user_id );

	exit( wp_redirect( admin_url( 'users.php?update=users_updated' ) ) );
}
add_action( 'um_admin_do_action__unverify_user', 'um_admin_do_action__unverify_user' );


/**
 * Add verification info to profile
 *
 * @param $user_id
 * @param $args
 */
function um_verified_info( $user_id, $args ) {

	if (um_profile_id() != get_current_user_id())
		return;

	if (um_user( 'verified_req_disallowed' ))
		return;

	$user_status = UM()->Verified_Users_API()->api()->verified_status( $user_id );

	if ( $user_status == 'unverified' ) {
		echo '<div class="um-verified-info"><a href="' . UM()->Verified_Users_API()->api()->verify_url( $user_id, um_user_profile_url() ) . '" class="um-link um-verified-request-link">' . __( 'Request Verification', 'um-verified' ) . '</a></div>';
	} elseif ( $user_status == 'pending' ) {
		$cancel = UM()->Verified_Users_API()->api()->verify_cancel_url( $user_id, um_user_profile_url() );
		echo '<div class="um-verified-info">' . sprintf( __( 'Your verification request is currently pending. <a href="%s" class="um-verified-cancel-request">Cancel request?</a>', 'um-verified' ), $cancel ) . '</div>';
	}
}
add_action( 'um_after_header_meta', 'um_verified_info', 50, 2 );


/**
 * Add verification info to account
 *
 * @param $shortcode_args
 */
function um_verified_account_info( $shortcode_args ) {
	if ( isset( $shortcode_args['_verified_info'] ) && 0 == $shortcode_args['_verified_info'] )
		return;

	$user_id = um_user( 'ID' );

	if ( UM()->Verified_Users_API()->api()->is_verified( $user_id ) ) return;

	if ( um_user( 'verified_req_disallowed' ) )
		return;

	echo '<div class="um-field">';

	echo '<div class="um-field-label"><label>' . __( 'Get Verified', 'um-verified' ) . '</label><div class="um-clear"></div></div>';

	$verified_status = UM()->Verified_Users_API()->api()->verified_status( $user_id );

	if ( $verified_status == 'unverified') {
		echo '<div class="um-verified-info"><a href="' . UM()->Verified_Users_API()->api()->verify_url( $user_id, um_get_core_page( 'account' ) ) . '" class="um-link um-verified-request-link">' . __( 'Request Verification', 'um-verified' ) . '</a></div>';
	} elseif ( $verified_status == 'pending' ) {
		$cancel = UM()->Verified_Users_API()->api()->verify_cancel_url( $user_id, um_get_core_page( 'account' ) );
		echo '<div class="um-verified-info">' . sprintf( __( 'Your verification request is currently pending. <a href="%s" class="um-verified-cancel-request">Cancel request?</a>', 'um-verified' ), $cancel ) . '</div>';
	}

	echo '</div>';
}
add_action( 'um_after_account_general', 'um_verified_account_info', 10, 1 );


/**
 * Clear pending queue in backend
 *
 * @param $user_id
 */
function um_verified_cached_queue_clear( $user_id ) {
	delete_option( 'um_cached_users_queue' );
}
add_action( 'um_after_user_request_verification', 'um_verified_cached_queue_clear' );
add_action( 'um_after_user_undo_request_verification', 'um_verified_cached_queue_clear' );
add_action( 'um_after_user_is_verified', 'um_verified_cached_queue_clear' );
add_action( 'um_after_user_is_unverified', 'um_verified_cached_queue_clear' );


/**
 * Creates user_meta "_um_verified" for sorted
 *
 * @param $user_id
 */
function um_verified_add_meta_um_verified( $user_id ) {
	update_user_meta( $user_id, '_um_verified', 'unverified' );
}
add_action( 'um_before_save_registration_details', 'um_verified_add_meta_um_verified', 10, 1 );


/**
 * Creates user_meta "_um_verified" for sorted
 *
 * @param $user_id
 */
function um_verified_registration_complete( $user_id, $args ) {
	$user_role = UM()->roles()->get_priority_user_role( $user_id );
	$permissions = UM()->roles()->role_data( $user_role );
	$permissions = apply_filters( 'um_user_permissions_filter', $permissions, $user_id );

	if ( isset( $permissions['verified_by_role'] ) && $permissions['verified_by_role'] ) {
		UM()->Verified_Users_API()->api()->verify( $user_id, true );
	} else {
		UM()->Verified_Users_API()->api()->unverify( $user_id );
	}
}
add_action( 'um_registration_complete', 'um_verified_registration_complete', 10, 2 );


/**
 *
 */
function um_request_verification() {

	if ( ! is_user_logged_in() )
		return;

	if ( isset( $_REQUEST['request_verification'] ) && isset( $_REQUEST['uid'] ) ) {
		$user_id = absint( $_REQUEST['uid'] );

		if ( $user_id != get_current_user_id() || UM()->Verified_Users_API()->api()->verified_status( $user_id ) != 'unverified' )
			wp_die( __('Unauthorized.','um-verified') );

		um_fetch_user( $user_id );

		if ( um_user('verified_req_disallowed') )
			wp_die( __('You are not allowed to do this action.','um-verified') );

		update_user_meta( $user_id, '_um_verified', 'pending' );
		do_action( 'um_after_user_request_verification', $user_id );


		$emails = um_multi_admin_email();
		if ( ! empty( $emails ) ) {
			foreach ( $emails as $email ) {
				UM()->mail()->send( $email, 'verification_request', array(
					'tags' => array('{verify_approve}','{verify_reject}'),
					'tags_replace' => array( UM()->Verified_Users_API()->api()->verify_user_url( $user_id ), UM()->Verified_Users_API()->api()->unverify_user_url( $user_id ) )
				) );
			}
		}

		exit( wp_redirect( esc_attr( $_REQUEST['redirect_to'] ) ) );
	}

	if ( isset( $_REQUEST['request_verification_undo'] ) && isset( $_REQUEST['uid'] ) ) {
		$user_id = absint( $_REQUEST['uid'] );

		if ( $user_id != get_current_user_id() || UM()->Verified_Users_API()->api()->verified_status( $user_id ) != 'pending' )
			wp_die( __('Unauthorized.','um-verified') );

		um_fetch_user( $user_id );

		update_user_meta( $user_id, '_um_verified', 'unverified' );
		do_action('um_after_user_undo_request_verification', $user_id );

		exit( wp_redirect( esc_attr( $_REQUEST['redirect_to'] ) ) );
	}

}
add_action( 'init', 'um_request_verification', 5 );


/**
 * @param $user_id
 */
function um_update_verification_field( $user_id ) {
	if ( current_user_can( 'edit_user', $user_id ) && isset( $_POST['um_set_verification'] ) ) {

		$user = get_userdata( $user_id );

		$state = (int) UM()->Verified_Users_API()->api()->is_verified( $user_id );

		if ( $_POST['um_set_verification'] == 1 && $state == 0 ) {
			UM()->Verified_Users_API()->api()->verify( $user_id );
		} else if ( $state == 1 && $_POST['um_set_verification'] == 0 ) {
			UM()->Verified_Users_API()->api()->unverify( $user_id );
		}
	}
}
add_action( 'personal_options_update',  'um_update_verification_field', 10, 1 );
add_action( 'edit_user_profile_update', 'um_update_verification_field', 10, 1 );


/**
 * Save user group as verified accounts one time
 *
 * @param $post_id
 * @param $post
 */
function um_admin_before_save_role( $post_id, $post ) {

	$v = absint( $_POST['_um_verified_by_role'] );
	if ( $v == 1 && ! get_option('um_verified_' . $post->post_name ) ) {

		$args = array( 'fields' => 'ID', 'number' => 0 );
		$args['meta_query'][] = array( array( 'key' => 'role', 'value' => $post->post_name, 'compare' => '=' ) );

		$users = new WP_User_Query( $args );

		if ( isset( $users->results ) ) {
			foreach( $users->results as $user_id ) {
				UM()->Verified_Users_API()->api()->verify( $user_id );
			}
		}

		update_option( 'um_verified_'. $post->post_name, 'done' );

	}

}
add_action( 'um_admin_before_save_role', 'um_admin_before_save_role', 1000, 2 );


/**
 * Auto-verify role's account
 *
 * @param $user_id
 * @param $role
 */
function um_after_user_role_is_updated( $user_id, $role ) {
	$meta = UM()->roles()->role_data( $role );
	$meta = apply_filters('um_user_permissions_filter', $meta, $user_id);
	if ( isset( $meta['verified_by_role'] ) && $meta['verified_by_role'] ) {
		UM()->Verified_Users_API()->api()->verify( $user_id );
	} else {
		UM()->Verified_Users_API()->api()->unverify( $user_id );
	}
}
add_action( 'um_after_user_role_is_updated', 'um_after_user_role_is_updated', 1000, 2 );