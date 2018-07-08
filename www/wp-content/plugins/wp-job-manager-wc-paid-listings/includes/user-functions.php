<?php
/**
 * Get a users packages from the DB
 *
 * @param  int          $user_id
 * @param string|array $package_type
 * @return array of objects
 */
function wc_paid_listings_get_user_packages( $user_id, $package_type = '' ) {
	global $wpdb;

	if ( empty( $package_type ) ) {
		$package_type = array( 'resume', 'job_listing' );
	} else {
		$package_type = array( $package_type );
	}

	$packages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE user_id = %d AND package_type IN ( '" . implode( "','", $package_type ) . "' ) AND ( package_count < package_limit OR package_limit = 0 );", $user_id ), OBJECT_K );

	return $packages;
}

/**
 * Get a package
 *
 * @param  int $package_id
 * @return WC_Paid_Listings_Package
 */
function wc_paid_listings_get_user_package( $package_id ) {
	global $wpdb;

	$package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE id = %d;", $package_id ) );
	return wc_paid_listings_get_package( $package );
}

/**
 * Give a user a package
 *
 * @param  int $user_id
 * @param  int $product_id
 * @param  int $order_id
 * @return int|bool false
 */
function wc_paid_listings_give_user_package( $user_id, $product_id, $order_id = 0 ) {
	global $wpdb;

	$package = wc_get_product( $product_id );

	if ( ! $package->is_type( 'job_package' ) && ! $package->is_type( 'resume_package' ) && ! $package->is_type( 'job_package_subscription' ) && ! $package->is_type( 'resume_package_subscription' ) ) {
		return false;
	}

	$is_featured = false;
	if ( $package instanceof WC_Product_Job_Package || $package instanceof WC_Product_Job_Package_Subscription ) {
		$is_featured = $package->is_job_listing_featured();
	} elseif ( $package instanceof WC_Product_Resume_Package || $package instanceof WC_Product_Resume_Package_Subscription ) {
		$is_featured = $package->is_resume_featured();
	}

	$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}wcpl_user_packages WHERE
		user_id = %d
		AND product_id = %d
		AND order_id = %d
		AND package_duration = %d
		AND package_limit = %d
		AND package_featured = %d
		AND package_type = %d",
		$user_id,
		$product_id,
		$order_id,
		$package->get_duration(),
		$package->get_limit(),
		$is_featured ? 1 : 0,
	$package->is_type( array( 'resume_package', 'resume_package_subscription' ) ) ? 'resume' : 'job_listing' ) );

	if ( $id ) {
		return $id;
	}

	$wpdb->insert(
		"{$wpdb->prefix}wcpl_user_packages",
		array(
			'user_id'          => $user_id,
			'product_id'       => $product_id,
			'order_id'         => $order_id,
			'package_count'    => 0,
			'package_duration' => $package->get_duration(),
			'package_limit'    => $package->get_limit(),
			'package_featured' => $is_featured ? 1 : 0,
			'package_type'     => $package->is_type( array( 'resume_package', 'resume_package_subscription' ) ) ? 'resume' : 'job_listing',
		)
	);

	return $wpdb->insert_id;
}

/**
 * Get customer ID from Order
 *
 * @param WC_Order $order
 * @return int
 */
function wc_paid_listings_get_order_customer_id( $order ) {
	if ( WC_Paid_Listings::is_woocommerce_pre( '3.0.0' ) ) {
		return $order->customer_user;
	}
	return $order->get_customer_id();
}

/**
 * Get customer ID from Order
 *
 * @param WC_Order $order
 * @return int
 */
function wc_paid_listings_get_order_id( $order ) {
	if ( WC_Paid_Listings::is_woocommerce_pre( '3.0.0' ) ) {
		return $order->id;
	}
	return $order->get_id();
}

/**
 * @deprecated
 */
function get_user_job_packages( $user_id ) {
	return wc_paid_listings_get_user_packages( $user_id, 'job_listing' );
}

/**
 * @deprecated
 */
function get_user_job_package( $package_id ) {
	return wc_paid_listings_get_user_package( $package_id );
}

/**
 * @deprecated
 */
function give_user_job_package( $user_id, $product_id ) {
	return wc_paid_listings_give_user_package( $user_id, $product_id );
}

/**
 * @deprecated
 */
function user_job_package_is_valid( $user_id, $package_id ) {
	return wc_paid_listings_package_is_valid( $user_id, $package_id );
}

/**
 * @deprecated
 */
function increase_job_package_job_count( $user_id, $package_id ) {
	wc_paid_listings_increase_package_count( $user_id, $package_id );
}

/**
 * Get listing IDs for a user package
 *
 * @return array
 */
function wc_paid_listings_get_listings_for_package( $user_package_id ) {
	global $wpdb;

	return $wpdb->get_col( $wpdb->prepare(
		"SELECT post_id FROM {$wpdb->postmeta} " .
		"LEFT JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID " .
		"WHERE meta_key = '_user_package_id' " .
		'AND meta_value = %s;'
	, $user_package_id ) );
}
