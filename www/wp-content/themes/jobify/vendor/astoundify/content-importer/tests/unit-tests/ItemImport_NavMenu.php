<?php
/**
 * Item Import Nav Menu
 *
 * @since 1.0.0
 */
class Test_ItemImport_NavMenu extends WP_UnitTestCase {

	public function test_import_ItemImport_NavMenu_returns_wp_error_with_invalid_menu_name() {
		register_nav_menu( 'primary', 'Primary' );

		$data = array(
			'id' => 'navmenu-primary',
			'type' => 'nav-menu',
			'data' => array(
				'location' => 'primary',
				// 'name' must be set to be valid
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenu( $data );
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_NavMenu_returns_wp_error_with_invalid_menu_location() {
		register_nav_menu( 'primary', 'Primary' );

		$data = array(
			'id' => 'navmenu-primary',
			'type' => 'nav-menu',
			'data' => array(
				'location' => 'secondary',
				'menu_name' => 'Secondary Navigation',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenu( $data );
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_NavMenu_returns_wp_error_with_previous_import() {
		$data = array(
			'id' => 'navmenu-primary',
			'type' => 'nav-menu',
			'data' => array(
				'menu_name' => 'Primary',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenu( $data );
		$item = $item_import->import();
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_NavMenu_returns_wp_term_with_valid_data() {
		register_nav_menu( 'primary', 'Primary' );

		$data = array(
			'id' => 'navmenu-primary',
			'type' => 'nav-menu',
			'data' => array(
				'name' => 'Primary',
				'location' => 'primary',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenu( $data );
		$item = $item_import->import();

		$this->assertEquals( 'Primary', $item->name );
	}

	public function test_imporm_ItemImport_OBject_can_set_menu_location() {
		register_nav_menu( 'primary', 'Primary' );

		$data = array(
			'id' => 'navmenu-primary',
			'type' => 'nav-menu',
			'data' => array(
				'name' => 'Primary',
				'location' => 'primary',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenu( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertTrue( has_nav_menu( 'primary' ) );
	}

	public function test_reset_ItemImport_NavMenu_returns_wp_error_with_no_previous_import() {
		register_nav_menu( 'primary', 'Primary' );

		$data = array(
			'id' => 'navmenu-primary',
			'type' => 'nav-menu',
			'data' => array(
				'name' => 'Primary',
				'location' => 'primary',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenu( $data );
		$item = $item_import->reset();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_reset_ItemImport_OBject_returns_menu_id_with_previous_import() {
		register_nav_menu( 'primary', 'Primary' );

		$menu_id = wp_create_nav_menu( 'Primary' );

		$data = array(
			'id' => 'navmenu-primary',
			'type' => 'nav-menu',
			'data' => array(
				'name' => 'Primary',
				'location' => 'primary',
			),
		);

		$item_import = new Astoundify_ItemImport_NavMenu( $data );
		$item = $item_import->reset();

		$this->assertEquals( $menu_id, $item );
	}

}
