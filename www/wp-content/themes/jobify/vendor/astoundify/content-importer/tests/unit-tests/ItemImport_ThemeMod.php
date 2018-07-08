<?php
/**
 * Item Import Theme Mod
 *
 * @since 1.2.0
 */
class Test_ItemImport_ThemeMod extends WP_UnitTestCase {

	public function test_import_ItemImport_ThemeMod_returns_wp_error_with_invalid_data() {
		// `id` is required and used as theme mod key
		$data = array(
			'type' => 'thememod',
			'data' => 'setting_value',
		);

		$item_import = new Astoundify_ItemImport_ThemeMod( $data );
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_ThemeMod_returns_updated_value_with_previous_import() {
		set_theme_mod( 'foo', 'baz' );

		$data = array(
			'id' => 'foo',
			'type' => 'thememod',
			'data' => 'bar',
		);

		$item_import = new Astoundify_ItemImport_ThemeMod( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertTrue( $item->get_processed_item() );
		$this->assertEquals( 'bar', get_theme_mod( 'foo' ) );
	}

	public function test_import_ItemImport_ThemeMod_returns_updated_value_with_no_previous_import_array() {
		$data = array(
			'id' => 'foo',
			'type' => 'thememod',
			'data' => array(
				'a' => 'b',
				'c' => 'd',
			),
		);

		$item_import = new Astoundify_ItemImport_ThemeMod( $data );
		$item = $item_import->iterate( 'import' );

		$mod = get_theme_mod( 'foo' );

		$this->assertTrue( $item->get_processed_item() );
		$this->assertEqualSets( array(
			'a' => 'b',
			'c' => 'd',
		), $mod );
	}

	public function test_import_ItemImport_ThemeMod_returns_updated_value_with_previous_import_array() {
		set_theme_mod( 'foo', array(
			'a' => 'b',
			'c' => 'd',
		) );

		$data = array(
			'id' => 'foo',
			'type' => 'thememod',
			'data' => array(
				'c' => 'z',
				'd' => 'e',
			),
		);

		$item_import = new Astoundify_ItemImport_ThemeMod( $data );
		$item = $item_import->iterate( 'import' );

		$mod = get_theme_mod( 'foo' );

		$this->assertTrue( $item->get_processed_item() );
		$this->assertEqualSets( array(
			'a' => 'b',
			'c' => 'z',
			'd' => 'e',
		), $mod );
	}

	public function test_import_ItemImport_ThemeMod_can_import_boolean() {
		set_theme_mod( 'foo', 'baz' );

		$data = array(
			'id' => 'foo',
			'type' => 'thememod',
			'data' => 0,
		);

		$item_import = new Astoundify_ItemImport_ThemeMod( $data );
		$item = $item_import->iterate( 'import' );

		$option = get_theme_mod( 'foo' );
		$this->assertInternalType( 'integer', $option );
	}

	public function test_reset_ItemImport_ThemeMod_returns_wp_error_with_invalid_data() {
		// `id` is required and used as theme mod key
		$data = array(
			'type' => 'thememod',
			'data' => 'setting_value',
		);

		$item_import = new Astoundify_ItemImport_ThemeMod( $data );
		$item_import->import();
		$item = $item_import->reset();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_reset_ItemImport_ThemeMod_returns_true_with_valid_data() {
		$data = array(
			'id' => 'astoundify_setting',
			'type' => 'thememod',
			'data' => 'astoundify_setting_value',
		);

		$item_import = new Astoundify_ItemImport_ThemeMod( $data );
		$item_import->import();
		$item = $item_import->reset();

		$this->assertTrue( $item );
	}

	public function test_reset_ItemImport_ThemeMod_returns_updated_value_with_previous_import_array() {
		set_theme_mod( 'foo', array(
			'a' => 'b',
			'c' => 'd',
		) );

		$data = array(
			'id' => 'foo',
			'type' => 'thememod',
			'data' => array(
				'c' => 'z',
			),
		);

		$item_import = new Astoundify_ItemImport_ThemeMod( $data );
		$item = $item_import->reset();

		$option = array_values( get_theme_mod( 'foo' ) );

		// we can't know the original value so we just have to remove it and hope they have good defaults
		$this->assertEqualSets( array(
			'a' => 'b',
		), $option );
	}

}
