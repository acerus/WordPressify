<?php
namespace um_ext\um_notifications\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Notifications_Shortcode {

	function __construct() {
	
		add_shortcode( 'ultimatemember_notifications', array(&$this, 'ultimatemember_notifications') );
		add_shortcode( 'ultimatemember_notification_count', array(&$this, 'ultimatemember_notification_count') );
		
		add_filter( 'wp_title', array(&$this,'wp_title'), 10, 2 );
		
	}
	
	/***
	***	@custom title for page
	***/
	function wp_title( $title, $sep=null ) {
		global $post;
		if ( isset( $post->ID ) && $post->ID == UM()->permalinks()->core['notifications'] ) {
			$unread = UM()->Notifications_API()->api()->get_notifications( 0, 'unread', true );
			if ( $unread ){
				$title = "($unread) $title";
			}
		}
		return $title;
	}

	/***
	***	@Shortcode
	***/
	function ultimatemember_notifications( $args = array() ) {
		if ( !is_user_logged_in() )
			exit( wp_redirect( home_url() ) );

		$has_notifications = UM()->Notifications_API()->api()->get_notifications( 1 );
		if ( !$has_notifications ) {

			$template = 'no-notifications';
		
		} else {
			
			$notifications = UM()->Notifications_API()->api()->get_notifications( 50 );
			$template = 'notifications';
		
		}
		
		ob_start();
		
		echo '<div class="um-notification-shortcode">';

		include_once um_notifications_path . 'templates/'. $template . '.php';
		
		echo '</div>';

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/***
	***	@Shortcode
	***/
	function ultimatemember_notification_count( $args = array() ) {
		$count = UM()->Notifications_API()->api()->unread_count( get_current_user_id() );
		return (int)$count;
	}

}