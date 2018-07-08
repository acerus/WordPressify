function l(variable) { console.log(variable); }

jQuery(function($) {

    /*
     * SIDEBAR HANDLER
     */

    window.SidebarHandler = function() {
        this.preventHoverEvent = false;
    };

    SidebarHandler.prototype = {
        constructor: SidebarHandler,

        /**
         * Loads the sidebar
         */
        loadSidebar: function() {
            this.onUpdateAllUsedSelectors(null);
        },

        /**
         * Update alternative CSS selectors section of the sidebar
         * @param {Array} alternatives An array of CSS selectors
         * @return {Array} An array of objects with each storing an alternative selector and number of elements found
         * via that selector.
         */
        updateAlternativeSelectors: function(alternatives) {
            var preparedHtml = '',
                $contents = iframeHandler.getIframeContents(),
                alternativeObjects = [],
                o, count;
            for(var i in alternatives) {
                if(!alternatives.hasOwnProperty(i)) continue;

                count = $contents.find(alternatives[i]).length;
                if(count < 1) continue;

                o = {
                    selector: alternatives[i],
                    count: count
                };

                alternativeObjects.push(o);

                preparedHtml += sidebarHandler.getCssSelectorListItemHtml(o);
            }

            // Update the sidebar section
            sidebarHandler.updateSectionContent("<ul data-alternatives='" + JSON.stringify(alternativeObjects) + "'>" + preparedHtml + "</ul>", dtv.sidebarSectionAlternativeSelectorsClass);

            return alternativeObjects;
        },

        /**
         * Update all used selectors section of the sidebar
         * @param {object} e Event
         */
        onUpdateAllUsedSelectors: function(e) {
            var allSelectors = [], $self, name, val, valCounter = [];

            // Find all used CSS selectors
            $(dtv.settingsMetaBoxSelector).find("input.css-selector").each(function() {
                $self = $(this);
                name = $self.attr("name");

                if(name.indexOf("selector") !== -1) {
                    val = $self.val();

                    if(val != undefined && val.length) {
                        if(valCounter.hasOwnProperty(val)) {
                            valCounter[val] += 1;
                        } else {
                            allSelectors.push(val);
                            valCounter[val] = 1;
                        }
                    }
                }
            });

            var preparedSelectors = [], current, preparedHtml = '';
            for(var i in allSelectors) {
                if(allSelectors.hasOwnProperty(i)) {
                    val = allSelectors[i];
                    current = {
                        count: valCounter[val],
                        selector: val
                    };
                    preparedSelectors.push(current);

                    preparedHtml += sidebarHandler.getCssSelectorListItemHtml(current);
                }
            }

            // Update the sidebar section
            sidebarHandler.updateSectionContent("<ul>" + preparedHtml + "</ul>", dtv.sidebarSectionUsedSelectorsClass);
        },

        /**
         * Update the content of a section.
         * @param {string} html New HTML for the section content
         * @param {string} sectionClass Target class name
         */
        updateSectionContent: function(html, sectionClass) {
            var $section = this.getSectionElement(sectionClass),
                $content = $section.find("." + dtv.sidebarSectionContentClass).first()
                ;

            // Flash the content element with color
            devTools.flashBackground($section);

            $content.html(html);
        },

        /**
         * Get section element via a section class.
         * @param {string} sectionClass
         * @returns {*|jQuery|HTMLElement}
         */
        getSectionElement: function(sectionClass) {
            return $(dtv.sidebarSelector + " ." + sectionClass);
        },

        /**
         * Get list item HTML code for a CSS selector
         * @param {object} object The object that can be set as "data-selector" attribute's value. This has to have
         *                        "selector" property. It can also contain "count" property that will be shown next to
         *                        the selector.
         * @return {string}
         */
        getCssSelectorListItemHtml: function(object) {
            var count = object.count ? "<i class='count'>(" + object.count + ")</i>" : '';
            return "<li><span class='selector' data-selector='" + JSON.stringify(object) + "'>" + object.selector + count + "</span></li>";
        },

        /**
         * Callback that can handle click events of CSS selectors. The element should have 'data-selector' attr.
         * @param {object} e Event
         */
        onClickCssSelector: function(e) {
            var $self = $(this),
                selectorData = sidebarHandler.getCssSelectorData($self)
                ;

            if(!selectorData) return;

            // Copy the selector to the CSS selector input
            cssSelectorToolbar.updateInput(selectorData.selector);

            // Test the selector
            $(dtv.cssTestSelector).click();

            // Close the sidebar
            $(dtv.sidebarCloseSelector).click();

            // When mouse is moved after the sidebar is closed, hover event still fires on the clicked element.
            // This causes unwanted scroll animations. Below boolean logic fixes this.
            sidebarHandler.preventHoverEvent = true;
        },

        /**
         * Callback that can handle click events of URLs.
         * @param {object} e Event
         */
        onClickHistoryUrl: function(e) {
            var $element = $(e.target),
                url = $element.text();

            // Do not proceed if the URL is not valid.
            if(url == undefined || !url.length) return;

            // Find the index of the URL in the list
            var index = $element.closest("ul").find("li").index($element.closest("li"));

            // If the index is found
            if(index != null && index > -1) {
                // Load the URL and arrange forward and back button statuses.
                addressBar.travelInTime(index);
            }
        },

        /**
         * Callback that can handle hover events of CSS selectors. The element should have 'data-selector' attr.
         * @param {object} e Event
         */
        onHoverCssSelector: function(e) {
            if(sidebarHandler.preventHoverEvent) {
                sidebarHandler.preventHoverEvent = false;
                return;
            }
            var $self = $(this),
                selectorData = sidebarHandler.getCssSelectorData($self)
                ;

            if(!selectorData) return;

            // Highlight the hovered CSS selector in iframe
            iframeHandler.clearHighlights();
            iframeHandler.highlight(selectorData.selector, true);
        },

        /**
         * Get CSS selector data of a CSS selector element. The element should have "data-selector" attr and this attr
         * should have 'selector' property.
         * @param $element
         * @returns {null|object} If found, an object definitely having 'selector' property.
         */
        getCssSelectorData: function($element) {
            var selectorData = $element.data("selector");

            return selectorData == undefined || !selectorData.hasOwnProperty('selector') ? null : selectorData;
        },

        /**
         * Callback that can handle close button clicks
         * @param e
         */
        onCloseSidebar: function(e) {
            var $sidebar = sidebarHandler.getSidebar();
            $sidebar.removeClass(dtv.sidebarOpenedClass);

            // Show the open button
            $(dtv.sidebarOpenSelector).removeClass("hidden");
        },

        /**
         * Callback that can handle open button clicks
         * @param e
         */
        onOpenSidebar: function(e) {
            var $sidebar = sidebarHandler.getSidebar();
            $sidebar.addClass(dtv.sidebarOpenedClass);

            // Hide the open button
            $(dtv.sidebarOpenSelector).addClass("hidden");
        },

        /**
         * Callback that can handle expand/collapse sidebar section clicks
         * @param e
         */
        onToggleExpand: function(e) {
            var $button = $(this),
                expanded = $button.hasClass(dtv.sidebarSectionExpandedClass),
                $section = $button.closest(dtv.sidebarSectionSelector),
                $sectionContent = $button.closest(dtv.sidebarSectionContentSelector)
                ;

            $section.toggleClass(dtv.sidebarSectionExpandedClass);
            $button
                .toggleClass(dtv.sidebarSectionExpandedClass)
                .toggleClass("dashicons-arrow-down")
                .toggleClass("dashicons-arrow-up")
            ;
        },

        /**
         * Get the sidebar element
         * @returns {*|jQuery|HTMLElement}
         */
        getSidebar: function() {
            return $(dtv.sidebarSelector);
        },

        /**
         * Handles key presses related to the sidebar
         * @param {object} e Event
         */
        handleKeyPress: function(e) {
            // Left key opens the sidebar
            if(e.which == 37) sidebarHandler.onOpenSidebar(e);

            // Right key closes the sidebar
            if(e.which == 39) sidebarHandler.onCloseSidebar(e);
        }
    };

});