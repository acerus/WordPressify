<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_shortcode_add_question{
	
    public function __construct(){
		add_shortcode( 'qa_add_question', array( $this, 'qa_add_question_display' ) );
   	}	
	
	public function qa_add_question_display($atts, $content = null ) {
			
		$atts = shortcode_atts( array(
					
		), $atts);

		ob_start();
		include( QA_PLUGIN_DIR . 'templates/add-question/add-question.php');
		return ob_get_clean();
	}
	
} new class_qa_shortcode_add_question();