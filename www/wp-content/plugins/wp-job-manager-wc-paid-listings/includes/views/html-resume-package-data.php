<?php

if ( is_plugin_active( 'wp-job-manager-resumes/wp-job-manager-resumes.php' ) ) { ?>

	<div class="options_group show_if_resume_package show_if_resume_package_subscription">
		<?php woocommerce_wp_select( array(
			'id' => '_resume_package_subscription_type',
			'wrapper_class' => 'hide_if_resume_package show_if_resume_package_subscription',
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
			'id' => '_resume_limit',
			'label' => __( 'Resume posting limit', 'wp-job-manager-wc-paid-listings' ),
			'description' => __( 'The number of resumes a user can post with this package.', 'wp-job-manager-wc-paid-listings' ),
			'value' => ( $limit = get_post_meta( $post_id, '_resume_limit', true ) ) ? $limit : '',
			'placeholder' => __( 'Unlimited', 'wp-job-manager-wc-paid-listings' ),
			'type' => 'number',
			'desc_tip' => true,
			'custom_attributes' => array(
			'min'   => '',
			'step' 	=> '1',
			),
		) ); ?>

		<?php woocommerce_wp_text_input( array(
			'id' => '_resume_duration',
			'label' => __( 'Resume listing duration', 'wp-job-manager-wc-paid-listings' ),
			'description' => __( 'The number of days that the resume will be active.', 'wp-job-manager-wc-paid-listings' ),
			'value' => get_post_meta( $post_id, '_resume_duration', true ),
			'placeholder' => get_option( 'resume_manager_submission_duration' ),
			'desc_tip' => true,
			'type' => 'number',
			'custom_attributes' => array(
			'min'   => '',
			'step' 	=> '1',
			),
		) ); ?>

		<?php woocommerce_wp_checkbox( array(
			'id' => '_resume_featured',
			'label' => __( 'Feature Listings?', 'wp-job-manager-wc-paid-listings' ),
			'description' => __( 'Feature this resume - it will be styled differently and sticky.', 'wp-job-manager-wc-paid-listings' ),
			'value' => get_post_meta( $post_id, '_resume_featured', true ),
		) ); ?>

		<script type="text/javascript">
			jQuery(function(){
				jQuery('.pricing').addClass( 'show_if_resume_package' );
				jQuery('._tax_status_field').closest('div').addClass( 'show_if_resume_package show_if_resume_package_subscription' );
				jQuery('.show_if_subscription, .options_group.pricing').addClass( 'show_if_resume_package_subscription' );
				jQuery('.options_group.pricing ._regular_price_field').addClass( 'hide_if_resume_package_subscription' );
				jQuery('#product-type').change();
				jQuery('#_resume_package_subscription_type').change(function(){
					if ( jQuery(this).val() === 'listing' ) {
						jQuery('#_resume_duration').closest('.form-field').hide().val('');
					} else {
						jQuery('#_resume_duration').closest('.form-field').show();
					}
				}).change();
			});
		</script>
	</div>
<?php
}// End if().
