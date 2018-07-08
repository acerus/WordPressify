<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_question_column{
	
	public function __construct(){

		add_action( 'manage_question_posts_columns', array( $this, 'add_core_question_columns' ), 16, 1 );
		add_action( 'manage_question_posts_custom_column', array( $this, 'custom_columns_content' ), 10, 2 );
		//add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 2 );
		
		add_action( 'restrict_manage_posts', array( $this, 'status_filter' ), 9, 0 ); // Filter by ticket status
		add_filter( 'parse_query', array( $this, 'status_filter_by_status' ), 10, 1 );
		
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
	
	public function add_core_question_columns( $columns ) {

		$new = array();
		
		$count = 0;
		foreach ( $columns as $col_id => $col_label ) { $count++;

			if ( $count == 3 ) 
			$new['qa-status'] = '' . esc_html__( 'Question status', 'question-answer' );
			
			if( 'title' === $col_id ) {
				$new[$col_id] = '<i class="fa fa-question-circle fs_18"></i> ' . esc_html__( 'Question title', 'question-answer' );
			
			} elseif ( 'author' === $col_id ) {
				$new[$col_id] = '' . esc_html__( 'Created by', 'question-answer' );
			
			} elseif( 'taxonomy-question_tags' === $col_id ) {
				$new[$col_id] = '' . esc_html__( 'Question tags', 'question-answer' );
			
			} elseif( 'taxonomy-question_cat' === $col_id ) {
				$new[$col_id] = '' . esc_html__( 'Questions categories', 'question-answer' );
			
			} else {
				$new[ $col_id ] = $col_label;
			}
		}

		//$new['date'] = $new['date'] = '' . esc_html__( 'Date', 'question-answer' );
		
		return $new;
	}
	
	public function custom_columns_content( $column, $post_id ) {
		switch ( $column ) {
		case 'qa-status':
			
			if( 'pending' === get_post_status($post_id) ) {
				echo '<div class="qa_queued fs_13">'.__('Pending','question-answer').'</div>';
			} else {
				
				$status_meta 	= get_post_meta( $post_id, 'qa_question_status', true );
				
				if( 'processing' === $status_meta )
					echo '<div class="qa_publish fs_13">'.__('On discussion','question-answer').'</div>';
				
				if( 'hold' === $status_meta )
					echo '<div class="qa_hold fs_13">'.__('On hold','question-answer').'</div>';
				
				if( 'solved' === $status_meta ) 
					echo '<div class="qa_solved fs_13">'.__('Solved','question-answer').'</div>';
			}
			
			
			break;

		case 'qa-activity':

			//echo 'Jaed';
			break;

		}
	}
	
	public function remove_quick_edit( $actions ) {
		global $post;

		if ( $post->post_type === 'question' ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}
	
	
} new class_qa_question_column();