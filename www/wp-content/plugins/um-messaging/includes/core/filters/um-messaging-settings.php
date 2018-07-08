<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@extend settings
	***/
add_filter( 'um_settings_structure', 'um_messaging_settings', 10, 1 );

function um_messaging_settings( $settings ) {

    $settings['licenses']['fields'][] = array(
        'id'      		=> 'um_messaging_license_key',
        'label'    		=> __( 'Private Messaging License Key', 'um-messaging' ),
        'item_name'     => 'Private Messages',
        'author' 	    => 'Ultimate Member',
        'version' 	    => um_messaging_version,
    );

    $key = ! empty( $settings['extensions']['sections'] ) ? 'messaging' : '';
    $settings['extensions']['sections'][$key] = array(
        'title'     => __( 'Private Messaging', 'um-messaging' ),
        'fields'    => array(
            array(
                'id'       		=> 'pm_char_limit',
                'type'     		=> 'text',
                'label'   		=> __( 'Message character limit','um-messaging' ),
                'validate'		=> 'numeric',
                'size'          => 'small',
            ),
            array(
                'id'       		=> 'pm_block_users',
                'type'     		=> 'text',
                'label'   		=> __( 'Block users from sending messages', 'um-messaging' ),
                'tooltip' 	=> __('A comma seperated list of user IDs that cannot send messages on the site.','um-messaging'),
                'size'          => 'medium',
            ),
            array(
                'id'       		=> 'pm_active_color',
                'type'     		=> 'color',
                'label'    		=> __( 'Primary color', 'um-messaging' ),
                'validate' 		=> 'color',
                'transparent'	=> false,
            ),
            array(
                'id'       		=> 'pm_notify_period',
                'type'     		=> 'select',
                'label'    		=> __( 'Send email notifications If user did not login for','um-messaging' ),
                'tooltip'   => __( 'Send email notifications about new messages if the user\'s last login time exceeds that period.','um-messaging'),
                'options' 		=> array(
                    3600 		=> __( '1 Hour', 'um-messaging' ),
                    86400 		=> __( '1 Day', 'um-messaging' ),
                    604800 		=> __( '1 Week', 'um-messaging' ),
                    2592000  	=> __( '1 Month', 'um-messaging' ),
                ),
                'placeholder' 	=> __( 'Select...', 'um-messaging' ),
                'size'          => 'small',
            )
        )
    );

    return $settings;
}


add_filter( 'um_email_notifications', 'um_messaging_email_notifications', 10, 1 );

function um_messaging_email_notifications( $email_notifications ) {
    $email_notifications['new_message'] = array(
        'key'           => 'new_message',
        'title'         => __( 'New Message Notification','um-messaging' ),
        'subject'       => '{sender} has messaged you on {site_name}!',
        'body'          => 'Hi {recipient},<br /><br />' .
            '{sender} has just sent you a new private message on {site_name}.<br /><br />' .
            'To view your new message(s) click the following link:<br />' .
            '{message_history}<br /><br />' .
            'This is an automated notification from {site_name}. You do not need to reply.',
        'description'   => __('Send a notification to user when he receives a new private message','um-messaging'),
        'recipient'   => 'user',
        'default_active' => true
    );

    return $email_notifications;
}



	/***
	***	@Adds a notification type
	***/
	add_filter('um_notifications_core_log_types', 'um_messaging_add_notification_type', 100 );
	function um_messaging_add_notification_type( $array ) {
		
		$array['new_pm'] = array(
			'title' => __('User get a new private message','um-messaging'),
			'template' => '<strong>{member}</strong> has just sent you a private message.',
			'account_desc' => __('When someone sends a private message to me','um-messaging'),
		);
		
		return $array;
	}