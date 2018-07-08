<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

class class_qa_shortcode_qa_my_account{
	
    public function __construct(){
		add_shortcode( 'qa_my_account', array( $this, 'qa_my_account' ) );
   	}	
		
	public function qa_my_account($atts, $content = null ) {
			
		$atts = shortcode_atts( array(
					
		), $atts);

		global $current_user;

		$classified_maker_account_page_id = get_option('classified_maker_account_page_id');
		$account_page_url = get_permalink($classified_maker_account_page_id);

		//echo '<pre>'.var_export($current_user, true).'</pre>';


		ob_start();

		?>
		<div class="qa-my-account">

			<p class="welcome">
				Welcome <strong><?php echo $current_user->display_name; ?></strong> <a href="<?php echo wp_logout_url($account_page_url); ?>"><?php echo __('Logout', 'question-answer'); ?></a>
			</p>

			<div class="item">
				<div class="header"><?php echo __('Email', 'question-answer'); ?></div>
				<?php echo $current_user->user_email; ?>
			</div>

			<div class="item">
				<div class="header"><?php echo __('Website', 'question-answer'); ?></div>
				<a href="<?php echo $current_user->user_url; ?>"><?php echo $current_user->user_url; ?></a>
			</div>

			<div class="item">
				<div class="header"><?php echo __('Biographical Info', 'question-answer'); ?></div>
				<?php echo $current_user->description; ?>
			</div>


		</div>
		<?php

		return ob_get_clean();
	}
}


new class_qa_shortcode_qa_my_account();