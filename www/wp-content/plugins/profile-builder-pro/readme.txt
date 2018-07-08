=== Profile Builder Pro === 

Contributors: reflectionmedia, sareiodata, cozmoslabs, madalin.ungureanu, iova.mihai, barinagabriel, adi.spiac, vadasan
Donate link: https://www.cozmoslabs.com/wordpress-profile-builder/
Tags: registration, profile, user registration, custom field registration, customize profile, user fields, builder, profile builder, custom profile, user profile, custom user profile, user profile page, 
custom registration, custom registration form, custom registration page, extra user fields, registration page, user custom fields, user listing, user login, user registration form, front-end login, 
front-end register, front-end registration, frontend edit profile, edit profileregistration, customize profile, user fields, builder, profile builder, custom fields, avatar
Requires at least: 3.1
Tested up to: 4.9.6
Stable tag: 2.8.4


Login, registration and edit profile shortcodes for the front-end. Also you can choose what fields should be displayed or add custom ones.

 
== Description ==

Profile Builder is WordPress registration done right. 

**Like this plugin?** Consider leaving a [5 star review](https://wordpress.org/support/view/plugin-reviews/profile-builder?filter=5).

It lets you customize your website by adding a Front-end menu for all your users, 
giving them a more flexible way to modify their user-information or register new users (front-end registration). 
Also, grants users with administrator rights to customize basic user fields or add custom ones. 

To achieve this, just create a new page and give it an intuitive name(i.e. Edit Profile).
Now all you need to do is add the following shortcode(for the previous example): [wppb-edit-profile]. 
Publish the page and you are done!

You can use the following shortcodes:

* **[wppb-edit-profile]** - to grant users front-end access to their personal information (requires user to be logged in).
* **[wppb-login]** - to add a front-end log-in form.
* **[wppb-register]** - to add a front-end registration form.
* **[wppb-recover-password]** - to add a password recovery form.

Users with administrator rights have access to the following features:

* add a custom stylesheet/inherit values from the current theme or use one of the following built into this plugin: default, white or black.
* select whether to display or not the admin bar in the front end for a specific user-group registered to the site.
* select which information-field can users see/modify. The hidden fields values remain unmodified.
* add custom fields to the existing ones, with several types to choose from: heading, text, textarea, select, checkbox, radio, and/or upload.
* add an avatar upload for users.
* create custom redirects
* front-end userlisting using the **[wppb-list-users]** shortcode.
* role editor: add, remove, clone and edit roles and also capabilities for these roles.

NOTE:

This plugin only adds/removes fields in the front-end. The default information-fields will still be visible(and thus modifiable) from the back-end, while custom fields will only be visible in the front-end.
	


== Installation ==

1. Upload the profile-builder folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new page and use one of the shortcodes available

== Frequently Asked Questions ==

= I navigated away from Profile Builder and now I can�t find it anymore; where is it? =
	
	Profile Builder can be found in the default menu of your WordPress installation below the �Users� menu item.

= Why do the custom WordPress fields still show up, even though I set it to be "hidden"? =

	Profile Builder only disables the default fields in the front-end of your site/blog, it does absolutely nothing in the dashboard.

= I entered the serial number I received in the confirmation e-mail, but the indicator still is red. What�s wrong? =

	The validation, as well as the update checks require an active internet connection. If you are currently working on a localhost, check your internet connection. If you, however, are on a live server and your serial number won't validate, it means that either our servers are/were down or your server blocked the validation request.

= I see that there is a heading in the Extra Profile Fields section of Profile Builder, but it isn�t displaying in the front-end, neither in the back-end. How come? =

	If you mean the default Header item "Extra Profile Fields", as long as you don't change the title, it won't show up.

= I deleted the default header from the Extra Profile Fields section, but when I refreshed the page, it was there again. Am I seeing things? =

	Luckily for you, you aren't imagining it! The plugin is designed in such way, that there must always be a header item in the list. But don't worry: your users won't see the default header.
 

= I can't find a question similar to my issue; Where can I find support? =

	For more information please visit https://www.cozmoslabs.com and check out the faq section from Profile Builder


== Screenshots ==
1. Plugin Layout (Control Panel): screenshot1.jpg
2. Show/Hide the Admin Bar (Control Panel): screenshot2.jpg
3. Default Profile Fields (Control Panel): screenshot3.jpg
4. Extra Profile Fields (Control Panel): screenshot4.jpg
5. Register Your Version (Control Panel): screenshot5.jpg
6. Edit Profile Page: screenshot6.jpg
7. Login Page: screenshot7.jpg
8. Registration Page: screenshot8.jpg
9. Customizable Userlisting (Control Panel): screenshot9.png
10.Userlisting: screenshot10.png



== Changelog ==
2.8.4
Refactored the login form. This should fix a lot of issues with wordpress.com and other incompatibilities with plugins
Fixed issue with content restriction and Woocommerce products adding extra html
Fixed issue with conditional hidden selects that were still sent in the POST
Enfold theme compatibility with upload button
Fixed issues with faceted filters and urls that have a # hash in them
Added the for and id attributes on faceted checkboxes

2.8.3
Usability improvements and some name changes
Refactored manage fields dropdown to be more user friendly
Added the GDPR field on the Edit Profile as well

2.8.2
Added GDPR checkbox default field
Fixed some warnings with Onfleek theme
More fixes for Faceted Filters selects that sometimes gave an undefined value
Added classes and ids on faceted filters in Userlisting

2.8.1
Small fixed for Faceted Filters selects that sometimes gave an undefined value
Select (CPT) is now displaying the post title when used with Faceted Filters.
Added a feedback modal on plugin deactivate for profile builder
Added 'form_name' parameter to the submit button value hook.
Fixed small typos.

2.8.0
Improved reCaptcha security on login forms
Fixed issue with 'User to edit' field and multiple edit forms on the same page
Fixed some warnings regarding the 'save_post' hook
Fixed a warning regarding 'create_function'

2.7.9
Now content restriction works on WooCommerce shop page and products
Fixed php version 7.2 warnings
Modification to the recaptcha field that will eliminate some warnings
Fixed an issue with from name not changing on default register email
Improved Userlisting search and facets to handle values with ' in them

2.7.8
Fixed issue with reCaptcha not appearing any more in some cases
Fixed a notice introduced in the last update

2.7.7
Added Invisible reCAPTCHA support for both PB forms as well as default WordPress forms
Small CSS modification in role editor
Fixing some CSS issues with notifications class on some pages and addon pages

2.7.6
Fixed some issues on the login form that prevented some users from logging in
When changing/recovering password we now log out of all other/all sessions
Increased the performance of the plugin on the frontend

2.7.5
Improved security on forms
Implemented a better plugin notification system
Improved user role field compatibility with conditional fields, now it works better if a user has multiple roles

2.7.4
You can now approve users from the admin email directly
Fixed possible notices in Userlisting on first product activation
Improved admin interface speed in most cases by up to 100% by reducing the number of ajax calls
Improved the admin interface with small visual tweaks and bug-fixes
Fixed bug with email not showing up for unconfirmed users table listing in backend

2.7.3
Now we save the registration date only in gmt time to avoid confusions. We have a filter to be able to save to local time: wppb_return_local_time_for_register
Added functionality to update meta name for existing fields in the database if they change. It is off by default but can be activated with the filter wppb_update_field_meta_key_in_db
Added a mention to Roles Editor in the Basic Info Page
Fixed Userlisting conflict with Woocommerce v3.2 that prevented the search function to work
Refactored register_date tag from Userlisting, now it returns the local time of the site
Fixed conditional mustache tags that were not working

2.7.2
Updated translation file.
Fixed issue with login token generating duplicated ID validation error
The Email Customizer User Role Label tag was working only if a single role was assigned at registration.
Fixed certain tags not appearing in the user listing due to an incorrect trim. Sometimes the last letter got chopped off

2.7.1
Fixed an issue with the Biographical Info field that was showing html tags
Fixed Content Restriction preview post before more-tag issue
Fixed Roles Editor conflict with Dokan plugin
Fixed redirect_priority='top' not working after login
Fixed back-end login with after login redirect set to http_referer
Added support for user role label in Email Customizer
Improved User Listing speed. We're now generating merge tags based on whats found in the template.

2.7.0
Added [wppb-restrict] shortcode for Content Restriction
Added an extra filter (wppb_mail) to wppb_mail function that gives the possibility to also send headers
Password Strength Indicator improvements
Updated German translation files.
Added context to the 3 wppb_mail calls so we can identify the recover password emails being sent using the filters/actions from wppb_mail.
Fixed content restriction meta-box for attachments
Fixed conditional fields based on user role on edit profile
Added Userlisting template tag for user role slug
The Select (CPT) field is now showing CPTs created with the Toolset Types plugin

2.6.9
Implemented Content Restriction feature
Fixed an issue with po file-names that was causing random issues with different environments/FTP clients.
Fixed a redirect loop when we log in from Paid Member Subscribtions and we had a redirect for default WordPress login
Added "After Registration" redirect for default WordPress register form
Added "After login" redirect for default WordPress login form
Added nonce field on Profile Builder login form for security check
Security improvements on login form
Changed the locale for the Slovenian translation files. It was using the locale for Sierra Leone.

2.6.8
Edit other users dropdown on the frontend Edit Profile form is now a select2
Fixed a potential error when submitting the Register form
Fixed issue with the Userlisting Facet Range filter not working properly on mobile
The Select(CPT) field is now a select2

2.6.7
Added option in backend user new/edit screen to add multiple user roles when user roles module is active
Added user role multiple select for admin in front-end edit profile form when roles editor is active and select role field is in the form
Changed password reset success email
Fixed issue with Avatar field that wasn't saving when the metaname contained capitalized letters
Fixed issue with Admin Approval when an admin was adding a user from the back-end the user was unapproved
Fixed issue with the Upload field that wasn't setting correctly the post author for the attachment
Fixed a javascript error with the Map Field
Fixed a notice with Admin Approval that happened in some cases

2.6.6
Added select multiple type to facet filters in Userlisting
Userlisting pagination now supports array GET parameters
Updated translation files
Added the wppb_fields_extra_css_class filter to default fields
Added actions in bulk approved and unapproved functions: wppb_after_user_approval, wppb_after_user_unapproval
Fixed Repeater Fields missing space for the group html attribute
Fixed Repeater Fields js error if limit per role was empty for certain jQuery versions

2.6.5
Fixed an issue where certain users could view the Roles Editor page without permission
Changed the strings in Recover Password accordingly with the option set in 'Allow Users to Log in With' setting
Fixed an issue with Userlisting custom field sorting arrows in table header
Fixed an issue in Userlisting Faceted filters with numeric values that were showing values instead of labels
Fixed an issue with Userlisting Faceted range filters and the value 0

2.6.4
Fixed a bug which was preventing deleting thrashed posts
Compatibility fixes with Advanced Custom Fields Plugin
Added a new parameter to the wppb_datepicker_format filter
Fixed a warning that was happening in Email Customizer because of Roles Editor
Fixed a couple of bugs with Email Customizer that some tags were empty in certain contexts

2.6.3
Fixed a small display bug for custom capabilities on Roles Editor
Fixed a potential warning with the login form and WPML when cURL was not working
Fixed a issue with role facets filters and Roles to Display setting in Userlisting

2.6.2
Added Role Editor which grants you control over roles and capabilities on your site.
We now prevent our forms from executing in the header on the wp_head hook to prevent conflicts with other plugins like Yoast SEO
Improved WPML compatibility with login forms
Now checkboxes retain their value on edit profile forms if the form errors out
Changed the way we set the default settings that was sometimes not adding them properly
Modified count on User Listing faceted filters and added a filter so we can not show the count:wppb_ul_show_filter_count
Added css classes on body when we have facets or search on User Listing

2.6.1
Updated translation files
Added a filter for already logged in message on recover password form: wppb_recover_password_already_logged_in
We now process only the submitted form so we can have multiple forms on the same page
Added extra Datepicker fields formats
Added filter on the Select(CPT) labels: wppb_fields_cpt_select_label
Added WCAG 2.0 compliance to Upload field remove link
Fixed a notice in Edit Profile forms that were missing email or username fields
We no longer execute the filter on manage fields from repeater fields more than once if not needed to significantly improve execution time
Prefixed spinner class in css to avoid conflicts
We no longer lose the values from a field on Edit Profile if we hide it with conditional fields and update the profile

= 2.6.0 =
Fixed issues with username tag in Email Customizer that was empty sometimes or sent email instead of username in certain cases
Fixed Automatic Login problem with Admin Approval on User Roles that did not take into consideration the registered user role
Fixed an issue with Conditional Fields and User Role Field
Added filter on Select Field options and labels
Serial number field is now a password field
Small change to meta name generation function that could eliminate a notice on some setups
Compatibility with WPML for login widget/shortcode error messages

= 2.5.9 =
Security improvements and audit
Fixed an issue with Search All field in Userlisting that produced a database error
Fixed an issue with Remove All Facets link in Userlisting that sometimes was not working properly
Fixed a potential error with a Profile Builder class that could be called from a WordPress hook without it beeing included and produced an error
Fixed compatibility with Captcha by Bestwebsoft plugin
Fixed a html tag in Contact Info heading description

= 2.5.7 =
Fixed Repeater fields not displaying in Userlisting
Fixed bug when Repeater Fields are required and hidden by Conditional Logic but required error message is still returned - with Multi-Step Forms
Fixed a possible error with avatar resize and folder permissions
Fixed an issue with "Display name as" field on register forms
Recover password form now doesn't appear for logged in users
Fixed a wrong variable passed to a filter in Email Confirmation

= 2.5.6 =
Compatibility fix with php 7.1
Redirects code refactoring which should fix some minor issues with redirects as well
When modifying a Datepicker field from the Backend, it was not respecting the format defined in Manage Fields
Fixed an issue with Userlisting and upload field meta tags that weren't returning the right url
Fixed an issue in a js function in userlisting.js

= 2.5.5 =
Fixed an issue with "Changed Email Address Notification" emails content type in Email Customizer
Biographical info tag used in default Userlisting template now has 3 mustaches
Added Blog Details field type, with support in Email Customizer and Userlisting
Added a new filter on Textarea field output based on meta name
Added action hooks on admin approval and unapproval
Fixed Select(CPT) field issue with Placeholder Labels Add-on
Email From Name and Subject should now display proper special characters in all cases
Fix css issue with notice image on forms taking an inherit width instead of auto
Fixed an issue with automatic login with redirect on Firefox

= 2.5.4 =
CSS changes for the Twenty Seventeen theme
Fixed a notice caused sometimes by general settings option not setting properly
Small changes to readme file
Improved Userlisting performance
Small css changes regarding facets in Userlisting
Multiple modifications to Userlisting: new filters, js modifications, code rewrites

= 2.5.3 =
Fixed a potential js error in Manage Fields and Conditional Logic
Major improvement to loading performance of the Manage Fields admin interface
Added actions before and after submit form button:wppb_form_before_submit_button and wppb_form_after_submit_button
Added a filter on the forms submit button class
Updated Dutch translations

= 2.5.2 =
Added search to Admin Approval user listing in admin area
Fixed a bug with Conditional Fields and Numbers field
Fixed an issue with faceted Userlisting filters and the & character
Removed unnecessary slash from require_once statement in Userlisting
Added a filter for CPT Select fields first option
We now handle the possibility when field meta names contain dots in field validation
We no longer show add forms or userlisting in admin bar for non admin users
Updated translation files
Added a filter to the submit button which can be used to add extra attributes: wppb_form_submit_extra_attr
Fixed a warnings inside pb-compatiblities.php file
Changed text for Email Confirmation description in admin area
Fixed a bug with the "Add field" button in Manage Fields that wasn't disabled after we added a field
Reorganized and added filters on form id and form class on hte Profile Builder forms
Removed Note message from PMS cross promotion saying that PMS does not work with admin approval / email confirmation
Modified multiple filters

= 2.5.1 =
Improvements regarding caching plugins and user registration
Added a search field in the admin area on the Users with unconfirmed email address screen
Improved queries for displaying users in the admin area on the Users with unconfirmed email address screen
Added support for Conditional Logic and Paid Member Subscriptions plans
Repeater fields limit per role now takes into account Select(Role) field and Paid Member Subscriptions plans
Fixed Upload field part of Repeaters that did not take into account allowed extensions.
Fixed and issue with Conditional fields on Multiple forms when the rule was set to any and one of the fields was not present in the form
Renamed Tabs containing mustache variables from User Listing and Email Customizer
Repeater fields limit reached css style fixes

= 2.5.0 =
Added Repeater Fields Module
We now have canonical urls for single Userlisting
Add support for single Userlisting based on ID or current user. Usage: [wppb-list-users single id='3']. An empty or lack of ID loads the current user.
On multisite we now take the avatar from the main site if it is not present on the current site
We now delete cache when updating a user with email confirmation so solve issues with cache-ing plugins
Fixed select2 JS error when select2 addon is inactive but select2 fields are still in front-end.
Fixed js issues in manage fields interface when opened multiple fields for editing: sorting was possible and it shouldn't, the first opened field disappeared, a stack limit exceeded error

= 2.4.9 =    
Security improvements and fixes
Fixed a warning that happened on older WordPress versions regarding the get_user_by() function
Login with email uses default functions now for WordPress versions higher thab 4.5 
Removed login with email when username is selected from settings
Removed sending password from default registration email
We now cleanup the user_status taxonomy for Admin Approval when deleting users
Added numbers to Admin Approval zone as well as a green check and yellow notice icons for approved/unapproved users
Fixed an issue with the Map field that displayed markers on register form for logged in admins
Fixed issue with Maps field and Conditional Logic 
Removed Validation Field empty html from Multiple Edit Profile Forms
We now save multiple select on Edit Profile even when nothing selected
Removed html field tags from Email Customizer
Fixed a warning regarding email customizer and email confirmation
Added labels for country and currency selects in Email Customizer 
Fixed issue with search all in user listing when sorting by user meta was also present

= 2.4.8 =
Added Custom Post Type Select extra field
Fixed issues with some extra fields and email confirmation
Now when an administrator registers an user the Register button text has changed to Add User
Multiple fixes regarding redirect
Fixed issues with redirect_url shortcode parameter and changed the logout shortcode parameter to redirect_url from redirect
Checkboxes and Multiple Selects are displayed with a space between values on the Userlisting now
Added the filter 'wppb_edit_other_users_dropdown_query_args' for changing the user query on the edit other users dropwdown
We now allow different fields with same name to be added in multiple registration and edit forms
Added filters for search all meta query in Userlisting
Userlisting fixes including adding display_name in search all and fix for multiple options fields in regexp expression

= 2.4.7 =
Fixed an issue with the redirect after registration autologin and string translations
Changes to Addons page to meet wp directory requirements
Fixed a bug with forms on static front pages and the username field
Fixed an issue with Automatically Login with Custom Redirects -> User Role based redirects
Fixed an error with the 'save_post' hook
Fixed an issue with faceted menus in Userlisting and multiple select

= 2.4.6 =
CSS modifications to accomodate dark/black themes
Autologin and Custom Redirects improvements and fixes
Added do shortcode on wysiwyg output in Userlisting
Check heading-tag in heading field and add a default when it's not set
Modifications to multiple select field required check to only run the function when the field is required
User role field throws errors on edit profile form if it is required and non-admin user tries to save their profile
Small changes for E-mail Confirmation and Paid Member Subscriptions compatibility

= 2.4.5 =
Add changed email address notification template in user email customizer
Fixed Userlisting issues regarding problem with wp_capabilities prefix and sorting problems
Modifications to Addons Page
Apply filter to email on all forms to allow stripping slashes if necessary
Added filter so we can bypass Email Confirmation when needed
Fixed Conditional Fields small UI issues in admin area
New menu icon and small branding changes

= 2.4.4 =
Added Number field
Added Conditional fields support
Added New branding images
Added -required- html tag to fields
Faceted filters for checkboxes now display options and not what is stored in the DB
Fixed Userlisting bugs
Created function to return field based on id or meta_name
Display Message option inside the Multiple Registration Forms module now works when you use the Automatically Login option
Renamed HTML ID for recover-password div, to avoid duplicate IDs

= 2.4.3 =
Fixed notice appearing in Email Customizer when WooCommerce is active
When login with email we remove the li for the username field now
Check for correct meta name on upload field
When user role field is required, administrators can edit their profile now.
Small code review changes
Add -required- html tag to fields if necessary.
PHP 7.0 compatibility code review
Define filter in wck-api for adding support for custom field types
Added CLASS and CSS improvements for heading field
Added possibility to specify the heading field tag (h1 to h6)

= 2.4.2 =
Edit profile double redirects after submitting changes
Fix XSS security issues

= 2.4.1 =
Security update for ajax calls

= 2.4.0 =
Now we check checkboxes default value to not be empty
Display name with email confirmation now is set to First name Last name or Nickname if they exist
Fixed UI adjustment for checkbox in admin approval and email confirmation table
Fixed hidden input padding-bottom in forms for non-admin users
Changed CSS for HTML field on front-end
Removed meta-name from HTML field
Added a third parameter to shortcode_atts in the user listing shortcode
Avatar and upload image are now cropped
Added meta name in manage fields for the Map field

= 2.3.9 =
Security update

= 2.3.8 =
Added Userlisting Advanced Facet Filters
We now use WP_User_Query for our Userlisting
Added Map field type
Added HTML field type
Added Phone field type
Fixed an issue with the currency field that saved 0  instead of empty string when no option was selected by the user
Fixed an issue with the redirect parameter for login widget
Added extra_attr filter for recover password forms: 'wppb_recover_password_extra_attr'
Added filter in select fields for placeholder labels add-on support
Fixed the cozmoslabs.com url from http to https
Fixed a weird bug with Fire Fox and Edge that won't open the media upload window because of a css rule
Added new filter admin approval: 'wppb_admin_approval_user_listing_user_object'

= 2.3.7 =
Added Time Picker field type
Added Color Picker field type
Added Validation field type
Added Currency Select field type
Added a new filter wppb_send_to_admin_email to Email Confirmation
Changed the wppb_curpageurl function to hopefully fix the missing www problem

= 2.3.6 =
Fixed a deprecated function warning in the hidden input field
Fixed a security issue regarding shortcodes
Fixed a notice in the WCK API
Fixed a compatibility issue with ACF Pro
We now make sure we call jQuery dialog only if it exists
We now have the user id as id attribute in the admin approval list table in backend to fix compatibilities with different plugins
Fixed a potential notice in Custom Redirects when trying to access the admin area when not logged out
Fixed a js error regarding the user role field and values with spaces

= 2.3.5 =
Fixed issue regarding password update not working in certain cases
Changed label for when login with username is selected
Fixed a issue regarding the password field and Admin Email customizer
Fixed small css issue regarding checkboxes labels
Fixed a problem with Userlisting templates not saving correctly

= 2.3.4 =
We now load the plugin translation from the current theme in the folder local_pb_lang if it exists otherwise normally from the plugin dir
Fixed incompatibility with the Adminize plugin and Email Customizer
Fixed notices regarding the 'add_meta_box' hook
Added a filter in wordpress-creation-kit API before testing for required fields: 'wck_before_test_required'
Added hook before saving fields: 'wppb_before_saving_form_values'

= 2.3.3 =
Added more fields to be available in wpml string translations: labels, default value and default content
Made css modifications so that Checkbox, Radio and Select fields align properly in Twenty Sixteen theme
Added new moustache variable for user count in Userlisting
Fixed different notices and warnings that appeared in certain cases

= 2.3.2 =
When upgrading from an older version than 2.2.6 on a Multisite install Email Confirmation is set to yes automatically now
Fixed notice undefined variable from wppb_mail when using filter to not send email
When updating from PB v2.1.5 to latest version Country Name is now saved correctly
Custom redirects based on User role now takes into account the field Select (User Role) on redirect after registration
Fixed filter wppb_curpageurl not being applied

= 2.3.1 =
We now display correctly the wysiwyg field in the userlisting by adding paragraphs when necessary
Fixed conflict with WPMUDEV Set Password plugin with which we had a function with the same name
Refactored username exists check to search only in username
Fixed issue when there was a meta in the db with no meta key and we couldn't add our fields that had no meta key because it would generate meta name already in use

= 2.3.0 =
We no longer crop the avatar images when we are resizing them.
Added filters in admin approval page so we can add extra columns: 'wppb_admin_approval_page_columns' and 'wppb_admin_approval_page_manage_column_data'
Changed the input type of 'Email' field to type="email"
Added filter in login form so we can display html at the bottom
Fixed a filter in login redirect link that was broken
Removed 'Display name publicly as' from Registration Forms.
We no longer create custom directories in the WordPress uploads directory.
Removed notice from Manage Fields page when the WPML  plugin was active.

= 2.2.9 =
Fixed compatibility with Yoast and Page Builder by SiteOrigin that caused our shortcodes to be executed multiple times
Admin approval screen now available in network admin for multisite as well

= 2.2.8 =
Translation updates
Changed User Registered date and time according to timezone selected in WordPress settings
Added sorting criteria by Nickname in Userlisting
Fixed issue in Admin Approval that sometimes failed to get the right user if the user didn't have an email

= 2.2.7 =
Translation updates
Fixed issue with Custom Redirects based on User Role
Custom Redirect for successful Email Confirm now works when Admin Approval is on
We now send the proper email from Email Customizer when Admin Approval on user role is active
Fixed tag {{http_referer}} in Custom Redirects on Edit Profile, Registration, Logout

= 2.2.6 =
Email Confirmation can now be disabled on WordPress multisite
We now trigger error on Custom Redirects when trying to add a duplicate entry for a user ID's associated username
Fixed Custom Redirects issue with HTTP_REFERER
Fixed notices in Login Widget and Custom Redirects
Fixed issue that blocked admin_ajax.php when using Dashboard Redirects
Fixed warning on Login Redirect because wp_signon() function returned an wp_user object and not the username

= 2.2.5 =
New and Improved Custom Redirects Module
Fixed issue that prevented  the value 0 to be set as default value
Fixed xss vulnerability
Fixed issue that was preventing to change back to original email address in edit-profile, after changing to a new one
Removed default value option from PB Password and Repeat Password fields
Fixed sorting by "User-status" in Admin Approval page
Changed name of japanese translation file
Fixed updating edit-profile form without email field, when allowing users to login only with email

= 2.2.4 =
Translation Updates

= 2.2.3 =
Fixed issue with Email Customizer for reset password that was not sending user email to administrators when loggin with email was on
Fixed a warning "Warning: Illegal offset type in isset or empty" for country select in Userlisting
Fixed website field not saving on registering with email confirmation
Fixed a potential security vulnerability
Removed condition in edit-users dropdown to allow custom ones
Replaced the comma in Userlisting checkboxes listing to comma and space

= 2.2.2 =
User Listing visibility now gets applied for all user roles selected, not only for the first user role selected
Fixed notice that was thrown in WordPress 4.3 related to WP_Widget constructor being deprecated in login widget.
Added text to inform that "Display name publicly as" only appears on the edit profile page

= 2.2.1 =
Admin Approval now is based on User Role
Changed recover password link from using username to using the user-nicename
Refactored country select field
We no longer strip spaces from usernames on singele-site registration when Email Confirmation is on and we also do not allow usernames with spaces on multisite installs
Fixed issue with reCaptch on login forms for translated sites
Fixed issues with bulk Admin Approval
fixed issue with User Role field that threw error when required even if it had a value selected
Changed message in Manage Fields sidebar
Fixed issue that prevented sometimes 0 values to be saved

= 2.2.0 =
Added support for meta names with spaces in them
Reverted the position of the Agree to terms box so it’s similar to the Send Credentials box
Fixed issue with Field Select (Timezone) that didn't save when trying to update profile
The tag {{site_name}} in Email Customizer now outputs correctly special characters

= 2.1.9 =
Fixed an issue that prevented the Captha plugin to work properly with Profile Builder and reCaptha field
Scripts no longer load if the field is not present in the form for upload,avatar,datepicker and reCaptcha
Add attribute filter to each Profile Builder form input: apply_filters( 'wppb_extra_attribute', '', $field )
Added the reset password emails in wpml-config.xml
Changed Admin Approval query for listing in the backend to take up as little resources as possible.
Added default options for Select Country and Select Timezone fields

= 2.1.8 =
Changed hook priority for upload field and small upload.js modification
Added filter to wppb_curpageurl() function to easily modify returned URL: apply_filters('wppb_curpageurl', $pageURL)
Fixed a issue with default fields not having labels and descriptions localized sometimes
Removed link to author page in logged in user shortcode
Shortcodes on Basic Info page should no longer be translated
Admin approval link is broken if WP is install in another place other then the root
Replaced home_url with site_url in login.php
Fixed a warning generated by the Recaptcha field
Fixed an error when admin was editing another user from the front end sometimes we got 'This email is already reserved to be used soon.'
Select a User to edit (as admin) adds HTML special char (&amp;) in URL when should not
Added filters that can be used to stop emails being sent to users or admins
Redirect registration form after login to same page. Also added a filter on the url
Fixed multiple reCAPTCHAs not working on the same page

= 2.1.7 =
Fixed issues regarding the new upload field
Images can be uploaded as expected in the media library
Password Strength meeter now shows up as expected

= 2.1.6 =
Rewritten the upload and avatar fields
Added reCaptcha support for default login, register and lost password forms as well as PB forms + our own login widget
Added RTL support for Profile Buider Forms and Userlisting
Fixed a problem regarding required fields
Added filter on add custom field values on user signup 'wppb_add_to_user_signup_form_meta'
Fixed issue where username was sent instead of email when Login with Email was set in the user emails
Fixed incompatibility with Easy Digital Downloads login form
Localized some strings in Userlisting

= 2.1.5 =
Now User Listing is responsive.
Updated translation files.
Bulk approve email in Email Confirmation now functions as expected
Now User Listing based on include parameter lists users in the order they were entered.
Now the Addons Page in Profile Builder is compatible with Multisite.
Added filter to add extra css classes directly on the fields input: apply_filters( 'wppb_fields_extra_css_class', '', $field )
The Show Meta button in the Email Confirmation admin screen no longer throws js errors when site in other language.
Fixed bug that was preventing Checkboxes, Selects and Radios to not save correctly if they had special chars in their values

= 2.1.4 =
Added compatibility with "Captcha" plugin
Fixed Recaptcha field issues with the file_get_contents() function
Fixed issue on Add-Ons Page that prevented addons to be activated right after install
Fixed issue on multisite where Adminstrator roles were able to edit other users from frontend
Added filters to edit other users dropdown:'wppb_display_edit_other_users_dropdown' and 'wppb_edit_profile_user_dropdown_role'

= 2.1.3 =
Fixed vulnerability regarding activating/deactivationg addons through ajax. We added nonces and permission checks.
Updated reCAPTCHA labels
Added a filter in which we can change the classes on the li element for fields: 'wppb_field_css_class'
Improvements to User Listings  search in default fields
Fixed automatic login on registration when filtering the random username generated when login with email is active
Email Customizers Password Reset Success Email now sends tje password correctly

= 2.1.2 =
Fixed bug that prevented non-administrator roles to save fields in their profile on the admin area
Backend Admin Approval link is now on the same line as view/edit/delete
Added Spanish translation
Automatically approve users created by administrators when Admin Approval is on
Styled the alerts and errors in registration/edit profile, above the forms
Added line in footer that asks users to leave a review if they enjoyed the plugin
Fixed bug in registration forms that allowed users to create accounts even when they removed the email box from the DOM
Fixed bug that was outputting wrong successful user registration message on multisite
We now can add fields from Addons that will save on user activation
Fixed bug in Email Customizer where 'From (name)' didn't work for 'Password Reset' emails
Added filter for custom fields so we don't search in them if we don't want to
We now make sure we save the resized avatar path with the correct extension
Fixed issue with getimagesize() function that threw warnings when reading remote urls on some servers
Fixed bug that was sending the hashed password in the email when Email Customizer is on
Add Slashes to dynamic JS -- brakes when using a translation'
Now WPPB_PLUGIN_DIR is pointing to the correct directory
Added User Role field in the edit profile only for admin

= 2.1.1 =
Created Add-On Page in Profile Builder
Added Email Customizer for Password Reset email
Now in Userlisting you can display the labels for checkboxes, selects and radios
Now in Email Customizer you can show the labels for checkboxes, selects and radios
Added "redirect_url" parameter to Register and Edit-profile shortcodes
We now check if $wpdb->esc_like() function exists so the plugin works below WordPress 4.0
Added support for Tweenty Fifteen theme to better target inputs
Add support for "redirect_url" parameter to Login shortcode (will do the same thing as "redirect" - for consistency)

= 2.1.0 =
Added a WYSIWYG extra field
Updated ReCAPTCHA to the new “Are you a robot?" CAPTCHA
Added {{user_nicename}} tag to userlisting
Added username validation for illegal characters
On WordPress Multisite we now check correctly for the serial status on sub-blogs
Fixed wp_mail() From headers being set sitewide
Userlisting now searches for an exact user role
Deleted second upload tag from Userlisting -> {{meta_upload_..._URL}}
Fields that have special characters in labels now display correctly in Multiple Forms (Register/Edit)
Fixed incorrect call of sprintf() function in Email Confirmation subject and message
Fixed php notice in user-role field
The {{meta_role}} tag now pulls information correctly.
User Role Select now preserve state in case of form error.

= 2.0.9 =
Fixed bug that was preventing certain fields in Userlisting and Forms settings from saving when the admin area was translated in another language.
Added User Role tag in User Email Customizer.
Added function for Log In with both Username and Email.
Set default values for Logout shortcode so it displays even if you don't pass any arguments.

= 2.0.8 =
Fixed bug that was causing the username to be sent instead of the email when login with email was set to true in the default registration emails.
Added WPML support for dynamic strings. Previously was in an custom plugin.
Fixed bug in Password Reset email when Login with email was on.
The "This email is already reserved to be used soon" error wasn't appearing on single site when Email Confirmation was on. Now it does when iti is the case.
Create a Role Select Field where users can select role at registration.
Fixed bug that was causing in admin approval email to send hashed pass. Now we send placeholder password text instead.
File extensions for upload fields are case insensitive now so you can upload a .JPG for ex.
Fixed bug that aws causing an upload incompatibility with WordPress media uploader.
Single user listing now takes into consideration the restrictions from the backend settings when displaying a user.
Fixed bug that was causing Password strength and Password length error messages to nob be translatable.
Interface changes to forms in admin area on Profile Builder Pages.
Added possibility to edit other users from the front end edit form when an admin is logged in.
Added a popup in unconfirmed email user listing in admin area where the admin can see the users meta information.
Add logout shortcode and menu link to Profile Builder.
Add CSS ID’s and classes to multiple user registration and edit profile forms so we can differentiate between them.
Add classes and css icons for sorting order (up or down) in userlisting.
Fixed CSS for Datepicker in front-end to show normal size.

= 2.0.7 =
Fixed problem that when Email Confirmation was active the password in the registration emails was empty. We now have a placeholder for when we can't send the actual password.
On multisite installs moved Register Version to Network admin screen.
Fixed bug that was causing Avatar Fields and Upload Fields not to show up when upgrading from version 1.3
Userlisting Sort by Role now works as expected
Added 'wppb_login_form_args' filter to filter wp_login_form() arguments.
Added css classes to loged in message links so we can style.
Fixed bug that was allowing us to change meta_name on un-editable fields:First Name, Last Name etc.
Fixed "Display Name Publicly as” field on front-end.
Fixed bug that was throwing required errors on "Add new" user screen even though the extra fields weren't present there.
Fixed bug that was showing the "Datepicker" field in backend smaller than normal.
Changed "Datepicker" field default year range.
Now User Email Confirmation and Admin Approval work on multisite as expected.
Fixed bug that was throwing “This email is already reserved to be used soon” ERROR on Edit Profile form on multisite.
Fixed bug that caused metaboxes and the Profile Builder page to appeared for roles that shouldn't have.
Fixed bug that was causing "About To Expire" serials to not save in Profile Builder.
Added XML file for translations of Email Customizer texts in WPML.
Removed notices from "Recaptcha" field when api keys were invalid.

= 2.0.6 =
Added Include and Exclude Users arguments to user listing shortcode
You can no longer remove meta-name attribute for extra fields inside Manage Fields
Added icon with tooltip on registration pages 'Users can register themselves or you can manually create users here' message
Fixed bug that sometimes caused custom fields meta-names to stop incrementing after 'custom_field10'
Fixed bug that sometimes caused avatar not to load in User Listing
Updated translation files
Removed some php notices from the code-base
Improved theme compatibility for the submit buttons inside the Profile Builder forms
Removed UL dots from Register form in Chrome, Safari

= 2.0.5 =
Fixed a bug with checkbox field that didn't pass the required if the value of the checkbox contained spaces
Changed default WordPress notices to something more intuitive in UserListing, Reg Forms and Edit Profile Forms modules
When email confirmation is enabled we no longer can send the selected password via email because we now store the hased password inside wp-signups table and not a encoded version of it. This was done to improve security
Fixed problem that caused in multiple registration and edit profile forms to show fuplicated fields
Fixed problem that was causing "Insert into post" image button not to work
We now use nicename to create the single user link when the filter to not create the link with id is enabled
Shortcodes inside userlisting templates can now take mustache tags as parameters
Fixed Fatal error when having both Free and Premium versions activated.
Added notification to enable user registration via Profile Builder (Anyone can register checkbox).
Added register_url and lostpassword_url parameters to login shortcode
In email customizer email to and from name now work as expected
Fixed a issue that broke the drop-down in Edit Profile forms when we had reCaptcha in Manage Fields
Fixed the sort by nickname in userlisting
Added Bulk delete fields in multiple registration and edit profile forms

= 2.0.4 =
Created filter to allow changing Lost Password link
Changed number of users per page in userlisting settings from dropdown to text input
We now set 404 page in userlisting when the user doesn't exist
Fixed some strings that weren't localized properly
Avatar image now displays the same on all browsers
Fixed bug in shortcode name that was displaying wppb-register instead of wppb-editprofile
Added $account_name as a parameter in the wppb_register_success_message filter
Fixed typo in password strength (Week instead of Weak)

= 2.0.3 =
Fixed bug that made radio buttons field types not to throw error when they are required
Fixed XSS security vulnerability in fallback-page.php
Reintroduced the filters:'wppb_generated_random_username', 'wppb_userlisting_extra_meta_email' and 'wppb_userlisting_extra_meta_user_name'
Fixed the bug when changing the password in a edit profile form we were logged out

= 2.0.2 =
Created new translation file
The "Field" dropdown in the "Add New Field to the List" metabox now only allows you to select fields that aren't in the form already
Now we can select the shortcodes for registration, edit profile and userlisting with just one click in the admin area.
Now checkbox fields with values that contain spaces save properly
Fixed "T_PAAMAYIM_NEKUDOTAYIM" error in class userlisting that appeared on php v 5.2
Fixed a couple of notices regarding email customizer and userlisting
Fixed bug that was preventing sometimes extra fields to save on registration forms
Fixed bug that when the admin area was localized sometimes some fields weren't saving correctly

= 2.0.1 =
Profile Builder Hobbyist serial now verifies correctly
Fixed bug in Registration forms and Edit Profile forms that was preventing a field save if you already had another field opened for edit
Added hooks on successful submission of edit profile and register forms
Fixed bug that was blocking in some cases requests to admin_ajax.php when WordPress Dashboard Redirect was enabled
Fix sorting order and criteria on user-listing and add support for RAND() as sorting criteria

= 2.0 =
Brand new UI.
More flexibility for Managing Default and Extra User Fields
Create Multiple Registration Forms with different fields
Create Multiple Edit Profile Forms
Setup different fields on Register and Edit Profile forms
Better Security by Enforcing Minimum Password Length and Strength on all Registration Forms
Improved User Listing
Updated English and French (thanks to Tatthieu Beucher, moyenbeuch@hotmail.com) translation files.

= 1.3.23 =
Improved some of the queries meant to select users at certain points, hidden input value on front-end (Pro version) and the remember me checkbox on the login page.

= 1.3.22 =
Fixed the checkbox listing on the single-userlisting (Pro version).

= 1.3.21 =
Fixed some bugs which only appeared in WPMU sites.

= 1.3.20 =
Fixed notice in the "Email Customizer" (pro version).

= 1.3.19 =
Small fix in the hobbyist version.

= 1.3.18 =
Added activation_url and activation_link to the "Email Customizer" feature (pro). Also, once the "Email Confirmation" feature is activated, an option will appear to select the registration page for the "Resend confirmation email" feature, which was also added to the back-end userlisting.

= 1.3.17 =
The ajax url, needed for deleting avatars/uploads was still hard-coded.

= 1.3.16 =
Minor bugfix on avatar upload fixed (back-end), and also re-done the checkbox, select and radio options/labels description.

= 1.3.15 =
Added new filters, and renamed/changed some of the old ones to adapt them for the new and improved "Email Customizer" - which was a bit buggy before.

= 1.3.14 =
Improved SQL security, and improved existing extra-fields (checkbox, radio and select) UI (they can now have different pairs of values-labels).

= 1.3.13 =
Added a few more settings field for the checkbox, radio and select. Also, few features have been improved, like the avatar resizing and displaying.

= 1.3.12 =
Minor upgrades to the plugin.

= 1.3.11 =
Improved the "Admin Approval" userlisting, added full HTTPS compatibility.

= 1.3.10 =
Improved the userlisting feature.

= 1.3.9 =
Fixed "Edit Profile" bug and impred the "Admin Approval" default listing.

= 1.3.8 =
Improved a few existing features (like "Admin Approval", required fields, WPML compatibility), and added a new feature: login with email address.

= 1.3.7 =
Fixed the rewrite rule in the userlisting, so that it is compatible with several hierarchy-leveled pages.

= 1.3.6 =
Fixed a few bugs from v1.3.5

= 1.3.5 =
Added new options for the "Userlisting" feature.
Added translations: persian (thanks to Ali Mirzaei, info@alimir.ir).

= 1.3.4 =
Improved the Email Confirmation feature.

= 1.3.3 =
Improved a few existing functions.

= 1.3.2 =
Fixed a few warnings on the register page.

= 1.3.1 =
Fixed the issue where the admin bar wouldn't display anymore once set to hidden.

= 1.3.0 =
Added the "Email Customizer" feature. Also, fixed a few existing bugs.

= 1.2.9 =
Minor security fix.

= 1.2.8 =
Email Confirmation bug on WPMU fixed.

= 1.2.7 =
Fixed reCAPTCHA compatibility issue with wp-recaptcha WP plugin.

= 1.2.6 =
Security issue fixed regarding the "Email Confirmation" feature.

= 1.2.5 =
Added a fix (suggested by http://wordpress.org/support/profile/maximinime) regarding the admin bar not displaying properly in some instances.

= 1.2.4 =
Improved localizations.

= 1.2.3 =
Minor changes to the redirect function.

= 1.2.2 =
Minor changes to the plugin's files.

= 1.2.1 =
Added support for WP 3.5.

= 1.2.0 =
Added support for Profile Builder Hobbyist.

= 1.1.59 =
Fixed CSS issue in the back-end.

= 1.1.58 =
A few bugs fixed.

= 1.1.57 =
Separated the plugins files.

= 1.1.56 =
Replaced include with include_once to stop the class already declared error. 
Changed the plugin url in the plugin headers. 

= 1.1.55 =
Minor changes to the plugin.

= 1.1.54 =
Minor changes to the plugin.

= 1.1.53 =
Minor improvements to the userlisting feature.

= 1.1.52 =
Hotfix for the registration page.

= 1.1.51 =
Added a few more specicif notifications to the registration page and WP dashboard.

= 1.1.50 =
Added a login widget for dynamic redirecting.

= 1.1.49 =
Few more notices fixed.

= 1.1.48 =
Fixed a few notices.

= 1.1.47 =
Unapproved users don't have access to the recover password feature either.

= 1.1.46 =
Fixed an issue where users couldn't sign up with the same username, even though their account has been deleted. (only present when email confirmation was activated, or on a wpmu site).

= 1.1.45 =
Fixed a warning where the uploaded avatar size couldn't be returned because of "allow_url_fopen" being turned off.

= 1.1.44 =
Fixed a few warnings and a fatal error on registration specific for users with PHP v5.4.x.

= 1.1.43 =
Fixed a few warnings.

= 1.1.42 =
Added reCAPTCHA as a permanent addon into Profile Builder.

= 1.1.41 =
A few bugfixes and security improvements.

= 1.1.40 =
Added the long-awaited "Admin Approval" feature.

= 1.1.39 =
Fixed an error when trying to edit a select/checkbox field gave a bad error message.

= 1.1.38 =
Userlisting bugfix.

= 1.1.37 =
Added email confirmation for both single site and multi-site installations, added new filters for the datepicker date format, and many more.

= 1.1.36 =
Changes made in the update function.

= 1.1.35 =
Changed path for the update handler to a more stable url.

= 1.1.34 =
Security issue fix on the recover password page.

= 1.1.33 =
Minor update.

= 1.1.32 =
Added 2 extra filters on the add/edit custom field script.

= 1.1.31 =
Added a few extra filter-parameters to the edit profile page.

= 1.1.30 =
Serial Number validation bugfix.

= 1.1.29 =
Userlisting bugfix.

= 1.1.28 =
Plugin performance and stability increased.

= 1.1.27 =
Minor addons. Compatibility fix for AIOEC plugin.

= 1.1.26 =
Plugin performance increased.

= 1.1.25 =
Minor bugfixes.

= 1.1.24 =
Minor bugfixes.

= 1.1.23 =
Minor bugfixes.
Updated English translation.

= 1.1.22 =
Added Romanian translation.

= 1.1.21 =
Customizable userlisting.

= 1.1.20 =
Userlisting query improvement.

= 1.1.19 =
Avatar bugfix.

= 1.1.18 =
Avatar bugfixes (would't display image when image was smaller then the given size)

= 1.1.17 =
Minor bugfixes and few minor features.

= 1.1.16 =
Minor bugfixes and layout/functionality modifications.

= 1.1.15 =
Minor bugfix: the accepted and declined sign didn't appear on the Register Profile Builder page within the plugin.
Added translation:
*dutch (thanks to Guido vd Leest, gjvdleest@yahoo.com)

= 1.1.14 = 
Minor bugfix on the extra fields (terms and agreement checkbox description).
Updated the .po file.

= 1.1.13 = 
Code review and minor bugfixes. Also updated the readme.txt file.
Added translation:
*polish - update to existing one (thanks to krys, krys@krys.info).

= 1.1.12 =
Minor changes to the code and improvements.

= 1.1.11 =
Avatar and JS include bugfixes.

= 1.1.10 =
Had to revert to an older version as the bugfixes produced even more bugs.

= 1.1.9 =
Minor modifications and bugfixes.

= 1.1.8 = 
Avatar bugfix (error occured sometimes when trying to delete the avatar image).

= 1.1.7 = 
Minor bugfix.

= 1.1.6 =
Added more redirect options for more control over your site/blog, and a password recovery shortcode to go with the rest of the plugin's theme. 
Also added the possibility to set both the default and the custom fields as required (only works in the front end for now), a lot of new filters for a better and easier way to personalize the plugin, and a password recovery feature (shortcode) to be in tune with the rest of the plugin.
Added translations:
*italian (thanks to Gabriele, globalwebadvices@gmail.com)
*updated the english translation

= 1.1.5 =
Minor bugfix on the registration page. The user was prompted to agree to the terms and conditions even though this was not set on the register page.
Added translations:
*czech (thanks to Martin Jurica, martin@jurica.info)
*updated the english translation

= 1.1.4 =
Added the possibility to set up the default user-role on registration; by adding the role="role_name" argument (e.g. [wppb-register role="editor"]) the role is automaticly set to all new users. Also, you can find new custom fields, like a time-zone select, a datepicker, country select etc. 
Added addons feature: 
*custom redirect url after registration/login
*added user-listing (use short-code: [wppb-list-users])
Added translations:
*norvegian (thanks to Havard Ulvin, haavard@ulvin.no)
*dutch (thanks to Pascal Frencken, pascal.frencken@dedeelgaard.nl)
*german (thanks to Simon Stich, simon@1000ff.de)
*spanish (thanks to redywebs, www.redywebs.com) 
 

= 1.1.3 =
Avatar bugfix.

= 1.1.2 =
Added translations to: 
*hungarian(thanks to Peter VIOLA, info@violapeter.hu)
*french(thanks to Sebastien CEZARD, sebastiencezard@orange.fr)

Bugfixes/enhancements:
*login page now automaticly refreshes itself after 1 second, a little less annoying than clicking the refresh button manually
*fixed bug where translation didn't load like it should
*added new user notification: the admin will now know about every new subscriber
*fixed issue where adding one or more spaces in the checkbox options list, the user can't save values.


= 1.1.1 =
Avatar bugfix where the image appeared from another account's ID

= 1.1 =
Added a new user-interface (borrowed from the awesome plugin OptionTree created by Derek Herman) and the posibility to add custom fields to the list.

= 1.0.10 =
Bugfix - The wp_update_user attempts to clear and reset cookies if it's updating the password.
 Because of that we get "headers already sent". Fixed by hooking into the init.

= 1.0.9 =
Bugfix - On the edit profile page the website field added a new http:// everytime you updated your profile.
Bugfix/ExtraFeature - Add support for shortcodes to be run in a text widget area.

= 1.0.6 =
Apparently the WordPress.org svn converts my EOL from Windows to Mac and because of that you get "The plugin does not have a valid header."

= 1.0.5 =
You can now actualy install the plugin. All because of a silly line break.

= 1.0.4 =
Still no Change.

= 1.0.3 =
No Change.

= 1.0.2 =
Small changes.

= 1.0.1 =
Changes to the ReadMe File

= 1.0 =
Added the posibility of displaying/hiding default WordPress information-fields, and to modify basic layout.
