<?php
/**
 * A better way to manage large <select> boxes.
 *
 * Especially useful if there are multiple <select> boxes in a section
 * that all contain the same options.
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
class BigChoices extends \Astoundify_ThemeCustomizer_TestCase {

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

		parent::setUp();
	}

	/**
	 * choice id can be set
	 */
	public function test_choice_id_is_set() {
		$control = $this->wp_customize->add_control( new \Astoundify_ThemeCustomizer_Control_BigChoices(
			$this->wp_customize,
			'choice_id_is_set',
			array(
				'choices_id' => 'numbers',
			)
		) );

		$this->assertEquals( 'numbers', $control->choices_id );
	}

	/**
	 * choices can be set
	 */
	public function test_choices_are_set() {
		$control = $this->wp_customize->add_control( new \Astoundify_ThemeCustomizer_Control_BigChoices(
			$this->wp_customize,
			'choices_are_set',
			array(
				'choices' => array( 1, 2, 3 ),
			)
		) );

		$this->assertEqualSets( array( 1, 2, 3 ), $control->choices );
	}

	/**
	 * localization data is passed
	 */
	public function test_choices_set_in_source() {
		$control = $this->wp_customize->add_control( new \Astoundify_ThemeCustomizer_Control_BigChoices(
			$this->wp_customize,
			'choices_set_in_source',
			array(
				'choices_id' => 'more-numbers',
				'choices' => array( 1, 2, 3 ),
			)
		) );

		do_action( 'customize_controls_enqueue_scripts', 9 );

		$opts = apply_filters( 'astoundify_themecustomizer_scripts', array(
			'BigChoices' => array(),
		) );

		$this->assertTrue( isset( $opts['BigChoices']['more-numbers'] ) );
		$this->assertEqualSets( array( 1, 2, 3 ), $opts['BigChoices']['more-numbers'] );
	}

	/**
	 * scripts are enqueued
	 */
	public function test_scripts_are_enqueued() {
		$control = $this->wp_customize->add_control( new \Astoundify_ThemeCustomizer_Control_BigChoices(
			$this->wp_customize,
			'scripts_are_enqueued',
			array()
		) );

		do_action( 'customize_controls_enqueue_scripts' );

		$this->assertTrue( wp_script_is( 'astoundify-themecustomizer-bigchoices', 'enqueued' ) );
	}

}
