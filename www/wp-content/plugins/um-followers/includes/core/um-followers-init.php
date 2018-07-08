<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class UM_Followers_API {
    private static $instance;

    var $api;
    var $enqueue;
    var $shortcode;


    static public function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * UM_Followers_API constructor.
     */
	function __construct() {
        // Global for backwards compatibility.
        $GLOBALS['um_followers'] = $this;
        add_filter( 'um_call_object_Followers_API', array( &$this, 'get_this' ) );

		add_action( 'plugins_loaded', array( &$this, 'init' ), 0 );

		require_once um_followers_path . 'includes/core/um-followers-widget.php';
		add_action( 'widgets_init', array( &$this, 'widgets_init' ) );

        add_filter( 'um_settings_default_values', array( &$this, 'default_settings' ), 10, 1 );
        add_filter( 'um_rest_query_mode_get.following', array( &$this, 'default_settings' ), 10, 1 );
        add_filter( 'um_rest_query_mode_get.followers', array( &$this, 'default_settings' ), 10, 1 );

        add_filter( 'um_rest_query_mode', array( &$this, 'rest_api_query_mode' ), 10, 3 );
        add_filter( 'um_rest_userdata', array( &$this, 'rest_userdata' ), 10, 2 );

        add_filter( 'um_rest_get_auser', array( &$this, 'rest_get_auser' ), 10, 3 );

        add_filter( 'um_email_templates_path_by_slug', array( &$this, 'email_templates_path_by_slug' ), 10, 1 );
    }


    function email_templates_path_by_slug( $slugs ) {
        $slugs['new_follower'] = um_followers_path . 'templates/email/';
        return $slugs;
    }


    function rest_get_auser( $response, $field, $user_id ) {
        if ( 'followers' == $field ) {
            $response['followers_count'] = $this->api()->count_followers_plain( $user_id );
            $response['following_count'] = $this->api()->count_following_plain( $user_id );
        }

        return $response;
    }

    function rest_userdata( $userdata, $user_id ) {
        $userdata->followers_count = $this->api()->count_followers_plain( $user_id );
        $userdata->following_count = $this->api()->count_following_plain( $user_id );

        return $userdata;
    }


    function rest_api_query_mode( $data, $query_mode, $args ) {
        switch( $query_mode ) {

            case 'get.following':
                $data = $this->api()->rest_get_following( $args );
                break;

            case 'get.followers':
                $data = $this->api()->rest_get_followers( $args );
                break;

            default:
                $data = apply_filters( 'um_rest_query_mode', $data , $query_mode );
        }

        return $data;
    }


    function default_settings( $defaults ) {
        $defaults = array_merge( $defaults, $this->setup()->settings_defaults );
        return $defaults;
    }


    function get_this() {
        return $this;
	}


    /**
     * @return um_ext\um_followers\core\Followers_Setup()
     */
    function setup() {
        if ( empty( UM()->classes['um_followers_setup'] ) ) {
            UM()->classes['um_followers_setup'] = new um_ext\um_followers\core\Followers_Setup();
        }
        return UM()->classes['um_followers_setup'];
    }


    /**
     * @return um_ext\um_followers\core\Followers_Main_API()
     */
    function api() {
        if ( empty( UM()->classes['um_followers_api'] ) ) {
            UM()->classes['um_followers_api'] = new um_ext\um_followers\core\Followers_Main_API();
        }
        return UM()->classes['um_followers_api'];
    }


    /**
     * @return um_ext\um_followers\core\Followers_Enqueue()
     */
    function enqueue() {
        if ( empty( UM()->classes['um_followers_enqueue'] ) ) {
            UM()->classes['um_followers_enqueue'] = new um_ext\um_followers\core\Followers_Enqueue();
        }
        return UM()->classes['um_followers_enqueue'];
    }


    /**
     * @return um_ext\um_followers\core\Followers_Shortcode()
     */
    function shortcode() {
        if ( empty( UM()->classes['um_followers_shortcode'] ) ) {
            UM()->classes['um_followers_shortcode'] = new um_ext\um_followers\core\Followers_Shortcode();
        }
        return UM()->classes['um_followers_shortcode'];
    }


	/***
	***	@Init
	***/
	function init() {

        $this->enqueue();
        $this->shortcode();

		// Actions
		require_once um_followers_path . 'includes/core/actions/um-followers-profile.php';
		require_once um_followers_path . 'includes/core/actions/um-followers-notifications.php';
		require_once um_followers_path . 'includes/core/actions/um-followers-members.php';

        require_once um_followers_path . 'includes/core/actions/um-followers-admin.php';
		require_once um_followers_path . 'includes/core/actions/um-followers-account.php';

		// Filters
		require_once um_followers_path . 'includes/core/filters/um-followers-settings.php';
		require_once um_followers_path . 'includes/core/filters/um-followers-profile.php';
		require_once um_followers_path . 'includes/core/filters/um-followers-admin.php';
		require_once um_followers_path . 'includes/core/filters/um-followers-account.php';
		require_once um_followers_path . 'includes/core/filters/um-followers-search.php';

	}


	function widgets_init() {
		register_widget( 'um_my_followers' );
		register_widget( 'um_my_following' );
	}
}

//create class var
add_action( 'plugins_loaded', 'um_init_followers', -10, 1 );
function um_init_followers() {
    if ( function_exists( 'UM' ) ) {
        UM()->set_class( 'Followers_API', true );
    }
}