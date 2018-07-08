<?php
/**
 * Item Import Object
 *
 * @since 1.0.0
 */
class Test_Utils extends WP_UnitTestCase {

	public $upload_dir;
	public $image;

	public function setUp() {
		$this->upload_dir = wp_upload_dir();
	}

	public function test_cannot_upload_invalid_image_asset() {
		$file = 'http://invalid/image.jpg';
		$image = Astoundify_Utils::upload_asset( $file );

		$this->assertFalse( file_exists( $this->upload_dir['path'] . '/image.jpg' ) );
	}

	public function test_can_upload_real_image_asset() {
		$file = 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/45/A_small_cup_of_coffee.JPG/1px-A_small_cup_of_coffee.JPG';
		$image = Astoundify_Utils::upload_asset( $file );

		$this->assertTrue( file_exists( $this->upload_dir['path'] . '/1px-A_small_cup_of_coffee.jpg' ) );
	}

	public function test_can_attach_real_image_asset() {
		$object_data = array(
			'id' => 'object-id',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Object',
			),
		);

		$object_import = new Astoundify_ItemImport_Object( $object_data );
		$object = $object_import->iterate( 'import' );

		$file = 'https://17315-presscdn-0-68-pagely.netdna-ssl.com/classic/wp-content/uploads/sites/2/2016/05/photo-1459597093177-5c47509d2d61.jpg';
		$image = Astoundify_Utils::upload_asset( $file, $object->get_processed_item()->ID );

		$images = get_posts( array(
			'post_type' => 'attachment',
			'post_status' => 'any',
			'posts_per_page' => -1,
		) );

		$this->assertEquals( 1, count( $images ) );
		$this->assertEquals( $object->get_processed_item()->ID, get_post( $image )->post_parent );
	}

}
