<?php
/**
 * Home: Search Hero
 *
 * @package Jobify
 * @category Widget
 * @since 3.0.0
 */
class Jobify_Widget_Search_Hero extends Jobify_Widget {

	public function __construct() {
		$this->widget_description = __( 'Display a "hero" search area.', 'jobify' );
		$this->widget_id          = 'jobify_widget_search_hero';
		$this->widget_cssclass    = 'widget--home-hero-search';
		$this->widget_name        = __( 'Jobify - Page: Search Hero', 'jobify' );
		$this->control_ops        = array(
			'width' => 400,
		);
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'height' => array(
				'type' => 'select',
				'std'  => 'medium',
				'label' => __( 'Hero Height', 'jobify' ),
				'options' => array(
					'small' => __( 'Small', 'jobify' ),
					'medium' => __( 'Medium', 'jobify' ),
					'large' => __( 'Large', 'jobify' ),
				),
			),
			'margin' => array(
				'type' => 'checkbox',
				'std'  => 1,
				'label' => __( 'Add standard spacing above/below widget', 'jobify' ),
			),
			'text_color' => array(
				'type'  => 'colorpicker',
				'std'   => '#ffffff',
				'label' => __( 'Text Color:', 'jobify' ),
			),
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'jobify' ),
			),
			'description' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Description:', 'jobify' ),
				'rows'  => 5,
			),
			'image' => array(
				'type'  => 'image',
				'std'   => '',
				'label' => __( 'Background Image:', 'jobify' ),
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
			'cover_overlay' => array(
				'type' => 'checkbox',
				'std'  => 1,
				'label' => __( 'Use transparent overlay', 'jobify' ),
			),
		);

		if ( jobify()->get( 'wp-job-manager-resumes' ) ) {
			$what = array(
				'what' => array(
					'type' => 'select',
					'std' => 'job',
					'label' => __( 'Search:', 'jobify' ),
					'options' => array(
						'job' => __( 'Jobs', 'jobify' ),
						'resume' => __( 'Resumes', 'jobify' ),
					),
				),
			);

			$this->settings = $what + $this->settings;
		}

		parent::__construct();
	}

	function widget( $args, $instance ) {
		extract( $args );

		$text_align = isset( $instance['text_align'] ) ? esc_attr( $instance['text_align'] ) : 'left';
		$background_position = isset( $instance['background_position'] ) ? esc_attr( $instance['background_position'] ) : 'center center';
		$overlay = isset( $instance['cover_overlay'] ) && 1 == $instance['cover_overlay'] ? 'has-overlay' : 'no-overlay';
		$margin = isset( $instance['margin'] ) && 1 == $instance['margin'] ? true : false;
		$height = isset( $instance['height'] ) ? esc_attr( $instance['height'] ) : 'medium';

		if ( ! $margin ) {
			$before_widget = str_replace( 'widget--home ', 'widget--home widget--home--no-margin ', $before_widget );
		}

		$image = isset( $instance['image'] ) ? esc_url( $instance['image'] ) : null;
		$content = $this->assemble_content( $instance );

		$what = isset( $instance['what'] ) ? esc_attr( $instance['what'] ) : 'job';

		global $is_flat;
		$is_flat = true;

		ob_start();
		?>

		<?php echo $before_widget; ?>

			<div class="hero-search hero-search--<?php echo esc_attr( $overlay ); ?> hero-search--height-<?php echo esc_attr( $height ); ?>" style="background-image:url(<?php echo $image; ?>); ?>; background-position: <?php echo $background_position; ?>">

			<div class="container">
				<?php echo $content; ?>

				<?php locate_template( array( $what . '-filters-flat.php' ), true, false ); ?>
			</div>

		</div>

		<?php echo $after_widget; ?>

		<?php

		$content = ob_get_clean();

		echo apply_filters( $this->widget_id, $content );
	}

	private function assemble_content( $instance ) {
		$text_color = isset( $instance['text_color'] ) ? esc_attr( $instance['text_color'] ) : '#fff';

		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$content = isset( $instance['description'] ) ? $instance['description'] : '';

		$output  = '<div class="hero-search__content" style="color:' . $text_color . '">';
		$output .= '<h2 class="hero-search__title" style="color:' . $text_color . '">' . $title . '</h2>';
		$output .= wpautop( $content );
		$output .= '</div>';

		return $output;
	}
}
