<?php

class Jobify_WP_Job_Manager_Map {

	public function __construct() {
		add_filter( 'jobify_listing_data', array( $this, 'job_listing_data' ) );
		add_filter( 'jobify_listing_data', array( $this, 'create_job_listing_data' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'jobify_output_map', array( $this, 'output_map' ) );
		add_action( 'wp_footer', array( $this, 'infobubble_template' ) );
	}

	public function job_listing_data( $data ) {
		global $post, $jobify_job_manager;

		$data = $output = array();

		/** Longitude */
		$long = esc_attr( $post->geolocation_long );

		if ( $long ) {
			$data['longitude'] = $long;
		}

		/** Latitude */
		$lat = esc_attr( $post->geolocation_lat );

		if ( $lat ) {
			$data['latitude'] = $lat;
		}

		/** Title */
		if ( 'job_listing' == $post->post_type ) {
			if ( $post->_company_name && '' !== $post->_company_name ) {
				$data['title'] = sprintf( __( '%1$s at %2$s', 'jobify' ), $post->post_title, $post->_company_name );
			} else {
				$data['title'] = $post->post_title;
			}
		} else {
			$data['title'] = sprintf( __( '%1$s - %2$s', 'jobify' ), $post->post_title, $post->_candidate_title );
		}

		/** Link */
		$data['href'] = get_permalink( $post->ID );

		foreach ( $data as $key => $value ) {
			$output[] .= sprintf( 'data-%s="%s"', $key, $value );
		}

		return $output;
	}

	public function create_job_listing_data( $data ) {
		return implode( ' ', $data );
	}

	public function infobubble_template() {
		locate_template( array( 'tmpl/tmpl-infobubble.php' ), true );
	}

	public function enqueue_scripts() {
		$deps = array(
			'jquery',
			'jquery-ui-slider',
			'google-maps',
			'wp-backbone',
			'wp-job-manager-ajax-filters',
		);

		$deps[] = 'jobify';

		$base = 'https://maps.googleapis.com/maps/api/js';
		$args = array(
			'v' => 3,
			'libraries' => 'geometry,places',
			'language' => get_locale() ? substr( get_locale(), 0, 2 ) : '',
		);

		if ( '' != get_theme_mod( 'map-behavior-api-key', false ) ) {
			$args['key'] = get_theme_mod( 'map-behavior-api-key' );
		}

		wp_enqueue_script( 'google-maps', esc_url_raw( add_query_arg( $args, $base ) ) );
		wp_enqueue_script( 'jobify-app-map', jobify()->get( 'wp-job-manager' )->get_url() . 'js/map/app.min.js', $deps, jobify_get_theme_version(), true );

		$settings = array(
			'useClusters' => (bool) get_theme_mod( 'map-behavior-clusters', 1 ),
			'overlayTitle' => __( '%d Found', 'jobify' ),
			'autoFit' => get_theme_mod( 'map-behavior-autofit', 1 ),
			'trigger' => get_theme_mod( 'map-behavior-trigger', 'mouseover' ),
			'mapOptions' => array(
				'zoom' => get_theme_mod( 'map-behavior-zoom', 3 ),
				'maxZoom' => get_theme_mod( 'map-behavior-max-zoom', 17 ),
				'maxZoomOut' => get_theme_mod( 'map-behavior-max-zoom-out', 3 ),
				'gridSize' => get_theme_mod( 'map-behavior-grid-size', 60 ),
				'scrollwheel' => get_theme_mod( 'map-behavior-scrollwheel', 'on' ) == 'on' ? true : false,
			),
		);

		if ( '' != ( $center = get_theme_mod( 'map-behavior-center', '' ) ) ) {
			$settings['mapOptions']['center'] = array_map( 'trim', explode( ',', $center ) );
		}

		if ( has_filter( 'job_manager_geolocation_region_cctld' ) ) {
			$settings['autoComplete']['componentRestrictions'] = array(
				'country' => $bias,
			);
		}

		$settings = apply_filters( 'jobify_map_settings', $settings );

		wp_localize_script( 'jobify-app-map', 'jobifyMapSettings', apply_filters( 'jobify_map_settings', $settings ) );
	}

	public function output_map( $type = false ) {
		if ( ! $type ) {
			$type = 'job_listing';
		}

		$map = locate_template( array( 'content-job_listing-map.php' ), false, false );

		include( $map );
	}

	public function job_manager_get_listings_custom_filter_text( $text ) {
		$params = array();

		parse_str( $_POST['form_data'], $params );

		if ( ! isset( $params['search_lat'] ) || '' == $params['search_lat'] ) {
			return $text;
		}

		$text .= ' ' . sprintf( __( 'within a %d mile radius', 'jobify' ), $params['search_radius'] );

		return $text;
	}

}
