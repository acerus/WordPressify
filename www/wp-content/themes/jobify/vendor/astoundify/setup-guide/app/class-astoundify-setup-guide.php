<?php
if ( ! class_exists( 'Astoundify_Setup_Guide' ) ) :
	/**
	 * Setup guide.
	 *
	 * @since 1.0.0
	 */
	class Astoundify_Setup_Guide {

		/**
		 * @var array
		 * @since 1.0.0
		 */
		public static $args = array();

		/**
		 * @var array
		 * @since 1.0.0
		 */
		public static $steps = array();

		/**
		 * The strings used for any output in the drop-ins.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @var array
		 */
		public static $strings = array();

		/**
		 * The current theme.
		 *
		 * @var object
		 * @since 1.0.0
		 */
		public static $current_theme;

		/**
		 * The child theme (if available).
		 *
		 * @var object
		 * @since 1.0.0
		 */
		public static $current_child_theme;

		/**
		 * Retrieves the directory name of the current theme, without the trailing slash.
		 *
		 * @var object
		 * @since 1.0.0
		 */
		public static $template;

		/**
		 * Start things up (only in the admin).
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function init( $args = array() ) {
			if ( ! is_admin() ) {
				return;
			}

			$defaults = array(
			'steps' => array(),
			'strings' => array(),
			'steps_dir' => get_template_directory() . '/inc/setup/steps',
			'stylesheet_uri' => get_template_directory_uri() . '/inc/setup/setup-guide/style.css',
			);

			self::$args = wp_parse_args( $args, $defaults );

			// set strings
			self::set_strings( $args['strings'] );

			// if the current theme is a child theme find the parent
			self::$current_child_theme = wp_get_theme();

			if ( false !== self::$current_child_theme->parent() ) {
				self::$current_theme = wp_get_theme( self::$current_child_theme->get_template() );
			} else {
				self::$current_theme = wp_get_theme();
			}

			// for easy access
			self::$template = self::$current_theme->get_template();

			self::setup_actions();
		}

		/**
		 * Actions
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 * @codeCoverageIgnore
		 */
		public static function setup_actions() {
			add_action( 'admin_menu', array( __CLASS__, 'add_admin_page' ) );
			add_action( 'admin_menu', array( __CLASS__, 'add_meta_boxes' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );

			// fill in some page defaults
			add_action( 'astoundify_setup_guide_intro', array( __CLASS__, 'output_page_intro_title' ), 5 );

			// allow the menu item to be hidden
			add_action( 'admin_init', array( __CLASS__, 'maybe_hide_menu_item' ) );
		}

		/**
		 * Set the strings to be used inside the other drop in files.
		 *
		 * @since 1.0.0
		 *
		 * @return self::$strings
		 */
		public static function set_strings( $strings = array() ) {
			$defaults = array(
			'page-title' => 'Setup Guide',
			'menu-title' => 'Getting Started',
			'intro-title' => 'Welcome to %s',
			'step-complete' => 'Completed',
			'step-incomplete' => 'Not Complete',
			);

			$strings = wp_parse_args( $strings, $defaults );

			self::$strings = $strings;
		}

		/**
		 * Get strings.
		 *
		 * Set the defaults if none are available.
		 *
		 * @since 1.0.0
		 * @return self::$strings
		 */
		public static function get_strings() {
			if ( empty( self::$strings ) ) {
				self::set_strings();
			}

			return self::$strings;
		}

		/**
		 * Get a string.
		 *
		 * @since 1.0.0
		 *
		 * @param string $string
		 * @return string
		 */
		public static function get_string( $string ) {
			$strings = self::get_strings();

			if ( isset( $strings[ $string ] ) ) {
				return $strings[ $string ];
			}

			return '';
		}

		public static function get_steps() {
			if ( isset( self::$steps ) && ! empty( self::$steps ) ) {
				return self::$steps;
			}

			self::$steps = self::$args['steps'];

			return apply_filters( self::$template . '_setup_guide_steps', self::$steps );
		}

		/**
		 * Get the Setup Guide screen ID
		 *
		 * @since 1.1.0
		 * @return string $screen_id
		 */
		public static function get_screen_id() {
			$screen_id = false;

			if ( get_option( 'astoundify_setup_guide_hidden', false ) ) {
				$screen_id = 'appearance_page_' . self::$template . '-setup';
			} else {
				$screen_id = 'toplevel_page_' . self::$template . '-setup';
			}

			return $screen_id;
		}

		/**
		 * Get the Setup Guide page ID
		 *
		 * @since 1.1.0
		 * @return string $screen_id
		 */
		public static function get_page_id() {
			return self::$template . '-setup';
		}

		/**
		 * Get the Setup Guide page URL
		 *
		 * @since 1.1.0
		 * @return string $page_url
		 */
		public static function get_page_url() {
			$page_args = array(
			'page' => self::get_page_id(),
			);

			if ( get_option( 'astoundify_setup_guide_hidden', false ) ) {
				$page_base = admin_url( 'themes.php' );
			} else {
				$page_base = admin_url( 'admin.php' );
			}

			return esc_url( add_query_arg( $page_args, $page_base ) );
		}

		/**
		 * Add the theme submenu page.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 * @codeCoverageIgnore
		 */
		public static function add_admin_page() {
			if ( ! get_option( 'astoundify_setup_guide_hidden', false ) ) {
				add_menu_page(
					sprintf( self::get_string( 'page-title' ), self::$current_theme->get( 'Name' ) ),
					self::get_string( 'menu-title' ),
					'edit_theme_options',
					self::get_page_id(),
					array( __CLASS__, 'output_admin_page' ),
					'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjIuMSwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHdpZHRoPSI0MDBweCIgaGVpZ2h0PSI0MDBweCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQwMCA0MDA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4KCS5zdDB7ZmlsbDojRkZGRkZGO30KPC9zdHlsZT4KPGc+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMzk0LjYsMjQ2LjJjLTUuMi00My43LTkuMi04Ny42LTEyLTEzMS41Yy0yLjgtNDQtMjUuOS03MC4yLTY5LjEtNzguOGMtNDMuMy04LjYtODYuMy0xOC4zLTEyOS4xLTI5LjIKCQljLTQyLjgtMTAuOS03NC45LDIuOC05Ni40LDQxLjNjLTIxLjUsMzguNC00NC4xLDc2LjMtNjcuNywxMTMuNWMtMjMuNiwzNy4yLTIwLjQsNzIsOS41LDEwNC4zYzMwLDMyLjMsNTkuMSw2NS40LDg3LjIsOTkuNAoJCWMyOC4yLDMzLjksNjIuMyw0MS43LDEwMi4zLDIzLjJjNDAuMS0xOC41LDgwLjYtMzUuOCwxMjEuNy01Mi4xQzM4MS45LDMxOS45LDM5OS44LDI4OS45LDM5NC42LDI0Ni4yeiBNMTM0LjUsMzc3LjlsLTIuMS0wLjMKCQlMMTcxLjYsMjI3bC03My43LTQuOUwyNjcuNiwyOC4ybDIuMSwwLjNsLTM5LjIsMTUwLjZsNzMuNyw0LjlMMTM0LjUsMzc3Ljl6Ii8+CjwvZz4KPC9zdmc+Cg=='
				);

				add_submenu_page(
					self::get_page_id(),
					sprintf( self::get_string( 'page-title' ), self::$current_theme->get( 'Name' ) ),
					self::get_string( 'sub-menu-title' ),
					'edit_theme_options',
					self::$template . '-setup',
					array( __CLASS__, 'output_admin_page' )
				);
			} else {
				add_theme_page(
					sprintf( self::get_string( 'page-title' ), self::$current_theme->get( 'Name' ) ),
					self::get_string( 'sub-menu-title' ),
					'edit_theme_options',
					self::$template . '-setup',
					array( __CLASS__, 'output_admin_page' )
				);
			}
		}

		/**
		 * Enqueue setup guide CSS.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function admin_scripts() {
			$screen = get_current_screen();

			if ( $screen->id != self::get_screen_id() ) {
				return;
			}

			add_thickbox();
			wp_enqueue_style( self::$template . '-setup', self::$args['stylesheet_uri'] );

			wp_enqueue_script( 'underscore' );

			do_action( 'astoundify_setup_guide_scripts' );
		}

		/**
		 * Add a metabox to the page for each step.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function add_meta_boxes() {
			foreach ( self::get_steps() as $step => $info ) {
				$info = array_merge( array(
					'step' => $step,
				), $info );

				add_meta_box(
					$step,
					$info['title'],
					array( __CLASS__, 'step_box' ),
					self::$template . '_setup_steps',
					'normal',
					'high',
					$info
				);
			}
		}

		/**
		 * Output the step content.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function step_box( $object, $metabox ) {
			$args = $metabox['args'];

			if ( 'n/a' !== $args['completed'] ) {
				printf(
					'<div id="%s" class="%s" data-string-complete="%s" data-string-incomplete="%s">%s</div>',
					'step-status-' . esc_attr( $args['step'] ),
					'step-status step-' . ( $args['completed'] ? 'complete' : 'incomplete' ),
					self::get_string( 'step-complete' ),
					self::get_string( 'step-incomplete' ),
					$args['completed'] ? self::get_string( 'step-complete' ) : self::get_string( 'step-incomplete' )
				);
			}

			$step_file = apply_filters(
				self::$template . '_setup_step_' . $args['step'] . '_file',
				trailingslashit( self::$args['steps_dir'] ) . $args['step'] . '.php'
			);

			if ( file_exists( $step_file ) ) {
				include( $step_file );
			}
		}

		/**
		 * Output the admin page.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function output_admin_page() {
	?>
		<?php do_action( 'astoundify_setup_guide_before', __CLASS__ ); ?>

		<div class="wrap about-wrap setup-guide-steps">
			<?php do_action( 'astoundify_setup_guide_intro', __CLASS__ ); ?>
		</div>

		<div id="poststuff" class="wrap setup-guide-steps" style="margin: 25px 40px 0 20px">
			<?php self::steps(); ?>
		</div>

		<?php do_action( 'astoundify_setup_guide_after', __CLASS__ ); ?>
<?php
		}

		/**
		 * Run the steps.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function steps() {
			do_accordion_sections( self::$template . '_setup_steps', 'normal', null );
		}

		/**
		 * Add the page title to the intro.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public static function output_page_intro_title() {
			echo '<h1>' . sprintf( self::get_string( 'intro-title' ), esc_attr( self::$current_theme->get( 'Name' ) . ' ' . self::$current_theme->get( 'Version' ) ) ) . '</h1>';
		}

		/**
		 * Generate a link that allows site admins to supress the Setup Guide menu location
		 * under the "Appearance" tab.
		 *
		 * @since 1.1.0
		 * @return string $url
		 */
		public static function get_hide_menu_item_url() {
			$url = admin_url( 'admin.php?page=' . self::get_page_id() );

			return esc_url( add_query_arg( array(
				'hide_menu_item' => 1,
			) ) );
		}

		/**
		 * Check if we want to hide the menu item.
		 *
		 * @since 1.1.0
		 * @return void
		 */
		public static function maybe_hide_menu_item() {
			$page = isset( $_GET['page'] ) && self::get_page_id() == $_GET['page'];
			$hide = isset( $_GET['hide_menu_item'] );

			if ( $page && $hide ) {
				update_option( 'astoundify_setup_guide_hidden', true );

				wp_safe_redirect( self::get_page_url() );
				exit();
			}
		}
	}
endif;
