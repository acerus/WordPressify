<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class qa_Admin_Setup_Wizard {

	private $step   = '';
	private $steps  = array();

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'qa_notice_setup_wizard') );
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_init', array( $this, 'setup_wizard' ) );
	}

	public function qa_notice_setup_wizard() {
		
		$notice_action = isset( $_GET['qa-hide-notice'] ) ? $_GET['qa-hide-notice'] : '';
		if( $notice_action == 'wizard' ) {
			update_option('qa_complete_setting_wizard', 'skip' );
		}

		if( get_option( 'qa_complete_setting_wizard', 'no' ) == 'no' ) {
			?>
			<div id="message" class="updated">
				<p><?php _e( '<strong>Welcome to Question Answer</strong> &#8211; You did not run the setup wizard yet. You can do that now.', 'question-answer' ); ?></p>
				<p class="submit">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=qa-setup' ) ); ?>" class="button-primary"><?php _e( 'Run setup wizard', 'question-answer' ); ?></a> 
					<a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'qa-hide-notice', 'wizard' ), 'qa_hide_notices_nonce', '_qa_notice_nonce' ) ); ?>"><?php _e( 'Skip setup', 'question-answer' ); ?></a>
				</p>
			</div>
			<?php
		}
	}
	
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'qa-setup', '' );
	}
	
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'qa-setup' !== $_GET['page'] ) return;

		$this->steps = array(
			'introduction' => array(
				'name'    =>  __( 'Introduction', 'question-answer' ),
				'view'    => array( $this, 'qa_setup_introduction' ),
				'handler' => ''
			),
			'pages' => array(
				'name'    =>  __( 'Page setup', 'question-answer' ),
				'view'    => array( $this, 'qa_setup_pages' ),
				'handler' => array( $this, 'qa_setup_pages_save' )
			),
			'question' => array(
				'name'    =>  __( 'Question', 'question-answer' ),
				'view'    => array( $this, 'qa_setup_question' ),
				'handler' => array( $this, 'qa_setup_question_save' )
			),
			'answer' => array(
				'name'    =>  __( 'Answer', 'question-answer' ),
				'view'    => array( $this, 'qa_setup_answer' ),
				'handler' => array( $this, 'qa_setup_answer_save' ),
			),
			'next_steps' => array(
				'name'    =>  __( 'Ready!', 'question-answer' ),
				'view'    => array( $this, 'qa_setup_ready' ),
				'handler' => ''
			)
		);
		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );
		
		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'] );
		} 
	
		wp_enqueue_style( 'qa-setup', QA_PLUGIN_URL.'/assets/admin/css/qa-welcome-setup.css' );
		
		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	public function qa_setup_introduction() {
		?>
		<h2>Welcome to Question and Answer!</h2>
		<p>Thank you for choosing this item. This quick setup wizard will help you configure the basic settings. <strong>It’s completely optional and shouldn’t take longer than five minutes.</strong></p>
		<p>No time right now? If you don’t want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime if you want.</p>
		<p class="qa-setup-actions step">
			<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="qa-button-next">Let's Go!</a>
			<a href="<?php echo esc_url( admin_url() ); ?>" class="qa-button-previous">Not right now</a>
		</p>
		<?php
	}

	public function get_next_step_link() {
		$keys = array_keys( $this->steps );
		return add_query_arg( 'step', $keys[ array_search( $this->step, array_keys( $this->steps ) ) + 1 ] );
	}
	
	public function setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php _e( 'Question Answer &rsaquo; Setup sizard', 'question-answer' ); ?></title>
			<?php wp_print_scripts( 'qa-setup' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="qa-setup wp-core-ui">
			<h1 id="qa-logo">Question Answer Setup</h1>
		<?php
	}


	public function setup_wizard_footer() {
		?>
			<?php if ( 'next_steps' === $this->step ) : ?>
				<div class="qa-return-to-dashboard"><a href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Return to the WordPress dashboard', 'question-answer' ); ?></a></div>
			<?php endif; ?>
			</body>
		</html>
		<?php
	}

	public function setup_wizard_steps() {
		$ouput_steps = $this->steps;
		array_shift( $ouput_steps );
		?>
		<ol class="qa-setup-steps">
			<?php foreach ( $ouput_steps as $step_key => $step ) : ?>
				<li class="<?php
					if ( $step_key === $this->step ) {
						echo 'active';
					} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
						echo 'done';
					}
				?>"><?php echo esc_html( $step['name'] ); ?></li>
			<?php endforeach; ?>
		</ol>
		<?php
	}

	public function setup_wizard_content() {
		echo '<div class="qa-setup-content">';
		call_user_func( $this->steps[ $this->step ]['view'] );
		echo '</div>';
	}

	public function qa_setup_pages() {
		?>
		<h1><?php _e( 'Page setup', 'question-answer' ); ?></h1>
		<form method="post">
			<p><?php printf( __( 'Your website needs a few essential %spages%s. The following will be created automatically (if they do not already exist):', 'question-answer' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=page' ) ) . '" target="_blank">', '</a>' ); ?></p>
			<table class="qa-setup-pages" cellspacing="0">
				<thead>
					<tr>
						<th class="page-name"><?php _e( 'Page name', 'question-answer' ); ?></th>
						<th class="page-description"><?php _e( 'Description', 'question-answer' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="page-name"><?php echo _x( 'Question submission', 'Page title', 'question-answer' ); ?></td>
						<td><?php _e( 'This page will display a form where visitor can submit new question.', 'question-answer' ); ?></td>
					</tr>
					<tr>
						<td class="page-name"><?php echo _x( 'Question archive', 'Page title', 'question-answer' ); ?></td>
						<td><?php _e( 'This page will show all the live questions.', 'question-answer' ); ?></td>
					</tr>
                    <tr>
                        <td class="page-name"><?php echo _x( 'User profile', 'Page title', 'question-answer' ); ?></td>
                        <td><?php _e( 'This page will show user profile.', 'question-answer' ); ?></td>
                    </tr>


					<tr>
						<td class="page-name"><?php echo _x( 'My account', 'Page title', 'question-answer' ); ?></td>
						<td><?php _e( 'This page will show your user account info.', 'question-answer' ); ?></td>
					</tr>
				</tbody>
			</table>

			<p><?php printf( __( 'Once created, these pages can be managed from your admin dashboard on the %sPages screen%s. <br> You can control which pages are shown on your website via %sAppearance > Menus%s.', 'question-answer' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=page' ) ) . '" target="_blank">', '</a>', '<a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '" target="_blank">', '</a>' ); ?></p>

			<p class="qa-setup-actions step">
				
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="qa-button-skip"><?php _e( 'Skip this step', 'question-answer' ); ?></a>
				<input type="submit" class="qa-button-next qa-input" value="<?php esc_attr_e( 'Continue', 'question-answer' ); ?>" name="save_step" />
				<?php wp_nonce_field( 'qa-setup' ); ?>
				
			</p>
		</form>
		<?php
	}
	public function qa_setup_pages_save() {
		check_admin_referer( 'qa-setup' );
			
		if( get_option('qa_page_question_post', '') == '' ) { 
		
			$question_submission_page_id = wp_insert_post(
				array(
					'post_title'    => 'Add Question',
					'post_content'  => '[qa_add_question]',
					'post_status'   => 'publish',
					'post_type'   	=> 'page',
				)
			);
			update_option( 'qa_page_question_post', $question_submission_page_id );
		}
		
		if( get_option('qa_page_question_archive', '') == '' ) {
		
			$question_archive_page_id = wp_insert_post(
				array(
					'post_title'    => 'Question Archive',
					'post_content'  => '[question_archive]',
					'post_status'   => 'publish',
					'post_type'   	=> 'page',
				)
			);
			update_option( 'qa_page_question_archive', $question_archive_page_id );
		}

		if( get_option('qa_page_user_profile', '') == '' ) {

			$question_archive_page_id = wp_insert_post(
				array(
					'post_title'    => 'QA Profile',
					'post_content'  => '[qa_user_profile]',
					'post_status'   => 'publish',
					'post_type'   	=> 'page',
				)
			);
			update_option( 'qa_page_user_profile', $question_archive_page_id );
		}



		if( get_option('qa_page_myaccount', '') == '' ) {
		
			$qa_myaccount_page_id = wp_insert_post(
				array(
					'post_title'    => 'My Account',
					'post_content'  => '[qa_dashboard]',
					'post_status'   => 'publish',
					'post_type'   	=> 'page',
				)
			);
			update_option( 'qa_page_myaccount', $qa_myaccount_page_id );
		}
	
			
		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}


	public function qa_setup_question() {
		
		// Defaults
		$qa_question_item_per_page       	= get_option( 'qa_question_item_per_page', '10' );
		$qa_question_excerpt_length   		= get_option( 'qa_question_excerpt_length', '50' );
		$qa_account_required_post_question  = get_option( 'qa_account_required_post_question', 'yes' );
		$qa_submitted_question_status   	= get_option( 'qa_submitted_question_status', 'pending' );
		
		
		?>
		<h1><?php _e( 'Question settings', 'question-answer' ); ?></h1>
		<form method="post">
			<table class="form-table">

				<tr>
					<th scope="row"><label for="qa_question_item_per_page"><?php _e( 'Item per page', 'question-answer' ); ?></label></th>
					<td>
						<input type="text" id="qa_question_item_per_page" name="qa_question_item_per_page" size="2" value="<?php echo esc_attr( $qa_question_item_per_page ) ; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="qa_question_excerpt_length"><?php _e( 'Excerpt length in question archive', 'question-answer' ); ?></label></th>
					<td>
						<input type="text" id="qa_question_excerpt_length" name="qa_question_excerpt_length" size="2" value="<?php echo esc_attr( $qa_question_excerpt_length ) ; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="qa_account_required_post_question"><?php _e( 'Account required to submit question?', 'question-answer' ); ?></label></th>
					<td>
						<select id="qa_account_required_post_question" name="qa_account_required_post_question" class="qa-enhanced-select">
							<option value="yes" <?php selected( $qa_account_required_post_question, 'yes' ); ?>><?php echo __( 'Yes', 'question-answer' ); ?></option>
							<option value="no" <?php selected( $qa_account_required_post_question, 'no' ); ?>><?php echo __( 'No', 'question-answer' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="qa_submitted_question_status"><?php _e( 'New submitted question status ?', 'question-answer' ); ?></label></th>
					<td>
						<select id="qa_submitted_question_status" name="qa_submitted_question_status" class="qa-enhanced-select">
							<option value="pending" <?php selected( $qa_submitted_question_status, 'pending' ); ?>><?php echo __( 'Pending', 'question-answer' ); ?></option>
							<option value="draft" <?php selected( $qa_submitted_question_status, 'draft' ); ?>><?php echo __( 'Draft', 'question-answer' ); ?></option>
							<option value="publish" <?php selected( $qa_submitted_question_status, 'publish' ); ?>><?php echo __( 'Published', 'question-answer' ); ?></option>
							<option value="private" <?php selected( $qa_submitted_question_status, 'private' ); ?>><?php echo __( 'Privated', 'question-answer' ); ?></option>
							<option value="trash" <?php selected( $qa_submitted_question_status, 'trash' ); ?>><?php echo __( 'Trashed', 'question-answer' ); ?></option>
						</select>
					</td>
				</tr>
			</table>
			<p class="qa-setup-actions step">
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="qa-button-skip"><?php _e( 'Skip this step', 'question-answer' ); ?></a>
				<input type="submit" class="qa-button-next qa-input" value="<?php esc_attr_e( 'Continue', 'question-answer' ); ?>" name="save_step" />
				<?php wp_nonce_field( 'qa-setup' ); ?>
			</p>
		</form>
		<?php
	}

	public function qa_setup_question_save() {
		check_admin_referer( 'qa-setup' );

		$qa_question_item_per_page 			= sanitize_text_field( $_POST['qa_question_item_per_page'] );
		$qa_question_excerpt_length  		= sanitize_text_field( $_POST['qa_question_excerpt_length'] );
		$qa_account_required_post_question 	= sanitize_text_field( $_POST['qa_account_required_post_question'] );
		$qa_submitted_question_status    	= sanitize_text_field( $_POST['qa_submitted_question_status'] );
		
		update_option( 'qa_question_item_per_page', $qa_question_item_per_page );
		update_option( 'qa_question_excerpt_length', $qa_question_excerpt_length );
		update_option( 'qa_account_required_post_question', $qa_account_required_post_question );
		update_option( 'qa_submitted_question_status', $qa_submitted_question_status );
		
		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}


	public function qa_setup_answer() {
		
		// Defaults
		$qa_answer_item_per_page       		= get_option( 'qa_answer_item_per_page', '10' );
		$qa_account_required_post_answer    = get_option( 'qa_account_required_post_answer', 'yes' );
		$qa_submitted_answer_status   		= get_option( 'qa_submitted_answer_status', 'pending' );
		
		?>
		<h1><?php _e( 'Question settings', 'question-answer' ); ?></h1>
		<form method="post">
			<table class="form-table">

				<tr>
					<th scope="row"><label for="qa_answer_item_per_page"><?php _e( 'Item per page', 'question-answer' ); ?></label></th>
					<td>
						<input type="text" id="qa_answer_item_per_page" name="qa_answer_item_per_page" size="2" value="<?php echo esc_attr( $qa_answer_item_per_page ) ; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="qa_account_required_post_answer"><?php _e( 'Account required to submit answer?', 'question-answer' ); ?></label></th>
					<td>
						<select id="qa_account_required_post_answer" name="qa_account_required_post_answer" class="qa-enhanced-select">
							<option value="yes" <?php selected( $qa_account_required_post_answer, 'yes' ); ?>><?php echo __( 'Yes', 'question-answer' ); ?></option>
							<option value="no" <?php selected( $qa_account_required_post_answer, 'no' ); ?>><?php echo __( 'No', 'question-answer' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="qa_submitted_answer_status"><?php _e( 'New submitted answer status ?', 'question-answer' ); ?></label></th>
					<td>
						<select id="qa_submitted_answer_status" name="qa_submitted_answer_status" class="qa-enhanced-select">
							<option value="pending" <?php selected( $qa_submitted_answer_status, 'pending' ); ?>><?php echo __( 'Pending', 'question-answer' ); ?></option>
							<option value="draft" <?php selected( $qa_submitted_answer_status, 'draft' ); ?>><?php echo __( 'Draft', 'question-answer' ); ?></option>
							<option value="publish" <?php selected( $qa_submitted_answer_status, 'publish' ); ?>><?php echo __( 'Published', 'question-answer' ); ?></option>
							<option value="private" <?php selected( $qa_submitted_answer_status, 'private' ); ?>><?php echo __( 'Privated', 'question-answer' ); ?></option>
							<option value="trash" <?php selected( $qa_submitted_answer_status, 'trash' ); ?>><?php echo __( 'Trashed', 'question-answer' ); ?></option>
						</select>
					</td>
				</tr>
			</table>
			<p class="qa-setup-actions step">
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="qa-button-skip"><?php _e( 'Skip this step', 'question-answer' ); ?></a>
				<input type="submit" class="qa-button-next qa-input" value="<?php esc_attr_e( 'Continue', 'question-answer' ); ?>" name="save_step" />
				<?php wp_nonce_field( 'qa-setup' ); ?>
			</p>
		</form>
		<?php
	}

	public function qa_setup_answer_save() {
		check_admin_referer( 'qa-setup' );

		$qa_answer_item_per_page 			= sanitize_text_field( $_POST['qa_answer_item_per_page'] );
		$qa_account_required_post_answer	= sanitize_text_field( $_POST['qa_account_required_post_answer'] );
		$qa_submitted_answer_status 		= sanitize_text_field( $_POST['qa_submitted_answer_status'] );
		
		update_option( 'qa_answer_item_per_page', $qa_answer_item_per_page );
		update_option( 'qa_account_required_post_answer', $qa_account_required_post_answer );
		update_option( 'qa_submitted_answer_status', $qa_submitted_answer_status );

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}


	public function qa_setup_ready() {
		update_option('qa_complete_setting_wizard', 'yes');
		?>
		
		<h1><?php _e( 'Your website is ready!', 'question-answer' ); ?></h1>
		
		<p>Your website is now complete with allmost all settings. You can customize these from your settings panel anytime you prefer.</p>
		<p>Thank You</p>
		
<!-- 

		<div class="qa-setup-help">
			<h2>To learn more watch this video.</h2>
			<iframe width="560" height="315" src="https://www.youtube.com/embed/V-FWI57sw0I?showinfo=0&fs=0" frameborder="0" allowfullscreen></iframe>
		</div>

-->
		<?php
	}
}

new qa_Admin_Setup_Wizard();
