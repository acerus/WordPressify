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
	
	
	$qa_answer_review		= get_post_meta( get_the_ID(), 'qa_answer_review', true );
	$review_count 	= empty( $qa_answer_review['reviews'] ) ? 0 : (int)$qa_answer_review['reviews'];
	
	$current_user	= wp_get_current_user();
	$author_id 		= get_post_field( 'post_author', get_the_ID() );
	$author 		= get_userdata($author_id);
	
	$status 		= isset( $qa_answer_review['users'][$current_user->ID]['type'] ) ? $qa_answer_review['users'][$current_user->ID]['type'] : '';
	
	//echo '<pre>'.var_export($status, true).'</pre>';
	
	
	
	$votted_up 		= ( $status == 'up' ) ? 'votted' : ''; 
	$votted_down 	= ( $status == 'down' ) ? 'votted' : '';
	
	
	
	?>
	
	<div class="question-side-box">
	
        <div data-id="<?php echo get_the_ID(); ?>" class="qa-single-vote qa-single-vote-<?php echo get_the_ID(); ?>">
        
            <span class="net-vote-count net-vote-count-<?php echo get_the_ID(); ?>">
                <?php echo apply_filters('qa_filter_answer_vote_count_html', $review_count); ?>
                <?php //echo $review_count; ?>
            </span>
        
            <span class="qa-thumb-up ap-tip vote-up <?php echo $votted_up; ?>" post_id="<?php echo get_the_ID(); ?>">
                <?php echo apply_filters('qa_filter_answer_vote_up_html','<i class="fa s_22 fa-thumbs-up"></i>'); ?>
            </span>
            
            <span class="qa-thumb-down ap-tip vote-down <?php echo $votted_down; ?>" post_id="<?php echo get_the_ID(); ?>">
                <?php echo apply_filters('qa_filter_answer_vote_up_html','<i class="fa s_22 fa-thumbs-down"></i>'); ?>
            </span>
        </div>
    </div>