function l(variable) { console.log(variable); }

jQuery(function($) {

    /*
     * OPTIONS TOOLBAR
     */

    window.OptionsToolbar = function() {
        this.targetHTMLTagSelector = null;
    };

    OptionsToolbar.prototype = {
        constructor: OptionsToolbar,

        /**
         * Change handler for target HTML tag input
         * @param {object} e Event
         */
        onChangeTargetHTMLTagInput: function(e) {
            var val = $(dtv.optTargetHTMLTagSelector).val();

            // If there is no value, set the target HTML selector as null.
            if(val == undefined || !val.length) {
                optionsToolbar.targetHTMLTagSelector = null;
                return;
            }

            // Otherwise, get the part until space and trim it to clear any whitespace.
            optionsToolbar.targetHTMLTagSelector = $.trim(val.split(" ")[0]);
        },

        /**
         * Callback handling the click events on toggle hover select button
         * @param {object} e Event
         */
        onClickToggleHoverSelect: function(e) {
            var $optionButton = $(dtv.optHoverSelectSelector).first();
            $optionButton.toggleClass("active");

            // Activate/deactivate hover select in the iframe
            iframeHandler.activateHoverSelect($optionButton.hasClass("active"));

            // Save options
            devTools.saveState();
        },

        /**
         * Check if hover select is active
         * @returns {boolean|*}
         */
        isHoverSelectActive: function() {
            var $optionButton = $(dtv.optHoverSelectSelector).first();
            return $optionButton.hasClass("active");
        },

        /**
         * Check if "use immediately" is active
         * @returns {*}
         */
        isUseImmediately: function() {
            return $(dtv.optUseImmediatelySelector)[0].checked;
        },

        /**
         * Check if scripts should be removed
         * @returns {*}
         */
        isRemoveScripts: function() {
            return $(dtv.optRemoveScriptsSelector)[0].checked;
        },

        /**
         * Check if styles should be removed
         * @returns {*}
         */
        isRemoveStyles: function() {
            return $(dtv.optRemoveStylesSelector)[0].checked;
        },
    };

});