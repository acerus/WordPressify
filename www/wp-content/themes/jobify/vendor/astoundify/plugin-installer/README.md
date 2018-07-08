# Astoundify Plugin Installer

Controlled (shiny) plugin installation and activation.

## Why build this?

TGM Plugin Activation is not very actively developed and the process remains very convoluted. It involves going through many separate screens and has limitations on how you can control that flow. Also it's not shiny.

This library lacks some useful TGMPA functionality such as admin notices, requirements, external files, and more. Instead it simply makes a portable version of the "Add New" plugin screen.

## Usage

See all information in [../blob/master/examples](../blob/master/examples)

```php
// require the library
include_once( 'astoundify-plugininstaller/astoundify-plugininstaller.php' );

// init library
astoundify_plugininstaller( array(
	// a list of wp.org plugin slugs
	'plugins' => array( 
		'react', 
		'akismet', 
		'theme-check',
		'two-factor'
	),

	// automatically activate once installed
	'forceActivate' => false,

	// translateable strings
	'l10n' => array(
		'buttonActivePlugin' => 'Active',
		'buttonErrorActivating' => 'Error',
		'activationFailed' => 'Activation failed: %s',
		'invalidPlugin' => 'Invalid plugin supplied.',
		'invalidNonce' => 'Invalid nonce supplied.',
		'invalidCap' => 'You do not have permission to install plugins on this site.',
		'activateAll' => 'Install and Activate All',
		'activateAllComplete' => 'Complete'
	),

	// the url where the library is located (probably in your theme)
	'install_url' => get_template_directory_url() . '/astoundify-plugininstaller'
) );
```

### Output Plugin List

```php
// load scripts -- should only be loaded on your page
astoundify_plugininstaller_enqueue_scripts();

// gather the plugins to be installed/activated
$plugins = astoundify_plugininstaller_get_listtable();

// output
$plugins->display();
```

### Bulk Process Button

Output a button to trigger processing for all plugins. Will trigger installation or activation depending on the current status.

```
astoundify_plugininstaller_get_activate_all_button();
```

## Known Issues

### Activation Redirects

The reason plugin activation is not currently in WordPress core alongside installation, updates, and deletions is all the funkiness that can happen on an activation. If a plugin tries to redirect or do any number of weird things it can mess up the subsequent AJAX requests.

This is worked around in Astoundify themes by faking whichever things these plugins look for to tell them to redirect. 

### Plugin Updates

Plugin updates cannot be processed outside of core-defined pages (`plugins`, `plugins-network`, `plugin-install`, or `plugin-install-network`) so the "Update Now" buttons are automatically disabled and changed to "Active" on output.

## Development

### Test

https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/

```
$ phpunit
```

## Changelog

### 1.0.1

**December 21, 2016**

- Fix: Backwards compatibility support for `get_user_locale()` and WordPress < 4.7

### 1.0.0

**December 5, 2016**

- Initial release.
