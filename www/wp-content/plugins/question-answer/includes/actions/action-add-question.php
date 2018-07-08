<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


	add_action( 'qa_action_breadcrumb', 'qa_action_breadcrumb_suggestion', 10 );
	
	
	// Answer action functions
	if ( ! function_exists( 'qa_action_breadcrumb_suggestion' ) ) {
		function qa_action_breadcrumb_suggestion() {
			include( QA_PLUGIN_DIR. 'templates/add-question/ajax-suggestions.php');
		}
	}
	
	