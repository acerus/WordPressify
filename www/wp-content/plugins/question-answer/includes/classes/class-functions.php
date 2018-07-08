<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class class_qa_functions{
	
	public function __construct() {

		//add_action('add_meta_boxes', array($this, 'meta_boxes_question'));
		//add_action('save_post', array($this, 'meta_boxes_question_save'));




	}





	public function faq(){

		$faq['core'] = array(
							'title'=>__('Core', 'question-answer'),
							'items'=>array(
							
							
											array(
												'question'=>__('Single question page full width issue', 'question-answer'),
												'answer_url'=>'https://www.pickplugins.com/documentation/question-answer/faq/single-question-page-full-width-issue/',
					
												),							
											
											array(
												'question'=>__('Question page 404 error', 'question-answer'),
												'answer_url'=>'https://www.pickplugins.com/documentation/question-answer/faq/question-page-404-error/',
					
												),												
											
											
												
											),

								
							);

		$faq = apply_filters('accordions_filters_faq', $faq);		

		return $faq;

		}		
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function qa_social_share_items() {
		
		$items = array(
			
			'facebook' => array(
				'name' => __('Facebook', 'question-answer'),
				'icon' => '<i class="fa fa-facebook"></i>',
				'class' => 'qa-social-facebook',
				'share' => 'https://www.facebook.com/sharer/sharer.php?u=',
				'bg_color' => '#4061a5',
				
			),
			'twitter' => array(
				'name' => __('Twitter', 'question-answer'),
				'icon' => '<i class="fa fa-twitter"></i>',
				'class' => 'qa-social-twitter',
				'share' => 'https://twitter.com/share?url=',
				'bg_color' => '#55acee',
			),
			'gplus' => array(
				'name' => __('Google Plus', 'question-answer'),
				'icon' => '<i class="fa fa-google-plus"></i>',
				'class' => 'qa-social-gplus',
				'share' => 'https://plus.google.com/share?url=',
				'bg_color' => '#e02f2f',
			),
/*

			'pinterest' => array(
				'name' => __('Pinterest', 'question-answer'),
				'icon' => '<i class="fa fa-pinterest"></i>',
				'class' => 'qa-social-pinterest',
				'share' => 'https://www.pinterest.com/pin/create/button/?url=',
				'bg_color' => '#cd151e',
			),

*/
			
		);
		
		return apply_filters( 'qa_filter_social_share_items', $items );
	}
	
	
	public function qa_breadcrumb_menu_items_function() {
		
		$page_id_ask_question_id 	= get_option( 'qa_page_question_post', '' );
		$page_id_ask_question_title = get_the_title($page_id_ask_question_id);
		
		$qa_page_myaccount_id 		= get_option( 'qa_page_myaccount', '' );
		$qa_page_myaccount_title = get_the_title($qa_page_myaccount_id);

		$qa_page_question_archive_id 		= get_option( 'qa_page_question_archive', '' );

		$current_user = wp_get_current_user();
		if( $current_user->ID != 0 ) {
			$author_id = $current_user->user_login; 
			
			}
		else{$author_id='';}
		
		
		$menu_items = array(
			
			'add_question' => array(
				'title' => empty( $page_id_ask_question_title ) ? __( 'Ask Question', 'question-answer' ) : $page_id_ask_question_title,
				'link'	=> empty( $page_id_ask_question_id ) ? '' : get_the_permalink( $page_id_ask_question_id ),
			),
			
			'qa_dashboard' => array(
				'title' => empty( $qa_page_myaccount_title ) ? __( 'Dashboard', 'question-answer' ) : $qa_page_myaccount_title,
				'link'	=> empty( $qa_page_myaccount_id ) ? '' : get_the_permalink( $qa_page_myaccount_id ),
			),
			
			'my_question' => array(
				'title' => __( 'My Question', 'question-answer' ),
				'link'	=> empty( $qa_page_question_archive_id ) ? '#' : get_the_permalink( $qa_page_question_archive_id ).'?author='.$author_id,
			),			
			
			
		);
		
		return apply_filters( 'qa_filter_breadcrumb_menu_items', $menu_items );
	}
	
	public function qa_question_archive_filter_options() {
		
		$sorter = array(
			'' => __( 'Default Sorting', 'question-answer' ),
			'title' => __( 'Sort by Title', 'question-answer' ),
			'comment_count' => __( 'Sort by Comment Count', 'question-answer' ),
			'date_older' => __( 'Sort by Older Questions', 'question-answer' ),
		);
		
		return apply_filters( 'qa_question_archive_filter_options', $sorter );
	}
	
	
	
	public function qa_question_list_sections() {
		
		$sections = array(
			'question_icon' => array(
				'css_class'	=> 'question_icon',
				'title'		=> '<i class="fa fa-angle-down"></i>',
			),
			'question_title' => array(
				'css_class'	=> 'question_title',
				'title'		=> __('Question Title', 'question-answer'),
			),
			'question_status' => array(
				'css_class'	=> 'question_status',
				'title'		=> __('Status', 'question-answer'),
			),
			'question_date' => array(
				'css_class'	=> 'question_date',
				'title'		=> __('Date', 'question-answer'),
			),
			'question_answer' => array(
				'css_class'	=> 'question_answer',
				'title'		=> __('Answers', 'question-answer'),
			),
			
		);
		
		return array_merge($sections, apply_filters( 'qa_filters_question_list_sections',array() ) );
	}
	
	public function qa_question_status() {
		return array(
			''    => __('All', 'question-answer'),
			'processing'    => __('Processing', 'question-answer'),
			'hold'			=> __('Hold', 'question-answer'),
			'solved'		=> __('Solved', 'question-answer'),
		);
	}
		
	public function qa_get_pages() {
		$array_pages[''] = __('None','question-answer');
		
		foreach( get_pages() as $page )
		if ( $page->post_title ) $array_pages[$page->ID] = $page->post_title;
		
		return $array_pages;
	}
	
	
	
	
	
	
	public function post_type_input_fields(){
		
		
		
		
	
			$input_fields = array(
								
								'recaptcha'=>array(
														'meta_key'=>'recaptcha',
														'css_class'=>'recaptcha',
														'required'=>'no', // (yes, no) is this field required.
														'display'=>'yes', // (yes, no)
														//'placeholder'=>'',
														'title'=>__('reCaptcha', 'question-answer'),
														'option_details'=>__('reCaptcha test.', 'question-answer'),					
														'input_type'=>'recaptcha', // text, radio, checkbox, select,
														'input_values'=>get_option('qa_reCAPTCHA_site_key'), // could be array
														//'field_args'=> array('size'=>'',),
														),																		
								'post_title'=>array(
														'meta_key'=>'post_title',
														'css_class'=>'post_title',
														'placeholder'=>'',
														'required'=>'yes',
														'title'=>__('Question Title', 'question-answer'),
														'option_details'=>__('Write question title', 'question-answer'),					
														'input_type'=>'text', // text, radio, checkbox, select,
														'input_values'=>'', // could be array
														//'field_args'=> array('size'=>'',),
														),
								'post_content'=>array(
														'meta_key'=>'post_content',
														'css_class'=>'post_content',
														'required'=>'yes',
														'placeholder'=>'',
														'title'=>__('Question Descriptions', 'question-answer'),
														'option_details'=>__('Write question descriptions here', 'question-answer'),					
														'input_type'=>'wp_editor', // text, radio, checkbox, select,
														'input_values'=>'', // could be array
														//'field_args'=> array('size'=>'',),
														),	
														
														
								'post_status'=>array(
														'meta_key'=>'post_status',
														'css_class'=>'post_status',
														'required'=>'yes',
														'placeholder'=>'',
														'title'=>__('Is private?', 'question-answer'),
														'option_details'=>__('If this is private question.', 'question-answer'),
														'input_type'=>'select',
														'input_values'=>'',
														'input_args'=> apply_filters( 'qa_filter_quesstion_status', array( 'default'=>__('Public', 'question-answer'), 'private'=>__('Private', 'question-answer') ) ),
														),															
																
																											
								
								'post_taxonomies'=>array(	
								
														'question_cat'=>array(
															'meta_key'=>'question_cat',
															'css_class'=>'question_cat',
															'placeholder'=>'',
															'required'=>'yes',
															'title'=>__('Question Category', 'question-answer'),
															'option_details'=>__('Select question category.', 'question-answer'),					
															'input_type'=>'select', // text, radio, checkbox, select,
															'input_values'=>'', // could be array
															'input_args'=> qa_get_terms('question_cat'),
															//'field_args'=> array('size'=>'',),
															),

														'question_tags'=>array(
															'meta_key'=>'question_tags',
															'css_class'=>'question_tags',
															'placeholder'=>'Tags 1, Tags 2',
															'title'=>__('Question tags', 'question-answer'),
															'option_details'=>__('Choose question tags, comma separated.', 'question-answer'),					
															'input_type'=>'text', // text, radio, checkbox, select,
															'input_values'=>'', // could be array
															//'input_args'=> '',
															//'field_args'=> array('size'=>'',),
															),

							

														),							
														
								'meta_fields'=>array(



/*

													'polls'=>array(
														'meta_key'=>'polls',
														'css_class'=>'polls',
														'placeholder'=>'',
														'required'=>'no',														
														'title'=>__('Polls', 'question-answer'),
														'option_details'=>__('Add your polls', 'question-answer'),					
														'input_type'=>'text_multi', // text, radio, checkbox, select,
														'input_values'=>array(time()=>'',), // could be array
														'field_args'=> array('dummy'=>'Dummy',),
														),

*/






													)
								);
	
	
		
		$input_fields_all = apply_filters( 'qa_filter_question_input_fields', $input_fields );

		return $input_fields_all;
	}
	

} new class_qa_functions();