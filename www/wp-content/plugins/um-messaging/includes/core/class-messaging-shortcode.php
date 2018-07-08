<?php
namespace um_ext\um_messaging\core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class Messaging_Shortcode
 * @package um_ext\um_messaging\core
 */
class Messaging_Shortcode {


	/**
	 * Messaging_Shortcode constructor.
	 */
	function __construct() {
		add_shortcode( 'ultimatemember_messages', array( &$this, 'ultimatemember_messages' ) );
		add_shortcode( 'ultimatemember_message_button', array( &$this, 'ultimatemember_message_button' ) );
		add_shortcode( 'ultimatemember_message_count', array( &$this, 'ultimatemember_message_count' ) );
	}


	/**
	 * Unread messages shortcode
	 *
	 * @param array $args
	 *
	 * @return int|string
	 */
	function ultimatemember_message_count( $args = array() ) {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$defaults = array(
			'user_id' => get_current_user_id()
		);
		$args = wp_parse_args( $args, $defaults );

		/**
		 * @var $user_id
		 */
		extract( $args );

		$count = UM()->Messaging_API()->api()->get_unread_count( $user_id );
		$count = ( $count > 10 ) ? 10 . '+' : $count;
		return $count;
	}


	/**
	 * Start conversation button shortcode
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	function ultimatemember_message_button( $args = array() ) {
		global $wp_query;

		$defaults = array(
			'user_id' => 0
		);
		$args = wp_parse_args( $args, $defaults );

		/**
		 * @var $user_id
		 */
		extract( $args );
		
		$current_url = UM()->permalinks()->get_current_url();

		if ( um_get_core_page( 'user' ) ) {
			do_action( "um_messaging_button_in_profile", $current_url, $user_id );
		}

		if ( ! is_user_logged_in() ) {
			$redirect = um_get_core_page('login');

			if ( ! empty( $wp_query->query_vars['um_page'] ) && 'api' == $wp_query->query_vars['um_page'] ) {
				if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
					$redirect = add_query_arg( 'redirect_to', urlencode( $_SERVER['HTTP_REFERER'] ), $redirect );
				}
			} else {
				$redirect = add_query_arg( 'redirect_to', $current_url, $redirect );
			}

			$user_id = intval($args['user_id']);
			if ( UM()->Messaging_API()->api()->can_message( $user_id ) ) {
				$btn = '<a href="' . $redirect . '" class="um-login-to-msg-btn um-message-btn um-button" data-message_to="'.$user_id.'">'. __('Message','um-messaging'). '</a>';
				return $btn;
			}
			
		} elseif ( $user_id != get_current_user_id() ) {
		
			if ( UM()->Messaging_API()->api()->can_message( $user_id ) ) {
				$btn = '<a href="javascript:void(0);" class="um-message-btn um-button" data-message_to="'.$user_id.'"><span>'. __('Message','um-messaging'). '</span></a>';
				return $btn;
			}
			
		}

		return '';
	}


	/**
	 * Conversations list shortcode
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	function ultimatemember_messages( $args = array() ) {
		$defaults = array(
			'user_id' => get_current_user_id()
		);
		$args = wp_parse_args( $args, $defaults );

		/**
		 * @var $user_id
		 */
		extract( $args );

		ob_start();

		$conversations = UM()->Messaging_API()->api()->get_conversations( $user_id );

		if ( isset( $_GET['conversation_id'] ) ) {
			if ( esc_attr( absint( $_GET['conversation_id'] ) ) ) {
				foreach( $conversations as $conversation ) {
					if ( $conversation->conversation_id == $_GET['conversation_id'] ) {
						$current_conversation = esc_attr( absint( $_GET['conversation_id'] ) );
						continue;
					}
				}
			}
		}

		if ( file_exists( get_stylesheet_directory() . '/ultimate-member/templates/conversations.php' ) ) {
			include_once get_stylesheet_directory() . '/ultimate-member/templates/conversations.php';
		} else {
			include_once um_messaging_path . 'templates/conversations.php';
		}

		$output = ob_get_clean();
		return $output;
	}

}