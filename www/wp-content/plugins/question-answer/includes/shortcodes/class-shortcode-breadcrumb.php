<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_shortcode_breadcrumb{
	
    public function __construct(){
		add_shortcode('qa_breadcrumb', array( $this, 'qa_breadcrumb_function') );
   	}	
		
	public function qa_breadcrumb_function($atts, $content = null ) {
			
		$atts = shortcode_atts( array(
					
		), $atts);
		
		ob_start();
		
		include( QA_PLUGIN_DIR . 'templates/template-breadcrumb.php');
		
		return ob_get_clean();
	}
	
	

} new class_qa_shortcode_breadcrumb();