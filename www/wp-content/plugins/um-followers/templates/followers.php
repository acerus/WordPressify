<?php if ( $followers ) { ?>
	
	<?php foreach( $followers as $k => $arr ) {
		/**
		 * @var $user_id2;
		 */
	    extract( $arr );

		um_fetch_user( $user_id2 ); 
		
		?>
	
	<div class="um-followers-user">
	
		<a href="<?php echo um_user_profile_url(); ?>" class="um-followers-user-photo" title="<?php echo um_user('display_name'); ?>"><?php echo get_avatar( um_user('ID'), 50 ); ?></a>
		
		<div class="um-followers-user-btn">
			<?php
			if ( $user_id2 == get_current_user_id() ) {
				echo '<a href="' . um_edit_profile_url() . '" class="um-follow-edit um-button um-alt">' . __('Edit profile','um-followers') . '</a>';
			} else {
				echo UM()->Followers_API()->api()->follow_button( $user_id2, get_current_user_id() );
			}
			?>
		</div>
		
		<div class="um-followers-user-name">
			<a href="<?php echo um_user_profile_url(); ?>" title="<?php echo um_user('display_name'); ?>"><?php echo um_user('display_name'); ?></a>
			
			<?php do_action('um_followers_list_post_user_name', $user_id, $user_id2 ); ?>
			
			<?php if ( um_user('ID') == get_current_user_id() ) { ?>
			<span class="um-followers-user-span"><?php _e('You','um-followers'); ?></span>
			<?php } else if ( $user_id == get_current_user_id() && UM()->Followers_API()->api()->followed( get_current_user_id(), $user_id2 ) ) { ?>
			<span class="um-followers-user-span"><?php _e('Follows you','um-followers'); ?></span>
			<?php } ?>
			
			<?php do_action('um_followers_list_after_user_name', $user_id, $user_id2 ); ?>
			
		</div>
		
		<?php do_action('um_followers_list_pre_user_bio', $user_id, $user_id2 ); ?>
		
		<div class="um-followers-user-bio"><?php echo um_get_snippet( um_filtered_value('description'), 25); ?></div>
		
		<?php do_action('um_followers_list_post_user_bio', $user_id, $user_id2 ); ?>
		
	</div>
	
	<?php } ?>

<?php } else { ?>

	<div class="um-profile-note"><span><?php echo ( $user_id == get_current_user_id() ) ? __('You do not have any followers yet.','um-followers') : __('This user does not have any followers yet.','um-followers'); ?></span></div>
	
<?php } ?>