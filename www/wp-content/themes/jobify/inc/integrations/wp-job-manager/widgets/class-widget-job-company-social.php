<?php
/**
 * Job: Company Social
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Company_Social extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_company_social';
		$this->widget_description = __( 'Display the job\'s company social profiles', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_company_social';
		$this->widget_name        = __( 'Jobify - Job: Company Social', 'jobify' );
		$this->settings           = array(
			'job_listing' => array(
				'std' => __( 'Job', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'jobify' ),
			),
		);

		parent::__construct();
	}

	function widget( $args, $instance ) {
		$output = $content = '';

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		ob_start();
?>

	<?php if ( jobify_get_the_company_website() ) : ?>
	<li><a href="<?php echo esc_url( jobify_get_the_company_website() ); ?>" target="_blank" class="job_listing-website">
		<?php _e( 'Website', 'jobify' ); ?>
	</a></li>
	<?php endif; ?>

	<?php if ( jobify_get_the_company_twitter() ) : ?>
	<li><a href="<?php echo esc_url( jobify_get_the_company_twitter() ); ?>" target="_blank" class="job_listing-twitter">
		<?php _e( 'Twitter', 'jobify' ); ?>
	</a></li>
	<?php endif; ?>

	<?php if ( jobify_get_the_company_facebook() ) : ?>
	<li><a href="<?php echo esc_url( jobify_get_the_company_facebook() ); ?>" target="_blank" class="job_listing-facebook">
		<?php _e( 'Facebook', 'jobify' ); ?>
	</a></li>
	<?php endif; ?>

	<?php if ( jobify_get_the_company_gplus() ) : ?>
	<li><a href="<?php echo esc_url( jobify_get_the_company_gplus() ); ?>" target="_blank" class="job_listing-googleplus">
		<?php _e( 'Google+', 'jobify' ); ?>
	</a></li>
	<?php endif; ?>

	<?php if ( jobify_get_the_company_linkedin() ) : ?>
	<li><a href="<?php echo esc_url( jobify_get_the_company_linkedin() ); ?>" target="_blank" class="job_listing-linkedin">
		<?php _e( 'LinkedIn', 'jobify' ); ?>
	</a></li>
	<?php endif; ?>

<?php
		$items = trim( ob_get_clean() );

if ( '' == $items ) {
	return;
}

		$output .= $args['before_widget'];

if ( $title ) {
	$output .= $args['before_title'] . $title . $args['after_title'];
}

		ob_start();
?>

<ul class="job_listing-company-social company-social">
	<?php do_action( 'job_listing_company_social_before' ); ?>

	<?php echo $items; ?>

	<?php do_action( 'job_listing_company_social_after' ); ?>
</ul>

<?php
		$content .= ob_get_clean();

		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
