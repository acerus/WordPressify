<?php
namespace um_ext\um_user_tags\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class User_Tags_Setup {
	var $settings_defaults;
	var $core_form_meta;

	function __construct() {
		//settings defaults
		$this->settings_defaults = array(
			'user_tags_max_num' => 5,
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