<?php
/**
 * Header
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />

	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<meta name="viewport" content="initial-scale=1">
	<meta name="viewport" content="width=device-width" />

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<div id="page" class="hfeed site">

		<header id="masthead" class="site-header" role="banner">
			<div class="container">

				<div class="site-header__wrap">

					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home" class="site-branding">
						<?php $header_image = get_header_image(); ?>
						<h<?php echo is_singular( 'job_listing' ) ? 2 : 1; ?> class="site-title">
							<?php if ( ! empty( $header_image ) ) : ?>
								<img src="<?php echo $header_image ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" />
							<?php endif; ?>

							<span><?php bloginfo( 'name' ); ?></span>
						</h<?php echo is_singular( 'job_listing' ) ? 2 : 1; ?>>
						<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
					</a>

					<nav id="site-navigation" class="site-primary-navigation">
						<a href="#site-navigation" class="js-primary-menu-toggle primary-menu-toggle primary-menu-toggle--close"><?php _e( 'Close', 'jobify' ); ?></a>

						<?php
							// output searchform-header.php
							add_filter( 'get_search_form', array( jobify()->template->header, 'search_form' ) );
							get_search_form();
							remove_filter( 'get_search_form', array( jobify()->template->header, 'search_form' ) );

							// output primary menu location
							wp_nav_menu( array(
								'theme_location' => 'primary',
								'menu_class' => 'nav-menu nav-menu--primary',
								'container_class' => 'nav-menu nav-menu--primary',
							) );
						?>
					</nav>
				</div>

				<a href="#site-navigation" class="js-primary-menu-toggle primary-menu-toggle primary-menu-toggle--open"><span class="screen-reader-text"><?php _e( 'Menu', 'jobify' ); ?></span></a>
			</div>
		</header><!-- #masthead -->

		<div id="main" class="site-main">
