<?php
/**
 * Setting up Jobify
 *
 * @see https://github.com/astoundify/setup-guide
 * @see https://github.com/astoundify/content-importer
 * @see https://github.com/astoundify/themeforest-updater
 * @see https://github.com/facetwp/use-child-theme
 *
 * @since 3.0.0
 * @package Jobify
 * @category Admin
 */
class Jobify_Setup {

	/**
	 * Start things up.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function init() {
		if ( ! is_admin() ) {
			return;
		}

		self::includes();

		// set early
		add_filter( 'astoundify_contentimporter_required_plugins', array( __CLASS__, 'set_required_plugins' ) );
		add_filter( 'astoundify_contentimporter_recommended_plugins', array( __CLASS__, 'set_recommended_plugins' ) );
		add_filter( 'astoundify_content_importer_screen', array( __CLASS__, 'screen_id' ) );

		self::plugin_installer();
		self::theme_updater();
		self::setup_guide();
		self::content_importer();
	}

	public static function includes() {
		// load external libs
		include_once( get_template_directory() . '/vendor/astoundify/content-importer/astoundify-contentimporter.php' );
		include_once( get_template_directory() . '/vendor/astoundify/plugin-installer/astoundify-plugininstaller.php' );
		include_once( get_template_directory() . '/vendor/astoundify/setup-guide/astoundify-setupguide.php' );
		include_once( get_template_directory() . '/vendor/astoundify/themeforest-updater/astoundify-themeforestupdater.php' );
	}

	/**
	 * Filter the child theme's notice output
	 *
	 * @since 3.3.0
	 * @param array $screen_ids
	 * @return array $screen_ids
	 */
	public static function screen_id( $screen_ids ) {
		return array( Astoundify_Setup_Guide::get_screen_id() );
	}

	/**
	 * Create the setup guide.
	 *
	 * @since 3.3.0
	 * @return void
	 */
	public static function setup_guide() {
		add_action( 'astoundify_setup_guide_intro', array( __CLASS__, '_setup_guide_intro' ) );

		astoundify_setupguide( array(
			'steps' => include_once( dirname( __FILE__ ) . '/steps.php' ),
			'steps_dir' => get_template_directory() . '/inc/setup/steps',
			'strings' => array(
				'page-title' => __( 'Setup %s', 'jobify' ),
				'menu-title' => __( 'Getting Started', 'jobify' ),
				'sub-menu-title' => __( 'Setup Guide', 'jobify' ),
				'intro-title' => __( 'Welcome to %s', 'jobify' ),
				'step-complete' => __( 'Completed', 'jobify' ),
				'step-incomplete' => __( 'Not Complete', 'jobify' ),
			),
			'stylesheet_uri' => get_template_directory_uri() . '/vendor/astoundify/setup-guide/app/assets/css/style.css',
		) );
	}

	/**
	 * The introduction text for the setup guide page.
	 *
	 * @since 3.3.0
	 * @return void
	 */
	public static function _setup_guide_intro() {
?>
<p class="about-text"><?php printf( __( 'Creating a job listing website has never been easier with Jobify — the easiest to use job board theme available. Use the steps below to finish setting up your new website. If you have more questions please <a href="%s">review the documentation</a>.', 'jobify' ), 'http://jobify.astoundify.com' ); ?></p>

<div class="setup-guide-theme-badge"><img src="<?php echo get_template_directory_uri(); ?>/inc/setup/assets/images/badge.png" width="140" alt="" /></div>

<p class="helpful-links">
	<a href="http://jobify.astoundify.com" class="button button-primary js-trigger-documentation"><?php _e( 'Search Documentation', 'jobify' ); ?></a>&nbsp;
	<a href="https://astoundify.com/go/astoundify-support/" class="button button-secondary"><?php _e( 'Submit a Support Ticket', 'jobify' ); ?></a>&nbsp;
</p>

<script>
	jQuery(document).ready(function($) {
		$('.js-trigger-documentation').click(function(e) {
			e.preventDefault();
			HS.beacon.open();
		});
	});
</script>
<script>!function(e,o,n){window.HSCW=o,window.HS=n,n.beacon=n.beacon||{};var t=n.beacon;t.userConfig={},t.readyQueue=[],t.config=function(e){this.userConfig=e},t.ready=function(e){this.readyQueue.push(e)},o.config={modal: true, docs:{enabled:!0,baseUrl:"//astoundify-jobify.helpscoutdocs.com/"},contact:{enabled:!1,formId:"7f6e93b6-cb77-11e5-9e75-0a7d6919297d"}};var r=e.getElementsByTagName("script")[0],c=e.createElement("script");c.type="text/javascript",c.async=!0,c.src="https://djtflbt20bdde.cloudfront.net/",r.parentNode.insertBefore(c,r)}(document,window.HSCW||{},window.HS||{});</script>
<?php
	}

	/**
	 * Create the plugin installer.
	 *
	 * @since 3.5.0
	 */
	public static function plugin_installer() {
		astoundify_plugininstaller( array(
			'plugins' => array( 'wp-job-manager', 'woocommerce', 'wp-job-manager-companies', 'wp-job-manager-colors', 'wp-job-manager-locations', 'wp-job-manager-contact-listing', 'woocommerce-simple-registration', 'ninja-forms', 'testimonials-by-woothemes', 'if-menu' ),
			'forceActivate' => true,
			'l10n' => array(
				'buttonActivePlugin' => __( 'Active', 'jobify' ),
				'buttonErrorActivating' => 'Error',
				'activationFailed' => 'Activation failed: %s',
				'invalidPlugin' => 'Invalid plugin supplied.',
				'invalidNonce' => 'Invalid nonce supplied.',
				'invalidCap' => 'You do not have permission to install plugins on this site.',
				'activateAll' => 'Install and Activate All',
				'activateAllComplete' => 'Complete',
			),
			'install_url' => get_template_directory_uri() . '/vendor/astoundify/plugin-installer',
		) );
	}

	/**
	 * Create the theme updater.
	 *
	 * @since 3.3.0
	 * @return void
	 */
	public static function theme_updater() {
		// start the updater
		$updater = astoundify_themeforestupdater();

		call_user_func( array( $updater, 'set_strings' ), array(
			'cheating' => __( 'Cheating?', 'jobify' ),
			'no-token' => __( 'An API token is required.', 'jobify' ),
			'api-error' => __( 'API error.', 'jobify' ),
			'api-connected' => __( 'Connected', 'jobify' ),
			'api-disconnected' => __( 'Disconnected', 'jobify' ),
		) );

		// set a filter for the token
		add_filter( 'astoundify_themeforest_updater', array( __CLASS__, '_theme_updater_get_token' ) );

		// init the api so it has a token value
		Astoundify_Envato_Market_API::instance();

		// ajax callback
		add_action( 'wp_ajax_astoundify_updater_set_token', array( __CLASS__, '_theme_updater_set_token' ) );
	}

	/**
	 * Filter the Theme Updater token.
	 *
	 * @since 3.3.0
	 * @return string
	 */
	public static function _theme_updater_get_token() {
		return get_option( self::get_template_name() . '_themeforest_updater_token', null );
	}

	/**
	 * AJAX response when a token is set in the Setup Guide.
	 *
	 * @since 3.3.0
	 * @return void
	 */
	public static function _theme_updater_set_token() {
		check_ajax_referer( 'astoundify-add-token', 'security' );

		$token = isset( $_POST['token'] ) ? esc_attr( $_POST['token'] ) : false;
		$api = Astoundify_Envato_Market_API::instance();

		update_option( self::get_template_name() . '_themeforest_updater_token', $token );
		delete_transient( 'atu_can_make_request' );

		// hotswap the token
		$api->token = $token;

		wp_send_json_success( array(
			'token' => $token,
			'can_request' => $api->can_make_request_with_token(),
			'request_label' => $api->connection_status_label(),
		) );

		exit();
	}

	/**
	 * Create the content importer.
	 *
	 * @since 3.3.0
	 * @return void
	 */
	public static function content_importer() {
		// init importer
		astoundify_contentimporter();

		astoundify_contentimporter_add_config( 'strings', array(
			'type_labels' => array(
				'childtheme' => array( __( 'Child Theme', 'jobify' ), __( 'Child Theme', 'jobify' ) ),
				'setting' => array( __( 'Setting', 'jobify' ), __( 'Settings', 'jobify' ) ),
				'thememod' => array( __( 'Theme Customization', 'jobify' ), __( 'Theme Customizations', 'jobify' ) ),
				'term' => array( __( 'Term', 'jobify' ), __( 'Terms', 'jobify' ) ),
				'nav-menu' => array( __( 'Navigation Menu', 'jobify' ), __( 'Navigation Menus', 'jobify' ) ),
				'nav-menu-item' => array( __( 'Navigation Menu Item', 'jobify' ), __( 'Navigation Menu Items', 'jobify' ) ),
				'object' => array( __( 'Content', 'jobify' ), __( 'Content', 'jobify' ) ),
				'widget' => array( __( 'Widget', 'jobify' ), __( 'Widgets', 'jobify' ) ),
			),
			'import' => array(
				'complete' => __( 'Import Complete!', 'jobify' ),
			),
			'reset' => array(
				'complete' => __( 'Reset Complete!', 'jobify' ),
			),
			'errors' => array(
				'process_action' => __( 'Invalid process action.', 'jobify' ),
				'process_type' => __( 'Invalid process type.', 'jobify' ),
				'iterate' => __( 'Iteration process failed.', 'jobify' ),
				'cap_check_fail' => __( 'You do not have permission to manage content.', 'jobify' ),
			),
		) );

		astoundify_contentimporter_add_config( 'url', get_template_directory_uri() . '/vendor/astoundify/content-importer/app' );
		astoundify_contentimporter_add_config( 'definitions', get_template_directory() . '/inc/setup/import-content' );
	}

	/**
	 * List plugins all content requires.
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	public static function set_required_plugins() {
		return array(
			'woocommerce' => array(
				'label' => '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=woocommerce&TB_iframe=true&width=772&height=642' ) . '" class="thickbox">WooCommerce</a>',
				'condition' => class_exists( 'WooCommerce' ),
			),
			'wp-job-manager-base' => array(
				'label' => '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=wp-job-manager&TB_iframe=true&width=772&height=642' ) . '" class="thickbox">WP Job Manager</a>',
				'condition' => class_exists( 'WP_Job_Manager' ),
			),
		);
	}

	/**
	 * List plugins other content depends on.
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	public static function set_recommended_plugins() {
		return array(
			'wp-job-manager-company-profiles' => array(
				'label' => '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=wp-job-manager-companies&TB_iframe=true&width=772&height=642' ) . '" class="thickbox">Company Profiles</a>',
				'condition' => class_exists( 'Astoundify_Job_Manager_Companies' ),
				'pack' => array( 'classic', 'extended' ),
			),
			'wp-job-manager-contact-listing' => array(
				'label' => '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=wp-job-manager-contact-listing&TB_iframe=true&width=772&height=642' ) . '" class="thickbox">Contact Listing</a>',
				'condition' => class_exists( 'Astoundify_Job_Manager_Contact_Listing' ),
				'pack' => array( 'classic', 'extended' ),
			),
			'wp-job-manager-favorites' => array(
				'label' => '<a href="https://wpjobmanager.com/add-ons/job-tags/" target="_blank">Bookmarks</a>',
				'condition' => defined( 'JOB_MANAGER_BOOKMARKS_VERSION' ),
				'pack' => array( 'extended' ),
			),
			'wp-job-manager-regions' => array(
				'label' => '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=wp-job-manager-locations&TB_iframe=true&width=772&height=642' ) . '" class="thickbox">Regions</a>',
				'condition' => class_exists( 'Astoundify_Job_Manager_Regions' ),
				'pack' => array( 'classic', 'extended' ),
			),
			'wp-job-manager-resumes' => array(
				'label' => '<a href="https://wpjobmanager.com/add-ons/resume-manager">Resume Manager</a>',
				'condition' => class_exists( 'WP_Resume_Manager' ),
				'pack' => array( 'extended' ),
			),
			'wp-job-manager-tags' => array(
				'label' => '<a href="https://wpjobmanager.com/add-ons/job-tags/" target="_blank">Tags</a>',
				'condition' => defined( 'JOB_MANAGER_TAGS_VERSION' ),
				'pack' => array( 'default', 'extended' ),
			),
			'wp-job-manager-wc-paid-listings' => array(
				'label' => '<a href="https://wpjobmanager.com/add-ons/wc-paid-listings/" target="_blank">Paid Listings</a>',
				'condition' => defined( 'JOB_MANAGER_WCPL_VERSION' ),
				'pack' => array( 'default', 'extended' ),
			),
			'woothemes-simple-registration' => array(
				'label' => '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=woocommerce-simple-registration&TB_iframe=true&width=772&height=642' ) . '" class="thickbox">Simple Registration</a>',
				'condition' => class_exists( 'WooCommerce_Simple_Registration' ),
				'pack' => array( 'classic', 'extended' ),
			),
			'woothemes-testimonials' => array(
				'label' => '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=testimonials-by-woothemes&TB_iframe=true&width=772&height=642' ) . '" class="thickbox">Testimonials</a>',
				'condition' => class_exists( 'Woothemes_Testimonials' ),
				'pack' => array( 'classic', 'extended' ),
			),
		);
	}

	/**
	 * Get the name of the current template (not child theme)
	 *
	 * @since 1.5.0
	 * @return string $template
	 */
	public static function get_template_name() {
		// if the current theme is a child theme find the parent
		$current_child_theme = wp_get_theme();

		if ( false !== $current_child_theme->parent() ) {
			$current_theme = wp_get_theme( $current_child_theme->get_template() );
		} else {
			$current_theme = wp_get_theme();
		}

		$template = $current_theme->get_template();

		return $template;
	}

}

Jobify_Setup::init();
