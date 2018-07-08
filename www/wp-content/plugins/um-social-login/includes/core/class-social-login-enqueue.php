<?php
namespace um_ext\um_social_login\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Social_Login_Enqueue {

	function __construct() {
	
		add_action( 'wp_enqueue_scripts',  array( &$this, 'wp_enqueue_scripts' ), 9 );
		add_action( 'admin_enqueue_scripts',  array( &$this, 'admin_enqueue_scripts' ), 9 );

        add_filter( 'um_enqueue_localize_data',  array( &$this, 'localize_data' ), 10, 1 );
	}


    function localize_data( $data ) {

        $data['social_login_change_photo'] = UM()->get_ajax_route( 'um_ext\um_social_login\core\Social_Login_Ajax', 'ajax_change_photo' );

        return $data;

    }

	/***
	***	@styles
	***/
	function wp_enqueue_scripts() {
		
		wp_register_style('um_social_login', um_social_login_url . 'assets/css/um-social-connect.css' );
		wp_enqueue_style('um_social_login');
		
		wp_register_script('um_social_login', um_social_login_url . 'assets/js/um-social-connect.js', array('jquery'), '', true );
		wp_enqueue_script('um_social_login');

		wp_register_script('um_facebook_fix', um_social_login_url . 'assets/js/um-facebook-fix.js', array(), '', true );
		wp_enqueue_script('um_facebook_fix');
		
	}


	function admin_enqueue_scripts() {

		wp_register_script('um_facebook_fix', um_social_login_url . 'assets/js/um-facebook-fix.js', array(), '', true );
		wp_enqueue_script('um_facebook_fix');

	}

}