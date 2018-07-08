jQuery(function($) {
    var $details            = $('.details'),
        $infoToggleButtons  = $('.toggle-info-texts');

    /**
     * Handle form submissions
     */
    $('.tool-form').on('submit', function($e) {
        $e.preventDefault();
        if(processing) return;

        var processing = true;

        var $resultContainer = $(this).find('.test-results').first();
        var $contentContainer = $resultContainer.find(".content");
        $resultContainer
            .removeClass("hidden")
            .addClass("loading");
        $contentContainer.html("");

        $.post(ajaxurl, {
            wcc_nonce: $("#wcc_nonce").val(),
            //action: 'wcc_tools',
            action: pageActionKey,
            data: getFormData($(this))
        })
            .done(function(response) {
                if(response) {
                    $contentContainer.html(response.view);
                } else {
                    $contentContainer.html(wpcc.no_result);
                }
            })
            .fail(function(response) {
                console.log(response);
                $contentContainer.html(wpcc.an_error_occurred + ": " + response.responseText);
            })
            .always(function() {
                $resultContainer.removeClass("loading");
                processing = false;
            });
    });

    /**
     * Hide tool results
     */
    $details.on('click', '.hide-test-results', function($event) {
        $event.preventDefault();

        // Find closest tool results
        var $self = $(this);

        // Hide it
        $self.closest(".test-results").addClass("hidden");
    });

    /**
     * Handle toggling info texts
     */
    $infoToggleButtons.on('click', function($event) {
        $event.preventDefault();

        // Find closest info texts and show/hide them.
        var first = false;
        var show = false;
        $(this).closest('.details').find('.info-text').each(function() {
            var $self = $(this);

            // Get the first info text's visibility. If it is visible, then we're gonna hide all of the infos. If it is
            // hidden, we'll do otherwise. By this way, we can keep track of info texts in different detail boxes.
            if(!first) {
                show = $self.hasClass("hidden");
                first = true;
            }

            if(show) {
                $self.removeClass("hidden");
            } else {
                $self.addClass("hidden");
            }
        });
    });

    function getFormData($form){
        var unindexedArray = $form.serializeArray();
        var indexedArray = {};

        $.map(unindexedArray, function(n, i){
            indexedArray[n['name']] = n['value'];
        });

        return indexedArray;
    }

});