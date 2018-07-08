<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_shortcode_migration{
	
    public function __construct(){
		add_shortcode('qa_migration', array( $this, 'qa_migration_function') );
   	}	
		
	public function qa_migration_function($atts, $content = null ) {
			
		$atts = shortcode_atts( array(
					
		), $atts);
		
		ob_start();
		
		include( QA_PLUGIN_DIR . 'templates/template-migration.php');
		
		return ob_get_clean();
	}
	
	

} new class_qa_shortcode_migration();