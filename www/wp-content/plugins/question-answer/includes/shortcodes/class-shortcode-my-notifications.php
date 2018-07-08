<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_shortcode_my_notifications{
	
    public function __construct(){
		add_shortcode('qa_my_notifications', array( $this, 'my_notifications') );
   	}	
		
	public function my_notifications($atts, $content = null ) {
			
		$atts = shortcode_atts( array(
					
		), $atts);
		
		ob_start();
		
		include( QA_PLUGIN_DIR . 'templates/my-notifications.php');
		

		
		return ob_get_clean();
	}
	
	

} new class_qa_shortcode_my_notifications();