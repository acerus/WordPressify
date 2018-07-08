<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	add_action( 'um_admin_custom_register_metaboxes', 'um_recaptcha_add_metabox_register' );
	function um_recaptcha_add_metabox_register( $action ) {
		//UM()->metabox()->is_loaded = true;
		
		if ( !is_admin() || !current_user_can('manage_options') ) die();

		add_meta_box(
		    "um-admin-form-register_recaptcha{" . um_recaptcha_path . "}",
            __('Google reCAPTCHA'),
            array( UM()->metabox(), 'load_metabox_form'),
            'um_form',
            'side',
            'default'
        );
		
	}
	
	add_action('um_admin_custom_login_metaboxes', 'um_recaptcha_add_metabox_login');
	function um_recaptcha_add_metabox_login( $action ) {
        //UM()->metabox()->is_loaded = true;
		
		if ( !is_admin() || !current_user_can('manage_options') ) die();

		add_meta_box(
		    "um-admin-form-login_recaptcha{" . um_recaptcha_path . "}",
            __('Google reCAPTCHA'),
            array( UM()->metabox(), 'load_metabox_form'),
            'um_form',
            'side',
            'default'
        );
		
	}
	
	add_action('um_admin_do_action__skip_recaptcha_notice', 'um_admin_do_action__skip_recaptcha_notice');
	function um_admin_do_action__skip_recaptcha_notice( $action ){
		if ( !is_admin() || !current_user_can('manage_options') ) die();

		update_option( 'um_recaptcha_notice', 1 );
		
		exit( wp_redirect( remove_query_arg( 'um_adm_action' ) ) );
	}