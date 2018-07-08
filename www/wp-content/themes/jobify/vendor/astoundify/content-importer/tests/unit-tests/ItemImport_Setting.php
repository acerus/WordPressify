<?php
/**
 * Item Import Setting
 *
 * @since 1.0.0
 */
class Test_ItemImport_Setting extends WP_UnitTestCase {

	public function test_import_ItemImport_Setting_returns_wp_error_with_invalid_data() {
		// `id` is required and used as theme mod key
		$data = array(
			'type' => 'setting',
			'data' => 'setting_value',
		);

		$item_import = new Astoundify_ItemImport_Setting( $data );
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_Setting_returns_updated_value_with_previous_import() {
		add_option( 'foo', 'baz' );

		$data = array(
			'id' => 'foo',
			'type' => 'setting',
			'data' => 'bar',
		);

		$item_import = new Astoundify_ItemImport_Setting( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertTrue( $item->get_processed_item() );
		$this->assertEquals( 'bar', get_option( 'foo' ) );
	}

	public function test_import_ItemImport_Setting_returns_updated_value_with_no_previous_import_array() {
		$data = array(
			'id' => 'foo',
			'type' => 'setting',
			'data' => array(
				'a' => 'b',
				'c' => 'd',
			),
		);

		$item_import = new Astoundify_ItemImport_Setting( $data );
		$item = $item_import->iterate( 'import' );

		$option = get_option( 'foo' );

		$this->assertTrue( $item->get_processed_item() );
		$this->assertEqualSets( array(
			'a' => 'b',
			'c' => 'd',
		), $option );
	}

	public function test_import_ItemImport_Setting_returns_updated_value_with_previous_import_array() {
		add_option( 'foo', array(
			'a' => 'b',
			'c' => 'd',
		) );

		$data = array(
			'id' => 'foo',
			'type' => 'setting',
			'data' => array(
				'c' => 'z',
				'd' => 'e',
			),
		);

		$item_import = new Astoundify_ItemImport_Setting( $data );
		$item = $item_import->iterate( 'import' );

		$option = get_option( 'foo' );

		$this->assertTrue( $item->get_processed_item() );
		$this->assertEqualSets( array(
			'a' => 'b',
			'c' => 'z',
			'd' => 'e',
		), $option );
	}

	public function test_import_ItemImport_Setting_returns_updated_value_with_previous_theme_mods() {
		set_theme_mod( 'a', 'b' );
		set_theme_mod( 'c', 'd' );

		$data = array(
			'id' => 'theme_mods_twentyfifteen',
			'type' => 'setting',
			'data' => array(
				'c' => 'z',
				'd' => 'e',
			),
		);

		$item_import = new Astoundify_ItemImport_Setting( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertEqualSets( array(
			'a' => 'b',
			'c' => 'z',
			'd' => 'e',
		), get_theme_mods() );
	}

	public function test_import_ItemImport_Setting_returns_option_value_with_valid_data() {
		$data = array(
			'id' => 'astoundify_setting',
			'type' => 'setting',
			'data' => 'astoundify_setting_value',
		);

		$item_import = new Astoundify_ItemImport_Setting( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertTrue( $item->get_processed_item() );
		$this->assertEquals( 'astoundify_setting_value', get_option( 'astoundify_setting' ) );
	}

	public function test_import_ItemImport_Setting_can_import_boolean() {
		add_option( 'foo', 'baz' );

		$data = array(
			'id' => 'foo',
			'type' => 'setting',
			'data' => array(
				'foo' => false,
			),
		);

		// mock json
		$data = json_decode( json_encode( $data ), true );

		$item_import = new Astoundify_ItemImport_Setting( $data );
		$item = $item_import->iterate( 'import' );

		$option = get_option( 'foo' );
		$this->assertInternalType( 'boolean', $option['foo'] );
	}

	public function test_reset_ItemImport_Setting_returns_wp_error_with_invalid_data() {
		// `id` is required and used as theme mod key
		$data = array(
			'type' => 'setting',
			'data' => 'setting_value',
		);

		$item_import = new Astoundify_ItemImport_Setting( $data );
		$item_import->import();
		$item = $item_import->reset();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_reset_ItemImport_Setting_returns_true_with_valid_data() {
		$data = array(
			'id' => 'astoundify_setting',
			'type' => 'setting',
			'data' => 'astoundify_setting_value',
		);

		$item_import = new Astoundify_ItemImport_Setting( $data );
		$item_import->import();
		$item = $item_import->reset();

		$this->assertTrue( $item );
	}

	public function test_reset_ItemImport_Setting_returns_updated_value_with_previous_import_array() {
		add_option( 'foo', array(
			'a' => 'b',
			'c' => 'd',
		) );

		$data = array(
			'id' => 'foo',
			'type' => 'setting',
			'data' => array(
				'c' => 'z',
			),
		);

		$item_import = new Astoundify_ItemImport_Setting( $data );
		$item = $item_import->reset();

		$option = array_values( get_option( 'foo' ) );

		// we can't know the original value so we just have to remove it and hope they have good defaults
		$this->assertEqualSets( array(
			'a' => 'b',
		), $option );
	}

}
