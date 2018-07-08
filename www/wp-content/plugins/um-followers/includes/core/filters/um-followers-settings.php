<?php

	/***
	***	@extend settings
	***/
add_filter( 'um_settings_structure', 'um_followers_settings', 10, 1 );

function um_followers_settings( $settings ) {

    $settings['licenses']['fields'][] = array(
        'id'      		=> 'um_followers_license_key',
        'label'    		=> __( 'Followers License Key', 'um-followers' ),
        'item_name'     => 'Followers',
        'author' 	    => 'Ultimate Member',
        'version' 	    => um_followers_version,
    );

    $key = ! empty( $settings['extensions']['sections'] ) ? 'followers' : '';
    $settings['extensions']['sections'][$key] = array(
        'title'     => __( 'Followers', 'um-followers' ),
        'fields'    => array(
            array(
                'id'       		=> 'followers_show_stats',
                'type'     		=> 'checkbox',
                'label'   		=> __('Show followers stats in member directory','um-followers'),
            ),
            array(
                'id'       		=> 'followers_show_button',
                'type'     		=> 'checkbox',
                'label'   		=> __('Show follow button in member directory','um-followers'),
            ),
            array(
                'id'       		=> 'followers_allow_admin_to_follow',
                'type'     		=> 'checkbox',
                'label'    		=> __( 'Allow Administrators to follow users','um-followers' ),
                'tooltip' 	=> __('Displays Follow buttons in profiles & member directory','um-followers'),
            )
        )
    );

    return $settings;
}


add_filter( 'um_activity_settings_structure', 'um_followers_activity_settings', 10, 2 );

function um_followers_activity_settings( $settings, $key ) {

    $settings['extensions']['sections'][$key]['fields'] = array_merge($settings['extensions']['sections'][$key]['fields'], array(
        array(
            'id'       	=> 'activity_followers_mention',
            'type'     	=> 'checkbox',
            'label'   	=> __( 'Enable integration with followers to convert user names to user profile links automatically (mention users)?', 'um-followers' ),
        ),
        array(
            'id'       	=> 'activity_followed_users',
            'type'     	=> 'checkbox',
            'label'   	=> __( 'Show only followed users activity in the social wall','um-followers' ),
        )
    ) );

    return $settings;
}


add_filter( 'um_email_notifications', 'um_followers_email_notifications', 10, 1 );

function um_followers_email_notifications( $email_notifications ) {

    $email_notifications['new_follower'] = array(
        'key'           => 'new_follower',
        'title'         => __( 'New Follower Notification','um-followers' ),
        'subject'       => '{follower} is now following you on {site_name}!',
        'body'          => 'Hi {followed},<br /><br />' .
            '{follower} has just followed you on {site_name}.<br /><br />' .
            'View his/her profile:<br />' .
            '{follower_profile}<br /><br />' .
            'Click on the following link to see your followers:<br />' .
            '{followers_url}<br /><br />' .
            'This is an automated notification from {site_name}. You do not need to reply.',
        'description'   => __('Send a notification to user when he receives a new review','um-followers'),
        'recipient'   => 'user',
        'default_active' => true
    );

    return $email_notifications;
}

	/***
	***	@Adds a notification type
	***/
	add_filter('um_notifications_core_log_types', 'um_followers_add_notification_type', 200 );
	function um_followers_add_notification_type( $array ) {
		
		$array['new_follow'] = array(
			'title' => __('User get followed by a person','um-followers'),
			'template' => '<strong>{member}</strong> has just followed you!',
			'account_desc' => __('When someone follows me','um-followers'),
		);
		
		return $array;
	}
	
	/***
	***	@Adds a notification icon
	***/
	add_filter('um_notifications_get_icon', 'um_followers_add_notification_icon', 10, 2 );
	function um_followers_add_notification_icon( $output, $type ) {
		if ( $type == 'new_follow' ) {
			$output = '<i class="um-icon-android-person-add" style="color: #44b0ec"></i>';
		}
		return $output;
	}


    /**
     * @param $array
     * @return mixed
     */
    function um_followers_activity_wall_privacy_dropdown_values( $array ) {

        $array[3] = __( 'Followers', 'um-followers' );
        $array[4] = __( 'People I follow', 'um-followers' );

		return $array;
	}
    add_filter('um_activity_wall_privacy_dropdown_values', 'um_followers_activity_wall_privacy_dropdown_values', 10, 1 );