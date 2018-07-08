<?php
/**
 * Jobify
 *
 * Do not modify this file. Place all modifications in a child theme.
 *
 * @package Jobify
 * @category Theme
 * @since 1.0.0
 */
class Jobify {

	/**
	 * The single instance of the Jobify object.
	 *
	 * @var object $instance
	 */
	private static $instance;

	/**
	 * @var object $activation
	 */
	public $activation;

	/**
	 * @var object $setup
	 */
	public $setup;

	/**
	 * @var object $integrations
	 */
	public $integrations;

	/**
	 * @var object $template
	 */
	public $template;

	/**
	 * @var object $widgets
	 */
	public $widgets;

	/**
	 * Find the single instance of the class.
	 *
	 * @since 3.0.0
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Jobify ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Start things up.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->includes();
		$this->setup();
	}

	/**
	 * Integration getter helper.
	 *
	 * @since 3.0.0
	 *
	 * @param string $integration The name of the integration to load.
	 * @return object $integration
	 */
	public function get( $integration ) {
		return $this->integrations->get( $integration );
	}

	/**
	 * Load the necessary files.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function includes() {
		$this->files = array(
			'customizer/class-customizer.php',

			'class-deprecated.php',
			'class-helpers.php',

			'activation/class-activation.php',

			'setup/class-setup.php',
			'pages/class-page-settings.php',
			'pages/class-page-header.php',

			'listing/class-listing-factory.php',
			'listing/class-listing.php',
			'listing/template-tags.php',

			'integrations/class-integrations.php',
			'integrations/class-integration.php',

			'template/class-template.php',

			'widgets/class-widgetized-pages.php',
			'widgets/class-widgets.php',
			'widgets/class-widget.php',
		);

		foreach ( $this->files as $file ) {
			require_once( get_template_directory() . '/inc/' . $file );
		}
	}

	/**
	 * Instantiate necessary classes and assign them to relevant
	 * class properties.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function setup() {
		$this->activation = Jobify_Activation::init();
		$this->integrations = new Jobify_Integrations();
		$this->template = new Jobify_Template();
		$this->widgets = new Jobify_Widgets();

		add_action( 'after_setup_theme', array( $this, 'setup_theme' ) );
	}

	/**
	 * Standard WordPress theme setup
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function setup_theme() {
		// set the content width
		$GLOBALS['content_width'] = apply_filters( 'jobify_content_width', 680 );

		// load translations
		$locale = apply_filters( 'plugin_locale', get_locale(), 'jobify' );
		load_textdomain( 'jobify', WP_LANG_DIR . "/jobify-$locale.mo" );
		load_theme_textdomain( 'jobify', get_template_directory() . '/languages' );

		// load editor-style.css
		add_editor_style();

		// theme supports
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );

		add_theme_support( 'custom-background', array(
			'default-color'    => '#ffffff',
		) );

		add_theme_support( 'custom-header', array(
			'default-text-color'     => '666666',
			'height'                 => 44,
			'width'                  => 200,
			'flex-width'             => true,
			'flex-height'            => true,
			'wp-head-callback'       => array( jobify()->template->header, 'custom_header_style' ),
		) );

		add_theme_support( 'customize-selective-refresh-widgets' );

		// nav menus
		register_nav_menus( array(
			'primary'       => __( 'Navigation Menu', 'jobify' ),
			'footer-social' => __( 'Footer Social', 'jobify' ),
		) );

		// images
		add_image_size( 'content-grid', 400, 200, true );
		add_image_size( 'content-job-featured', 1350, 525, true );

		// extras
		add_filter( 'excerpt_more', '__return_false' );
		add_filter( 'widget_text', 'do_shortcode' );
	}

}

/**
 * Helper function for accessing the main `Jobify` class.
 *
 * @since 3.0.0
 *
 * @return object Jobify The single instance of the `Jobify` class.
 */
function jobify() {
	return Jobify::instance();
}

// Oh get a job? Just get a job?
jobify();
