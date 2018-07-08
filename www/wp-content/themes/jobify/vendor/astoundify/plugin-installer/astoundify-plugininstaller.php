<?php
/*
 * Plugin Name: Astoundify Plugin Installer
 * Plugin URI: https://astoundify.com
 * Description: Easy inline plugin installation.
 * Version: 1.0.1
 * Author: Astoundify
 * Author URI: http://astoundify.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include Astoundify ModuleLoader
 *
 * @since 1.0.0
 * @see https://github.com/Astoundify/module-loader
 */
require_once( dirname( __FILE__ ) . '/vendor/astoundify/module-loader/astoundify-moduleloader.php' );

/**
 * Autoloader for Modules
 *
 * @since 1.0.0
 *
 * @param string $class
 */
function astoundify_plugininstaller_moduleloader( $class ) {
	$prefix = 'Astoundify_PluginInstaller_';
	$base_dir = dirname( __FILE__ ) . '/app/';

	astoundify_moduleloader_autoload( $class, $prefix, $base_dir );
}
spl_autoload_register( 'astoundify_plugininstaller_moduleloader' );

/**
 * Start up the installer. Can pass initial configuration options.
 *
 * Included early so it can be referenced immediately when this file is included.
 *
 * @since 1.0.0
 *
 * @param array $options
 */
function astoundify_plugininstaller( $options = array() ) {
	$astoundify_plugininstaller = new Astoundify_PluginInstaller_Manager();
	call_user_func( array( $astoundify_plugininstaller, 'set_options' ), $options );
}
