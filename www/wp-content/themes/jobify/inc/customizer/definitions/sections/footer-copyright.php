<?php
/**
 * Footer: Copyright
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'footer-copyright', array(
	'title' => _x( 'Copyright', 'customizer section title (colors)', 'jobify' ),
	'panel' => 'footer',
	'priority' => 20,
) );
