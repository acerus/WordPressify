function l(variable) { console.log(variable); }

jQuery(function ($) {

    /*
     * ADDRESS BAR
     */

    window.AddressBar = function() {

        /**
         * Stores URL history
         * @type {Array}
         */
        this.history = [];

        /**
         * Stores the index of the URL in the history that is loaded via forward or back buttons.
         * @type {null|integer}
         */
        this.currentHistoryIndex = null;
    };

    AddressBar.prototype = {
        constructor: AddressBar,

        /**
         * Load the previous URL in the history. Click handler for back button.
         * @param {object} e Event
         */
        onClickBack: function(e) {
            addressBar.handleBackAndForward(true);
        },

        /**
         * Load the next URL in the history. Click handler for forward button.
         * @param {object} e Event
         */
        onClickForward: function(e) {
            addressBar.handleBackAndForward(false);
        },

        /**
         *
         * @param {boolean} isBack True if this is for back button. Otherwise, this is for forward button.
         */
        handleBackAndForward: function(isBack) {
            var i = isBack ? -1 : 1;

            // No need to proceed if the history is empty.
            if(!this.history.length) return;

            var url = null;

            // If there is a state, use it to find the previous URL in the history.
            if(this.currentHistoryIndex !== null) {
                if(this.history[this.currentHistoryIndex + i] !== undefined) {
                    url = this.history[this.currentHistoryIndex + i];
                    this.currentHistoryIndex += i;

                    // If prev index is not available, it means there is no prev URL.
                } else {
                    // Disable the back button.
                    this.disableButton($(isBack ? dtv.backButtonSelector : dtv.forwardButtonSelector));
                    return;
                }

                // Otherwise, if the history is not empty, get the prev URL.
            } else if(this.history.length > 1 && isBack) {
                url = this.history[this.history.length + 2 * i];
                this.currentHistoryIndex = this.history.length + 2 * i;
            }

            if(url) {
                // Enable forward button
                this.enableButton($(isBack ? dtv.forwardButtonSelector : dtv.backButtonSelector));

                // Load the URL
                iframeHandler.loadUrl(url);

                // If there is no previous URLs after this one, disable the back button.
                if(this.history[this.currentHistoryIndex + i] == undefined) {
                    this.disableButton($(isBack ? dtv.backButtonSelector : dtv.forwardButtonSelector));
                }
            }
        },

        /**
         * Refresh the current URL. Click handler for refresh button.
         * @param {object} e Event
         */
        onClickRefresh: function(e) {
            addressBar.refresh();
        },

        /**
         * Reloads the current URL.
         */
        refresh: function() {
            if(!this.history.length) return;
            var url = iframeHandler.getCurrentUrl();

            $(dtv.urlInputSelector).val(url);

            // If there is a URL cache, invalidate it to get fresh data.
            devTools.invalidateUrlCache(url);

            // Load the URL
            iframeHandler.loadUrl(url);
        },

        /**
         * Loads the URL into the iframe. Click handler for go button.
         * @param {object} e Event
         */
        onClickGo: function(e) {
            // Get the URL from the address bar input
            var $input = $(dtv.urlInputSelector),
                url = $input.val();

            // If there is no URL, do not proceed.
            if(url == undefined || !url.length) return;

            // Delete the history's later part
            if(this.currentHistoryIndex !== null && this.currentHistoryIndex > 0) {
                this.history = this.history.splice(0, this.currentHistoryIndex - 1);
            }

            addressBar.go(url);
        },

        go: function(url) {
            if(url == undefined || !url.length) return;

            l("Go: " + url);

            // If this is a relative URL, prepend the base URL to it.
            if(url.indexOf('/') == 0) {
                var $base = iframeHandler.getIframeContents().find("base");

                // Do not proceed if base URL does not exist
                if(!$base.length || $base.attr("href") == undefined || !$base.attr("href").length) return;

                // Prepend the base URL
                url = $base.attr("href") + url
            }

            // Do not proceed if the URL does not start with http
            if(url.indexOf("http") !== 0) return;

            // If this URL is not the last URL in the history, add the URL to the history.
            if(!this.history.length || this.history[this.history.length - 1] != url) {
                this.history.push(url);
            }

            // Load the URL
            iframeHandler.loadUrl(url);

            this.historyUpdated();
        },

        /**
         * Go to a specific URL in the history via its index
         * @param {Integer} targetHistoryIndex
         */
        travelInTime: function(targetHistoryIndex) {
            // Do not proceed if the history does not have the target index.
            if(targetHistoryIndex == undefined || targetHistoryIndex < 0 || this.history[targetHistoryIndex] == undefined) return;

            // Make sure the history index is an integer
            targetHistoryIndex = parseInt(targetHistoryIndex);

            // Load the URL
            iframeHandler.loadUrl(this.history[targetHistoryIndex]);

            // Enable/disable back button
            if(this.history[targetHistoryIndex - 1] !== undefined) {
                this.enableButton($(dtv.backButtonSelector));
            } else {
                this.disableButton($(dtv.backButtonSelector));
            }

            // Enable/disable forward button
            if(this.history[targetHistoryIndex + 1] !== undefined) {
                this.enableButton($(dtv.forwardButtonSelector));
            } else {
                this.disableButton($(dtv.forwardButtonSelector));
            }

            // Update the current history index
            this.currentHistoryIndex = targetHistoryIndex;

            // Make necessary changes since the history is updated
            this.historyUpdated();
        },

        /**
         * Click handler for clear history button
         * @param {object} e Event
         */
        onClickClearHistory: function(e) {
            addressBar.clearHistory();
        },

        /**
         * Clear the URL history
         */
        clearHistory: function() {
            this.history = [];
            this.currentHistoryIndex = null;
            this.disableButton($(dtv.backButtonSelector));
            this.disableButton($(dtv.forwardButtonSelector));
            this.disableButton($(dtv.refreshButtonSelector));
            this.historyUpdated();
        },

        /**
         * Called whenever the history is updated.
         */
        historyUpdated: function() {
            // Update sidebar's history section
            var html = '',
                activeIndex = this.currentHistoryIndex == null ? this.history.length - 1 : this.currentHistoryIndex;

            for(var i in this.history) {
                if(!this.history.hasOwnProperty(i)) continue;

                html += '<li' + (i == activeIndex ? ' class="active" ' : '') + '><span class="url">' + this.history[i] + '</span></li>';
            }

            sidebarHandler.updateSectionContent('<ul>' + html + '</ul>', dtv.sidebarSectionHistoryClass);

            // Save options
            devTools.saveState();
        },

        /**
         * Set URL input's value
         * @param {string} url
         */
        setAddressBarUrl: function(url) {
            $(dtv.urlInputSelector).val(url);
        },

        /**
         * Disable a button
         * @param {object} $button
         */
        disableButton: function($button) {
            $button.addClass("disabled");
        },

        /**
         * Enable a button
         * @param {object} $button
         */
        enableButton: function($button) {
            $button.removeClass("disabled");
        },
    };

});