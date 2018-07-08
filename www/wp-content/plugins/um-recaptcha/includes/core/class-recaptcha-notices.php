<?php
namespace um_ext\um_recaptcha\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class reCAPTCHA_Notices {

	function __construct() {

		add_action('admin_notices', array(&$this, 'admin_notices'), 1);

	}
	
	/***
	***	@show main notices
	***/
	function admin_notices(){
	
		$hide_notice = get_option('um_recaptcha_notice');
		
		if ( $hide_notice ) return;
		
		$skip_this = add_query_arg( 'um_adm_action', 'skip_recaptcha_notice' );
		
		$status = UM()->options()->get('g_recaptcha_status');
		$sitekey = UM()->options()->get('g_recaptcha_sitekey');
		$secretkey = UM()->options()->get('g_recaptcha_secretkey');
		
		if ( $status && ( !$sitekey || !$secretkey ) ) {
			
			echo '<div class="updated" style="border-color: #3ba1da;"><p>';
		
			echo sprintf(__( 'Google reCAPTCHA is active on your site. However you need to fill in both your <strong>site key and secret key</strong> to start protecting your site against spam. <a href="%s">Hide this notice</a>','um-recaptcha'), $skip_this);
			
			echo '</p>';
			
			echo '<p><a href="' . admin_url('admin.php?page=um_options') . '" class="button button-primary">' . __( 'I already have the keys', 'um-recaptcha' ) . '</a>';
			echo '&nbsp;<a href="http://google.com/recaptcha" class="button-secondary" target="_blank">' . __( 'Generate your site and secret key', 'um-recaptcha' ) . '</a></p></div>';
		
		}
	}

}