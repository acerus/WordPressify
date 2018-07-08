<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@extend settings
	***/
add_filter( 'um_settings_structure', 'um_notifications_settings', 10, 1 );

function um_notifications_settings( $settings ) {
    $settings['licenses']['fields'][] = array(
        'id'      		=> 'um_notifications_license_key',
        'label'    		=> __( 'Real-time Notifications License Key', 'um-notifications' ),
        'item_name'     => 'Real-time Notifications',
        'author' 	    => 'Ultimate Member',
        'version' 	    => um_notifications_version,
    );

    $key = ! empty( $settings['extensions']['sections'] ) ? 'notifications' : '';
    $settings['extensions']['sections'][$key] = array(
        'title'     => __( 'Notifications', 'um-notifications' ),
        'fields'    => array(
            array(
                'id'       		=> 'realtime_notify',
                'type'     		=> 'checkbox',
                'label'   		=> __('Enable real-time instant notification','um-notifications'),
                'tooltip'	=> __('Turn off please If your server is getting some load.','um-notifications'),
            ),
            array(
                'id'       		=> 'notify_pos',
                'type'     		=> 'select',
                'label'    		=> __( 'Where should the notification icon appear?','um-notifications' ),
                'options' 		=> array(
                    'right' 			=> __('Right bottom','um-notifications'),
                    'left' 			=> __('Left bottom','um-notifications')
                ),
                'placeholder' 	=> __('Select...','um-notifications'),
                'conditional'	=> array( 'realtime_notify', '=', 1 ),
                'size' => 'small',
            ),
            array(
                'id'       		=> 'realtime_notify_timer',
                'type'     		=> 'text',
                'label'   		=> __('How often do you want the ajax notifier to check for new notifications? (in seconds)','um-notifications'),
                'validate' 		=> 'numeric',
                'conditional'	=> array( 'realtime_notify', '=', 1 ),
                'size' => 'small',
            ),
            array(
                'id'       		=> 'notification_icon_visibility',
                'type'     		=> 'checkbox',
                'label'   		=> __('Always display the notification icon','um-notifications'),
                'tooltip'   => __('If turned off, the icon will only show when there\'s a new notification.','um-notifications'),
            ),
            array(
                'id'       		=> 'account_tab_webnotifications',
                'type'     		=> 'checkbox',
                'label'   		=> __( 'Account Tab','um-notifications' ),
                'tooltip' 	=> __('Show or hide an account tab that shows the web notifications.','um-notifications'),
            )
        )
    );

    foreach( UM()->Notifications_API()->api()->get_log_types() as $k => $desc ) {

        $settings['extensions']['sections'][$key]['fields'] = array_merge( $settings['extensions']['sections'][$key]['fields'], array(
            array(
                'id'       		=> 'log_' . $k,
                'type'     		=> 'checkbox',
                'label'   		=> $desc['title'],
            ),
            array(
                'id'       		=> 'log_' . $k . '_template',
                'type'     		=> 'textarea',
                'label'   		=> __( 'Template', 'um-notifications' ),
                'conditional'	=> array('log_' . $k, '=', 1),
                'rows'			=> 2,
            )
        ) );
    }

    return $settings;
}