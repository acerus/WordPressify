<?php
/**
 * Control Group
 *
 * A single control that is linked to many "child" controls.
 * When the parent control is changed all of the child controls
 * are automatically triggered to their set default.
 *
 * @uses WP_Customize_Control
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */
class Astoundify_ThemeCustomizer_Control_ControlGroup extends WP_Customize_Control {

	/**
	 * @since 1.0.0
	 * @access public
	 * @var string $type
	 */
	public $type = 'ControlGroup';

	/**
	 * @since 1.0.0
	 * @access public
	 * @var array $group
	 */
	public $group;

	/**
	 * @since 1.2.0
	 * @access public
	 * @var string $input_type
	 */
	public $input_type = 'radio';

	/**
	 * Start thigns up.
	 *
	 * @since 1.0.0
	 * @param object $wp_customize Customize API
	 * @param string $id Control Group identifier
	 * @param array  $args
	 */
	public function __construct( $wp_customize, $id, $args = array() ) {
		parent::__construct( $wp_customize, $id, $args );

		if ( ! $this->group ) {
			$this->group = astoundify_themecustomizer_get_control_group( $id );
		}
	}

	/**
	 * Allow the ControlGroup JS control access to information.
	 *
	 * @since 1.2.0
	 */
	public function to_json() {
		parent::to_json();

		$this->json['input_type'] = $this->input_type;
	}

	/**
	 * Output the control HTML
	 *
	 * The control contains a group of radio toggles.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		$name = '_customize-radio-' . $this->id;
?>

<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

<?php if ( 'radio' == $this->input_type ) : ?>

	<?php foreach ( $this->group as $group_id => $group_data ) : ?>

		<p>
			<label>
				<input <?php $this->link(); ?> name="<?php echo esc_attr( $name ); ?>" value="<?php echo $group_id; ?>" type="radio" <?php echo $this->generate_linked_control_data( $group_data['controls'] ); ?> <?php checked( $group_id, sanitize_title( $this->value() ) ); ?> />
				<span class="label"><?php echo esc_attr( $group_data['title'] ); ?></span>
			</label>
		</p>

	<?php endforeach; ?>

<?php else : ?>

	<p>
		<select <?php $this->link(); ?> name="<?php echo esc_attr( $name ); ?>">

			<?php foreach ( $this->group as $group_id => $group_data ) : ?>

				<option value="<?php echo $group_id; ?>" <?php echo $this->generate_linked_control_data( $group_data['controls'] ); ?> <?php selected( $group_id, sanitize_title( $this->value() ) ); ?>><?php echo esc_attr( $group_data['title'] ); ?></option>

			<?php endforeach; ?>

		</select>
	</p>

<?php endif; ?>

<?php if ( $this->description ) : ?>
	<p><?php echo $this->description; ?></p>
<?php endif; ?>

<?php
	}

	/**
	 * Using the group data generate the the data attribute linking
	 * the rest of the controls to this one.
	 *
	 * @since 1.0.0
	 * @param array $controls Key value control and default value
	 * @return string
	 */
	public function generate_linked_control_data( $controls ) {
		$output = array();

		foreach ( $controls as $key => $value ) {
			$output[ $key ] = $value;
		}

		return "data-controls='" . json_encode( $output ) . "'";
	}

	/**
	 * Enqueue additional scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		$install_url = trailingslashit( astoundify_themecustomizer_get_option( 'install_url' ) );

		wp_enqueue_script( 'astoundify-themecustomizer-controlgroup', $install_url . '/assets/js/controls/ControLGroup.js', array( 'jquery', 'customize-controls' ), false, true );
	}

}
