<div class="um-admin-metabox">

	<?php $role = $object['data'];

	UM()->admin_forms( array(
		'class'		=> 'um-role-friends um-half-column',
		'prefix_id'	=> 'role',
		'fields' => array(
			array(
				'id'		    => '_um_can_friend',
				'type'		    => 'checkbox',
				'label'		    => __( 'Can friend others?','um-friends' ),
				'tooltip'	=> __( 'Can this role friend other members or not.','um-friends' ),
				'value'		    => isset( $role['_um_can_friend'] ) ? $role['_um_can_friend'] : 1,
			),
			array(
				'id'		    => '_um_can_friend_roles',
				'type'		    => 'select',
				'multi'		    => true,
				'label'		    => __( 'Can friend these user roles only','um-friends' ),
				'value'		    => ! empty( $role['_um_can_friend_roles'] ) ? $role['_um_can_friend_roles'] : array(),
				'options'		=>  UM()->roles()->get_roles(),
				'conditional'	=> array( '_um_can_friend', '=', '1' )
			)
		)
	) )->render_form(); ?>

	<div class="um-admin-clear"></div>
</div>