<?php
/**
 * Import an navigation menu item
 *
 * @uses Astoundify_AbstractItemImport
 * @implements Astoundify_ItemImportInterface
 *
 * @since 1.0.0
 */
class Astoundify_ItemImport_NavMenuItem extends Astoundify_AbstractItemImport implements Astoundify_ItemImportInterface {

	public function __construct( $item ) {
		parent::__construct( $item );
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function setup_actions() {
		add_action(
			'astoundify_import_content_after_import_item_type_nav-menu-item',
			array( $this, 'set_nav_menu_role' )
		);
	}

	/**
	 * Get the name of the menu to deal with
	 *
	 * @since 1.0.0
	 * @return bool|string The menu name if set, or false.
	 */
	private function get_menu_name() {
		if ( isset( $this->item['data']['menu_name'] ) ) {
			return esc_attr( $this->item['data']['menu_name'] );
		}

		return false;
	}

	/**
	 * Import a single item
	 *
	 * @return object|WP_Error Menu item object on success, WP_Error object on failure.
	 */
	public function import() {
		if ( $this->get_previous_import() ) {
			return $this->get_previously_imported_error();
		}

		$menu = wp_get_nav_menu_object( $this->get_menu_name() );

		if ( ! $menu ) {
			return $this->get_default_error();
		}

		// fill in any missing data
		$menu_item_data = $this->item['data'];
		$menu_item_data = $this->_decorate_menu_item_data( $menu_item_data );

		// create a menu item
		$result = wp_update_nav_menu_item( $menu->term_id, 0, $menu_item_data );

		if ( ! is_wp_error( $result ) ) {
			$result = wp_setup_nav_menu_item( get_post( $result ) );
		}

		return $result;
	}

	/**
	 * Reset a single object
	 *
	 * Navigation menus are always reset first and will delete all menu items.
	 *
	 * @return true
	 */
	public function reset() {
		return true;
	}

	/**
	 * Retrieve a previously imported item
	 *
	 * @since 1.0.0
	 * @uses $wpdb
	 * @return mixed Menu item ID if found or false.
	 */
	public function get_previous_import() {
		global $wpdb;

		$menu_item_data = $this->item['data'];

		if ( ! isset( $menu_item_data['menu-item-title'] ) ) {
			return false;
		}

		$menu_item = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = '%s' AND post_type = 'nav_menu_item'", $menu_item_data['menu-item-title'] ) );

		if ( null == $menu_item ) {
			return false;
		}

		return $menu_item;
	}

	private function _decorate_menu_item_data( $menu_item_data ) {
		// remove the menu name
		unset( $menu_item_data['menu_name'] );

		// set the status
		$menu_item_data['menu-item-status'] = 'publish';

		/**
		 * To set a parent we need to know the ID of the menu item we want as our ancestor.
		 * However we don't have this. So we pass `menu-item-parent-title` and use this to find
		 * the menu item object. Because of this if a menu item is going to have children it
		 * must explicitly set a its own `menu-item-title` so it can be queried against.
		 */
		if ( isset( $menu_item_data['menu-item-parent-title'] ) ) {
			global $wpdb;

			$parent_item = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = '%s' AND post_type = 'nav_menu_item'", $menu_item_data['menu-item-parent-title'] ) );

			if ( $parent_item ) {
				$menu_item_data['menu-item-parent-id'] = $parent_item->ID;
				unset( $menu_item_data['menu-item-parent-title'] );
			} else {
				unset( $menu_item_data['menu-item-parent-id'] );
				unset( $menu_item_data['menu-item-parent-title'] );
			}
		}

		/**
		 * To set a term archive we need to know the ID of the term. However, we don't have this.
		 * So we pass `menu-item-object-title` and use this to find the term object.
		 */
		if ( 'taxonomy' == $menu_item_data['menu-item-type'] && isset( $menu_item_data['menu-item-object-title'] ) ) {
			$term = get_term_by( 'name', $menu_item_data['menu-item-object-title'], $menu_item_data['menu-item-object'], 'raw' );

			if ( $term ) {
				$menu_item_data['menu-item-object-id'] = $term->term_id;
				unset( $menu_item_data['menu-item-object-title'] );
			} else {
				// set to an invalid menu so it fails early
				$menu->term_id = 0;
				unset( $menu_item_data['menu-item-object-title'] );
			}
		}

		/**
		 * To set an endpoint to a URL we need to get the original object ID url and depending on
		 * the permalink structure create a URL to set to the custom menu item.
		 */
		if ( isset( $menu_item_data['menu-item-endpoint'] ) ) {
			$menu_item_data['menu-item-type'] = 'custom';

			$base_url = get_permalink( $menu_item_data['menu-item-object-id'] );

			if ( get_option( 'permalink_structure' ) ) {
				$url = trailingslashit( $base_url ) . $menu_item_data['menu-item-endpoint'];
			} else {
				$url = add_query_arg( $menu_item_data['menu-item-endpoint'], '', $base_url );
			}

			$menu_item_data['menu-item-url'] = esc_url_raw( $url );
		}

		return $menu_item_data;
	}

	/**
	 * Set the nav menu display role.
	 *
	 * @since 1.1.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public function set_nav_menu_role() {
		$error = new WP_Error(
			'set-menu-role',
			sprintf( 'Display role for %s was not set', $this->get_id() )
		);

		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$role = false;

		if ( isset( $this->item['data']['menu-item-role'] ) ) {
			$role = $this->item['data']['menu-item-role'];
		}

		if ( ! $role || ! in_array( $role, array( 'in', 'out' ) ) ) {
			return $error;
		}

		// keep Nav Menu Role support for < 4.7
		add_post_meta( $object->ID, '_nav_menu_role', $role );

		// If Menu support.
		add_post_meta( $object->ID, 'if_menu_enable', 1, true );
		add_post_meta( $object->ID, 'if_menu_condition', 'User is logged in', true );

		if ( 'in' == $role ) {
			add_post_meta( $object->ID, 'if_menu_condition_type', 'show', true );
		} else {
			add_post_meta( $object->ID, 'if_menu_condition_type', 'hide', true );
		}
	}
}
