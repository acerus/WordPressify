<?php

class Jobify_WooCommerce_Layout {

	public function __construct() {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

		/** Account */
		add_action( 'woocommerce_before_customer_login_form', array( $this, 'before_customer_login' ) );
		add_action( 'woocommerce_after_customer_login_form', array( $this, 'after_customer_login' ) );

		/** Title */
		add_action( 'woocommerce_before_main_content', array( $this, 'page_title' ), 5 );

		// sale
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );

		// rating
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating' );
		add_filter( 'jobify_woocommerce_page_title_meta', array( $this, 'rating' ) );

		/** Structure */
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
		add_action( 'woocommerce_before_main_content', array( $this, 'output_content_wrapper' ) );

		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );
		add_action( 'woocommerce_after_main_content', array( $this, 'after_main_content' ) );

		add_action( 'woocommerce_sidebar', array( $this, 'after_sidebar' ) );

		/** Summary */
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

		/** Shop */
		add_filter( 'loop_shop_columns', array( $this, 'loop_shop_columns' ) );
	}

	public function loop_shop_columns() {
		return 3;
	}

	public function before_customer_login() {
		echo '<div class="woocommerce-customer-login">';
	}

	public function after_customer_login() {
		echo '</div>';
	}

	public function page_title() {
?>
<header class="page-header">
	<h2 class="page-title">
		<?php if ( is_singular( 'product' ) ) : ?>
			<?php the_title(); ?>
		<?php else : ?>
			<?php echo woocommerce_page_title( false ) ? woocommerce_page_title() : __( 'Shop', 'jobify' ); ?>
		<?php endif; ?>
	</h2>
	<?php
		$actions = apply_filters( 'jobify_woocommerce_page_title_meta', array() );

	if ( ! empty( $actions ) ) :
	?>
	<h2 class="page-subtitle">
	<ul><?php echo implode( '', $actions ); ?></ul>
	</h2>
	<?php endif; ?>
</header>
<?php
	}

	public function sale_flash( $actions ) {
		if ( ! is_singular( 'product' ) ) {
			return $actions;
		}

		$output = $this->_get_page_action( 'woocommerce_show_product_sale_flash' );

		if ( '' != $output ) {
			$actions['sale'] = '<li class="sale">' . $output . '</li>';
		}

		return $actions;
	}

	public function rating( $actions ) {
		if ( ! is_singular( 'product' ) ) {
			return $actions;
		}

		$output = $this->_get_page_action( 'woocommerce_template_single_rating' );

		if ( '' != $output ) {
			$actions['rating'] = '<li class="rating">' . $output . '</li>';
		}

		return $actions;
	}

	private function _get_page_action( $action ) {
		global $product, $post;

		the_post();

		$product = wc_get_product( $post );

		ob_start();

		call_user_func( $action );

		$output = ob_get_clean();

		rewind_posts();

		return $output;
	}

	public function output_content_wrapper() {
	?>
		<div id="content" class="container" role="main">
			<div class="row">
				<div class="col-xs-12 col-md-8">
	<?php
	}

	public function after_main_content() {
	?>
				</div>
	<?php
	}

	public function after_sidebar() {
	?>
			</div>
		</div>
	<?php
	}

}
