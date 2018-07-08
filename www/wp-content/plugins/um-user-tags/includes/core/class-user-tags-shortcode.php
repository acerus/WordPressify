<?php
namespace um_ext\um_user_tags\core;

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class User_Tags_Shortcode
 * @package um_ext\um_user_tags\core
 */
class User_Tags_Shortcode {


	/**
	 * User_Tags_Shortcode constructor.
	 */
	function __construct() {
		add_shortcode( 'ultimatemember_tags', array( &$this, 'ultimatemember_tags' ) );
	}


	/**
	 * Shortcode
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	function ultimatemember_tags( $args = array() ) {
		$defaults = array(
			'term_id'       => 0,
			'user_field'    => 0,
			'number'        => 0,
			'orderby'       => 'count',
			'order'         => 'desc'
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args );
		
		if ( ! $args['term_id'] ) {
			return '';
		}
		
		if ( $args['orderby'] != 'count' ) {
			$args['order'] = 'asc';
		}
		
		$terms = get_terms( 'um_user_tag', array(
			'hide_empty'    => 0,
			'parent'        => $args['term_id'],
			'number'        => $args['number'],
			'orderby'       => $args['orderby'],
			'order'         => $args['order']
		) );

		ob_start();

		if ( ! $terms ) {
			_e( 'There are no tags to display.', 'um-user-tags' );
		} else {
			$tags = get_option( 'um_user_tags_filters' );
			$members_page = ( ! $tags || ! in_array( $args['term_id'], $tags ) ) ? false : true;

			if ( empty( $tags ) ) {
				_e( 'There are no tags to display.', 'um-user-tags' );
			} else {
				//calculate count of members in tag
				//if there are more then 1 field for 1 parent tag - use new logic with parse users with current user_tags
				//else use old logic with $term->count
				if ( ! ( count( $tags ) == count( array_unique( array_values( $tags ) ) ) ) ) {
					foreach ( $terms as $term ) {
						$users = get_users( array(
							'meta_query' => array(
								array(
									'key'       => $args['user_field'],
									'compare'   => 'LIKE',
									'value'     => ':"' . $term->term_id . '";'
								)
							),
							'fields' => 'ids'
						) );

						$term->count = ! empty( $users ) ? count( $users ) : 0;
					}

					if ( $args['orderby'] == 'count' ) {
						usort( $terms, function ( $a, $b ) {
							if ( $a->count == $b->count ) {
								return 0 ;
							}
							return ( $a->count < $b->count ) ? 1 : -1;
						} );
					}
				} ?>

				<div class="um-user-tags-wdgt">

					<?php foreach ( $terms as $term ) {
						if ( $members_page ) {

							$search_tags = array_keys( $tags, $args['term_id'] );
							$link_args = array( 'um_search' => 1 );
							foreach ( $search_tags as $tag_name ) {
								if ( $tag_name != $args['user_field'] ) {
									continue;
								}
								$link_args[ $tag_name ] = $term->term_id;
							}

							$link = add_query_arg( $link_args, um_get_core_page( 'members' ) ); ?>

							<div class="um-user-tags-wdgt-item">
								<a href="<?php echo $link ?>" class="tag"><?php echo $term->name ?></a>
								<span class="count"><?php echo $term->count ?></span>
							</div>

						<?php } else { ?>

							<div class="um-user-tags-wdgt-item">
								<span class="tag"><?php echo $term->name ?></span>
								<span class="count"><?php echo $term->count ?></span>
							</div>

						<?php }
					} ?>

				</div>

			<?php }
		}
		$output = ob_get_clean();
		return $output;
	}

}