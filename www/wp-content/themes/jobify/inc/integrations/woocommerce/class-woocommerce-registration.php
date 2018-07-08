<?php

class Jobify_WooCommerce_Registration {

	public function __construct() {
		add_action( 'woocommerce_register_form', array( $this, 'register_form' ) );
		add_filter( 'woocommerce_new_customer_data', array( $this, 'new_customer_data' ) );
	}

	public function new_customer_data( $data ) {
		if ( ! isset( $_POST['reg_role'] ) ) {
			return $data;
		}

		$role = esc_attr( $_POST['reg_role'] );
		$whitelist = $this->get_registration_roles();

		if ( ! in_array( $role, array_keys( $whitelist ) ) ) {
			return $data;
		}

		$data['role'] = $role;

		return $data;
	}

	public function get_allowed_roles() {
		$roles = (array) get_theme_mod( 'registration-roles', array( 'employer' ) );

		if ( empty( $roles ) ) {
			return array();
		}

		if ( ! is_array( $roles ) ) {
			$roles = explode( ',', $roles );
		}

		$roles = array_map( 'trim', $roles );

		return $roles;
	}

	private function get_editable_roles() {
		global $wp_roles;

		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters( 'editable_roles', $all_roles );

		return (array) $editable_roles;
	}

	public function get_registration_roles() {
		add_filter( 'editable_roles', array( $this, 'editable_roles' ) );

		$roles = $this->get_editable_roles();

		if ( empty( $roles ) ) {
			return array();
		}

		$value = array_keys( $roles );
		$labels = wp_list_pluck( $roles, 'name' );

		$options = array_combine( $value, $labels );

		remove_filter( 'editable_roles', array( $this, 'editable_roles' ) );

		return $options;
	}

	public function editable_roles( $roles ) {
		$remove = apply_filters( 'jobify_removed_roles', array( 'administrator', 'editor', 'author', 'contributor', 'shop_manager' ) );

		foreach ( $remove as $role ) {
			unset( $roles[ $role ] );
		}

		return $roles;
	}

	public function register_form() {
		$default = get_theme_mod( 'registration-default', 'employer' );
		$roles = $this->get_allowed_roles();
		$labels = $this->get_registration_roles();

		if ( empty( $roles ) ) {
			return;
		}

		if ( 1 == count( $roles ) ) {
			echo '<input type="hidden" value="' . esc_attr( $roles[0] ) . '" name="reg_role" />';
			return;
		}

		$options = array();

		foreach ( $roles as $value ) {
			// in case things get out of sync
			if ( ! isset( $labels[ $value ] ) ) {
				continue;
			}

			$label = apply_filters( 'jobify_registration_role_' . $value, $labels[ $value ] );
			$options[] = '<option value="' . $value . '"' . selected( $default, $value, false ) . '>' . esc_attr( $label ) . '</option>';
		}

		$options = implode( '', $options );
	?>
		<p class="form-row form-row-wide">
			<label for="reg_role"><?php _e( 'Register As', 'jobify' ); ?></label>
			<select name="reg_role" class="jobify-registration-role"><?php echo $options; ?></select>
		</p>
	<?php
	}

}
