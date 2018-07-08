<?php
namespace um_ext\um_verified_users\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Verified_Users_Setup {
	/**
	 * @var array
	 */
	var $settings_defaults;


	/**
	 * Verified_Users_Setup constructor.
	 */
	function __construct() {
		//settings defaults
		$this->settings_defaults = array(
			'verified_redirect'         => home_url(),
			'verified_account_on'       => 1,
			'verified_account_sub'      => 'Your account is verified on {site_name}!',
			'verified_account'          => 'Hi {display_name},<br /><br />' .
			                               'Good News! We have reviewed your verification request and are happy to say that your account is now verified.<br /><br />' .
			                               'View your profile:<br />' .
			                               '{user_profile_link}<br /><br />' .
			                               'Thank You!<br />' .
			                               '{site_name}',
			'verification_request_on' => 1,
			'verification_request_sub'  => '{display_name} ({username}) verification request on {site_name}',
			'verification_request'      => '{display_name} ({username}) has requested that their account be verified.<br /><br />' .
			                               'View their profile:<br />' .
			                               '{user_profile_link}<br /><br />' .
			                               'To approve request:<br />' .
			                               '{verify_approve}<br /><br />' .
			                               'To reject request:<br />' .
			                               '{verify_reject}',
			'activity-verified-account' => 1
		);


		$notification_types['account_verified'] = array(
			'title'         => __('User account is verified','um-verified'),
			'template'      => 'Congratulations! Your account is now verified.',
			'account_desc'  => __('When my account gets verified','um-verified'),
		);

		foreach ( $notification_types as $k => $desc ) {
			$this->settings_defaults['log_' . $k] = 1;
			$this->settings_defaults['log_' . $k . '_template'] = $desc['template'];
		}
	}


	/**
	 *
	 */
	function set_default_settings() {
		$options = get_option( 'um_options' );
		$options = empty( $options ) ? array() : $options;

		foreach ( $this->settings_defaults as $key => $value ) {
			//set new options to default
			if ( ! isset( $options[$key] ) )
				$options[$key] = $value;

		}

		update_option( 'um_options', $options );
	}


	/**
	 *
	 */
	function add_users_meta() {
		$args = array( 'fields' => 'ID', 'number' => 0 );

		$args['meta_query'][] = array( array( 'key' => '_um_verified', 'compare' => 'NOT EXISTS' ) );
		$users = new \WP_User_Query( $args );
		foreach ( $users->results as $user ) {
			update_user_meta( $user, '_um_verified', 'unverified' );
		}
	}


	/**
	 * RUN Setup
	 */
	function run_setup() {
		$this->set_default_settings();
		$this->add_users_meta();
	}
}