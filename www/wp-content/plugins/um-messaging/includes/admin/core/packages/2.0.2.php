<?php
if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;

$user_ids = get_users( array(
	'fields' => 'ids'
) );

$conversations = UM()->Messaging_API()->api()->table_name1;
$messages = UM()->Messaging_API()->api()->table_name2;

$wpdb->query(
	"DELETE
    FROM {$conversations}
    WHERE user_a NOT IN( '" . implode( "','", $user_ids ) . "' ) OR
          user_b NOT IN( '" . implode( "','", $user_ids ) . "' )"
);

$wpdb->query(
	"DELETE
    FROM {$messages}
    WHERE recipient NOT IN( '" . implode( "','", $user_ids ) . "' ) OR
          author NOT IN( '" . implode( "','", $user_ids ) . "' )"
);