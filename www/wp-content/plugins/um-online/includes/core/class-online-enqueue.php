<?php
namespace um_ext\um_online\core;

if ( ! defined( 'ABSPATH' ) ) exit;


class Online_Enqueue {

	function __construct() {
	
		add_action( 'wp_enqueue_scripts',  array( &$this, 'wp_enqueue_scripts' ), 0 );
	
	}

	function wp_enqueue_scripts() {
		
		wp_register_style( 'um_online', um_online_url . 'assets/css/um-online.css' );
		wp_enqueue_style( 'um_online' );
		
		wp_register_script( 'um_online', um_online_url . 'assets/js/um-online.js', '', '', true );
		wp_enqueue_script( 'um_online' );

	}
	
}