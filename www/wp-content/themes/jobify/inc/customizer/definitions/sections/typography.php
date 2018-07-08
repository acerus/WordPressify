<?php
/**
 * Typography
 *
 * @uses $wp_customize
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$elements = jobify_themecustomizer_get_typography_elements();

foreach ( $elements as $element => $label ) {

	$wp_customize->add_section( 'typography-' . $element, array(
		'title' => $label,
		'panel' => 'typography',
	) );

}
