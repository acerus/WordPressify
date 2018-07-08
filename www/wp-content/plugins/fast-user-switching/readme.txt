=== Fast User Switching ===
Contributors: kasperta
Tags: user, switching, switch, users, admin, bar, top, fast, roles, impersonate, authentication, tikweb
Donate link: http://www.tikweb.dk/donate/
Requires at least: 4.6
Tested up to: 4.9.4
Requires PHP: 5.2
Stable tag: 1.4.7

Fast user switching between users and roles directly from the admin bar - switch from a list or search for users/roles by id, username, mail etc.

== Description ==

Fast user switching between users and roles directly from the admin bar or the user list - switch from a list or search for users/roles by id, username, mail etc.

Use settings to select roles that are allowed to switch user. By default only administrators can switch user.

To return to your own user, just log out. A log out link is available in the black top menu, top right, profile submenu.

When you impersonate a user, you will be effectively logged in as that user, and acquire the same rights - very convenient for testing rights for users. Also practical for consultants, bureaus and copy-writers who need to create and edit content for customers.

== Installation ==

1. Upload the 'fast-user-switching' folder to the '/wp-content/plugins/' directory
2. Browse to your WordPress admin control panel, and activate the plugin through the 'Plugins' menu
3. Go to the 'Users' list and press Impersonate.

== Frequently Asked Questions ==

= There is no Impersonate link in the Users list =
Only administrators can see the link - or other users who have the "add_users" capability added (only admins by default).

= How do i get back to my own login? =
Log out and you are back, the plugin remembers your original login, and returns you to your usual login.

== Screenshots ==

1. All Users Page
2. Switch back to old user
3. Recent impersonate user list

== Changelog ==

= 1.4.7 - 2018-06-12 =
* Fixed the CSS issue in the search dropdown that shows transparent text.

[Changelog](https://plugins.svn.wordpress.org/fast-user-switching/trunk/changelog.txt)

== Upgrade Notice ==

= x.0.0 =
* There are nothing else needed, than upgrading from the WordPress plugins screen.
