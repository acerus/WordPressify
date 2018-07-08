<?php if ( $packages || $user_packages ) :
	$checked = 1;
	?>
	<ul class="job_packages">
		<?php if ( $user_packages ) : ?>
			<li class="package-section"><?php _e( 'Your Packages:', 'wp-job-manager-wc-paid-listings' ); ?></li>
			<?php foreach ( $user_packages as $key => $package ) :
				$package = wc_paid_listings_get_package( $package );
				?>
				<li class="user-job-package">
					<input type="radio" <?php checked( $checked, 1 ); ?> name="job_package" value="user-<?php echo $key; ?>" id="user-package-<?php echo $package->get_id(); ?>" />
					<label for="user-package-<?php echo $package->get_id(); ?>"><?php echo $package->get_title(); ?></label><br/>
					<?php
					if ( $package->get_limit() ) {
						printf( _n( '%1$s job posted out of %2$d', '%1$s jobs posted out of %2$d', $package->get_count(), 'wp-job-manager-wc-paid-listings' ), $package->get_count(), $package->get_limit() );
					} else {
						printf( _n( '%s job posted', '%s jobs posted', $package->get_count(), 'wp-job-manager-wc-paid-listings' ), $package->get_count() );
					}

					if ( $package->get_duration() ) {
						printf( ', ' . _n( 'listed for %s day', 'listed for %s days', $package->get_duration(), 'wp-job-manager-wc-paid-listings' ), $package->get_duration() );
					}

						$checked = 0;
					?>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if ( $packages ) : ?>
			<li class="package-section"><?php _e( 'Purchase Package:', 'wp-job-manager-wc-paid-listings' ); ?></li>
			<?php foreach ( $packages as $key => $package ) :
				$product = wc_get_product( $package );
				if ( ! $product->is_type( array( 'job_package', 'job_package_subscription' ) ) || ! $product->is_purchasable() ) {
					continue;
				}
				/* @var $product WC_Product_Job_Package|WC_Product_Job_Package_Subscription */
				if ( $product->is_type( 'variation' ) ) {
					$post = get_post( $product->get_parent_id() );
				} else {
					$post = get_post( $product->get_id() );
				}
				?>
				<li class="job-package">
					<input type="radio" <?php checked( $checked, 1 );
					$checked = 0; ?> name="job_package" value="<?php echo $product->get_id(); ?>" id="package-<?php echo $product->get_id(); ?>" />
					<label for="package-<?php echo $product->get_id(); ?>"><?php echo $product->get_title(); ?></label><br/>
					<?php if ( ! empty( $post->post_excerpt ) ) : ?>
						<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
					<?php else :
						printf( _n( '%1$s for %2$s job', '%1$s for %2$s jobs', $product->get_limit(), 'wp-job-manager-wc-paid-listings' ) . ' ', $product->get_price_html(), $product->get_limit() ? $product->get_limit() : __( 'unlimited', 'wp-job-manager-wc-paid-listings' ) );
						echo $product->get_duration() ? sprintf( _n( 'listed for %s day', 'listed for %s days', $product->get_duration(), 'wp-job-manager-wc-paid-listings' ), $product->get_duration() ) : '';
					endif; ?>
				</li>

			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
<?php else : ?>

	<p><?php _e( 'No packages found', 'wp-job-manager-wc-paid-listings' ); ?></p>

<?php endif; ?>
