<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@send notification for future messages
	***/
	add_action('um_after_existing_conversation','um_messaging_user_got_message', 20, 3 );
	function um_messaging_user_got_message( $to, $from, $conversation_id ) {
		if ( ! UM()->Messaging_API()->api()->enabled_email( $to ) ) return false;

		$get_ts = um_user_last_login_timestamp( $to );
		if ( $get_ts > 0 ) {
			if ( ( current_time('timestamp') - $get_ts ) <= UM()->options()->get('pm_notify_period') ) {
				return false;
			}
		}

		// send a mail notification
		um_fetch_user( $to );
		$recipient_e = um_user('user_email');
		$recipient = um_user('display_name');
		$message_history = add_query_arg('profiletab', 'messages', um_user_profile_url() );

		// who sends the message
		um_fetch_user( $from );
		$sender = um_user('display_name');

		UM()->mail()->send( $recipient_e, 'new_message', array(

            'plain_text' => 1,
            'path' => um_messaging_path . 'templates/email/',
            'tags' => array(
                '{recipient}',
                '{message_history}',
                '{sender}'
            ),
            'tags_replace' => array(
                $recipient,
                $message_history,
                $sender
            )

		) );

	}

	/***
	***	@Send a mail notification
	***/
	add_action('um_after_new_conversation','um_messaging_mail_notification', 20, 3 );
	function um_messaging_mail_notification( $to, $from, $conversation_id ) {
		if ( ! UM()->Messaging_API()->api()->enabled_email( $to ) ) return false;

		// send a mail notification
		um_fetch_user( $to );
		$recipient_e = um_user('user_email');
		$recipient = um_user('display_name');
		$message_history = add_query_arg('profiletab', 'messages', um_user_profile_url() );

		// who sends the message
		um_fetch_user( $from );
		$sender = um_user('display_name');

		UM()->mail()->send( $recipient_e, 'new_message', array(

			'plain_text' => 1,
			'path' => um_messaging_path . 'templates/email/',
			'tags' => array(
				'{recipient}',
				'{message_history}',
				'{sender}'
			),
			'tags_replace' => array(
				$recipient,
				$message_history,
				$sender
			)

		) );

	}