<?php
/**
 * Output linked products
 *
 * @package Jobify
 * @category Widget
 * @since 3.0.0
 */
class Jobify_Widget_Products extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_share widget--job_listing-products';
		$this->widget_description = __( 'Output products linked to this listing.', 'jobify' );
		$this->widget_id          = 'jobify_widget_products';
		$this->widget_name        = __( 'Jobify - Job: Products', 'jobify' );
		$this->settings           = array(
			'job_listing' => array(
				'std' => __( 'Job', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'jobify' ),
			),
		);

		parent::__construct();
	}

	function widget( $args, $instance ) {
		$output = $content = '';

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		$products = get_post_meta( get_the_ID(), '_products', true );

		// Stop if there are no products
		if ( ! $products || ! is_array( $products ) ) {
			return;
		}

		$query_args = apply_filters( 'woocommerce_related_products_args', array(
			'post_type'            => 'product',
			'ignore_sticky_posts'  => 1,
			'no_found_rows'        => 1,
			'posts_per_page'       => -1,
			'post__in'             => $products,
		) );

		$products = new WP_Query( $query_args );

		$output .= $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		$content .= apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget">' );

		ob_start();

		while ( $products->have_posts() ) {
			$products->the_post();
			wc_get_template( 'content-widget-product.php', array(
				'show_rating' => false,
			) );
		}

		wp_reset_query();

		$content .= ob_get_clean();

		$content .= apply_filters( 'woocommerce_after_widget_product_list', '</ul>' );

		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
