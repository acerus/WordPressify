<?php
/**
 * Item Import Child Theme
 *
 * @since 1.3.0
 */
class Test_ItemImport_ChildTheme extends WP_UnitTestCase {

	/**
	 * Current theme
	 */
	public $theme;

	public function setUp() {
		parent::setUp();

		$this->theme = wp_get_theme();

		$creds = request_filesystem_credentials( admin_url() );
		WP_Filesystem( $creds ); // we already have direct access
	}

	public function test_import_ItemImport_ChildTheme_returns_wp_error_with_invalid_data() {
		// `id` cannot be empty -- this is the dir name
		$data = array(
			'type' => 'childtheme',
			'data' => '',
		);

		$item_import = new Astoundify_ItemImport_ChildTheme( $data );
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_ChildTheme_can_set_stylesheet() {
		// `id` cannot be empty -- this is the dir name
		$data = array(
			'type' => 'childtheme',
			'id' => 'astoundify-child',
			'data' => array(
				'stylesheet' => '/**
 * Theme Name: Child Theme
 */',
			),
		);

		$item_import = new Astoundify_ItemImport_ChildTheme( $data );
		$item = $item_import->import();

		global $wp_filesystem;
		$child_dir = trailingslashit( get_theme_root() ) . 'astoundify-child';

		$stylesheet = $wp_filesystem->get_contents( $child_dir . '/style.css' );

		$this->assertContains( 'Theme Name: Child Theme', $stylesheet );
	}

	public function test_import_ItemImport_ChildTheme_can_set_functions() {
		$data = array(
			'type' => 'childtheme',
			'id' => 'astoundify-child',
			'data' => array(
				'functions' => '<?php
/**
 * Child Theme
 */',
			),
		);

		$item_import = new Astoundify_ItemImport_ChildTheme( $data );
		$item = $item_import->iterate( 'import' );

		global $wp_filesystem;
		$child_dir = trailingslashit( get_theme_root() ) . 'astoundify-child';

		$functions = $wp_filesystem->get_contents( $child_dir . '/functions.php' );

		$this->assertContains( 'Child Theme', $functions );
	}

	public function test_import_ItemImport_ChildTheme_can_set_screenshot() {
		$data = array(
			'type' => 'childtheme',
			'id' => 'astoundify-child',
			'data' => array(
				'functions' => '<?php
/**
 * Child Theme
 */',
			),
		);

		$item_import = new Astoundify_ItemImport_ChildTheme( $data );
		$item = $item_import->iterate( 'import' );

		global $wp_filesystem;
		$child_dir = trailingslashit( get_theme_root() ) . 'astoundify-child';

		$functions = $wp_filesystem->get_contents( $child_dir . '/functions.php' );

		$this->assertContains( 'Child Theme', $functions );
	}

	public function test_import_ItemImport_ChildTheme_does_not_duplicate() {
		$data = array(
			'type' => 'childtheme',
			'id' => 'astoundify-child',
			'data' => array(
				'functions' => '<?php
/**
 * Child Theme
 */',
			),
		);

		$item_import = new Astoundify_ItemImport_ChildTheme( $data );
		$item = $item_import->iterate( 'import' );

		// run import again with different data
		$data = array(
			'type' => 'childtheme',
			'id' => 'astoundify-child',
			'data' => array(),
		);

		$item_import = new Astoundify_ItemImport_ChildTheme( $data );
		$item = $item_import->iterate( 'import' );

		// check functions
		global $wp_filesystem;
		$child_dir = trailingslashit( get_theme_root() ) . 'astoundify-child';

		$functions = $wp_filesystem->get_contents( $child_dir . '/functions.php' );

		$this->assertEquals( 'astoundify-child', wp_get_theme()->get_template() );
		$this->assertContains( 'Child Theme', $functions );
	}

	public function test_reset_ItemImport_ChildTheme_returns_to_parent_theme() {
		$parent_theme = wp_get_theme();

		$data = array(
			'type' => 'childtheme',
			'id' => 'astoundify-child',
			'data' => array(
				'screenshot' => 'https://jobify-demos.astoundify.com/wp-content/themes/jobify/screenshot.png',
			),
		);

		$item_import = new Astoundify_ItemImport_ChildTheme( $data );
		$item_import->iterate( 'import' );

		$theme = wp_get_theme();

		$this->assertTrue( file_exists( $theme->get_stylesheet_directory() . '/' . $theme->get_screenshot( 'relative' ) ) );
	}

}
