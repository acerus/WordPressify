<?php
namespace um_ext\um_recaptcha\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Recaptcha_Setup {
    var $settings_defaults;

    function __construct() {
        //settings defaults
        $this->settings_defaults = array(
            'g_recaptcha_status' => 1,
            'g_recaptcha_sitekey' => '',
            'g_recaptcha_secretkey' => '',
            'g_recaptcha_language_code' => 'en',
            'g_recaptcha_theme' => 'light',
            'g_recaptcha_type' => 'image',
            'g_recaptcha_size' => 'normal',
        );
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
        $this->set_default_settings();
    }

}