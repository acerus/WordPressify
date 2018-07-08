<?php
namespace um_ext\um_notifications\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Notifications_Setup {
    var $settings_defaults;

    function __construct() {
        //settings defaults
        $this->settings_defaults = array(
            'realtime_notify' => 1,
            'notify_pos' => 'right',
            'realtime_notify_timer' => 45,
            'notification_icon_visibility' => 1,
            'account_tab_webnotifications' => 1,
        );

        foreach( $this->get_log_types() as $k => $desc ) {
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


    function run_setup() {
        $this->sql_setup();
        $this->setup();
        $this->set_default_settings();
    }


    /***
     ***	@sql setup
     ***/
    function sql_setup() {
        global $wpdb;

        if ( !current_user_can('manage_options') ) return;
        if ( get_option('ultimatemember_notification_db') == um_notifications_version ) return;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . "um_notifications";

        $sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  user tinytext NOT NULL,
		  status tinytext NOT NULL,
		  photo varchar(255) DEFAULT '' NOT NULL,
		  type tinytext NOT NULL,
		  url varchar(255) DEFAULT '' NOT NULL,
		  content text NOT NULL,
		  UNIQUE KEY id (id)
		) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        update_option('ultimatemember_notification_db', um_notifications_version );

    }

    /***
     ***	@setup
     ***/
    function setup() {
        $version = get_option( 'um_notifications_version' );

        if ( ! $version ) {
            $options = get_option( 'um_options' );
            $options = empty( $options ) ? array() : $options;

            //only on first install
            $page_exists = UM()->query()->find_post_id( 'page', '_um_core', 'notifications' );
            if ( ! $page_exists ) {

                $user_page = array(
                    'post_title'		=> __( 'Notifications', 'um-notifications' ),
                    'post_content'		=> '[ultimatemember_notifications]',
                    'post_name'			=> 'notifications',
                    'post_type' 	  	=> 'page',
                    'post_status'		=> 'publish',
                    'post_author'   	=> get_current_user_id(),
                    'comment_status'    => 'closed'
                );

                $post_id = wp_insert_post( $user_page );

                if ( $post_id )
                    update_post_meta( $post_id, '_um_core', 'notifications');

            } else {
                $post_id = $page_exists;
            }


            if ( $post_id ) {
	            $key = UM()->options()->get_core_page_id( 'notifications' );
                $options[ $key ] = $post_id;
            }

            update_option( 'um_options', $options );
        }
    }


    function get_log_types() {

        $array['upgrade_role'] = array(
            'title' => __('Role upgrade','um-notifications'),
            'template' => __('Your membership level has been changed from <strong>{role_pre}</strong> to <strong>{role_post}</strong>','um-notifications'),
            'account_desc' => __('When my membership level is changed','um-notifications'),
        );

        $array['comment_reply'] = array(
            'title' => __('New comment reply','um-notifications'),
            'template' => __('<strong>{member}</strong> has replied to one of your comments.','um-notifications'),
            'account_desc' => __('When a member replies to one of my comments','um-notifications'),
        );

        $array['user_comment'] = array(
            'title' => __('New user comment','um-notifications'),
            'template' => __('<strong>{member}</strong> has commented on your <strong>post</strong>. <span class="b1">"{comment_excerpt}"</span>','um-notifications'),
            'account_desc' => __('When a member comments on my posts','um-notifications'),
        );

        $array['guest_comment'] = array(
            'title' => __('New guest comment','um-notifications'),
            'template' => __('A guest has commented on your <strong>post</strong>. <span class="b1">"{comment_excerpt}"</span>','um-notifications'),
            'account_desc' => __('When a guest comments on my posts','um-notifications'),
        );

        $array['profile_view'] = array(
            'title' => __('User view profile','um-notifications'),
            'template' => __('<strong>{member}</strong> has viewed your profile.','um-notifications'),
            'account_desc' => __('When a member views my profile','um-notifications'),
        );

        $array['profile_view_guest'] = array(
            'title' => __('Guest view profile','um-notifications'),
            'template' => __('A guest has viewed your profile.','um-notifications'),
            'account_desc' => __('When a guest views my profile','um-notifications'),
        );

        $array = apply_filters( 'um_notifications_core_log_types', $array );

        return $array;

    }

}