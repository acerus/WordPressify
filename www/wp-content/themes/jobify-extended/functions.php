<?php
/**
 * Jobify Extended child theme.
 *
 * Place any custom functionality/code snippets here.
 *
 * @since Jobify Classic 1.0.0
 */
function jobify_child_styles() {
    wp_enqueue_style( 'jobify-child', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'jobify_child_styles', 20 );

function filter_events_title_month( $title ) {
    if ( tribe_is_month() ) {
        $title = 'Month view page';
    }

    return $title;
}
add_filter( 'tribe_events_title_tag', 'filter_events_title_month' );
