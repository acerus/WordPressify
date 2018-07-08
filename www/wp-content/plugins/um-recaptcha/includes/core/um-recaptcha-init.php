<?php
if ( ! defined( 'ABSPATH' ) ) exit;


class UM_reCAPTCHA_API {
    private static $instance;

    static public function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

	function __construct() {
        // Global for backwards compatibility.
        $GLOBALS['um_recaptcha'] = $this;
        add_filter( 'um_call_object_reCAPTCHA_API', array( &$this, 'get_this' ) );

        //if ( UM()->is_request( 'frontend' ) )
        //    $this->enqueue();

        if ( UM()->is_request( 'admin' ) )
            $this->notices();

		add_action( 'plugins_loaded', array( &$this, 'init' ), 0 );

        add_filter( 'um_settings_default_values', array( &$this, 'default_settings' ), 10, 1 );
    }


    function default_settings( $defaults ) {
        $defaults = array_merge( $defaults, $this->setup()->settings_defaults );
        return $defaults;
    }


    /**
     * @return um_ext\um_recaptcha\core\Recaptcha_Setup()
     */
    function setup() {
        if ( empty( UM()->classes['um_recaptcha_setup'] ) ) {
            UM()->classes['um_recaptcha_setup'] = new um_ext\um_recaptcha\core\Recaptcha_Setup();
        }
        return UM()->classes['um_recaptcha_setup'];
    }


    function get_this() {
        return $this;
    }


    /**
     * @return um_ext\um_recaptcha\core\reCAPTCHA_Enqueue()
     */
    function enqueue() {
        if ( empty( UM()->classes['um_recaptcha_enqueue'] ) ) {
            UM()->classes['um_recaptcha_enqueue'] = new um_ext\um_recaptcha\core\reCAPTCHA_Enqueue();
        }
        return UM()->classes['um_recaptcha_enqueue'];
    }


    /**
     * @return um_ext\um_recaptcha\core\reCAPTCHA_Notices()
     */
    function notices() {
        if ( empty( UM()->classes['um_recaptcha_notices'] ) ) {
            UM()->classes['um_recaptcha_notices'] = new um_ext\um_recaptcha\core\reCAPTCHA_Notices();
        }
        return UM()->classes['um_recaptcha_notices'];
    }


	/***
	***	@Init
	***/
	function init() {

		// Actions
		require_once um_recaptcha_path . 'includes/core/actions/um-recaptcha-form.php';
		require_once um_recaptcha_path . 'includes/core/actions/um-recaptcha-admin.php';
		
		// Filters
		require_once um_recaptcha_path . 'includes/core/filters/um-recaptcha-settings.php';

	}
	
	/***
	***	@Captcha allowed
	***/
	function captcha_allowed( $args ) {
		$enable = false;
		
		$your_sitekey = UM()->options()->get('g_recaptcha_sitekey');
		$your_secret = UM()->options()->get('g_recaptcha_secretkey');
		$recaptcha = UM()->options()->get('g_recaptcha_status');
		
		if ( $recaptcha )
			$enable = true;
		
		if ( isset( $args['g_recaptcha_status'] ) && $args['g_recaptcha_status'] )
			$enable = true;
		
		if ( isset( $args['g_recaptcha_status'] ) && !$args['g_recaptcha_status'] )
			$enable = false;
		
		if ( !$your_sitekey || !$your_secret )
			$enable = false;
		
		if ( $enable == false )
			return false;
		
		return true;
	}
	
}

//create class var
add_action( 'plugins_loaded', 'um_init_recaptcha', -10, 1 );
function um_init_recaptcha() {
    if ( function_exists( 'UM' ) ) {
        UM()->set_class( 'reCAPTCHA_API', true );
    }
}