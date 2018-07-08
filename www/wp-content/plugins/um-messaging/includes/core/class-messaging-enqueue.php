<?php
namespace um_ext\um_messaging\core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Messaging_Enqueue {

	function __construct() {
	
		add_action( 'wp_enqueue_scripts',  array( &$this, 'wp_enqueue_scripts' ), 9999 );

        add_filter( 'um_enqueue_localize_data',  array( &$this, 'localize_data' ), 10, 1 );
	}

    function localize_data( $data ) {

        $data['messaging_unblock_user'] = UM()->get_ajax_route( 'um_ext\um_messaging\core\Messaging_Main_API', 'ajax_messaging_unblock_user' );
        $data['messaging_block_user'] = UM()->get_ajax_route( 'um_ext\um_messaging\core\Messaging_Main_API', 'ajax_messaging_block_user' );
        $data['messaging_delete_conversation'] = UM()->get_ajax_route( 'um_ext\um_messaging\core\Messaging_Main_API', 'ajax_messaging_delete_conversation' );
        $data['messaging_remove'] = UM()->get_ajax_route( 'um_ext\um_messaging\core\Messaging_Main_API', 'ajax_messaging_remove' );
        $data['messaging_send'] = UM()->get_ajax_route( 'um_ext\um_messaging\core\Messaging_Main_API', 'ajax_messaging_send' );
        $data['messaging_login_modal'] = UM()->get_ajax_route( 'um_ext\um_messaging\core\Messaging_Main_API', 'ajax_messaging_login_modal' );
        $data['messaging_start'] = UM()->get_ajax_route( 'um_ext\um_messaging\core\Messaging_Main_API', 'ajax_messaging_start' );
        $data['messaging_update'] = UM()->get_ajax_route( 'um_ext\um_messaging\core\Messaging_Main_API', 'ajax_messaging_update' );

        return $data;

    }

	function wp_enqueue_scripts() {
	
		wp_register_script( 'moment', um_messaging_url . 'assets/js/moment-with-locales.min.js', '', '', true );
		wp_enqueue_script( 'moment' );

		wp_register_script( 'moment-timezone', um_messaging_url . 'assets/js/moment-timezone.js', '', '', true );
		wp_enqueue_script( 'moment-timezone' );
		wp_register_style( 'um_messaging', um_messaging_url . 'assets/css/um-messaging.css' );
		wp_enqueue_style( 'um_messaging' );
	
		wp_register_script( 'um_autosize', um_messaging_url . 'assets/js/autosize.js', '', '', true );
		wp_enqueue_script( 'um_autosize' );
		
		wp_enqueue_script( 'jquery-ui-datepicker' );
		
		wp_register_script( 'um_messaging', um_messaging_url . 'assets/js/um-messaging.js', '', '', true );
		
		// Localize the script with new data
		$translation_array = array(
			'no_chats_found' => __( 'No chats found here', 'um-messaging' ),
		);

		wp_localize_script( 'um_messaging', 'um_message_i18n', $translation_array );

		// Localize time
		$timezone_array = array(
			'string' => get_option( 'timezone_string' ),
			'offset' => get_option( 'gmt_offset' ),
		);

		wp_localize_script( 'um_messaging', 'um_message_timezone', $timezone_array );

		wp_enqueue_script( 'um_messaging' );
	}
	
}