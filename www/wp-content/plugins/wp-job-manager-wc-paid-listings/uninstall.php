<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

delete_option( 'wcpl_db_version' );

$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'wcpl_user_packages' );
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'user_job_packages' );
