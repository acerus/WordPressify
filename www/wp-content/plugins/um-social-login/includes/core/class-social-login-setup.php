<?php
namespace um_ext\um_social_login\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Social_Login_Setup {
    var $settings_defaults;
    var $core_form_meta;
    var $networks;

    function __construct() {
        //settings defaults
        $this->settings_defaults = array(
            'activity_posts_num' => 10,
            'activity_posts_num_mob' => 5,
            'activity_init_comments_count' => 2,
            'activity_load_comments_count' => 10,
            'activity_order_comment' => 'asc',
            'activity_post_truncate' => 25,
            'activity_enable_privacy' => 1,
            'activity_trending_days' => 7,
            'activity_require_login' => 0,
            'activity_need_to_login' => 2,
            'activity_followers_mention' => 1,
            'activity_followed_users' => 0,
            'activity_friends_users' => 0,
        );

        $this->core_form_meta = array(
            '_um_custom_fields' => 'a:5:{s:10:"user_login";a:15:{s:5:"title";s:8:"Username";s:7:"metakey";s:10:"user_login";s:4:"type";s:4:"text";s:5:"label";s:8:"Username";s:8:"required";i:1;s:6:"public";i:1;s:8:"editable";i:0;s:8:"validate";s:15:"unique_username";s:9:"min_chars";i:3;s:9:"max_chars";i:24;s:8:"position";s:1:"1";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:10:"first_name";a:12:{s:5:"title";s:10:"First Name";s:7:"metakey";s:10:"first_name";s:4:"type";s:4:"text";s:5:"label";s:10:"First Name";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:8:"position";s:1:"2";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:9:"last_name";a:12:{s:5:"title";s:9:"Last Name";s:7:"metakey";s:9:"last_name";s:4:"type";s:4:"text";s:5:"label";s:9:"Last Name";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:8:"position";s:1:"3";s:8:"in_group";s:0:"";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";}s:10:"user_email";a:13:{s:5:"title";s:14:"E-mail Address";s:7:"metakey";s:10:"user_email";s:4:"type";s:4:"text";s:5:"label";s:14:"E-mail Address";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:8:"validate";s:12:"unique_email";s:8:"position";s:1:"4";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:9:"_um_row_1";a:5:{s:4:"type";s:3:"row";s:2:"id";s:9:"_um_row_1";s:8:"sub_rows";s:1:"1";s:4:"cols";s:1:"1";s:6:"origin";s:9:"_um_row_1";}}',
            '_um_mode' => 'register',
            '_um_core' => 'social',
            '_um_register_use_custom_settings' => 0,
        );


        $this->networks['facebook'] = array(
            'name' => __('Facebook','um-social-login'),
            'button' => __('Sign in with Facebook','um-social-login'),
            'color' => '#fff',
            'bg' => '#3b5998',
            'bg_hover' => '#324D84',
            'icon' => 'um-faicon-facebook',
            'opts' => array(
                'facebook_app_id' => __('App ID','um-social-login'),
                'facebook_app_secret' => __('App Secret','um-social-login'),
            ),
            'sync' => array(
                'handle' => 'facebook_handle',
                'link' => 'facebook_link',
                'photo_url' => 'http://graph.facebook.com/{id}/picture?type=square',
            ),
        );

        $this->networks['twitter'] = array(
            'name' => __('Twitter','um-social-login'),
            'button' => __('Sign in with Twitter','um-social-login'),
            'color' => '#fff',
            'bg' => '#55acee',
            'bg_hover' => '#4997D2',
            'icon' => 'um-faicon-twitter',
            'opts' => array(
                'twitter_consumer_key' => __('Consumer Key','um-social-login'),
                'twitter_consumer_secret' => __('Consumer Secret','um-social-login'),
            ),
            'sync' => array(
                'handle' => 'twitter_handle',
                'link' => 'twitter_link',
                'photo_url_dyn' => 'twitter_photo_url_dyn',
            ),
        );

        $this->networks['google'] = array(
            'name' => __('Google+','um-social-login'),
            'button' => __('Sign in with Google+','um-social-login'),
            'color' => '#fff',
            'bg' => '#dd4b39',
            'bg_hover' => '#BE4030',
            'icon' => 'um-faicon-google-plus',
            'opts' => array(
                'google_client_id' => __('Client ID','um-social-login'),
                'google_client_secret' => __('Client secret','um-social-login'),
            ),
            'sync' => array(
                'handle' => 'google_handle',
                'link' => 'google_link',
                'photo_url_dyn' => 'google_photo_url_dyn',
            ),
        );

        $this->networks['linkedin'] = array(
            'name' => __('LinkedIn','um-social-login'),
            'button' => __('Sign in with LinkedIn','um-social-login'),
            'color' => '#fff',
            'bg' => '#0976b4',
            'bg_hover' => '#07659B',
            'icon' => 'um-faicon-linkedin',
            'opts' => array(
                'linkedin_api_key' => __('API Key','um-social-login'),
                'linkedin_api_secret' => __('API Secret','um-social-login'),
            ),
            'sync' => array(
                'handle' => 'linkedin_handle',
                'link' => 'linkedin_link',
                'photo_url_dyn' => 'linkedin_photo_url_dyn',
            ),
        );

        $this->networks['instagram'] = array(
            'name' => __('Instagram','um-social-login'),
            'button' => __('Sign in with Instagram','um-social-login'),
            'color' => '#fff',
            'bg' => '#3f729b',
            'bg_hover' => '#4480aa',
            'icon' => 'um-faicon-instagram',
            'opts' => array(
                'instagram_client_id' => __('Client ID','um-social-login'),
                'instagram_client_secret' => __('Client Secret','um-social-login'),
            ),
            'sync' => array(
                'handle' => 'instagram_handle',
                'link' => 'instagram_link',
                'photo_url_dyn' => 'instagram_photo_url_dyn',
            ),
        );

        $this->networks['vk'] = array(
            'name' => __('VK','um-social-login'),
            'button' => __('Sign in with VK','um-social-login'),
            'color' => '#fff',
            'bg' => '#45668e',
            'bg_hover' => '#395f8e',
            'icon' => 'um-faicon-vk',
            'opts' => array(
                'vk_api_key' => __('API Key','um-social-login'),
                'vk_api_secret' => __('API Secret','um-social-login'),
            ),
            'sync' => array(
                'handle' => 'vk_handle',
                'link' => 'vk_link',
                'photo_url_dyn' => 'vk_photo_url_dyn',
            ),
        );

        $this->networks = apply_filters( 'um_social_login_networks', $this->networks );

        foreach ( $this->networks as $network_id => $array ) {

            $this->settings_defaults['enable_' . $network_id] = 0;

            if ( isset( $array['opts'] ) ) {
                foreach ( $array['opts'] as $opt_id => $title ) {
                    $this->settings_defaults[$opt_id] = '';
                }
            }
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
        $this->setup();
        $this->set_default_settings();
    }


    /***
     ***	@setup
     ***/
    function setup() {

        if ( !current_user_can('manage_options') ) return;

        $um_social_login_form_installed = get_option( 'um_social_login_form_installed' );

        $has_form_installed = get_post_status( $um_social_login_form_installed );

        if ( in_array( $has_form_installed, array( 'publish', 'draft', 'trash' ) )  )
            return;

        $user_id = get_current_user_id();

        $form = array(
            'post_type' 	  	=> 'um_form',
            'post_title'		=> __('Social Registration','um-social-login'),
            'post_status'		=> 'publish',
            'post_author'   	=> $user_id,
        );

        $form_id = wp_insert_post( $form );

        foreach ( $this->core_form_meta as $key => $value ) {
            if ( $key == '_um_custom_fields' ) {
                $array = unserialize( $value );
                update_post_meta( $form_id, $key, $array );
            } else {
                update_post_meta($form_id, $key, $value);
            }
        }

        update_post_meta( $form_id, '_um_social_login_form', 1 );
        update_option('um_social_login_form_installed', $form_id);

    }
}