<?php
namespace um_ext\um_friends\core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Friends_Enqueue {

    function __construct() {
	
        add_action( 'wp_enqueue_scripts',  array( &$this, 'wp_enqueue_scripts' ), 9999 );
        add_filter( 'um_enqueue_localize_data',  array( &$this, 'localize_data' ), 10, 1 );
    }
	
    function wp_enqueue_scripts(){
		
        wp_register_style( 'um_friends', um_friends_url . 'assets/css/um-friends.css' );
        wp_enqueue_style( 'um_friends' );
		
        wp_register_script( 'um_friends', um_friends_url . 'assets/js/um-friends.js', '', '', true );
        wp_enqueue_script( 'um_friends' );
		
    }


    function localize_data( $data ) {

        $data['friends_approve'] = UM()->get_ajax_route( 'um_ext\um_friends\core\Friends_Main_API', 'ajax_friends_approve' );
        $data['friends_add'] = UM()->get_ajax_route( 'um_ext\um_friends\core\Friends_Main_API', 'ajax_friends_add' );
        $data['friends_unfriend'] = UM()->get_ajax_route( 'um_ext\um_friends\core\Friends_Main_API', 'ajax_friends_unfriend' );
        $data['friends_cancel_request'] = UM()->get_ajax_route( 'um_ext\um_friends\core\Friends_Main_API', 'ajax_friends_cancel_request' );

        return $data;

    }
	
}