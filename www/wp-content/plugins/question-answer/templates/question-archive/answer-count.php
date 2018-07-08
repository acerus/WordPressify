<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

	$wp_answer_query = new WP_Query( array (
		'post_type' => 'answer',
		'post_status' => 'publish',
		'meta_query' => array(
			array(
				'key' 		=> 'qa_answer_question_id',
				'value' 	=> get_the_ID(),
				'compare'	=> '=',
			),
		),
	) );
	
	global $qa_css;
	
	$qa_color_archive_answer_count = get_option( 'qa_color_archive_answer_count' );
	if( empty( $qa_color_archive_answer_count ) ) $qa_color_archive_answer_count = '';

	
	$qa_css .= ".questions-archive .answer-count{ color: $qa_color_archive_answer_count; }";
	
	?>
	<div class="question-side-box">
	<span class="answer-count"><?php echo $wp_answer_query->found_posts; ?></span><span class="answer-text"><?php echo __('Answer', 'question-answer'); ?></span>
	</div>
