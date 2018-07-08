<?php
/**
 * Polylang
 *
 * @since 3.8.5
 *
 * @package Jobify
 * @category Integration
 * @author Astoundify
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Jobify_Polylang extends Jobify_Integration {

	/**
	 * Load integration.
	 *
	 * @since 3.8.5
	 */
	public function __construct() {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}

		parent::__construct( dirname( __FILE__ ) );
	}

	/**
	 * Hook in to WordPress.
	 *
	 * @since 3.8.5
	 */
	public function setup_actions() {
		add_action( 'init', array( $this, 'register_strings' ) );

		add_filter( 'theme_mod_cta-text', array( $this, 'pll__' ) );
		add_filter( 'theme_mod_copyright', array( $this, 'pll__' ) );
	}

	/**
	 * Register customizer strings.
	 *
	 * @since 3.8.5
	 */
	public function register_strings() {
		pll_register_string(
			'Call to Action',
			'<h2>Got a question?</h2>We&#39;re here to help. Check out our FAQs, send us an email or call us at 1 800 555 5555',
			'Jobify',
			true
		);

		pll_register_string(
			'Copyright',
			'&copy; %1$s %2$s &mdash; All Rights Reserved',
			'Jobify',
			false
		);
	}

	/**
	 * Translate mod string.
	 *
	 * @since 3.8.5
	 *
	 * @param string $mod The value of the modification
	 * @return string
	 */
	public function pll__( $mod ) {
		return pll__( $mod );
	}

}
