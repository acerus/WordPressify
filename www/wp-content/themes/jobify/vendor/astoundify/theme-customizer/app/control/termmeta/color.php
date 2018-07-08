<?php
/**
 * Add a color to a searched term.
 *
 * @uses WP_Customize_Control
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.2.0
 */
class Astoundify_ThemeCustomizer_Control_TermMeta_Color extends Astoundify_ThemeCustomizer_Control_TermMeta {

	/**
	 * @since 1.2.0
	 * @access public
	 * @var string $type
	 */
	public $type = 'TermMetaColor';

	/**
	 * @since 1.2.0
	 *
	 * @param WP_Customize $manager
	 * @param string       $id
	 * @param array        $args
	 */
	public function __construct( $manager, $id, $args = array() ) {
		add_action( "astoundify_themecustomizer_control_termmeta_input_{$id}", array( $this, 'meta_setter' ) );

		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Render markup used to assign meta to a term.
	 *
	 * @since 1.2.0
	 */
	public function meta_setter() {
?>

<p>
	<label>
		<?php echo esc_attr( $this->labels['choose'] ); ?>
		<input type="text" class="<?php echo $this->id; ?>-color-picker color-picker-hex" value="#000000" data-hide="false" />
	</label>
</p>

<?php
	}

	/**
	 * Custom term color JS underscore template.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function edit_term_content_template() {
		$setting = "{$this->id}[{$this->taxonomy}][{{{data.termId}}}]";
?>

<script type="text/html" id="tmpl-customize-control-edit-term-<?php echo esc_attr( $this->type ); ?>-content">
	<label>
		<span class="customize-control-title">{{{data.termLabel}}}</span>
		<div class="customize-control-content">
		<input class="color-picker-hex" type="text" name="<?php echo $this->id; ?>[<?php echo $this->taxonomy; ?>][{{{data.termId}}}]" value="{{{data.metaValue}}}" data-customize-setting-link="<?php echo $this->id; ?>[<?php echo $this->taxonomy; ?>][{{{data.termId}}}]" maxlength="7" />
			<a class="js-astoundify-themecustomizer-remove-term button button-secondary button-small"><?php _e( 'Remove', 'jobify' ); ?></a>
		</div>
	</label>
</script>

<?php
	}

}
