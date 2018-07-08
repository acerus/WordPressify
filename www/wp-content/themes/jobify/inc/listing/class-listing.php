<?php
/**
 * Handle individual listing data.
 *
 * This class implements WordPress-level data management
 * but does not interface with any 3rd party plugins directly.
 *
 * @since 3.7.0
 */
abstract class Jobify_Listing {

	/**
	 * The associated WordPress post object.
	 *
	 * @since 3.7.0
	 * @var WP_Post $post
	 */
	protected $post;

	/**
	 * Load a new instance of a listing.
	 *
	 * @since 3.7.0
	 *
	 * @param null|int|WP_Post
	 */
	public function __construct( $post ) {
		if ( ! $post || is_int( $post ) ) {
			$this->post = get_post( $post );
		} elseif ( is_a( $post, 'WP_Post' ) ) {
			$this->post = $post;
		}
	}

	/**
	 * Listing ID
	 *
	 * @since 3.7.0
	 *
	 * @return int
	 */
	public function get_id() {
		if ( $this->get_object() ) {
			return $this->get_object()->ID;
		}
		return false;
	}

	/**
	 * Associated listing object
	 *
	 * @since 3.7.0
	 */
	public function get_object() {
		return $this->post;
	}

	/**
	 * Status
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->get_object()->post_status;
	}

	/**
	 * Title
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	public function get_title() {
		return get_the_title( $this->get_id() );
	}

	/**
	 * Short Description
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	public function get_short_description() {
		return wp_trim_words( $this->get_object()->post_content, 55 );
	}

	/**
	 * Permalink
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	public function get_permalink() {
		return get_permalink( $this->get_object() );
	}

	/**
	 * Get Posted Date
	 *
	 * @since 3.7.0
	 *
	 * $return string Date in YYYY-MM-DD format
	 */
	abstract public function get_posted_date();

	/**
	 * Get Expiry Date
	 *
	 * @since 3.7.0
	 *
	 * $return string Date in YYYY-MM-DD format
	 */
	abstract public function get_expiry_date();

	/**
	 * HTML Class
	 *
	 * @since 3.7.0
	 *
	 * @param string|array $class
	 * @return array
	 */
	public function get_html_class( $class = '' ) {
		$classes = array();
		$object = $this->get_object();

		if ( empty( $object ) ) {
			return $classes;
		}

		$classes[] = 'job_listing';

		if ( $this->get_the_job_types() ) {
			foreach ( $this->get_the_job_types() as $type ) {
				$classes[] = 'job-type-' . sanitize_title( $type->slug );
			}
		}

		if ( $this->is_position_filled() ) {
			$classes[] = 'job_position_filled';
		}

		if ( is_position_featured() ) {
			$classes[] = 'job_position_featured';
		}

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_merge( $classes, $class );
		}

		return get_post_class( $classes, $this->get_id() );
	}

	/**
	 * Featured Image
	 *
	 * @since 3.7.0
	 *
	 * @param string $size Image size.
	 * @return false|string Image HTML or false if no image.
	 */
	abstract public function get_featured_image( $size = 'thumbnail' );

	/**
	 * Is Position Filled
	 *
	 * @since 3.7.0
	 *
	 * @return bool
	 */
	abstract public function is_position_filled();

	/**
	 * Return whether or not the position has been featured
	 *
	 * @since 3.7.0
	 *
	 * @param  object $post
	 * @return boolean
	 */
	abstract function is_position_featured();

	/**
	 * Application Point
	 *
	 * @since 3.7.0
	 *
	 * @return string can be an email or URL
	 */
	abstract public function get_the_application_point();

	/**
	 * Get Company Name
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	abstract public function get_the_company_name();

	/**
	 * Get Company Tagline
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	abstract public function get_the_company_tagline();

	/**
	 * Get Company Description
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	abstract public function get_the_company_description();

	/**
	 * Get Company Logo
	 *
	 * @since 3.7.0
	 *
	 * @param string $size Image size.
	 * @return string
	 */
	abstract public function get_the_company_logo( $size = 'thumbnail' );

	/**
	 * Get Company Video
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	abstract public function get_the_company_video();

	/**
	 * Get Company Website
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	abstract public function get_the_company_website();

	/**
	 * Get Company Twitter
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	abstract public function get_the_company_twitter();

	/**
	 * Get Company Facebook
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	abstract public function get_the_company_facebook();

	/**
	 * Get Company Google Plus
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	abstract public function get_the_company_gplus();

	/**
	 * Get Company LinkedIn
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	abstract public function get_the_company_linkedin();

	/**
	 * Get Listing Location
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	abstract public function get_location();

	/**
	 * Get Listing Location Data
	 *
	 * @since 3.7.0
	 *
	 * @return array
	 */
	abstract public function get_location_data();

	/**
	 * Get The Job Type (Deprecated)
	 *
	 * @since 3.7.0
	 * @deprecated 3.8.0
	 *
	 * @return object|bool
	 */
	public function get_the_job_type() {
		$types = $this->get_the_job_types();

		if ( $types ) {
			return current( $types );
		}

		return false;
	}

	/**
	 * The Job Type Name (Deprecated)
	 *
	 * @since 3.7.0
	 * @deprecated 3.8.0
	 *
	 * @return string
	 */
	public function get_the_job_type_name() {
		$type = $this->get_the_job_type();

		if ( $type ) {
			return $type->name;
		}

		return false;
	}

	/**
	 * Get The Job Types
	 *
	 * @since 3.7.0
	 * @deprecated 3.8.0
	 *
	 * @return object|bool
	 */
	abstract public function get_the_job_types();

	/**
	 * The Job Type Names
	 *
	 * @since 3.7.0
	 * @deprecated 3.8.0
	 *
	 * @return string
	 */
	abstract public function get_the_job_type_names();

	/**
	 * Get The Job Category
	 *
	 * @since 3.7.0
	 *
	 * @return array
	 */
	abstract public function get_the_job_category();

	/**
	 * Get The Job Category
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	abstract public function get_the_job_category_names();

}
