<?php
/**
 * Multiselect
 *
 * A select box that can contain multiple selections.
 * Plain HTML by default but can be transformed with Javascript libraries.
 *
 * @uses WP_Customize_Control
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */
class Astoundify_ThemeCustomizer_Control_Multiselect extends WP_Customize_Control {

	/**
	 * @since 1.0.0
	 * @access public
	 * @var string $type
	 */
	public $type = 'Multiselect';

	/**
	 * @since 1.0.0
	 * @access public
	 * @var string $placeholder
	 */
	public $placeholder;

	/**
	 * Set our custom arguments to class properties, and other things.
	 *
	 * @since 1.0.0
	 * @param oject  $manager WP_Customize_Manager
	 * @param string $id
	 * @param array  $args
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Send the current selection to the control JS.
	 *
	 * This allows external Javascript libraries to manipulate the
	 * control/setting easily.
	 *
	 * @since 1.0.0
	 */
	public function to_json() {
		parent::to_json();

		$this->json['selection'] = $this->get_saved_value();
		$this->json['placeholder'] = esc_attr( $this->placeholder );
	}

	/**
	 * Output the control HTML
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		$saved_value = $this->get_saved_value();
?>

<label>
	<?php if ( ! empty( $this->label ) ) : ?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
	<?php endif;
if ( ! empty( $this->description ) ) : ?>
		<span class="description customize-control-description"><?php echo $this->description; ?></span>
	<?php endif; ?>

	<select <?php $this->link(); ?> multiple>
		<?php
		foreach ( $this->choices as $value => $label ) {
			echo '<option value="' . esc_attr( $value ) . '"' . selected( false !== array_search( $value, $saved_value ), ! false, false ) . '>' . $label . '</option>';
		}
		?>
	</select>
</label>

<?php
	}

	/**
	 * Allow backwards compatibility for comma separated lists.
	 *
	 * @since 1.5.0
	 * @return array $saved_value
	 */
	public function get_saved_value() {
		$saved_value = $this->value();

		if ( ! is_array( $this->value() ) ) {
			$saved_value = array_map( 'trim', explode( ',', $this->value() ) );
		}

		return $saved_value;
	}

	/**
	 * Enqueue custom scripts
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue() {
		$install_url = trailingslashit( astoundify_themecustomizer_get_option( 'install_url' ) );

		wp_enqueue_style( 'astoundify-themecustomizer-select2' );
		wp_enqueue_script( 'astoundify-themecustomizer-multiselect', $install_url . '/assets/js/controls/Multiselect.js', array( 'jquery', 'customize-controls', 'astoundify-themecustomizer-select2' ), false, true );

		parent::enqueue();
	}
}
