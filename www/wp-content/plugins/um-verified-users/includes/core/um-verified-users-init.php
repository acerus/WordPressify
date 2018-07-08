<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class UM_Verified_Users_API
 */
class UM_Verified_Users_API {

	private static $instance;

	static public function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * UM_Verified_Users_API constructor.
	 */
	function __construct() {

		// Global for backwards compatibility.
		$GLOBALS['um_verified_users'] = $this;
		add_filter( 'um_call_object_Verified_Users_API', array( &$this, 'get_this' ) );

		if ( UM()->is_request( 'admin' ) ) {
			$this->admin_upgrade();
		}

		$this->api();
		$this->enqueue();

		add_action( 'plugins_loaded', array( &$this, 'init' ), 0 );

		add_filter( 'um_settings_default_values', array( &$this, 'default_settings' ), 10, 1 );

		add_filter( 'um_email_notifications', array( &$this, 'um_verified_email_notifications' ), 10, 1 );
		add_filter( 'um_email_templates_path_by_slug', array( &$this, 'email_templates_path_by_slug' ), 10, 1 );
	}


	/**
	 * @return $this
	 */
	function get_this() {
		return $this;
	}


	/**
	 * @return um_ext\um_verified_users\core\Verified_Users_Setup()
	 */
	function setup() {
		if ( empty( UM()->classes['um_verified_users_setup'] ) ) {
			UM()->classes['um_verified_users_setup'] = new um_ext\um_verified_users\core\Verified_Users_Setup();
		}
		return UM()->classes['um_verified_users_setup'];
	}


	/**
	 * @return um_ext\um_verified_users\core\Verified_Users_Main_API()
	 */
	function api() {
		if ( empty( UM()->classes['um_verified_users_api'] ) ) {
			UM()->classes['um_verified_users_api'] = new um_ext\um_verified_users\core\Verified_Users_Main_API();
		}
		return UM()->classes['um_verified_users_api'];
	}


	/**
	 * @return um_ext\um_verified_users\core\Verified_Users_Enqueue()
	 */
	function enqueue() {
		if ( empty( UM()->classes['um_verified_users_enqueue'] ) ) {
			UM()->classes['um_verified_users_enqueue'] = new um_ext\um_verified_users\core\Verified_Users_Enqueue();
		}
		return UM()->classes['um_verified_users_enqueue'];
	}


	/**
	 * @return um_ext\um_verified_users\admin\core\Admin_Upgrade()
	 */
	function admin_upgrade() {
		if ( empty( UM()->classes['um_verified_users_admin_upgrade'] ) ) {
			UM()->classes['um_verified_users_admin_upgrade'] = new um_ext\um_verified_users\admin\core\Admin_Upgrade();
		}
		return UM()->classes['um_verified_users_admin_upgrade'];
	}


	/**
	 * Init
	 */
	function init() {
		require_once um_verified_users_path . 'includes/core/um-verified-users-filters.php';
		require_once um_verified_users_path . 'includes/core/um-verified-users-actions.php';
	}


	/**
	 * @param $defaults
	 *
	 * @return array
	 */
	function default_settings( $defaults ) {
		$defaults = array_merge( $defaults, $this->setup()->settings_defaults );
		return $defaults;
	}


	/**
	 * @param $email_notifications
	 *
	 * @return mixed
	 */
    function um_verified_email_notifications( $email_notifications ) {

        $email_notifications['verified_account'] = array(
            'key'           => 'verified_account',
            'title'         => __( 'Account is verified E-mail', 'um-verified' ),
            'subject'       => 'Your account is verified on {site_name}!',
            'body'          => 'Hi {display_name},<br /><br />' .
                'Good News! We have reviewed your verification request and are happy to say that your account is now verified.<br /><br />' .
                'View your profile:<br />' .
                '{user_profile_link}<br /><br />' .
                'Thank You!<br />' .
                '{site_name}',
            'description'   => __('Send a notification to user when his account is verified','um-verified'),
            'recipient'   => 'user',
            'default_active' => true
        );

        $email_notifications['verification_request'] = array(
            'key'           => 'verification_request',
            'title'         => __( 'Verification Request  E-mail', 'um-verified' ),
            'subject'       => '{display_name} ({username}) verification request on {site_name}',
            'body'          => '{display_name} ({username}) has requested that their account be verified.<br /><br />' .
                'View their profile:<br />' .
                '{user_profile_link}<br /><br />' .
                'To approve request:<br />' .
                '{verify_approve}<br /><br />' .
                'To reject request:<br />' .
                '{verify_reject}',
            'description'   => __('Send a notification e-mail to admin when a user requests to have their account verified.','um-verified'),
            'recipient'   => 'admin',
            'default_active' => true
        );

        return $email_notifications;
    }


	/**
	 * @param array $slugs
	 *
	 * @return array
	 */
	function email_templates_path_by_slug( $slugs ) {
		$slugs['verified_account'] = um_verified_users_path . 'templates/email/';
		$slugs['verification_request'] = um_verified_users_path . 'templates/email/';
		return $slugs;
	}
}

//create class var
add_action( 'plugins_loaded', 'um_init_verified_users', -10, 1 );
function um_init_verified_users() {
	if ( function_exists( 'UM' ) ) {
		UM()->set_class( 'Verified_Users_API', true );
	}
}


/**
 * @deprecated since 2.0
 * @return string
 */
function um_verified() {
	return UM()->Verified_Users_API()->api()->verified_badge();
}