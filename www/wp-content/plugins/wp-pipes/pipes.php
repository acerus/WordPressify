<?php
/*
Plugin Name: WP Pipes
Plugin URI: http://thimpress.com/shop/pipes/
Description: WP Pipes plugin works the same way as Yahoo Pipes or Zapier does, give your Pipes input and get output as your needs.
Version: 1.31
Author: ThimPress
Author URI: http://thimpress.com
*/
define( '_JEXEC', 1 );
@session_start();
define( "PIPES_CORE", 1 );
define( "PIPES_PATH", dirname( __FILE__ ) );
define( "PIPES_MAIN_FILE_PATH", __FILE__ );
defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );
require_once 'define.php';
require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'application.php';
require_once dirname( __FILE__ ) . DS . 'helpers' . DS . 'common.php';

/**
 * Class PIPES
 */
class PIPES extends Application {
	public static $__page_prefix = '';
	public static $__prefix = '';
	public static $__dashboard_screen = '';

	public function __construct( $prefix = '', $page_prefix ) {
		self::$__page_prefix = $page_prefix;
		self::$__prefix      = $prefix;
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'pipes_plugin_redirect' ) );
		//add_action( 'upgrader_process_complete', array( $this, 'update_pipe_option' ), 20 );
		parent::__construct( $prefix, $page_prefix );
	}

	public function admin_init() {
		wp_register_style( 'pipes-obstyle', plugin_dir_url( __FILE__ ) . 'assets/css/obstyle.css' );
		wp_register_style( 'pipes-bootstrap-min', plugin_dir_url( __FILE__ ) . 'assets/css/bootstrap.min.css' );
		wp_register_style( 'pipes-font-awesome-css', '//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css' );
		wp_register_style( 'pipes-process-css', plugin_dir_url( __FILE__ ) . 'assets/css/process.css' );
		wp_register_style( 'pipes-chosen-css', plugin_dir_url( __FILE__ ) . 'assets/css/chosen.css' );
		wp_register_style( 'pipes-ads-css', plugin_dir_url( __FILE__ ) . 'assets/css/ad_style.css' );
		//wp_register_style( 'pipes-inputtags-css', plugin_dir_url( __FILE__ ) . '/assets/css/bootstrap-tagsinput.css' );
		wp_register_script( 'pipes-bootstrap-min', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap.min.js' );
		wp_register_script( 'pipes-process', plugin_dir_url( __FILE__ ) . 'assets/js/process.js' );
		wp_register_script( 'pipes-ogb-lib-admin', plugin_dir_url( __FILE__ ) . 'assets/js/ogb-lib-admin.js' );
		wp_register_script( 'pipes-chosen', plugin_dir_url( __FILE__ ) . 'assets/js/chosen.jquery.js' );
		wp_register_script( 'pipes-angular', plugin_dir_url( __FILE__ ) . 'assets/js/angular.js' );
		wp_register_script( 'pipes-ads-js', plugin_dir_url( __FILE__ ) . 'assets/js/ad_script.js' );
		//js for input tags
		//wp_register_script( 'pipes-bootstrap-tagsinput', plugin_dir_url( __FILE__ ) . '/assets/js/bootstrap-tagsinput.js' );

		parent::admin_init();
	}

	public function pipes_plugin_redirect() {
		if ( get_option( 'pipes_plugin_do_activation_redirect', false ) ) {
			delete_option( 'pipes_plugin_do_activation_redirect' );
			wp_redirect( "admin.php?page=pipes.pipes" );
		}
	}

	/*public function update_pipe_option(){
		add_option( 'pipes_not_use_cache', 0 );
	}*/

	public function init() {
		require_once dirname( __FILE__ ) . DS . 'plugin.php';
		pipes_system::cronjob();
	}

	public function pipes_default_options() {
		global $pipes_settings;
		include_once( dirname( __FILE__ ) . DS . 'settings-init.php' );
		foreach ( $pipes_settings as $section ) {
			foreach ( $section as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					add_option( $value['id'], $value['default'] );
				}
			}
		}
	}

	public function admin_menu() {
		# add main menu
		if ( function_exists( "add_menu_page" ) ) {
//			$icon_url  = plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/images/menu_icon_core.png';
			$icon_url = 'dashicons-editor-justify';
			$position = $this->get_free_menu_position( 5 );
			add_menu_page( __( "Pipes", "pipes" ), __( "Pipes", "pipes" ), "manage_options", $this->_page_prefix . ".pipes", array(
				$this,
				'display'
			), $icon_url, $position );
			if ( function_exists( "add_submenu_page" ) ) {
//				add_submenu_page( $this->_page_prefix . '.pipes', __( 'Dashboard', 'cpanel' ), __( 'Dashboard', 'cpanel' ), "manage_options", $this->_page_prefix . ".cpanel", array( $this, 'display' ) );
				$items_page   = add_submenu_page( $this->_page_prefix . '.pipes', __( 'All Pipes', 'pipes' ), __( 'All Pipes', 'pipes' ), "manage_options", $this->_page_prefix . ".pipes", array(
					$this,
					'display'
				) );
				$item_page    = add_submenu_page( $this->_page_prefix . '.pipes', __( 'Add New Pipe', 'add_new' ), __( 'Add New', 'add_new' ), "manage_options", $this->_page_prefix . ".pipe", array(
					$this,
					'display'
				) );
				$addon_page   = add_submenu_page( $this->_page_prefix . '.pipes', __( 'Addons', 'plugins' ), __( 'Addons', 'plugins' ), "manage_options", $this->_page_prefix . ".plugins", array(
					$this,
					'display'
				) );
				$setting_page = add_submenu_page( $this->_page_prefix . '.pipes', __( 'Settings', 'settings' ), __( 'Settings', 'settings' ), "manage_options", $this->_page_prefix . ".settings", array(
					$this,
					'display'
				) );
				add_action( 'admin_print_styles-' . $item_page, array( $this, 'admin_style_item' ) );
				add_action( 'admin_print_styles-' . $items_page, array( $this, 'admin_style_item' ) );
				add_action( 'admin_print_styles-' . $addon_page, array( $this, 'admin_enqueue_addon_page' ) );
				add_action( 'admin_print_styles-' . $addon_page, array( $this, 'admin_style_item' ) );
				add_action( 'admin_print_styles-' . $setting_page, array( $this, 'admin_style_item' ) );
			}
			add_action( 'load-' . $item_page, array( $this, 'on_load_page' ) );
			add_action( 'load-' . $items_page, array( $this, 'on_load_page' ) );
			add_action( 'load-' . $addon_page, array( $this, 'on_load_page' ) );
		}
	}

	public function install() {
		$filename = pathinfo( __FILE__, PATHINFO_BASENAME );
		$filepath = dirname( __FILE__ ) . DS . 'install.' . $filename;
		$this->pipes_default_options();
		require_once $filepath;
	}

	public function uninstall() {
		$filename = pathinfo( __FILE__, PATHINFO_BASENAME );
		$filepath = dirname( __FILE__ ) . DS . 'uninstall.' . $filename;
		require_once $filepath;
	}

	public function admin_style_item() {
		wp_enqueue_style( 'pipes-obstyle' );
		wp_enqueue_style( 'pipes-bootstrap-min' );
		wp_enqueue_style( 'pipes-bootstrap-extended' );
		wp_enqueue_style( 'pipes-font-awesome-css' );
		wp_enqueue_style( 'pipes-process-css' );
		wp_enqueue_style( 'pipes-chosen-css' );
		wp_enqueue_style( 'pipes-ads-css' );
//		wp_enqueue_style( 'pipes-inputtags-css' );//css for input tags
		wp_enqueue_script( 'pipes-bootstrap-min' );
		wp_enqueue_script( 'pipes-process' );
		wp_enqueue_script( 'pipes-ogb-lib-admin' );
		wp_enqueue_script( 'pipes-chosen' );
		wp_enqueue_script( 'pipes-ads-js' );
		//wp_enqueue_script( 'pipes-angular' );
		$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		if ( $page == 'pipes.plugins' ) {
			wp_enqueue_script( 'pipes-angular' );
		}
		//js for input tags
		//wp_enqueue_script( 'pipes-bootstrap-tagsinput' );

	}

	public function admin_enqueue_addon_page() {
		wp_enqueue_style( 'pipes-bootstrap-min' );
		wp_enqueue_script( 'pipes-angular' );
	}

	public static function add_message( $msg, $type = 'message' ) {
		if ( ! isset( $_SESSION['PIPES']['messages'] ) || empty( $_SESSION['PIPES']['messages'] ) ) {
			$_SESSION['PIPES']['messages'][]    = array( 'msg' => $msg, 'type' => $type );
			$_SESSION['PIPES']['messages_show'] = 0;
		}
	}

	public static function show_message( $bootstrap = false ) {
		if ( isset( $_SESSION['PIPES']['messages'] ) && count( $_SESSION['PIPES']['messages'] ) ) {
			$msgs    = $_SESSION['PIPES']['messages'];
			$classes = array(
				'message' => 'info',
				'error'   => 'danger',
				'warning' => 'warning'
			);
			$out     = array();
			foreach ( $msgs as $msg ) {
				$msg_class = isset( $classes[ $msg['type'] ] ) ? $classes[ $msg['type'] ] : $classes['message'];
				if ( $bootstrap ) {
					$out[] = '<div class="alert alert-' . $msg_class . ' fade in">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<div>' . $msg['msg'] . '</div>
							</div>';
				} else {
					$out[] = '<div id="message" class="updated"><p>' . $msg['msg'] . '</p></div>';
				}

			}
			$contens                       = implode( '', $out );
			$_SESSION['PIPES']['messages'] = array();

			return $contens;
		}
	}

	public function get_free_menu_position( $start, $increment = 1 ) {
		foreach ( $GLOBALS['menu'] as $key => $menu ) {
			$menus_positions[] = $key;
		}

		if ( ! in_array( $start, $menus_positions ) ) {
			return $start;
		}

		/* the position is already reserved find the closet one */
		while ( in_array( $start, $menus_positions ) ) {
			$start += $increment;
		}

		return $start;
	}
}


function mywppipes_enqueue( $hook ) {
	//wp_enqueue_script( 'my_custom_script', plugin_dir_url( __FILE__ ) . 'assets/js/ogb.js' );
	wp_register_script( 'my_custom_script', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/call_pipe.js' );
	wp_enqueue_script( 'my_custom_script' );
}

function ts_js() {
	_e( '<script type="text/javascript">
		var obHost ="' . get_site_url() . '/";
		</script>' );
}

if ( ! is_admin() ) {
	error_reporting( E_ERROR );
	if ( get_option( 'pipes_cronjob_active' ) ) {
		add_action( 'wp_enqueue_scripts', 'mywppipes_enqueue' );
		add_action( 'wp_print_scripts', 'ts_js' );
	}
}
//add_action( 'plugins_loaded', 'ob_advertisment' );
add_action( 'wppipes_loaded_ads', 'ob_advertisment' );
function ob_advertisment() {
	new Ob_Advertisment();
}

define( 'OB_ADVERTISMENT_URL', plugin_dir_url( __FILE__ ) );
define( 'OB_ADVERTISMENT_VER', '1.0' );

if ( ! class_exists( 'Ob_Advertisment' ) ) {

	class Ob_Advertisment {

		/**
		 * @var
		 */
		public $list_themes;

		/**
		 * Ob_Advertisment constructor.
		 */
		public function __construct() {

			//add_action('admin_enqueue_scripts', array( $this, 'admin_enqueue_script'));
			add_action( 'admin_footer', array( $this, 'ob_advertisment' ), - 10 );
		}

		/**
		 * ob_advertisment
		 */
		public function ob_advertisment() {

			$list_themes = array(
				array(
					'name'        => 'Education WordPress Theme | Education WP',
					'url'         => 'http://themeforest.net/item/education-wordpress-theme-education-wp/14058034',
					'demo'        => 'http://educationwp.thimpress.com',
					'img'         => 'https://s3.envato.com/files/237805781/eduma_preview.__large_preview-3.__large_preview.__large_preview.jpg',
					'description' => 'Education WordPress Theme – Education WP is made for educational web, LMS, Training Center, Courses Hub, College, Academy, University, School, Kindergarten. Education WP 2.7 newly released: Seamless lesson design, LearnPress 2.0, new Ivy League Demos, Visual Coposer, faster, stable, scalable, more light weight. See changelog. Complete Education WordPress Theme Based on our experience of building LMS with our previous theme eLearning WP - Education WP is the next generation and one of the best education WordPress themes around, containing all the strength of eLearning WP but with a better UI/UX. This WordPress educational theme has been developed based on the #1 LMS plugin on the official WordPress Plugins directory',
				),
				array(
					'name'        => 'Speaker and Life Coach WordPress Theme | Coaching WP',
					'url'         => 'http://themeforest.net/item/speaker-and-life-coach-wordpress-theme-coaching-wp/17097658',
					'demo'        => 'http://live2.thimpress.com/?item=coaching',
					'img'         => 'https://0.s3.envato.com/files/221054929/01_preview.__large_preview.png',
					'description' => 'Speaker & Life Coach WordPress Theme (Coaching WP) is a stunning, flexible and multipurpose WP theme for speakers, mentors, trainers, therapists, and coaches. Its ultimate aim is to help individuals and businesses in the coaching industry promote their speeches, services, and consultancies to the world easier. Coaching WP’s uniqueness is due to its amazingly beautiful designs and easy to use Website template solution that maximizes user satisfaction. The Speaker and Coaching Theme made ONLY FOR YOU. Do you want to help people and inspire others? Are you an Entrepreneur, a Public speaker, a Mentor, a Fitness trainer, a doctor, a health coach, a physical therapist, a nutritionist, a personal trainer, a f',
				)
			);

			shuffle( $list_themes );
			array_unshift( $list_themes, array(
				'name'        => 'MagWP - The Complete Magazine WordPress Theme ',
				'url'         => 'https://themeforest.net/item/magazine-wordpress-theme-mag-wp/19305239?utm_source=wporg&utm_medium=pipes&ref=thimpress&utm_campaign=magwp',
				'demo'        => 'https://magwp.thimpress.com/',
				'img'         => 'https://thimpress.com/wp-content/uploads/2017/06/mag.__large_preview.png',
				'description' => 'MagWP is a WordPress theme that lets you write articles and blog posts with ease. We offer great support, awesome designs (10 website demos in 1 theme), and friendly help!',
			) );

			?>
            <div id="ob-advertisment" class="ob-advertisment">
				<?php
				foreach ( $list_themes as $theme ) {
					$theme['url'] = add_query_arg( array(
						'utm_source' => 'pipes',
						'utm_medium' => 'pipe-back-end'
					), $theme['url'] );
					$url_demo     = add_query_arg( array(
						'utm_source' => 'pipes',
						'utm_medium' => 'pipe-back-end'
					), $theme['demo'] );

					$theme['description'] = preg_replace( '/(?<=\S,)(?=\S)/', ' ', $theme['description'] );
					$theme['description'] = str_replace( "\n", ' ', $theme['description'] );
					$theme['description'] = explode( " ", $theme['description'] );
					$theme['description'] = array_splice( $theme['description'], 0, sizeof( $theme['description'] ) - 0 );
					$theme['description'] = implode( " ", $theme['description'] ) . " ...";
					?>

                    <div class="item">
                        <div class="theme-thumbnail">
                            <a href="<?php _e( esc_url( $theme['url'] ) ); ?>">
                                <img src="<?php _e( esc_url( $theme['img'] ) ) ?>"/>
                            </a>
                        </div>

                        <div class="theme-detail">
                            <h2><a href="<?php _e( esc_url( $theme['url'] ) ); ?>"><?php _e( $theme['name'] ); ?></a>
                            </h2>
                            <p class="ob-description">
								<?php _e( wp_kses_post( $theme['description'] ) ); ?>
                            </p>
                            <p class="theme-controls">
                                <a href="<?php _e( esc_url( $theme['url'] ) ); ?>" class="button button-primary"
                                   target="_blank">Read More</a>
                                <a href="<?php _e( esc_url( $url_demo ) ); ?>" class="button" target="_blank">View
                                    Demo</a>
                            </p>
                        </div>

                    </div>
					<?php
				}
				?>
            </div>
			<?php
		}
	}
}
$wplo_mvc = new PIPES( 'PIPES', 'pipes' );
