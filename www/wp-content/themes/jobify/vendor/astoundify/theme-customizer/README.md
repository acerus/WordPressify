# Astoundify Theme Customizer

Easier WordPress Customize API implementation.

## Why build this?

There generally tends to be two implementations of the WordPress Customize API.

The first is using just a few sections/settings/controls and using the vanilla Customize API. This works great up until about your 3rd control. 

The second common method is to define an array of sections/settings/controls that are then parsed and fed to the Customize API. This works pretty well but often gets carried away and ends up trying to do too much and doesn't let the Customize API handle the real grunt work.

This library tries to fall somewhere between these two implementations by providing a framework to easily bulk-register and load panels, sections, settings, controls. Alongside this a few advanced custom controls are provided as well as a way to manage external asset sources (think: fonts, icons, etc). 

## Usage

See all information in [../master/examples](../master/examples)

```php
// require the library
include_once( 'astoundify-themecustomizer/astoundify-themecustomizer.php' );

// load the customizer
astoundify_themecustomizer( array(
	// the handle of the stylesheet inline styles should be attached to
	'stylesheet' => 'astoundify', 

	// the url of where the library is located
	'install_url' => plugin_dir_url( 'astoundify-themecustomizer/astoundify-themecustomizer.php' ),

	// the dir of where the library is located
	'install_dir' => 'astoundify-themecustomizer/astoundify-themecustomizer.php',

	// the path of where definitions are located
	'definitions_dir' => get_template_directory() . '/inc/customizer/definitions'
) );
```

### Adding Controls

#### Generic

[../master/examples/definitions](../master/examples/definitions)

Drop files in `{$definitions_dir}/control-groups`, `{$definitions_dir}/panels`, `{$definitions_dir}/sections`, `{$definitions_dir}/controls` where `$definitions_dir` is the defined setting when the library was initialized.

These files will automatically load on the `customize_register` hook and can access the `$wp_customize` variable to register an item using the standard API.

__Note__: Generally settings should be registered inside control files.

```php
if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'my-setting', array(
	'default' => 'bar'
) );

$wp_customize->add_control( 'my-setting', array(
	'label' => 'My Setting',
	'section' => 'title_tagline'
) );
```

#### ControlGroup

[../master/astoundify-themecustomizer/astoundify-themecustomizer/app/control/controlgroup.php](../control/controlgroup.php)

`Astoundify_ThemeCustomizer_Control_ControlGroup` allows updating multiple controls across the customizer based on a single chosen vlaue. 
```php
if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'site-theme', array(
	'default' => 'default'
) );

$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_ControlGroup( 
	$wp_customize,
	'site-theme', 
	array(
		'label' => 'Site Theme',
		'section' => 'title_tagline',
		'priority' => 1
	)
) );
```

To define what controls the `ControlGroup` controls simply define a list inside the `{$definitions_dir}/control-groups/` directory with a filename using the setting ID (`site-theme.php`).

```php
return array(
	'default' => array(
		'title' => 'Default',
		'controls' => array(
			'site-title-color' => '#000fff',
			'site-link-color' => '#000000',
		)
	),
	'pro' => array(
		'title' => 'Pro',
		'controls' => array(
			'site-title-color' => '#eee555',
			'site-link-color' => '#222222',
		)
	)
);
```

This will create a radio list in the Customizer with options for Default and Pro. When this option toggles the defined settings will update to the assigned value.

#### Typography

[../master/astoundify-themecustomizer/astoundify-themecustomizer/app/control/typography.php](../control/typography.php)

#### Multiselect

[../master/astoundify-themecustomizer/astoundify-themecustomizer/app/control/multiselect.php](../control/multiselect.php)

#### BigChoices

[../master/astoundify-themecustomizer/astoundify-themecustomizer/app/control/bigchoices.php](../control/bigchoices.php)

`Astoundify_ThemeCustomizer_Control_BigChoices` is an enhanced `select` control using [select2](https://select2.github.io/) to create more accessible dropdowns. Useful when a list needs to be searchable, or there are a lot of options in general.

The choices are properly cached to avoid crashing the DOM when the same long list of choices appears in multiple controls.

This control is useful for defined AssetSources that often contain many options.

```php
if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'site-visitors', array(
	'default' => 500
) );

$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_BigChoices(
	$wp_customize,
	'site-visitors',
	array(
		'label' => 'How many visitors has your site had?',
		'placeholder' => 'Choose a number..',
		'choices_id' => 'numbers',
		'choices' => range(0, 1000),
		'section' => 'title_tagline'
	) 
) );
```

Defining another `Astoundify_ThemeCustomizer_Control_BigChoices` with a `choices_id` setting of `numbers` will automatically use the previously defined control's numbers and avoid over manipulation of the DOM.

### Live Preview

When a setting defines `postMessage` as the `transport` option and the setting key contains either `color`, `typography`, or `font-family` they will automatically refresh the generated CSS to provide a live preview of style changes.

```php
if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'my-color-setting', array(
	'default' => '#ffffff',
	'transport' => 'postMesage'
) );
```

### `AssetSource`

An `AssetSource` is a way to manage external asset sources that are meant to be used as choices for customize controls. Common examples are list of icons, fonts, or other arbitrary data. 

Creating a new `AssetSource` only requires defining a parse method to parse the external data. Once the data is parsed in to the expected format it can easily  be used in normal customize controls, or the provided `BigChoices` control.

#### Create the Asset Source

```php
class Custom_Icons extends Astoundify_ThemeCustomizer_AssetSource_Source implements Astoundify_ThemeCustomizer_AssetSource_SourceInterface {
	/**
	 * Parse results of the raw data find.
	 *
	 * @since 1.0.0
	 */
	public function parse() {
		$data = $this->load_raw_data( dirname( __FILE__ ) . '/icons.json' );
		if ( empty( $data ) ) {
			return;
		}
		$this->set_data( $data );
	}
}
```

Implementing `Astoundify_ThemeCustomizer_AssetSource_SourceInterface` only requires defining a parse method used to normalize the data to the expected format.

```php
Array
(
    [icon-a] => Array
        (
            [label] => Icon A
            [code] => custom-icon-a
        )

)
```

The format should be a keyed array with each element containing a `label` key. 

Support for both externally and locally stored `.json` files is included. Local `.php` files can also be set as the raw data source and parsed as pleased.

#### Use in a Control

```php
$source = 'ionicons';

$wp_customize->add_setting( 'site-icon', array(
	'default' => 'flash'
) );

$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_BigChoices(
	$wp_customize,
	'site-icon',
	array(
		'label' => 'What icon best represents your website?',
		'placeholder' => 'Choose an icon...',
		'choices_id' => 'custom-icons',
		'choices' => astoundify_themecustomizer_get_assetsource_choices( 'custom-icons' ),
		'priority' => 31,
		'section' => 'title_tagline'
	) 
) );
```

#### Included `AssetSource`s

The library comes with included `googlefonts` and `ionicons` asset sources that can be used to list all [Google Web Font](https://fonts.google.com/) choices, and [Ionicon](http://ionicons.com/) icons, respectively. 

##### Google Web Fonts

Automatically output a URL to load all `typography` font-family theme mods font selections. 

```php
wp_enqueue_style( 'theme-google-fonts', astoundify_themecustomizer_get_googlefont_url(), array( 'theme-styles' ) );
```

Create a font stack based on the theme mod value in your `output-styles`

```php
$body = astoundify_themecustomizer_get_typography_mod( 'site-font-family' );

astoundify_themecustomizer_add_css( array(
	'selectors' => 'body',
	'declarations' => array(
		'font-family' => astoundify_themecustomizer_get_font_stack( $body, 'googlefonts' )
	)
) );
```

For more information see the [#typography](`Typography`) control.

##### Ionicons

Use however you would like. The saved theme mod value is only the icon name, so creating the CSS class would be:

```html
<i class="icon ion-<?php echo esc_attr( get_theme_mod( 'my-icon-setting', 'alert' ) ); ?>"></i>
```

## Development

### Test

https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/

```
$ phpunit
```

### Update `AssetSource`s

All included asset sources keep a local copy to avoid extra churn. Use the following to update the local source data.

#### Google Web Fonts

A Google API key is required. [Get one here](https://console.developers.google.com/apis/library).

```
$ ruby tools/googlefonts.rb
```

#### Ionicons

```
$ ruby tools/ionicons.rb
```

## Changelog

### 1.2.0

**January 10, 2017**

- New: Introduce Astoundify_ThemeCustomizer_Control_ColorScheme to generate color scheme previews of a control group.
- New: Support postMessage transport on any settings with `color` or `typography`
- Fix: Update Google Font AssetSource list.

### 1.1.0

**October 31, 2016**

- New: Introduce Astoundify_ThemeCustomizer_Control_Typography control.
- New: Allow Google Web Fonts fonts and Ionicon icons to easily be loaded and utilized.

### 1.0.0

**October 21, 2016**

- Initial release.
