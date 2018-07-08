<?php
/**
 * Basic example of using the library.
 *
 * Creates a submenu page under "Settings" that allows plugins to be installed/activated
 * on the same page.
 *
 * @since 1.0.0
 */

// require the library
// include_once( dirname( dirname( __FILE__ ) ) . '/astoundify-plugininstaller.php' );
// init library
astoundify_plugininstaller( array(
	// a list of wp.org plugin slugs
	'plugins' => array(
		'react',
		'akismet',
		'theme-check',
		'two-factor',
	),

	// automatically activate once installed
	'forceActivate' => true,

	// translateable strings
	'l10n' => array(
		'buttonActivePlugin' => 'Active',
		'buttonErrorActivating' => 'Error',
		'buttonUpdate' => 'Update',
		'activationFailed' => 'Activation failed: %s',
		'invalidPlugin' => 'Invalid plugin supplied.',
		'invalidNonce' => 'Invalid nonce supplied.',
		'invalidCap' => 'You do not have permission to install plugins on this site.',
	),

	// the url where the library is located
	'install_url' => plugin_dir_url( dirname( __FILE__ ) ),
) );

/**
 * Load and init the library + create the admin menu item.
 *
 * @since 1.0.0
 */
add_action( 'admin_menu', function() {
	add_options_page( 'Install Plugins', 'Install Plugins', 'manage_options', 'astoundify-plugininstaller', 'page_output' );
} );

/**
 * Output scripts.
 *
 * This needs to happen after the library has been initialized.
 *
 * @since 1.0.0
 */
add_action( 'admin_enqueue_scripts', function() {
	$screen = get_current_screen();

	if ( 'settings_page_astoundify-plugininstaller' != $screen->id ) {
		return;
	}

	astoundify_plugininstaller_enqueue_scripts();
} );

/**
 * Output the page
 *
 * @since 1.0.0
 */
function page_output() {
	$plugins = astoundify_plugininstaller_get_listtable();
?>

<div class="wrap">
	<h1>Install Plugins</h1>
	<p><?php astoundify_plugininstaller_activate_all_button(); ?></p>
	<?php $plugins->display(); ?>
</div>

<?php
}
