<?php
/**
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */

namespace Astoundify\ThemeCustomizer\Tests;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0.0
 */
class Manager extends \Astoundify_ThemeCustomizer_TestCase {

	/** dont init astoundify_themecustomizer() */
	public function setUp() {}

	/**
	 * @since 1.1.0
	 */
	public function test_first_instantiation_has_no_options() {
		astoundify_themecustomizer();

		$this->assertEqualSets(
			array(),
			astoundify_themecustomizer_get_options()
		);
	}

	/**
	 * @since 1.1.0
	 */
	public function test_set_option_before_instantiation() {
		astoundify_themecustomizer_set_option( 'foo', 'bar' );
		astoundify_themecustomizer();

		$this->assertEquals( astoundify_themecustomizer_get_option( 'foo' ), 'bar' );
	}

	/**
	 * @since 1.1.0
	 */
	public function test_set_options_on_instantiation() {
		astoundify_themecustomizer( array(
			'asset_url' => 'http://test.com',
		) );

		$this->assertEquals( 'http://test.com', astoundify_themecustomizer_get_option( 'asset_url' ) );
	}

	/**
	 * @since 1.1.0
	 */
	public function test_change_option_after_instantiation() {
		astoundify_themecustomizer( array(
			'asset_url' => 'http://foo.com',
		) );

		astoundify_themecustomizer_set_option( 'asset_url', 'http://test.com' );

		$this->assertEquals( astoundify_themecustomizer_get_option( 'asset_url' ), 'http://test.com' );
	}

	/**
	 * @since 1.1.0
	 */
	public function test_get_options_returns_array() {
		$this->assertInternalType( 'array', astoundify_themecustomizer_get_options() );
	}

}
