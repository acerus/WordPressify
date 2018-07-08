<?php

add_filter( 'um_prepare_user_query_args', 'um_add_user_tags_to_query', 55, 2 );

/**
 * Display users which have only selected user tags
 *
 *
 * @param $query_args
 * @param $args
 * @return mixed
 */
function um_add_user_tags_to_query( $query_args, $args ) {
    if ( ! isset( $args['user_tags_on'] ) || ! $args['user_tags_on'] )
        return $query_args;

    $tags = get_option( 'um_user_tags_filters' );

    if ( empty( $tags ) )
        return $query_args;

    $meta_query = array();

    foreach ( $tags as $tag => $term ) {
        if ( empty( $args[ 'user_tags_' . $tag ] ) )
            continue;

        foreach ( $args[ 'user_tags_' . $tag ] as $term_id ) {
            $meta_query[] = array(
                array(
                    'key'       => $tag,
                    'value'     => $term_id,
                    'compare'   => '=',
                ),
                array(
                    'key'       => $tag,
                    'value'     => $term_id,
                    'compare'   => 'LIKE',
                ),
                array(
                    'key'       => $tag,
                    'value'     => trim( serialize( strval( $term_id ) ) ),
                    'compare'   => 'LIKE',
                ),
                'relation' => 'OR',
            );
        }
    }

    if ( empty( $meta_query ) )
        return $query_args;

    if ( count( $meta_query ) > 1 )
        $meta_query['relation'] = 'AND';

    $query_args['meta_query'][] = $meta_query;

    return $query_args;
}