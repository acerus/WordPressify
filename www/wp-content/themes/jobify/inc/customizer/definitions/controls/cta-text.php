<?php
/**
 * CTA: Text
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'cta-text', array(
	'default' => '<h2>Got a question?</h2>We&#39;re here to help. Check out our FAQs, send us an email or call us at 1 800 555 5555',
) );

$wp_customize->add_control( 'cta-text', array(
	'label'   => __( 'Description', 'jobify' ),
	'type' => 'textarea',
	'section' => 'footer-cta',
	'priority' => 10,
) );
