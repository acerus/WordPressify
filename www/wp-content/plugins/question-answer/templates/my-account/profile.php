<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 
	
	global $current_user;
		
	if(isset($_POST['_wpnonce'])){
		
		$nonce = $_POST['_wpnonce'];
			//var_dump($nonce);
		}
	else{
		$nonce = '';
		}
	
	//var_dump($nonce);
	
	
	if(  wp_verify_nonce( $nonce, 'qa_profile' ) && !empty( $_POST ) && isset( $_POST['qa_email'] ) ) {
		
		$qa_first_name 	= isset( $_POST['qa_first_name'] ) ? sanitize_text_field($_POST['qa_first_name']) : '';
		$qa_last_name	= isset( $_POST['qa_last_name'] ) ? sanitize_text_field($_POST['qa_last_name']) : '';
		$qa_email 		= isset( $_POST['qa_email'] ) ? sanitize_email($_POST['qa_email']) : '';
		$qa_website		= isset( $_POST['qa_website'] ) ? esc_url($_POST['qa_website']) : '';
		$description 	= isset( $_POST['description'] ) ? sanitize_text_field($_POST['description']) : '';
		
		$user_data = wp_update_user( array( 
					
			'ID' 			=> $current_user->ID,
			'first_name'	=> $qa_first_name,
			'last_name'		=> $qa_last_name,
			'user_email' 	=> $qa_email,
			'user_url' 		=> $qa_website ,
			'description' 	=> $description ,
		));
		
		wp_safe_redirect( get_the_permalink() );
		
	} else {
	
		$qa_first_name 	= $current_user->user_firstname;
		$qa_last_name 	= $current_user->user_lastname ;
		$qa_email 		= $current_user->user_email;
		$qa_website 	= $current_user->user_url;
		$description 	= $current_user->description;
		
	}
	
	?>
	<h2><?php echo __('Profile Management', 'question-answer' ); ?></h2>
	
	<div class="para-settings myaccount-profile-container">
	
		<ul class="tab-nav">
			
			<li nav="1" class="nav1 active"><i class="fa fa-folder"></i><?php echo __('Name', 'question-answer' ); ?></li>
			<li nav="2" class="nav2"><i class="fa fa-envelope"></i><?php echo __('Contact', 'question-answer' ); ?></li>
			<li nav="3" class="nav3"><i class="fa fa-user-secret"></i><?php echo __('About Yourself', 'question-answer' ); ?></li>
			
		</ul>
		
		<form enctype="multipart/form-data"   method="POST" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<ul class="box">
			<li style="display: block;" class="box1 tab-box active">
				
				<div class="option-box">
					<p class="option-title"></p>
					
					<p class="option-info"><?php echo __('First Name', 'question-answer' ); ?></p>
					<input type="text" placeholder="<?php echo __('First name', 'question-answer' ); ?>" name="qa_first_name" value="<?php echo $qa_first_name; ?>" />
					
					<p class="option-info"><?php echo __('Last Name', 'question-answer' ); ?></p>
					<input type="text" placeholder="<?php echo __('Last name', 'question-answer' ); ?>" name="qa_last_name" value="<?php echo $qa_last_name; ?>" />
					
				</div>	
			</li>

			<li style="display: none;" class="box2 tab-box">

				<div class="option-box">
					<p class="option-title"></p>

					<p class="option-info"><?php echo __('Email (Required)', 'question-answer' ); ?></p>
					<input type="email" placeholder="yourname@email.com" name="qa_email" value="<?php echo $qa_email; ?>" />

					<p class="option-info"><?php echo __('Website', 'question-answer' ); ?></p>
					<input type="text" placeholder="www.yourwebsite.com" name="qa_website" value="<?php echo $qa_website; ?>" />
				</div>
			</li>
			
			<li style="display: none;" class="box3 tab-box">
				<div class="option-box">
					<p class="option-title"></p>

					<p class="option-info"><?php echo __('Biographical Info', 'question-answer' ); ?></p>
					<textarea placeholder="<?php echo __('Some text...', 'question-answer' ); ?> " name="description" rows="4" cols="50"><?php echo $description; ?></textarea>
					
				</div>
			</li>
			
			
		</ul>
		<?php wp_nonce_field( 'qa_profile' ); ?>
		<input type="submit" class="button qa-profile-savebutton" value="<?php echo __('Save Data', 'question-answer' ); ?>" />
		</form>
		
	</div>
	<br><br>
	