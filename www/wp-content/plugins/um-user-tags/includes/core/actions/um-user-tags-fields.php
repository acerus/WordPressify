<?php if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Member directory header
 *
 * @param $args
 */
function um_user_tags_show_filters( $args ) {
	if ( UM()->User_Tags_API()->filters ) {
		echo '<div class="um-user-tags-md">';
		foreach( UM()->User_Tags_API()->filters as $metakey => $term_id ) {
			$term_field = is_numeric($term_id)?'id':'slug';
			$term = get_term_by($term_field, $term_id, 'um_user_tag');
			$remove_filter = remove_query_arg( $metakey );
			if( $term ){
				echo '<span>' . $term->name . '<a href="'. $remove_filter .'"><i class="um-icon-close"></i></a></span>';
			}
		}
		echo '</div>';
	}
}
add_action( 'um_members_directory_head', 'um_user_tags_show_filters', 100 );


/**
 * Modal field settings
 *
 * @param $val
 */
function um_admin_field_edit_hook_tag_source( $val ) {

	$parent_tags = get_terms( 'um_user_tag', array(
		'hide_empty' => 0,
		'parent' => 0
	) ); ?>

	<p>
		<label for="_tag_source"><?php _e( 'Select a user tags source', 'um-user-tags' ); ?> <?php UM()->tooltip( __( 'Choose the user tags type that user can select from', 'um-user-tags' ) ); ?></label>
		<select name="_tag_source" id="_tag_source" style="width: 100%">
			<?php foreach ( $parent_tags as $tag ) { ?>
				<option value="<?php echo $tag->term_id; ?>" <?php selected( $tag->term_id, $val ); ?>><?php echo $tag->name; ?></option>
			<?php } ?>
		</select>
	</p>

	<?php
}
add_action( 'um_admin_field_edit_hook_tag_source', 'um_admin_field_edit_hook_tag_source' );