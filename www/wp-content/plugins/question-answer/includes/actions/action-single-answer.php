<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


	add_action( 'qa_action_single_answer_main', 'qa_action_single_answer_main_function', 10 );
	
	if ( ! function_exists( 'qa_action_single_answer_main_function' ) ) {
		function qa_action_single_answer_main_function() {
			
			do_action('qa_action_single_answer_title');
			//do_action('qa_action_answer_status');
			
			do_action('qa_action_breadcrumb');
			
			
			do_action('qa_action_answer_single_content');
			//do_action('qa_action_single_answer_social_share');
			//do_action('qa_action_single_answer_subscriber');
			
			//do_action('qa_action_answer_posting');
			
			//do_action('qa_action_answer_section');
			
		}
	}
	
	
	add_action( 'qa_action_single_answer_title', 'qa_action_single_answer_title_function', 10 );
	//add_action( 'qa_action_single_answer_title', 'qa_action_single_answer_view_count_function', 10 );	
	
	//add_action( 'qa_action_answer_status', 'qa_action_answer_status_function', 10 );	
	
	
	//add_action( 'qa_action_single_answer_meta', 'qa_action_single_answer_meta_function', 10 );
	
	add_action( 'qa_action_answer_single_content', 'qa_action_answer_single_content_function', 20 );
	//add_action( 'qa_action_single_answer_social_share', 'qa_action_single_answer_social_share_function', 20 );	
	//add_action( 'qa_action_single_answer_subscriber', 'qa_action_single_answer_subscriber_function', 20 );	
		
	//add_action( 'qa_action_answer_section', 'qa_action_answer_section_function', 10 );
	//add_action( 'qa_action_single_answer_content', 'qa_action_single_answer_content_function', 10 );
	//add_action( 'qa_action_answer_posting', 'qa_action_answer_posting_function', 10 );
	//add_action( 'qa_action_single_answer_reply', 'qa_action_single_answer_reply_function', 10 );

	
	// Answer action functions
	if ( ! function_exists( 'qa_action_answer_section_function' ) ) {
		function qa_action_answer_section_function() {
			include( QA_PLUGIN_DIR. 'templates/single-answer/answer-section.php');
		}
	}
	
	if ( ! function_exists( 'qa_action_answer_single_content_function' ) ) {
		function qa_action_answer_single_content_function() {
			include( QA_PLUGIN_DIR. 'templates/single-answer/answer-content.php');
		}
	}
	
	if ( ! function_exists( 'qa_action_answer_posting_function' ) ) {
		function qa_action_answer_posting_function() {
			require_once( QA_PLUGIN_DIR. 'templates/single-answer/answer-posting.php');
		}
	}
	
	if ( ! function_exists( 'qa_action_single_answer_reply_function' ) ) {
		function qa_action_single_answer_reply_function() {
			include( QA_PLUGIN_DIR. 'templates/single-answer/answer-reply.php');
		}
	}
	

	
	
	// Question action functions
	if ( ! function_exists( 'qa_action_single_answer_title_function' ) ) {
		function qa_action_single_answer_title_function() {
			require_once( QA_PLUGIN_DIR. 'templates/single-answer/title.php');
		}
	}

	if ( ! function_exists( 'qa_action_single_answer_view_count_function' ) ) {
		function qa_action_single_answer_view_count_function() {
			require_once( QA_PLUGIN_DIR. 'templates/single-answer/view-count.php');
		}
	}

	if ( ! function_exists( 'qa_action_answer_status_function' ) ) {
		function qa_action_answer_status_function() {
			require_once( QA_PLUGIN_DIR. 'templates/single-answer/status.php');
		}
	}


	
	if ( ! function_exists( 'qa_action_single_answer_meta_function' ) ) {
		function qa_action_single_answer_meta_function() {
			require_once( QA_PLUGIN_DIR. 'templates/single-answer/meta.php');
		}
	}


	if ( ! function_exists( 'qa_action_single_answer_content_function' ) ) {
		function qa_action_single_answer_content_function() {
			require_once( QA_PLUGIN_DIR. 'templates/single-answer/content.php');
		}
	}
	
	if ( ! function_exists( 'qa_action_single_answer_social_share_function' ) ) {
		function qa_action_single_answer_social_share_function() {
			require_once( QA_PLUGIN_DIR. 'templates/single-answer/social-share.php');
		}
	}
	
	if ( ! function_exists( 'qa_action_single_answer_subscriber_function' ) ) {
		function qa_action_single_answer_subscriber_function() {
			require_once( QA_PLUGIN_DIR. 'templates/single-answer/subscriber.php');
		}
	}
	
	