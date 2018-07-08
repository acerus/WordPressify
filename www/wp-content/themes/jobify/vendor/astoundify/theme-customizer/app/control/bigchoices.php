<?php
/**
 * A better way to manage large <select> boxes.
 *
 * Especially useful if there are multiple <select> boxes in a section
 * that all contain the same options.
 *
 * @uses WP_Customize_Control
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */
class Astoundify_ThemeCustomizer_Control_BigChoices extends WP_Customize_Control {

	/**
	 * @since 1.0.0
	 * @access public
	 * @var string $type
	 */
	public $type = 'BigChoices';

	/**
	 * @since 1.0.0
	 * @access public
	 * @var string $choices_id
	 */
	public $choices_id;

	/**
	 * @since 1.0.0
	 * @access public
	 * @var array $choices
	 */
	public $choices = array();

	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_scripts' ) );
	}

	/**
	 * Send the choices we want to use to the JS
	 *
	 * @since 1.0.0
	 */
	public function to_json() {
		parent::to_json();

		$this->json['choices_id'] = $this->choices_id;
	}

	/**
	 * Add our data to astoundifyThemeCustomizer.
	 *
	 * Need to wait for the proper context before adding the filter.
	 *
	 * @since 1.0.0
	 */
	public function customizer_scripts() {
		add_filter( 'astoundify_themecustomizer_scripts', array( $this, 'set_big_choices' ) );
	}

	/**
	 * Set the BigChoices in the astoundifyThemeCustomizer JS object
	 *
	 * This should be more dynamic on a per-control basis or ane extended class.
	 *
	 * @since 1.0.0
	 * @param array $data
	 * @return array $data
	 */
	public function set_big_choices( $data ) {
		if ( isset( $data['BigChoices'] ) && ! isset( $data['BigChoices'][ $this->choices_id ] ) ) {
			$data['BigChoices'][ $this->choices_id ] = $this->choices;
		}

		return $data;
	}

	/**
	 * Enqueue additional scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		$install_url = trailingslashit( astoundify_themecustomizer_get_option( 'install_url' ) );

		wp_enqueue_script( 'astoundify-themecustomizer-bigchoices', $install_url . '/assets/js/controls/BigChoices.js', array( 'jquery', 'astoundify-themecustomizer-select2', 'customize-controls' ) );
		wp_enqueue_style( 'astoundify-themecustomizer-select2' );
	}

	/**
	 * Output the control HTML
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
?>

<label>
	<?php if ( ! empty( $this->label ) ) : ?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
	<?php endif;
if ( ! empty( $this->description ) ) : ?>
		<span class="description customize-control-description"><?php echo $this->description; ?></span>
	<?php endif; ?>

	<select <?php $this->link(); ?>>
		<option selected="selected"></option>
	</select>
</label>
<?php
	}

}
