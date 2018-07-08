<?php
/**
 * Sidebar Single Resume Top
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */
if ( 'side' == get_theme_mod( 'resume-display-sidebar', 'top' ) ) {
	return;
}

$args    = array(
	'before_widget' => '<aside class="widget widget--resume widget--resume-top">',
	'after_widget'  => '</aside>',
	'before_title'  => '<h3 class="widget-title widget-title--resume widget-title--resume-top">',
	'after_title'   => '</h3>',
);

$count = get_theme_mod( 'resume-display-sidebar-columns', 3 );
$columns = floor( 12 / $count );
?>

<div class="resume-meta-top row">

	<?php do_action( 'single_resume_info_start' ); ?>

	<div class="col-md-<?php echo 3 == $count ? '3' : $columns; ?> col-sm-6 col-xs-12">
		<?php
		if ( ! is_active_sidebar( 'single-resume-top-1' ) ) {
			the_widget( 'Jobify_Widget_Job_Company_Logo', array(), $args );
		} else {
			dynamic_sidebar( 'single-resume-top-1' );
		}
		?>
	</div>

	<?php if ( $count > 1 ) : ?>

	<div class="col-md-<?php echo 3 == $count ? '5' : $columns; ?> col-sm-6 col-xs-12">
		<?php
		if ( ! is_active_sidebar( 'single-resume-top-2' ) ) {
			the_widget( 'Jobify_Widget_Job_Share', array(
				'title' => __( 'Share Resume', 'jobify' ),
			), $args );

			if ( get_option( 'resume_manager_enable_skills' ) ) {
				the_widget( 'Jobify_Widget_Resume_Skills', array(
					'title' => __( 'Candidate Skills', 'jobify' ),
				), $args );
			}

			if ( get_option( 'resume_manager_enable_resume_upload' ) ) {
				the_widget( 'Jobify_Widget_Resume_File', array(
					'title' => __( 'Candidate Resume', 'jobify' ),
				), $args );
			}
		} else {
			dynamic_sidebar( 'single-resume-top-2' );
		}
		?>
	</div>

	<?php endif; if ( $count > 2 ) : ?>

	<div class="col-md-<?php echo 3 == $count ? '4' : $columns; ?> col-sm-6 col-xs-12">
		<?php
		if ( ! is_active_sidebar( 'single-resume-top-3' ) ) {
			the_widget( 'Jobify_Widget_Resume_Links', array(
				'title' => __( 'Candidate Details', 'jobify' ),
			), $args );
			the_widget( 'Jobify_Widget_Job_Apply', array(), $args );
		} else {
			dynamic_sidebar( 'single-resume-top-3' );
		}
		?>
	</div>

	<?php endif; if ( $count > 3 ) : ?>
		
	<div class="col-md-<?php echo 3 == $count ? '5' : $columns; ?> col-sm-6 col-xs-12">
		<?php dynamic_sidebar( 'single-resume-top-4' ); ?>
	</div>

	<?php endif; ?>

	<?php do_action( 'single_resume_meta_end' ); ?>

</div>
