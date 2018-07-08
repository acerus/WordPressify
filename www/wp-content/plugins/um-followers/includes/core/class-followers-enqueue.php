<?php
namespace um_ext\um_followers\core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Followers_Enqueue {

	function __construct() {
	
		add_action( 'wp_enqueue_scripts',  array( &$this, 'wp_enqueue_scripts' ), 9999 );
		add_filter( 'um_enqueue_localize_data',  array( &$this, 'localize_data' ), 10, 1 );

	}
	
	function wp_enqueue_scripts() {
		
		wp_register_style( 'um_followers', um_followers_url . 'assets/css/um-followers.css' );
		wp_enqueue_style( 'um_followers' );
		
		wp_register_script( 'um_followers', um_followers_url . 'assets/js/um-followers.js', '', '', true );
		wp_enqueue_script( 'um_followers' );
		
	}


	function localize_data( $data ) {

        $data['followers_follow'] = UM()->get_ajax_route( 'um_ext\um_followers\core\Followers_Main_API', 'ajax_followers_follow' );
        $data['followers_unfollow'] = UM()->get_ajax_route( 'um_ext\um_followers\core\Followers_Main_API', 'ajax_followers_unfollow' );

		return $data;

	}
	
}