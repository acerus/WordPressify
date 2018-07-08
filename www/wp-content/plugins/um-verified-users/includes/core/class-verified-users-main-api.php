<?php
namespace um_ext\um_verified_users\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Verified_Users_Main_API {

	function __construct() {


	}


	/**
	 * Number of pending requests
	 */
	function verified_requests_count() {
		$users = new \WP_User_Query( array(
			'fields'        => 'ID',
			'number'        => 0,
			'meta_query'    => array(
				array(
					'key'       => '_um_verified',
					'value'     => 'pending',
					'compare'   => '='
				)
			)
		) );
		return (int)count( $users->get_results() );
	}


	/**
	 * URL to verify a user
	 *
	 * @param int $user_id
	 *
	 * @return string
	 */
	function verify_user_url( $user_id ) {
		$url = add_query_arg( array( 'um_adm_action' => 'verify_user', 'uid' => $user_id ), admin_url( 'users.php' ) );
		return $url;
	}


	/**
	 * URL to unverify a user
	 *
	 * @param int $user_id
	 *
	 * @return string
	 */
	function unverify_user_url( $user_id ) {
		$url = add_query_arg( array( 'um_adm_action' => 'unverify_user', 'uid' => $user_id ), admin_url( 'users.php' ) );
		return $url;
	}


	/**
	 * Check if user is verified
	 *
	 * @param int $user_id
	 *
	 * @return bool
	 */
	function is_verified( $user_id ) {
		$is_verified = get_user_meta( $user_id, '_um_verified', true );
		return ( $is_verified && $is_verified == 'verified' ) ? true : false;
	}


	/**
	 * Get user verification status
	 *
	 * @param int $user_id
	 *
	 * @return string
	 */
	function verified_status( $user_id ) {
		$is_verified = get_user_meta( $user_id, '_um_verified', true );
		return ( $is_verified ) ? $is_verified : 'unverified';
	}


	/**
	 * Verify user
	 *
	 * @param int $user_id
	 * @param bool $sendmail
	 */
	function verify( $user_id, $sendmail = false ) {

		update_user_meta( $user_id, '_um_verified', 'verified' );
		do_action('um_after_user_is_verified', $user_id );

		if ( $sendmail ) {
			um_fetch_user( $user_id );

			$user = get_userdata( $user_id );
			$email = $user->user_email;

			UM()->mail()->send( $email, 'verified_account' );
		}
	}


	/**
	 * Unverify user
	 *
	 * @param int $user_id
	 */
	function unverify( $user_id ) {
		update_user_meta( $user_id, '_um_verified', 'unverified' );
		do_action( 'um_after_user_is_unverified', $user_id );
	}


	/**
	 * Verification request URL
	 *
	 * @param int $user_id
	 * @param string $redirect_to
	 *
	 * @return string
	 */
	function verify_url( $user_id, $redirect_to = '' ) {
		$args = array(
			'request_verification'  => 'true',
			'uid'                   => $user_id
		);

		if ( $redirect_to ) {
			$args['redirect_to'] = urlencode( $redirect_to );
		}
		return add_query_arg( $args, get_bloginfo( 'url' ) );
	}


	/**
	 * Cancel verification request URL
	 *
	 * @param int $user_id
	 * @param string $redirect_to
	 *
	 * @return string
	 */
	function verify_cancel_url( $user_id, $redirect_to = '' ) {
		$args = array(
			'request_verification_undo' => 'true',
			'uid'                       => $user_id
		);

		if ( $redirect_to ) {
			$args['redirect_to'] = urlencode( $redirect_to );
		}
		return add_query_arg( $args, get_bloginfo( 'url' ) );
	}


	/**
	 * @return string
	 */
	function verified_badge() {
		return '<i class="um-verified um-icon-checkmark-circled um-tip-s" title="' . __('Verified Account','um-verified') . '"></i>';
	}
}