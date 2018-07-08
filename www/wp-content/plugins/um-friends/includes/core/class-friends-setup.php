<?php
namespace um_ext\um_friends\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Friends_Setup {
    var $settings_defaults;

    function __construct() {
        //settings defaults
        $this->settings_defaults = array(
            'profile_tab_friends'           => 1,
            'profile_tab_friends_privacy'   => 0,
            'friends_show_stats' => 1,
            'friends_show_button' => 1,
            'new_friend_request_on' => 1,
            'new_friend_request_sub' => '{friend} wants to be friends with you on {site_name}',
            'new_friend_request' => 'Hi {receiver},<br /><br />' .
                '{friend} has just sent you a friend request on {site_name}.<br /><br />' .
                'View their profile to accept/reject this friendship request:<br />' .
                '{friend_profile}<br /><br />' .
                'This is an automated notification from {site_name}. You do not need to reply.',
            'new_friend_on' => 1,
            'new_friend_sub' => '{friend} has accepted your friend request',
            'new_friend' => 'Hi {receiver},<br /><br />' .
                'You are now friends with {friend} on {site_name}.<br /><br />' .
                'View their profile:<br />' .
                '{friend_profile}<br /><br />' .
                'This is an automated notification from {site_name}. You do not need to reply.',
        );

        $notification_types['new_friend_request'] = array(
            'title' => __('User get a new friend request','um-friends'),
            'template' => __('<strong>{member}</strong> has sent you a friendship request'),
            'account_desc' => __('When someone requests friendship','um-friends'),
        );

        $notification_types['new_friend'] = array(
            'title' => __('User get a new friend','um-friends'),
            'template' => __('<strong>{member}</strong> has accepted your friendship request'),
            'account_desc' => __('When someone accepts friendship','um-friends'),
        );

        foreach( $notification_types as $k => $desc ) {
            $this->settings_defaults['log_' . $k] = 1;
            $this->settings_defaults['log_' . $k . '_template'] = $desc['template'];
        }
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

	/***
	***	@sql setup
	***/
	function sql_setup() {
		global $wpdb;

		if ( !current_user_can('manage_options') ) return;
	
		//delete_option('ultimatemember_friends_db');

		if ( get_option('ultimatemember_friends_db') == um_friends_version ) return;
		
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . "um_friends";
		
		$sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  user_id1 mediumint(9) NOT NULL,
		  user_id2 mediumint(9) NOT NULL,
		  status mediumint(9) NOT NULL,
		  UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		update_option('ultimatemember_friends_db', um_friends_version );

	}


    function run_setup() {
        $this->sql_setup();
        $this->set_default_settings();
    }

}