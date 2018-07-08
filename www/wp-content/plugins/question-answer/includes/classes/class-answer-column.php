<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_answer_column{
	
	public function __construct(){

		add_action( 'manage_answer_posts_columns', array( $this, 'add_core_answer_columns' ), 16, 1 );
		add_action( 'manage_answer_posts_custom_column', array( $this, 'custom_columns_content' ), 10, 2 );
		//add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 2 );
		
		// add_action( 'restrict_manage_posts', array( $this, 'status_filter' ), 9, 0 ); // Filter by ticket status
		// add_filter( 'parse_query', array( $this, 'status_filter_by_status' ), 10, 1 );
		
	}
	
	public function add_core_answer_columns( $columns ) {

		$new = array();
		
		$count = 0;
		foreach ( $columns as $col_id => $col_label ) { $count++;

			if ( $count == 3 )
			$new['qa-answer-question'] = '<i class="fa fa-question"></i> ' . esc_html__( 'Question', 'question-answer' );
						
			if( 'title' === $col_id )
			$new[$col_id] = '<i class="fa fa-reply"></i> ' . esc_html__( 'Answer title', 'question-answer' );
			
			elseif ( 'author' === $col_id )
			$new[$col_id] = '' . esc_html__( 'Answered by', 'question-answer' );
			
			else
			$new[ $col_id ] = $col_label;
			
		}

		return $new;
	}
	
	public function custom_columns_content( $column, $post_id ) {

		
		
		if( 'qa-answer-question' === $column ) {
			
			$qa_answer_question_id 	= get_post_meta($post_id, 'qa_answer_question_id', true);
			
			if ( get_post_status ( $qa_answer_question_id ) ) {
			
				echo sprintf( '<a href="%s">%s</a>', "post.php?post=$qa_answer_question_id&action=edit", get_the_title($qa_answer_question_id) );
				echo '<div class="row-actions">';
				
				echo sprintf(  '<span class="edit"><a href="%s" target="_blank" rel="permalink">'.__('Edit', 'question-answer').'</a></span>', "post.php?post=$qa_answer_question_id&action=edit" );
				echo ' | ';
				echo sprintf( '<span class="view"><a href="%s" target="_blank" rel="permalink">'.__('View', 'question-answer').'</a></span>', get_the_permalink($qa_answer_question_id) );
				
				echo '</div>';
			} else {
				echo '<span class="red">'.__('This question is not exist any more !', 'question-answer').'</span> ';
			}
		}
	}
	
	public function remove_quick_edit( $actions ) {
		global $post;

		if ( $post->post_type === 'answer' ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}
	
	public function status_filter() {

		global $typenow;

		//if ( ('question' != $typenow ) || !isset( $_GET['post_status'] ) ) {
		if ( ('question' != $typenow ) ) return;
		

		$this_sort	= isset( $_GET['qa_status'] ) ? filter_input( INPUT_GET, 'qa_status', FILTER_SANITIZE_STRING ) : '';
		$all    	= ( '' 			=== $this_sort ) ? 'selected' : '';
		$pending	= ( 'pending' 	=== $this_sort ) ? 'selected' : '';
		$hold		= ( 'hold' 		=== $this_sort ) ? 'selected' : '';
		$solved		= ( 'solved' 	=== $this_sort ) ? 'selected' : '';
		$processing = ( ! isset( $_GET['qa_status'] ) || 'processing' === $this_sort ) ? 'selected' : '';
		
		$dropdown        = '<select id="qa_status" name="qa_status">';
		$dropdown        .= "<option value='' $all>" . __( 'Any status', 'question-answer' ) . "</option>";
		$dropdown        .= "<option value='pending' $pending>" . __( 'Pending', 'question-answer' ) . "</option>";
		$dropdown        .= "<option value='processing' $processing>" . __( 'On discussion', 'question-answer' ) . "</option>";
		$dropdown        .= "<option value='hold' $hold>" . __( 'On hold', 'question-answer' ) . "</option>";
		$dropdown        .= "<option value='solved' $solved>" . __( 'Solved', 'question-answer' ) . "</option>";
		$dropdown        .= '</select>';

		echo $dropdown;

	}
	
	public function status_filter_by_status( $query ) {

		global $pagenow;


		if ( is_admin()
		     && 'edit.php' == $pagenow
		     && isset( $_GET['post_type'] )
		     && 'question' == $_GET['post_type']
		     && isset( $_GET['qa_status'] )
		     && ! empty( $_GET['qa_status'] )
		     && $query->is_main_query()
		) {
			
			
			if( $_GET['qa_status'] == 'pending' ) {
				$query->set( 'post_status', 'pending' );
				return;
			}
			
			if( $_GET['qa_status'] == 'processing' ) $query->set( 'post_status', 'publish' );
			
				
			

				$meta_query = $query->get( 'meta_query' );
				if ( ! is_array( $meta_query ) ) {
					$meta_query = array_filter( (array) $meta_query );
				}
				$meta_query[] = array(
						'key'     => 'qa_question_status',
						'value'   => sanitize_text_field( $_GET['qa_status'] ),
						'compare' => '=',
						'type'    => 'CHAR',
				);
				$query->set( 'meta_query', $meta_query );
			
			//}
		}

	}
	

	
} new class_qa_answer_column();