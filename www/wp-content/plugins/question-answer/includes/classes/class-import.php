<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_import{
	
	public function __construct(){

		//add_action('add_meta_boxes', array($this, 'meta_boxes_job'));
		//add_action('save_post', array($this, 'meta_boxes_job_save'));

		}
		



	public function answer_import($question_id, $answer_data){
		
		foreach($answer_data as $answer){
			
			//$userid = $answer['userid'];
			$answer = (string)$answer;
		
		
		
			if(!empty($answer)){
				
				$answer_post = array(
				'post_title'    => '#'.$question_id.' - question',
				  'post_content'  => $answer,
				  'post_type'   => 'answer',
				  'post_author'   => 0,
				);
				
				// Insert the post into the database
				//wp_insert_post( $my_post );
				$answer_id = wp_insert_post($answer_post);
				
				update_post_meta($answer_id, 'qa_answer_question_id' , $question_id);	
				
				}
			
		
			}
		
		
		
		}








	public function question_import($job_data){
		
			$taxonomy = $job_data['taxonomy'];			
			$taxonomy_terms = $job_data['taxonomy_terms'];	
				
			$is_imported = $job_data['is_imported'];
			$import_source = $job_data['import_source'];
			$import_source_id = $job_data['import_source_id'];
		
	
		
		
			//var_dump($meta_fields_data);
		
		
		
		


			$meta_query[] = array(
									'key' => 'import_source',
									'value' => $import_source,
									'compare' => '=',
									
								);
								
			$meta_query[] = array(
									'key' => 'import_source_id',
									'value' => $import_source_id,
									'compare' => '=',
									
								);					
					
		
		
			$wp_query = new WP_Query(
				array (
					'post_type' => 'question',
					'post_status' => 'any',
					'meta_query' => $meta_query,
					'posts_per_page' => -1,
					) );


		
		
			if ( $wp_query->have_posts() ) :
			// No post insert.
			else:
		
		
		
			$qa_submitted_question_status = get_option('qa_submitted_question_status');
		
			if ( is_user_logged_in() ) 
				{
					$userid = get_current_user_id();
				}
			else{
					$userid = 1;
				}
		
			$post_title = sanitize_text_field($job_data['post_title']);
			$post_content = $job_data['post_content'];				
			
			$job_post = array(
			  'post_title'    => $post_title,
			  'post_content'  => $post_content,
			  'post_status'   => $qa_submitted_question_status,
			  'post_type'   => 'question',
			  'post_author'   => $userid,
			);
			
			// Insert the post into the database
			//wp_insert_post( $my_post );
			$job_ID = wp_insert_post($job_post);
		
			//wp_set_post_terms( $job_ID, $taxonomy_terms, $taxonomy );
			
			update_post_meta($job_ID, 'is_imported' , $is_imported);
			update_post_meta($job_ID, 'import_source' , $import_source);		
			update_post_meta($job_ID, 'import_source_id' , $import_source_id);					
			//$class_job_bm_error_log = new class_job_bm_error_log();
			
			return $job_ID;

		
		endif;

		}

	}
	
//new class_job_bm_import();