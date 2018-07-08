<?php
/**
 * Map Settings
 *
 * Lazy in one file for now.
 *
 * @uses $wp_customize
 * @since 3.5.0
 */

// google api
$wp_customize->add_setting( 'map-behavior-api-key', array(
	'default' => '',
) );

$wp_customize->add_control( 'map-behavior-api-key', array(
	'label' => __( 'Google Maps API Key', 'jobify' ),
	'description' => sprintf( __( 'Learn how to %s', 'jobify' ), '<a href="http://jobify.astoundify.com/article/856-create-a-google-maps-api-key" target="_blank">' . __( 'create a Google Maps API key', 'jobify' ) . '</a>' ),
	'priority' => 10,
	'section' => 'map-settings',
) );

// info trigger
$wp_customize->add_setting( 'map-behavior-trigger', array(
	'default' => 'mouseover',
) );

$wp_customize->add_control( 'map-behavior-trigger', array(
	'label' => __( 'Marker Popup Trigger', 'jobify' ),
	'priority' => 20,
	'type' => 'select',
	'choices' => array(
		'mouseover' => __( 'Hover', 'jobify' ),
		'click' => __( 'Click', 'jobify' ),
	),
	'section' => 'map-settings',
) );

// autofit
$wp_customize->add_setting( 'map-behavior-autofit', array(
	'default' => true,
) );

$wp_customize->add_control( 'map-behavior-autofit', array(
	'label' => __( 'Autofit Pins', 'jobify' ),
	'description' => __( 'Ensure all active pins are shown in the initial map view.', 'jobify' ),
	'type' => 'checkbox',
	'priority' => 28,
	'section' => 'map-settings',
) );

// center
$wp_customize->add_setting( 'map-behavior-center', array(
	'default' => '',
) );

$wp_customize->add_control( 'map-behavior-center', array(
	'label' => __( 'Default Location View', 'jobify' ),
	'description' => __( 'The default coordinates view if autofit is disabled. Ex <code>42.0616453, -88.2670675</code>', 'jobify' ),
	'priority' => 30,
	'section' => 'map-settings',
) );

// clusters
$wp_customize->add_setting( 'map-behavior-clusters', array(
	'default' => true,
) );

$wp_customize->add_control( 'map-behavior-clusters', array(
	'label' => __( 'Cluster Markers in Proximity', 'jobify' ),
	'priority' => 50,
	'type' => 'checkbox',
	'section' => 'map-settings',
) );

// grid size
$wp_customize->add_setting( 'map-behavior-grid-size', array(
	'default' => 60,
) );

$wp_customize->add_control( 'map-behavior-grid-size', array(
	'label' => __( 'Cluster Grid Size (px)', 'jobify' ),
	'priority' => 60,
	'description' => __( 'How close the markers are before the clusters appear.', 'jobify' ),
	'section' => 'map-settings',
) );

// default zoom
$wp_customize->add_setting( 'map-behavior-zoom', array(
	'default' => 3,
) );

$wp_customize->add_control( 'map-behavior-zoom', array(
	'label' => __( 'Default Zoom', 'jobify' ),
	'description' => __( '<strong>1</strong>: World, <strong>5</strong>: Landmass/continent, <strong>10</strong>: City, <strong>15</strong>: Streets, <strong>20</strong>: Buildings', 'jobify' ),
	'choices' => range( 1, 20 ),
	'type' => 'select',
	'priority' => 70,
	'section' => 'map-settings',
) );

// max zoom in
$wp_customize->add_setting( 'map-behavior-max-zoom', array(
	'default' => 17,
) );

$wp_customize->add_control( 'map-behavior-max-zoom', array(
	'label' => __( 'Maximum Zoom In', 'jobify' ),
	'description' => __( 'Must be larger than Default Zoom and Maximum Zoom Out', 'jobify' ),
	'choices' => range( 1, 20 ),
	'type' => 'select',
	'priority' => 80,
	'section' => 'map-settings',
) );

// max zoom out
$wp_customize->add_setting( 'map-behavior-max-zoom-out', array(
	'default' => 3,
) );

$wp_customize->add_control( 'map-behavior-max-zoom-out', array(
	'label' => __( 'Maximum Zoom Out', 'jobify' ),
	'description' => __( 'Must be equal to or larger than Default Zoom and less than Maximum Zoom In', 'jobify' ),
	'choices' => range( 1, 20 ),
	'type' => 'select',
	'priority' => 90,
	'section' => 'map-settings',
) );

// scrollwheel
$wp_customize->add_setting( 'map-behavior-scrollwheel', array(
	'default' => false,
) );

$wp_customize->add_control( 'map-behavior-scrollwheel', array(
	'label' => __( 'Zoom with Scrollwheel', 'jobify' ),
	'type' => 'checkbox',
	'priority' => 100,
	'section' => 'map-settings',
) );
