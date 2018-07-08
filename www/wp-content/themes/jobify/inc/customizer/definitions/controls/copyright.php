<?php
/**
 * Copyright
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'copyright', array(
	'default' => sprintf( '&copy; %1$s %2$s &mdash; All Rights Reserved', date( 'Y' ), get_bloginfo( 'name' ) ),
) );

$wp_customize->add_control( 'copyright', array(
	'label'   => __( 'Text', 'jobify' ),
	'section' => 'footer-copyright',
	'priority' => 10,
) );
