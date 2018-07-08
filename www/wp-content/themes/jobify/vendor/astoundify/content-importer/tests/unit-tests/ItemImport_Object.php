<?php
/**
 * Item Import Object
 *
 * @since 1.0.0
 */
class Test_ItemImport_Object extends WP_UnitTestCase {

	public function test_import_ItemImport_Object_returns_wp_error_with_invalid_data() {
		$data = array(
			'id' => false,
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_content' => '',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_Object_returns_wp_error_with_previous_import() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_content' => 'Test',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->import();
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_Object_returns_object_with_valid_data() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->import();

		$this->assertEquals( 'Object Title', $item->post_title );
	}

	public function test_import_ItemImport_Object_returns_wp_error_with_existing_object() {
		$this->factory->post->create( array(
			'post_name' => 'object-id',
		) );

		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'data' => array(),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_Object_can_set_generated_content_defaults() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Object Title',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$object = get_post( $item->get_processed_item()->ID );

		$this->assertContains( '<p>', $object->post_content );
	}

	public function test_import_ItemImport_Object_can_set_generated_content_with_set_url() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Object Title',
				'post_content' => 'http://www.randomtext.me/api/gibberish/p-1/100-150/',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$object = get_post( $item->get_processed_item()->ID );

		$this->assertContains( '<p>', $object->post_content );
	}

	public function test_import_ItemImport_Object_can_set_replace_inline_assets() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Object Title',
				'post_content' => '<img src="http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png" alt="" />',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$object = get_post( $item->get_processed_item()->ID );

		// pretty lame check
		$this->assertContains( 'http://example.org/', $object->post_content );
	}

	public function test_import_ItemImport_Object_can_set_parent() {
		$data = array(
			'id' => 'parent-id',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Parent Title',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$parent = $item_import->iterate( 'import' );

		$data = array(
			'id' => 'child-id',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Child Title',
				'post_parent' => 'parent-id',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$child = $item_import->iterate( 'import' );

		// get the updated object
		$child = get_post( $child->get_processed_item()->ID );

		$this->assertEquals( $parent->get_processed_item()->ID, $child->post_parent );
	}

	public function test_import_ItemImport_Object_can_set_post_format() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'post_format' => 'aside',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertEquals( 'aside', get_post_format( $item->get_processed_item()->ID ) );
	}

	public function test_import_ItemImport_Object_can_set_featured_image() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'featured_image' => 'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertInternalType( 'string', $item->get_processed_item()->_thumbnail_id );
	}

	public function test_import_ItemImport_Object_can_set_post_meta() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'meta' => array(
					'foo' => 'bar',
					'bar' => array(
						'foo' => 'bar',
					),
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertEquals( 'bar', $item->get_processed_item()->foo );
		$this->assertEquals( array(
			'foo' => 'bar',
		), $item->get_processed_item()->bar );
	}

	/**
	 * @since 1.2.0
	 */
	public function test_import_ItemImport_Object_can_set_post_meta_assets_array() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'meta' => array(
					'images' => array(
						'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
						'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
					),
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$media = get_attached_media( 'image', $item->get_processed_item()->ID );
		$media_urls = wp_list_pluck( $media, 'guid' );

		$this->assertEqualSets( $item->get_processed_item()->images, $media_urls );
	}

	/**
	 * @since 1.2.0
	 */
	public function test_import_ItemImport_Object_can_set_post_meta_assets_array_set_ids() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'meta' => array(
					'images|ids' => array(
						'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
						'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
					),
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$media = get_attached_media( 'image', $item->get_processed_item()->ID );
		$media_ids = array_values( wp_list_pluck( $media, 'ID' ) );

		$this->assertEqualSets( maybe_unserialize( get_post_meta( $item->get_processed_item()->ID, 'images', true ) ), $media_ids );
	}

	/**
	 * @since 1.2.0
	 */
	public function test_import_ItemImport_Object_can_set_post_meta_assets_set_id() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'meta' => array(
					'images|ids' => 'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$media = get_attached_media( 'image', $item->get_processed_item()->ID );
		$media_ids = array_values( wp_list_pluck( $media, 'ID' ) );

		$this->assertEquals( get_post_meta( $item->get_processed_item()->ID, 'images', true ), $media_ids[0] );
	}

	public function test_import_ItemImport_Object_can_set_post_terms() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'terms' => array(
					'category' => array( 'uncategorized' ),
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$terms = get_the_terms( $item->get_processed_item()->ID, 'category' );

		$this->assertInstanceOf( 'WP_Term', $terms[0] );
	}

	public function test_import_ItemImport_Object_can_set_post_media() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'media' => array(
					'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png'
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$media = get_posts( array(
			'post_type' => 'attachment',
			'post_status' => 'any',
			'posts_per_page' => -1,
		) );

		$this->assertEquals( 1, count( $media ) );
	}

	public function test_import_ItemImport_Object_can_set_menu_item() {
		$menu = wp_create_nav_menu( 'Primary' );

		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'menus' => array(
					'Primary' => array(
						'menu-item-title' => 'Menu Item Title',
					),
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$menu_items = wp_get_nav_menu_items( $menu );

		$this->assertEquals( 'Menu Item Title', $menu_items[0]->title );
	}

	public function test_import_ItemImport_Object_can_set_multiple_menu_items() {
		$menu = wp_create_nav_menu( 'Primary' );

		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'menus' => array(
					array(
						'menu-item-title' => 'Menu Item Title',
						'menu_name' => 'Primary',
					),
					array(
						'menu-item-title' => 'Menu Item Title 2',
						'menu_name' => 'Primary',
					),
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$menu_items = wp_get_nav_menu_items( $menu );

		$this->assertEquals( 'Menu Item Title 2', $menu_items[1]->title );
	}

	public function test_import_ItemImport_Object_can_add_single_comments() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'comments' => array(
					array(
						'comment_author' => 'Spencer',
						'comment_author_email' => 'spencer@astoundify.com',
						'comment_content' => 'Great post!',
					),
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$comments = get_comments( array(
			'post_id' => $item->get_processed_item()->ID,
		) );

		$this->assertEquals( $item->get_processed_item()->ID, current( $comments )->comment_post_ID );
	}

	public function test_import_ItemImport_Object_can_add_multiple_comments() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'comments' => array(
					array(
						'comment_author' => 'Spencer',
						'comment_author_email' => 'spencer@astoundify.com',
						'comment_content' => 'Great post!',
					),
					array(
						'comment_author' => 'Spencer',
						'comment_author_email' => 'spencer@astoundify.com',
						'comment_content' => 'I agree with Spencer!',
					),
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$comments = get_comments( array(
			'post_id' => $item->get_processed_item()->ID,
		) );

		$this->assertEquals( 2, count( $comments ) );
	}

	public function test_import_ItemImport_Object_can_set_page_on_front() {
		$data = array(
			'id' => 'home',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Homepage',
				'post_type' => 'page',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertEquals( $item->get_processed_item()->ID, get_option( 'page_on_front' ) );
	}

	public function test_import_ItemImport_Object_can_set_page_for_posts() {
		$data = array(
			'id' => 'blog',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Blog',
				'post_type' => 'page',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$this->assertEquals( $item->get_processed_item()->ID, get_option( 'page_for_posts' ) );
	}

	public function test_reset_ItemImport_Object_returns_wp_error_with_no_previous_import() {
		$data = array(
			'id' => 'object-id',
			'type' => 'object',
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->reset();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_reset_ItemImport_Object_returns_wp_post_with_previous_import() {
		$imported_object = $this->factory->post->create( array(
			'post_name' => 'factory-object-id',
		) );

		$data = array(
			'id' => 'factory-object-id',
			'type' => 'object',
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'reset' );

		$this->assertEquals( 'factory-object-id', $item->get_processed_item()->post_name );
	}

	public function test_reset_ItemImport_Object_delete_attachments() {
		$data = array(
			'id' => 'object-id-with-media',
			'type' => 'object',
			'priority' => 10,
			'data' => array(
				'post_title' => 'Object Title',
				'media' => array(
					'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png'
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );

		// import
		$item = $item_import->iterate( 'import' );

		// reset
		$item = $item_import->iterate( 'reset' );
		$media = get_attached_media( 'image', $item->get_processed_item()->ID );

		$this->assertEquals( 0, count( $media ) );
	}

}
