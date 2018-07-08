<?php

class Jobify_Template_Header {

	public function __construct() {
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Filter the `body` class.
	 *
	 * @since 3.0.0
	 *
	 * @param array $classes
	 * @return array $classes
	 */
	public function body_class( $classes ) {
		if ( true === (bool) get_theme_mod( 'fixed-header', true ) ) {
			$classes[] = 'fixed-header';
		}

		return $classes;
	}

	/**
	 * Load the `header` variant of the searchform.
	 *
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	public function search_form() {
		ob_start();
		locate_template( array( 'searchform-header.php' ), true, false );
		return ob_get_clean();
	}

	/**
	 * Output the custom header styles set in the Customizer
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function custom_header_style() {
		$header_image = get_header_image();
		$text_color   = get_header_textcolor();

		if ( 'blank' == $text_color ) {
			$text_color = 'fff';
		}
	?>
		<style type="text/css">
		<?php if ( ! display_header_text() ) : ?>
		.site-title span {
			position: absolute;
			clip: rect(1px, 1px, 1px, 1px);
		}
		<?php endif; ?>
		.site-branding,
		.site-description,
		.site-branding:hover {
			color: #<?php echo esc_attr( $text_color ); ?>;
			text-decoration: none;
		}
		</style>
		<?php
	}

}
