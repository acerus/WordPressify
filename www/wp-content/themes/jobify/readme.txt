=== Jobify ===
Contributors: Astoundify
Requires at least: WordPress 4.8.0
Tested up to: WordPress 4.9.6
Version: 3.9.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Tags: white, two-columns, one-column, right-sidebar, left-sidebar, fluid-layout, custom-background, custom-header, theme-options, full-width-template, featured-images, flexible-header, custom-menu, translation-ready

== Copyright ==

Jobify Theme, Copyright 2014-2016 Astoundify -
Marketify is distributed under the terms of the GNU GPL.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

The Jobify theme bundles the following third-party resources:

Bootstrap v3.0.3
Copyright 2013 Twitter, Inc
Licensed under the Apache License v2.0
http://www.apache.org/licenses/LICENSE-2.0

Slick.js v1.5.7, Copyright 2015 Ken Wheeler
Licenses: MIT/GPL2
Source: https://github.com/kenwheeler/slick/

salvattore.js Copyright (c) 2013-2015 Rolando Murillo and Giorgio Leveroni
License: MIT/GPL2
Source: https://github.com/rnmp/salvattore/

Magnific-Popup Copyright (c) 2014-2015 Dmitry Semenov (http://dimsemenov.com)
Licenses: MIT
Source: https://github.com/dimsemenov/Magnific-Popup

Ionicons icon font, Copyright (c) 2014 Drifty (http://drifty.com/)
License: MIT
Source: https://github.com/driftyco/ionicons

InfoBubble, Copyright (c) Google (http://google.com)
License: Apache 2.0
Source: http://www.apache.org/licenses/LICENSE-2.0

MarkerClusterer, Copyright (c) Google (http://google.com)
License: Apache 2.0
Source: http://www.apache.org/licenses/LICENSE-2.0

RichMarker, Copyright (c) Google (http://google.com)
License: Apache 2.0
Source: http://www.apache.org/licenses/LICENSE-2.0

== Changelog ==

To ensure there is no downtime or incompatibles with your website you should always install theme updates on a staging server first. This will allow you to make the necessary adjustments and then migrate them to a production website.

= 3.9.0: May 17, 2018 =

* New: Show company name in Job Spotlight
* Fix: Fall back to entered address is unable to format.
* Fix: Avoid PHP error on registration form.
* Fix: Update registration form styles.
* Fix: Allow company slider autorotation to be disabled.
* Fix: Hide duplicate Ninja Form buttons on job listing page.
* Fix: Do not link "Anywhere" text to Google Maps.

= 3.8.6: April 12, 2018 =

* Fix: WooCommerce 3.3.5 compatibility.
* Fix: Remove duplicate Gravity Form on single job listing page if needed.
* Fix: Avoid error when deactivating Polylang.
* Fix: Ensure asset versions are updated.

= 3.8.5: March 15, 2018 =

* New: Register customizer strings with Polylang.
* New: Add jobify_company_logo_url filter for company logo URLs.
* Fix: Indeed (and imported job) timestamp fixes.
* Fix: Remove unused files.
* Fix: Feature callout z-index ordering.

= 3.8.4: January 30, 2018 =

* New: WooCommerce 3.3.0 compatibility.
* Fix: Avoid infinite filter loop in job location.
* Fix: Update automatic theme updater.

= 3.8.3: November 3, 2017 =

* Fix: Ensure proper image size is used. Default to hard crop square thumbnail.
* Fix: Correct date on imported jobs.

= 3.8.2: October 6, 2017 =

* Fix: Avoid PHP error when activating using WP-CLI.

= 3.8.1: September 21, 2017 =

* Fix: Remove hyphens and word breaks.
* Fix: Ensure proper company logo size is used in widgets.
* Fix: Avoid error in category widget when no category is available.

= 3.8.0: August 17, 2017 =

* New: Jobs with the "URL" application method will link directly to the URL.
* New: Support for "No CAPTCHA reCAPTCHA for WooCommerce" https://wordpress.org/plugins/no-captcha-recaptcha-for-woocommerce/
* New: Allow testimonials to be randomized.
* New: Add avatar menu item. Create a menu item with the title of {{account}}
* New: WP Job Manager 1.28.0 support. Display multiple job types, JSON-LD updates.
* Fix: WooCommerce product review star appearance.
* Fix: Media/Image field toggles in customizer.
* Fix: Load proper RTL stylesheet.
* Fix: Respect WP Job Manager date settings.

= 3.7.2: July 7, 2017 =

* Fix: Ensure set Regions can appear when a location is blank.
* Fix: Ensure company name exists before using in marker popup.
* Fix: Update deprecated WooCommerce function.
* Fix: Update file-field.php for WP Job Manager compatibility.
* Fix: Update WP Job Manager - Applications styling.
* Fix: Companies We've Helped slider spacing.

= 3.7.1: May 30, 2017 =

* Fix: PHP 5.x compatibility.
* Fix: Avoid javascript error on widget.

= 3.7.0: May 17, 2017 =

* New: Add JSON-LD markup for better rich search results.
* New: Add autoplay setting to "Companies Helped" widget.
* New: Add "Slides per page" setting in "Testimonials" widget.
* New: Remove WooCommerce dependency for formatting addresses.
* Fix: Output input address if no formatted address can be created.
* Fix: "Back to Top" functionality with fixed header.
* Fix: Mobile map scrolling with two fingers.
* Fix: Show WP Editor on Pricing page template for backwards compatibility.
* Fix: Center company logos for better visual display.
* Fix: WP Job Manager - Applications "Applied" message in job spotlight.

= 3.6.1: April 12, 2017 = 

* Fix: Update setup guide instructions.

= 3.6.0: March 27, 2017 = 

* New: Selective refresh support. Easily modify your website via "Appearance ▸ Customize"
* New: Modify your website's typography via "Appearance ▸ Customize ▸ Typography"
* New: Modify your website's color scheme via "Appearance ▸ Customize ▸ Colors"
* New: Favorites support. Releasing soon. Follow us https://twitter.com/@astoundify/
* New: Listing Tags support. Releasing soon. https://twitter.com/@astoundify/
* New: WC Advanced Paid Listing support. Releasing soon. https://twitter.com/@astoundify/
* New: Notify when a page is being managed by a page template or widget area.
* New: WooCommerce 2.7 compatibility.
* Fix: Import widgets so they are immediately customizable.

= 3.5.1: December 23, 2016 = 

* Fix: Avoid PHP error in Content Importer for versions below 5.6.
* Fix: Avoid PHP error in Plugin Installer for WordPress versions below 4.7.
* Fix: Properly detect if WP Job Manager - Tags is active for setting default widgets.

= 3.5.0: December 12, 2016 = 

* New: Automatically create a child theme during content import.
* New: WooCommerce Social Login 2.0+ compatibility.
* Fix: Implement customizer compatibility and functionality.
* Fix: Avoid PHP error on Recent Posts widget.
* Fix: Enqueue Jetpack assets on submission/preview pages.
* Fix: Only use "Parallax" effect on Feature Callout widget on large devices.
* Fix: Don't plot jobs with empty/null latitudes. Fixes ZipRecruiter extension.

= 3.4.0: October 11, 2016 =

* Fix: Remove custom Polylang integration code. The plugin handles this now.
* Fix: Customizer settings for the "Extended Demo" potentially not importing with a child theme active.
* Fix: "How it Works" page content.
* Fix: WooCommerce Social Login 1.8+ compatibility.
* Fix: Update vendor Javascript libraries.
* Fix: Content importer tweaks to better match the demos.
* Fix: Avoid page jump when using a fixed header.
* Fix: Ninja Forms THREE compatibility.

= 3.3.0: August 29, 2016 =

* New: Automatic content importer: quickly and easily install demo menus, pages, widgets, settings, and more. Get up in running in minutes.
* New: Child theme creator: create a child theme while maintaining any customized options.
* New: Automatic updates: one-click automatic theme updates directly from ThemeForest.net.
* New: "Parallax" background option for Feature Callout widget.
* New: Allow page headers to be hidden via the WordPress dashboard.
* Fix: Don't register a widgetized area if the page no longer exists.
* Fix: Ninja Forms success message styling.

= 3.2.1: July 26, 2016 =

* Fix: WooCommerce 2.6.3 compatibility.

= 3.2.0: July 12, 2016 =

Google has updated their Google Maps API requirements. All websites *must* enter a valid API key.
For more information on setting up your key please visit: http://jobify.astoundify.com/article/1023-create-a-google-maps-api-key

* New: Alert if no Google Maps API key is set.
* New: Allow testimonials widget to set a background color.
* New: Redirect to submission page when coming from Plans & Pricing page.
* New: Resume Spotlight widget.
* Fix: Better UX for non-AJAX file uploads.
* Fix: Restrict Content Pro alert styling.
* Fix: Geo My WP compatibility tweaks.
* Fix: Do not load Google Maps cluster image.
* Fix: Only highlight top level menu items.
* Fix: Better UX for Applications add-on errors.
* Fix: Update TGMPA class.
* Fix: Update WP_Widget child class.

= 3.1.2: June 13, 2016 = 

* Fix: WooCommerce 2.6 compatibility.

= 3.1.1: May 1, 2016 =

* Fix: Add helper classes to force white hover on buttons.
* Fix: Remove default footer link hover.
* Fix: Job and Resume video popup width.
* Fix: JP Sharing plugin can trigger Jetpack share widget.

= 3.1.0: April 27, 2016 =

* New: Updated customizer defaults for improved visual appearance.
* New: Updated screenshot.png
* New: Set text, background, and link colors for footer widgets and copyright.
* New: Output term description on term archive pages.
* New: Option to control the height of the Search Hero widget.
* New: Minor visual updates, including WooCommerce style updates.
* Fix: LinkedIn company profile URL typo.
* Fix: GeoMyWP styles when plugin is active but page is not using custom form.
* Fix: Contact Form 7 Apply modal styles.
* Fix: WooCommerce Product searchform display.
* Fix: Modal close button opacity.
* Fix: Video embed popup for jobs.
* Fix: Use body color for chosen and other inputs.
* Fix: Search hero button color matches standard primary button.
* Fix: Add a container width to the default Text widget on the homepage.
* Fix: Integration directory paths on Windows servers.
* Fix: Better defaults for site title images and primary navigation width.

= 3.0.1: April 14, 2016 =

* Fix: Set default redirect for `login_url()` method.
* Fix: Use proper static method in Jobify_Activation class.
* Fix: File upload field displaying currently uploaded file.
* Fix: Do not remove all old theme mods on update.
* Fix: Autofit map setting.
* Fix: Loading animation when loading jobs and resumes.
* Fix: Do not add spacing to the top of the first Hero Search widget.
* Fix: WP Job Manager - Applications icons.

= 3.0.0: April 12, 2016 =

Version 3.0.0 of Jobify is a total rewrite of the theme. Please do not update directly on your production server. You should always test the update on a staging server first.

Please thoroughly review: http://jobify.astoundify.com/article/937-upgrading-to-jobify-3-0-0

This update brings both functionality and visual changes. Jobify has been updated to provide more complete support for all official WP Job Manager add-ons, as well as providing WooCommerce support.

WooCommerce 2.5+ is now required and is used to handle all account management tasks, as well as provide additional functionalities throughout the theme.

* New: Setup Guide to help you get your website set up like the demo in minutes.
* New: Minor style updates to provide more consistency and easier maintaining in the future. 
       Update icon pack to Ioniocons: http://ionicons.com/
* New: Full support for all official WP Job Manager add-ons.
* New: Full support for WooCommerce.
* New: Rewritten mobile views for a more complete experience.
* New: Set a custom address format for Jobs & Resumes in "Customize > Jobs/Resumes"
* Fix: Hundreds of stability improvements and code hardening.
* Deprecated: Simple MailChimp plugin styles.

= 2.0.9: February 15, 2016 =

* New: Add Sidekick support: https://wordpress.org/plugins/sidekick/

= 2.0.8: February 5, 2016 =

* Fix: WP Job Manager 1.24.0 compatibility.

= 2.0.7: December 8, 2015 = 

* Fix: wp_new_user_notification() instance.
* Fix: Move map controls to the top right.
* Fix: Don't override the post type supports array.

= 2.0.6: October 22, 2015 =

* Fix: wp_new_user_notification (for WP 4.3.1).
* Fix: Restrict Content Pro gateway compatibility.

= 2.0.5: August 11, 2015 =

* Fix: Restore sane verseion numbering.
* Fix: PHP5 style constructors (for WP 4.3+)
* Fix: Update TGMPA.
* Fix: WooCommerce 2.4.0+ compatibility.
* Fix: Remove Soliloquy dependency. See: http://jobify.astoundify.com/article/411-create-a-hero-slider

= 2.0.4.7: May 11, 2015 =

* Fix: Update TGM Plugin Activation to 2.4.2

= 2.0.4.6: April 21, 2015 =

* Fix: Update TGM Plugin Activation class (again).

= 2.0.4.5: April 21, 2015 =

* Fix: Update TGM Plugin Activation class.
* Fix: Escape a few instances of add_query_arg().

= 2.0.4.4: April 17, 2015 =

* Fix: Check for WC Paid Listings existence with constant instead of class name.

= 2.0.4.3: April 12, 2015 =

* Fix: WP Job Manager 1.22.0 compatibility.

= 2.0.4.2: March 16, 2015 =

* Fix: Add Chosen styles for Predefined Regions
* Fix: "Applied" notice icon and styling.
* Fix: WP Job Manager - Extended Location support. Remove "Auto City Suggest" option as it is not supported.
= 2.0.4.1: January 27, 2015 =

* Fix: Template for imported jobs.

= 2.0.4: January 16, 2015 =

* New: Pull translations from Transifex.
* New: Filters in Stats widget to existing stats can be used as a pad.
* New: Add Envato WordPress Toolkit to TGMPA.
* Fix: Respect the menu order on the pricing page template.
* Fix: Make sure the resume map height properly adjusts as well.
* Fix: Pricing table string updates.
* Fix: Log in to Bookmark style fixes.
* Fix: Gravity Forms confirmation message style fixes.
* Fix: Check WPLANG directory for translations. jobify-xx_XX.mo/po
* Fix: Add .button-white class for CTA section.

= 2.0.3.1: December 9, 2014 =

* Fix: Only adjust map height when the filters are in the map.

= 2.0.3: December 9, 2014 =

* New: Use Jetpack for sharing on jobs, resumes, and posts. Must enable which post types in "Settings > Sharing"
* Fix: Remove Javascript debug.
* Fix: Remove unnecessary hooks from Indeed template.
* Fix: Make sure the map height always resizes on all devices sizes to not block scrolling.
* Fix: Remove the top margin when using the generic slider widget as the first widget.
* Fix: Make sure long InfoBubble instances can properly scroll.

= 2.0.2.2: November 23, 2014 =

* Fix: Remove debug code from resume file widget.
* Tweak: Link location to Google Map in single job listing

= 2.0.2.1: November 12, 2014 =

* New: Page Template: "Page with Sidebar"
* New: New hooks in WP Job Manager 1.18.0
* Fix: Don't show "at" if no company name is present.
* Fix: Properly plot all listings loaded from Indeed.
* Fix: Verbage of packages with unlimited listings.
* Fix: Show "Load More Jobs" when neccesary on the homepage widget.
* Fix: Properly display resume categories when standard categories are disabled.
* Fix: Don't try and load a sourcemap that does not exist.

= 2.0.2: October 13, 2014 =

* New: Support Instagram social icon.
* New: Resume Packages Pricing page template.
* New: Adjust the map cluster grid size in the customizer.
* Fix: Properly display Job Package Subscription types.
* Fix: Avoid errors when no RCP subscriptions exist.
* Fix: Job/Resume pagination styling (numerical).
* Fix: Avoid text overflow on job type labels.
* Fix: A few responsive tweaks on resume pages.
* Fix: Make sure the map canvas is never larger than the device window.
* Fix: Slight Bookmark style tweak when logged out.
* Tweak: Reduce horizontal spacing between primary menu items.

= 2.0.1.2: September 16, 2014 =

* Fix: Make sure body_class filter isn't being overwritten.
* Fix: When categories are disabled make sure all inputs appear on the same row.
* Fix: Remove hook duplication to avoid errors using Field Editor.
* Fix: Don't load LinkedIn object if not available.
* Fix: Load Google Maps API JS protocol agnostically.
* Fix: Fix styles for Geo Job Manager (requires latest version).
* Fix: Chosen category styles on resume search.
* Fix: Map + Job Search conflicts on the same page.
* Fix: Placement of WooCommerce "Place Order" button on checkout.
* Fix: Load larger candidate photos when available to avoid blurry photos.

= 2.0.1.1: September 12, 2014 =

* Fix: If there is a Javascript error on the page still display the dropdown borders.
* Fix: Blog search submit style fixes.
* Fix: When contacting via a URL only improve modal styles.
* Fix: Add a title to the Applications modal.
* Fix: Apply with Linked In details now open in a modal.

= 2.0.1: September 10, 2014  =

* Tweak: Always load the parent theme CSS to avoid @import rules in child themes.
         If you are using a child theme and using @import to bring in the parent theme
         CSS you can remove this line.
* Fix: A few minor CSS adjustments and fixes.

= 2.0.0: September 6, 2014 =

* Note: This is a large update that introduces a few behavior and visual changes. Please read more about the changes and upgrade procedures at http://astoundify-jobify.helpscoutdocs.com/article/353-upgrading-to-version-2-0-0

* New: Set the header background color and navigation link color independently of other colors.
* New: Support region dropdowns instead of location text field (requires v1.5.0 wp-job-manager-locations)
* New: Support for video resumes and company videos (requires v1.14.0 wp-job-manager)
* New: Support for Resume Packages output on the homepage (requires v2.0.0 wp-job-manager-wc-paid-listings)
* New: Control map settings globally via the Theme Customizer.
* New: Support for multiple category searching (requires v1.14.0 wp-job-manager).
* Fix: Clicking the arrows on dropdowns now triggers the dropdown.
* Fix: Job/Resume packages will automatically stack instead of creating whitespace.
* Fix: Plot markers based off search results instead of a separate query.
* Fix: Map plotting rewrite to use standard Google Maps API libraries instead of 3rd party.
* Fix: Better/simpler WooCommerce styling.
* Tweak: Set the Information Display to "Top" to match the demos by default.
* Tweak: Split all CSS in to separate files and convert to SASS.
* Tweak: Remove many extra template file overrides that are no longer needed.
* Tweak: Update various file structures for future scalability

= 1.8.2.1: July 25, 2014 =

* Fix: Update preview handler function name so job submissions aren't broken!

= 1.8.2: July 23, 2014 =

* Note: The Gravity Forms and Ninja Forms "apply" plugins have been merged in to a new plugin that also brings support for Contact Form 7. If you update to Jobify 1.8.2 you must install "WP Job Manager - Contact Listing" and disable your current "apply plugin".

Read more here: http://docs.astoundify.com/category/76-wp-job-manager---contact-listing

* New: Support for WP Job Manager - Applications
* New: Support for WP Job Manager - Apply with LinkedIn (v2)
* New: Support for standard Text widget on the homepage.
* Fix: Make sure TGMPA doesn't exist already before using
* Fix: Update RCP Pricing table widget to match registration page and link to registration page.
* Fix: Update various file structure for future scalability

= 1.8.1.2: July 14, 2014 =

* Fix: Reponsive viewport regression.
* Fix: Introduce maximum zoom levels for the map to avoid indistinguishable results.

= 1.8.1.1: July 11, 2014 =

* Fix: Make sure Apply with LinkedIn and Bookmarks are active before trying to manipulate.

= 1.8.1: July 10, 2014 =

* New: Job location widget
* Fix: Show featured listings at the top on archive pages
* Fix: Respect hiding of products on WooCommerce shop page
* Fix: Load the correct minified Customizer Javascript
* Fix: Remove the ability to turn off the responsive design (now mobile-first only)
* Fix: Remove duplicate hooks to avoid duplicate content output
* Fix: Properly respect pin count settings on Resume + Map template
* Fix: Add new hooks and styles to stay up to date with WP Job Manager + Addons
* Fix: RCP Pricing/Register table styles and buttons
* Fix: Adjust map height on mobile devices for better scrolling
* Fix: Blurry modals on Android

= 1.8.0: May 21, 2014 =

* New: Complete rework of all responsive functionality.
* New: TGM Plugin Activation to help with new installs.
* New: "WP Job Manager - Apply with Contact Form 7" support.
* New: Separate Job Spotlight widget.
* Fix: Gravity Forms multiple file uploads not work properly.
* Fix: Geo Job Manager styling integration.
* Fix: Soliloquy stability tweaks.
* Fix: Update/add the same template hooks provided in WP Job Manager core template files.
* Tweak: Update and organize file structure, update language files, and various improvements.

= 1.7.1: March 19, 2014 =

* New: Turn off themed login by default. Can be turned on in the customizer.
* New: Apply with Resume styling (requires Resume Manager update).
* Fix: Soliloquy 2.0.0+ compatibility fixes. (You must update Soliloquy to continue using the widgets)
		- For the "Hero" slider configuration, set a width and height of 5000px.
		- For the "Content" slider configuration, set a width of 980px and height of 555px;
* Fix: More map tweaks and updates.
* Fix: Testimonials by WooThemes 1.5.1 compatibility.
* Fix: Update a few incorrect textdomains.
* Fix: Separate resume and job listing content (visually) when using the top information display setting.
* Fix: Output proper duration and fee when using RCP
* Tweak: Update and organize file structure, update language files, and various improvements.

For help and troubleshooting upgrades, please be sure to review all documentation at http://astoundify.com/documentation/jobify.

= 1.7.0: March 18, 2014 =

* New: Resume Map and Resume Map + Resume Listing page template.
* New: Mailbag (http://wordpress.org/plugins/mailbag/) support.
* New: NinjaForms (http://wordpress.org/plugins/ninja-forms/) support for applying to Resumes and Job listings. (Requires https://github.com/Astoundify/wp-job-manager-ninja-forms-apply/)
* New: Resume count in site stats widget.
* New: Control the number of markers output by map widgets via widget settings.
* New: Job expiry widget (requires Job Application Deadline addon)
* Fix: Don't output markers with no location to avoid map errors.
* Fix: When more than one listing are in the same location make the cluster clickable to show all listings.
* Fix: Avoid conflict with Gravity Forms when submitting a resume. Enable Gravity Forms AJAX support.
* Fix: Save form values when registering an account with errors.
* Fix: Don't redirect to WordPress login form during a failed login attempt.
* Fix: Make sure the WordPress admin bar responds alongside the theme.
* Fix: Various string context and spelling fixes.
* Fix: Show a loading icon when job listings and resumes are loading.
* Tweak: Update and organize file structure, update language files, various improvements.

For help and troubleshooting upgrades, please be sure to review all documentation at http://astoundify.com/documentation/jobify.

= 1.6.2: February 27, 2014 =

* Fix: Use the Jobify login page for the Resume Manager link.
* Fix: Contact Resume permissions popup.
* Fix: Form errors use proper width.
* Fix: Make sure registration form is loaded in the correct order.
* Fix: LinkedIn field output display issues.
* Fix: target="_blank" on application URLs

= 1.6.1: February 26, 2014 =

* Fix: Make sure widget files are loaded properly to avoid fatal errors.
* Fix: Add Candidate Skill archives
* Fix: Only show candidate file if it's filled out.

= 1.6.0: February 25, 2014 =

** Note: You may need to remove your Map widget and add it back if the pins do not appear.

* New: Widgetized job listing/resume sidebars.
* New: Restrict job listing/resume widgets based on RCP subscription level.
* New: Alternate job listing/resume widget layout at the top of the page.
* New: Blog/Single standard sidebar.
* New: Map improvements: Lighter-weight libraries, marker clusters, widget options, and only load when necessary.
* New: Generic "Slider" widget that any slider shortcode can be placed in.
* New: Use standard sharing buttons for sharing content.
* New: Company LinkedIn field.
* New: Show RCP discounts on pricing options.
* Fix: Update Magnificant Popup CSS to fix Android display bugs.
* Fix: Use WordPress's default role when registering as an "employer"
* Fix: Make sure the logo looks good at larger sizes.
* Fix: Make sure the job spotlight collapses when needed.
* Fix: Make sure template files respect capabilities for resume viewing/contacting.
* Fix: Use the "Primary" color for the RCP pricing table.
* Fix: Make sure the map category dropdown uses the same order as the filter.
* Fix: Make previewing a job listing much more accurate.
* Fix: Update screenshot.png
* Tweak: Update and organize file structure, introduce Bootstrap grid system, and Grunt

= 1.5.3: February 6, 2014 =

* New: Ensure compatibility with Indeed job listing addon.
* Fix: Class independence from the Gravity Forms Apply plugin. Check for existence of options.
* Fix: Typo in registration form.
* Fix: Update Entypo Icon Font to avoid conflict with Chrome 32.
* Fix: Make sure there is no extra whitespace on mobile browsers.
* Fix: Make sure resume category filters display properly.

= 1.5.2: January 29, 2014 =

* Fix: Make sure Gravity Forms load properly.
* Fix: Make sure roles are assigned properly.
* Tweak: Various tweaks and improvements for a more stable experience.

= 1.5.1: January 23, 2014 =

* New: Select role when registering if using Resume Manager
* Fix: Avoid testimonial archives breaking when not using the widget.
* Fix: Make sure all taxonomy archives load properly.

= 1.5: January 20, 2014 =

* New: Support for Resumes Addon
* New: Support for 3rd-level dropdowns.
* New: Add LinkedIn to share popup.
* New: On the Map + Jobs Page template hide the map search and update when searching through the Jobs table.
* Fix: Various addon support improvements.
* Tweak: Various tweaks and improvements for a more stable experience.

= 1.4.3: December 11, 2013 =

* Fix: Avoid errors with the map widget.
* Fix: Show user packages first (to stay consistent with WP Job Manager)
* Fix: Make sure addons can display extra information in content-single-job.php

= 1.4.2: November 17, 2013 =

* Fix: When a job listing is updated clear the location cache just in case.
* Fix: Map javascript error that may cause the search from not showing.
* Fix: Make sure the map widget zoom levels show in the correct order.
* Tweak: RCP style compatability.
* Tweak: Change the animation on modals.
* Tweak: Change the animation on site stats widget.
* Tweak: Remove the "Load More Jobs" when using the jobs widget on the homepage.
* Tweak: Various tweaks and improvements for a more stable experience.

= 1.4.1: October 19, 2013 =

* New: Map + Jobs Page Template
* Fix: Related jobs are now based on categories which are standard.
* Fix: Better support for browsers on the mobile menu.
* Fix: Don't crop company testimonial logos.
* Fix: Various tweaks and improvements for a more stable experience.

= 1.4: September 14, 2013 =

* New: Support for WP Job Manager WooCommerce Paid Listings (http://mikejolley.com/projects/wp-job-manager/add-ons/woocommerce-paid-listings/)
* New: Styling for WP Job Manager Alerts (http://mikejolley.com/projects/wp-job-manager/add-ons/job-alerts/)
* New: Related Jobs below single job view.
* Fix: Filter WP Job Manager to use the login shortcode page, not standard WordPress
* Fix: When using RCP, allow the proper HTML to go through the description.
* Fix: Various tweaks and improvements for a more stable experience.

= 1.3: August 25, 2013 =

* New: Footer social menu items are now controlled through a custom menu. Instead of links in the Customizer, assign a menu to the footer, and add custom social links.
* New: jQuery Animations can be toggled on/off for certain widgets. You may need to resave their options.
* Fix: Various tweaks and improvements for a more stable experience.

= 1.2: August 15, 2013 =

* New: Support for Apply with Gravity Forms for WP Job Manager
* Fix: Various other tweaks and improvements.

= 1.1: July 30, 2013 =

* New: Add support for WP Job Manager Company Profiles (https://github.com/Astoundify/wp-job-manager-companies)
* New: Add a search button the the job filters (can still search on enter or unfocus)
* Fix: Properly save custom extra custom fields on frontend submission.

= 1.0: July 25, 2013 =

First release!
