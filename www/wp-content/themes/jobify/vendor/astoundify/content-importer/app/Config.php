<?php
/**
 * Configuration
 *
 * @since 1.3.0
 * @package ContentImporter
 */
class Astoundify_ContentImporter_Config {

	/**
	 * @since 1.0.0
	 * @access public
	 * @var array $options
	 */
	public static $options = array();

	/**
	 * Add an item to the config options if it does not exist.
	 *
	 * @since 1.0.0
	 *
	 * @param string $config_key
	 * @return mixed False if config already exists or null
	 */
	public static function add( $config_key, $value ) {
		if ( self::exists( $config_key ) ) {
			return false;
		}

		self::$options[ $config_key ] = $value;
	}

	/**
	 * Get a config option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $config_key
	 * @return mixed
	 */
	public static function get( $config_key ) {
		if ( ! self::exists( $config_key ) ) {
			return null;
		}

		return self::$options[ $config_key ];
	}

	/**
	 * Set a configuration item (override if it exists).
	 *
	 * @since 1.0.0
	 *
	 * @param string $config_key
	 */
	public static function set( $config_key, $value ) {
		self::$options[ $config_key ] = $value;
	}

	/**
	 * Check if a configuration item exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $config_key
	 * @return bool
	 */
	public static function exists( $config_key ) {
		return isset( self::$options[ $config_key ] );
	}

}
