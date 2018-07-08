<?php
/**
 * Item Import Term
 *
 * @since 1.0.0
 */
class Test_ItemImport_Term extends WP_UnitTestCase {

	public function test_import_ItemImport_Term_returns_wp_error_with_invalid_taxonomy() {
		$data = array(
			'id' => 'business',
			'type' => 'term',
			'data' => array(
				'taxonomy' => 'download_category',
				'name' => 'Business',
			),
		);

		$item_import = new Astoundify_ItemImport_Term( $data );
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_Term_returns_wp_error_with_previous_import() {
		$data = array(
			'id' => 'cat-business',
			'type' => 'term',
			'data' => array(
				'taxonomy' => 'category',
				'name' => 'Business',
			),
		);

		$item_import = new Astoundify_ItemImport_Term( $data );
		$item = $item_import->import();
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_Term_returns_wp_term_with_valid_data() {
		$data = array(
			'id' => 'business',
			'type' => 'term',
			'data' => array(
				'taxonomy' => 'category',
				'name' => 'Business',
			),
		);

		$item_import = new Astoundify_ItemImport_Term( $data );
		$item = $item_import->import();

		$this->assertEquals( 'Business', $item->name );
	}

	public function test_import_ItemImport_Term_returns_wp_term_with_parent_term() {
		$data = array(
			'id' => 'business',
			'type' => 'term',
			'data' => array(
				'taxonomy' => 'category',
				'name' => 'Business',
			),
		);

		$item_import = new Astoundify_ItemImport_Term( $data );
		$term_1 = $item_import->import();

		$data = array(
			'id' => 'local',
			'type' => 'term',
			'data' => array(
				'taxonomy' => 'category',
				'name' => 'Local',
				'parent' => 'Business',
			),
		);

		$item_import = new Astoundify_ItemImport_Term( $data );
		$term_2 = $item_import->import();

		$this->assertEquals( $term_1->term_id, $term_2->parent );
	}

	public function test_import_ItemImport_Term_returns_wp_term_with_term_meta() {
		$data = array(
			'id' => 'business',
			'type' => 'term',
			'data' => array(
				'taxonomy' => 'category',
				'name' => 'Business',
				'meta' => array(
					'foo' => 'bar',
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Term( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertEquals( 'bar', get_term_meta( $item->get_processed_item()->term_id, 'foo', true ) );
	}

	public function test_import_ItemImport_Term_can_set_term_meta_assets_set_id() {
		$data = array(
			'id' => 'business',
			'type' => 'term',
			'data' => array(
				'taxonomy' => 'category',
				'name' => 'Business',
				'meta' => array(
					'image|id' => 'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Term( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertInternalType( 'integer', intval( get_term_meta( $item->get_processed_item()->term_id, 'image', true ) ) );
	}

	public function test_reset_ItemImport_Term_returns_wp_error_with_invalid_taxonomy() {
		$data = array(
			'id' => 'category-business',
			'type' => 'term',
			'data' => array(
				'taxonomy' => 'download_category',
				'name' => 'Business',
			),
		);

		$item_import = new Astoundify_ItemImport_Term( $data );
		$item = $item_import->reset();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_reset_ItemImport_Term_returns_wp_error_with_missing_term() {
		$data = array(
			'id' => 'business',
			'type' => 'term',
			'data' => array(
				'taxonomy' => 'category',
			),
		);

		$item_import = new Astoundify_ItemImport_Term( $data );
		$item = $item_import->reset();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_reset_ItemImport_Term_returns_true_existing_term() {
		$term_id = wp_insert_term( 'Business', 'category' );
		$term = get_term( $term_id, 'category' );

		$data = array(
			'id' => 'category-business',
			'type' => 'term',
			'data' => array(
				'taxonomy' => 'category',
				'name' => 'Business',
			),
		);

		$item_import = new Astoundify_ItemImport_Term( $data );
		$item = $item_import->reset();

		$this->assertTrue( $item );
	}

}
