<?php
/**
 * Manage font source.
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manage an font source.
 *
 * @since 1.1.0
 */
class Astoundify_ThemeCustomizer_AssetSource_Font extends Astoundify_ThemeCustomizer_AssetSource_Source {

	/**
	 * Register default font stacks for standard categories.
	 *
	 * @since 1.1.0
	 * @access public
	 * @var array $default_stacks
	 */
	public $default_stacks;

	/**
	 * Get default font stacks.
	 *
	 * @since 1.1.0
	 *
	 * @return array $default_stacks;
	 */
	public function get_default_stacks() {
		return $this->default_stacks = array(
			'serif' => 'Georgia,Times,"Times New Roman",serif',
			'sans-serif' => '"Helvetica Neue",Helvetica,Arial,sans-serif',
			'display' => 'Copperplate,Copperplate Gothic Light,fantasy',
			'handwriting' => 'Brush Script MT,cursive',
			'monospace' => 'Monaco,"Lucida Sans Typewriter","Lucida Typewriter","Courier New",Courier,monospace',
		);
	}

	/**
	 * Get a fallback stack based on a set category.
	 *
	 * @since 1.1.0
	 *
	 * @param string $category
	 * @return string $stack
	 */
	public function get_fallback_stack( $category ) {
		$stacks = $this->get_default_stacks();

		if ( ! isset( $stacks[ $category ] ) ) {
			return '';
		}

		return $stacks[ $category ];
	}

	/**
	 * Create a full font stack from the set custom font.
	 *
	 * @since 1.1.0
	 *
	 * @param string $font The selected font choice
	 * @return string $font_stack
	 */
	public function get_font_stack( $font ) {
		$font_data = $this->get_item( $font );
		$category = isset( $font_data['category'] ) ? $font_data['category'] : 'sans-serif';
		$stack = $this->get_fallback_stack( $category );

		return "\"{$font}\",{$stack}";
	}

	/**
	 * Loop through any `font-family` theme mods and create a list of unique fonts.
	 *
	 * This implementation assumes fonts are managed through a ControlGroup
	 * and have defaults that can be referenced.
	 *
	 * @since 1.1.0
	 */
	public function get_fonts_used() {
		// only works with controls containing `font-family`
		$type_controls = astoundify_themecustomizer_get_control_group_defaults( 'typography-font-pack' );
		$fonts = array();

		foreach ( $type_controls as $theme_mod_key => $value ) {
			$fonts[] = astoundify_themecustomizer_get_typography_mod( $theme_mod_key );
		}

		return array_unique( array_filter( $fonts ) );
	}

}
