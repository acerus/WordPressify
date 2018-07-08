<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://aum.im
 * @since             1.0.0
 * @package           Featured_Image_Generator
 *
 * @wordpress-plugin
 * Plugin Name:       Featured Image Generator
 * Plugin URI:        https://designilcode.com
 * Description:       Get beautiful photos from free license website like Unsplash or uploads your photo. You can customize images by inserting layers and texts. An export image for ready to use.
 * Version:           1.1.7
 * Author:            DesignilCode
 * Author URI:        https://www.designilcode.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       featured-image-generator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-featured-image-generator-activator.php
 */
function activate_featured_image_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-featured-image-generator-activator.php';	
	Featured_Image_Generator_Activator::activate();	
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-featured-image-generator-deactivator.php
 */
function deactivate_featured_image_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-featured-image-generator-deactivator.php';
	Featured_Image_Generator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_featured_image_generator' );
register_deactivation_hook( __FILE__, 'deactivate_featured_image_generator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-featured-image-generator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_featured_image_generator() {

	$plugin = new Featured_Image_Generator();
	$plugin->run();

}

run_featured_image_generator();

add_filter( 'plugin_row_meta', 'fig_add_plugin_row_meta', 10, 2);
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'fig_add_plugin_action_links' );

function fig_add_plugin_row_meta($meta, $file) {
    if ($file == plugin_basename( __FILE__ )) {
    	$meta[] = '<a href="https://www.paypal.me/watcharapon/0usd" target="_blank">Donate</a>';
	}
	return $meta;
}

function fig_add_plugin_action_links( $links ) {
	return array_merge(
		array(
			'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=featured-image-generator-setting-page">Settings</a>'
		),
		$links
	);
}