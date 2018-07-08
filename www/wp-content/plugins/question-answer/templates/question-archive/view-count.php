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
	
	$qa_color_archive_view_count = get_option( 'qa_color_archive_view_count' );
	if( empty( $qa_color_archive_view_count ) ) $qa_color_archive_view_count = '';

	
	$qa_css .= ".questions-archive .view-count{ color: $qa_color_archive_view_count; }";
	
	$qa_view_count = get_post_meta(get_the_ID(), 'qa_view_count', true);
	
	if(empty($qa_view_count)){$qa_view_count = 0;}
	
	?>
	<div class="question-side-box">
	<span class="view-count"><?php echo $qa_view_count; ?></span><span class="answer-text"><?php echo __('View', 'question-answer'); ?></span>
	</div>   
