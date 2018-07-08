=== WC Paid Listings ===
Contributors: mikejolley, kraftbj, donncha, jakeom
Requires at least: 4.1
Tested up to: 4.8
Stable tag: 2.8.0
License: GNU General Public License v3.0

Add paid listing functionality via WooCommerce. Create 'job packages' as products with their own price, listing duration, listing limit, and job featured status and either sell them via your store or during the job submission process.

A user's packages are shown on their account page and can be used to post future jobs if they allow more than 1 job listing.

= Documentation =

Usage instructions for this plugin can be found on the wiki: [https://wpjobmanager.com/document/woocommerce-paid-listings/](https://wpjobmanager.com/document/woocommerce-paid-listings/).

= Support Policy =

I will happily patch any confirmed bugs with this plugin, however, I will not offer support for:

1. Customisations of this plugin or any plugins it relies upon
2. Conflicts with "premium" themes from ThemeForest and similar marketplaces (due to bad practice and not being readily available to test)
3. CSS Styling (this is customisation work)
4. WooCommerce help.

If you need help with customisation you will need to find and hire a developer capable of making the changes.

== Installation ==

To install this plugin, please refer to the guide here: [http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation)

== Changelog ==

= 2.8.0 =
* Adds compatibility with WP Job Manager 1.29.0 and requires it for future updates.
* Show expiration date of paid job listing as tied to their subscription.

= 2.7.3 =
* Adds "Sign up now" button on subscription product pages.
* Fixes issue with post approval status getting reset on subscription renewal.
* Deletes user packages if order is canceled.
* WPJM products can now be featured without having to feature their attached job listings/resumes.
* Fixes issue with the deletion/restoration of user listing packages when their orders are trashed/untrashed.
* Fixes issue with updating of listing counts in standard user listing packages when the listings are trashed/untrashed.
* Fixes issue where attached job listings weren't expired when the subscription expires.
* Fixes issue with sale price field not showing up on job manager product edit views in WP Admin.
* Fixes issue with subscription fields showing up in standard job or resume package product edit views in WP Admin.
* Fixes issue with WPML where some WCPL fields weren't synced across languages and package counts would include language variations.
* Minor WooCommerce 3.x deprecated hook usage update. 

= 2.7.2 =
* Fixes issue that prevents assigning product type for Job Package Subscription products in WooCommerce 3.x.
* Migrates away from deprecated WooCommerce 3.x properties, methods, and functions.
* Updates shortcodes to use WooCommerce 3.x's new visibility terms when querying for products.
* Show short description for resume products to match job products.
* Prevents duplication of user subscription records when renewing subscription.
* Show resume fields on product edit screen only when resume plugin is active.
* Fixes issue with license notice.

= 2.7.1 =
* Fix - When relisting expired listings, keep them expired until payment.
* Dev - wcpl_admin_updated_package/wcpl_admin_created_package hooks.

= 2.7.0 =
* Require Job Manager 1.24.0.
* Require Resume Manager 1.11.0.
* Optimise the way paid listings hooks in, and add compatibility when preview step is removed.
* wc_paid_listings_decrease_package_count() function.
* Correctly deal with on-hold subscriptions.
* When reactivating subscriptions, reactivate listings.

= 2.6.2 =
* Prevent expiry date being set on new listings.

= 2.6.1 =
* Prevent listings linked to subscriptions expiring.
* Remove deprecated subs function.

= 2.6.0 =
* Feature - Supports WC subscriptions 2.0+
* Feature - Show short description for packages if set, otherwise show prices on the package selection screen.
* Tweak - Delete packages when user is deleted.
* Tweak - Use wc_get_order.
* Tweak - Filters on packages. wcpl_get_job_packages_args and wcpl_get_resume_packages_args
* Tweak - Improved messages after submission.
* Tweak - If submitting resume whilst applying for a job, thanks page will link back to job to continue with application.

= 2.5.7 =
* Tweak - Error handling.

= 2.5.6 =
* Fix - Subscriptions package check.
* Fix - Subscription renewal meta.
* Fix - is subscription check for valid product.
* Tweak - get_product -> wc_get_product.

= 2.5.5 =
* Fix - wc_paid_listings_give_user_package featured check.
* Tweak - Added handling for subscription_end_of_prepaid_term action.

= 2.5.4 =
* Update translation.

= 2.5.3 =
* Show listing link on order confirmation page.

= 2.5.2 =
* Fix tax display.
* Decrease package count when expiring a listing tied to subscription.
* wcpl_process_package_for_job_listing / wcpl_process_package_for_resume actions
* Filter jobs by package from Users > Listing Packages.
* Append package name to page title when choosing package as the first step.

= 2.5.1 =
* Fix resume form name.

= 2.5.0 =
* Job Manager 1.22.0 support
* Resume Manager 1.11.0 support
* Fix subscription handling.
* Added user package management screens to admin area.
* Renamed classes and added instances for easier modification.

= 2.4.1 =
* Fix form when cookie is missing.
* Hide subscription type when not a subscription package.

= 2.4.0 =
* Show taxes for resume packages.
* Sort packages by menu order.
* Separate option for resume paid listing flow.
* Fixed text domains.
* Fixed package expirey logic.
* Don't force reg on checkout if guest submission is off. Leave WC/the store to handle things.

= 2.3.0 =
* Feature - Per package option to link subscriptions to the listings rather than the packages (so listings are renewed and expired with the status of the subscription).
* Feature - Ability to pass 'packages' (comma separted IDs) to the submit shortcode to limit displayed packages.
* Tweaked some strings/capitialization.
* Fixed text domain.

= 2.2.1 =
* Fix get_message() -> get_error_message()

= 2.2.0 =
* Option to control package flow (before or after entering listing details).

= 2.1.3 =
* Load translation files from the WP_LANG directory.
* Updated the updater class.

= 2.1.2 =
* Fix update query.
* Uninstaller.

= 2.1.1 =
* Fixed hidden packages.

= 2.1.0 =
* Check types of package on output.
* Fix output of resume duration in admin.
* Show sections in choose package form to visually separate paid and purchased packages.

= 2.0.7 =
* Fix version error on install.
* Fix unlimited packages showing 'invalid package'.

= 2.0.6 =
* Fix tax class options display.

= 2.0.5 =
* Fix subscription package meta save.

= 2.0.4 =
* Fix subscription display for resume packages.

= 2.0.3 =
* Fix package assignment when multiple are checked out at once.

= 2.0.2 =
* Added wcpl_enable_paid_job_listing_submission filter to disable paid listings dynamically.
* Fix get_product_id()

= 2.0.1 =
* Added wcpl_job_package_is_sold_individually filter.
* Added wcpl_resume_package_is_sold_individually filter.
* Respect catalog visibility settings when outputting packages.

= 2.0.0 =
* Added support for resumes - paid resume submission and resume packages.
* Only enable paid submission when packages exist.
* Refactored package handling. wc_paid_listings_get_package() function + classes now used.
* Updated my-packages.php template.
* Updated package-selection.php template.
* New wcpl_user_packages table for both resume and job packages. Old table will be migrated on install.
* Support subscriptions for resume packages.
* Support resume manager expirey (requires 1.7+).

= 1.2.1 =
* Reset expirey date during renewal.

= 1.2.0 =
* Packages can now be valid for unlimited jobs by leaving the limit field blank.

= 1.1.1 =
* Support renewals

= 1.1.0 =
* Support WooCommerce subscriptions for packages. Require subscriptions 1.5.3.
* Fix display of add to cart button.
* Fix add to cart button text.

= 1.0.12 =
* Updated text domain
* Hide pending payment jobs from 'all' list
* Added POT file
* Disable order_paid for on-hold order status. Orders must be processing or completed.

= 1.0.11 =
* Added new updater - This requires a licence key which should be emailed to you after purchase. Past customers (via Gumroad) will also be emailed a key - if you don't recieve one, email me.

= 1.0.10 =
* Switch limit to posts_per_page in package query

= 1.0.9 =
* Use wc-paid-listings for template overrides

= 1.0.8 =
* WC 2.1 compatibility

= 1.0.7 =
* Fix job names in cart

= 1.0.6 =
* pending_payment_to_publish hook

= 1.0.5 =
* Make first user package selected

= 1.0.4 =
* Moved user packages above product packages

= 1.0.3 =
* Fixed job_packages_processed meta check

= 1.0.2 =
* Fixed approve_paid_job_listing_with_package

= 1.0.1 =
* Fix count increment

= 1.0.0 =
* First release.
