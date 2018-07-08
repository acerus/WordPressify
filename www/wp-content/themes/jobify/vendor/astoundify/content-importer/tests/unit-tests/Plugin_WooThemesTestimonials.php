<?php
/**
 * WooThemes Testimonials
 *
 * @since 1.0.0
 */
class Test_Plugin_WooThemesTestimonials extends WP_UnitTestCase {

	public function test_set_widget_category_after_widget_import() {
		// mock plugin
		register_post_type( 'testimonial' );
		register_taxonomy( 'testimonial-category', 'testimonial' );
		$term = wp_insert_term( 'customer-testimonials', 'testimonial-category' );

		$data = array(
			'id' => 'widget-testimonials',
			'type' => 'widget',
			'data' => array(
				'widget' => 'woothemes_testimonials',
				'sidebar' => 'sidebar-1',
				'title' => 'Testimonials',
				'category' => 'customer-testimonials',
			),
		);

		$item_import = new Astoundify_ItemImport_Widget( $data );
		$item = $item_import->iterate( 'import' );

		$widget_settings = get_option( 'widget_woothemes_testimonials' );
		$widget_settings = $widget_settings[1];

		$this->assertEquals( $term['term_id'], $widget_settings['category'] );
	}

}
