<?php
/**
 * Handle individual listing data for WP Job Manager.
 *
 * @since 3.8.0
 */
class Jobify_WP_Job_Manager_Listing extends Jobify_Listing {

	/**
	 * Featured Image
	 *
	 * @since 3.8.0
	 *
	 * @param string $size Size of image to load.
	 * @return string $image
	 */
	public function get_featured_image( $size = 'thumbnail' ) {
		$image = $this->get_object()->_featured_image;

		if ( ! $image ) {
			return;
		}

		// @codingStandardsIgnoreStart
		$image = attachment_url_to_postid( $image );
		// @codingStandardsIgnoreEnd

		if ( $image ) {
			return wp_get_attachment_image( $image, $size, false );
		}

		return false;
	}

	/**
	 * Permalink
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_permalink() {
		return get_the_job_permalink( $this->get_object() );
	}

	/**
	 * Get Posted Date
	 *
	 * @since 3.8.0
	 *
	 * $return string Date based on option.
	 */
	public function get_posted_date() {
		return get_the_job_publish_date( $this->get_object() );
	}

	/**
	 * Get Expiry Date
	 *
	 * @since 3.8.0
	 *
	 * @return string Date in YYYY-MM-DD format
	 */
	public function get_expiry_date() {
		$expiry_time = $this->get_object()->_job_expires;

		return $expiry_time ? date_i18n( 'Y-m-d', strtotime( $expiry_time ) ) : '';
	}

	/**
	 * Is position filled?
	 *
	 * @since 3.8.0
	 *
	 * @return bool
	 */
	public function is_position_filled() {
		return is_position_filled( $this->get_object() );
	}

	/**
	 * Return whether or not the position has been featured
	 *
	 * @since 3.8.0
	 *
	 * @return boolean
	 */
	function is_position_featured() {
		return is_position_featured( $this->get_object() );
	}

	/**
	 * Application Point
	 *
	 * @since 3.8.0
	 *
	 * @return string can be an email or URL
	 */
	public function get_the_application_point() {
		$point = $this->get_object()->_application;

		if ( is_email( $point ) ) {
			return $point;
		}

		return esc_url( $point );
	}

	/**
	 * Get Company Name
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_company_name() {
		return get_the_company_name( $this->get_object() );
	}

	/**
	 * Get Company Tagline
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_company_tagline() {
		return get_the_company_tagline( $this->get_object() );
	}

	/**
	 * Get Company Description
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_company_description() {
		return apply_filters( 'the_company_description', $this->get_object()->_company_description, $this->get_object() );
	}

	/**
	 * Get Company Logo
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_company_logo( $size = 'thumbnail' ) {
		return get_the_company_logo( $this->get_object(), $size );
	}

	/**
	 * Get Company Video
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_company_video() {
		return get_the_company_video( $this->get_object() );
	}

	/**
	 * Get Company Website
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_company_website() {
		return esc_url( get_the_company_website( $this->get_object() ) );
	}

	/**
	 * Get Company Twitter
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_company_twitter() {
		return get_the_company_twitter( $this->get_object() );
	}

	/**
	 * Get Company Facebook
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_company_facebook() {
		$company_facebook = $this->get_object()->_company_facebook;

		if ( $company_facebook && filter_var( $company_facebook, FILTER_VALIDATE_URL ) === false ) {
			$company_facebook = 'http://facebook.com/' . $company_facebook;
		}

		return apply_filters( 'the_company_facebook', $company_facebook, $this->get_object() );
	}

	/**
	 * Get Company Google Plus
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_company_gplus() {
		$company_google = $this->get_object()->_company_google;

		if ( $company_google && filter_var( $company_google, FILTER_VALIDATE_URL ) === false ) {
			$company_google = 'http://plus.google.com/' . $company_google;
		}

		return apply_filters( 'the_company_google', $company_google, $this->get_object() );
	}

	/**
	 * Get Company LinkedIn
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_company_linkedin() {
		$company_linkedin = $this->get_object()->_company_linkedin;

		if ( $company_linkedin && filter_var( $company_linkedin, FILTER_VALIDATE_URL ) === false ) {
			$company_linkedin = 'http://linkedin.com/company/' . $company_linkedin;
		}

		return apply_filters( 'the_company_linkedin', $company_linkedin, $this->get_object() );
	}

	/**
	 * Get Listing Location
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_location() {
		return get_the_job_location( $this->get_object() );
	}

	/**
	 * Get Listing Location Data
	 *
	 * @since 3.8.0
	 *
	 * @return array
	 */
	public function get_location_data() {
		$data = array(
			'street_number' => $this->get_object()->geolocation_street_number,
			'address_1'     => $this->get_object()->geolocation_street,
			'address_2'     => '',
			'city'          => $this->get_object()->geolocation_city,
			'state'         => $this->get_object()->geolocation_state_short,
			'full_state'    => $this->get_object()->geolocation_state_long,
			'postcode'      => $this->get_object()->geolocation_postcode,
			'country'       => $this->get_object()->geolocation_country_short,
			'full_country'  => $this->get_object()->geolocation_country_long,
			'latitude'      => $this->get_object()->geolocation_lat,
			'longitude'     => $this->get_object()->geolocation_long,
		);
		return apply_filters( 'jobify_location_data', $data, $this->get_object() );
	}

	/**
	 * Get The Job Types
	 *
	 * @since 3.8.0
	 *
	 * @return array
	 */
	public function get_the_job_types() {
		if ( ! get_option( 'job_manager_enable_types', true ) ) {
			return array();
		}
		$types = array();
		$_types = wpjm_get_the_job_types( $this->get_object() );

		if ( $_types && ! is_wp_error( $_types ) ) {
			$types = $_types;
		}

		return $types;
	}

	/**
	 * Get The Job Type
	 *
	 * @since 3.8.0
	 *
	 * @return object|bool
	 */
	public function get_the_job_type() {
		if ( ! get_option( 'job_manager_enable_types', true ) ) {
			return false;
		}
		return parent::get_the_job_type();
	}

	/**
	 * The Job Type
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_job_type_names() {
		$_types = $this->get_the_job_types();
		$types = array();

		if ( $_types ) {
			foreach ( $_types as $type ) {
				$types[ $type->slug ] = $type->name;
			}
		}

		return implode( ', ', $types );
	}

	/**
	 * Get The Job Category
	 *
	 * @since 3.8.0
	 *
	 * @return array
	 */
	public function get_the_job_category() {
		$_categories = wp_get_post_terms( $this->get_id(), 'job_listing_category' );
		$categories = array();

		if ( ! is_wp_error( $_categories ) && $_categories ) {
			$categories = $_categories;
		}

		return $categories;
	}

	/**
	 * Get The Job Category
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_the_job_category_names() {
		$_categories = $this->get_the_job_category();
		$categories = array();

		if ( $_categories ) {
			foreach ( $_categories as $category ) {
				$categories[ $category->slug ] = $category->name;
			}
		}

		return implode( ', ', $categories );
	}

}
