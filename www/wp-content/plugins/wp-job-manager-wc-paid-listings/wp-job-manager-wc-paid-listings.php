<?php
/**
 * Plugin Name: WP Job Manager - WooCommerce Paid Listings
 * Plugin URI: https://wpjobmanager.com/add-ons/wc-paid-listings/
 * Description: Add paid listing functionality via WooCommerce. Create 'job packages' as products with their own price, listing duration, listing limit, and job featured status and either sell them via your store or during the job submission process. A user's packages are shown on their account page and can be used to post future jobs if they allow more than 1 job listing. Also allows 'resume packages' if using the resumes add-on.
 * Version: 2.8.0
 * Author: Automattic
 * Author URI: https://wpjobmanager.com
 * Requires at least: 4.1
 * Tested up to: 4.8
 *
 * WPJM-Product: wp-job-manager-wc-paid-listings
 *
 * Copyright: 2017 Automattic
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
define( 'JOB_MANAGER_WCPL_VERSION', '2.8.0' );
define( 'JOB_MANAGER_WCPL_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'JOB_MANAGER_WCPL_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'JOB_MANAGER_WCPL_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );

/**
 * WC_Paid_Listings class.
 */
class WC_Paid_Listings {
	const JOB_MANAGER_CORE_MIN_VERSION = '1.29.0';
	const WOOCOMMERCE_MIN_VERSION = '2.5.0';

	/** @var object Class Instance */
	private static $instance;

	/**
	 * Get the class instance
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		// Set up startup actions
		add_action( 'plugins_loaded', array( $this, 'load_text_domain' ), 12 );
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ), 13 );
		add_action( 'admin_notices', array( $this, 'version_check' ) );
	}

	/**
	 * Initializes plugin.
	 */
	public function init_plugin() {
		if ( ! class_exists( 'WP_Job_Manager' ) || ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Hooks
		add_action( 'init', array( $this, 'register_post_status' ), 12 );
		add_filter( 'the_job_status', array( $this, 'the_job_status' ), 10, 2 );
		add_filter( 'job_manager_valid_submit_job_statuses', array( $this, 'valid_submit_statuses' ) );
		add_filter( 'resume_manager_valid_submit_resume_statuses', array( $this, 'valid_submit_statuses' ) );
		add_filter( 'job_manager_settings', array( $this, 'job_manager_settings' ) );
		add_filter( 'resume_manager_settings', array( $this, 'resume_manager_settings' ) );

		// Includes
		include_once( dirname( __FILE__ ) . '/includes/class-wc-paid-listings-package-product.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-wc-product-job-package.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-wc-paid-listings-admin.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-wc-paid-listings-cart.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-wc-paid-listings-orders.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-wc-paid-listings-subscriptions.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-wc-paid-listings-package.php' );
		include_once( dirname( __FILE__ ) . '/includes/class-wc-paid-listings-submit-job-form.php' );
		include_once( dirname( __FILE__ ) . '/includes/user-functions.php' );
		include_once( dirname( __FILE__ ) . '/includes/package-functions.php' );

		// Load 3rd party customizations
		require_once( dirname( __FILE__ ) . '/includes/3rd-party/3rd-party.php' );

		if ( class_exists( 'WP_Resume_Manager' ) ) {
			if ( version_compare( RESUME_MANAGER_VERSION, '1.11.0', '<' ) ) {
				add_filter( 'admin_notices', array( $this, 'resume_update_required' ) );
			} else {
				include_once( dirname( __FILE__ ) . '/includes/class-wc-product-resume-package.php' );
				include_once( dirname( __FILE__ ) . '/includes/class-wc-paid-listings-submit-resume-form.php' );
			}
		}

		if ( class_exists( 'WC_Product_Subscription' ) ) {
			include_once( dirname( __FILE__ ) . '/includes/class-wc-paid-listings-subscription-product.php' );
			include_once( dirname( __FILE__ ) . '/includes/class-wc-product-job-package-subscription.php' );

			if ( class_exists( 'WP_Resume_Manager' ) ) {
				include_once( dirname( __FILE__ ) . '/includes/class-wc-product-resume-package-subscription.php' );
			}
		}

		// Updates
		if ( version_compare( get_option( 'wcpl_db_version', 0 ), JOB_MANAGER_WCPL_VERSION, '<' ) ) {
			wp_job_manager_wcpl_install();
		}

		if ( class_exists( 'WC_Subscriptions' ) && version_compare( WC_Subscriptions::$version, '2.0', '<' ) ) {
			add_filter( 'admin_notices', array( $this, 'subscriptions_update_required' ) );
		}

	}

	/**
	 * Checks WPJM core version.
	 */
	public function version_check() {
		if ( ! class_exists( 'WP_Job_Manager' ) || ! defined( 'JOB_MANAGER_VERSION' ) ) {
			$screen = get_current_screen();
			if ( null !== $screen && 'plugins' === $screen->id ) {
				$this->display_error( __( '<em>WP Job Manager - WC Paid Listings</em> requires WP Job Manager to be installed and activated.', 'wp-job-manager-wc-paid-listings' ) );
			}
		} elseif (
			/**
			 * Filters if WPJM core's version should be checked.
			 *
			 * @since 2.8.0
			 *
			 * @param bool   $do_check                       True if the add-on should do a core version check.
			 * @param string $minimum_required_core_version  Minimum version the plugin is reporting it requires.
			 */
			apply_filters( 'job_manager_addon_core_version_check', true, self::JOB_MANAGER_CORE_MIN_VERSION )
			&& version_compare( JOB_MANAGER_VERSION, self::JOB_MANAGER_CORE_MIN_VERSION, '<' )
		) {
			$this->display_error( sprintf( __( '<em>WP Job Manager - WC Paid Listings</em> requires WP Job Manager %s (you are using %s).', 'wp-job-manager-wc-paid-listings' ), self::JOB_MANAGER_CORE_MIN_VERSION, JOB_MANAGER_VERSION ) );
		}

		$screen = get_current_screen();
		if ( null === $screen || ! in_array( $screen->id, array( 'plugins', 'edit-job_listing' ) ) ) {
			return;
		}

		if ( ! defined( 'WC_VERSION' ) ) {
			$this->display_error( __( '<em>WP Job Manager - WC Paid Listings</em> requires WooCommerce to be installed and activated.', 'wp-job-manager-wc-paid-listings' ) );
		} elseif ( version_compare( WC_VERSION, self::WOOCOMMERCE_MIN_VERSION, '<' ) ) {
			$this->display_error( sprintf( __( '<em>WP Job Manager - WC Paid Listings</em> requires WooCommerce %s (you are using %s).', 'wp-job-manager-wc-paid-listings' ), self::WOOCOMMERCE_MIN_VERSION, WC_VERSION ) );
		}
	}

	/**
	 * Display error message notice in the admin.
	 *
	 * @param string $message
	 */
	private function display_error( $message ) {
		echo '<div class="error">';
		echo '<p>' . $message . '</p>';
		echo '</div>';
	}

	/**
	 * Update nags
	 */
	public function resume_update_required() {
		?><div class="update-nag">
			<?php _e( 'WC Paid Listings requires Resume Manager 1.11.0 and above. Please upgrade to continue using paid listings functionality for resumes.', 'wp-job-manager-wc-paid-listings' ); ?>
		</div><?php
	}

	/**
	 * Update nags
	 */
	public function subscriptions_update_required() {
		?><div class="update-nag">
			<?php _e( 'WC Paid Listings requires WooCommerce Subscriptions 2.0 and above. Please upgrade as soon as possible!', 'wp-job-manager-wc-paid-listings' ); ?>
		</div><?php
	}

	/**
	 * Localisation
	 */
	public function load_text_domain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-wc-paid-listings' );
		load_textdomain( 'wp-job-manager-wc-paid-listings', WP_LANG_DIR . "/wp-job-manager-wc-paid-listings/wp-job-manager-wc-paid-listings-$locale.mo" );

		load_plugin_textdomain( 'wp-job-manager-wc-paid-listings', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Registers post status.
	 */
	public function register_post_status() {
		global $job_manager;

		register_post_status( 'pending_payment', array(
			'label'                     => _x( 'Pending Payment', 'job_listing', 'wp-job-manager-wc-paid-listings' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'wp-job-manager-wc-paid-listings' ),
		) );

		add_action( 'pending_payment_to_publish', array( $job_manager->post_types, 'set_expiry' ) );
	}

	/**
	 * Filter job status name
	 *
	 * @param  string $nice_status
	 * @param  string $status
	 * @return string
	 */
	public function the_job_status( $status, $job ) {
		if ( $job->post_status == 'pending_payment' ) {
			$status = __( 'Pending Payment', 'wp-job-manager-wc-paid-listings' );
		}
		return $status;
	}

	/**
	 * Ensure the submit form lets us continue to edit/process a job with the pending_payment status
	 *
	 * @return array
	 */
	public function valid_submit_statuses( $status ) {
		$status[] = 'pending_payment';
		return $status;
	}

	/**
	 * Add Settings
	 *
	 * @param  array $settings
	 * @return array
	 */
	public function job_manager_settings( $settings = array() ) {
		$settings['job_submission'][1][] = array(
			'name' 		=> 'job_manager_paid_listings_flow',
			'std' 		=> '',
			'label' 	=> __( 'Paid Listings Flow', 'wp-job-manager-wc-paid-listings' ),
			'desc'		=> __( 'Define when the user should choose a package for submission.', 'wp-job-manager-wc-paid-listings' ),
			'type'      => 'select',
			'options'   => array(
				'' => __( 'Choose a package after entering job details', 'wp-job-manager-wc-paid-listings' ),
				'before' => __( 'Choose a package before entering job details', 'wp-job-manager-wc-paid-listings' ),
			),
		);
		return $settings;
	}

	/**
	 * Add Settings
	 *
	 * @param  array $settings
	 * @return array
	 */
	public function resume_manager_settings( $settings = array() ) {
		$settings['resume_submission'][1][] = array(
			'name' 		=> 'resume_manager_paid_listings_flow',
			'std' 		=> '',
			'label' 	=> __( 'Paid Listings Flow', 'wp-job-manager-wc-paid-listings' ),
			'desc'		=> __( 'Define when the user should choose a package for submission.', 'wp-job-manager-wc-paid-listings' ),
			'type'      => 'select',
			'options'   => array(
				'' => __( 'Choose a package after entering resume details', 'wp-job-manager-wc-paid-listings' ),
				'before' => __( 'Choose a package before entering resume details', 'wp-job-manager-wc-paid-listings' ),
			),
		);
		return $settings;
	}

	/**
	 * Check if the installed version of WooCommerce is older than a specified version.
	 *
	 * @since 2.7.2
	 *
	 * @param string $version
	 * @return bool
	 */
	public static function is_woocommerce_pre( $version ) {
		if ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, $version, '<' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check if the installed version of WPJM is older than a specified version.
	 *
	 * @since 2.7.3
	 *
	 * @param string $version
	 * @return bool
	 */
	public static function is_wpjm_pre( $version ) {
		if ( ! defined( 'JOB_MANAGER_VERSION' ) || version_compare( JOB_MANAGER_VERSION, $version, '<' ) ) {
			return true;
		}
		return false;
	}
}

$GLOBALS['job_manager_wc_paid_listings'] = WC_Paid_Listings::get_instance();

/**
 * Install the plugin
 */
function wp_job_manager_wcpl_install() {
	global $wpdb;

	$wpdb->hide_errors();

	$collate = '';
	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) ) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	/**
	 * Table for user packages
	 */
	$sql = "
CREATE TABLE {$wpdb->prefix}wcpl_user_packages (
  id bigint(20) NOT NULL auto_increment,
  user_id bigint(20) NOT NULL,
  product_id bigint(20) NOT NULL,
  order_id bigint(20) NOT NULL default 0,
  package_featured int(1) NULL,
  package_duration bigint(20) NULL,
  package_limit bigint(20) NOT NULL,
  package_count bigint(20) NOT NULL,
  package_type varchar(100) NOT NULL,
  PRIMARY KEY  (id)
) $collate;
";
	dbDelta( $sql );

	// Upgrades
	if ( get_option( 'wcpl_db_version', 0 ) && version_compare( get_option( 'wcpl_db_version', 0 ), '2.1.2', '<' ) ) {
		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_job_packages WHERE job_count < job_limit OR job_limit = 0;" );
		if ( $results ) {
			foreach ( $results as $result ) {
				$wpdb->insert(
					"{$wpdb->prefix}wcpl_user_packages",
					array(
						'user_id'          => $result->user_id,
						'product_id'       => $result->product_id,
						'package_count'    => $result->job_count,
						'package_limit'    => $result->job_limit,
						'package_featured' => $result->job_featured,
						'package_duration' => $result->job_duration,
						'package_type'     => 'job_listing',
					)
				);
			}
		}
	}

	// Update version
	update_option( 'wcpl_db_version', JOB_MANAGER_WCPL_VERSION );

	add_action( 'shutdown', 'wp_job_manager_wcpl_delayed_install' );
}

/**
 * Installer (delayed)
 */
function wp_job_manager_wcpl_delayed_install() {
	if ( ! get_term_by( 'slug', sanitize_title( 'job_package' ), 'product_type' ) ) {
		wp_insert_term( 'job_package', 'product_type' );
	}
	if ( ! get_term_by( 'slug', sanitize_title( 'resume_package' ), 'product_type' ) ) {
		wp_insert_term( 'resume_package', 'product_type' );
	}
	if ( ! get_term_by( 'slug', sanitize_title( 'job_package_subscription' ), 'product_type' ) ) {
		wp_insert_term( 'job_package_subscription', 'product_type' );
	}
	if ( ! get_term_by( 'slug', sanitize_title( 'resume_package_subscription' ), 'product_type' ) ) {
		wp_insert_term( 'resume_package_subscription', 'product_type' );
	}
}

register_activation_hook( __FILE__, 'wp_job_manager_wcpl_install' );
