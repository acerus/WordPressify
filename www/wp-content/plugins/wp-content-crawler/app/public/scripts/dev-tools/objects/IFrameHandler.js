function l(variable) { console.log(variable); }

jQuery(function($) {

    /*
     * IFRAME
     */

    window.IFrameHandler = function() {
        this.hoverSelectActive = true;

        /**
         * Stores the current URL loaded into the iframe.
         * @type {null|string}
         */
        this.currentUrl = null;
    };

    IFrameHandler.prototype = {
        constructor: IFrameHandler,

        /**
         * Update iframe status
         * @param status
         */
        updateStatus: function(status) {
            var $status = $(dtv.iframeStatusSelector).first();
            $status.html(status ? status : "");

            if(!status || !status.length) {
                $status.addClass("hidden");
            } else {
                $status.removeClass("hidden");
            }

            // Flash the background of the status element to indicate the change
            devTools.flashBackground($status);
        },

        /**
         * Highlight elements with a selector.
         * @param {string} selector CSS selector
         * @param {boolean} scroll Scroll to the top of the first highlighed element
         * @return {integer} Found element count
         */
        highlight: function(selector, scroll) {
            var $contents = this.getIframeContents();

            // First, remove current highlights.
            $contents.find("." + dtv.hoverClass).removeClass(dtv.hoverClass);

            var foundCount = 0;

            // Now, highlight the target elements.
            try {
                var $foundElements = $contents.find(selector);
                $foundElements.addClass(dtv.hoverClass);

                // Scroll to the first found element
                if(scroll != undefined && scroll) {

                    if($foundElements.length > 0) {
                        var originalHoverSelectStatus = optionsToolbar.isHoverSelectActive();

                        iframeHandler.activateHoverSelect(false);

                        $contents.find('body').stop().animate({
                            scrollTop: $foundElements.first().offset().top - $(window).height()/4
                        }, 500, 'swing', function() {
                            if(originalHoverSelectStatus) {
                                setTimeout(function () {
                                    iframeHandler.activateHoverSelect(true);
                                }, 100);
                            }
                        });
                    }
                }

                foundCount = $foundElements.length;

                // Assign the last highlighted element.
                dtv.$lastHighlighted = $foundElements.last();

            } catch(err) {
                foundCount = 0;
            }

            // Show found element count
            this.updateStatus(wpcc.found + ": " + foundCount);

            return foundCount;
        },

        /**
         * Clear all highlights
         */
        clearHighlights: function() {
            var $contents = this.getIframeContents();

            // First, remove current highlights.
            $contents.find("." + dtv.hoverClass).removeClass(dtv.hoverClass);

            // Clear the status
            this.updateStatus(null);

            // Invalidate the last highlighted element
            dtv.$lastHighlighted = null;
        },

        /**
         * Listen to the cursor movements in the iframe in the dev tools lightbox
         */
        listenToCursor: function() {
            var self = this;

            /**
             * Attaches events to the iframe elements and makes it possible interact with the iframe content
             */
            var iframeReadyCallback = function() {
                // Get the contents inside the iframe so that we can assign listeners to them
                var $contents = self.getIframeContents();

                // This will be called when a hovered element is clicked
                var clickCallback = function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Get the CSS selector for the element
                    var element = e.target,
                        $element = $(element),
                        ignoredTags = ["html", "body"],
                        ignoredAttrs = ["src", "alt", "target", "href", "title", "width", "height", "method", "dir"]
                        ;

                    $element.removeClass(dtv.hoverClass);

                    var optimalSelectOptions = {

                        // default reference
                        //root: document,

                        //skip: function(traverseNode) {
                        //    // ignore select information of the element
                        //    return traverseNode === element
                        //},

                        // define order of attribute processing
                        priority: ['id', 'class', 'tag'],

                        // define patterns which should't be included
                        ignore: {
                            class: function(className) {
                                return className.indexOf("!") !== -1;
                            },

                            attribute: function(name, value, defaultPredicate) {
                                // exclude HTML5 data attributes
                                return $.inArray(name, ignoredAttrs) !== -1 ||
                                    name.indexOf('/') !== -1 || // Ignore URLs
                                    value.indexOf('/') !== -1 ||
                                    name.indexOf('\\') !== -1 || // Ignore URLs
                                    value.indexOf('\\') !== -1 ||
                                    (/data-*/).test(name) ||
                                    (/aria-*/).test(name) ||
                                    defaultPredicate(name, value)
                            },

                            // define simplified ignore patterns as a boolean/string/number/regex
                            tag: function(name) {
                                return $.inArray(name, ignoredTags) !== -1;
                            }
                        }
                    };

                    var selector = OptimalSelect.select($element[0], optimalSelectOptions);

                    // OptimalSelect behaves in a weird way by starting the selector from "strong" element when there is
                    // a "strong" element in the found selector. Below is a workaround for this problem.
                    // If this element is in a "strong" element, get CSS selector for that "strong" element's parent and
                    // prepend it to the first found selector.
                    if(selector.startsWith("strong")) {
                        var $targetElement = $element.closest("strong").parent(),
                            strongParentSelector = OptimalSelect.select($targetElement[0], optimalSelectOptions);

                        selector = strongParentSelector + " " + selector;
                    }

                    //l("Raw Selector: " + selector);

                    selector = selector
                        .replace(dtv.multipleSpaceRegex, " ") // Replace multiple spaces with single space
                        .replace(/\\:/g, ':') // Replace \: with :
                        .replace("nth-child(1)", "first-child") // Prefer first-child
                        .replace(".wpcc-element-hovered", "") // Remove hover class
                    ;

                    // Replace classes like [class="cls1 cls2"] with .cls1.cls2
                    selector = devTools.unbracketClassNames(selector);

                    // Remove improper class names
                    selector = devTools.removeImproperClassNames(selector);

                    var split = selector.split(" ");

                    split = $.map(split, function(val, i) {
                        // Remove values having ".\" or "!"
                        return ((/^\.\\/g).test(val) || val.indexOf("!") !== -1) ? null : val;
                    });

                    selector = split.join(" ");

                    //l("Selector: " + selector);

                    // Update the CSS selector input
                    cssSelectorToolbar.updateInput(selector);

                    // Compute alternatives
                    cssSelectorToolbar.onShowAlternatives(e, false);

                    // Get the shortest alternative
                    var shortestAlternative = devTools.getBestAlternativeSelector();

                    // Update the CSS selector input
                    if(shortestAlternative !== null) {
                        cssSelectorToolbar.updateInput(shortestAlternative);
                    }

                    // Trigger keyup on CSS selector input to update highlights
                    $(dtv.cssInputSelector).keyup();

                    // If the user wants to use the first found selector, do so.
                    if(optionsToolbar.isUseImmediately()) {
                        $(dtv.cssUseButtonSelector).click();
                    }

                    return selector;
                };

                // Add the hover style to the iframe if it is not already added.
                if(!$contents.find(dtv.hoverStyleSelector).length) {
                    $contents.find("head").append($(dtv.hoverStyleSelector)[0].outerHTML);
                }

                // Keep the last hovered element so that we can remove the styling we applied to it.
                dtv.$lastHighlighted = null;
                var $allElements = $contents.find("*");

                $allElements
                    .off('click')
                    .off('hover')
                    // Handle click events
                    .on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // If there is a highlighted element, retarget the click to that element.
                        if(dtv.$lastHighlighted != null && e.target != dtv.$lastHighlighted[0]) {
                            e.target = dtv.$lastHighlighted[0];
                            clickCallback(e);
                            return;
                        }

                        // Otherwise, go to the closest anchor's URL if the hover select is deactivated.
                        if(!optionsToolbar.isHoverSelectActive()) {
                            var $target = $(e.target);
                            addressBar.go($target.closest('a').attr("href"));
                        }
                    })

                    // Listen to the hover events for all elements
                    .hover(function(e) {
                        var $element = $(e.target);

                        // It should be possible to target an HTML tag. For example, if the target tag is 'a',
                        // when an element is hovered, the closest 'a' tag should be hovered instead. So, $element
                        // should be the best possible element having the target HTML tag.
                        if(optionsToolbar.targetHTMLTagSelector != null && $element.prop("tagName").toLowerCase() != optionsToolbar.targetHTMLTagSelector) {
                            var $closestInner = $element.find(optionsToolbar.targetHTMLTagSelector).first(),
                                $closestOuter = $element.closest(optionsToolbar.targetHTMLTagSelector),
                                elParentCount = $element.parents().length;

                            // If there are two target elements found, keep the closest one.
                            if($closestInner.length && $closestOuter.length) {
                                if($closestInner.parents().length - elParentCount < elParentCount - $closestOuter.parents().length) {
                                    $element = $closestInner;
                                } else {
                                    $element = $closestOuter;
                                }

                                // If there is only the closest inner element, use it.
                            } else if($closestInner.length) {
                                $element = $closestInner;

                                // If there is only the closest outer element, use it.
                            } else if($closestOuter.length) {
                                $element = $closestOuter;

                                // Otherwise, do not proceed, since there are no target elements found.
                            } else {
                                //// Prevent clicking the links.
                                //$element.click(function($e) {
                                //    $e.preventDefault();
                                //});
                                return;
                            }
                        }

                        // Do not proceed if hover select is not active
                        if(!iframeHandler.hoverSelectActive) {
                            //// Prevent clicking the links.
                            //$element.click(function($e) {
                            //    $e.preventDefault();
                            //});

                            if(dtv.$lastHighlighted != null) {
                                dtv.$lastHighlighted.off('click');
                                dtv.$lastHighlighted = null;
                            }

                            return;
                        }

                        // If there was a hovered element, clear its click callback and remove hover class from it.
                        if(dtv.$lastHighlighted) {
                            dtv.$lastHighlighted
                                .removeClass(dtv.hoverClass)
                                .off('click')
                            ;
                        }

                        // Add the hover class to the currently hovered element and assign the click callback.
                        $element
                            .addClass(dtv.hoverClass)
                            .click(clickCallback)
                        ;

                        // We are done with the currently hovered element. Set it as last hovered element.
                        dtv.$lastHighlighted = $element;
                    })
                ;
            };

            // Start listening to the events when the iframe is ready/loaded
            // The proper way is to listen to 'load' event. However, it is also important that the user can start
            // interacting with the iframe content as soon as iframe is ready. Hence, we listen both 'ready' and 'load'
            // events.
            self.getIframe()
                .ready(iframeReadyCallback)
                .load(iframeReadyCallback);
        },

        /**
         * Listen to the keyboard events from inside the iframe
         */
        listenToKeyboard: function() {
            var self = this;

            var readyCallback = function() {
                // Get the iframe's document
                var iframe = self.getIframe()[0],
                    doc = iframe.contentWindow.document;

                // Listen to the keydown events inside the iframe document
                $(doc).on('keydown', function(e) {
                    sidebarHandler.handleKeyPress(e);
                });
            };

            // Call the callback when the iframe is ready and loaded.
            self.getIframe()
                .ready(readyCallback)
                .load(readyCallback);
        },

        /**
         * If there is a CSS selector already defined by the user, tries to find and highlight the element in the iframe.
         * If there is no CSS selector defined by the user and there are target CSS selectors for the current DEV tools
         * button, tries to find elements by using those selectors and highlight them.
         */
        initCssSelectors: function() {
            // Do everything in a callback so that we can attach the callback to multiple events.
            var callback = function() {
                var currentCssSelectorVal = cssSelectorToolbar.getCssSelectorInput().val(),
                    foundCount = 0;

                // If current CSS selector exists, try to highlight it.
                if(currentCssSelectorVal != undefined && currentCssSelectorVal.length) {
                    foundCount = iframeHandler.highlight(currentCssSelectorVal, true);

                // Otherwise, use target CSS selectors if they exist
                } else {
                    // Get the button data
                    var buttonData = dtv.$currentDevToolsButton.data("wcc");
                    if(buttonData == undefined) return;

                    // Get target selectors
                    var targetSelectors = buttonData["targetCssSelectors"];

                    // Do not proceed if target CSS selectors do not exist.
                    if(targetSelectors == undefined || !targetSelectors.length) return;

                    // Now, try to find the elements with the selectors one by one.
                    for(var i in targetSelectors) {
                        if(!targetSelectors.hasOwnProperty(i)) continue;

                        var selector = targetSelectors[i];

                        // We've found at least one match.
                        foundCount = iframeHandler.highlight(selector, true);
                        if(foundCount > 0) {
                            // Set the CSS input and highlight the found elements
                            cssSelectorToolbar.updateInput(selector);

                            // Notify the user
                            devTools.notify(
                                cssSelectorToolbar.getCssSelectorInput(),
                                wpcc.css_selector_found,
                                'success',
                                'bottom'
                            );

                            // One match is enough. Do not proceed.
                            return;
                        }
                    }
                }
            };

            // Call the callback when the iframe is ready and loaded.
            this.getIframe()
                .ready(callback)
                .load(callback);
        },

        /**
         * Get the iframe jQuery element
         * @returns {*|jQuery|HTMLElement}
         */
        getIframe: function() {
            return $(dtv.iframeSelector);
        },

        /**
         * Get contents of the iframe.
         * @returns {*|{xml, html, json}|{script}}
         */
        getIframeContents: function() {
            return this.getIframe().contents();
        },

        /**
         * Get current URL loaded into the iframe.
         * @returns {null|string|string|*}
         */
        getCurrentUrl: function() {
            return this.currentUrl;
        },

        /**
         * Load a URL into the iframe
         * @param {string} url
         */
        loadUrl: function(url) {
            // Make sure the URL is valid
            if(url == undefined || !url || !url.length || url.indexOf("http") !== 0) return;

            l("Load URL: " + url);
            this.currentUrl = url;

            addressBar.setAddressBarUrl(url);
            addressBar.enableButton($(dtv.refreshButtonSelector));
            addressBar.historyUpdated();

            if(addressBar.currentHistoryIndex == 0) {
                addressBar.disableButton($(dtv.backButtonSelector));

            } else if(addressBar.history.length > 1) {
                addressBar.enableButton($(dtv.backButtonSelector));
            }

            // Indicate the URL is being loaded
            var $urlInput = $(dtv.urlInputSelector);
            $urlInput.addClass("loading");

            // Load the URL
            var data = $(dtv.devToolsButtonSelector).data("wcc");
            devTools.getSourceCode(url, data,
                // Done
                function(response){
                    // Do not proceed if the response is not valid.
                    if(response == undefined || response == null || response.html == undefined) {
                        l("Request succeeded. Getting source code was not successful. Response:");
                        l(response);
                        return;
                    }

                    // Refresh the content
                    iframeHandler.setIframeContent(response.html, url);
                },

                // Fail
                function(response) {
                    if(response.statusText == 'abort') return;
                    l("Request failed. Getting source code was not successful. Response:");
                    l(response);
                },

                // Always
                function() {
                    // Indicate the process has finished.
                    $urlInput.removeClass("loading");
                }
            );
        },

        /**
         * Set content of the iframe in the lightbox. The content will be interacted with.
         * @param {string} content HTML
         * @param {string} url URL of the content
         */
        setIframeContent: function(content, url) {
            var $iframe = $(dtv.iframeSelector).first(),
                iframe = $iframe[0],
                iframedoc = iframe.contentDocument || iframe.contentWindow.document,
                currentUrl = $iframe.data("currenturl")
                ;

            // Do not proceed if the same URL is tried to be loaded, since its source code are already inside the iframe.
            if(currentUrl != undefined && currentUrl == url) return;

            //iframedoc.body.innerHTML = content;
            iframedoc.open();
            iframedoc.write(content);
            iframedoc.close();

            this.setIframeHeight();

            $iframe.data("currenturl", url);

            // Listen to the cursor movements
            iframeHandler.listenToCursor();

            // Listen to the keyboard
            iframeHandler.listenToKeyboard();

            // Initialize selectors. You can read method description to better understand what it does.
            iframeHandler.initCssSelectors();
        },

        /**
         * Sets the height of the iframe so that it fills the remaining space inside of the lightbox
         */
        setIframeHeight: function() {
            var $toolbar = $(dtv.toolbarSelector).first(),
                toolbarHeight = $toolbar.innerHeight(),
                $iframe = $(dtv.iframeSelector).first();

            $iframe.css("height", "calc(100% - " + toolbarHeight + "px)");
        },

        /**
         * Activate/deactivate hover select
         * @param {boolean} active
         */
        activateHoverSelect: function(active) {
            this.hoverSelectActive = active;
        }

    };

});