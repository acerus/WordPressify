<?php

$wppb_generalSettings = get_option( 'wppb_general_settings', 'not_found' );
if( $wppb_generalSettings != 'not_found' ) {
    if( ! empty( $wppb_generalSettings['contentRestriction'] ) && ( $wppb_generalSettings['contentRestriction'] == 'yes' ) ) {
        include_once 'content-restriction-meta-box.php';
        include_once 'content-restriction-functions.php';
        include_once 'content-restriction-filtering.php';

        add_action( 'admin_menu', 'wppb_content_restriction_submenu', 10 );
        add_action( 'admin_enqueue_scripts', 'wppb_content_restriction_scripts_styles' );
    }
}

function wppb_content_restriction_submenu() {

    add_submenu_page( 'profile-builder', __( 'Content Restriction', 'profile-builder' ), __( 'Content Restriction', 'profile-builder' ), 'manage_options', 'profile-builder-content_restriction', 'wppb_content_restriction_content' );

}

function wppb_content_restriction_settings_defaults() {

    add_option( 'wppb_content_restriction_settings',
        array(
            'restrict_type'         =>  'message',
            'redirect_url'          =>  '',
            'message_logged_out'    =>  '',
            'message_logged_in'     =>  '',
            'post_preview'          =>  'none',
            'post_preview_length'   =>  '20',
        )
    );

}

function wppb_content_restriction_content() {

    wppb_content_restriction_settings_defaults();

    $wppb_content_restriction_settings = get_option( 'wppb_content_restriction_settings', 'not_found' );

    ?>
    <div class="wrap wppb-content-restriction-wrap">
        <h2><?php _e( 'Content Restriction', 'profile-builder' ); ?></h2>

        <form method="post" action="options.php">
            <?php settings_fields( 'wppb_content_restriction_settings' ); ?>

            <div id="wppb-settings-content-restriction">
                <div class="wppb-restriction-fields-group">
                    <label class="wppb-restriction-label"><?php _e( 'Type of Restriction', 'profile-builder' ); ?></label>

                    <div class="wppb-restriction-type">
                        <label for="wppb-content-restrict-type-message">
                            <input type="radio" id="wppb-content-restrict-type-message" value="message" <?php echo ( ( $wppb_content_restriction_settings != 'not_found' && $wppb_content_restriction_settings['restrict_type'] == 'message' ) ? 'checked="checked"' : '' ); ?> name="wppb_content_restriction_settings[restrict_type]">
                            <?php _e( 'Message', 'profile-builder' ); ?>
                        </label>

                        <label for="wppb-content-restrict-type-redirect">
                            <input type="radio" id="wppb-content-restrict-type-redirect" value="redirect" <?php echo ( ( $wppb_content_restriction_settings != 'not_found' && $wppb_content_restriction_settings['restrict_type'] == 'redirect' ) ? 'checked="checked"' : '' ); ?> name="wppb_content_restriction_settings[restrict_type]">
                            <?php _e( 'Redirect', 'profile-builder' ); ?>
                        </label>

                        <p class="description" style="margin-top: 10px;"><?php echo __( 'If you select "Message", the post\'s content will be protected by being replaced with a custom message.', 'profile-builder' ); ?></p>
                        <p class="description"><?php echo __( 'If you select "Redirect", the post\'s content will be protected by redirecting the user to the URL you specify. The redirect happens only when accessing a single post. On archive pages the restriction message will be displayed, instead of the content.', 'profile-builder' ); ?></p>
                    </div>
                </div>

                <div class="wppb-restriction-fields-group">
                    <label class="wppb-restriction-label"><?php _e( 'Redirect URL', 'profile-builder' ); ?></label>
                    <input type="text" class="widefat" name="wppb_content_restriction_settings[redirect_url]" value="<?php echo ( ( $wppb_content_restriction_settings != 'not_found' && ! empty( $wppb_content_restriction_settings['redirect_url'] ) ) ? esc_url( $wppb_content_restriction_settings['redirect_url'] ) : '' ); ?>" />
                </div>

                <div class="wppb-restriction-fields-group">
                    <label class="wppb-restriction-label"><?php _e( 'Message for logged-out users', 'profile-builder' ); ?></label>
                    <?php wp_editor( wppb_get_restriction_content_message( 'logged_out' ), 'message_logged_out', array( 'textarea_name' => 'wppb_content_restriction_settings[message_logged_out]', 'editor_height' => 250 ) ); ?>
                </div>

                <div class="wppb-restriction-fields-group">
                    <label class="wppb-restriction-label"><?php _e( 'Message for logged-in users', 'profile-builder' ); ?></label>
                    <?php wp_editor( wppb_get_restriction_content_message( 'logged_in' ), 'message_logged_in', array( 'textarea_name' => 'wppb_content_restriction_settings[message_logged_in]', 'editor_height' => 250 ) ); ?>
                </div>

                <div class="wppb-restriction-fields-group">
                    <label class="wppb-restriction-label" for="restricted-posts-preview"><?php echo __( 'Restricted Posts Preview', 'profile-builder' ) ?></label>

                    <div class="wppb-restriction-post-preview">
                        <div>
                            <label>
                                <input type="radio" name="wppb_content_restriction_settings[post_preview]" value="none" <?php echo ( ( $wppb_content_restriction_settings != 'not_found' ) && $wppb_content_restriction_settings['post_preview'] == 'none' ? 'checked' : '' ); ?> />
                                <span><?php echo __( 'None', 'profile-builder' ); ?></span>
                            </label>
                        </div>

                        <div>
                            <label>
                                <input type="radio" name="wppb_content_restriction_settings[post_preview]" value="trim-content" <?php echo ( ( $wppb_content_restriction_settings != 'not_found' ) && $wppb_content_restriction_settings['post_preview'] == 'trim-content' ? 'checked' : '' ); ?> />

                                <span>
                                    <?php echo sprintf( __( 'Show the first %s words of the post\'s content', 'profile-builder' ), '<input name="wppb_content_restriction_settings[post_preview_length]" type="text" value="'. ( $wppb_content_restriction_settings != 'not_found' && ! empty( $wppb_content_restriction_settings['post_preview_length'] ) ? esc_attr( $wppb_content_restriction_settings['post_preview_length'] ) : 20 ) .'" style="width: 50px;" />' ); ?>
                                 </span>
                            </label>
                        </div>

                        <div>
                            <label>
                                <input type="radio" name="wppb_content_restriction_settings[post_preview]" value="more-tag" <?php echo ( ( $wppb_content_restriction_settings != 'not_found' ) && $wppb_content_restriction_settings['post_preview'] == 'more-tag' ? 'checked' : '' ); ?> />
                                <span><?php echo __( 'Show the content before the "more" tag', 'profile-builder' ); ?></span>
                            </label>
                        </div>

                        <p class="description"><?php echo __( 'Show a portion of the restricted post to logged-out users or users that are not allowed to see it.', 'profile-builder' ); ?></p>
                    </div>
                </div>
            </div>

            <?php submit_button( __( 'Save Settings', 'profile-builder' ) ); ?>
        </form>
    </div>
    <?php

}

function wppb_content_restriction_scripts_styles() {

    wp_enqueue_script( 'wppb_content_restriction_js', plugin_dir_url( __FILE__ ) .'assets/js/content-restriction.js', array( 'jquery' ), PROFILE_BUILDER_VERSION );
    wp_enqueue_style( 'wppb_content_restriction_css', plugin_dir_url( __FILE__ ) .'assets/css/content-restriction.css', array(), PROFILE_BUILDER_VERSION );

}

