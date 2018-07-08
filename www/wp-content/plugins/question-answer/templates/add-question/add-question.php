<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

	$class_pickform 	= new class_pickform();
	$class_qa_functions = new class_qa_functions();
	
	$qa_reCAPTCHA_enable_question		= get_option('qa_reCAPTCHA_enable_question');	
	$qa_question_login_page_id 			= get_option('qa_question_login_page_id');
	$login_page_url 					= get_permalink($qa_question_login_page_id);
	$qa_account_required_post_question 	= get_option('qa_account_required_post_question', 'yes');
	$qa_submitted_post_status 			= get_option('qa_submitted_question_status', 'pending' );
	$qa_page_myaccount 			= get_option('qa_page_myaccount', '' );	
	
	
	if(!empty($qa_page_myaccount)){
		
		$qa_page_myaccount_url = get_permalink($qa_page_myaccount);
		
		}
	else{
		$qa_page_myaccount_url = wp_login_url($_SERVER['REQUEST_URI']);
		}
	
	
	if ( is_user_logged_in() ) {
		$userid = get_current_user_id();
	} else {
		$userid = 0;
		if( $qa_account_required_post_question=='yes'){
			echo sprintf (__('Please <a href="%s">login</a> to submit question.', 'question-answer'), $qa_page_myaccount_url ) ;
			return;
		}
	}

	$post_input_fields 	= $class_qa_functions->post_type_input_fields();
		
	$post_title 	= $post_input_fields['post_title'];
	$post_content 	= $post_input_fields['post_content'];	
	$post_status 	= $post_input_fields['post_status'];		
	
	$post_taxonomies 	= $post_input_fields['post_taxonomies'];		
	$question_cat 		= $post_taxonomies['question_cat'];
	$question_tags 		= $post_taxonomies['question_tags'];
	
	$recaptcha 		= $post_input_fields['recaptcha'];
	$meta_fields = $post_input_fields['meta_fields'];
		
	

?>


<?php do_action('qa_action_breadcrumb'); ?>



<div class="question-submit pickform">
    <div class="validations">
    <?php
	
	
	//var_dump($_POST);
	
	
	if( isset($_POST['post_question_hidden']) ){

		$validations = array();
		
		
		
		if(empty($_POST['post_title'])){
			
			 $validations['post_title'] = '';
			 echo '<div class="failed"><b><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$post_title['title'].'</b> '.__('missing', 'question-answer').'.</div>';
			}
		
		if(empty($_POST['post_content'])){
			
			 $validations['post_content'] = '';
			 echo '<div class="failed"><b><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$post_content['title'].'</b> '.__('missing', 'question-answer').'.</div>';
			}		
		
		if($qa_reCAPTCHA_enable_question=='yes'){
			if(empty($_POST['g-recaptcha-response'])){
				
				 $validations['recaptcha'] = '';
				 echo '<div class="failed"><b><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$recaptcha['title'].'</b> '.__('missing', 'question-answer').'.</div>';
				}
			
			}
		
		if(!empty($post_taxonomies))
		foreach($post_taxonomies as $field_key => $field_details){
			
				if( isset( $_POST[$field_key]) )
				$valid = $class_pickform->validations($field_details, $_POST[$field_key]);
				if( !empty( $valid) ) {
				// if( empty( $_POST[$field_key] ) ) {
					$validations[$field_key] = '';
					echo '<div class="failed"><b><i class="fa fa-exclamation-circle"></i></b> '.sprintf(__( '<b>%s</b> missing', 'question-answer' ), $field_details['title'] ).'</div>';
				}
			
			}
		
		
		
		
		if(!empty($meta_fields))
		foreach( $meta_fields as $field_key => $field_details ) {
			
			if( isset( $_POST[$field_key]) )
			$valid = $class_pickform->validations($field_details, $_POST[$field_key]);
			
			if( !empty( $valid) ) {
			// if( empty( $_POST[$field_key] ) ) {
				$validations[$field_key] = '';
				echo '<div class="failed"><b><i class="fa fa-exclamation-circle"></i></b> '.sprintf(__( '<b>%s</b> missing', 'question-answer' ), $field_details['title'] ).'</div>';
			}
		}
			
			
		
		if( empty($validations) ) {
			
			$post_title_val 	= $class_pickform->sanitizations($_POST['post_title'], 'text');
			$post_content_val 	= $class_pickform->sanitizations($_POST['post_content'], 'wp_editor');		
			$post_status_val 	= $class_pickform->sanitizations($_POST['post_status'], 'select');	
			$question_category_val 	= $class_pickform->sanitizations($_POST['question_cat'], 'select');	
			$question_tags_val 		= $class_pickform->sanitizations($_POST['question_tags'], 'text');	
				
			$question_ID = wp_insert_post( 
				array(
					'post_title'    => $post_title_val,
					'post_content'  => $post_content_val,
					'post_status'   => ( $post_status_val == 'default' ) ? $qa_submitted_post_status : $post_status_val,
					'post_type'   	=> 'question',
					'post_author'   => $userid,
				)
			);
			
			
			/*
			Subscribe to question.
			*/
			
			
			//update_post_meta($question_ID,'q_subscriber',array($userid));
			
			
			wp_set_post_terms( $question_ID, $question_tags_val, 'question_tags', true );
			wp_set_post_terms( $question_ID, $question_category_val, 'question_cat' );
			

			
			foreach($meta_fields as $key=>$field_data){
				
				$meta_key = $field_data['meta_key'];						
				$input_type = $field_data['input_type'];
				
				//var_dump($_POST[$meta_key]);
				
				if(!empty($_POST[$meta_key])){
					
					if(is_array( $_POST[$meta_key])){
						
						$meta_value = serialize($_POST[$meta_key]);
						
						//var_dump($meta_value);

						
						}
					else{
						
						$meta_value = $class_pickform->sanitizations( $_POST[$meta_key], $input_type);
						//var_dump($meta_value);
						}
						
					}
				
					//var_dump($meta_value);
					//var_dump($meta_key);
								
					update_post_meta($question_ID, $meta_key, $meta_value);

				}
	
			

			update_post_meta($question_ID,'q_subscriber',array($userid));
			
			do_action('qa_action_after_question_submit', $question_ID);


			$admin_email = get_option('admin_email');
			$admin = get_user_by( 'email', $admin_email );
			$subscriber_id = $admin->ID;

			$notification_data = array();


			$notification_data['user_id'] = get_current_user_id();
			$notification_data['q_id'] = $question_ID;
			$notification_data['a_id'] = '';
			$notification_data['c_id'] = '';
			$notification_data['subscriber_id'] = $subscriber_id;
			$notification_data['action'] = 'new_question';

			do_action('qa_action_notification_save', $notification_data);



			
			//wp_safe_redirect( get_the_permalink() );


			//var_dump(get_post_stati());

			echo '<div class="success"><i class="fa fa-check"></i> '.__('Question Submitted.', 'question-answer').'</div>';				
			echo '<div class="success"><i class="fa fa-check"></i> '.__('Submission Status:', 'question-answer').' '.ucfirst($qa_submitted_post_status).'</div>';	


		} else {
			
			$post_title 	= array_merge( $post_title , array('input_values'=>$class_pickform->sanitizations($_POST['post_title'], 'text')));
			$post_content 	= array_merge( $post_content, array('input_values'=>$class_pickform->sanitizations($_POST['post_content'], 'wp_editor')));	
			$post_status 	= array_merge( $post_status, array('input_values'=>$class_pickform->sanitizations($_POST['post_status'], 'select')));	
			$question_tags 		= array_merge( $question_tags, array('input_values'=>$class_pickform->sanitizations($_POST['question_tags'], 'text')));			
			
			if(isset($_POST['question_cat']))
			$question_cat 		= array_merge($question_cat, array('input_values'=>$class_pickform->sanitizations($_POST['question_cat'], 'select')));					
		}
	} ?>
        
        
    </div>
	
    <?php do_action('qa_action_before_ask_question'); ?>
    
    <form enctype="multipart/form-data"   method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="post_question_hidden" value="Y" />
		
		<?php
		do_action('qa_action_question_submit_main');
		
		
		echo '<div class="option">';
		echo $class_pickform->field_set($post_title);
		echo '</div>';
		
		echo '<div class="option">';
		echo $class_pickform->field_set($post_content);
		echo '</div>';		
		
		echo '<div class="option">';
		echo $class_pickform->field_set($post_status);
		echo '</div>';			
		
		
		if(!empty($post_taxonomies)){
			
			foreach($post_taxonomies as $taxonomies){
				
					echo '<div class="option">';
					echo $class_pickform->field_set($taxonomies);
					echo '</div>';	

				}
			}
		
		
		if(!empty($meta_fields))
		foreach( $meta_fields as $field_key => $field_details ) {
		
			echo '<div class="option">';
			echo $class_pickform->field_set( $field_details );
			echo '</div>';
			
		}
		
		
		
		if($qa_reCAPTCHA_enable_question=='yes'){
			echo '<div class="option">';
			echo $class_pickform->field_set($recaptcha);
			echo '</div>';
		}
		
		
		?>
            
		<div class="question-submit-button"> <?php		

			
			wp_nonce_field( 'qa_question' ); ?>
			<input type="submit"  name="submit" value="<?php _e('Submit', 'question-answer'); ?>" />
		</div>
    </form>
        
	<?php do_action('qa_action_after_ask_question'); ?>
        
</div>