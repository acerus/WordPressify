<?php
/**
 * Primary Menu Mobile Width
 *
 * @uses $wp_customize
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'nav-menu-primary-width', array(
	'default' => 'large',
) );

$wp_customize->add_control( 'nav-menu-primary-width', array(
	'label' => __( 'Mobile Menu Trigger Width', 'jobify' ),
	'type' => 'select',
	'choices' => array(
		'small' => __( 'Small', 'jobify' ),
		'medium' => __( 'Medium', 'jobify' ),
		'large' => __( 'Large', 'jobify' ),
	),
	'priority' => 10,
	'section' => 'nav-menu-settings',
) );
