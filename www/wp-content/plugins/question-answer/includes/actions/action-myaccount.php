<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


	add_action('qa_action_myaccount_profile','qa_action_myaccount_profile_function');
	add_action('qa_action_myaccount_questions','qa_action_myaccount_questions_function');
	
	if ( ! function_exists( 'qa_action_myaccount_profile_function' ) ) {
		function qa_action_myaccount_profile_function() {
			require_once( QA_PLUGIN_DIR .'templates/my-account/profile.php');
		}
	}
	
	if ( ! function_exists( 'qa_action_myaccount_questions_function' ) ) {
		function qa_action_myaccount_questions_function() {
			require_once( QA_PLUGIN_DIR .'templates/my-account/question-list.php');
		}
	}
	
	
	
	
	
	
	