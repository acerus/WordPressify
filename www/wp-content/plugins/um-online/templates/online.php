<div class="um-online" data-max="<?php echo $max; ?>">

	<?php $previous_user_id = um_user( 'ID' );
	foreach ( $online as $user => $last_seen ) {

		um_fetch_user( $user );

		//$user_roles = um_user( 'roles' );

		$user_meta = get_userdata( $user );
		$user_roles = $user_meta->roles;
		if ( $roles != 'all' && count( array_intersect( $user_roles, explode( ',', $roles ) ) ) <= 0 ) {
			continue;
		}

		$name = um_user( 'display_name' );
		if ( empty( $name ) ) {
			continue;
		} ?>

		<div class="um-online-user">
			<div class="um-online-pic">
				<a href="<?php echo um_user_profile_url(); ?>" class="um-tip-n" title="<?php echo $name; ?>">
					<?php echo get_avatar( um_user( 'ID' ), 40 ); ?>
				</a>
			</div>
		</div>

	<?php }

	if ( ! $previous_user_id ) {
		um_reset_user();
	} else {
		um_fetch_user( $previous_user_id );
	} ?>

	<div class="um-clear"></div>
</div>