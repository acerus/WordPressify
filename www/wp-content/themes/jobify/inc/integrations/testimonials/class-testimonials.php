<?php
/**
 * Testimonials by WooThemes
 */

class Jobify_Testimonials extends Jobify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {}

	public function setup_actions() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_filter( 'woothemes_testimonials_item_template', array( $this, 'testimonials_item_template' ), 10, 2 );
	}

	/**
	 * Registers widgets, and widget areas for Testimonials
	 *
	 * @since Jobify 1.7.0
	 *
	 * @return void
	 */
	function widgets_init() {
		require_once( get_template_directory() . '/inc/integrations/testimonials/widgets/class-widget-companies.php' );
		require_once( get_template_directory() . '/inc/integrations/testimonials/widgets/class-widget-testimonials.php' );

		register_widget( 'Jobify_Widget_Companies' );
		register_widget( 'Jobify_Widget_Testimonials' );
	}

	/**
	 * Testimonials by WooThemes
	 *
	 * @since Jobify 1.0
	 *
	 * @param string $tpl
	 * @return string $tpl
	 */
	function testimonials_item_template( $tpl, $args ) {
		if ( 'individual' == $args['category'] ) {
			$tpl  = '<blockquote id="quote-%%ID%%" class="woothemes-testimonial woothemes-testimonial--individual">';
			$tpl .= '%%TEXT%%';
			$tpl .= '<cite class="woothemes-testimonial--individual__author">%%AVATAR%% %%AUTHOR%%</cite>';
			$tpl .= '</blockquote>';
		} else {
			$tpl  = '<div class="woothemes-testimonial woothemes-testimonial--company">';
			$tpl .= '%%AVATAR%%';
			$tpl .= '</div>';
		}

		return $tpl;
	}

}
