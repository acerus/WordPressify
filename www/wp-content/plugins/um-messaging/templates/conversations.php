<?php if ( ! empty( $conversations ) ) { ?>

	<div class="um um-viewing">
		<div class="um-message-conv">

			<?php $i = 0;
			foreach ( $conversations as $conversation ) {

				if ( $conversation->user_a == um_profile_id() ) {
					$user = $conversation->user_b;
				} else {
					$user = $conversation->user_a;
				}

				if ( UM()->Messaging_API()->api()->blocked_user( $user ) ) {
					continue;
				}

				if ( UM()->Messaging_API()->api()->hidden_conversation( $conversation->conversation_id ) ) {
					continue;
				}

				$i++;

				if ( $i == 1 && ! isset( $current_conversation ) ) {
					$current_conversation = $conversation->conversation_id;
				}

				um_fetch_user( $user );

				$user_name = ( um_user( 'display_name' ) ) ? um_user( 'display_name' ) : __( 'Deleted User', 'um-messaging' );

				$is_unread = UM()->Messaging_API()->api()->unread_conversation( $conversation->conversation_id, um_profile_id() ); ?>

				<a href="<?php echo add_query_arg( 'conversation_id', $conversation->conversation_id ); ?>" class="um-message-conv-item <?php if ( $conversation->conversation_id == $current_conversation ) echo 'active '; ?>" data-message_to="<?php echo $user; ?>" data-trigger_modal="conversation" data-conversation_id="<?php echo $conversation->conversation_id; ?>">

					<span class="um-message-conv-name"><?php echo $user_name; ?></span>

					<span class="um-message-conv-pic"><?php echo get_avatar( $user, 40 ); ?></span>

					<?php if ( $is_unread ) { ?>
						<span class="um-message-conv-new"><i class="um-faicon-circle"></i></span>
					<?php }

					do_action( 'um_messaging_conversation_list_name' ); ?>

				</a>

			<?php } ?>

		</div>

		<div class="um-message-conv-view">

			<?php $i = 0;
			foreach ( $conversations as $conversation ) {

				if ( isset( $current_conversation ) && $current_conversation != $conversation->conversation_id ) {
					continue;
				}

				if ( $conversation->user_a == um_profile_id() ) {
					$user = $conversation->user_b;
				} else {
					$user = $conversation->user_a;
				}

				if ( UM()->Messaging_API()->api()->blocked_user( $user ) ) {
					continue;
				}
				if ( UM()->Messaging_API()->api()->hidden_conversation( $conversation->conversation_id ) ) {
					continue;
				}

				$i++;
				if ( $i > 1 ) {
					continue;
				}

				um_fetch_user( $user );

				$user_name = ( um_user( 'display_name' ) ) ? um_user( 'display_name' ) : __( 'Deleted User', 'um-messaging' );

				UM()->Messaging_API()->api()->conversation_template( $user, $user_id );

			} ?>

		</div>
		<div class="um-clear"></div>
	</div>

	<?php do_action( 'um_messaging_after_conversations_list' );

} else { ?>

	<div class="um-message-noconv">
		<i class="um-icon-android-chat"></i>
		<?php _e( 'No chats found here', 'um-messaging' ); ?>
	</div>

<?php } ?>
