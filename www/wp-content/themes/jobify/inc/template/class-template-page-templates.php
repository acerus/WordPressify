<?php
/**
 * Manage page templates.
 *
 * Unregisteres templates depending on active integrations and
 * provides a better UX for pages that do not use the editor.
 *
 * @since 3.0.0
 */
class Jobify_Template_Page_Templates {

	/**
	 * Hook in to WordPress
	 *
	 * @since 3.6.0
	 */
	public static function init() {
		add_filter( 'theme_page_templates', array( __CLASS__, 'wp_job_manager_resumes' ) );
		add_filter( 'theme_page_templates', array( __CLASS__, 'testimonials' ) );

		add_action( 'add_meta_boxes', array( __CLASS__, 'write_panel_setup' ), 0 );
	}

	/**
	 * Remove Resume Manager page templates if inactive.
	 *
	 * @since 3.0.0
	 *
	 * @param array $page_templates
	 * @return array $page_templates
	 */
	public static function wp_job_manager_resumes( $page_templates ) {
		if ( jobify()->get( 'wp-job-manager-resumes' ) ) {
			return $page_templates;
		}

		unset( $page_templates['page-templates/map-resumes.php'] );
		unset( $page_templates['page-templates/pricing-resumes.php'] );

		return $page_templates;
	}

	/**
	 * Remove Testimonials page templates if inactive.
	 *
	 * @since 3.0.0
	 *
	 * @param array $page_templates
	 * @return array $page_templates
	 */
	public static function testimonials( $page_templates ) {
		if ( jobify()->get( 'testimonials' ) ) {
			return $page_templates;
		}

		unset( $page_templates['page-templates/testimonials.php'] );

		return $page_templates;
	}

	/**
	 * Potentially remove editor support if the assigned page template
	 * does not output `the_content()`.
	 *
	 * @since 3.6.0
	 */
	public static function write_panel_setup() {
		global $post_type, $post;
		$page_template = $post->_wp_page_template;
		$content = $post->post_content;

		/* jobify.php */
		if ( 'page-templates/jobify.php' == $page_template ) {
			add_action( 'edit_form_after_title', array( __CLASS__, 'notice_template_jobify' ) );
			if ( ! $content ) {
				remove_post_type_support( $post_type, 'editor' );
			}
		}

		/* template-widgetized.php */
		if ( 'page-templates/template-widgetized.php' == $page_template ) {
			add_action( 'edit_form_after_title', array( __CLASS__, 'notice_template_widgetized' ) );
			if ( ! $content ) {
				remove_post_type_support( $post_type, 'editor' );
			}
		}

		/* map-jobs.php */
		if ( 'page-templates/map-jobs.php' == $page_template ) {
			add_action( 'edit_form_after_title', array( __CLASS__, 'notice_template_map_jobs' ) );
		}

		/* map-resumes.php */
		if ( 'page-templates/map-resumes.php' == $page_template ) {
			add_action( 'edit_form_after_title', array( __CLASS__, 'notice_template_map_resumes' ) );
		}

		/* pricing.php */
		if ( 'page-templates/pricing.php' == $page_template ) {
			add_action( 'edit_form_after_title', array( __CLASS__, 'notice_template_pricing' ) );
		}

		/* pricing-resumes.php */
		if ( 'page-templates/pricing-resumes.php' == $page_template ) {
			add_action( 'edit_form_after_title', array( __CLASS__, 'notice_template_pricing_resumes' ) );
		}

		/* testimonials.php */
		if ( 'page-templates/testimonials.php' == $page_template ) {
			add_action( 'edit_form_after_title', array( __CLASS__, 'notice_template_testimonials' ) );
			if ( ! $content ) {
				remove_post_type_support( $post_type, 'editor' );
			}
		}

	}

	/**
	 * Admin notice for: jobify.php (Home Template)
	 *
	 * @since 3.7.0
	 */
	public static function notice_template_jobify() {
		?>
		<div class="notice notice-warning inline">
			<p><?php _e( 'You are currently editing the page that is managed by widgets.', 'jobify' ); ?> <a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=widgets' ) ); ?>" class="button button-small"><?php _e( 'Manage Widgets', 'jobify' ); ?></a></p>
		</div><!-- .notice -->
		<?php
	}

	/**
	 * Admin notice for: template-widgetized.php
	 *
	 * @since 3.7.0
	 */
	public static function notice_template_widgetized() {
		?>
		<div class="notice notice-warning inline">
			<p><?php _e( 'You are currently editing the page that is managed by widgets.', 'jobify' ); ?> <a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=widgets' ) ); ?>" class="button button-small"><?php _e( 'Manage Widgets', 'jobify' ); ?></a></p>
		</div><!-- .notice -->
		<?php
	}

	/**
	 * Admin notice for: map-jobs.php
	 *
	 * @since 3.7.0
	 */
	public static function notice_template_map_jobs() {
		?>
		<div class="notice notice-warning inline">
			<p><?php _e( 'You are currently editing the page that has automatically generated content.', 'jobify' ); ?></p>
		</div><!-- .notice -->
		<?php
	}

	/**
	 * Admin notice for: map-resumes.php
	 *
	 * @since 3.7.0
	 */
	public static function notice_template_map_resumes() {
		?>
		<div class="notice notice-warning inline">
			<p><?php _e( 'You are currently editing the page that has automatically generated content.', 'jobify' ); ?></p>
		</div><!-- .notice -->
		<?php
	}

	/**
	 * Admin notice for: pricing.php
	 *
	 * @since 3.7.0
	 */
	public static function notice_template_pricing() {
		?>
		<div class="notice notice-warning inline">
			<p><?php _e( 'You are currently editing the page that has automatically generated content.', 'jobify' ); ?></p>
		</div><!-- .notice -->
		<?php
	}

	/**
	 * Admin notice for: pricing-resumes.php
	 *
	 * @since 3.7.0
	 */
	public static function notice_template_pricing_resumes() {
		?>
		<div class="notice notice-warning inline">
			<p><?php _e( 'You are currently editing the page that has automatically generated content.', 'jobify' ); ?></p>
		</div><!-- .notice -->
		<?php
	}

	/**
	 * Admin notice for: template-archive-job_listing.php
	 *
	 * @since 3.7.0
	 */
	public static function notice_template_testimonials() {
		?>
		<div class="notice notice-warning inline">
			<p><?php _e( 'You are currently editing the page that has automatically generated content.', 'jobify' ); ?></p>
		</div><!-- .notice -->
		<?php
	}

}

Jobify_Template_Page_Templates::init();
