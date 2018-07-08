<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_shortcode_qa_reset{
	
    public function __construct(){
		add_shortcode('qa_reset', array( $this, 'qa_reset') );
   	}	
		
	public function qa_reset($atts, $content = null ) {
			
		$atts = shortcode_atts( array(
					
		), $atts);
		
		ob_start();
		
		include( QA_PLUGIN_DIR . 'templates/qa-reset.php');
		
		
		
		return ob_get_clean();
	}
	
	

} new class_qa_shortcode_qa_reset();