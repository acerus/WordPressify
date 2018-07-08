<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 
	
	global $current_user;
	
	$qa_myaccount_show_register_form		= get_option( 'qa_myaccount_show_register_form', 'yes' );
	$qa_myaccount_show_login_form 			= get_option( 'qa_myaccount_show_login_form', 'yes' );
	$qa_myaccount_show_profile_management 	= get_option( 'qa_myaccount_show_profile_management', 'yes' );
	$qa_myaccount_show_question_list 		= get_option( 'qa_myaccount_show_question_list', 'yes' );
	$qa_myaccount_login_redirect_page 		= get_option( 'qa_myaccount_login_redirect_page', '' );	
	$qa_page_myaccount 						= get_option( 'qa_page_myaccount', '' );		


	if( $current_user->ID === 0 ) {
		
		$token = 0;
		
		if( $qa_myaccount_show_register_form == 'yes' ) {
			
			echo '<div class="qa_register">';
			echo '<h3>'.__('Register', 'question-answer').'</h3>';	
			echo do_shortcode('[qa_registration_form]');
			echo '</div>';
			
			$token = 1;
		}

		if( $qa_myaccount_show_login_form == 'yes' ) {
			
			
			if(!empty($qa_myaccount_login_redirect_page)){
				
				$login_redirect_page_url = get_permalink($qa_myaccount_login_redirect_page);
				
				}
			else{
				$login_redirect_page_url = '';
				}
			
			
			if(!empty($qa_page_myaccount)){
				
				$qa_page_myaccount_url = get_permalink($qa_page_myaccount);
				
				}
			else{
				$qa_page_myaccount_url = wp_login_url($_SERVER['REQUEST_URI']);
				}			
			
			
			
			
			
			
			//echo '<pre>'.var_export($login_redirect_page_url, true).'</pre>';
			
			
			
			echo '<div class="qa_login">';
			echo '<h3>'.__('Login', 'question-answer').'</h3>';	
			
			$args = array(
				'echo'           => true,
				'remember'       => true,
				'redirect'        => $login_redirect_page_url,				
				'form_id'        => 'loginform',
				'id_username'    => 'user_login',
				'id_password'    => 'user_pass',
				'id_remember'    => 'rememberme',
				'id_submit'      => 'wp-submit',
				'label_username' => __( 'Username or email address', 'question-answer' ),
				'label_password' => __( 'Password' , 'question-answer'),
				'label_remember' => __( 'Remember Me', 'question-answer' ),
				'label_log_in'   => __( 'Login', 'question-answer' ),
				'value_username' => '',
				'value_remember' => false
			);
			
			wp_login_form($args);
			
			echo '</div>';
			
			$token = 1;
		}
			
		if( $token == 0 )
			
			 echo sprintf(__('Please <a href="%s">login</a> to access this page', 'question-answer'), $qa_page_myaccount_url);
	}	
	else {
	
		do_action('qa_action_breadcrumb');
		
		if( $qa_myaccount_show_profile_management == 'yes' ) {
			
			do_action('qa_action_before_myaccount_profile');
			do_action('qa_action_myaccount_profile');
			do_action('qa_action_after_myaccount_profile');
			
		}
		
		
		if( $qa_myaccount_show_question_list == 'yes' ) {
			
			do_action('qa_action_before_myaccount_questions');
			do_action('qa_action_myaccount_questions');
			do_action('qa_myaccount_after__question_list');
			
		}
	}	

		