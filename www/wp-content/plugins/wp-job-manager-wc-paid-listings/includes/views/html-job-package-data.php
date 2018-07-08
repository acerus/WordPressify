<div class="options_group show_if_job_package show_if_job_package_subscription">
	<?php woocommerce_wp_select( array(
		'id' => '_job_listing_package_subscription_type',
		'wrapper_class' => 'hide_if_job_package show_if_job_package_subscription',
		'label' => __( 'Subscription Type', 'wp-job-manager-wc-paid-listings' ),
		'description' => __( 'Choose how subscriptions affect this package', 'wp-job-manager-wc-paid-listings' ),
		'value' => get_post_meta( $post_id, '_package_subscription_type', true ),
		'desc_tip' => true,
		'options' => array(
			'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-job-manager-wc-paid-listings' ),
			'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-job-manager-wc-paid-listings' ),
		),
	) ); ?>

	<?php woocommerce_wp_text_input( array(
		'id' => '_job_listing_limit',
		'label' => __( 'Job listing limit', 'wp-job-manager-wc-paid-listings' ),
		'description' => __( 'The number of job listings a user can post with this package.', 'wp-job-manager-wc-paid-listings' ),
		'value' => ( $limit = get_post_meta( $post_id, '_job_listing_limit', true ) ) ? $limit : '',
		'placeholder' => __( 'Unlimited', 'wp-job-manager-wc-paid-listings' ),
		'type' => 'number',
		'desc_tip' => true,
		'custom_attributes' => array(
		'min'   => '',
		'step' 	=> '1',
		),
	) ); ?>

	<?php woocommerce_wp_text_input( array(
		'id' => '_job_listing_duration',
		'label' => __( 'Job listing duration', 'wp-job-manager-wc-paid-listings' ),
		'description' => __( 'The number of days that the job listing will be active.', 'wp-job-manager-wc-paid-listings' ),
		'value' => get_post_meta( $post_id, '_job_listing_duration', true ),
		'placeholder' => get_option( 'job_manager_submission_duration' ),
		'desc_tip' => true,
		'type' => 'number',
		'custom_attributes' => array(
		'min'   => '',
		'step' 	=> '1',
		),
	) ); ?>

	<?php woocommerce_wp_checkbox( array(
		'id' => '_job_listing_featured',
		'label' => __( 'Feature Listings?', 'wp-job-manager-wc-paid-listings' ),
		'description' => __( 'Feature this job listing - it will be styled differently and sticky.', 'wp-job-manager-wc-paid-listings' ),
		'value' => get_post_meta( $post_id, '_job_listing_featured', true ),
	) ); ?>

	<script type="text/javascript">
		jQuery(function(){
			jQuery('#product-type').change( function() {
				jQuery('#woocommerce-product-data').removeClass(function(i, classNames) {
					var classNames = classNames.match(/is\_[a-zA-Z\_]+/g);
					if ( ! classNames ) {
						return '';
					}
					return classNames.join(' ');
				});
				jQuery('#woocommerce-product-data').addClass( 'is_' + jQuery(this).val() );
			} );
			jQuery('.pricing').addClass( 'show_if_job_package' );
			jQuery('._tax_status_field').closest('div').addClass( 'show_if_job_package show_if_job_package_subscription' );
			jQuery('.show_if_subscription, .options_group.pricing').addClass( 'show_if_job_package_subscription' );
			jQuery('.options_group.pricing ._regular_price_field').addClass( 'hide_if_job_package_subscription' );
			jQuery('#product-type').change();
			jQuery('#_job_listing_package_subscription_type').change(function(){
				if ( jQuery(this).val() === 'listing' ) {
					jQuery('#_job_listing_duration').closest('.form-field').hide().val('');
				} else {
					jQuery('#_job_listing_duration').closest('.form-field').show();
				}
			}).change();
		});
	</script>
</div>
