<?php
/**
 * Navigation Setup.
 *
 * @since unknown
 */
class Jobify_Template_Navigation {

	/**
	 * Constructor.
	 *
	 * @since unknown
	 */
	public function __construct() {

		// Always show home link.
		add_filter( 'wp_page_menu_args', array( $this, 'always_show_home' ) );

		// Pop Up Trigger CSS class.
		add_filter( 'nav_menu_css_class', array( $this, 'popup_trigger_class' ), 10, 3 );

		// Avatar Menu Item.
		add_filter( 'walker_nav_menu_start_el', array( $this, 'avatar_item' ), 10, 4 );
		add_filter( 'nav_menu_css_class', array( $this, 'avatar_item_class' ), 10, 3 );
	}

	/**
	 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
	 *
	 * @since unknown
	 *
	 * @param array $args WP Page Menu Args.
	 * @return array
	 */
	public function always_show_home( $args ) {
		$args['show_home'] = true;

		return $args;
	}

	/**
	 * Pop Up Trigger Class.
	 *
	 * @since unknown
	 *
	 * @param array  $classes Nav menu CSS class.
	 * @param object $item    WP_Post.
	 * @param object $args    An object of wp_nav_menu() arguments.
	 */
	public function popup_trigger_class( $classes, $item, $args ) {
		// Check if item class contain "login", "register", and/or "popup".
		$popup = array_intersect( array( 'login', 'register', 'popup' ), $classes );

		// Class do not contain the target classes. 
		if ( false === $popup || empty( $popup ) ) {
			remove_filter( 'nav_menu_link_attributes', array( $this, 'popup_trigger_attributes' ), 10, 3 );

			return $classes;
		} else { // Class contain the target classes.
			foreach ( $popup as $key ) {
				unset( $classes[ $key ] );
			}

			add_filter( 'nav_menu_link_attributes', array( $this, 'popup_trigger_attributes' ), 10, 3 );
		}

		return $classes;
	}

	/**
	 * Pop Up Menu HTML attributes.
	 *
	 * @since unknown
	 *
	 * @param array  $atts The HTML attributes.
	 * @param object $item Nav Menu Item.
	 * @param array  $atts An object of wp_nav_menu() arguments.
	 * @return array
	 */
	public function popup_trigger_attributes( $atts, $item, $args ) {
		$atts['class'] = 'popup-trigger-ajax';

		if ( in_array( 'popup-wide', $item->classes ) ) {
			$atts['class'] .= ' modal-wide';
		}

		return $atts;
	}

	/**
	 * Custom Account menu item.
	 *
	 * Look for a menu item with a title of `{{account}}` and replace the
	 * content with information about the current account.
	 *
	 * @since 3.8.0
	 *
	 * @param string $item_output Current item output.
	 * @param object $item Current item.
	 * @param int    $depth Current depth.
	 * @param array  $args Nav menu item arguments.
	 * @return string $item_output
	 */
	public function avatar_item( $item_output, $item, $depth, $args ) {
		if ( '{{account}}' !== $item->title ) {
			return $item_output;
		}

		$user = wp_get_current_user();

		if ( ! is_user_logged_in() ) {
			$display_name = apply_filters( 'jobify_account_menu_guest_label', __( 'Guest', 'jobify' ) );

			$avatar = '';
		} else {
			if ( $user->first_name ) {
				$display_name = $user->first_name;
			} else {
				$display_name = $user->display_name;
			}

			$display_name = apply_filters( 'jobify_acount_menu_user_label', $display_name, $user );
			$display_name = '<span class="display-name">' . $display_name . '</span>';

			$avatar =
				'<div class="current-account-avatar" data-href="' . esc_url( apply_filters( 'jobify_avatar_menu_link', get_author_posts_url( $user->ID, $user->user_nicename ) ) ) .
				'">' .
				get_avatar( $user->ID, 90 )
				. '</div>';
		}

		$item_output = str_replace( '{{account}}', $avatar . $display_name, $item_output );

		return $item_output;
	}

	/**
	 * If the menu item has the `{{account}}` tag add a custom class to the item.
	 *
	 * @since 3.8.0
	 *
	 * @param array  $classes Current nav item classes.
	 * @param object $item Current nav item.
	 * @param array  $args Arguments.
	 * @return array $classes
	 */
	public function avatar_item_class( $classes, $item, $args ) {
		if ( 'primary' !== $args->theme_location ) {
			return $classes;
		}

		if ( '{{account}}' !== $item->title || ! is_user_logged_in() ) {
			return $classes;
		}

		$classes[] = 'account-avatar';

		return $classes;
	}

}
