<?php

class Jobify_WooCommerce_Template {

	public function __construct() {
		add_action( 'jobify_output_customizer_css', array( $this, 'output_colors' ), 10 );

		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ), 20 );

		add_filter( 'woocommerce_enqueue_styles', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_social_login_scripts' ) );
	}

	public function load_social_login_scripts() {
		if ( class_exists( 'WC_Social_Login' ) ) {
			wc_social_login()->get_frontend_instance()->load_styles_scripts();
		}
	}

	public function enqueue_styles( $enqueue_styles ) {
		if ( isset( $enqueue_styles['woocommerce-general'] ) ) {
			unset( $enqueue_styles['woocommerce-general'] );
		}

		return $enqueue_styles;
	}

	/**
	 * Sets up theme support.
	 *
	 * @since Jobify 1.7.0
	 *
	 * @return void
	 */
	public function add_theme_support() {
		add_theme_support( 'woocommerce' );

		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}

	/**
	 * Registers widgets, and widget areas for WooCommerce
	 *
	 * @since Jobify 1.7.0
	 *
	 * @return void
	 */
	public function widgets_init() {
		register_sidebar( array(
			'name'          => __( 'Shop Sidebar', 'jobify' ),
			'id'            => 'widget-area-sidebar-shop',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}

}
