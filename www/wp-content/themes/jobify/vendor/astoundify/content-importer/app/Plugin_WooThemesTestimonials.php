<?php
/**
 * Extra procsesing for Testimobials by WooThemes
 *
 * @since 1.0.0
 */
class Astoundify_Plugin_WooThemesTestimonials implements Astoundify_PluginInterface {

	/**
	 * Initialize the plugin processing
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		self::setup_actions();
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function setup_actions() {
		$widgets = array( 'widget-testimonials', 'widget-companies' );

		foreach ( $widgets as $widget ) {
			add_action(
				'astoundify_import_content_after_import_item_' . $widget,
				array( __CLASS__, 'add_widget_settings' )
			);
		}
	}

	/**
	 * Assign the relevant setting to the widget.
	 *
	 * Converts the testimonial category slug in to an ID needed by the widget settings.
	 *
	 * @since 1.0.0
	 * @param array $args Import item context.
	 * @return void
	 */
	public static function add_widget_settings( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		$processed = $ItemImport->get_processed_item();

		$widget_settings = get_option( 'widget_' . $item_data['widget'], array() );

		if ( empty( $widget_settings ) ) {
			return false;
		}

		foreach ( $widget_settings as $key => $single_widget_settings ) {
			if ( ! is_int( $key ) ) {
				continue;
			}

			// We have found the widget
			if ( $single_widget_settings['title'] == $item_data['title'] ) {
				$category = get_term_by( 'slug', $item_data['category'], 'testimonial-category' );

				if ( ! isset( $single_widget_settings['category'] ) ) {
					continue;
				}

				$single_widget_settings['category'] = $category->term_id;
				$widget_settings[ $key ] = $single_widget_settings;
			}
		}

		update_option( 'widget_' . $item_data['widget'], $widget_settings );
	}

}

Astoundify_Plugin_WooThemesTestimonials::init();
