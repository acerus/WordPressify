<?php
/**
 * Import content via JSON files for easier immediate reference and manipulation.
 *
 * @since 1.0.0
 * @package Astoundify_ContentImporter
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astoundify_ContentImporter' ) ) :
	/**
	 * Main ContentImporter Class.
	 *
	 * @class Astoundify_ContentImporter
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class Astoundify_ContentImporter {

		/**
		 * The single class instance.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var object
		 */
		private static $_instance = null;

		/**
		 * Static instance of Astoundify_ContentImporter
		 *
		 * Ensures only one instance of this class exists in memory at any one time.
		 *
		 * @see Astoundify_ContentImporter
		 *
		 * @since 1.0.0
		 * @static
		 * @return object The one true Astoundify_Content_Importer
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
				self::init();
			}

			return self::$_instance;
		}

		/**
		 * Include necessary files and hook in to WordPres
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public static function init() {
			self::includes();
			self::setup_actions();
		}

		/**
		 * Include necessary files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function includes() {
			include_once( dirname( __FILE__ ) . '/Config.php' );
			include_once( dirname( __FILE__ ) . '/SortableInterface.php' );
			include_once( dirname( __FILE__ ) . '/Utils.php' );

			include_once( dirname( __FILE__ ) . '/contentimporter-functions.php' );

			include_once( dirname( __FILE__ ) . '/ImportManager.php' );

			include_once( dirname( __FILE__ ) . '/ImporterInterface.php' );
			include_once( dirname( __FILE__ ) . '/ImporterFactory.php' );
			include_once( dirname( __FILE__ ) . '/Importer.php' );
			include_once( dirname( __FILE__ ) . '/JSONImporter.php' );

			include_once( dirname( __FILE__ ) . '/ItemImportInterface.php' );
			include_once( dirname( __FILE__ ) . '/ItemImportFactory.php' );
			include_once( dirname( __FILE__ ) . '/ItemImport.php' );
			include_once( dirname( __FILE__ ) . '/ItemImport_Object.php' );
			include_once( dirname( __FILE__ ) . '/ItemImport_NavMenu.php' );
			include_once( dirname( __FILE__ ) . '/ItemImport_NavMenuItem.php' );
			include_once( dirname( __FILE__ ) . '/ItemImport_Term.php' );
			include_once( dirname( __FILE__ ) . '/ItemImport_Setting.php' );
			include_once( dirname( __FILE__ ) . '/ItemImport_ThemeMod.php' );
			include_once( dirname( __FILE__ ) . '/ItemImport_Widget.php' );
			include_once( dirname( __FILE__ ) . '/ItemImport_Comment.php' );
			include_once( dirname( __FILE__ ) . '/ItemImport_ChildTheme.php' );

			include_once( dirname( __FILE__ ) . '/PluginInterface.php' );
			include_once( dirname( __FILE__ ) . '/Plugin_WooThemesTestimonials.php' );
			include_once( dirname( __FILE__ ) . '/Plugin_EasyDigitalDownloads.php' );
			include_once( dirname( __FILE__ ) . '/Plugin_FrontendSubmissions.php' );
			include_once( dirname( __FILE__ ) . '/Plugin_WooCommerce.php' );
			include_once( dirname( __FILE__ ) . '/Plugin_WPJobManager.php' );
			include_once( dirname( __FILE__ ) . '/Plugin_WPJobManagerProducts.php' );
			include_once( dirname( __FILE__ ) . '/Plugin_WPJobManagerResumes.php' );
			include_once( dirname( __FILE__ ) . '/Plugin_MultiplePostThumbnails.php' );

			include_once( dirname( __FILE__ ) . '/Theme_Listify.php' );
		}

		/**
		 * Hooks/filters
		 *
		 * @since 1.1.0
		 * @return void
		 */
		public static function setup_actions() {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Enqueue import scripts
		 *
		 * @since 1.1.0
		 * @return void
		 */
		public static function admin_enqueue_scripts() {
			$admin_screen_id = apply_filters( 'astoundify_content_importer_screen', array() );
			$screen = get_current_screen();

			if ( ! isset( $screen->id ) || ! in_array( $screen->id, (array) $admin_screen_id ) ) {
				return;
			}

			$url = astoundify_contentimporter_get_config( 'url' );

			wp_enqueue_style( 'astoundify-content-importer', esc_url_raw( $url . '/assets/css/content-importer.css' ) );
			wp_enqueue_script( 'astoundify-content-importer', esc_url_raw( $url . '/assets/js/content-importer.js' ), array( 'jquery', 'underscore' ), '', true );

			wp_localize_script( 'astoundify-content-importer', 'astoundifyContentImporter', array(
				'nonces' => array(
				'stage' => wp_create_nonce( 'setup-guide-stage-import' ),
				),
				'i18n' => astoundify_contentimporter_get_config( 'strings' ),
			) );
		}

	}
endif;
