<?php
/**
 * Bootstrap the ThemeCustomizer.
 *
 * This is the file that is included by the theme and the Astoundify_ThemeCustomizer
 * class is instantiated with settings such as strings, etc.
 *
 * @since 1.0.0
 */
class Astoundify_ThemeCustomizer_Manager extends Astoundify_ModuleLoader_Module {

	/**
	 * @since 1.1.0
	 * @var array $modules
	 * @access protected
	 */
	protected $modules = array(
		'customize' => 'Astoundify_ThemeCustomizer_Customize_Manager',
		'output' => 'Astoundify_ThemeCustomizer_Output_Manager',
	);

	/**
	 * Theme-specific options.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public static $options;

	/**
	 * Set options.
	 *
	 * Options are stored in an array and should be set before
	 * anything else.
	 *
	 * $customizer = astoundify_themecustomizer();
	 * $customizer::set_options( array(
	 *
	 * ) );
	 *
	 * @since 1.0.0
	 * @param array $options
	 * @return array $options
	 */
	public static function set_options( $options = array() ) {
		self::$options = wp_parse_args( $options, self::$options );

		return self::$options;
	}

	/**
	 * Get all set options.
	 *
	 * @since 1.0.0
	 * @return array $options
	 */
	public static function get_options() {
		return (array) self::$options;
	}

	/**
	 * Set a specific option.
	 *
	 * @since 1.0.0
	 * @param string $key
	 * @param mixed  $value
	 */
	public static function set_option( $key, $value ) {
		self::$options[ $key ] = $value;

		return self::get_option( $key );
	}

	/**
	 * Get a specific option.
	 *
	 * @since 1.0.0
	 * @param string $key
	 * @return mixed $option False if no option exists.
	 */
	public static function get_option( $key ) {
		$options = self::get_options();

		if ( isset( $options[ $key ] ) ) {
			return $options[ $key ];
		}

		return false;
	}

	/**
	 * Load extra dependencies.
	 *
	 * @since 1.1.0
	 */
	public function load() {
		if ( $this->is_loaded() ) {
			return;
		}

		require_once( dirname( __FILE__ ) . '/astoundify-themecustomizer-functions.php' );

		$this->loaded = true;
	}

}
