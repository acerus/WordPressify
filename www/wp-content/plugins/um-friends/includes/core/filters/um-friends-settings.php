<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@extend settings
	***/
add_filter( 'um_settings_structure', 'um_friends_settings', 10, 1 );

function um_friends_settings( $settings ) {

    $settings['licenses']['fields'][] = array(
        'id'      		=> 'um_friends_license_key',
        'label'    		=> __( 'Friends License Key', 'um-friends' ),
        'item_name'     => 'Friends',
        'author' 	    => 'Ultimate Member',
        'version' 	    => um_friends_version,
    );

    $key = ! empty( $settings['extensions']['sections'] ) ? 'friends' : '';
    $settings['extensions']['sections'][$key] = array(
        'title'     => __( 'Friends', 'um-friends' ),
        'fields'    => array(
            array(
                'id'       		=> 'friends_show_stats',
                'type'     		=> 'checkbox',
                'label'   		=> __( 'Show friends stats in member directory', 'um-friends' ),
            ),
            array(
                'id'       		=> 'friends_show_button',
                'type'     		=> 'checkbox',
                'label'   		=> __( 'Show friend button in member directory', 'um-friends' ),
            )
        )
    );

    return $settings;
}


add_filter( 'um_activity_settings_structure', 'um_friends_activity_settings', 10, 2 );

function um_friends_activity_settings( $settings, $key ) {

    $settings['extensions']['sections'][$key]['fields'] = array_merge($settings['extensions']['sections'][$key]['fields'], array(
        array(
            'id'       	=> 'activity_friends_users',
            'type'     	=> 'checkbox',
            'label'   	=> __( 'Show only friends activity in the social wall','um-friends'),
        )
    ) );

    return $settings;
}


add_filter( 'um_email_notifications', 'um_friends_email_notifications', 10, 1 );

function um_friends_email_notifications( $email_notifications ) {

    $email_notifications['new_friend_request'] = array(
        'key'           => 'new_friend_request',
        'title'         => __( 'New Friend Request Notification','um-friends' ),
        'subject'       => '{friend} wants to be friends with you on {site_name}',
        'body'          => 'Hi {receiver},<br /><br />' .
            '{friend} has just sent you a friend request on {site_name}.<br /><br />' .
            'View their profile to accept/reject this friendship request:<br />' .
            '{friend_profile}<br /><br />' .
            'This is an automated notification from {site_name}. You do not need to reply.',
        'description'   => __('Send a notification to user when they receive a new friend request','um-friends'),
        'recipient'   => 'user',
        'default_active' => true
    );

    $email_notifications['new_friend'] = array(
        'key'           => 'new_friend',
        'title'         => __( 'New Friend Notification','um-friends' ),
        'subject'       => '{friend} has accepted your friend request',
        'body'          => 'Hi {receiver},<br /><br />' .
            'You are now friends with {friend} on {site_name}.<br /><br />' .
            'View their profile:<br />' .
            '{friend_profile}<br /><br />' .
            'This is an automated notification from {site_name}. You do not need to reply.',
        'description'   => __('Send a notification to user when a friend request get approved','um-friends'),
        'recipient'   => 'user',
        'default_active' => true
    );

    return $email_notifications;
}

	/***
	***	@Adds a notification type
	***/
	add_filter('um_notifications_core_log_types', 'um_friends_add_notification_type', 200 );
	function um_friends_add_notification_type( $array ) {
		
		$array['new_friend_request'] = array(
			'title' => __('User get a new friend request','um-friends'),
			'template' => __('<strong>{member}</strong> has sent you a friendship request'),
			'account_desc' => __('When someone requests friendship','um-friends'),
		);
		
		$array['new_friend'] = array(
			'title' => __('User get a new friend','um-friends'),
			'template' => __('<strong>{member}</strong> has accepted your friendship request'),
			'account_desc' => __('When someone accepts friendship','um-friends'),
		);
		
		return $array;
	}
	
	/***
	***	@Adds a notification icon
	***/
	add_filter('um_notifications_get_icon', 'um_friends_add_notification_icon', 10, 2 );
	function um_friends_add_notification_icon( $output, $type ) {
		if ( $type == 'new_friend_request' ) {
			$output = '<i class="um-icon-android-person-add" style="color: #44b0ec"></i>';
		}
		
		if ( $type == 'new_friend' ) {
			$output = '<i class="um-icon-android-person" style="color: #44b0ec"></i>';
		}
		return $output;
	}