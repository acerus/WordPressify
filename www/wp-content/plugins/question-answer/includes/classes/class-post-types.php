<?php

/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_post_types{
	
	public function __construct(){
		
		add_action( 'init', array( $this, 'qa_posttype_question' ), 0 );
		add_action( 'init', array( $this, 'qa_posttype_answer' ), 0 );
        add_action( 'init', array( $this, 'qa_posttype_qa_keyword' ), 0 );
		//add_action( 'init', array( $this, 'qa_register_post_status'), 10, 0 );
		
	}
	
	public function qa_posttype_question(){
		if ( post_type_exists( "question" ) )
		return;

		$singular  = __( 'Question', 'question-answer' );
		$plural    = __( 'Questions', 'question-answer' );
	 
	 
		register_post_type( "question",
			apply_filters( "register_post_type_question", array(
				'labels' => array(
					'name' 					=> $plural,
					'singular_name' 		=> $singular,
					'menu_name'             => __( $singular, 'question-answer' ),
					'all_items'             => sprintf( __( 'All %s', 'question-answer' ), $plural ),
					'add_new' 				=> __( 'Add '.$singular, 'question-answer' ),
					'add_new_item' 			=> sprintf( __( 'Add %s', 'question-answer' ), $singular ),
					'edit' 					=> __( 'Edit', 'question-answer' ),
					'edit_item' 			=> sprintf( __( 'Edit %s', 'question-answer' ), $singular ),
					'new_item' 				=> sprintf( __( 'New %s', 'question-answer' ), $singular ),
					'view' 					=> sprintf( __( 'View %s', 'question-answer' ), $singular ),
					'view_item' 			=> sprintf( __( 'View %s', 'question-answer' ), $singular ),
					'search_items' 			=> sprintf( __( 'Search %s', 'question-answer' ), $plural ),
					'not_found' 			=> sprintf( __( 'No %s found', 'question-answer' ), $plural ),
					'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'question-answer' ), $plural ),
					'parent' 				=> sprintf( __( 'Parent %s', 'question-answer' ), $singular )
				),
				'description' => sprintf( __( 'This is where you can create and manage %s.', 'question-answer' ), $plural ),
				'public' 				=> true,
				'show_ui' 				=> true,
				'capability_type' 		=> 'post',
				'map_meta_cap'          => true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false,
				'rewrite' 				=> true,
				'query_var' 			=> true,
				'supports' 				=> array('title','editor','author','comments','custom-fields'),
				'show_in_nav_menus' 	=> false,
				//'taxonomies' => array('question_tags'),
				'menu_icon' => 'dashicons-editor-help',
			) )
		); 
			
			$singular  = __( 'Question Category', 'question-answer' );
			$plural    = __( 'Questions Categories', 'question-answer' );
	 
			register_taxonomy( "question_cat",
				apply_filters( 'register_taxonomy_question_cat_object_type', array( 'question' ) ),
	       	 	apply_filters( 'register_taxonomy_question_cat_args', array(
		            'hierarchical' 			=> true,
		            'show_admin_column' 	=> true,					
		            'update_count_callback' => '_update_post_term_count',
		            'label' 				=> $plural,
		            'labels' => array(
						'name'              => $plural,
						'singular_name'     => $singular,
						'menu_name'         => ucwords( $plural ),
						'search_items'      => sprintf( __( 'Search %s', 'question-answer' ), $plural ),
						'all_items'         => sprintf( __( 'All %s', 'question-answer' ), $plural ),
						'parent_item'       => sprintf( __( 'Parent %s', 'question-answer' ), $singular ),
						'parent_item_colon' => sprintf( __( 'Parent %s:', 'question-answer' ), $singular ),
						'edit_item'         => sprintf( __( 'Edit %s', 'question-answer' ), $singular ),
						'update_item'       => sprintf( __( 'Update %s', 'question-answer' ), $singular ),
						'add_new_item'      => sprintf( __( 'Add New %s', 'question-answer' ), $singular ),
						'new_item_name'     => sprintf( __( 'New %s Name', 'question-answer' ),  $singular )
	            	),
		            'show_ui' 				=> true,
		            'public' 	     		=> true,
				    'rewrite' => array(
						'slug' => 'question_cat', // This controls the base slug that will display before each term
						'with_front' => false, // Don't display the category base before "/locations/"
						'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
				),
		        ) )
		    );
			
			
			
			$singular  = __( 'Question Tag', 'question-answer' );
			$plural    = __( 'Question Tags', 'question-answer' );
	 
			register_taxonomy( "question_tags",
				apply_filters( 'register_taxonomy_question_tag_object_type', array( 'question' ) ),
				apply_filters( 'register_taxonomy_question_tag_args', array(
					'hierarchical' 			=> false,
					'show_admin_column' 	=> true,					
					'update_count_callback' => '_update_post_term_count',
					'label' 				=> $plural,
					'labels' => array(
						'name'              => $plural,
						'singular_name'     => $singular,
						'menu_name'         => ucwords( $plural ),
						'search_items'      => sprintf( __( 'Search %s', 'question-answer' ), $plural ),
						'all_items'         => __(  sprintf( 'All %s',$plural   ), 'question-answer' ),
						'parent_item'       => sprintf( __( 'Parent %s', 'question-answer' ), $singular ),
						'parent_item_colon' => sprintf( __( 'Parent %s:', 'question-answer' ), $singular ),
						'edit_item'         => sprintf( __( 'Edit %s', 'question-answer' ), $singular ),
						'update_item'       => sprintf( __( 'Update %s', 'question-answer' ), $singular ),
						'add_new_item'      => sprintf( __( 'Add New %s', 'question-answer' ), $singular ),
						'new_item_name'     => sprintf( __( 'New %s Name', 'question-answer' ),  $singular )
					),
					'show_ui' 				=> true,
					'public' 	     		=> true,
					'rewrite' => array(
						'slug' => 'question_tags', // This controls the base slug that will display before each term
						'with_front' => false, // Don't display the category base before "/locations/"
						'hierarchical' => false // This will allow URL's like "/locations/boston/cambridge/"
				),
				) )
			);
			
			
		}
		
	public function qa_posttype_answer(){
		
		if ( post_type_exists( "answer" ) ) return;

		$singular  = __( 'Answer', 'question-answer' );
		$plural    = __( 'Answers', 'question-answer' );
	 
	 
		register_post_type( "answer",
			apply_filters( "register_post_type_answer", array(
				'labels' => array(
					'name' 					=> $plural,
					'singular_name' 		=> $singular,
					'menu_name'             => __( $singular, 'question-answer' ),
					'all_items'             => __( sprintf(  'All %s', $plural ), 'question-answer' ),
					'add_new' 				=> __( 'Add '.$singular, 'question-answer' ),
					'add_new_item' 			=> sprintf( __( 'Add %s', 'question-answer' ), $singular ),
					'edit' 					=> __( 'Edit', 'question-answer' ),
					'edit_item' 			=> sprintf( __( 'Edit %s', 'question-answer' ), $singular ),
					'new_item' 				=> sprintf( __( 'New %s', 'question-answer' ), $singular ),
					'view' 					=> sprintf( __( 'View %s', 'question-answer' ), $singular ),
					'view_item' 			=> sprintf( __( 'View %s', 'question-answer' ), $singular ),
					'search_items' 			=> sprintf( __( 'Search %s', 'question-answer' ), $plural ),
					'not_found' 			=> sprintf( __( 'No %s found', 'question-answer' ), $plural ),
					'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'question-answer' ), $plural ),
					'parent' 				=> sprintf( __( 'Parent %s', 'question-answer' ), $singular )
				),
				'description' => sprintf( __( 'This is where you can create and manage %s.', 'question-answer' ), $plural ),
				'public' 				=> true,
				'show_ui' 				=> true,
				'capability_type' 		=> 'post',
				'map_meta_cap'          => true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false,
				'rewrite' 				=> true,
				'query_var' 			=> true,
				'supports' 				=> array('title','editor','author','comments','custom-fields'),
				'show_in_nav_menus' 	=> false,
				'show_in_menu' 			=> 'edit.php?post_type=question',
				'menu_icon' => 'dashicons-megaphone',
			) )
		); 

    }




    public function qa_posttype_qa_keyword(){

        if ( post_type_exists( "qa_keyword" ) ) return;

        $singular  = __( 'Keyword', 'question-answer' );
        $plural    = __( 'Keywords', 'question-answer' );


        register_post_type( "qa_keyword",
            apply_filters( "register_post_type_qa_keyword", array(
                'labels' => array(
                    'name' 					=> $plural,
                    'singular_name' 		=> $singular,
                    'menu_name'             => __( $singular, 'question-answer' ),
                    'all_items'             => __( sprintf(  'All %s', $plural ), 'question-answer' ),
                    'add_new' 				=> __( 'Add '.$singular, 'question-answer' ),
                    'add_new_item' 			=> sprintf( __( 'Add %s', 'question-answer' ), $singular ),
                    'edit' 					=> __( 'Edit', 'question-answer' ),
                    'edit_item' 			=> sprintf( __( 'Edit %s', 'question-answer' ), $singular ),
                    'new_item' 				=> sprintf( __( 'New %s', 'question-answer' ), $singular ),
                    'view' 					=> sprintf( __( 'View %s', 'question-answer' ), $singular ),
                    'view_item' 			=> sprintf( __( 'View %s', 'question-answer' ), $singular ),
                    'search_items' 			=> sprintf( __( 'Search %s', 'question-answer' ), $plural ),
                    'not_found' 			=> sprintf( __( 'No %s found', 'question-answer' ), $plural ),
                    'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'question-answer' ), $plural ),
                    'parent' 				=> sprintf( __( 'Parent %s', 'question-answer' ), $singular )
                ),
                'description' => sprintf( __( 'This is where you can create and manage %s.', 'question-answer' ), $plural ),
                'public' 				=> true,
                'show_ui' 				=> true,
                'capability_type' 		=> 'post',
                'map_meta_cap'          => true,
                'publicly_queryable' 	=> true,
                'exclude_from_search' 	=> false,
                'hierarchical' 			=> false,
                'rewrite' 				=> true,
                'query_var' 			=> true,
                'supports' 				=> array('title','editor','author','comments','custom-fields'),
                'show_in_nav_menus' 	=> false,
                'show_in_menu' 			=> 'edit.php?post_type=question',
                'menu_icon' => 'dashicons-megaphone',
            ) )
        );




    }












    public function qa_register_post_status() {
		
		$class_qa_functions = new class_qa_functions();
		$qa_question_status = $class_qa_functions->qa_question_status();
		
		foreach ( $qa_question_status as $id => $custom_status ) {

			$args = array(
				'label'                     => $custom_status,
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( "$custom_status <span class='count'>(%s)</span>", "$custom_status <span class='count'>(%s)</span>", 'question-answer' ),
			);

			register_post_status( $id, $args );
		}
		
	}
	
	
} new class_qa_post_types();