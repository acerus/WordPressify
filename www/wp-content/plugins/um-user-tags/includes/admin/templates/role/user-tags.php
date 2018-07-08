<?php

$terms = get_terms( 'um_user_tag', array(
	'hide_empty' => 0,
	'parent' => 0
) );

if ( ! $terms )
	return '';

$tags_set = get_option( 'um_user_tags_filters' );

$options = array();
if ( ! empty( $tags_set ) ) {
	foreach ( $tags_set as $metakey => $i ) {
		$data = UM()->fields()->get_field( $metakey );
		if ( ! empty( $data ) ) {
			$term = get_term_by( 'id', $i, 'um_user_tag' );
			$options[ $metakey ] = $term->name . ' (' . $data['title'] . ')';
		}
	}
}

$role = $object['data']; ?>

<div class="um-admin-metabox">

	<?php $fields = array(
		'class'		=> 'um-role-user-tags um-half-column',
		'prefix_id'	=> 'role',
		'fields' => array(
			array(
				'id'		    => '_um_show_user_tags',
				'type'		    => 'checkbox',
				'label'    		=> __( 'Show user tags in profile head?', 'um-user-tags' ),
				'value' 		=> ! empty( $role['_um_show_user_tags'] ) ? $role['_um_show_user_tags'] : 0,
			)
		)
	);

	if ( $tags_set ) {
		$fields['fields'][] = array(
			'id'		=> '_um_user_tags_metakey',
			'type'		=> 'select',
			'label'    		=> __( 'Choose the user tags source to show in profile header', 'um-user-tags' ),
			'value' 		=> ! empty( $role['_um_user_tags_metakey'] ) ? $role['_um_user_tags_metakey'] : '',
			'options' 		=> $options,
			'conditional'	=> array( '_um_show_user_tags', '=', 1 )
		);
	} else {
		_e('You did not create any user tags fields yet.','um-user-tags');
	}

	UM()->admin_forms( $fields )->render_form(); ?>

	<div class="um-admin-clear"></div>
</div>