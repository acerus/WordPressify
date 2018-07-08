# Astoundify Content Importer

Easier content importing.

## How it Works

`.json` files are sent through various processing methods that expose WordPress
APIs to easily add information to a WordPress install. The processing adds
actions before and after each action to allow processed items to be referenced
and manipulated.

Import classes can add special processing actions depending on the information
they are importing.


## Usage

This "plugin" does not implement itself by default. The `/importer` directory
should be place instead the theme, and a bootstrap file should be created in
order to set everything up.

You can see an example in the `/example` directory.

### Supported Import Items

- [Comment](https://github.com/Astoundify/content-importer/blob/master/importer/ItemImport_Comment.php)
- [Navigation
  Menu](https://github.com/Astoundify/content-importer/blob/master/importer/ItemImport_NavMenu.php)
- [Navigation Menu
  Item](https://github.com/Astoundify/content-importer/blob/master/importer/ItemImport_NavMenuItem.php)
- [Post
  Type](https://github.com/Astoundify/content-importer/blob/master/importer/ItemImport_Object.php)
- [Setting](https://github.com/Astoundify/content-importer/blob/master/importer/ItemImport_Setting.php)
- [Term](https://github.com/Astoundify/content-importer/blob/master/importer/ItemImport_Term.php)
- [Widget](https://github.com/Astoundify/content-importer/blob/master/importer/ItemImport_Widget.php)

### Supplemented Plugins

- [Easy Digital
  Downloads](https://github.com/Astoundify/content-importer/blob/master/importer/Plugin_EasyDigitalDownloads.php)
- [Easy Digital Downloads - Frontend
  Submissions](https://github.com/Astoundify/content-importer/blob/master/importer/Plugin_FrontendSubmissions.php)
- [WP Job
  Manager](https://github.com/Astoundify/content-importer/blob/master/importer/Plugin_WPJobManager.php)
- [WP Job Manager -
  Products](https://github.com/Astoundify/content-importer/blob/master/importer/Plugin_WPJobManagerProducts.php)
- [WooCommerce](https://github.com/Astoundify/content-importer/blob/master/importer/Plugin_WooCommerce.php)
- [WooThemes
  Testimonials](https://github.com/Astoundify/content-importer/blob/master/importer/Plugin_WooThemesTestimonials.php)

### Supplemented Themes

- [Listify](https://github.com/Astoundify/content-importer/blob/master/importer/Theme_Listify.php)

## Changelog

### 1.2.1

**December 5, 2016**

- Fix: Add support for "If Menu" (http://wordpress.org/plugins/if-menu) alongside Nav Menu Roles

### 1.2.0

**November 7, 2016**

- New: Support ThemeMod imports.
- New: Support term meta.
- New: Support WP Job Manager - Resumes
- New: Downlaod images and use references of local media.

### 1.1.0

**August 1, 2016**

- New: Support WP Job Manager
- New: Support Listify
- New: Support WooCommerce
- New: Support WooCommerce Endpoint URLs
- New: Support "Navigation Menu" widget.
- New: Support Nav Menu Roles.
- New: Support object parents.
- New: Support WP Job Manager - Products
- New: Support child terms.
- New: Alert before leaving active import.

### 1.0.1

**June 2, 2016**

- Fix: Don't use return value on empty()

### 1.0.0

**May 17, 2016**

- Initial release.


