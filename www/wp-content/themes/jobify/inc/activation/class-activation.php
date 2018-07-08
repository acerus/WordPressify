<?php
/**
 * Activating Jobify
 *
 * @since 3.0.0
 * @package Jobify
 * @category Admin
 */
class Jobify_Activation {
	
	/**
	 * @var object
	 */
	public static $theme;

	/**
	 * @var string
	 */
	public static $version;

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

		self::$theme = wp_get_theme( 'jobify' );
		self::$version = get_option( 'jobify_version', false );

		if ( self::$theme->exists() && self::$version && version_compare( self::$version, '3.0.0', '<' ) ) {
			self::upgrade( '300' );
		}

		self::setup_actions();
	}

	/**
	 * Run an upgrade
	 *
	 * @since 3.3.0
	 * @param string $run
	 * @return void
	 */
	public static function upgrade( $run ) {
		$upgrade = '_upgrade_' . $run;
	
		if ( method_exists( __CLASS__, $upgrade ) ) {
			self::$upgrade();
		}

		self::set_version();
	}

	/**
	 * Set hooks and callbacks.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function setup_actions() {
		add_action( 'after_switch_theme', array( __CLASS__, 'after_switch_theme' ) );
		add_action( 'add_option_job_manager_installed_terms', array( __CLASS__, 'enable_categories' ) );

		add_action( 'admin_notices', array( __CLASS__, 'google_maps_api_key_notice' ) );
		add_action( 'wp_ajax_jobify_google_maps_api_notice_dismiss', array( __CLASS__, 'jobify_google_maps_api_notice_dismiss' ) );
	}

	/**
	 * When this theme is activated set the current version and redirect if a new
	 * install. This is only called when a theme is actually switched.
	 *
	 * @since 3.0.0
	 *
	 * @param object $theme
	 * @return void
	 */
	public static function after_switch_theme( $theme ) {
		// If it's set just update version can cut out
		if ( get_option( 'jobify_version' ) ) {
			self::set_version();

			return;
		}

		// Don't let WP Job Manager run its setup guide
		update_option( 'wp_job_manager_version', 100 );

		// Don't let WooCommerce run its setup guide (sorry)
		update_option( 'woocommerce_version', 100 );
		update_option( 'woocommerce_cart_page_id', -1 );

		self::set_version();
		self::redirect();
	}

	/**
	 * Save the current theme's version to the database.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function set_version() {
		update_option( 'jobify_version', self::$theme->get( 'Version' ) );
	}

	/**
	 * Redirect to the theme setup admin page.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function redirect() {
		unset( $_GET[ 'action' ] );

		wp_safe_redirect( admin_url( 'themes.php?page=jobify-setup' ) );

		exit();
	}

	/**
	 * Automatically turn on WP Job Manager categories.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function enable_categories() {
		update_option( 'job_manager_enable_categories', 1 );
	}

	/**
	 * Display a notice until a Google Maps API key is entered or this
	 * notice is dismissed.
	 *
	 * @since 3.2.0
	 * @return void
	 */
	public static function google_maps_api_key_notice() {
		if ( '' != get_theme_mod( 'map-behavior-api-key', '' ) || get_option( 'jobify-google-maps-api-notice', false ) ) {
			return;
		}

		wp_enqueue_script( 'wp-util' );
?>

<div class="jobify-google-maps-api-notice notice notice-error is-dismissible">
	<p><?php printf( 
		__( '<strong>You have not entered a Google Maps API key!</strong> You will not have access to certain features of Jobify. %s', 'jobify' ), 
		'<a href="' . esc_url_raw( admin_url( 'customize.php?autofocus[control]=map-behavior-api-key' ) ) . '">' . __( 'Add an API key &rarr;', 'jobify' ) . '</a>' 
	); ?></p>
</div>

<script>
jQuery(function($) {
	$( '.jobify-google-maps-api-notice' ).on( 'click', '.notice-dismiss', function(e) {
		e.preventDefault();

		wp.ajax.send( 'jobify_google_maps_api_notice_dismiss', {
			data: {
				security: '<?php echo wp_create_nonce( 'jobify-google-maps-api-notice' ); ?>'
			}
		} );
	});
});
</script>

<?php
	}

	/**
	 * Persist notice dismiss.
	 *
	 * @since 3.2.0
	 * @return void
	 */
	public static function jobify_google_maps_api_notice_dismiss() {
		check_ajax_referer( 'jobify-google-maps-api-notice', 'security' );

		add_option( 'jobify-google-maps-api-notice', true );

		wp_send_json_success();
	}

	/**
	 * Jobify 3.0.0
	 *
	 * Changes how the theme mods are stored (again).
	 */
	private static function _upgrade_300() {
		$theme_mods = get_theme_mods();

		if ( ! $theme_mods ) {
			return;
		}

		foreach ( $theme_mods as $mod => $value ) {
			switch ($mod) {
				case 'jobify_listing_display_area' :
					set_theme_mod( 'job-display-sidebar', $mod );
					set_theme_mod( 'resume-display-sidebar', $mod );
					break;
				case 'jobify_listing_topbar_columns' :
					set_theme_mod( 'job-display-sidebar-columns', $mod );
					set_theme_mod( 'resume-display-sidebar-columns', $mod );
					remove_theme_mod( 'jobify_listings_topbar_colspan' );
					break;
				case 'header_background' :
					set_theme_mod( 'color-header-background', $value );
					break;
				case 'primary' :
					set_theme_mod( 'color-primary', $value );
					break;
				case 'navigation' :
					set_theme_mod( 'color-navigation-text', $value );
					break;
				case 'jobify_cta_display' :
					set_theme_mod( 'cta-display', $value );
					break;
				case 'jobify_cta_text' :
					set_theme_mod( 'cta-text', $value );
					break;
				case 'jobify_cta_text_color' :
					set_theme_mod( 'color-cta-text', $value );
					break;
				case 'jobify_cta_background_color' :
					set_theme_mod( 'color-cta-background', $value );
					break;
				case 'clusters' :
					set_theme_mod( 'map-behavior-clusters', $value );
					break;
				case 'grid-size' :
					set_theme_mod( 'map-behavior-grid-size', $value );
					break;
				case 'autofit' :
					set_theme_mod( 'map-behavior-autofit', $value );
					break;
				case 'center' :
					set_theme_mod( 'map-behavior-center', $value );
					break;
				case 'zoom' :
					set_theme_mod( 'map-behavior-zoom', $value );
					break;
				case 'max-zoom' :
					set_theme_mod( 'map-behavior-max-zoom', $value );
					break;
				default:
					//
					break;
			}
		}
	}

}
