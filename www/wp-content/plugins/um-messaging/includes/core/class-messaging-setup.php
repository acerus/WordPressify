<?php
namespace um_ext\um_messaging\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Messaging_Setup {
    var $settings_defaults;

    function __construct() {
        //settings defaults
        $this->settings_defaults = array(
            'profile_tab_messages'           => 1,
            'profile_tab_messages_privacy'   => 0,
            'pm_char_limit' => 200,
            'pm_block_users' => '',
            'pm_notify_period' => 86400,
            'pm_active_color' => '#0085ba',
            'new_message_on' => 1,
            'new_message_sub' => '{sender} has messaged you on {site_name}!',
            'new_message' => 'Hi {recipient},<br /><br />' .
                '{sender} has just sent you a new private message on {site_name}.<br /><br />' .
                'To view your new message(s) click the following link:<br />' .
                '{message_history}<br /><br />' .
                'This is an automated notification from {site_name}. You do not need to reply.',
        );

        $notification_types['new_pm'] = array(
            'title' => __('User get a new private message','um-messaging'),
            'template' => '<strong>{member}</strong> has just sent you a private message.',
            'account_desc' => __('When someone sends a private message to me','um-messaging'),
        );

        foreach ( $notification_types as $k => $desc ) {
            $this->settings_defaults['log_' . $k] = 1;
            $this->settings_defaults['log_' . $k . '_template'] = $desc['template'];
        }
    }


    /***
     ***	@sql setup
     ***/
    function sql_setup() {
        global $wpdb;

        if ( ! current_user_can('manage_options') ) return;
        if ( get_option('ultimatemember_messaging_db2') == um_messaging_version ) return;

        $charset_collate = $wpdb->get_charset_collate();

        $table_name1 = $wpdb->prefix . "um_conversations";
        $table_name2 = $wpdb->prefix . "um_messages";

        $sql = "
		
		CREATE TABLE $table_name1 (
		  conversation_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  user_a bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
		  user_b bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
		  last_updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  UNIQUE KEY conversation_id (conversation_id)
		) $charset_collate;
		
		CREATE TABLE $table_name2 (
		  message_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  conversation_id bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  content longtext DEFAULT '' NOT NULL,
		  status int(11) DEFAULT 0 NOT NULL,
		  author bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
		  recipient bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
		  UNIQUE KEY message_id (message_id)
		) $charset_collate;
		
		";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        update_option('ultimatemember_messaging_db2', um_messaging_version );

    }


    function set_default_settings() {
        $options = get_option( 'um_options' );
        $options = empty( $options ) ? array() : $options;

        foreach ( $this->settings_defaults as $key => $value ) {
            //set new options to default
            if ( ! isset( $options[$key] ) )
                $options[$key] = $value;

        }

        update_option( 'um_options', $options );
    }


    function run_setup() {
        $this->sql_setup();
        $this->set_default_settings();
    }

}