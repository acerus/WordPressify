<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_post_meta_question{
	
	public function __construct(){

		add_action('add_meta_boxes', array($this, 'meta_boxes_question'));
		add_action('save_post', array($this, 'meta_boxes_question_save'));
	}
	
	public function meta_boxes_question($post_type) {
		
		$post_types = array('question');
		if (in_array($post_type, $post_types)) {
		
			add_meta_box('question_metabox',
				__( 'Question Data Box', 'question-answer' ),
				array($this, 'question_meta_box_function'),
				$post_type,
				'side',
				'high'
			);
		}
	}
	
	public function question_meta_box_function($post) {
 
        wp_nonce_field('question_nonce_check', 'question_nonce_check_value');
		echo '<div class="para-settings question-meta">';
		
		$class_qa_functions = new class_qa_functions();
		$question_status 	= get_post_meta( $post -> ID, 'qa_question_status', true);
		
		echo '<div class="option-box">';
		echo '<p class="option-info">'.__('Question Status','question-answer').'</p>';
		echo '<select name="qa_question_status">';		
		foreach( $class_qa_functions->qa_question_status() as $key => $value ) {			
			$selected = ( $key == $question_status ) ? 'selected' : '';
			echo '<option '.$selected.' value="'.$key.'">'.$value.'</option>';
		}		
		echo '</select>';
		echo '</div>'; // option-box status
		
		
		
		$featured = get_option( 'qa_featured_questions', array() );
		$selected = ( in_array( $post->ID, $featured ) ) ? 'selected' : '';
		
		echo '<div class="option-box">';
		echo '<p class="option-info">'.__('Featured question?', 'question-answer').'</p>';
		echo '<select name="qa_question_featured">';		
		echo __('<option value="no">No</option>', 'question-answer' );
		echo sprintf( __('<option %s value="yes">Yes</option>', 'question-answer' ), $selected );
		echo '</select>';
		echo '</div>'; // option-box featured
		
		echo '</div>';
   	}
	
	public function meta_boxes_question_save($post_id){
	 
		if (!isset($_POST['question_nonce_check_value'])) return $post_id;
		$nonce = $_POST['question_nonce_check_value'];
		if (!wp_verify_nonce($nonce, 'question_nonce_check')) return $post_id;

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
	 
		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id)) return $post_id;
		} else {
			if (!current_user_can('edit_post', $post_id)) return $post_id;
		}

		$qa_question_status = stripslashes_deep( $_POST['qa_question_status'] );
		update_post_meta( $post_id, 'qa_question_status', $qa_question_status );		

		$featured_selected 	= isset( $_POST['qa_question_featured'] ) ? $_POST['qa_question_featured'] : '';
		$featured_questions	= get_option( 'qa_featured_questions', array() );
		$featured_post_key	= array_search( $post_id , $featured_questions);
		
		if( in_array( $post_id, $featured_questions ) && $featured_selected == 'no' )
			unset( $featured_questions[$featured_post_key] );
		if( ! in_array( $post_id, $featured_questions ) && $featured_selected == 'yes' )
			array_push( $featured_questions, $post_id );
		
		update_option( 'qa_featured_questions', $featured_questions );
	}
	
} new class_qa_post_meta_question();