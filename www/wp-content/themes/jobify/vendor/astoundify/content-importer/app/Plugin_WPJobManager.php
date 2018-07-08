<?php
/**
 * Extra procsesing for WP Job Manager
 *
 * @since 1.0.0
 */
class Astoundify_Plugin_WPJobManager implements Astoundify_PluginInterface {

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
		$pages = array( 'jobs', 'submit_job_form', 'job_dashboard', 'stats', 'claim_listing' );

		foreach ( $pages as $page ) {
			add_action(
				'astoundify_import_content_after_import_item_' . $page,
				array( __CLASS__, 'add_page_option' )
			);

			add_action(
				'astoundify_import_content_after_reset_item_' . $page,
				array( __CLASS__, 'delete_page_option' )
			);
		}

		add_action(
			'astoundify_import_content_after_import_item_type_object',
			array( __CLASS__, 'set_location' )
		);

		add_action(
			'astoundify_import_content_after_import_item_type_object',
			array( __CLASS__, 'set_featured' )
		);
	}

	/**
	 * Assign the relevant setting.
	 *
	 * @since 1.1.0
	 * @param array $args Import item context.
	 * @return void
	 */
	public static function add_page_option( $ItemImport ) {
		update_option( "job_manager_{$ItemImport->get_id()}_page_id", $ItemImport->get_processed_item()->ID );
	}

	/**
	 * Delete the relevant setting.
	 *
	 * @since 1.1.0
	 * @param array $args Import item context.
	 * @return void
	 */
	public static function delete_page_option( $ItemImport ) {
		delete_option( "job_manager_{$ItemImport->get_id()}_page_id" );
	}

	/**
	 * Add a location to a listing.
	 *
	 * If `location` is a string the geolocation data will automatically
	 * be generated. Otherise all data can be supplied.
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public static function set_location( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		// do nothing if this is not relevant to the current object type
		if ( ! in_array( $item_data['post_type'], array( 'job_listing', 'resume' ) ) ) {
			return false;
		}

		$error = new WP_Error(
			'set-location',
			sprintf( 'Location for %s was not set', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$listing_id = $object->ID;

		if ( isset( $item_data['location'] ) ) {
			$location = $item_data['location'];

			if ( ! is_array( $location ) ) {
				/**
				 * @codeCoverageIgnore
				 */
				if ( class_exists( 'WP_Job_Manager_Geocode' ) ) {
					WP_Job_Manager_Geocode::generate_location_data( $listing_id, sanitize_text_field( $location ) );

					if ( 'job_listing' == $item_data['post_type'] ) {
						update_post_meta( $listing_id, '_job_location', $location );
					} elseif ( 'job_listing' == $item_data['post_type'] ) {
						update_post_meta( $listing_id, '_candidate_location', $location );
					}
				}
			} else {
				if ( 'job_listing' == $item_data['post_type'] ) {
					update_post_meta( $listing_id, '_job_location', $location['address'] );
				} elseif ( 'job_listing' == $item_data['post_type'] ) {
					update_post_meta( $listing_id, '_candidate_location', $location['address'] );
				}

				update_post_meta( $listing_id, 'geolocated', 1 );
				update_post_meta( $listing_id, 'geolocation_city', $location['city'] );
				update_post_meta( $listing_id, 'geolocation_country_long', $location['country_long'] );
				update_post_meta( $listing_id, 'geolocation_country_short', $location['country_short'] );
				update_post_meta( $listing_id, 'geolocation_formatted_address', $location['address'] );
				update_post_meta( $listing_id, 'geolocation_lat', $location['latitude'] );
				update_post_meta( $listing_id, 'geolocation_long', $location['longitude'] );
				update_post_meta( $listing_id, 'geolocation_state_long', $location['state'] );
				update_post_meta( $listing_id, 'geolocation_state_short', $location['state_short'] );
				update_post_meta( $listing_id, 'geolocation_street', $location['street'] );
				update_post_meta( $listing_id, 'geolocation_street_number', $location['street_number'] );
				update_post_meta( $listing_id, 'geolocation_postcode', $location['postcode'] );
			}
		}// End if().

		// needs better error checking
		return true;
	}

	/**
	 * Set a listing as featured.
	 *
	 * @since 1.2.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public static function set_featured( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		// do nothing if this is not relevant to the current object type
		if ( ! in_array( $item_data['post_type'], array( 'job_listing', 'resume' ) ) ) {
			return false;
		}

		$error = new WP_Error(
			'set-featured',
			sprintf( '%s was not set as featured', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$listing_id = $object->ID;

		if ( isset( $item_data['featured'] ) ) {
			global $wpdb;

			$wpdb->update( $wpdb->posts, array(
				'menu_order' => -1,
				), array(
				'ID' => $listing_id,
			) );
			update_post_meta( $listing_id, '_featured', 1 );
		}
	}

}

Astoundify_Plugin_WPJobManager::init();
