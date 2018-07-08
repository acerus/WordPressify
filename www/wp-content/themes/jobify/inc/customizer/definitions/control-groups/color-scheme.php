<?php
/**
 * Color scheme group definitions.
 *
 * @since 3.6.0
 */
$groups = array(
	'default' => array(
		'title' => __( 'Classic', 'jobify' ),
		'controls' => array(
			'background_color' => '#ffffff',
			'color-primary' => '#7dc246',
			'color-accent' => '#7dc246',
			'color-body-text' => '#797979',
			'color-link' => '#797979',
			'header_textcolor' => '#797979',
			'color-navigation-text' => '#797979',
			'color-header-background' => '#ffffff',
			'color-cta-text' => '#ffffff',
			'color-cta-background' => '#666666',
			'color-footer-widgets-text' => '#d1d1d1',
			'color-footer-widgets-title' => '#d1d1d1',
			'color-footer-widgets-link' => '#d1d1d1',
			'color-footer-widgets-background' => '#666666',
			'color-copyright-text' => '#ffffff',
			'color-copyright-link' => '#ffffff',
			'color-copyright-background' => '#7dc246',
		),
	),

	'dark-blue' => array(
		'title' => __( 'Dark Blue', 'jobify' ),
		'controls' => array(
			'background_color' => '#ffffff',
			'color-primary' => '#1e83f0',
			'color-accent' => '#1e83f0',
			'color-body-text' => '#647585',
			'color-link' => '#647585',
			'header_textcolor' => '#ffffff',
			'color-navigation-text' => '#ffffff',
			'color-header-background' => '#222b38',
			'color-cta-text' => '#fffff',
			'color-cta-background' => '#222b38',
			'color-footer-widgets-text' => '#647585',
			'color-footer-widgets-title' => '#647585',
			'color-footer-widgets-link' => '#647585',
			'color-footer-widgets-background' => '#ffffff',
			'color-copyright-text' => '#647585',
			'color-copyright-link' => '#647585',
			'color-copyright-background' => '#ffffff',
		),
	),
);

return apply_filters( 'jobify_customize_color_schemes', $groups );
