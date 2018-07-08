If Menu
=========

**If Menu** is a WordPress plugin which adds extra functionality for menu items, making it easy to hide or display menu items based on user-defined rules. Example:

* Display a menu item only if current `User is logged in`
* Hide menu items if `visiting from mobile device`
* Display menu items just for `Admins and Editors`

The plugin is easy to use, each menu item will have a “Change menu item visibility” option which will enable the selection of rules.

> This repo is used only for development, downloading & installing from here won't work as expected. Install from [WordPress.org plugin page](https://wordpress.org/plugins/if-menu/)


## Features

* Basic set of visibility rules
  * User state `User is logged in`
  * User roles `Admin` `Editor` `Author` etc
  * Page type `Front page` `Single page` `Single post`
  * Device `Is Mobile`
  * Language `Is RTL`
* Multiple rules - mix multiple rules for a menu item visibility
  * show if `User is logged in` AND `Device is mobile`
  * show if `User is Admin` AND `Is front page`
* Support for adding your custom rules



## Adding custom visibility rules in a plugin or theme

Custom visibility rules can be added easily by any plugin or theme.
Example of adding a new rule for displaying/hiding a menu item when current page is a custom-post-type.

```
// theme's functions.php or plugin file
add_filter('if_menu_conditions', 'my_new_menu_conditions');

function my_new_menu_conditions($conditions) {

  $conditions[] = array(
    'id'        =>  'single-my-custom-post-type',           // unique ID for the condition
    'name'      =>  __('Single my-CPT', 'i18n-domain'),     // name of the condition
    'condition' =>  function($item) {                       // callback - must return Boolean
      return is_singular('my-custom-post-type');
    }
  );

  return $conditions;
}
```



## WordPress.org

Head over to [plugin's page on WordPress.org](https://wordpress.org/plugins/if-menu/) for more info, reviews and support
