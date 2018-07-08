<?php
/**
 * Item Import Widget
 *
 * @since 1.0.0
 */
class Test_ItemImport_Widget extends WP_UnitTestCase {

	public function test_import_ItemImport_Widget_returns_wp_error_with_invalid_sidebar() {
		$data = array(
			'id' => 'widget-text',
			'type' => 'widget',
			'data' => array(
				'widget' => 'text',
				'sidebar' => 'sidebar',
			),
		);

		$item_import = new Astoundify_ItemImport_Widget( $data );
		$item = $item_import->import();

		$this->assertTrue( is_wp_error( $item ) );
	}

	public function test_import_ItemImport_Widget_returns_widget_settings_with_valid_sidebar() {
		$data = array(
			'id' => 'widget-text',
			'type' => 'widget',
			'data' => array(
				'widget' => 'text',
				'sidebar' => 'sidebar-1',
				'title' => 'Text Widget',
			),
		);

		$item_import = new Astoundify_ItemImport_Widget( $data );
		$item = $item_import->import();

		$this->assertEquals( 'Text Widget', $item['title'] );
	}

	public function test_import_ItemImport_Widget_returns_widget_settings_with_asset_setting() {
		$data = array(
			'id' => 'widget-text',
			'type' => 'widget',
			'data' => array(
				'widget' => 'text',
				'sidebar' => 'sidebar-1',
				'title' => 'Text Widget',
				'image' => 'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
			),
		);

		$item_import = new Astoundify_ItemImport_Widget( $data );
		$item = $item_import->iterate( 'import' )->get_processed_item();

		$this->assertRegexp( '/example.org/', $item['image'] );
	}

	public function test_import_ItemImport_Widget_returns_widget_settings_with_widgetized_sidebar() {
		$data = array(
			'id' => 'widgetized-page',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Widgetized Page',
				'post_type' => 'page',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$page_id = $item->get_processed_item()->ID;

		register_sidebar( array(
			'id' => 'widget-area-page-' . $page_id,
			'name' => 'Widgetized Sidebar',
		) );

		$data = array(
			'id' => 'widget-text',
			'type' => 'widget',
			'data' => array(
				'widget' => 'text',
				'sidebar' => 'widget-area-page-widgetized-page',
				'title' => 'Text Widget',
			),
		);

		$item_import = new Astoundify_ItemImport_Widget( $data );
		$item = $item_import->iterate( 'import' );
		$widget = $item->get_processed_item();

		$this->assertEquals( 'widget-area-page-' . $page_id, $item->item['data']['sidebar'] );
		$this->assertEquals( 'Text Widget', $widget['title'] );
	}

	public function test_import_ItemImport_Widget_returns_widget_settings_with_multiple_widgets() {
		$data = array(
			'id' => 'widget-text',
			'type' => 'widget',
			'data' => array(
				'widget' => 'text',
				'sidebar' => 'sidebar-1',
				'title' => 'Text Widget',
			),
		);

		$widget_1 = new Astoundify_ItemImport_Widget( $data );
		$widget_1 = $widget_1->import();

		$widget_2 = new Astoundify_ItemImport_Widget( $data );
		$widget_2 = $widget_2->import();

		$this->assertEquals( 'Text Widget', $widget_2['title'] );
	}

	public function test_import_multiple_ItemImport_Widget() {
		// clear the initial sidebars
		update_option( 'sidebars_widgets', array() );

		register_sidebar( array(
			'id' => 'sidebar-2',
		) );

		$data = array(
			'id' => 'widget-text',
			'type' => 'widget',
			'data' => array(
				'widget' => 'text',
				'sidebar' => 'sidebar-1',
				'title' => 'Text Widget',
			),
		);

		$widget_1 = new Astoundify_ItemImport_Widget( $data );
		$widget_1 = $widget_1->import();

		$widget_2 = new Astoundify_ItemImport_Widget( $data );
		$widget_2 = $widget_2->import();

		$data_rss = array(
			'id' => 'widget-rss',
			'type' => 'widget',
			'data' => array(
				'widget' => 'rss',
				'sidebar' => 'sidebar-2',
				'title' => 'RSS Widget',
			),
		);

		$widget_3 = new Astoundify_ItemImport_Widget( $data_rss );
		$widget_3 = $widget_3->import();

		$widget_4 = new Astoundify_ItemImport_Widget( $data_rss );
		$widget_4 = $widget_4->import();

		$widget_5 = new Astoundify_ItemImport_Widget( $data );
		$widget_5 = $widget_5->import();

		$text = get_option( 'widget_text' );
		$expected_text = array(
			1 => array(
				'title' => 'Text Widget',
			),
			2 => array(
				'title' => 'Text Widget',
			),
			3 => array(
				'title' => 'Text Widget',
			),
		);

		$rss = get_option( 'widget_rss' );
		$expected_rss = array(
			1 => array(
				'title' => 'RSS Widget',
			),
			2 => array(
				'title' => 'RSS Widget',
			),
		);

		$sidebars = get_option( 'sidebars_widgets' );
		$expected_sidebars = array(
			'sidebar-1' => array( 'text-1', 'text-2', 'text-3' ),
			'sidebar-2' => array( 'rss-1', 'rss-2' ),
		);

		$this->assertEqualSets( $expected_text, $text );
		$this->assertEqualSets( $expected_rss, $rss );
		$this->assertEqualSets( $expected_sidebars, $sidebars );
	}

	public function test_import_ItemImport_Widget_can_convert_nav_menu() {
		$menu = wp_get_nav_menu_object( wp_create_nav_menu( 'Primary' ) );

		$data = array(
			'id' => 'widget-text',
			'type' => 'widget',
			'data' => array(
				'widget' => 'nav_menu',
				'sidebar' => 'sidebar-1',
				'title' => 'Custom Nav Menu',
				'nav_menu' => 'Primary',
			),
		);

		$widget = new Astoundify_ItemImport_Widget( $data );
		$widget = $widget->iterate( 'import' );

		$widgets = get_option( 'widget_nav_menu' );

		$this->assertEquals( $menu->term_id, $widgets[1]['nav_menu'] );
	}

}
