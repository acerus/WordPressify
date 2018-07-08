=== If Menu - Visibility control for Menu Items ===
Contributors: andrei.igna
Tags: menu, visibility, rules, roles, hide, if, nav menu, show, display
Requires at least: 4
Tested up to: 4.9
Requires PHP: 5.4
Stable tag: trunk
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Display tailored menu items to each visitor with visibility rules

== Description ==

Control what menu items your site's visitors see, based on visibility rules. Here are a few examples:

* Display a menu item only if current `User is logged in`
* Hide menu items if `Device is mobile`
* Display menu items for `Admins and Editors`
* Hide Login or Register links for `Logged in Users`
* Display menu items for `Users from US and UK`
* Display menu items only for `Customers with active membership`

The plugin is easy to use, each menu item will have a new option “Change menu item visibility” which will enable the selection of rules (example in Screenshots)

## Features

* Basic set of visibility rules
  * User state `User is logged in`
  * User roles `Admin` `Editor` `Author` etc
  * Page type `Front page` `Single page` `Single post`
  * Visitor device `Is Mobile`
* Advanced visibility rules - requires Premium plan
  * Visitor location - detect visitor's Country
  * WooCommerce Subscriptions - Display menus for users with active subscription
  * WooCommerce Memberships - Display menus for customers with active membership plans
  * Groups - Detect if users are in specific groups
  * WishList Member - Detect the users' membership level
  * Restrict Content Pro - Detect the users' subscription level
* Multiple rules - mix multiple rules for a menu item visibility
  * show if `User is logged in` AND `Device is mobile`
  * show if `User is Admin` AND `Is front page`
* Support for adding your custom rules

Example of adding a new visibility rule is described in the FAQ section

== Frequently Asked Questions ==

= If Menu is broken, no visibility rules are available =

The code for modifying the menu items is limited and if other plugins/themes try to alter the menu items, this plugin will break.

This is an ongoing [issue with WordPress](http://core.trac.wordpress.org/ticket/18584) which hopefully will be fixed in a future release.

Try to use just one plugin that changes functionality for menu items.

= How can I add a custom visibility rule for menu items? =

New rules can be added by any other plugin or theme.

Example of adding a new custom rule for displaying/hiding a menu item when current page is a custom-post-type.

`
// theme's functions.php or plugin file
add_filter('if_menu_conditions', 'my_new_menu_conditions');

function my_new_menu_conditions($conditions) {
  $conditions[] = array(
    'id'        =>  'single-my-custom-post-type',                       // unique ID for the rule
    'name'      =>  __('Single my-custom-post-type', 'i18n-domain'),    // name of the rule
    'condition' =>  function($item) {                                   // callback - must return Boolean
      return is_singular('my-custom-post-type');
    }
  );

  return $conditions;
}
`

= Where can I find conditional functions? =

WordPress provides [a lot of functions](http://codex.wordpress.org/Conditional_Tags) which can be used to create custom rules for almost any combination that a theme/plugin developer can think of.

== Screenshots ==

1. Enable visibility rules for Menu Items
2. Example of visibility rules

== Changelog ==

= 0.10 - 8 May 2018 =
* Added - Visibility rule - User has Subscription Level, integration with [Restrict Content Pro](https://restrictcontentpro.com/) plugin
* Fixed - Display all WooCommerce Membership plans and save the visibility rule
* Fixed - Small render artifact in menu item title

= 0.9 - 21 April 2018 =
*This version requires PHP version to be at least 5.4*
* Added - Visibility rule - Customer has active membership, integration with [WooCommerce Memberships](https://woocommerce.com/products/woocommerce-memberships/) plugin
* Added - Visibility rule - Customer has active Job Manager Listing Subscription, integration with [Listing Payments for WP Job Manager](https://astoundify.com/products/wp-job-manager-listing-payments/) plugin
* Added - Option to disable menu item filtering in Admin panel
* Updated - Texts and notices

= 0.8.3 =
*Release Date - 22 February 2018*

* Fixed - Support for PHP <= 5.3, fixes error

= 0.8.2 =
*Release Date - 20 February 2018*

* Fixed - Support for older visibilty rule names, fixes PHP warning

= 0.8.1 =
*Release Date - 20 February 2018*

* Fixed - Better options checking, fixes PHP warning

= 0.8 =
*Release Date - 19 February 2018*

* Added - Visibility rules with multiple options. Requires Premium plan
* Added - Visibility rule - User country
* Added - Visibility rule - Is Super Admin on MultiSite
* Added - Visibility rule - User is in Group, integration with [Groups](https://wordpress.org/plugins/groups/) plugin
* Added - Visibility rule - User has subscription, integration with [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/) plugin
* Added - Visibility rule - User has active membership plan, integration with [WooCommerce Memberships](https://woocommerce.com/products/woocommerce-memberships/) plugin
* Added - Visibility rule - User membership level, integration with [WishList Member](https://member.wishlistproducts.com/) plugin
* Updated - Better conflict detection for Nav_Menu Walker
* Fixed - translation strings & function used

= 0.7 =
*Release Date - 18 September 2017*

* Enhancement - Nicer styling for visibility rules
* Added - Peek option - Let admins preview hidden menu items
* Added - Settings page

= 0.6.3 =
*Release Date - 17 August 2017*

* New visibility rule - Language Is RTL
* Fix - Single rule works on servers with Eval disabled

= 0.6.2 =
*Release Date - 8 August 2017*

* Fix - Backwards compatibility with PHP < 5.4

= 0.6.1 =
*Release Date - 7 August 2017*

* Improvement - Change labels & texts for easier use
* Improvement - Better compatibility with latest versions of WordPress
* Improvement - Better compatibility with translation plugins
* Fix - Detection for conflict with other plugins

= 0.6 =
*Release Date - 27 August 2016*

* Improvement - Dynamic conditions based on default & custom user roles (added by plugins or themes) [thanks Daniele](https://wordpress.org/support/topic/feature-request-custom-roles)
* Improvement - Grouped conditions by User, Page or other types
* Fix - Filter menu items in admin section
* Fix - Better menu items filter saving code

= 0.5 =
*Release Date - 20 August 2016*

* Improvement - Support for WordPress 4.6
* Feature - New condition checking logged in user for current site in Multi Site [requested here](https://wordpress.org/support/topic/multi-site-user-is-logged-in-conditi
on)
* Feature - Added support for multi conditions [thanks for this ideea](https://wordpress.org/support/topic/more-than-one-condition-operators-1)
* Improvement - RO & DE translations

= 0.4.1 =
*Release Date - 13 December 2015*

* Fix - Fixes [issue](https://wordpress.org/support/topic/cant-add-items-to-menu-with-plugin-enabled) with adding new menu items

= 0.4 =
*Release Date - 29 November 2015*

* Improved compatibility with other plugins/themes using a [shared action hook for menu item fields](https://core.trac.wordpress.org/ticket/18584#comment:37)
* Enhancement - show visibility status in menu item titles

= 0.3 =

Small update

* Plugin icon
* Set as compatible with WordPress 4

= 0.2.1 =

Minor fixes

* [Fix](https://twitter.com/joesegal/status/480386235249082368) - Editing menus - show/hide conditions when adding new item (thanks [Joseph Segal](https://twitter.com/joesegal))

= 0.2 =

Update for compatibility with newer versions of WordPress

* [Feature](http://wordpress.org/support/topic/new-feature-power-to-the-conditions) - access to menu item object in condition callback (thanks [BramNL](http://wordpress.org/support/profile/bramnl))
* [Fix](http://wordpress.org/support/topic/save-is-requested-before-leaving-menu-page) - alert for leaving page even if no changes were made for menus (thanks [Denny](http://wordpress.org/support/profile/ddahly))
* Fix - update method in `Walker_Nav_Menu_Edit` to be compatible with newer version of WP
* [Fix](http://wordpress.org/support/topic/bugfix-for-readmetxt) - example in Readme (thanks [BramNL](http://wordpress.org/support/profile/bramnl))

= 0.1 =
* Plugin release. Included basic menu conditional statements

== Upgrade Notice ==

= 0.9 =
Starting with If Menu v0.9, PHP version is required to be at least 5.4. Make sure the PHP version on your site is higher than this before upgrading
