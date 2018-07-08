<?php
/**
 * Output colors.
 *
 * @todo split this up.
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Page Background Color
 */
$page_background = '#' . get_background_color();

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'html'
	),
	'declarations' => array(
		'background-color' => esc_attr( $page_background ),
	),
) );

/**
 * Body Text Color
 *
 * A lot of the specific selectors are to override plugin CSS
 * or links that stick out too much (buttons, etc).
 */
$body_text_color = jobify_theme_color( 'color-body-text', '#797979' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'body',
		'input',
		'textarea',
		'select',
		'body .chosen-container-single .chosen-single span',
		'body .chosen-container-single .chosen-single div:before',
	),
	'declarations' => array(
		'color' => esc_attr( $body_text_color ),
	),
) );

/**
 * Body Link Color
 */
$body_link_color = jobify_theme_color( 'color-link', '#000000' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'a',
		'.job_listing-clickbox:hover',
	),
	'declarations' => array(
		'color' => esc_attr( $body_link_color ),
	),
) );

// darken on hover
astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'a:active',
		'a:hover',
	),
	'declarations' => array(
		'color' => astoundify_themecustomizer_darken_hex( esc_attr( $body_link_color ), -25 ),
	),
) );

/**
 * Header & Navigation
 */
$header_background = jobify_theme_color( 'color-header-background', '#ffffff' );
$header_text = maybe_hash_hex_color( get_header_textcolor() );
$navigation_text = jobify_theme_color( 'color-navigation-text', '#666666' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.site-header',
		'.nav-menu--primary .sub-menu',
	),
	'declarations' => array(
		'background' => esc_attr( $header_background ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.site-title'
	),
	'declarations' => array(
		'color' => esc_attr( $header_text ),
	),
) );

// Determine when to show the desktop menu.
$width = get_theme_mod( 'nav-menu-primary-width', 'large' );

switch ( $width ) {
	case 'large' :
		$px = 1200;
		break;
	case 'medium' :
		$px = 992;
		break;
	case 'small' :
		$px = 768;
		break;
}

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.nav-menu--primary ul li a',
		'.nav-menu--primary li a',
		'.nav-menu--primary ul li a:hover',
		'.nav-menu--primary li a:hover',
		'.primary-menu-toggle',
		'.searchform--header__submit',
		'.searchform--header__input',
	),
	'declarations' => array(
		'color' => esc_attr( $navigation_text ),
	),
	'media' => 'screen and (min-width: ' . $px . 'px)',
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.nav-menu--primary ul li.highlight > a',
		'.nav-menu--primary ul li.login > a',
	),
	'declarations' => array(
		'border-color' => esc_attr( $navigation_text ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.nav-menu--primary ul li.highlight > a:hover',
		'.nav-menu--primary ul li.login > a:hover',
	),
	'declarations' => array(
		'color' => esc_attr( $header_background ),
		'background-color' => esc_attr( $navigation_text ),
		'border-color' => esc_attr( $navigation_text ),
	),
) );

/**
 * Primary
 */
$primary = jobify_theme_color( 'color-primary', '#7dc246' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.search_jobs',
		'.search_resumes',
		'.cluster div',
		'.job-type',
		'.price-option__title',
		'.entry-header__featured-image:hover .overlay',
		'.widget_price_filter .ui-slider-horizontal .ui-slider-range',
	),
	'declarations' => array(
		'background-color' => esc_attr( $primary ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		// Tags
		'.job_filters .search_jobs .filter_by_tag a.active'
	),
	'declarations' => array(
		'color' => esc_attr( $primary ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.cluster div:after',
		'input:focus',
		'.widget_price_filter .ui-slider .ui-slider-handle',
	),
	'declarations' => array(
		'border-color' => esc_attr( $primary ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'ul.job_listings .job_listing:hover',
		'.job_position_featured',
		'li.type-resume:hover',
	),
	'declarations' => array(
		'box-shadow' => 'inset 5px 0 0 ' . esc_attr( $primary ),
	),
) );

// Buttons
astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.button',
		'input[type=button]',
		'button',
		'#submitcomment',
		'#commentform input[type=submit]',
		'.widget--footer input[type=submit]',
		'.mfp-close-btn-in .mfp-close',

		// Applications
		'input[name=wp_job_manager_send_application]',
		'input[name=wp_job_manager_edit_application]',

		// Bookmarks
		'input[name=submit_bookmark]',

		// Favorites
		'.add-favorite',
		'.update-favorite',

		// RCP
		'#rcp_submit',

		// Resumes
		'input[name=wp_job_manager_resumes_apply_with_resume]',
		'input[name=wp_job_manager_resumes_apply_with_resume_create]',

		// Contact Form 7
		'.wpcf7-submit',

		// Ninja
		'input[type=submit].ninja-forms-field',

		// Alerts
		'input[name=submit-job-alert]',

		// Hero Search
		'.hero-search .search_jobs>div input[type=submit]',
		'.hero-search .search_resumes>div input[type=submit]',
	),
	'declarations' => array(
		'background-color' => esc_attr( $primary ),
		'border-color' => 'transparent',
		'color' => '#fff',
	),
) );

// Button Hover
astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.button:hover',
		'input[type=button]:hover',
		'button:hover',
		'.job-manager-pagination a:hover',
		'.job-manager-pagination span:hover',
		'.page-numbers:hover',
		'#searchform button:hover',
		'#searchform input[type=submit]:hover',
		'#submitcomment:hover',
		'#commentform input[type=submit]:hover',
		'.page-numbers.current',
		'.widget--footer input[type=submit]:hover',
		'.mfp-close-btn-in .mfp-close:hover',

		// Applications
		'input[name=wp_job_manager_send_application]:hover',
		'input[name=wp_job_manager_edit_application]:hover',

		// Bookmarks
		'input[name=submit_bookmark]:hover',

		// Favorites
		'.add-favorite:hover',
		'.update-favorite:hover',

		// RCP
		'#rcp_submit:hover',

		// Resumes
		'input[name=wp_job_manager_resumes_apply_with_resume]:hover',
		'input[name=wp_job_manager_resumes_apply_with_resume_create]:hover',

		// Contact Form 7
		'.wpcf7-submit:hover',

		// Ninja
		'input[type=submit].ninja-forms-field:hover',

		// Alerts
		'input[name=submit-job-alert]:hover',

		// Soliloquy
		'.tp-caption .button:hover',
	),
	'declarations' => array(
		'background-color' => 'transparent',
		'color' => esc_attr( $primary ),
		'border-color' => esc_attr( $primary ),
	),
) );

// Inverted Buttons
astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.button--type-inverted',

		// Widgets
		'.widget--home-video .button',

		// WP Job Manager
		'.load_more_jobs strong',
		'.load_more_resumes strong',

		// Bookmarks
		'.job-manager-form.wp-job-manager-bookmarks-form a.bookmark-notice',

		// Favorites
		'.job-manager-form.wp-job-manager-favorites-form a.favorite-notice',
	),
	'declarations' => array(
		'color' => esc_attr( $primary ),
		'border-color' => esc_attr( $primary ),
	),
) );

// Inverted Button Hover
astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.button--type-inverted:hover',

		// Widgets
		'.widget--home-video .button:hover',

		// WP Job Manager
		'.load_more_jobs strong:hover',
		'.load_more_resumes strong:hover',

		// Bookmarks
		'.job-manager-form.wp-job-manager-bookmarks-form a.bookmark-notice:hover',

		// Favorites
		'.job-manager-form.wp-job-manager-favorites-form a.favorite-notice:hover',
	),
	'declarations' => array(
		'background-color' => esc_attr( $primary ),
		'color' => '#fff',
	),
) );

/**
 * Accent
 */
$accent = jobify_theme_color( 'color-accent', '#7dc246' );

// Special Button
astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		// Button
		'.button--type-action',
		'.button--type-secondary:hover',

		// WooCommerce
		'.single-product #content .single_add_to_cart_button',
		'.checkout-button',
		'#place_order',

		// Apply
		'input[type=button].application_button',
		'.application_button_link',
		'input[type=button].resume_contact_button',
	),
	'declarations' => array(
		'color' => esc_attr( $accent ),
		'background-color' => 'transparent',
		'border-color' => esc_attr( $accent ),
	),
) );

// Special Button Hover
astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.button--type-action:hover',
		'.button--type-secondary',

		// WooCommerce
		'.single-product #content .single_add_to_cart_button:hover',
		'.checkout-button:hover',
		'#place_order:hover',

		// Apply
		'input[type=button].application_button:hover',
		'.application_button_link:hover',
		'input[type=button].resume_contact_button:hover',
	),
	'declarations' => array(
		'background-color' => esc_attr( $accent ),
		'color' => '#ffffff',
		'border-color' => esc_attr( $accent ),
	),
) );

/**
 * White Buttons
 */
astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.button--color-white',
		'.button--color-white.button--type-inverted:hover',
		'.button--type-hover-white:hover',
	),
	'declarations' => array(
		'color' => esc_attr( $body_text_color ),
		'background-color' => '#ffffff',
		'border-color' => '#ffffff',
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.button--color-white:hover',
		'.button--color-white.button--type-inverted',
		'.button--type-hover-inverted-white:hover',
	),
	'declarations' => array(
		'background-color' => 'transparent',
		'color' => '#ffffff',
		'border-color' => '#ffffff',
	),
) );
