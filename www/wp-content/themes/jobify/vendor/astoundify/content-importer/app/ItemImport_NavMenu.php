<?php
/**
 * Import an navigation menu
 *
 * @uses Astoundify_AbstractItemImport
 * @implements Astoundify_ItemImportInterface
 *
 * @since 1.0.0
 */
class Astoundify_ItemImport_NavMenu extends Astoundify_AbstractItemImport implements Astoundify_ItemImportInterface {

	public function __construct( $item ) {
		parent::__construct( $item );
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function setup_actions() {
		// set location
		add_action(
			'astoundify_import_content_after_import_item_type_nav-menu',
			array( $this, 'set_menu_location' )
		);
	}

	/**
	 * Get the name of the menu to deal with
	 *
	 * @since 1.0.0
	 * @return bool|string The menu name if set, or false.
	 */
	private function get_menu_name() {
		if ( isset( $this->item['data']['name'] ) ) {
			return esc_attr( $this->item['data']['name'] );
		}

		return false;
	}

	/**
	 * Get the location the menu is assigned to
	 *
	 * @since 1.0.0
	 * @return bool|string The menu name if set, or false.
	 */
	private function get_menu_location() {
		if ( isset( $this->item['data']['location'] ) ) {
			return esc_attr( $this->item['data']['location'] );
		}

		return false;
	}

	/**
	 * Import a single item
	 *
	 * @since 1.0.0
	 * @return WP_Term|WP_Error WP_Term menu object on success, WP_Error on failure.
	 */
	public function import() {
		if ( $this->get_previous_import() ) {
			return $this->get_previously_imported_error();
		}

		if ( false == ( $menu_name = $this->get_menu_name() ) ) {
			return $this->get_default_error();
		}

		$result = wp_create_nav_menu( $menu_name );

		if ( ! is_wp_error( $result ) ) {
			$result = wp_get_nav_menu_object( $result );

			if ( ! $result ) {
				return $this->get_default_error();
			}
		}

		return $result;
	}

	/**
	 * Reset a single object
	 *
	 * @since 1.0.0
	 *
	 * @return int|WP_Error Menu ID on success, WP_Error on failure
	 */
	public function reset() {
		$menu = $this->get_previous_import();

		if ( ! $menu ) {
			return $this->get_not_found_error();
		}

		$result = wp_delete_nav_menu( $menu );

		// wp_delete_nav_menu() can return false instead of WP_Error
		if ( ! $result ) {
			return $this->get_default_error();
		}

		return $result;
	}

	/**
	 * Retrieve a previously imported item
	 *
	 * @since 1.0.0
	 * @uses $wpdb
	 * @return mixed Menu object if found or false.
	 */
	public function get_previous_import() {
		$menu = wp_get_nav_menu_object( $this->get_menu_name() );

		return $menu;
	}

	/**
	 * Set the location of the created menu.
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the location was set
	 */
	public function set_menu_location() {
		$error = new WP_Error(
			'set-menu-location',
			sprintf( 'Menu location %s was not set.', $this->get_id() )
		);

		// only work with a valid processed object
		$menu = $this->get_processed_item();

		if ( is_wp_error( $menu ) ) {
			return $error;
		}

		if ( false == ( $menu_location = $this->get_menu_location() ) ) {
			return $error;
		}

		$locations = get_theme_mod( 'nav_menu_locations' );
		$locations[ $menu_location ] = $menu->term_id;

		set_theme_mod( 'nav_menu_locations', $locations );

		if ( has_nav_menu( $menu_location ) ) {
			return true;
		}

		return $error;
	}

}
