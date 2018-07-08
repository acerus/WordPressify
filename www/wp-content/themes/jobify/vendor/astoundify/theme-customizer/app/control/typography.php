<?php
/**
 * Create multiple settings and controls for a group of typography elements.
 *
 * Creates:
 *
 *  - Font Family
 *  - Font Size
 *  - Font Weight
 *  - Line Height
 *
 * @uses WP_Customize_Control
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.1.0
 */
class Astoundify_ThemeCustomizer_Control_Typography extends WP_Customize_Control {

	/**
	 * @since 1.0.0
	 * @access public
	 * @var WP_Customize $wp_customize
	 */
	public $wp_customize;

	/**
	 * @since 1.0.0
	 * @access public
	 * @var string $type
	 */
	public $type = 'Typography';

	/**
	 * @since 1.0.0
	 * @access public
	 * @var string $format
	 */
	public $format = 'typography-{selector}-{setting}';

	/**
	 * @since 1.0.0
	 * @access public
	 * @var string $selector
	 */
	public $selector;

	/**
	 * @since 1.0.0
	 * @access public
	 * @var string $source
	 */
	public $source;

	/**
	 * @since 1.0.0
	 * @access public
	 * @var array $controls
	 */
	public $controls;

	public function __construct( $wp_customize, $args = array() ) {
		$this->wp_customize = $wp_customize;

		parent::__construct( $this->wp_customize, false, $args );

		$this->font_family();
		$this->font_size();
		$this->font_weight();
		$this->line_height();
	}

	/**
	 * Create a setting key based on the set base and format.
	 *
	 * @since 1.1.0
	 *
	 * @param string $what
	 * @return string $key
	 */
	public function get_key( $what ) {
		$key = str_replace( '{selector}', $this->selector, $this->format );
		$key = str_replace( '{setting}', $what, $key );

		return $key;
	}

	/**
	 * Create the Font Family setting and control.
	 *
	 * @since 1.1.0
	 */
	public function font_family() {
		$key = $this->get_key( 'font-family' );

		$this->wp_customize->add_setting( $key, array(
			'default' => astoundify_themecustomizer_get_typography_mod_default( $key ),
			'transport' => 'refresh',
		) );

		$this->wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_BigChoices(
			$this->wp_customize,
			$key,
			array_merge( $this->controls['font-family'], array(
				'choices_id' => $this->source,
				'choices' => astoundify_themecustomizer_get_assetsource_choices( $this->source ),
				'priority' => $this->priority,
				'section' => $this->section,
			) )
		) );
	}

	/**
	 * Create the font size setting and control.
	 *
	 * @since 1.1.0
	 */
	public function font_size() {
		$key = $this->get_key( 'font-size' );

		$this->wp_customize->add_setting( $key, array(
			'default' => astoundify_themecustomizer_get_typography_mod_default( $key ),
			'transport' => 'refresh',
		) );

		$this->wp_customize->add_control( $key, wp_parse_args( $this->controls['font-size'], array(
			'type' => 'number',
			'input_attrs' => array(
				'min' => 1,
				'max' => 78,
				'step' => 1,
			),
			'priority' => $this->priority + 1,
			'description' => __( 'Only affects large-screen devices', 'jobify' ),
			'section' => $this->section,
		) ) );
	}

	/**
	 * Create the font weight setting and control.
	 *
	 * @since 1.1.0
	 */
	public function font_weight() {
		$key = $this->get_key( 'font-weight' );

		$this->wp_customize->add_setting( $key, array(
			'default' => astoundify_themecustomizer_get_typography_mod_default( $key ),
			'transport' => 'refresh',
		) );

		$this->wp_customize->add_control( $key, wp_parse_args( $this->controls['font-weight'], array(
			'type' => 'select',
			'priority' => $this->priority + 2,
			'section' => $this->section,
		) ) );
	}

	/**
	 * Create the Font size setting and control.
	 *
	 * @since 1.1.0
	 */
	public function line_height() {
		$key = $this->get_key( 'line-height' );

		$this->wp_customize->add_setting( $key, array(
			'default' => astoundify_themecustomizer_get_typography_mod_default( $key ),
			'transport' => 'refresh',
		) );

		$this->wp_customize->add_control( $key, wp_parse_args( $this->controls['line-height'], array(
			'type' => 'number',
			'input_attrs' => array(
				'min' => 1,
				'max' => 5,
				'step' => 0.25,
			),
			'priority' => $this->priority + 3,
			'section' => $this->section,
		) ) );
	}
}
