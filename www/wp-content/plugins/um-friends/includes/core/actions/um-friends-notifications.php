<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@Send a mail notification
	***/
	add_action('um_friends_after_user_friend','um_friends_mail_notification', 20, 2 );
	function um_friends_mail_notification( $user_id1, $user_id2 ) {
		if ( ! UM()->Friends_API()->api()->enabled_email( $user_id1 ) ) return false;
		
		// send a mail notification
		um_fetch_user( $user_id1 );
		$email1 = um_user('user_email');
		$user1 = um_user('display_name');
		$friends_url = add_query_arg('profiletab', 'friends', um_user_profile_url() );
	
		// friend
		um_fetch_user( $user_id2 );
		$friend = um_user('display_name');
		$friend_profile = um_user_profile_url();
				
		UM()->mail()->send( $email1, 'new_friend', array(
				
			'plain_text' => 1,
			'path' => um_friends_path . 'templates/email/',
			'tags' => array(
				'{friend}',
				'{receiver}',
				'{friend_profile}',
			),
			'tags_replace' => array(
				$friend,
				$user1,
				$friend_profile
			)
					
		) );
				
	}

	/***
	***	@Send a mail notification
	***/
	add_action('um_friends_after_user_friend_request','um_friends_request_mail_notification', 20, 2 );
	function um_friends_request_mail_notification( $user_id1, $user_id2 ) {
		if ( ! UM()->Friends_API()->api()->enabled_email( $user_id1 ) ) return false;
		
		// send a mail notification
		um_fetch_user( $user_id1 );
		$email1 = um_user('user_email');
		$user1 = um_user('display_name');
		$friends_url = add_query_arg('profiletab', 'friends', um_user_profile_url() );
	
		// friend
		um_fetch_user( $user_id2 );
		$friend = um_user('display_name');
		$friend_profile = um_user_profile_url();
				
		UM()->mail()->send( $email1, 'new_friend_request', array(
				
			'plain_text' => 1,
			'path' => um_friends_path . 'templates/email/',
			'tags' => array(
				'{friend}',
				'{receiver}',
				'{friend_profile}',
			),
			'tags_replace' => array(
				$friend,
				$user1,
				$friend_profile
			)
					
		) );
				
	}