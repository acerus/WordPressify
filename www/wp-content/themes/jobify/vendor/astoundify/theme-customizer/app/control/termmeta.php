<?php
/**
 * Search for terms, assign some sort of value to that term(s)
 * and add create a dynamic setting for it.
 *
 * @uses WP_Customize_Control
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.2.0
 */
abstract class Astoundify_ThemeCustomizer_Control_TermMeta extends WP_Customize_Control {

	/**
	 * @since 1.2.0
	 * @access public
	 * @var string $type
	 */
	public $type = 'TermMeta';

	/**
	 * @since 1.2.0
	 * @access public
	 * @var string $taxonomy
	 */
	public $taxonomy;

	/**
	 * @since 1.2.0
	 * @access public
	 * @var array $values
	 */
	public $values;

	/**
	 * Labels
	 *
	 * @since 1.2.0
	 * @access public
	 * @var array $i10n
	 */
	public $labels;

	/**
	 * @since 1.2.0
	 *
	 * @param WP_Customize $manager
	 * @param string       $id
	 * @param array        $args
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		add_action( 'customize_controls_print_scripts', array( $this, 'edit_term_content_template' ) );
	}

	/**
	 * Add extra properties to JSON to pass to to the preview iframe.
	 *
	 * @since 1.0.0
	 */
	public function to_json() {
		parent::to_json();

		$this->json['values'] = $this->values;
		$this->json['taxonomy'] = $this->taxonomy;
		$this->json['labels'] = $this->labels;
	}

	/**
	 * Render custom control markup.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
?>
	<p>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			
			<select class="search-terms" multiple="multiple">
				<option value="3">Some Term</option>
			</select>
		</label>
	</p>

	<?php do_action( "astoundify_themecustomizer_control_termmeta_input_{$this->id}", $this ); ?>

	<p>
		<a href="#" class="js-astoundify-themecustomizer-add-term button"><?php echo esc_html( $this->labels['add'] ); ?></a>
	</p>

<?php
	}

	/**
	 * Enqueue custom scripts
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue() {
		parent::enqueue();

		$install_url = trailingslashit( astoundify_themecustomizer_get_option( 'install_url' ) );

		wp_enqueue_script( 'astoundify-themecustomizer-termsearch', $install_url . '/assets/js/controls/termmeta.js', array( 'astoundify-themecustomizer-bigchoices', 'jquery', 'customize-controls', 'astoundify-themecustomizer-select2' ), false, true );

		wp_enqueue_style( 'astoundify-themecustomizer-termsearch', $install_url . '/assets/css/term-search.css' , array( 'select2' ) );
	}

}
