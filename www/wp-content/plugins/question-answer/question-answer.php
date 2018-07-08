<?php
/*
Plugin Name: Question Answer
Plugin URI: https://www.pickplugins.com/product/question-answer/
Description: Create Awesome Question and Answer Website in a Minute
Version: 1.2.23
Text Domain: question-answer
Domain Path: /languages
Author: pickplugins
Author URI: http://pickplugins.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class QuestionAnswer{
	
	public function __construct(){
	
		$this->qa_define_constants();
		
		$this->qa_declare_classes();
		$this->qa_declare_shortcodes();
		$this->qa_declare_actions();
		$this->qa_declare_pickform();
		
		$this->qa_loading_script();
		$this->qa_loading_plugin();
		$this->qa_loading_widgets();
		$this->qa_loading_functions();
		
		register_activation_hook( __FILE__, array( $this, 'qa_activation' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ));

	}
	
	public function qa_activation() {

		$class_qa_post_types = new class_qa_post_types();
		$class_qa_post_types->qa_posttype_question();
		flush_rewrite_rules();
		
		wp_insert_term( 'General', 'question_cat', array(
			'description'=> __('General', 'question-answer'),
			'slug' => 'general',
		) );
				
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_notification = $wpdb->prefix .'qa_notification';
		$table_follow = $wpdb->prefix .'qa_follow';

		$sql = "CREATE TABLE IF NOT EXISTS ".$table_notification ." (
			id int(100) NOT NULL AUTO_INCREMENT,
			q_id int(100) NOT NULL,
			a_id int(100) NOT NULL,
			c_id int(100) NOT NULL,
			user_id int(100) NOT NULL,
			subscriber_id int(100) NOT NULL,
			action VARCHAR( 50 )	NOT NULL,
			status VARCHAR( 50 )	NOT NULL,
			datetime DATETIME NOT NULL,			
			
			UNIQUE KEY id (id)
		) $charset_collate;";


		$sql = "CREATE TABLE IF NOT EXISTS ".$table_follow ." (
			id int(100) NOT NULL AUTO_INCREMENT,
			author_id int(100) NOT NULL,
			follower_id int(100) NOT NULL,
			datetime datetime NOT NULL,
			
				
			UNIQUE KEY id (id)
		) $charset_collate;";








		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		//$wpdb->query( "ALTER TABLE " . $table . " ADD datetime datetime NOT NULL" );
		
	}
	
	public function load_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'question-answer' );
		load_textdomain('question-answer', WP_LANG_DIR .'/question-answer/question-answer-'. $locale .'.mo' );
		
		load_plugin_textdomain( 'question-answer', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	
	public function qa_loading_widgets() {

		require_once( QA_PLUGIN_DIR . 'includes/classes/class-widget-leaderboard.php');
        require_once( QA_PLUGIN_DIR . 'includes/classes/class-widget-top-questions.php');
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-widget-categories.php');
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-widget-tags.php');
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-widget-website-stats.php');		
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-widget-latest-questions.php');		
				
		
		add_action( 'widgets_init', array( $this, 'qa_widget_register' ) );
	}
	
	public function qa_widget_register() {
		register_widget( 'QAWidgetLeaderboard' );
        register_widget( 'QAWidgetTopQuestions' );
		register_widget( 'QAWidgetCategories' );
		register_widget( 'QAWidgetTags' );
		register_widget( 'QAWidgetWebsiteStats' );
		register_widget( 'QAWidgetLatestQuestions' );
	}
	
	public function qa_loading_functions() {

		require_once( QA_PLUGIN_DIR . 'includes/functions-user-profile.php');
		require_once( QA_PLUGIN_DIR . 'includes/functions.php');
		require_once( QA_PLUGIN_DIR . 'includes/functions-counter.php');
		require_once( QA_PLUGIN_DIR . 'includes/functions-notification.php');


		require_once( QA_PLUGIN_DIR . 'includes/deprecated.php');

		require_once( QA_PLUGIN_DIR . 'templates/single-question/single-question-hook.php');


	}
	
	public function qa_loading_plugin() {
		
		add_action( 'activated_plugin', array( $this, 'redirect_welcome' ));
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-admin-setup-wizard.php');
	}
	
	public function qa_loading_script() {
	
		add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' );
		add_action( 'wp_enqueue_scripts', array( $this, 'qa_front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'qa_admin_scripts' ) );
	}
	
	public function qa_declare_pickform() {

		require_once( QA_PLUGIN_DIR . 'includes/pickform/class-pickform.php');
	}
	
	public function qa_declare_actions() {

		require_once( QA_PLUGIN_DIR . 'includes/actions/action-question-archive.php');
		require_once( QA_PLUGIN_DIR . 'includes/actions/action-single-question.php');
		//require_once( QA_PLUGIN_DIR . 'includes/actions/action-single-answer.php');		
		require_once( QA_PLUGIN_DIR . 'includes/actions/action-myaccount.php');
		//require_once( QA_PLUGIN_DIR . 'includes/actions/action-breadcrumb.php');
		require_once( QA_PLUGIN_DIR . 'includes/actions/action-add-question.php');

		require_once( QA_PLUGIN_DIR . 'templates/user-profile/user-profile-hook.php');

	}
	
	public function qa_declare_shortcodes() {

		require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-user-profile.php');
		require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-question-archive.php');
		require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-add-question.php');
		require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-myaccount.php');
		require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-registration.php');
		//require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-migration.php');
		// require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-breadcrumb.php');

		require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-qa-edit-account.php');
		require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-qa-my-account.php');
		require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-qa-my-questions.php');

		require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-qa-reset.php');
		require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-my-notifications.php');
		require_once( QA_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-qa-my-answers.php');
	}
	
	public function qa_declare_classes() {
		
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-post-types.php');	
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-post-meta.php');	
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-post-meta-answer.php');	
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-functions.php');
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-dashboard.php');
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-settings.php');	
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-question-column.php');	
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-answer-column.php');	
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-dynamic-css.php');	
		require_once( QA_PLUGIN_DIR . 'includes/classes/class-import.php');		
		
		
	}
	
	public function qa_define_constants() {

		$this->define('QA_PLUGIN_URL', plugins_url('/', __FILE__)  );
		$this->define('QA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		$this->define('QA_PLUGIN_NAME', __('Question Answer', 'question-answer') );
		$this->define('QA_PLUGIN_SUPPORT', 'http://www.pickplugins.com/questions/'  );
		
	}
	
	private function define( $name, $value ) {
		if( $name && $value )
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
	
	
	public function redirect_welcome($plugin){

		if( get_option( 'qa_complete_setting_wizard', 'no' ) == 'no' ) {
			if( $plugin == 'question-answer/question-answer.php' ) {
				wp_safe_redirect( admin_url( 'index.php?page=qa-setup' ) );
				exit;
			}
		}
	}
		
		
	public function qa_front_scripts(){

		wp_enqueue_script('jquery');
        wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script('jquery-ui-tabs');

        wp_enqueue_script('jquery-ui-autocomplete');
		//wp_enqueue_script('jquery-ui-sortable');

		wp_enqueue_script('qa_front_js', plugins_url( '/assets/front/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
		wp_enqueue_script('qa_front_scripts-form', plugins_url( '/assets/front/js/scripts-form.js' , __FILE__ ) , array( 'jquery' ));

        wp_enqueue_style('jquery-ui', QA_PLUGIN_URL.'assets/front/css/jquery-ui.css');
		wp_enqueue_style('qa_style', QA_PLUGIN_URL.'assets/front/css/style.css');
		wp_enqueue_style('qa_dashboard.css', QA_PLUGIN_URL.'assets/front/css/dashboard.css');
		wp_enqueue_style('notifications.css', QA_PLUGIN_URL.'assets/front/css/notifications.css');
		wp_enqueue_style('hint.css', QA_PLUGIN_URL.'assets/front/css/hint.min.css');
		wp_enqueue_style('qa-user-profile.css', QA_PLUGIN_URL.'assets/front/css/user-profile.css');



		global $post;
		$active_plugins = get_option('active_plugins');
		if( $post->post_type=='question' && in_array( 'wordpress-seo/wp-seo.php', (array) $active_plugins ) ){
			wp_enqueue_style('qa-editor-style', includes_url().'css/editor.min.css');
			wp_enqueue_style('dashicons.min.css', includes_url().'css/dashicons.min.css');
		}

		//global
		wp_enqueue_style('font-awesome', QA_PLUGIN_URL.'assets/global/css/font-awesome.css');
		wp_enqueue_style('qa_global_style', QA_PLUGIN_URL.'assets/global/css/style.css');

		// pickform
		//wp_enqueue_script('pickform', plugins_url( '/assets/global/pickform/scripts.js' , __FILE__ ) , array( 'jquery' ));
		wp_enqueue_style('pickform', QA_PLUGIN_URL.'assets/front/css/pickform.css');
		//wp_enqueue_script('jquery.steps', plugins_url( '/assets/front/js/jquery.steps.js' , __FILE__ ) , array( 'jquery' ));

		// ParaAdmin
		//wp_enqueue_script('qa_ParaAdmin', plugins_url( '/assets/global/ParaAdmin/ParaAdmin.js' , __FILE__ ) , array( 'jquery' ));
		//wp_enqueue_style('qa_paraAdmin', QA_PLUGIN_URL.'assets/global/ParaAdmin/ParaAdmin.css');

		wp_enqueue_script('plupload-all');
		wp_enqueue_script('plupload_js', plugins_url( '/assets/global/js/scripts-plupload.js' , __FILE__ ) , array( 'jquery' ));

		wp_localize_script( 'qa_front_js', 'qa_ajax', array( 'qa_ajaxurl' => admin_url( 'admin-ajax.php')));




	}

	public function qa_admin_scripts(){
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-datepicker');
		
		wp_enqueue_script('qa_admin_js', plugins_url( '/assets/admin/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
		wp_localize_script( 'qa_admin_js', 'qa_ajax', array( 'qa_ajaxurl' => admin_url( 'admin-ajax.php')));

        wp_enqueue_style('jquery-ui', QA_PLUGIN_URL.'assets/global/css/jquery-ui.css');
		wp_enqueue_style('qa_admin_style', QA_PLUGIN_URL.'assets/admin/css/style.css');
		wp_enqueue_style('qa_admin_addons', QA_PLUGIN_URL.'assets/admin/css/addons.css');		
		
		wp_enqueue_script('qa_ParaAdmin', plugins_url( '/assets/admin/ParaAdmin/js/ParaAdmin.js' , __FILE__ ) , array( 'jquery' ));		
		wp_enqueue_style('qa_paraAdmin', QA_PLUGIN_URL.'assets/admin/ParaAdmin/css/ParaAdmin.css');
		
		//global
		wp_enqueue_style('font-awesome', QA_PLUGIN_URL.'assets/global/css/font-awesome.css');
		wp_enqueue_style('qa_global_style', QA_PLUGIN_URL.'assets/global/css/style.css');
		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'qa_color_picker', plugins_url('/assets/admin/js/color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	}
	
	
} new QuestionAnswer();