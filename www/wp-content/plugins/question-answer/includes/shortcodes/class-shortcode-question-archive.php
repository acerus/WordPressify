<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_shortcode_question_archive{
	
    public function __construct(){
		add_shortcode( 'question_archive', array( $this, 'question_archive' ) );
   	}	
		
	public function question_archive($atts, $content = null ) {
			
		$atts = shortcode_atts( array(
				
			'keywords'=> '',
			'cat_slug'=> '',
			'order_by'=> '',
			'qa_post_per_page'=> 10,
					
		), $atts);
					
		$keywords 			= empty( $atts['keywords'] ) ? '' : $atts['keywords'];
		$date 				= empty( $atts['date'] ) ? '' : $atts['date'];
		$category 			= empty( $atts['category'] ) ? '' : $atts['category'];
		$order_by 			= empty( $atts['order_by'] ) ? '' : $atts['order_by'];
		$order	 			= empty( $atts['order'] ) ? '' : $atts['order'];
		$filter_by	 		= empty( $atts['filter_by'] ) ? '' : $atts['filter_by'];
		$qa_post_per_page 	= empty( $atts['qa_post_per_page'] ) ? '' : $atts['qa_post_per_page'];



			
		ob_start();
		
		include( QA_PLUGIN_DIR . 'templates/question-archive/question-archive.php');

		return ob_get_clean();
	}
	
} new class_qa_shortcode_question_archive();