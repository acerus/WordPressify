<?php
/**
 * Steps for the setup guide.
 *
 * @since 3.3.0
 */

/** Create the steps */
$steps = array();

$api = Astoundify_Envato_Market_API::instance();

$steps['theme-updater'] = array(
	'title' => __( 'Enable Automatic Updates', 'jobify' ),
	'completed' => $api->can_make_request_with_token(),
);

$steps['install-plugins'] = array(
	'title' => __( 'Install Plugins', 'jobify' ),
	'completed' => 'n/a',
);

if ( current_user_can( 'manage_options' ) ) {
	$steps['import-content'] = array(
		'title' => __( 'Choose a Look For Your Site', 'jobify' ),
		'completed' => is_active_sidebar( 'widget-area-front-page' ),
	);
}

$steps['google-maps'] = array(
	'title' => __( 'Setup Google Maps', 'jobify' ),
	'completed' => get_theme_mod( 'map-behavior-api-key', false ),
);

$steps['customize-theme'] = array(
	'title' => __( 'Customize Your Site', 'jobify' ),
	'completed' => 'n/a',
);

$steps['support-us'] = array(
	'title' => __( 'Get Involved', 'jobify' ),
	'completed' => 'n/a',
);

return $steps;
