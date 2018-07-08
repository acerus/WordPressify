<?php
/*
 * Plugin Name: Astoundify Content Importer
 * Plugin URI: https://astoundify.com
 * Description: Import content via JSON files for easier immediate reference and manipulation.
 * Version: 1.3.0
 * Author: Astoundify
 * Author URI: http://astoundify.com
 */

// require app
require_once( dirname( __FILE__ ) . '/app/ContentImporter.php' );

/**
 * Return Astoundify_ContentImporter instance.
 *
 * @since 1.3.0
 *
 * @return Astoundify_ContentImporter
 */
function astoundify_contentimporter() {
	return Astoundify_ContentImporter::instance();
}
