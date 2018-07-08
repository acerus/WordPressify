<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
global $ime_events;
$open_source_support_url = 'https://wordpress.org/support/plugin/import-meetup-events/';
$support_url = 'https://xylusthemes.com/support/?utm_source=insideplugin&utm_medium=web&utm_content=sidebar&utm_campaign=freeplugin';

$review_url = 'https://wordpress.org/support/plugin/import-meetup-events/reviews/?rate=5#new-post';
$facebook_url = 'https://www.facebook.com/xylusinfo/';
$twitter_url = 'https://twitter.com/XylusThemes/';

?>
<div class="wpea_container">
    <div class="wpea_row">
        <div class="wpea-column support_well">
        	<h3><?php esc_attr_e( 'Getting Support', 'import-meetup-events' ); ?></h3>
            <p><?php _e( 'Thanks you for using Import Meetup Events, We are sincerely appreciate your support and we’re excited to see you using our plugins.','import-meetup-events' ); ?> </p>
            <p><?php _e( 'Our support team is always around to help you.','import-meetup-events' ); ?></p>
                
            <p><strong><?php _e( 'Looking for free support?','import-meetup-events' ); ?></strong></p>
            <a class="button button-secondary" href="<?php echo $open_source_support_url; ?>" target="_blank" >
                <?php _e( 'Open-source forum on WordPress.org','import-meetup-events' ); ?>
            </a>

            <p><strong><?php _e( 'Looking for more immediate support?','import-meetup-events' ); ?></strong></p>
            <p><?php _e( 'We offer premium support on our website with the purchase of our premium plugins.','import-meetup-events' ); ?>
            </p>
            
            <a class="button button-primary" href="<?php echo $support_url; ?>" target="_blank" >
                <?php _e( 'Contact us directly (Premium Support)','import-meetup-events' ); ?>
            </a>

            <p><strong><?php _e( 'Enjoying Import Meetup Events or have feedback?','import-meetup-events' ); ?></strong></p>
            <a class="button button-secondary" href="<?php echo $review_url; ?>" target="_blank" >Leave us a review</a> 
            <a class="button button-secondary" href="<?php echo $twitter_url; ?>" target="_blank" >Follow us on Twitter</a> 
            <a class="button button-secondary" href="<?php echo $facebook_url; ?>" target="_blank" >Like us on Facebook</a>
        </div>

        <?php 
        $plugins = array();
        $plugin_list = $ime_events->admin->get_xyuls_themes_plugins();
        if( !empty( $plugin_list ) ){
            foreach ($plugin_list as $key => $value) {
                $plugins[] = $ime_events->admin->get_wporg_plugin( $key );
            }
        }
        ?>
        <div class="" style="margin-top: 20px;">
            <h3 class="setting_bar"><?php _e( 'Plugins you should try','import-meetup-events' ); ?></h3>
            <?php 
            if( !empty( $plugins ) ){
                foreach ($plugins as $plugin ) {
                    ?>
                    <div class="plugin_box">
                        <?php if( $plugin->banners['low'] != '' ){ ?>
                            <img src="<?php echo $plugin->banners['low']; ?>" class="plugin_img" title="<?php echo $plugin->name; ?>">
                        <?php } ?>                    
                        <div class="plugin_content">
                            <h3><?php echo $plugin->name; ?></h3>

                            <?php wp_star_rating( array(
                            'rating' => $plugin->rating,
                            'type'   => 'percent',
                            'number' => $plugin->num_ratings,
                            ) );?>

                            <?php if( $plugin->version != '' ){ ?>
                                <p><strong><?php _e( 'Version:','import-meetup-events' ); ?> </strong><?php echo $plugin->version; ?></p>
                            <?php } ?>

                            <?php if( $plugin->requires != '' ){ ?>
                                <p><strong><?php _e( 'Requires:','import-meetup-events' ); ?> </strong> <?php _e( 'WordPress ','import-meetup-events' ); echo $plugin->requires; ?>+</p>
                            <?php } ?>

                            <?php if( $plugin->active_installs != '' ){ ?>
                                <p><strong><?php _e( 'Active Installs:','import-meetup-events' ); ?> </strong><?php echo $plugin->active_installs; ?>+</p>
                            <?php } ?>

                            <?php //print_r( $plugin ); ?>
                            <a class="button button-secondary" href="<?php echo admin_url( 'plugin-install.php?tab=plugin-information&plugin='. $plugin->slug.'&TB_iframe=1&width=772&height=600'); ?>" target="_blank">
                                <?php _e( 'Install Now','import-meetup-events' ); ?>
                            </a>
                            <a class="button button-primary" href="<?php echo $plugin->homepage . '?utm_source=crosssell&utm_medium=web&utm_content=supportpage&utm_campaign=freeplugin'; ?>" target="_blank">
                                <?php _e( 'Buy Now','import-meetup-events' ); ?>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
            <div style="clear: both;">
        </div>
    </div>
</div>
