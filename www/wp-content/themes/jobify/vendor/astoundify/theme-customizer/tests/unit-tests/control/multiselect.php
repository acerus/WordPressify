<?php
/**
 * Multiselect
 *
 * A select box that can contain multiple selections.
 * Plain HTML by default but can be transformed with Javascript libraries.
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */

namespace Astoundify\ThemeCustomizer\Tests\Control;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0.0
 */
class Multiselect extends \Astoundify_ThemeCustomizer_TestCase {

	/**
	 * @var object $wp_customize WP_Customize_Manager
	 */
	protected $wp_customize;

	/**
	 * setUp
	 */
	public function setUp() {
		// setup customize api
		require_once( ABSPATH . WPINC . '/class-wp-customize-manager.php' );
		$GLOBALS['wp_customize'] = new \WP_Customize_Manager();
		$this->wp_customize = $GLOBALS['wp_customize'];

		do_action( 'customize_register', $this->wp_customize );
	}

	/**
	 * Set a placeholder
	 */
	public function test_can_set_placeholder() {
		$control = $this->wp_customize->add_control( new \Astoundify_ThemeCustomizer_Control_Multiselect(
			$this->wp_customize,
			'no-setting',
			array(
				'placeholder' => 'Choose a tag...',
			)
		) );

		$this->assertEquals( 'Choose a tag...', $control->placeholder );
	}

	/**
	 * Convert a comma list to a usuable array
	 */
	public function test_can_use_old_data_store() {
		set_theme_mod( 'old-data', 'one, two, three' );

		$this->wp_customize->add_setting( 'old-data' );
		$control = $this->wp_customize->add_control( new \Astoundify_ThemeCustomizer_Control_Multiselect(
			$this->wp_customize,
			'old-data',
			array()
		) );

		$this->assertEqualSets( array( 'one', 'two', 'three' ), $control->get_saved_value() );
	}

	/**
	 * scripts are enqueued
	 */
	public function test_scripts_are_enqueued() {
		$control = $this->wp_customize->add_control( new \Astoundify_ThemeCustomizer_Control_Multiselect(
			$this->wp_customize,
			'no-setting',
			array()
		) );

		do_action( 'customize_controls_enqueue_scripts' );

		$this->assertTrue( wp_script_is( 'astoundify-themecustomizer-multiselect', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'astoundify-themecustomizer-select2', 'enqueued' ) );
	}

}
