<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


	add_action( 'qa_action_breadcrumb', 'qa_action_archive_breadcrumb', 10 );
	add_action( 'qa_action_question_archive_single', 'qa_action_question_archive_single_function', 10 );
	add_action( 'qa_action_question_archive_answer_count', 'qa_action_question_archive_answer_count_function', 10 );
	add_action( 'qa_action_question_archive_view_count', 'qa_action_question_archive_view_count_function', 10 );
	add_action( 'qa_action_question_archive_vote', 'qa_action_question_archive_vote_function', 10 );
    add_action( 'qa_action_submit_search', 'qa_action_submit_search_function', 10 );


if ( ! function_exists( 'qa_action_archive_breadcrumb' ) ) {
	function qa_action_archive_breadcrumb( $wp_query ) {
		//include( QA_PLUGIN_DIR. 'templates/question-archive/single-question.php');
		include( QA_PLUGIN_DIR. 'templates/template-breadcrumb.php');
	}
}


	if ( ! function_exists( 'qa_action_question_archive_single_function' ) ) {
		function qa_action_question_archive_single_function( $wp_query ) {
			include( QA_PLUGIN_DIR. 'templates/question-archive/single-question.php');
		}
	}
	
	if ( ! function_exists( 'qa_action_question_archive_answer_count_function' ) ) {
		function qa_action_question_archive_answer_count_function() {
			include( QA_PLUGIN_DIR. 'templates/question-archive/answer-count.php');
		}
	}

	
	if ( ! function_exists( 'qa_action_question_archive_view_count_function' ) ) {
		function qa_action_question_archive_view_count_function() {
			include( QA_PLUGIN_DIR. 'templates/question-archive/view-count.php');
		}
	}	
	
	if ( ! function_exists( 'qa_action_question_archive_vote_function' ) ) {
		function qa_action_question_archive_vote_function() {
			include( QA_PLUGIN_DIR. 'templates/question-archive/vote.php');
		}
	}

    if ( ! function_exists( 'qa_action_submit_search_function' ) ) {
        function qa_action_submit_search_function() {
            include( QA_PLUGIN_DIR. 'templates/question-archive/search-hook.php');
        }
    }