<?php
/**
 * Font Pack group definitions.
 *
 * @since 3.6.0
 */
$groups = array(
	'default' => array(
		'title' => __( 'Default', 'jobify' ),
		'controls' => array(
			'typography-body-font-family' => 'Varela Round',
			'typography-body-font-size' => 16,
			'typography-body-font-weight' => 'normal',
			'typography-body-line-height' => 1.5,

			'typography-page-header-font-family' => 'Varela Round',
			'typography-page-header-font-size' => 34,
			'typography-page-header-font-weight' => 'normal',
			'typography-page-header-line-height' => 1.3,

			'typography-entry-title-font-family' => 'Varela Round',
			'typography-entry-title-font-size' => 38,
			'typography-entry-title-font-weight' => 'normal',
			'typography-entry-title-line-height' => 1.3,

			'typography-widget-title-font-family' => 'Varela Round',
			'typography-widget-title-font-size' => 22,
			'typography-widget-title-font-weight' => 'normal',
			'typography-widget-title-line-height' => 1.3,

			'typography-home-widget-title-font-family' => 'Varela Round',
			'typography-home-widget-title-font-size' => 36,
			'typography-home-widget-title-font-weight' => 'normal',
			'typography-home-widget-title-line-height' => 1.3,

			'typography-home-widget-description-font-family' => 'Varela Round',
			'typography-home-widget-description-font-size' => 16,
			'typography-home-widget-description-font-weight' => 'normal',
			'typography-home-widget-description-line-height' => 1.5,

			'typography-button-font-family' => 'Montserrat',
			'typography-button-font-size' => 14,
			'typography-button-font-weight' => 'bold',
			'typography-button-line-height' => 1.3,

			'typography-input-font-family' => 'Varela Round',
			'typography-input-font-size' => 16,
			'typography-input-font-weight' => 'normal',
			'typography-input-line-height' => 1,
		),
	),
);

return apply_filters( 'jobify_customize_font_packs', $groups );
