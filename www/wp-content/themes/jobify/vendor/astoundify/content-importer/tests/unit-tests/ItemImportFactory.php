<?php
/**
 * Item Import factory
 *
 * @since 1.0.0
 */
class Test_ItemImportFactory extends WP_UnitTestCase {

	public function test_ItemImportFactory_returns_wp_error_with_no_item() {
		$importer = Astoundify_ItemImportFactory::create( array() );

		$this->assertTrue( is_wp_error( $importer ) );
	}

	public function test_ItemImportFactory_returns_wp_error_with_invalid_item_type() {
		$importer = Astoundify_ItemImportFactory::create( array(
			'type' => 'invalid',
		) );

		$this->assertTrue( is_wp_error( $importer ) );
	}

	public function test_ItemImportFactory_returns_item_import_class_with_object_type() {
		$importer = Astoundify_ItemImportFactory::create( array(
			'type' => 'object',
		) );

		$this->assertInstanceOf( 'Astoundify_ItemImport_Object', $importer );
	}

	public function test_ItemImportFactory_returns_item_import_class_with_navmenu_type() {
		$importer = Astoundify_ItemImportFactory::create( array(
			'type' => 'nav-menu',
		) );

		$this->assertInstanceOf( 'Astoundify_ItemImport_NavMenu', $importer );
	}

	public function test_ItemImportFactory_returns_item_import_class_with_navmenuitem_type() {
		$importer = Astoundify_ItemImportFactory::create( array(
			'type' => 'nav-menu-item',
		) );

		$this->assertInstanceOf( 'Astoundify_ItemImport_NavMenuItem', $importer );
	}

}
