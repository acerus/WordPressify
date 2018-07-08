<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_shortcode_qa_edit_account{
	
    public function __construct(){
		add_shortcode( 'qa_edit_account', array( $this, 'qa_edit_account' ) );
   	}	
		
	public function qa_edit_account($atts, $content = null ) {
			
		$atts = shortcode_atts( array(
					
		), $atts);

		global $current_user;
		$current_user_id = get_current_user_id();


		if(isset($_POST['qa_edit_account_hidden'])){

		}

		if(isset($_POST['_wpnonce']) && wp_verify_nonce( $_POST['_wpnonce'], 'qa_edit_account_nonce' ) && $_POST['qa_edit_account_hidden'] == 'Y') {


		    $display_name = sanitize_text_field($_POST['display_name']);
			$user_url = esc_url($_POST['user_url']);
			$user_description = sanitize_text_field($_POST['description']);

			$profile_photo = esc_url($_POST['profile_photo']);
			$cover_photo = esc_url($_POST['cover_photo']);

			wp_update_user( array( 'ID' => $current_user_id, 'display_name' => $display_name ) );
			wp_update_user( array( 'ID' => $current_user_id, 'user_url' => $user_url ) );



			update_user_meta( $current_user_id, 'description' , $user_description );
			update_user_meta( $current_user_id, 'profile_photo' , $profile_photo );
			update_user_meta( $current_user_id, 'cover_photo' , $cover_photo );


			//update_user_meta( $current_user_id, 'display_name' , sanitize_text_field($_POST['display_name']) );

            $success_massage = __('Profile updated.','question-answer');
		}
		else{

			$display_name = $current_user->display_name;
			$user_url = $current_user->user_url;
			$user_description = $current_user->description;


			$profile_photo = get_user_meta($current_user_id, 'profile_photo', true);
			$cover_photo = get_user_meta($current_user_id, 'cover_photo', true);



		}




		ob_start();

		?>
		<div class="qa-edit-account">

            <?php
            if(!empty($success_massage)):
            ?>
            <div class=""><i class="fa fa-check-square-o" aria-hidden="true"></i> <?php echo $success_massage; ?></div>
            <?php
            endif;
            ?>

			<form action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
				<input type="hidden" name="qa_edit_account_hidden" value="Y">

				<div class="item">
					<div class="header"><?php echo __('Display name', 'question-answer'); ?></div>
					<input type="text" name="display_name" value="<?php echo $display_name; ?>" />
				</div>

				<div class="item">
					<div class="header"><?php echo __('Website', 'question-answer'); ?></div>
					<input type="text" name="user_url" value="<?php echo $user_url; ?>" />
				</div>

				<div class="item">
					<div class="header"><?php echo __('Biographical Info', 'question-answer'); ?></div>
					<textarea name="description"><?php echo $user_description; ?></textarea>
				</div>

                <div class="item">
                    <div class="header"><?php echo __('Profile photo URL', 'question-answer'); ?></div>
                    <input type="text" name="profile_photo" value="<?php echo $profile_photo; ?>" />
                </div>


                <div class="item">
                    <div class="header"><?php echo __('Profile cover photo URL', 'question-answer'); ?></div>
                    <input type="text" name="cover_photo" value="<?php echo $cover_photo; ?>" />
                </div>



				<?php wp_nonce_field( 'qa_edit_account_nonce' ); ?>
				<input type="submit" value="<?php echo __('Update', 'question-answer'); ?>">

			</form>


		</div>
		<?php

		//include( QA_PLUGIN_DIR . 'templates/my-account/my-account.php');

		return ob_get_clean();
	}
}


new class_qa_shortcode_qa_edit_account();