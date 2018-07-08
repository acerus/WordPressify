<?php
add_filter( 'job_manager_job_listing_affects_package_count', 'wpjm_wcpl_wpml_check_job_listing_for_package_count', 10, 2 );

/**
 * Checks with WPML if the job listing is the original before counting it in package.
 *
 * @since 2.7.3
 *
 * @param bool $job_listing_affects_package_count
 * @param int $listing_id
 *
 * @return bool
 */
function wpjm_wcpl_wpml_check_job_listing_for_package_count( $job_listing_affects_package_count, $listing_id) {
	$trid = apply_filters( 'wpml_element_trid', null, $listing_id, 'post_job_listing' );
	if ( $trid ) {
		$translations = apply_filters( 'wpml_get_element_translations', null, $trid, 'post_job_listing' );
		if ( ! empty( $translations ) ) {
			foreach ( $translations as $translation ) {
				if ( (int) $listing_id === (int) $translation->element_id ) {
					$job_listing_affects_package_count = ! empty( $translation->original );
					break;
				}
			}
		}
	}
	return $job_listing_affects_package_count;
}
