<?php
/**
 * Helper/wrapper functions to allow internals to remain more dynamic.
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */

/**
 * Get all options from configuration.
 *
 * @since 1.1.0
 *
 * @return array $options
 */
function astoundify_themecustomizer_get_options() {
	return Astoundify_ThemeCustomizer_Manager::get_options();
}

/**
 * Set multiple options.
 *
 * @since 1.1.0
 *
 * @param array $options
 * @return array $options
 */
function astoundify_themecustomizer_set_options( $options ) {
	return Astoundify_ThemeCustomizer_Manager::set_options( $options );
}

/**
 * Get an option from configuration.
 *
 * @since 1.1.0
 *
 * @param string $key
 * @return mixed
 */
function astoundify_themecustomizer_get_option( $option ) {
	return Astoundify_ThemeCustomizer_Manager::get_option( $option );
}

/**
 * Set a configuration option.
 *
 * @since 1.1.0
 *
 * @param array $options
 * @return mixed
 */
function astoundify_themecustomizer_set_option( $option, $option_value ) {
	return Astoundify_ThemeCustomizer_Manager::set_option( $option, $option_value );
}

/**
 * Choose which way to load an AssetSource's data.
 *
 * @since 1.1.0
 *
 * @param string $origin The assetsources raw data origin
 * @return Astoundify_ThemeCustomizer_AssetLoader_Loader
 */
function astoundify_themecustomizer_get_assetloader( $origin ) {
	return Astoundify_ThemeCustomizer_AssetLoader_LoaderFactory::get_loader( $origin );
}

/**
 * Get theme mods based on a key pattern.
 *
 * @since 1.1.0
 *
 * @param string $pattern
 * @return array $keys
 */
function astoundify_themecustomizer_get_mods_like( $pattern ) {
	$mods = get_theme_mods();
	$found = array();

	if ( ! $mods ) {
		return $found;
	}

	foreach ( $mods as $key => $value ) {
		if ( preg_match( "/{$property}/", $key ) ) {
			$found[ $key ] = $value;
		}
	}

	return $found;
}

/**
 * Get a control group theme mod.
 *
 * This is a wrapper for get_theme_mod() and should only be used
 * for theme mods relating to a control group.
 *
 * @since 1.0.0
 *
 * @param string $theme_mod_key The theme mod to look for.
 * @param string $group The selected control group group.
 * @param string $group_id The control group to look in.
 * @return mixed
 */
function astoundify_themecustomizer_get_control_group_mod( $theme_mod_key, $group = false, $group_id ) {
	$default = astoundify_themecustomizer_get_control_group_mod_default( $theme_mod_key, $group, $group_id );

	return get_theme_mod( $theme_mod_key, $default );
}

/**
 * Get a control group theme mod default.
 *
 * This is a wrapper for get_theme_mod() and should only be used
 * for theme mods relating tocontrol groups.
 *
 * This implementation assumes fonts are managed through a ControlGroup
 * and have defaults that can be referenced. However that ControlGroup
 * does not have to have a registered setting or control.
 *
 * By default no specific group is selected in those control group and the first is used.
 * Pass `$group` and `$group_id` to get a different control group and coressponding child group.
 *
 * @since 1.0.0
 *
 * @param string $theme_mod_key The theme mod to look for.
 * @param string $group The selected control group group.
 * @param string $group_id The control group to look in.
 * @return mixed
 */
function astoundify_themecustomizer_get_control_group_mod_default( $theme_mod_key, $group = false, $group_id ) {
	$default = null;
	$defaults = astoundify_themecustomizer_get_control_group_defaults( $group_id, $group );

	if ( isset( $defaults[ $theme_mod_key ] ) ) {
		$default = $defaults[ $theme_mod_key ];
	}

	return $default;
}

/**
 * Get a specific control group.
 *
 * @since 1.0.0
 * @param string $group_id
 * @return mixed Array of control group information or false.
 */
function astoundify_themecustomizer_get_control_group( $group_id ) {
	$control_group = astoundify_themecustomizer_get_option( 'definitions_dir' ) . '/control-groups/' . $group_id . '.php';

	if ( file_exists( $control_group ) ) {
		$groups = include( $control_group );

		if ( is_array( $groups ) ) {
			return apply_filters( 'astoundify_themecustomizer_control_group_' . $group_id, $groups );
		}
	}

	return false;
}

/**
 * Get the defaults for a specific control group.
 *
 * @since 1.0.0
 * @param string $group_id
 * @param bool   $group
 * @return mixed Array of default values or false.
 */
function astoundify_themecustomizer_get_control_group_defaults( $group_id, $group = false ) {
	$groups = astoundify_themecustomizer_get_control_group( $group_id );

	if ( is_array( $groups ) ) {
		// the group was not set by a theme mod, but default values have been set
		if ( ! $group ) {
			$group = key( $groups );
		}

		return $groups[ $group ]['controls'];
	}

	return false;
}

/**
 * Get a font theme mod.
 *
 * @since 1.0.0
 *
 * @param string $theme_mod_key The theme mod to look for.
 * @param string $group The selected control group group.
 * @param string $group_id The control group to look in.
 * @return mixed
 */
function astoundify_themecustomizer_get_typography_mod( $theme_mod_key, $group = false, $group_id = 'typography-font-pack' ) {
	$default = astoundify_themecustomizer_get_typography_mod_default( $theme_mod_key, $group, $group_id );

	return get_theme_mod( $theme_mod_key, $default );
}

/**
 * Get a default for a typography control group.
 *
 * @since 1.0.0
 *
 * @param string $theme_mod_key The theme mod to look for.
 * @param string $group The selected control group group.
 * @param string $group_id The control group to look in.
 * @return mixed
 */
function astoundify_themecustomizer_get_typography_mod_default( $theme_mod_key, $group = false, $group_id = 'typography-font-pack' ) {
	if ( ! $group ) {
		$group = get_theme_mod( 'typography-font-pack', 'default' );
	}

	return astoundify_themecustomizer_get_control_group_mod_default( $theme_mod_key, $group, $group_id );
}

/**
 * Get a color theme mod.
 *
 * @since 1.0.0
 *
 * @param string $theme_mod_key The theme mod to look for.
 * @param string $group The selected control group group.
 * @param string $group_id The control group to look in.
 * @return mixed
 */
function astoundify_themecustomizer_get_colorscheme_mod( $theme_mod_key, $group = false, $group_id = 'color-scheme' ) {
	$default = astoundify_themecustomizer_get_colorscheme_mod_default( $theme_mod_key, $group, $group_id );

	return get_theme_mod( $theme_mod_key, $default );
}

/**
 * Get a color theme mod default.
 *
 * @since 1.0.0
 *
 * @param string $theme_mod_key The theme mod to look for.
 * @param string $group The selected control group group.
 * @param string $group_id The control group to look in.
 * @return mixed
 */
function astoundify_themecustomizer_get_colorscheme_mod_default( $theme_mod_key, $group = false, $group_id = 'color-scheme' ) {
	if ( ! $group ) {
		$group = get_theme_mod( 'color-scheme', 'default' );
	}

	return astoundify_themecustomizer_get_control_group_mod_default( $theme_mod_key, $group, $group_id );
}

/**
 * Add a new CSS rule to the array.
 *
 * Accepts data to eventually be turned into CSS. Usage:
 *
 * astoundify_themecustomizer_add_css( array(
 *     'selectors'    => array( '.site-header-main' ),
 *     'declarations' => array(
 *         'background-color' => '#00ff00',
 *     ),
 *     'media' => 'screen and (min-width: 800px)',
 * ) );
 *
 * Selectors represent the CSS selectors; declarations are the CSS properties and values with keys being properties
 * and values being values. 'media' can also be declared to specify the media query.
 *
 * Note that data *must* be sanitized when adding to the data array. Because every piece of CSS data has special
 * sanitization concerns, it must be handled at the time of addition, not at the time of output. The theme handles
 * this in the the other helper files, i.e., the data is already sanitized when `add()` is called.
 *
 * @since 1.0.0
 * @param array $data The selectors and properties to add to the CSS.
 */
function astoundify_themecustomizer_add_css( $data ) {
	Astoundify_ThemeCustomizer_Output_CSSGenerator::add( $data );
}

/**
 * Compile the CSS data array into standard CSS syntax.
 *
 * @since 1.0.0.
 * @return string The CSS that is built from the data.
 */
function astoundify_themecustomizer_get_css() {
	return Astoundify_ThemeCustomizer_Output_CSSGenerator::build();
}

/**
 * Darken a HEX value.
 *
 * @since 1.0.0
 * @param string $hex
 * @param int    $steps
 * @return string $hex
 */
function astoundify_themecustomizer_darken_hex( $hex, $steps ) {
	return Astoundify_ThemeCustomizer_Output_CSSGenerator::darken( $hex, $steps );
}

/**
 * Add an asset source.
 *
 * @since 1.1.0
 *
 * @param string $key The unique key of the source.
 * @param object $source Source instance.
 * @return object Source instance.
 */
function astoundify_themecustomizer_add_assetsource( $key, $source ) {
	return Astoundify_ThemeCustomizer_AssetSources_Manager::add( $key, $source );
}

/**
 * Get an asset source.
 *
 * @since 1.1.0
 *
 * @param string $key The unique key of the source.
 * @return mixed Source instance or false if not registered
 */
function astoundify_themecustomizer_get_assetsource( $key ) {
	return Astoundify_ThemeCustomizer_AssetSources_Manager::get( $key );
}

/**
 * Get an asset source's available choices for a customizer control.
 *
 * @since 1.1.0
 *
 * @param string $source_key The unique key of the source.
 * @return array $choices
 */
function astoundify_themecustomizer_get_assetsource_choices( $source_key ) {
	$source = astoundify_themecustomizer_get_assetsource( $source_key );

	// no choices if source is invalid
	if ( ! $source ) {
		return array();
	}

	return $source->get_customize_control_choices();
}

/**
 * Get a font stack based on a set font and a source.
 *
 * @since 1.1.0
 *
 * @param string $font The font choice.
 * @param string $source_key The unique key of the source.
 * @return string $font_stack.
 */
function astoundify_themecustomizer_get_font_stack( $font, $source_key ) {
	$source = astoundify_themecustomizer_get_assetsource( $source_key );

	// return the default; this should be grabbed from the default that is already defined
	if ( ! $source ) {
		return '"Helvetica Neue",Helvetica,Arial,sans-serif';
	}

	$stack = $source->get_font_stack( $font );

	return $stack;
}

/**
 * Return the URL for Google Fonts.
 *
 * @since 1.1.0
 *
 * @return string $url
 */
function astoundify_themecustomizer_get_googlefont_url() {
	$source = astoundify_themecustomizer_get_assetsource( 'googlefonts' );

	return $source->generate_url();
}

/**
 * Get default control settings for the Typography multi-control.
 *
 * @since 1.1.0
 *
 * @return array $controls
 */
function astoundify_themecustomizer_get_default_typography_controls() {
	return array(
		'font-family' => array(
			'label' => 'Font Family',
			'placeholder' => 'Search for a font...',
		),
		'font-size' => array(
			'label' => 'Font Size',
		),
		'font-weight' => array(
			'label' => 'Font Weight',
			'choices' => array(
				'normal' => 'Normal',
				'bold' => 'Bold',
			),
		),
		'line-height' => array(
			'label' => 'Line Height',
		),
	);
}
