<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_shortcode_registration{
	
    public function __construct(){
		add_shortcode('qa_registration_form', array( $this, 'qa_registration_form_function') );
   	}	
		
	public function qa_registration_form_function($atts, $content = null ) {
			
		$atts = shortcode_atts( array(
					
		), $atts);
		
		ob_start();
		
		include( QA_PLUGIN_DIR . 'templates/template-registration-form.php');
		
		qa_registration_function();
		
		return ob_get_clean();
	}
	
	

} new class_qa_shortcode_registration();