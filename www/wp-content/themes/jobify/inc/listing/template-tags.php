<?php
/**
 * Listing Template Tags
 *
 * @since 3.7.0
 *
 * @package Jobify
 * @category Template
 * @author Astoundify
 */

/**
 * Return the current version of the parent theme.
 *
 * @since 3.8.6
 *
 * @return string
 */
function jobify_get_theme_version() {
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		return time();
	}

	$version = wp_get_theme()->Version;

	if ( wp_get_theme()->parent() ) {
		$version = wp_get_theme()->parent()->Version;
	}

	return $version;
}

/**
 * Get listing permalink
 *
 * @since 3.7.0
 *
 * @return string|bool
 */
function jobify_get_listing_page_permalink() {
	if ( function_exists( 'job_manager_get_permalink' ) ) {
		return job_manager_get_permalink( 'jobs' );
	}

	return false;
}

/**
 * Get submit listing permalink
 *
 * @since 3.7.0
 *
 * @return string|bool
 */
function jobify_get_submit_listing_page_permalink() {
	if ( function_exists( 'job_manager_get_permalink' ) ) {
		return job_manager_get_permalink( 'submit_job_form' );
	}

	return false;
}

/**
 * Get a listing.
 *
 * @since 3.7.0
 *
 * @param null|int|WP_Post $post ID or existing object to find a listing.
 * @return false|object
 */
function jobify_get_listing( $post = null ) {
	$factory = new Jobify_Listing_Factory();
	$listing = $factory->get_listing( $post );

	return $listing;
}

/**
 * Listing Permalink function.
 *
 * @since 3.7.0
 *
 * @param mixed $post (default: null).
 * @return void
 */
function jobify_listing_permalink( $post = null ) {
	echo jobify_get_listing_permalink( $post ); // WPCS: XSS ok.
}

/**
 * Listing HTML Class
 *
 * @since 3.7.0
 *
 * @param string|array $class Default class to add.
 * @param mixed        $post (default: null).
 * @return void
 */
function jobify_listing_html_class( $class = '', $post = null ) {
	echo 'class="' . join( ' ', jobify_get_listing_html_class( $class, $post ) ) . '"';
}

/**
 * Get the permalink to a listing.
 *
 * @access public
 * @param mixed $post (default: null).
 * @return string
 */
function jobify_get_listing_permalink( $post = null ) {
	return jobify_get_listing( $post )->get_permalink();
}

/**
 * Get Posted Date
 *
 * @since 3.8.0
 */
function jobify_get_posted_date( $post = null ) {
	// A timestamp is passed directly -- don't reference a post object.
	if ( $post && ! is_a( 'WP_Post', $post ) ) {
		$date_format = get_option( 'job_manager_date_format' );
		$timestamp = $post;

		if ( 'default' === $date_format ) {
			return date( get_option( 'date_format' ), $timestamp );
		} else {
			// Translators: %s human time.
			return sprintf( __( 'Posted %s ago', 'jobify' ), human_time_diff( $timestamp, current_time( 'timestamp' ) ) );
		}
	}

	return jobify_get_listing( $post )->get_posted_date();
}

/**
 * Get Listing HTML Class
 *
 * @since 3.7.0
 *
 * @param string|array $class Default class to add.
 * @param mixed        $post (default: null).
 * @return array
 */
function jobify_get_listing_html_class( $class = '', $post = null ) {
	return jobify_get_listing( $post )->get_html_class( $class );
}

/**
 * Return whether or not the position has been marked as filled
 *
 * @since 3.7.0
 *
 * @param mixed $post (default: null).
 * @return boolean
 */
function jobify_is_listing_position_filled( $post = null ) {
	return apply_filters( 'jobify_is_listing_position_filled', jobify_get_listing( $post )->is_position_filled() );
}

/**
 * Get a single job type (first).
 *
 * @access public
 * @param mixed $post (default: null).
 * @return array|bool
 */
function jobify_get_the_job_type( $post = null ) {
	return jobify_get_listing( $post )->get_the_job_type();
}

/**
 * Get all job types.
 *
 * @since 3.8.0
 *
 * @access public
 * @param mixed $post (default: null).
 * @return array|bool
 */
function jobify_get_the_job_types( $post = null ) {
	return jobify_get_listing( $post )->get_the_job_types();
}

/**
 * the_job_type function.
 *
 * @access public
 * @return void
 */
function jobify_the_job_type( $post = null ) {
	echo jobify_get_listing( $post )->get_the_job_type_names();
}

/**
 * The company featured image
 *
 * @since 3.0.0
 *
 * @param string $size
 * @param object $post
 * @return string $image
 */
function jobify_get_the_featured_image( $size = 'content-job-featured', $post = null ) {
	return jobify_get_listing( $post )->get_featured_image( $size );
}

/**
 * Get the company name.
 *
 * @since Jobify 1.0
 *
 * @return string
 */
function jobify_get_the_company_name( $post = null ) {
	return jobify_get_listing( $post )->get_the_company_name();
}

/**
 * Display or retrieve the current company name with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function jobify_the_company_name( $before = '', $after = '', $echo = true, $post = null ) {
	$company_name = jobify_get_the_company_name( $post );

	if ( strlen( $company_name ) == 0 ) {
		return;
	}

	$company_name = esc_attr( strip_tags( $company_name ) );
	$company_name = $before . $company_name . $after;

	if ( $echo ) {
		echo $company_name;
	} else {
		return $company_name;
	}
}

/**
 * get_the_company_tagline function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function jobify_get_the_company_tagline( $post = null ) {
	return jobify_get_listing( $post )->get_the_company_tagline();
}

/**
 * Display or retrieve the current company tagline with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void|bool|string
 */
function jobify_the_company_tagline( $before = '', $after = '', $echo = true, $post = null ) {
	$company_tagline = jobify_get_the_company_tagline( $post );

	if ( strlen( $company_tagline ) == 0 ) {
		return false;
	}

	$company_tagline = esc_attr( strip_tags( $company_tagline ) );
	$company_tagline = $before . $company_tagline . $after;

	if ( $echo ) {
		echo $company_tagline;
	} else {
		return $company_tagline;
	}
}

/**
 * Get the company description.
 *
 * @since Jobify 1.0
 *
 * @return string
 */
function jobify_get_the_company_description( $post = null ) {
	return jobify_get_listing( $post )->get_the_company_description();
}


/**
 * The Company Description template tag.
 *
 * @since Jobify 1.0
 *
 * @param string $before
 * @param string $after
 * @return void
 */
function jobify_the_company_description( $before = '', $after = '', $post = null ) {
	$company_description = jobify_get_the_company_description( $post );

	if ( strlen( $company_description ) == 0 ) {
		return;
	}

	$company_description = wp_kses_post( $company_description );
	$company_description = $before . wpautop( $company_description ) . $after;

	echo $company_description;
}

/**
 * Get the company logo
 *
 * @since 3.7.0
 *
 * @param string $size
 * @return string
 */
function jobify_get_the_company_logo( $size = 'thumbnail', $post = null ) {
	return jobify_get_listing( $post )->get_the_company_logo( $size );
}

/**
 * The Company Logo
 *
 * @since 3.7.0
 */
function jobify_the_company_logo( $size = 'thumbnail', $default = null, $post = null ) {
	$logo = false;

	if ( has_post_thumbnail( $post ) ) {
		$logo = jobify_get_the_company_logo( $size, $post );
	} elseif ( ! empty( $logo ) && ( strstr( $logo, 'http' ) || file_exists( $logo ) ) ) {
		if ( $size !== 'full' && function_exists( 'job_manager_get_resized_image' ) ) {
			$logo = job_manager_get_resized_image( $logo, $size );
		}
	} elseif ( $default ) {
		$logo = $default;
	} elseif ( defined( 'JOB_MANAGER_PLUGIN_URL' ) ) {
		$logo = apply_filters( 'job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png' );
	}

	$logo = apply_filters( 'jobify_company_logo_url', $logo );

	echo '<img class="company_logo" src="' . esc_url( $logo ) . '" alt="' . esc_attr( jobify_get_the_company_name( $post ) ) . '" />';
}


/**
 * Get the Company Video
 *
 * @since 3.7.0
 *
 * @return string
 */
function jobify_get_the_company_video( $post = null ) {
	return jobify_get_listing( $post )->get_the_company_video();
}

/**
 * The Company Video (Embed)
 *
 * @since 3.7.0
 */
function jobify_the_company_video( $post = null ) {
	$video_embed = false;
	$video       = jobify_get_the_company_video( $post );
	$filetype    = wp_check_filetype( $video );

	if ( ! empty( $video ) ) {
		// FV Wordpress Flowplayer Support for advanced video formats
		if ( shortcode_exists( 'flowplayer' ) ) {
			$video_embed = '[flowplayer src="' . esc_attr( $video ) . '"]';
		} elseif ( ! empty( $filetype['ext'] ) ) {
			$video_embed = wp_video_shortcode( array(
				'src' => $video,
			) );
		} else {
			$video_embed = wp_oembed_get( $video );
		}
	}

	$video_embed = apply_filters( 'the_company_video_embed', $video_embed, $post );

	if ( $video_embed ) {
		echo '<div class="company_video">' . $video_embed . '</div>';
	}
}

/**
 * Get the Company Website
 *
 * @since 3.7.0
 *
 * @return string $company_twitter
 */
function jobify_get_the_company_website( $post = null ) {
	return jobify_get_listing( $post )->get_the_company_website();
}

/**
 * Get the Company Twitter
 *
 * @since 3.0.0
 *
 * @return string $company_twitter
 */
function jobify_get_the_company_twitter( $post = null ) {
	return jobify_get_listing( $post )->get_the_company_twitter();
}

/**
 * Get the Company Facebook
 *
 * @since Jobify 1.0
 *
 * @return string
 */
function jobify_get_the_company_facebook( $post = null ) {
	return jobify_get_listing( $post )->get_the_company_facebook();
}

/**
 * Get the Company Google Plus
 *
 * @since Jobify 1.0
 *
 * @return string
 */
function jobify_get_the_company_gplus( $post = null ) {
	return jobify_get_listing( $post )->get_the_company_gplus();
}

/**
 * Get the Company LinkedIn
 *
 * @since Jobify 1.6.0
 *
 * @return string
 */
function jobify_get_the_company_linkedin( $post = null ) {
	return jobify_get_listing( $post )->get_the_company_linkedin();
}

/**
 * Get location data
 *
 * @since 3.7.0
 *
 * @return array
 */
function jobify_get_location_data( $post = null ) {
	return jobify_get_listing( $post )->get_location_data();
}

/**
 * Get listing location function.
 *
 * @since 3.7.0
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function jobify_get_the_job_location( $post = null ) {
	$location = jobify_get_listing( $post )->get_location();

	if ( ! $location ) {
		return wp_kses_post( apply_filters( 'the_job_location_anywhere_text', __( 'Anywhere', 'jobify' ) ) );
	}

	return $location;
}

/**
 * Get location formatted address
 *
 * @since 3.7.0
 *
 * @return string
 */
function jobify_get_formatted_address( $post = null, $format = false ) {
	/* Get location */
	$data = jobify_get_location_data( $post );
	$full = jobify_get_the_job_location( $post );

	/* Filter: for back compat */
	$data = apply_filters( 'jobify_formatted_address', $data );

	/* Get Address format from theme mod if not set */
	$format = $format ? $format : get_theme_mod( 'job-display-address-format', '{city}, {state}' );

	// Allow to be set to blank to not autoformat.
	$format = '' === $format ? false : $format;

	$location = jobify_format_address( $data, $format );

	// Only link if there is location data.
	if ( jobify_get_listing( $post )->get_location() ) {
		$location = apply_filters( 'the_job_location_map_link', '<a class="google_map_link" href="' . esc_url( 'http://maps.google.com/maps?q=' . urlencode( strip_tags( $full ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ) . '" target="_blank">' . esc_html( strip_tags( $location ) ) . '</a>', $location, $post );
	}

	return $location;
}

/**
 * Jobify Format Address
 * This function will return a formatted address from location data.
 *
 * @since 3.7.0
 *
 * @param array  $data Address/location datas
 * @param string $format Address format using tags {address_1}, {city}, etc
 * @return string $formatted_address
 */
function jobify_format_address( $data, $format ) {
	/* No format, bail */
	if ( ! $format ) {
		return jobify_get_the_job_location();
	}

	/* Set default data */
	$default_args = array(
		'street_number' => '',
		'address_1'     => '',
		'address_2'     => '',
		'city'          => '',
		'state'         => '',
		'full_state'    => '',
		'postcode'      => '',
		'country'       => '',
		'full_country'  => '',
	);

	$data = array_map( 'trim', wp_parse_args( $data, $default_args ) );

	/* Extract args */
	extract( $data );

	/* Substitute address parts into the string */
	$replace = array(
		'{street_number}'    => $street_number,
		'{address_1}'        => $address_1,
		'{address_2}'        => $address_2,
		'{city}'             => $city,
		'{state}'            => $full_state,
		'{postcode}'         => $postcode,
		'{country}'          => $full_country,
		'{address_1_upper}'  => strtoupper( $address_1 ),
		'{address_2_upper}'  => strtoupper( $address_2 ),
		'{city_upper}'       => strtoupper( $city ),
		'{state_upper}'      => strtoupper( $full_state ),
		'{state_code}'       => strtoupper( $state ),
		'{postcode_upper}'   => strtoupper( $postcode ),
		'{country_upper}'    => strtoupper( $full_country ),
	);

	/* Sanitize */
	$replace = array_map( 'esc_html', $replace );

	/* Replace */
	$formatted_address = str_replace( array_keys( $replace ), $replace, $format );

	// See if there is anything added.
	$valid = str_replace( array( ' ', ',' ), '', $formatted_address );

	if ( '' == $valid ) {
		return jobify_get_the_job_location();
	}

	return $formatted_address;
}

/**
 * Array Filter Deep Helper
 */
function jobify_array_filter_deep( $item ) {
	if ( is_array( $item ) ) {
		return array_filter( $item, 'jobify_array_filter_deep' );
	}

	if ( ! empty( $item ) ) {
		return true;
	}
}
