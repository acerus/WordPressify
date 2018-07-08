<?php
/**
 * Settings for modifying pages.
 *
 * @since 3.3.0
 */
class Jobify_Page_Settings {

	public function __construct() {
		add_action( 'init', array( $this, 'register_meta' ) );

		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	/**
	 * Register the meta information.
	 *
	 * @since 3.3.0
	 * @return void
	 */
	public function register_meta() {
		register_meta( 'post', 'page_show_header', array(
			'sanitize_callback' => 'absint',
			'type' => 'integer',
		) );
	}

	/**
	 * Add the metabox.
	 *
	 * @since 3.3.0
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box( 'jobify-settings', __( 'Page Title', 'jobify' ), array( $this, 'meta_box_settings' ), 'page', 'side' );
	}

	/**
	 * Output the metabox content
	 *
	 * @since 3.3.0
	 * @return void
	 */
	public function meta_box_settings() {
		$post = get_post();

		$show = ! $post->page_show_header || 1 === $post->page_show_header;
?>

<p>
	<label for="page_show_header">
		<input type="checkbox" name="page_show_header" id="page_show_header" value="1" <?php checked( true, $show ); ?>>
		<?php _e( 'Display page title', 'jobify' ); ?>
	</label>
</p>

<?php
	}

	/**
	 * Save meta information.
	 *
	 * @since 3.3.0
	 * @param int $post_id
	 * @return void
	 */
	public function save_post( $post_id ) {
		global $post;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! is_object( $post ) ) {
			return;
		}

		if ( 'page' != $post->post_type ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}

		$show = ! isset( $_POST['page_show_header'] );

		if ( $show ) {
			add_post_meta( $post->ID, 'page_show_header', $show );
		} else {
			delete_post_meta( $post->ID, 'page_show_header' );
		}
	}

}

new Jobify_Page_Settings();
