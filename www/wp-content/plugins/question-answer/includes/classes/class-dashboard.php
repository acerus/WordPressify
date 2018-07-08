<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class class_qa_dashboard{
	
	public function __construct() {

		//add_action('add_meta_boxes', array($this, 'meta_boxes_question'));
		//add_action('save_post', array($this, 'meta_boxes_question_save'));


		add_shortcode( 'qa_dashboard', array( $this, 'qa_dashboard' ) );

		add_filter('qa_filter_dashboard_account', array( $this, 'my_account_html' ));
		add_filter('qa_filter_dashboard_account_edit', array( $this, 'edit_account_html' ));
		add_filter('qa_filter_dashboard_my_questions', array( $this, 'my_questions' ));
		add_filter('qa_filter_dashboard_my_answers', array( $this, 'my_answers' ));
		add_filter('qa_filter_dashboard_my_notifications', array( $this, 'my_notifications' ));




	}

	function my_account_html(){

		return do_shortcode('[qa_my_account]');

	}

	function edit_account_html(){

		return do_shortcode('[qa_edit_account]');

	}

	function my_questions(){

		return do_shortcode('[qa_my_questions]');

	}

	function my_answers(){

		return do_shortcode('[qa_my_answers]');

	}

	function my_notifications(){

		return do_shortcode('[qa_my_notifications]');

	}



	function dashboard_tabs(){

		$tabs['account'] =array(
			'title'=>__('Account', 'question-answer'),
			'html'=>apply_filters('qa_filter_dashboard_account',''),

		);


		$tabs['account_edit'] =array(
			'title'=>__('Account edit', 'question-answer'),
			'html'=>apply_filters('qa_filter_dashboard_account_edit',''),

		);

		$tabs['my_notifications'] =array(
			'title'=>__('My notifications', 'question-answer'),
			'html'=>apply_filters('qa_filter_dashboard_my_notifications',''),

		);

		$tabs['my_questions'] =array(
			'title'=>__('My questions', 'question-answer'),
			'html'=>apply_filters('qa_filter_dashboard_my_questions',''),

		);

		$tabs['my_answers'] =array(
			'title'=>__('My answers', 'question-answer'),
			'html'=>apply_filters('qa_filter_dashboard_my_answers',''),

		);



		return apply_filters('qa_filter_dashboard_tabs',$tabs);

	}



	public function qa_dashboard($atts, $content = null ) {
		$atts = shortcode_atts(
			array(

				'id' => 'flat',
			), $atts);

		ob_start();


		$qa_page_myaccount_id = get_option('qa_page_myaccount');
		$qa_page_myaccount_url = get_permalink($qa_page_myaccount_id);


		?>
		<div class="qa-dashboard">
			<?php


			if (is_user_logged_in() ):

				$dashboard_tabs = $this->dashboard_tabs();


				?>
				<ul class="navs">
					<?php


					foreach($dashboard_tabs as $tabs_key=>$tabs){

						$title = $tabs['title'];
						$html = $tabs['html'];


						?>
						<li>
							<a href="<?php echo $qa_page_myaccount_url; ?>?tabs=<?php echo $tabs_key; ?>">
								<?php echo $title; ?>
							</a>

						</li>
						<?php



					}
					?>
				</ul>
				<?php





				?>
				<div class="navs-content">
					<?php

					if(!empty($_GET['tabs'])){
						$current_tabs = sanitize_text_field($_GET['tabs']);

						//echo '<pre>'.var_export($current_tabs, true).'</pre>';

					}
					else{
						$current_tabs = 'account';

					}


					foreach($dashboard_tabs as $tabs_key=>$tabs){

						$title = $tabs['title'];
						$html = $tabs['html'];

						if($current_tabs==$tabs_key):

							?>
							<div class="<?php echo $tabs_key; ?>">
								<?php echo $html; ?>
							</div>
							<?php

						endif;


					}
					?>
				</div>
				<?php


			else:

				$qa_myaccount_show_register_form		= get_option( 'qa_myaccount_show_register_form', 'yes' );
				$qa_myaccount_show_login_form 			= get_option( 'qa_myaccount_show_login_form', 'yes' );
				$qa_myaccount_show_question_list 		= get_option( 'qa_myaccount_show_question_list', 'yes' );
				$qa_myaccount_login_redirect_page 		= get_option( 'qa_myaccount_login_redirect_page', '' );
				$qa_page_myaccount 						= get_option( 'qa_page_myaccount', '' );


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



			endif;

			?>
		</div>
		<?php

		return ob_get_clean();
	}



} new class_qa_dashboard();