<?php
/**
 * Control Group
 *
 * A single control that is linked to many "child" controls.
 * When the parent control is changed all of the child controls
 * are automatically triggered to their set default.
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
class ControlGroup extends \Astoundify_ThemeCustomizer_TestCase {

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
	 * data attribute is generated with all linked controls
	 */
	public function test_data_attributes_are_generated() {
		$control = $this->wp_customize->add_control( new \Astoundify_ThemeCustomizer_Control_ControlGroup(
			$this->wp_customize,
			'no-setting',
			array()
		) );

		$controls = array(
			'foo' => 'bar',
		);

		$this->assertEquals( "data-controls='{\"foo\":\"bar\"}'", $control->generate_linked_control_data( $controls ) );
	}

	/**
	 * scripts are enqueued
	 */
	public function test_scripts_are_enqueued() {
		$control = $this->wp_customize->add_control( new \Astoundify_ThemeCustomizer_Control_ControlGroup(
			$this->wp_customize,
			'no-setting',
			array()
		) );

		do_action( 'customize_controls_enqueue_scripts' );

		$this->assertTrue( wp_script_is( 'astoundify-themecustomizer-controlgroup', 'enqueued' ) );
	}

}
