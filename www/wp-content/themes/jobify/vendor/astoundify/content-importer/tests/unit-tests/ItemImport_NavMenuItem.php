<?php
/**
 * Item Import Nav Menu Item
 *
 * This is somewhat hard to test against because the `wp_update_nav_menu_item()` function is missing
 * a lot of error checking.
 *
 * @since 1.0.0
 */
class Test_ItemImport_NavMenuItem extends WP_UnitTestCase {

	public function test_import_ItemImport_NavMenuItem_returns_wp_error_with_invalid_menu() {
		$data = array(
			'id' => 'custom-menu-link',
			'type' => 'nav-menu-item',
			'data' => array(
				'menu_name' => 'Primary', // this menu does not exist
				'menu-item-url' => 'http://test.com',
				'menu-item-title' => 'Custom Menu Item',
				'menu-item-type' => 'custom',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenuItem( $data );
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_NavMenuItem_returns_wp_error_with_previous_import() {
		wp_create_nav_menu( 'Primary' );

		$data = array(
			'id' => 'custom-menu-link',
			'type' => 'nav-menu-item',
			'data' => array(
				'menu_name' => 'Primary',
				'menu-item-url' => 'http://test.com',
				'menu-item-title' => 'Custom Menu Item',
				'menu-item-type' => 'custom',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenuItem( $data );
		$item = $item_import->import();
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_NavMenuItem_returns_decorated_wp_post_with_valid_custom_url_data() {
		wp_create_nav_menu( 'Primary' );

		$data = array(
			'id' => 'custom-menu-link',
			'type' => 'nav-menu-item',
			'data' => array(
				'menu_name' => 'Primary',
				'menu-item-url' => 'http://test.com',
				'menu-item-title' => 'Custom Menu Title',
				'menu-item-type' => 'custom',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenuItem( $data );
		$item = $item_import->import();

		$this->assertEquals( 'Custom Menu Title', $item->title );
	}

	public function test_import_ItemImport_NavMenuItem_returns_decorated_wp_post_with_valid_post_type_data() {
		wp_create_nav_menu( 'Primary' );

		$post_id = $this->factory->post->create( array(
			'post_title' => 'Post For Menu',
		) );

		$data = array(
			'id' => 'post-type-menu-link',
			'type' => 'nav-menu-item',
			'data' => array(
				'menu_name' => 'Primary',
				'menu-item-title' => 'Custom Menu Title',
				'menu-item-type' => 'post_type',
				'menu-item-object-id' => $post_id,
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenuItem( $data );
		$item = $item_import->import();

		$this->assertEquals( 'Custom Menu Title', $item->title );
	}

	public function test_import_ItemImport_NavMenuItem_returns_decorated_wp_post_with_valid_post_type_archive_data() {
		wp_create_nav_menu( 'Primary' );

		$data = array(
			'id' => 'post-type-menu-link',
			'type' => 'nav-menu-item',
			'data' => array(
				'menu_name' => 'Primary',
				'menu-item-title' => 'All Posts',
				'menu-item-type' => 'post_type_archive',
				'menu-item-object' => 'post',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenuItem( $data );
		$item = $item_import->import();

		$this->assertEquals( 'All Posts', $item->title );
	}

	public function test_import_ItemImport_NavMenuItem_returns_decorated_wp_post_with_valid_taxonomy_data() {
		wp_create_nav_menu( 'Primary' );

		$data = array(
			'id' => 'post-type-menu-link',
			'type' => 'nav-menu-item',
			'data' => array(
				'menu_name' => 'Primary',
				'menu-item-type' => 'taxonomy',
				'menu-item-object' => 'category',
				'menu-item-object-title' => 'Uncategorized',
				'menu-item-title' => 'Uncategorized',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenuItem( $data );
		$item = $item_import->import();

		$this->assertEquals( 'Uncategorized', $item->title );
	}

	public function test_import_ItemImport_NavMenuItem_resturns_decorated_wp_post_with_valid_parent_data() {
		wp_create_nav_menu( 'Primary' );
		register_post_type( 'download', array(
			'has_archive' => true,
		) );

		// create the parent menu item
		$data = array(
			'id' => 'post-type-menu-link',
			'type' => 'nav-menu-item',
			'data' => array(
				'menu_name' => 'Primary',
				'menu-item-title' => 'All Downloads',
				'menu-item-type' => 'post_type_archive',
				'menu-item-object' => 'download',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenuItem( $data );
		$parent_item = $item_import->import();

		// add the child
		$data = array(
			'id' => 'child-taxonomy-menu-link',
			'type' => 'nav-menu-item',
			'data' => array(
				'menu_name' => 'Primary',
				'menu-item-type' => 'taxonomy',
				'menu-item-object' => 'category',
				'menu-item-object-title' => 'Uncategorized',
				'menu-item-title' => 'Uncategorized',
				'menu-item-parent-title' => 'All Downloads',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenuItem( $data );
		$item = $item_import->import();

		$this->assertEquals( $parent_item->ID, $item->menu_item_parent );
	}

	public function test_import_ItemImport_NavMenuItem_can_set_nav_menu_role() {
		wp_create_nav_menu( 'Primary' );

		$data = array(
			'id' => 'post-type-menu-link',
			'type' => 'nav-menu-item',
			'data' => array(
				'menu_name' => 'Primary',
				'menu-item-url' => 'http://test.com',
				'menu-item-title' => 'Custom Menu Title',
				'menu-item-type' => 'custom',
				'menu-item-role' => 'in',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenuItem( $data );
		$item = $item_import->iterate( 'import' );

		// Nav Menu Role
		$this->assertEquals( 'in', get_post_meta( $item->get_processed_item()->ID, '_nav_menu_role', true ) );

		// If Menu
		$this->assertEquals( 1, get_post_meta( $item->get_processed_item()->ID, 'if_menu_enable', true ) );
		$this->assertEquals( 'User is logged in', get_post_meta( $item->get_processed_item()->ID, 'if_menu_condition', true ) );
		$this->assertEquals( 'show', get_post_meta( $item->get_processed_item()->ID, 'if_menu_condition_type', true ) );
	}

	public function test_import_ItemImport_NavMenuItem_can_set_nav_menu_item_endpoint() {
		$menu = wp_get_nav_menu_object( wp_create_nav_menu( 'Primary' ) );

		$data = array(
			'id' => 'myaccount',
			'type' => 'object',
			'data' => array(
				'post_title' => 'My Account',
				'post_type' => 'page',
				'menus' => array(
					'Primary' => array(
						'menu-item-title' => 'Edit Account',
						'menu-item-endpoint' => 'edit-account',
						'menu-item-type' => 'custom',
					),
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$menu_items = wp_get_nav_menu_items( $menu );

		$expected = add_query_arg( 'edit-account', '', get_permalink( $item->get_processed_item()->ID ) );

		$this->assertEquals( $expected, $menu_items[0]->url );
	}

}
