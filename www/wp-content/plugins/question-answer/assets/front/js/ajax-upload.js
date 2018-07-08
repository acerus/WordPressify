jQuery(document).ready(function ($) {
	

	
    var CLASSIFIED_MAKER_FILE_UPLOAD = {
        init:function () {

            this.attach();
            
        },
        attach:function () {
				
				

            if (qa_file_uploader.upload_enabled !== '1') {
                return
            }

            var uploader = new plupload.Uploader(qa_file_uploader.plupload);

            $('#file-uploader').click(function (e) {
				
				alert('Hello');
                uploader.start();
				
                // To prevent default behavior of a tag
                e.preventDefault();
            });

            //initilize  wp plupload
            uploader.init();



            uploader.bind('FilesAdded', function (up, files) {


                $.each(files, function (i, file) {
					
					if(file)
						{
							$('#file-upload-container .loading').css('display','block');
							$('#file-upload-container .loading').html(file.name+', Size: '+plupload.formatSize(file.size));
						}
					
					
							
                });

               
			   
                uploader.start();
            });


            // On erro occur
            uploader.bind('Error', function (up, err) {
				
				$('#file-upload-container .loading').html('Error: '+err.code+', Message: '+err.message+'File:'+err.file.name);
				$('#file-upload-container .loading').fadeIn();

                
            });

            uploader.bind('FileUploaded', function (up, file, response) {
                var result = $.parseJSON(response.response);

                if (result.success) {
					//$('#file-upload-container .loading').fadeOut();
					
					//alert(result.html.attach_src);
					
					var attach_src = result.html.attach_src;
					var attach_id = result.html.attach_id;
					var attach_title = result.html.attach_title;									
					
					var html = '<div attach_id="'+attach_id+'"  class="file"><div class="preview"><img src="'+attach_src+'" title="'+attach_title+'" /></div><div class="name">'+attach_title+'</div><span attach_id="'+attach_id+'" class="remove"><i class="fa fa-times"></i></span><span class="move"><i class="fa fa-sort"></i></span></div>';
					
					
                    $('#file-upload-container').prepend(html);
					
					ads_thumbs  = $('#qa_ads_thumbs').val();
					
					if(ads_thumbs==''){
						ads_thumbs = attach_id;

						}
					else{
						ads_thumbs = ads_thumbs+','+attach_id;
						}
					

					
					$('#qa_ads_thumbs').val(ads_thumbs);
					
					
                   //var img_src = $('#uploaded-image-container img').attr('src');
				   
					//$('#file-upload-container').prev().val(img_src);
						   				
                   
                }
            });


        },

       


    };

    CLASSIFIED_MAKER_FILE_UPLOAD.init();
});