jQuery(function($) {
    var $testerForm         = $("#tester-form"),
        $resultsContainer   = $("#test-results");

    var inputIdSiteId       = "#site_id",
        inputIdTestType     = "#test_type",
        inputIdTestUrlPart  = "#test_url_part";

    /**
     * Handle form submissions.
     */
    $testerForm.on("submit", function($event) {
        $event.preventDefault();
        var $self = $(this);

        var siteId      = $self.find(inputIdSiteId + " option:selected").val();
        var testType    = $self.find(inputIdTestType + " option:selected").val();
        var testUrlPart = $self.find(inputIdTestUrlPart).val();

        if(testUrlPart == undefined || !testUrlPart) return;

        // Clear the content
        $resultsContainer.html("");

        if($resultsContainer.hasClass("hidden")) $resultsContainer.removeClass("hidden");
        $resultsContainer.addClass("loading");

        $.post(ajaxurl, {
            wcc_nonce: $("#wcc_nonce").val(),
            //action: 'wcc_site_tester',
            action: pageActionKey,
            data: {
                "site_id":          siteId,
                "test_type":        testType,
                "test_url_part":    testUrlPart
            }
        })
            .done(function(response) {
                $resultsContainer.html(response.view
                    .replace(/\[\u0000/g, '[\\')
                    .replace(/\u0000/g, ':')
                );
                initializeTooltip();
            })
            .fail(function(response) {
                console.log(response);
                $resultsContainer.html(wpcc.an_error_occurred + ": " + response.responseText);
            })
            .always(function() {
                $resultsContainer.removeClass("loading");
            });
    });

    /**
     * Go to top
     */
    $resultsContainer.on('click', '.go-to-top', function($event) {
        $event.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, "slow");
    });

    /**
     * Go to details
     */
    $resultsContainer.on('click', '#go-to-details', function($e) {
        $e.preventDefault();
        $("html, body").animate({ scrollTop: $("#details").offset().top - 40 }, "slow");
    });

    /**
     * React to "test this" buttons near the category and page URLs
     */
    $resultsContainer.on('click', 'button.test-this', function($e) {
        $e.preventDefault();
        var $self = $(this);

        // Change the form inputs
        $testerForm.find(inputIdTestUrlPart).val($self.data("url"));
        $testerForm.find(inputIdTestType).val($self.data("type"));

        // Submit the form
        $testerForm.submit();
    });

    function initializeTooltip() {
        $('[data-toggle="tooltip"]').tooltip();
    }
});