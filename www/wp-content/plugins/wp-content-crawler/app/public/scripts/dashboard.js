function l(variable) { console.log(variable); }
jQuery(function($) {

    var $wccNonce = $("#wcc_nonce"),
        refreshInputSelector = 'input[name="refresh"]',
        $nextRefreshIn = $(".next-refresh-in"),
        $remaining = $(".remaining"),
        $title = $("head > title").first(),
        remaining = 0,
        timeoutId = null,
        selectorDashboardContainer = "#dashboard-container",
        minSeconds = 9,
        titleRemainingRegex = /^(\([0-9]+\)\s)?/;

    /**
     * Counts down and refreshes the dashboard when the countdown reaches 0.
     */
    var refreshTimeout = function() {
        // If remaining time is less than 0, clear the time out and stop refreshing.
        if(remaining < 0) {
            clearTimeout(timeoutId);
            return;
        }

        // Show the count down text
        $nextRefreshIn.removeClass("hidden");

        // Set the remaining time
        updateRemainingText();

        // If remaining seconds is less than 1, refresh the dashboard.
        if(remaining == 0) {
            refresh();

        // Otherwise, set the timeout again to keep counting down.
        } else {
            remaining--;
            timeoutId = setTimeout(refreshTimeout, 1000);
        }

    };

    /**
     * Listen to the changes made on the refresh time input
     */
    $(document).on('change', refreshInputSelector, function() {
        var $self = $(this),
            val = $self.val();

        removeLoading($self);

        // If there is a timeout, first, cancel it.
        if(timeoutId != null) clearTimeout(timeoutId);

        // If the value is valid, initialize a new countdown.
        if(val != undefined && val > minSeconds) {
            remaining = val;
            updateRemainingText();

            // Call refreshTimeout immediately
            timeoutId = setTimeout(refreshTimeout, 0);

        } else {
            remaining = -1;

            updateRemainingText();

            // Hide the count down text
            $nextRefreshIn.addClass("hidden");
        }
    });

    /**
     * Refreshes the dashboard
     */
    function refresh() {
        // Hide the count down text
        $nextRefreshIn.addClass("hidden");

        var $refreshInput = $(refreshInputSelector);

        setLoading($refreshInput);

        $.post(ajaxurl, {
                wcc_nonce: $wccNonce.val(),
                action: pageActionKey,
                data: {
                    cmd: "refresh_dashboard"
                }
            })
            .done(function(response){
                var $newView = $(response.view);

                // Replace the dashboard
                $(selectorDashboardContainer).replaceWith($newView.find(selectorDashboardContainer));

                // Flash the backgrounds
                $(".content .details").each(function() {
                    flashBackground($(this));
                })

            })
            .fail(function(response) {
                l("fail");
                console.log(response);
            })
            .always(function() {
                removeLoading($refreshInput);

                // Trigger a change on time input so that a new count down can be started.
                $(refreshInputSelector).trigger("change");
            });
    }

    /**
     * Updates the HTML of the element that shows the time remaining to refresh the dashboard
     */
    function updateRemainingText() {
        if(remaining < 0) {
            // Remove the title's countdown text
            $title.text($title.text().replace(titleRemainingRegex, ""));

        } else {
            $remaining.html(remaining);

            // Show the remaining count in the title as well
            $title.text($title.text().replace(titleRemainingRegex, "(" + remaining + ") "));
        }
    }

    /**
     * Flashes the background color of the element from green to white.
     * @param $element
     */
    function flashBackground($element) {
        $element.stop().css("background-color", "#b8ea84")
            .animate({ backgroundColor: "#FFFFFF"}, 1000);
    }

    /**
     * Adds "loading" class to an element.
     * @param $element
     */
    function setLoading($element) {
        $element.addClass("loading");
    }

    /**
     * Removes "loading" class from an element.
     * @param $element
     */
    function removeLoading($element) {
        $element.removeClass("loading");
    }

    /**
     * Submit table forms when select element is changed
     */
    $(document).on('change', 'h2 .header select', function($e) {
        var $self = $(this),
            val = $self.val(),
            name = $self.attr("name");

        if(val == undefined || !val || name == undefined || !name) return;

        setLoading($self);

        $.post(ajaxurl, {
                wcc_nonce: $wccNonce.val(),
                action: pageActionKey,
                data: {
                    cmd: "refresh_section",
                    value: val,
                    optionKey: name
                }
            })
            .done(function(response) {
                // This is the entire dashboard. Get the related section from it and refresh only that section.
                var $newView = $(response.view),
                    inputSelector = 'select[name="' + name + '"]';

                $self.closest(".details").replaceWith($newView.find(inputSelector).first().closest('.details'));

                flashBackground($(inputSelector).closest(".details").first());

            })
            .fail(function(response) {
                l("Fail");
                console.log(response);
            })
            .always(function() {
                removeLoading($self);
            });

    });


});