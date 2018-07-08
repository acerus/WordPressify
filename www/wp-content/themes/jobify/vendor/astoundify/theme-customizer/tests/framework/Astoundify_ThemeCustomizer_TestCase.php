<?php
/**
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reusable test case that bootstraps some environment.
 *
 * @since 1.0.0
 */
class Astoundify_ThemeCustomizer_TestCase extends \WP_UnitTestCase {

	public function setUp() {
		astoundify_themecustomizer();
	}

}
