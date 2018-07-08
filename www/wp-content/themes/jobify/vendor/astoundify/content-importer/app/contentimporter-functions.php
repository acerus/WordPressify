<?php
/**
 * General functions that themes or plugins can use to
 * implement functionality without accessing any internals directly.
 *
 * @since 1.3.0
 */

/**
 * Get a config option.
 *
 * @since 1.3.0
 *
 * @param string $config_key
 * @return mixed
 */
function astoundify_contentimporter_get_config( $config_key ) {
	return Astoundify_ContentImporter_Config::get( $config_key );
}

/**
 * Add a config option.
 *
 * @since 1.3.0
 *
 * @param string $config_key
 * @param mixed  $value
 */
function astoundify_contentimporter_add_config( $config_key, $value ) {
	return Astoundify_ContentImporter_Config::add( $config_key, $value );
}

/**
 * Get default l10n strings.
 *
 * @since 1.3.0
 *
 * @return array $strings
 */
function astoundify_contentimporter_get_default_strings() {
	return array(
		'type_labels' => array(
			'childtheme' => array( __( 'Child Theme', 'jobify' ), __( 'Child Theme', 'jobify' ) ),
			'setting' => array( __( 'Setting', 'jobify' ), __( 'Settings', 'jobify' ) ),
			'thememod' => array( __( 'Theme Customization', 'jobify' ), __( 'Theme Customizations', 'jobify' ) ),
			'nav-menu' => array( __( 'Navigation Menu', 'jobify' ), __( 'Navigation Menus', 'jobify' ) ),
			'term' => array( __( 'Term', 'jobify' ), __( 'Terms', 'jobify' ) ),
			'object' => array( __( 'Content', 'jobify' ), __( 'Contents', 'jobify' ) ),
			'nav-menu-item' => array( __( 'Navigation Menu Item', 'jobify' ), __( 'Navigation Menu Items', 'jobify' ) ),
			'widget' => array( __( 'Widget', 'jobify' ), __( 'Widgets', 'jobify' ) ),
			'comment' => array( __( 'Comment', 'jobify' ), __( 'Comments', 'jobify' ) ),
		),
		'import' => array(
			'complete' => __( 'Import Complete!', 'jobify' ),
		),
		'reset' => array(
			'complete' => __( 'Reset Complete', 'jobify' ),
		),
		'errors' => array(
			'process_action' => __( 'Invalid process action.', 'jobify' ),
			'process_type' => __( 'Invalid process type.', 'jobify' ),
			'iterate' => __( 'Iteration process failed.', 'jobify' ),
			'cap_check_fail' => __( 'You do not have permission to manage content.', 'jobify' ),
			'file_reading' => __( 'Unable to read content files. Please add <code>define( "FS_METHOD", "direct" );</code> to your <code>wp-config.php</code> file.', 'jobify' ),
		),
	);
}

/**
 * Merge custom/implementation-defined strings.
 *
 * @since 1.3.0
 *
 * @return array $strings
 */
function astoundify_contentimporter_get_strings() {
	return wp_parse_args( astoundify_contentimporter_get_config( 'strings' ), astoundify_contentimporter_get_default_strings() );
}

/**
 * Get a l10n string.
 *
 * @since 1.3.0
 *
 * @param string $string
 * @param string $group
 * @return mixed false|string
 */
function astoundify_contentimporter_get_string( $string, $group = false ) {
	$strings = astoundify_contentimporter_get_strings();

	if ( $group ) {
		if ( isset( $strings[ $group ] ) && isset( $strings[ $group ][ $string ] ) ) {
			return $strings[ $group ][ $string ];
		}
	} else {
		if ( isset( $strings[ $string ] ) ) {
			return $strings[ $string ];
		}
	}

	return false;
}

/**
 * Get a list of plugins required for all base content.
 *
 * This is meant to interface with Astoundify SetupGuide and Astoundify PluginInstaller.
 *
 * At minimum it should contain a key of the plugin's slug, with an array
 * of additional information. e.g
 *
 * 'woocommerce' => array(
 *     'label' => 'WooCommerce',
 *     'condition' => class_exists( 'WooCommerce' )
 *  )
 *
 * The implementation of `label` and `condition` is up to the application using the library.
 *
 * @see http://github.com/astoundify/setup-guide
 * @see https://github.com/astoundify/plugin-installer
 *
 * @since 1.3.0
 */
function astoundify_contentimporter_get_required_plugins() {
	return apply_filters( 'astoundify_contentimporter_required_plugins', array() );
}

/**
 * Get a list of plugins recommended for the content being imported.
 *
 * This is meant to interface with Astoundify SetupGuide and Astoundify PluginInstaller.
 *
 * At minimum it should contain a key of the plugin's slug, with an array
 * of additional information. e.g
 *
 * 'woocommerce' => array(
 *     'label' => 'WooCommerce',
 *     'condition' => class_exists( 'WooCommerce' )
 *  )
 *
 * The implementation of `label` and `condition` is up to the application using the library.
 *
 * @see http://github.com/astoundify/setup-guide
 * @see https://github.com/astoundify/plugin-installer
 *
 * @since 1.3.0
 */
function astoundify_contentimporter_get_recommended_plugins() {
	return apply_filters( 'astoundify_contentimporter_recommended_plugins', array() );
}
