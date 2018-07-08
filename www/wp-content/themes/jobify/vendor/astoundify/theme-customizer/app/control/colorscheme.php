<?php
/**
 * Color Scheme control group.
 *
 * @uses WP_Customize_Control
 * @uses Astoundify_ThemeCustomizer_Control_ControlGroup
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.2.0
 */
class Astoundify_ThemeCustomizer_Control_ColorScheme extends Astoundify_ThemeCustomizer_Control_ControlGroup {

	/**
	 * Output the control HTML
	 *
	 * The control contains a group of radio toggles.
	 *
	 * @since 1.2.0
	 */
	public function render_content() {
		$name = '_customize-radio-' . $this->id;
?>

<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

<?php foreach ( $this->group as $group_id => $group_data ) : ?>

	<p>
		<label>
			<input <?php $this->link(); ?> name="<?php echo esc_attr( $name ); ?>" value="<?php echo $group_id; ?>" type="radio" <?php echo $this->generate_linked_control_data( $group_data['controls'] ); ?> <?php checked( $group_id, sanitize_title( $this->value() ) ); ?> />
			<?php echo $this->generate_scheme_preview( $group_data['controls'] ); ?>
			<span class="label"><?php echo esc_attr( $group_data['title'] ); ?></span>
		</label>
	</p>

<?php endforeach; ?>

<?php if ( $this->description ) : ?>
	<p><?php echo $this->description; ?></p>
<?php endif; ?>

<?php
	}

	/**
	 * Generate HTML markup to preview the color scheme data.
	 *
	 * @since 1.2.0
	 *
	 * @param array $colors
	 */
	public function generate_scheme_preview( $colors ) {
		echo '<span class="color-scheme">';

		// grab a short part from the middle that is a little more accurate
		if ( count( $colors ) > 9 ) {
			$colors = array_splice( $colors, 2, 10 );
		}

		foreach ( $colors as $color ) {
			echo '<span class="color-scheme-color" style="background-color: ' . $color . '"></span>';
		}

		echo '</span>';
	}

	/**
	 * Enqueue custom scripts
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function enqueue() {
		parent::enqueue();
		wp_add_inline_style( 'customize-controls', $this->get_css() );
	}

	/**
	 * Generate a bit of CSS to display a scheme preview.
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	public function get_css() {
		ob_start();
?>

.color-scheme {
	display: inline-block;
	height: 24px;
	vertical-align: middle;
	padding: 2px;
	border: 1px solid #ddd;
	margin-right: 4px;
	margin-top: -3px;
}

.color-scheme-color {
	display: inline-block;
	width: 10px;
	height: 24px;
}

<?php

		return ob_get_clean();
	}

}
