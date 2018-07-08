<?php

class Jobify_Template_Assets {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		add_filter( 'mce_css', array( $this, 'mce_css' ) );

		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	public function enqueue_scripts() {
		global $post;

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		$deps = array( 'jquery' );

		if ( jobify()->get( 'woocommerce' ) ) {
			$deps[] = 'woocommerce';
		}

		wp_enqueue_script( 'jobify', get_template_directory_uri() . '/js/jobify.min.js', $deps, jobify_get_theme_version(), true );
		wp_enqueue_script( 'salvattore', get_template_directory_uri() . '/js/vendor/salvattore/salvattore.min.js', array(), '', true );

		$jobify_settings = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'archiveurl' => get_post_type_archive_link( 'job_listing' ),
			'i18n'    => array(
				'magnific' => array(
					'tClose' => __( 'Close', 'jobify' ),
					'tLoading' => __( 'Loading...', 'jobify' ),
					'tError' => __( 'The content could not be loaded.', 'jobify' ),
				),
			),
			'pages'   => array(
				'is_job'          => is_singular( 'job_listing' ),
				'is_resume'       => is_singular( 'resume' ),
				'is_testimonials' => is_page_template( 'page-templates/testimonials.php' ) || is_post_type_archive( 'testimonial' ),
			),
		);

		$jobify_settings = apply_filters( 'jobify_js_settings', $jobify_settings );

		wp_localize_script( 'jobify', 'jobifySettings', $jobify_settings );
	}

	public function enqueue_styles() {
		$fonts_url = $this->google_fonts_url();

		if ( ! empty( $fonts_url ) ) {
			wp_enqueue_style( 'jobify-fonts', esc_url_raw( $fonts_url ), array(), null );
		}

		wp_enqueue_style( 'jobify-parent', get_template_directory_uri() . '/style.css', array(), jobify_get_theme_version() );
		wp_style_add_data( 'jobify-parent', 'rtl', 'replace' );
	}

	public function mce_css( $mce_css ) {
		$fonts_url = $this->google_fonts_url();

		if ( empty( $fonts_url ) ) {
			return $mce_css;
		}

		if ( ! empty( $mce_css ) ) {
			$mce_css .= ',';
		}

		$mce_css .= esc_url_raw( str_replace( ',', '%2C', $fonts_url ) );

		return $mce_css;
	}

	public function body_class( $classes ) {
		if ( wp_style_is( 'jobify-fonts', 'queue' ) ) {
			$classes[] = 'custom-font';
		}

		// Nav menu trigger width
		$classes[] = 'nav-menu-break-' . get_theme_mod( 'nav-menu-primary-width', 'large' );

		return $classes;
	}

	private function google_fonts_url() {
		return astoundify_themecustomizer_get_googlefont_url();
	}

}
