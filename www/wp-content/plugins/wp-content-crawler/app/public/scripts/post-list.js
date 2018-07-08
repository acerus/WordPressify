jQuery(function($) {
    var $columnActive           = $(".column-active"),
        $columnActiveRecrawling = $(".column-active_recrawling"),
        $columnActivePostDeleting = $(".column-active_post_deleting")
    ;

    $columnActive.on("change", "input[type=checkbox]", handleCheckbox);
    $columnActiveRecrawling.on("change", "input[type=checkbox]", handleCheckbox);
    $columnActivePostDeleting.on("change", "input[type=checkbox]", handleCheckbox);

    function handleCheckbox(e) {
        var $self   = $(this);
        var checked = $self[0].checked;
        var postId  = $self.data("post-id");

        if(postId == undefined || !postId) {
            console.log("Post ID is not valid: " + postId);
            return;
        }

        //var metaKey = $self.closest('td').hasClass('column-active') ? '_active' : '_active_recrawling';
        var metaKey = $self.attr("name");
        var data = {};
        data[metaKey] = checked;

        $.post(ajaxurl, {
                wcc_nonce: $("#wcc_nonce").val(),
                action: 'wcc_site_list',
                post_id: postId,
                data: data
            })
            .done(function(response) {
                if(!response[metaKey]) {
                    // Reverse the checkbox status
                    $self[0].checked = !checked;
                }
            })
            .fail(function(response) {
                console.log("Request failed: " + response.responseText);
                console.log(response);

                // Reverse the checkbox status
                $self[0].checked = !checked;
            })
    }
});