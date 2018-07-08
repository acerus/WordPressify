<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
$meetup_options = get_option( IME_OPTIONS );
?>
<div class="ime_container">
    <div class="ime_row">
    	
    	<form method="post" id="ime_setting_form">                

            <h3 class="setting_bar"><?php esc_attr_e( 'Meetup Settings', 'import-meetup-events' ); ?></h3>
            <p><?php _e( 'You need a Meetup API key to import your events from Meetup.','import-meetup-events' ); ?> </p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Meetup API key','import-meetup-events' ); ?> : 
                        </th>
                        <td>
                            <input class="meetup_api_key" name="meetup[meetup_api_key]" type="text" value="<?php if ( isset( $meetup_options['meetup_api_key'] ) ) { echo $meetup_options['meetup_api_key']; } ?>" />
                            <span class="xtei_small">
                                <?php printf('%s <a href="https://secure.meetup.com/meetup_api/key/" target="_blank">%s</a>', __( 'Insert your meetup.com API key you can get it from', 'import-meetup-events' ), __( 'here', 'import-meetup-events' ) ); ?>
                            </span>
                        </td>
                    </tr>       
                    <tr>
                        <th scope="row">
                            <?php _e( 'Update existing events', 'import-meetup-events' ); ?> : 
                        </th>
                        <td>
                            <?php 
                            $update_meetup_events = isset( $meetup_options['update_events'] ) ? $meetup_options['update_events'] : 'no';
                            ?>
                            <input type="checkbox" name="meetup[update_events]" value="yes" <?php if( $update_meetup_events == 'yes' ) { echo 'checked="checked"'; } ?> />
                            <span class="xtei_small">
                                <?php _e( 'Check to updates existing events.', 'import-meetup-events' ); ?>
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php _e( 'Advanced Synchronization', 'import-meetup-events' ); ?> : 
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" />
                            <span>
                                <?php _e( 'Check to enable advanced synchronization, this will delete events which are removed from Meetup. Also, it deletes passed events.', 'import-meetup-events' ); ?>
                            </span>
                            <?php do_action( 'ime_render_pro_notice' ); ?>
                        </td>
                    </tr>
                
                </tbody>
            </table>
            <br/>

            <div class="ime_element">
                <input type="hidden" name="ime_action" value="ime_save_settings" />
                <?php wp_nonce_field( 'ime_setting_form_nonce_action', 'ime_setting_form_nonce' ); ?>
                <input type="submit" class="button-primary xtei_submit_button" style=""  value="<?php esc_attr_e( 'Save Settings', 'import-meetup-events' ); ?>" />
            </div>
            </form>
    </div>
</div>
