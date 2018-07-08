<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


class UM_Friends_API {

    private static $instance;

    static public function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }


	function __construct() {
        // Global for backwards compatibility.
        $GLOBALS['um_friends'] = $this;
        add_filter( 'um_call_object_Friends_API', array( &$this, 'get_this' ) );

		add_action( 'plugins_loaded', array( &$this, 'init' ), 0 );

		require_once um_friends_path . 'includes/core/um-friends-widget.php';
		add_action( 'widgets_init', array(&$this, 'widgets_init' ) );

        add_filter( 'um_settings_default_values', array( &$this, 'default_settings' ), 10, 1 );

        add_filter( 'um_email_templates_path_by_slug', array( &$this, 'email_templates_path_by_slug' ), 10, 1 );
    }


    function email_templates_path_by_slug( $slugs ) {
        $slugs['new_friend'] = um_friends_path . 'templates/email/';
        $slugs['new_friend_request'] = um_friends_path . 'templates/email/';
        return $slugs;
    }


    function default_settings( $defaults ) {
        $defaults = array_merge( $defaults, $this->setup()->settings_defaults );
        return $defaults;
    }


    function get_this() {
        return $this;
    }


    /**
     * @return um_ext\um_friends\core\Friends_Setup()
     */
    function setup() {
        if ( empty( UM()->classes['um_friends_setup'] ) ) {
            UM()->classes['um_friends_setup'] = new um_ext\um_friends\core\Friends_Setup();
        }
        return UM()->classes['um_friends_setup'];
    }


    /**
     * @return um_ext\um_friends\core\Friends_Main_API()
     */
    function api() {
        if ( empty( UM()->classes['um_friends_api'] ) ) {
            UM()->classes['um_friends_api'] = new um_ext\um_friends\core\Friends_Main_API();
        }
        return UM()->classes['um_friends_api'];
    }


    /**
     * @return um_ext\um_friends\core\Friends_Enqueue()
     */
    function enqueue() {
        if ( empty( UM()->classes['um_friends_enqueue'] ) ) {
            UM()->classes['um_friends_enqueue'] = new um_ext\um_friends\core\Friends_Enqueue();
        }
        return UM()->classes['um_friends_enqueue'];
    }


    /**
     * @return um_ext\um_friends\core\Friends_Shortcode()
     */
    function shortcode() {
        if ( empty( UM()->classes['um_friends_shortcode'] ) ) {
            UM()->classes['um_friends_shortcode'] = new um_ext\um_friends\core\Friends_Shortcode();
        }
        return UM()->classes['um_friends_shortcode'];
    }


	/***
	***	@Init
	***/
	function init() {
        $this->enqueue();
        $this->shortcode();

		// Actions
		require_once um_friends_path . 'includes/core/actions/um-friends-profile.php';
		require_once um_friends_path . 'includes/core/actions/um-friends-notifications.php';
		require_once um_friends_path . 'includes/core/actions/um-friends-members.php';
		require_once um_friends_path . 'includes/core/actions/um-friends-admin.php';
		require_once um_friends_path . 'includes/core/actions/um-friends-account.php';

		// Filters
		require_once um_friends_path . 'includes/core/filters/um-friends-settings.php';
		require_once um_friends_path . 'includes/core/filters/um-friends-profile.php';
		require_once um_friends_path . 'includes/core/filters/um-friends-admin.php';
		require_once um_friends_path . 'includes/core/filters/um-friends-account.php';
		require_once um_friends_path . 'includes/core/filters/um-friends-search.php';

	}

	function widgets_init() {
		register_widget( 'um_my_friends' );
	}

}

//create class var
add_action( 'plugins_loaded', 'um_init_friends', -10, 1 );
function um_init_friends() {
    if ( function_exists( 'UM' ) ) {
        UM()->set_class( 'Friends_API', true );
    }
}