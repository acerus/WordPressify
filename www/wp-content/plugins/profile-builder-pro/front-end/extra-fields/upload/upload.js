jQuery(document).ready(function(){
    if( typeof wp.media != "undefined" ){
        // Uploading files
        var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id

        jQuery(document).on( 'click', '.wppb_upload_button', function( event ){
            event.preventDefault();

            var set_to_post_id = ''; // Set this

            var file_frame;
            var uploadInputId = jQuery( this ).data( 'upload_input' );
            var uploadButton = jQuery( this );

            /* set default tab to upload file */
            wp.media.controller.Library.prototype.defaults.contentUserSetting = false;
            wp.media.controller.Library.prototype.defaults.router = false;
            wp.media.controller.Library.prototype.defaults.searchable = true;
            wp.media.controller.Library.prototype.defaults.sortable = false;

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                // Set the post ID to what we want
                file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                // Open frame
                file_frame.open();
                return;
            } else {
                // Set the wp.media post id so the uploader grabs the ID we want when initialised
                wp.media.model.settings.post.id = set_to_post_id;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: jQuery( this ).data( 'uploader_title' ),
                button: {
                    text: jQuery( this ).data( 'uploader_button_text' )
                },
                multiple: false // Set to true to allow multiple files to be selected
            });

            /* send the meta_name of the field */
            file_frame.uploader.options.uploader['params']['wppb_upload'] = 'true';
            file_frame.uploader.options.uploader['params']['meta_name'] = jQuery( this ).data( 'upload_mn' );

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
                // We set multiple to false so only get one image from the uploader
                attachments = file_frame.state().get('selection').toJSON();
                var attids = [];

                for( var i=0;i < attachments.length; i++ ){
                    // Do something with attachment.id and/or attachment.url here
                    attids.push( attachments[i].id );
                    result = '<div class="upload-field-details" id="'+ uploadInputId +'_info_container" data-attachment_id="'+ attachments[i].id +'">';
                    if( attachments[i].sizes != undefined ){
                        if( attachments[i].sizes.thumbnail != undefined )
                            thumb = attachments[i].sizes.thumbnail;
                        else
                            thumb = attachments[i].sizes.full;
                        thumbnailUrl = thumb.url;
                    }
                    else{
                        thumbnailUrl = attachments[i].icon;
                    }

                    result += '<div class="file-thumb">';
                    result += '<a href="'+ attachments[i].url +'" target="_blank" class="wppb-attachment-link">';
                    result += '<img width="80" height="80" src="'+ thumbnailUrl +'"/>';
                    result += '</a>';
                    result += '</div>';
                    result += '<p><span class="file-name">'+attachments[i].filename+'</span><span class="file-type">'+attachments[i].mime +'</span><span class="wppb-remove-upload" tabindex="0">Remove</span></p></div>';

                    // if multiple upload false remove previous upload details
                    if( uploadButton.data( 'multiple_upload' ) == false ){
                        jQuery( '.upload-field-details', uploadButton.parent() ).remove();
                    }

                    uploadButton.before( result );
                    uploadButton.hide();

                }
                // turn into comma separated string
                attids = attids.join(',');
                jQuery( 'input[id="'+uploadInputId+'"]', uploadButton.parent() ).val( attids );

                // Restore the main post ID
                wp.media.model.settings.post.id = wp_media_post_id;
            });

            // Finally, open the modal
            file_frame.open();
            // remove tabs from the top ( this is done higher in the code when setting router to false )
            //jQuery('.media-frame-router').remove();

            if( jQuery( this ).data( 'uploader_logged_in' ) == undefined ){
                jQuery('.media-frame-title').append('<style type="text/css">label.setting, .edit-attachment{display:none !important;}</style>');
            }
        });

        // Restore the main ID when the add media button is pressed
        jQuery('a.add_media').on('click', function() {
            wp.media.model.settings.post.id = wp_media_post_id;
        });

        jQuery(document).on('keypress', '.wppb-remove-upload', function(e){
            if(e.which == 13) {
                jQuery(this).trigger('click');
            }
        });

        jQuery(document).on('click', '.wppb-remove-upload', function(e){
            if( confirm( 'Are you sure ?' ) ){
                /* update hidden input */
                removedAttachement = jQuery(this).parent().parent('.upload-field-details').data('attachment_id');
                upload_input = jQuery(this).closest('li, td, p.form-row, div.form-row').find('input[type="hidden"]');
                uploadAttachemnts = upload_input.val();
                uploadAttachemntsArray = uploadAttachemnts.split(',');
                newuploadAttachments = [];
                for (var i = 0; i < uploadAttachemntsArray.length; i++) {
                    if (uploadAttachemntsArray[i] != removedAttachement)
                        newuploadAttachments.push(uploadAttachemntsArray[i]);
                }
                newuploadAttachments = newuploadAttachments.join(',');
                upload_input.val(newuploadAttachments);

                /* remove the attachment details */
                jQuery(this).parent().parent('.upload-field-details').next('a').show();
                jQuery(this).parent().parent('.upload-field-details').remove();
            }
        });
    }
});