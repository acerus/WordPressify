<?php
/**
 * Colors: Footer Copyright
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'colors-footer-copyright', array(
	'title' => _x( 'Copyright', 'customizer section title (colors)', 'jobify' ),
	'panel' => 'colors',
	'priority' => 50,
) );
