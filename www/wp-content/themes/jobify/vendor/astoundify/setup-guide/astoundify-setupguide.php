<?php
/*
 * Plugin Name: Setup Guide
 * Plugin URI: https://astoundify.com
 * Description: Create an easy to follow Setup Guide for a WordPress theme.
 * Version: 1.1.0
 * Author: Astoundify
 * Author URI: http://astoundify.com
 */

// require app
require_once( dirname( __FILE__ ) . '/app/class-astoundify-setup-guide.php' );

/**
 * Return Astoundify_Setup_Guide instance.
 *
 * @since 1.1.0
 *
 * @return Astoundify_ContentImporter
 */
function astoundify_setupguide( $args = array() ) {
	return Astoundify_Setup_Guide::init( $args );
}
