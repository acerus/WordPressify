function l(variable) { console.log(variable); }

jQuery(function($) {

    /*
     * CSS SELECTOR TOOLBAR
     */

    /**
     * A class to handle CSS selector toolbar actions
     *
     * @constructor
     */
    window.CSSSelectorToolbar = function() {

    };

    CSSSelectorToolbar.prototype = {
        constructor: CSSSelectorToolbar,

        /**
         * Update CSS selector input's value
         * @param {string} newValue
         */
        updateInput: function(newValue) {
            var $cssSelectorInput = this.getCssSelectorInput();
            $cssSelectorInput.val(newValue);

            // Flash the background color of the input
            devTools.flashBackground($cssSelectorInput);
        },

        /**
         * Click callback for test button in CSS selector toolbar
         * @param {object} e Event
         */
        onClickTest: function(e) {
            var $button = $(this),
                $input = cssSelectorToolbar.getCssSelectorInput(),
                val = $input.val()
                ;

            if(val == undefined || !val.length) return;

            var $inputTestButtonBehavior = $(dtv.optUseTestButtonBehaviorSelector).first(),
                testButtonBehavior = $inputTestButtonBehavior.val(),
                testViaJS = testButtonBehavior != 'php',
                testViaPHP = testButtonBehavior != 'js'
                ;

            // Conduct PHP test
            if(testViaPHP) {
                // Conduct server-side test
                var data = $button.data("wcc"),
                    $contents = iframeHandler.getIframeContents();

                // Remove hover class from the iframe content to show the unchanged results
                iframeHandler.clearHighlights();
                data["content"] = $contents.find("html").html();
                data["selector"] = val;

                data["url"] = iframeHandler.getCurrentUrl();
                data["formItemName"] = dtv.cssInputId;
                data["serializedValues"] = $("<input/>").attr("name", dtv.cssInputId + "[0][selector]").val(val).serialize();

                // Add settings to the data
                if(WPCC_POST_SETTINGS != undefined) {
                    data = WPCC_POST_SETTINGS.addSettingsToAjaxData(data);
                }

                var $resultContainer = $(dtv.toolbarTestResultsContainerSelector).first(),
                    $contentContainer = $(dtv.toolbarTestResultsContentContainerSelector).first();

                $resultContainer
                    .removeClass("hidden")
                    .addClass("loading");
                $contentContainer.html("");

                // Test the selector via PHP as well
                $.post(ajaxurl, {
                        wcc_nonce: dtv.$wccNonce.val(),
                        action: pageActionKey,
                        data: data
                    })
                    .done(function (response) {
                        // Show the results
                        $contentContainer.html(response.view);

                    })
                    .fail(function (response) {
                        $contentContainer.html(wpcc.an_error_occurred + " <br />" + response.responseText);
                        console.log(response);
                    })
                    .always(function () {
                        // Remove loading class
                        $resultContainer.removeClass("loading");

                        iframeHandler.setIframeHeight();
                    });
            }

            // Conduct JS test
            if(testViaJS) {
                // Clear the highlights
                iframeHandler.clearHighlights();

                // Highlight the elements inside iframe
                iframeHandler.highlight(val, true);
            }
        },

        /**
         * Click callback for clear highlights button in CSS selector toolbar
         * @param {object} e Event
         */
        onClearHighlights: function(e) {
            iframeHandler.clearHighlights();
        },

        /**
         * Click callback for remove elements button in CSS selector toolbar
         * @param {object} e Event
         */
        onRemoveElements: function(e) {
            var selector = cssSelectorToolbar.getCssSelectorInput().val();
            if(selector == undefined || !selector.length) return;

            iframeHandler.getIframeContents().find(selector).remove();
        },

        /**
         * Click callback for show alternatives button in CSS selector toolbar
         * @param {object} e Event
         * @param {boolean} showSidebar True if the sidebar should be shown after the alternatives are computed. Default: true
         */
        onShowAlternatives: function(e, showSidebar) {
            var selector = cssSelectorToolbar.getCssSelectorInput().val();

            // Do not proceed if there is not a valid selector
            if(selector == undefined || !selector.length) return;

            var $sectionAlternativeSelector = $(dtv.sidebarSelector + " ." + dtv.sidebarSectionAlternativeSelectorsClass),
                currentSelector = $sectionAlternativeSelector.data("currentselector");

            // If current alternatives are not for this selector, compute the alternatives.
            if(currentSelector != selector) {
                // Get alternatives
                var alternatives = devTools.getAlternativeSelectors(selector);

                // Update sidebar's alternatives section
                sidebarHandler.updateAlternativeSelectors(alternatives);

                $sectionAlternativeSelector.data("currentselector", selector);
            }

            if(showSidebar == undefined || showSidebar) {
                // Show the sidebar
                sidebarHandler.onOpenSidebar(e);
            }
        },

        /**
         * Get input element storing the CSS selector
         * @returns {*|jQuery|HTMLElement}
         */
        getCssSelectorInput: function() {
            return $(dtv.cssInputSelector);
        },

        /**
         * Click handler for "use CSS selector" button
         */
        onClickUseCssSelector: function() {
            cssSelectorToolbar.useSelector();
        },

        /**
         * Use the selector written in the CSS selector input element
         */
        useSelector: function() {
            if(dtv.$currentDevToolsButton == undefined || dtv.$currentDevToolsButton == null) return;

            var val = this.getCssSelectorInput().val();
            if(val == undefined || !val.length) return;

            // Assign the value to the target input
            var $targetInput = dtv.$currentDevToolsButton.closest('.input-group').find('input.css-selector');
            $targetInput.val(val);

            // Close the lightbox
            devTools.closeLightbox();

            // Flash the target input
            devTools.flashBackground($targetInput);
        }
    };

});