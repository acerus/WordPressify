<?php
/**
 * Basic Astoundify ThemeCustomizer integration.
 */

$lib = dirname( dirname( __FILE__ ) );

// Require the library.
include_once( $lib . '/astoundify-themecustomizer.php' );

// Initialize Astoundify ThemeCustomizer
astoundify_themecustomizer( array(

	// The handle of the stylesheet inline styles should depend on.
	'stylesheet' => 'astoundify',

	// The URL of where the library is located.
	'install_url' => plugin_dir_url( $lib . '/astoundify-themecustomizer.php' ) . '/app',

	// The directory of where the library is located.
	'install_dir' => $lib,

	// The path of where panel, section, setting, and control definitions are located.
	'definitions_dir' => $lib . '/examples/definitions',
) );
