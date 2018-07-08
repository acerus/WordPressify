<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@Send a mail notification
	***/
	add_action('um_followers_after_user_follow','um_followers_mail_notification', 20, 2 );
	function um_followers_mail_notification( $user_id1, $user_id2 ) {

        if ( ! UM()->Followers_API()->api()->enabled_email( $user_id1 ) ) return false;
		
		// send a mail notification
		um_fetch_user( $user_id1 );
		$followed_email = um_user('user_email');
		$followed = um_user('display_name');
		$followers_url = add_query_arg('profiletab', 'followers', um_user_profile_url() );
	
		// follower
		um_fetch_user( $user_id2 );
		$follower = um_user('display_name');
		$follower_profile = um_user_profile_url();
				
		UM()->mail()->send(
		    $followed_email,
            'new_follower',
            array(
                'plain_text' => 1,
                'path' => um_followers_path . 'templates/email/',
                'tags' => array(
                    '{followed}',
                    '{followers_url}',
                    '{follower}',
                    '{follower_profile}'
                ),
                'tags_replace' => array(
                    $followed,
                    $followers_url,
                    $follower,
                    $follower_profile
                )
		    )
        );
				
	}