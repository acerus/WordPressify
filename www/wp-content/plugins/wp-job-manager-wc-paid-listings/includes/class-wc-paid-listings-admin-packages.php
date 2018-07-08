<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * WP_Job_Manager_WCPL_Admin_Packages class.
 *
 * @extends WP_List_Table
 */
class WP_Job_Manager_WCPL_Admin_Packages extends WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'package',
			'plural'   => 'packages',
			'ajax'     => false,
		) );
	}


	/**
	 * Get column default
	 *
	 * @param object $item
	 * @param string $column_name
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		global $wpdb;

		switch ( $column_name ) {
			case 'product_id' :
				$product = wc_get_product( $item->product_id );

				return $product ? '<a href="' . admin_url( 'post.php?post=' . absint( $product->get_id() ) . '&action=edit' ) . '">' . esc_html( $product->get_title() ) . '</a>' : __( 'n/a', 'wp-job-manager-wc-paid-listings' );
			case 'user_id' :
				$user = get_user_by( 'id', $item->user_id );

				if ( $item->user_id && $user ) {
					return '<a href="' . admin_url( 'user-edit.php?user_id=' . absint( $item->user_id ) ) . '">' . esc_attr( $user->display_name ) . '</a><br/><span class="description">' . esc_html( $user->user_email ) . '</span>';
				} else {
					return __( 'n/a', 'wp-job-manager-wc-paid-listings' );
				}
			case 'order_id' :
				return $item->order_id > 0 ? '<a href="' . admin_url( 'post.php?post=' . absint( $item->order_id ) . '&action=edit' ) . '">#' . absint( $item->order_id ) . ' &rarr;</a>' : __( 'n/a', 'wp-job-manager-wc-paid-listings' );
			case 'featured_job' :
				return $item->package_featured ? '&#10004;' : '&ndash;';
			case 'duration' :
				return $item->package_duration ? sprintf( __( '%d Days', 'wp-job-manager-wc-paid-listings' ), absint( $item->package_duration ) ) : '&ndash;';
			case 'limit' :
				return '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . ( 'resume' === $item->package_type ? 'resume' : 'job_listing' ) . '&package=' . absint( $item->id ) ) ) . '">' . ( $item->package_limit ? sprintf( __( '%s Posted', 'wp-job-manager-wc-paid-listings' ), absint( $item->package_count ) . ' / ' . absint( $item->package_limit ) ) : __( 'Unlimited', 'wp-job-manager-wc-paid-listings' ) ) . '</a>';
			case 'package_type' :
				return 'resume' === $item->package_type ? __( 'Resume Package', 'wp-job-manager-wc-paid-listings' ) : __( 'Job Package', 'wp-job-manager-wc-paid-listings' );
			case 'job_actions' :
				$edit_url = esc_url( add_query_arg( array(
					'action' => 'edit',
					'package_id' => $item->id,
				), admin_url( 'users.php?page=wc_paid_listings_packages' ) ) );
				$delete_url = wp_nonce_url( add_query_arg( array(
						'action' => 'delete',
						'package_id' => $item->id,
				), admin_url( 'users.php?page=wc_paid_listings_packages' ) ), 'delete', 'delete_nonce' );

				return '<div class="actions">' .
					'<a class="button button-icon icon-edit" href="' . $edit_url . '">' . __( 'Edit', 'wp-job-manager-wc-paid-listings' ) . '</a>' .
					'<a class="button button-icon icon-delete" href="' . $delete_url . '">' . __( 'Delete', 'wp-job-manager-wc-paid-listings' ) . '</a></div>' .
					'</div>';
		}// End switch().
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'user_id'      => __( 'User', 'wp-job-manager-wc-paid-listings' ),
			'package_type' => __( 'Type', 'wp-job-manager-wc-paid-listings' ),
			'product_id'   => __( 'Product', 'wp-job-manager-wc-paid-listings' ),
			'limit'        => __( 'Limit', 'wp-job-manager-wc-paid-listings' ),
			'duration'     => __( 'Duration', 'wp-job-manager-wc-paid-listings' ),
			'featured_job' => '<span class="tips" data-tip="' . __( 'Featured?', 'wp-job-manager-wc-paid-listings' ) . '">' . __( 'Featured?', 'wp-job-manager-wc-paid-listings' ) . '</span>',
			'order_id'     => __( 'Order ID', 'wp-job-manager-wc-paid-listings' ),
			'job_actions'  => __( 'Actions', 'wp-job-manager-wc-paid-listings' ),
		);
		return $columns;
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'order_id'     => array( 'order_id', false ),
			'user_id'      => array( 'user_id', true ),
			'product_id'   => array( 'product_id', false ),
			'package_type' => array( 'package_type', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @param string $which
	 */
	public function display_tablenav( $which ) {
		if ( 'top' == $which ) {
			return;
		}
		parent::display_tablenav( $which );
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @uses WP_List_Table::set_pagination_args()
	 *
	 * @access public
	 */
	public function prepare_items() {
		global $wpdb;

		$current_page          = $this->get_pagenum();
		$per_page              = 50;
		$orderby               = ! empty( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'user_id';
		$order                 = empty( $_REQUEST['order'] ) || $_REQUEST['order'] === 'asc' ? 'ASC' : 'DESC';
		$order_id              = ! empty( $_REQUEST['order_id'] ) ? absint( $_REQUEST['order_id'] ) : '';
		$user_id               = ! empty( $_REQUEST['user_id'] ) ? absint( $_REQUEST['user_id'] ) : '';
		$product_id            = ! empty( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : '';
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$where                 = array( 'WHERE 1=1' );

		if ( $order_id ) {
			$where[] = 'AND order_id=' . $order_id;
		}
		if ( $user_id ) {
			$where[] = 'AND user_id=' . $user_id;
		}
		if ( $product_id ) {
			$where[] = 'AND product_id=' . $product_id;
		}

		$where       = implode( ' ', $where );
		$max         = $wpdb->get_var( "SELECT COUNT(id) FROM {$wpdb->prefix}wcpl_user_packages $where;" );
		$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages $where ORDER BY `{$orderby}` {$order} LIMIT %d, %d", ( $current_page - 1 ) * $per_page, $per_page ) );

		$this->set_pagination_args( array(
			'total_items' => $max,
			'per_page'    => $per_page,
			'total_pages' => ceil( $max / $per_page ),
		) );
	}
}
