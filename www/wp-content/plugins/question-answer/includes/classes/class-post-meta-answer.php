<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_post_meta_answer{
	
	public function __construct(){
		add_action('add_meta_boxes', array($this, 'meta_boxes_answer'));
		add_action('save_post', array($this, 'meta_boxes_answer_save'));
	}
	
	public function answer_meta_box_function($post) {
		wp_nonce_field('answer_nonce_check', 'answer_nonce_check_value');
		
		$qa_answer_question_id 	= get_post_meta($post -> ID, 'qa_answer_question_id', true);
		$qa_answer_is_private 	= get_post_meta($post -> ID, 'qa_answer_is_private', true);
		
		if ( get_post_status ( $qa_answer_question_id ) ) {
		
			$post_content = qa_shorten_string( get_post_field('post_content', $qa_answer_question_id), 20, false );
			?>
			
			<div class="meta_section">
				<span class="meta_section_title"><i class="fa fa-question"></i> : <?php echo get_the_title( $qa_answer_question_id ); ?></span>
				<p class="meta_section_body">
					<?php echo wpautop( $post_content ); ?>
					<a href="post.php?post=<?php echo $qa_answer_question_id; ?>&action=edit"><?php echo __('See more', 'question-answer'); ?></a> </p>
			</div>
			
			<?php
		} else {
			echo '<span class="red">'.__('This question is not exist any more !', 'question-answer').'</span> ';
		}
		
	}
	
	public function meta_boxes_answer($post_type) {
		$post_types = array('answer');

		if( in_array( $post_type, $post_types ) ) {
			
			remove_meta_box( 'commentstatusdiv', $post_type ,'normal' );
			add_meta_box( 'answer_metabox', __('Databox','qa'), array($this, 'answer_meta_box_function'), $post_type, 'side', 'high' );
		}
	}
	
	public function meta_boxes_answer_save($post_id){
		
		//$nonce = $_POST['answer_nonce_check_value'];
		if (!isset($_POST['answer_nonce_check_value'])) return $post_id;
		if (!wp_verify_nonce($nonce, 'answer_nonce_check')) return $post_id;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
	 
		if ('page' == $_POST['post_type']) { 
			if (!current_user_can('edit_page', $post_id)) return $post_id;
		} else {
			if (!current_user_can('edit_post', $post_id)) return $post_id;
		}
		
		$qa_answer_question_id 	= isset( $_POST['qa_answer_question_id'] ) ? stripslashes_deep( $_POST['qa_answer_question_id'] ) : '';
		$qa_answer_is_private 	= isset( $_POST['qa_answer_is_private'] ) ? stripslashes_deep( $_POST['qa_answer_is_private'] ) : '';
		
		update_post_meta($post_id, 'qa_answer_question_id', $qa_answer_question_id);
		update_post_meta($post_id, 'qa_answer_is_private', $qa_answer_is_private);
	}
	
} new class_qa_post_meta_answer();