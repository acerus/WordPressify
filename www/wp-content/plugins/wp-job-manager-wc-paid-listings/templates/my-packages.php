<?php
/**
 * My Packages
 *
 * Shows packages on the account page
 */
if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}
?>
<h2><?php
if ( 'job_listing' === $type ) {
	echo apply_filters( 'woocommerce_my_account_wc_paid_listings_packages_title', __( 'My Job Packages', 'wp-job-manager-wc-paid-listings' ), $type );
} else {
	echo apply_filters( 'woocommerce_my_account_wc_paid_listings_packages_title', __( 'My Resume Packages', 'wp-job-manager-wc-paid-listings' ), $type );
}
?></h2>

<table class="shop_table my_account_job_packages my_account_wc_paid_listing_packages">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Package Name', 'wp-job-manager-wc-paid-listings' ); ?></th>
			<th scope="col"><?php _e( 'Remaining', 'wp-job-manager-wc-paid-listings' ); ?></th>
			<?php if ( 'job_listing' === $type ) : ?>
				<th scope="col"><?php _e( 'Listing Duration', 'wp-job-manager-wc-paid-listings' ); ?></th>
			<?php endif; ?>
			<th scope="col"><?php _e( 'Featured?', 'wp-job-manager-wc-paid-listings' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $packages as $package ) :
			$package = wc_paid_listings_get_package( $package );
			?>
			<tr>
				<td><?php echo $package->get_title(); ?></td>
				<td><?php echo $package->get_limit() ? absint( $package->get_limit() - $package->get_count() ) : __( 'Unlimited', 'wp-job-manager-wc-paid-listings' ); ?></td>
				<?php if ( 'job_listing' === $type ) : ?>
					<td><?php echo $package->get_duration() ? sprintf( _n( '%d day', '%d days', $package->get_duration(), 'wp-job-manager-wc-paid-listings' ), $package->get_duration() ) : '-'; ?></td>
				<?php endif; ?>
				<td><?php echo $package->is_featured() ? __( 'Yes', 'wp-job-manager-wc-paid-listings' ) : __( 'No', 'wp-job-manager-wc-paid-listings' ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
