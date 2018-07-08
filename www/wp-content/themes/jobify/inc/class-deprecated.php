<?php
/**
 * Deprecated things to avoid fatal errors.
 *
 * @since 3.0.0
 * @package Jobify
 * @category Utilities
 */
class Jobify_Deprecated {

	public function __construct() {
		// shortcodes
		add_shortcode( 'jobify_login_form', array( $this, 'shortcode_login_form' ) );
		add_shortcode( 'jobify_register_form', array( $this, 'shortcode_register_form' ) );
	}

	public function shortcode_login_form() {
		if ( jobify()->get( 'woocommerce' ) ) {
			return do_shortcode( '[woocommerce_my_account]' );
		} else {
			return sprintf( __( 'Please install <a href="%s">WooCommerce</a> to use a custom login form.', 'jobify' ), esc_url( 'https://wordpress.org/plugins/woocommerce/' ) );
		}
	}

	public function shortcode_register_form() {
		if ( jobify()->get( 'woocommerce' ) ) {
			if ( class_exists( 'WooCommerce_Simple_Registration' ) ) {
				return do_shortcode( '[woocommerce_simple_registration]' );
			} else {
				return do_shortcode( '[woocommerce_my_account]' );
			}
		} else {
			return sprintf( __( 'Please install <a href="%s">WooCommerce</a> to use a custom registration form. Enable the registration form in "WooCommerce &rarr; Settings &rarr; Accounts"', 'jobify' ), esc_url( 'https://wordpress.org/plugins/woocommerce/' ) );
		}
	}

}

// this *has* to be loaded so don't let anyone stop it
new Jobify_Deprecated();
