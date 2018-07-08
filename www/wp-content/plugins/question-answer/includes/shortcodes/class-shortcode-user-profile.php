<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_shortcode_user_profile{
	
    public function __construct(){
		add_shortcode( 'qa_user_profile', array( $this, 'qa_user_profile_display' ) );
   	}	
	
	public function qa_user_profile_display($atts, $content = null ) {
			
		$atts = shortcode_atts( array(
					
		), $atts);

		ob_start();
		include( QA_PLUGIN_DIR . 'templates/user-profile/user-profile.php');
		return ob_get_clean();
	}
	
} new class_qa_shortcode_user_profile();