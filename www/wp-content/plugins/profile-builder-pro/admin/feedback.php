<?php
/* add the modal on the plugin screen and embed a polldaddy form in it */
add_action( 'admin_footer', 'wppb_add_feedback_modal' );
function wppb_add_feedback_modal(){
    global $current_screen;
    if( $current_screen->base == 'plugins' ){
        ?>
        <div id="wppb-feedback-modal" style="display:none;">
            <h1 style="padding-left:10px;padding-top:15px;"><?php _e('Quick Feedback', 'profile-builder'); ?></h1>
            <script type="text/javascript" charset="utf-8" src="https://secure.polldaddy.com/p/9985202.js"></script>
            <noscript><a href="https://polldaddy.com/poll/9985202/"><?php _e( 'Because we care about our clients, please leave us feedback on why you are no longer using our plugin.', 'profile-builder'); ?></a></noscript>
            <a href="#" class="button secondary wppb-feedback-skip"><?php _e('Skip and Deactivate', 'profile-builder'); ?></a>
        </div>
        <?php
    }
}

/* add the scripts for the modal on the plugin screen */
add_action( 'admin_footer', 'wppb_add_feedback_script' );
function wppb_add_feedback_script(){
    global $current_screen;
    if( $current_screen->base == 'plugins' ) {
        ?>
        <script>
        jQuery(function () {
            pluginSlug = 'profile-builder';// define the plugin slug here

            if (jQuery('tr[data-slug="' + pluginSlug + '"] .deactivate a').length != 0) {

                deactivationLink = jQuery('tr[data-slug="' + pluginSlug + '"] .deactivate a').attr('href');

                jQuery('tr[data-slug="' + pluginSlug + '"] .deactivate a').click(function (e){
                    e . preventDefault();
                    e . stopPropagation();
                    tb_show("Profile Builder Quick Feedback", "#TB_inline?width=740&height=500&inlineId=wppb-feedback-modal");
                    jQuery('#TB_ajaxContent').closest('#TB_window').css({ height : "auto", top: "50%", marginTop: "-300px" });
                });

                jQuery('.pds-vote-button').on('click', function(e){
                    if(jQuery('.pds-radiobutton').is(':checked')) {
                        self.parent.tb_remove();
                        window.location.href = deactivationLink;
                    }
                });

                jQuery('.wppb-feedback-skip').on('click', function(e){
                    e.preventDefault();
                    self.parent.tb_remove();
                    window.location.href = deactivationLink;                    
                });

            }
        });
        </script>
        <?php
    }
}

/* add styling for the modal */
add_action( 'admin_footer', 'wppb_add_feedback_style' );
function wppb_add_feedback_style(){
    global $current_screen;
    if( $current_screen->base == 'plugins' ) {
        ?>
        <style type="text/css">
            #TB_window .pds-box{
                border:0 !important;
            }
            #TB_window .pds-links{
                display:none;
            }
            #TB_window .pds-question-top{
                font-size:13px;
                font-weight:normal;
            }
            #TB_window .pds-answer{
                border:0;
            }
            #TB_window .pds-vote-button span{
                display:none;
            }
            #TB_window .pds-vote-button:after{
                content:"<?php _e('Submit and Deactivate', 'profile-builder')?>";
            }
            #TB_window .pds-vote-button{
                padding: 6px 14px;
                line-height: normal;
                font-size: 14px;
                font-weight: normal;
                vertical-align: middle;
                height: auto;
                margin-bottom: 4px;
                background: #0085ba;
                border-color: #0073aa #006799 #006799;
                box-shadow: 0 1px 0 #006799;
                color: #fff;
                text-decoration: none;
                text-shadow: 0 -1px 1px #006799, 1px 0 1px #006799, 0 1px 1px #006799, -1px 0 1px #006799;
                cursor: pointer;
                border-width: 1px;
                border-style: solid;
                -webkit-appearance: none;
                border-radius: 3px;
            }

            .wppb-feedback-skip{
                float: right;
                margin-top: -55px !important;
                margin-right: 10px !important;
            }
        </style>
        <?php
    }
}