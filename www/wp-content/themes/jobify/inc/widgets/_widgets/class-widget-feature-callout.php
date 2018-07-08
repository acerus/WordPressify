<?php
/**
 * Home: Feature Callout
 *
 * @package Jobify
 * @category Widget
 * @since 3.0.0
 */
class Jobify_Widget_Feature_Callout extends Jobify_Widget {

	public function __construct() {
		$this->widget_description = __( 'Display a full-width "feature" callout', 'jobify' );
		$this->widget_id          = 'jobify_widget_feature_callout';
		$this->widget_cssclass    = 'jobify_widget_feature_callout widget--home-feature-callout';
		$this->widget_name        = __( 'Jobify - Page: Feature Callout', 'jobify' );
		$this->control_ops        = array(
			'width' => 400,
		);
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'background' => array(
				'type'  => 'select',
				'std'   => 'cover',
				'label' => __( 'Image Style:', 'jobify' ),
				'options' => array(
					'cover' => __( 'Cover', 'jobify' ),
					'pull'  => __( 'Pull Out', 'jobify' ),
				),
			),
			'background_position' => array(
				'type'  => 'select',
				'std'   => 'center center',
				'label' => __( 'Image Position:', 'jobify' ),
				'options' => array(
					'left top' => __( 'Left Top', 'jobify' ),
					'left center' => __( 'Left Center', 'jobify' ),
					'left bottom' => __( 'Left Bottom', 'jobify' ),
					'right top' => __( 'Right Top', 'jobify' ),
					'right center' => __( 'Right Center', 'jobify' ),
					'right bottom' => __( 'Right Bottom', 'jobify' ),
					'center top' => __( 'Center Top', 'jobify' ),
					'center center' => __( 'Center Center', 'jobify' ),
					'center bottom' => __( 'Center Bottom', 'jobify' ),
					'center top' => __( 'Center Top', 'jobify' ),
				),
			),
			'background_attachment' => array(
				'type' => 'select',
				'std' => 'scroll',
				'label' => 'Background Style',
				'options' => array(
					'scroll' => 'Scroll',
					'fixed' => 'Parallax',
				),
			),
			'cover_overlay' => array(
				'type' => 'select',
				'std'  => 'full',
				'options' => array(
					'none' => __( 'None', 'jobify' ),
					'full' => __( 'Full Overlay', 'jobify' ),
					'gradient-left' => __( 'Left Gradient', 'jobify' ),
					'gradient-right' => __( 'Right Gradient', 'jobify' ),
				),
				'label' => __( 'Transparent Overlay', 'jobify' ),
			),
			'cover_overlay_transparency' => array(
				'type' => 'number',
				'std'  => 0.50,
				'min'  => 0,
				'max'  => 1,
				'step' => 0.1,
				'label' => __( 'Overlay Transparency', 'jobify' ),
			),
			'margin' => array(
				'type' => 'checkbox',
				'std'  => 1,
				'label' => __( 'Add standard spacing above/below widget', 'jobify' ),
			),
			'height' => array(
				'type'  => 'select',
				'std'   => 'medium',
				'label' => __( 'Height:', 'jobify' ),
				'options' => array(
					'small' => __( 'Small', 'jobify' ),
					'medium' => __( 'Medium', 'jobify' ),
					'large' => __( 'Large', 'jobify' ),
				),
			),
			'text_align' => array(
				'type'  => 'select',
				'std'   => 'left',
				'label' => __( 'Text Align:', 'jobify' ),
				'options' => array(
					'left' => __( 'Left', 'jobify' ),
					'right' => __( 'Right', 'jobify' ),
					'center' => __( 'Center (cover only)', 'jobify' ),
				),
			),
			'text_color' => array(
				'type'  => 'colorpicker',
				'std'   => '#717A8F',
				'label' => __( 'Text Color:', 'jobify' ),
			),
			'background_color' => array(
				'type'  => 'colorpicker',
				'std'   => '#ffffff',
				'label' => __( 'Background Color:', 'jobify' ),
			),
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'jobify' ),
			),
			'content' => array(
				'type'  => 'textarea',
				'std'   => '',
				'label' => __( 'Content:', 'jobify' ),
				'rows'  => 5,
			),
			'image' => array(
				'type'  => 'image',
				'std'   => '',
				'label' => __( 'Image:', 'jobify' ),
			),
		);

		parent::__construct();

		add_filter( 'jobify_feature_callout_description', 'wptexturize' );
		add_filter( 'jobify_feature_callout_description', 'convert_smilies' );
		add_filter( 'jobify_feature_callout_description', 'convert_chars' );
		add_filter( 'jobify_feature_callout_description', 'wpautop' );
		add_filter( 'jobify_feature_callout_description', 'shortcode_unautop' );
		add_filter( 'jobify_feature_callout_description', 'prepend_attachment' );
		add_filter( 'jobify_feature_callout_description', 'do_shortcode' );
	}

	/**
	 * Output the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, array(
			'text_color'                 => '#717a8f',
			'text_align'                 => 'left',
			'background'                 => 'cover',
			'background_color'           => '#ffffff',
			'background_position'        => 'center center',
			'background_attachment'      => 'scroll',
			'cover_overlay'              => 'full',
			'cover_overlay_transparency' => '0.5',
			'margin'                     => true,
			'height'                     => 'medium',
			'title'                      => '',
			'image'                      => '',
			'content'                    => '',
		) );

		// force scroll on mobile
		if ( wp_is_mobile() ) {
			$instance['background_attachment'] = 'scroll';
		}

		$instance['content'] = $this->assemble_content( $instance );

		if ( ! $instance['margin'] ) {
			$args['before_widget'] = str_replace( 'widget--home ', 'widget--home widget--home--no-margin ', $args['before_widget'] );
		}

		ob_start();

		echo $args['before_widget'];
?>

<div class="feature-callout text-<?php echo $instance['text_align']; ?> image-<?php echo $instance['background']; ?>">

	<?php if ( 'pull' == $instance['background'] ) : ?>

		<div class="container">
			<div class="col-xs-12 col-sm-6 <?php echo ( 'right' == $instance['text_align'] ) ? 'col-sm-offset-6' : ''; ?>">
				<?php echo $instance['content']; ?>
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 <?php echo ( 'left' == $instance['text_align'] ) ? 'col-sm-offset-6' : ''; ?> feature-callout-image-pull"></div>

		<style>
		#<?php echo $this->id; ?> .feature-callout-image-pull {
			background-image: url(<?php echo esc_url( $instance['image'] ); ?>);
			background-position: <?php echo esc_attr( $instance['background_position'] ); ?>;
			background-attachment: <?php echo esc_attr( $instance['background_attachment'] ); ?>;
		}
		</style>

	<?php else : ?>

		<div class="feature-callout-cover feature-callout-cover--overlay-<?php echo esc_attr( $instance['cover_overlay'] ); ?>">

			<div class="container">
				<div class="row">
					<div class="col-xs-12 <?php echo ( in_array( $instance['text_align'], array( 'left', 'right' ) ) ) ? 'col-sm-8 col-md-6' : ''; ?> <?php echo ( 'right' == $instance['text_align'] ) ? 'col-sm-offset-4 col-md-offset-6' : ''; ?>">
						<?php echo $instance['content']; ?>
					</div>
				</div>
			</div>

		</div>

		<style>
		#<?php echo $this->id; ?> .feature-callout-cover {
			background-image: url(<?php echo esc_url( $instance['image'] ); ?>);
			background-position: <?php echo esc_attr( $instance['background_position'] ); ?>;
			background-attachment: <?php echo esc_attr( $instance['background_attachment'] ); ?>;
		}

		<?php if ( 'full' == $instance['cover_overlay'] ) : ?>
			@media(min-width: 768px) {
				#<?php echo $this->id; ?> .feature-callout-cover--overlay-full:after {
					background: rgba(0, 0, 0, <?php echo esc_attr( $instance['cover_overlay_transparency'] ); ?>);
				}
			}
		<?php elseif ( 'gradient-left' == $instance['cover_overlay'] ) : ?>
			@media(min-width: 768px) {
				#<?php echo $this->id; ?> .feature-callout-cover--overlay-gradient-left:after {
					background: linear-gradient(to left, rgba(0,0,0,0) 50%, rgba(0,0,0,<?php echo esc_attr( $instance['cover_overlay_transparency'] ); ?>) 100%);
				}
			}
		<?php elseif ( 'gradient-right' == $instance['cover_overlay'] ) : ?>
			@media(min-width: 768px) {
				#<?php echo $this->id; ?> .feature-callout-cover--overlay-gradient-right:after {
					background: linear-gradient(to right, rgba(0,0,0,0) 50%, rgba(0,0,0,<?php echo esc_attr( $instance['cover_overlay_transparency'] ); ?>) 100%);
				}
			}
		<?php endif; ?>
		</style>

	<?php endif; ?>

</div>

<style>
#<?php echo $this->id; ?> .feature-callout {
	background-color: <?php echo esc_attr( $instance['background_color'] ); ?>;
}

#<?php echo $this->id; ?> .callout-feature-content,
#<?php echo $this->id; ?> .callout-feature-content p,
#<?php echo $this->id; ?> .callout-feature-content a:not(.button),
#<?php echo $this->id; ?> .callout-feature-title {
	color: <?php echo esc_attr( $instance['text_color'] ); ?>
}
</style>

<?php
		echo $args['after_widget'];

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );
	}

	private function assemble_content( $instance ) {
		$instance = wp_parse_args( $instance, array(
			'title' => '',
			'content' => '',
			'height' => 'medium',
		) );

		ob_start();
?>

<div class="callout-feature-content callout-feature-content--height-<?php echo esc_attr( $instance['height'] ); ?>">
	<h2 class="callout-feature-title"><?php echo esc_attr( $instance['title'] ); ?></h2>
	<?php echo wpautop( $instance['content'] ); ?>
</div>

<?php
		return apply_filters( 'jobify_feature_callout_description', ob_get_clean() );
	}
}
