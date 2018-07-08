<?php
/**
 * Description Control
 *
 * @uses WP_Customize_Control
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */
class Astoundify_Theme_Customizer_Control_Description extends WP_Customize_Control {

	/**
	 * @since 1.0.0
	 * @access public
	 * @var string $type
	 */
	public $type = 'description';

	/**
	 * Output the control HTML
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		echo '<p>' . wp_kses_post( $this->label ) . '</p>';
	}

}
