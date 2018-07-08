<?php
/**
 * Price Table for WooCommerce Paid Listings
 *
 * Automatically populated with subscriptions.
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Price_Table_WC extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_price_table_wc';
		$this->widget_description = __( 'Outputs Job Packages from WooCommerce', 'jobify' );
		$this->widget_id          = 'jobify_widget_price_table_wc';
		$this->widget_name        = __( 'Jobify - Page: Price Table', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Plans and Pricing', 'jobify' ),
				'label' => __( 'Title:', 'jobify' ),
			),
			'packages' => array(
				'label' => __( 'Package Type:', 'jobify' ),
				'type' => 'select',
				'std' => 'job_package',
				'options' => array(
					'job_package' => __( 'Job Packages', 'jobify' ),
					'resume_package' => __( 'Resume Packages', 'jobify' ),
				),
			),
			'title-color' => array(
				'label' => __( 'Title Background Color:', 'jobify' ),
				'type' => 'colorpicker',
				'std' => get_theme_mod( 'color-primary', '#7dc246' ),
			),
			'description' => array(
				'type'  => 'textarea',
				'rows'  => 4,
				'std'   => '',
				'label' => __( 'Description:', 'jobify' ),
			),
		);

		parent::__construct();
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title       = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$description = isset( $instance['description'] ) ? $instance['description'] : '';
		$packages    = isset( $instance['packages'] ) ? esc_attr( $instance['packages'] ) : 'job_package';
		$type        = 'job_package' == $packages ? 'job_listing' : 'resume';
		$title_color = isset( $instance['title-color'] ) ? esc_attr( $instance['title-color'] ) : get_theme_mod( 'color-primary', '#7dc246' );
		$obj         = get_post_type_object( $type );

		if ( 'job_package' == $packages ) {
			if ( jobify()->get( 'wp-job-manager-wc-advanced-paid-listings' ) ) {
				$packages = jwapl_get_job_packages();
			} else {
				$packages = WP_Job_Manager_WCPL_Submit_Job_Form::get_packages();
			}

			$submit = jobify_get_submit_listing_page_permalink();
		} else {
			if ( jobify()->get( 'wp-job-manager-wc-advanced-paid-listings' ) ) {
				$packages = jwapl_get_resume_packages();
			} else {
				$packages = WP_Job_Manager_WCPL_Submit_Resume_Form::get_packages();
			}

			$submit = resume_manager_get_permalink( 'submit_resume_form' );
		}

		if ( ! $packages ) {
			return;
		}

		echo $before_widget;
?>

		<div class="container">

			<?php if ( $title ) { echo $before_title . $title . $after_title;} ?>

			<?php if ( $description ) : ?>
				<p class="widget-description widget-description--home"><?php echo $description; ?></p>
			<?php endif; ?>

			<div class="price-table row" data-columns>

				<?php foreach ( $packages as $key => $package ) : $product = wc_get_product( $package ); ?>
					<div class="price-option">
						<div class="price-option__title">
							<?php echo $product->get_title(); ?>
						</div>

						<div class="price-option__description">
							<h2 class="price-option__price"><?php echo $product->get_price_html(); ?></h2>

							<p class="price-option__duration"><?php
							if ( 'job_listing' == $type ) {
								printf( _n( '%1$s for %2$s job', '%1$s for %2$s jobs', $product->get_limit(), 'jobify' ) . ' ', $product->get_price_html(), $product->get_limit() ? $product->get_limit() : __( 'unlimited', 'jobify' ) );
								if ( $product->get_duration() ) {
									printf( _n( 'for %s day', 'for %s days', $product->get_duration(), 'jobify' ), $product->get_duration() );
								}
							} else {
								printf( _n( '%1$s to post %2$d resume', '%1$s to post %2$s resumes', $product->get_limit(), 'jobify' ) . ' ', $product->get_price_html(), $product->get_limit() ? $product->get_limit() : __( 'unlimited', 'jobify' ) );
								if ( $product->get_duration() ) {
									printf( ' ' . _n( 'for %s day', 'for %s days', $product->get_duration(), 'jobify' ), $product->get_duration() );
								}
							}
							?></p>

							<?php echo apply_filters( 'the_content', get_post_field( 'post_content', $product->get_id() ) ); ?>

							<p>
								<a href="<?php echo esc_url( add_query_arg( 'selected_package', $product->get_id(), $submit ) ); ?>" class="button button--type-action button--style-inverted"><?php _e( 'Get Started', 'jobify' ); ?></a>
							</p>
						</div>
					</div>

				<?php endforeach; ?>

			</div>

		</div>

		<style>
		#<?php echo esc_attr( $this->id ); ?> .price-option__title {
			background-color: <?php echo esc_attr( $title_color ); ?>;
		}
		</style>

		<?php
		echo $after_widget;
	}
}
