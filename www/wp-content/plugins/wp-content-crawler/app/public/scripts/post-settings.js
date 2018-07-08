function l(x) { console.log(x); }

var WPCC_POST_SETTINGS = {};

jQuery(function($) {
    var $containerMetaBox  = $('.wcc-settings-meta-box'),
        $containerTabs      = $('.nav-tab-wrapper'),
        $form               = $("#post"),
        $errorAlert         = $("#wcc-alert"),
        $wccNonce           = $("#wcc_nonce");

    var selectorCategoryMap                 = "#category-map",
        selectorTabMain                     = "#tab-main",
        selectorTabCategory                 = "#tab-category",
        selectorTabGsPost                   = "#tab-gs-post",
        selectorTabGeneralSettings          = "#tab-general-settings",
        selectorTestButton                  = '.wcc-test',
        selectorInputContainerPasswords     = '.input-container-passwords',
        selectorLoadGeneralSettingsButton   = '#btn-load-general-settings',
        selectorClearGeneralSettingsButton  = '#btn-clear-general-settings',
        selectorInputImport                 = '#_post_import_settings',
        selectorLoadTranslationLanguages    = '.load-languages',
        selectorInputActiveTab              = "input[name='active_tab']",

        clsHasError = 'has-error';

    var $inputAction = $("#hiddenaction");

    /**
     * Initializations
     */
    initTooltip();

    // If there is a hash for a tab, activate that tab.
    handleURLHash();

    // Listen to hash changes and react to them.
    $(window).on('hashchange', handleURLHash);

    /**
     * Initializes tooltip instances
     */
    function initTooltip() {
        if(typeof $.fn.tooltip == 'function')
            $('[data-toggle="tooltip"]').tooltip();
    }

    /**
     * Add an input group to its container.
     */
    $containerMetaBox.on('click', '.wcc-add-new', function($event) {
        $event.preventDefault();

        var $self = $(this);

        // Find the input container
        var $inputGroupContainer = $self.closest("td").find(".inputs");

        // Get max limit
        var max = $self.data("max");
        if(max != 0 && $inputGroupContainer.length >= max) return;

        addNewInputGroup($inputGroupContainer);
    });

    /**
     * Remove an input group.
     */
    $containerMetaBox.on('click', '.wcc-remove', function($event) {
        $event.preventDefault();
        var $self = $(this);

        // Get count of input groups
        var count = $self.closest(".inputs").find(".input-group").length;

        var $closestInputGroup = $self.closest(".input-group");

        // If there is only 1 input group, then do not remove it. Just clear the values.
        if(count == 1) {
            $closestInputGroup.find("input").each(function() {
                $(this).val("");
            });

            $closestInputGroup.find("textearea").each(function () {
                $(this).html("");
            });

            $closestInputGroup.find("input[type=checkbox]").each(function() {
                $(this).prop('checked', false);
            });

        // Otherwise, remove the input group.
        } else {
            $closestInputGroup.remove();
        }
    });

    /**
     * React to tests
     */
    $containerMetaBox.on('click', selectorTestButton, function($event) {
        $event.preventDefault();

        var $self = $(this);
        var data = prepareTestData($self);
        if(data == null) return;

        // Set the closest test results container loading and clear its content
        var $resultContainer = $self.closest("td").find(".test-results");
        var $contentContainer = $resultContainer.find(".content");
        $resultContainer
            .removeClass("hidden")
            .addClass("loading");
        $contentContainer.html("");

        $.post(ajaxurl, {
            wcc_nonce: $wccNonce.val(),
            action: pageActionKey,
            data: data
        })
            .done(function(response) {
                if(response == undefined || !response || response.view == undefined) {
                    $contentContainer.html(wpcc.an_error_occurred);
                    return;
                }

                // Show the results
                $contentContainer.html(response.view);

                // Check if this is category-mapping stuff
                if($self.hasClass('wcc-category-map')) {
                    // Get the container
                    var $inputGroupContainer = $(selectorCategoryMap).find('.inputs');

                    // Add resultant URLs as new input
                    for(var i = 0; i < response.data.length; i++) {
                        var url = response.data[i];
                        if(url.match("^javascript")) continue;

                        var $newInputGroup = addNewInputGroup($inputGroupContainer);
                        $newInputGroup.find('input').val(url);
                    }
                }

            })
            .fail(function(response) {
                $contentContainer.html(wpcc.an_error_occurred + " <br />" + response.responseText);
                console.log(response);
            })
            .always(function() {
                // Remove loading class
                $resultContainer.removeClass("loading");
            });
    });

    /**
     * Prepares the data that will be sent with the AJAX request when conducting tests
     *
     * @param {Object} $testButton The test button that is clicked
     * @returns {null}
     */
    function prepareTestData($testButton) {
        // Get the required data from the test button.
        var mData = $testButton.data("wcc");

        // Do not proceed if the data does not exist.
        if(mData == undefined || !mData) return null;

        // Clone the object.
        var data = JSON.parse(JSON.stringify(mData));

        data = addSettingsToAjaxData(data);

        // Get the inputs
        var $inputs = $testButton.closest(".input-group").find('.input-container').find(':input');
        if(!$inputs.length) return null;

        /*
         * REQUIRED ELEMENTS
         */

        // Get the required element selectors, if there are any
        var allSelectorsRequired = true,
            requiredElExpr = mData["requiredSelectors"];

        if(requiredElExpr != undefined) {
            // If required element selectors are supplied, that means not all of the "Selector"s in the data are required.
            allSelectorsRequired = false;

            // If there are required selectors, get their values and notify the user
            if(requiredElExpr.length) {
                // First, we need to prepare the expression string. Here is an example expression string
                // (.sel1 | ( .sel2 & .sel7 ) ) & ( .sel2 | .sel3) & .sel5 &#sel4

                // Append and prepend a space as well.
                requiredElExpr = " " + requiredElExpr
                    .replace(/([()&|])/g, " $1 ") // First surround every special char, such as ( ) &, with space
                    .replace(/\s{2,}/g, " ") // And get rid of multiple spaces.
                    .replace(/\&/g, '&&') // Replace single & with &&
                    .replace(/\|/g, '||') // Replace single | with ||
                    .trim() + " "
                ;

                // Now, get the selectors
                var selectorMatches = requiredElExpr.match(/([#\[\]=\^~.a-z0-9_\-"']+)\s?/g);

                var evalStr = requiredElExpr,
                    currentSelector, $el, valueExists, requiredEls = [];

                for(var i in selectorMatches) {
                    if(!selectorMatches.hasOwnProperty(i)) continue;

                    currentSelector = selectorMatches[i].trim();
                    if(!currentSelector.length) continue;

                    //l("Current selector");
                    //l(currentSelector);

                    $el = $(currentSelector).first();

                    //l("Escaped selector:");
                    //l(escapeRegExp(currentSelector));

                    valueExists = $el.length && $el.val() != undefined && $el.val().length;
                    if(!valueExists && $el.length) requiredEls.push($el);

                    evalStr = evalStr.replace(
                        new RegExp(escapeRegExp(currentSelector) + "\\s", "g"),
                        valueExists ? 'true ' : 'false '
                    );
                }

                // If the evaluation is false and there are required elements, notify the user for a required element.
                if(!eval(evalStr) && requiredEls.length) {
                    var max = requiredEls.length - 1,
                        min = 0;

                    notify(requiredEls[Math.floor(Math.random() * (max - min + 1)) + min]);
                    return null;
                }
            }
        }

        /*
         *
         */

        // If there are selectors in the data, get the values from those elements whose selectors are defined in the data
        for(var key in data) {
            // Make sure the key ends with "Selector".
            if(!data.hasOwnProperty(key) || !/Selector$/.test(key)) continue;

            // Find the target element
            var $targetEl = $(data[key]);

            // If all selectors are required and this element's value is empty, notify the user and return null.
            if(allSelectorsRequired && ($targetEl.val() == undefined || !$targetEl.val().length)) {
                notify($targetEl);
                return null;
            }

            // Remove the selector value from the data, since we do not need it.
            delete data[key];

            if(!$targetEl.length) continue;

            // Add the key with its value to the data to be sent by removing "Selector" from the key.
            data[key.replace("Selector", "")] = $targetEl.val() || null;
        }

        // Get the values that should be tested from the inputs next to current test button
        data["serializedValues"] = $inputs.serialize();

        // Add name of the form item that is being tested. Get the chars until the first opening bracket.
        data["formItemName"] = /^([^\[]+)/.exec($inputs.first().attr("name"))[1] || null;

        // Add the required data if this is a "find-replace in custom meta" test
        data = addDataForFindReplaceInCustomMetaOrShortCodeTest($testButton, data);

        //l("Prepared:");
        //l(data);

        return data;
    }

    /**
     * Add data to the original test data for find-replace in custom meta and custom short code test
     *
     * @param {object} $testButton The test button that is clicked to perform the test
     * @param {array} data Original data to which the new data will be added
     * @return {array} Data with the data for find replace in custom meta test
     */
    function addDataForFindReplaceInCustomMetaOrShortCodeTest($testButton, data) {
        var clsCustomMeta = "wcc-test-find-replace-in-custom-meta",
            clsCustomShortCode = "wcc-test-find-replace-in-custom-short-code";

        // If the test button is not the test button we are looking for, do not proceed and just return the original data.
        if(!$testButton.hasClass(clsCustomMeta) && !$testButton.hasClass(clsCustomShortCode)) return data;

        var isCustomMeta = $testButton.hasClass(clsCustomMeta),
            targetInputSelector = '.' + (isCustomMeta ? 'meta-key' : 'short-code'),
            targetInputGroupSelector = '.' + (isCustomMeta ? 'selector-custom-post-meta' : 'selector-custom-shortcode');

        // Get the meta key for which the user wants to perform find and replace operation
        var $keyInput = $testButton.closest(".input-group").find('.input-container').find(targetInputSelector);

        // If meta key input does not exist, no need to go on. Return the original data.
        if(!$keyInput.length) return data;

        // Get the meta key
        var key = $keyInput.val();
        if(key == undefined || !key.length) return data;

        var found = false;

        // There are two possible places the user can define custom meta keys. One of them can be defined by CSS selectors
        // and the other one by manually. We'll handle both of the cases below. We just need one value. So, if a value
        // is found, that's it. We're done.

        // Find meta key inputs defined in selector custom post meta options
        $('.input-group' + targetInputGroupSelector + ' ' + targetInputSelector).each(function() {
            if(found) return;

            var $self = $(this);
            if($self.val() == key) {
                // Get the selector and its attribute
                var $cssSelectorInput = $self.closest('.input-group').find('.css-selector'),
                    $cssSelectorAttrInput = $self.closest('.input-group').find('.css-selector-attr'),
                    cssSelector = $cssSelectorInput.val(),
                    attr = $cssSelectorAttrInput.val();

                // If there is a CSS selector, we've reached our goal.
                if(cssSelector != undefined && cssSelector.length) {
                    // Add the selector to the data
                    data["valueSelector"] = cssSelector;

                    if(attr != undefined && attr.length) {
                        data["valueSelectorAttr"] = attr;
                    }

                    // Mark it as found
                    found = true;
                }
            }
        });

        // If the selector could not be found, try custom post meta options.
        if(!found) {
            if(isCustomMeta) {
                $('.input-group.custom-post-meta .meta-key').each(function () {
                    if (found) return;

                    var $self = $(this);
                    if ($self.val() == key) {
                        var $valueInput = $self.closest('.input-group').find('input[type=text]:not(.meta-key)'),
                            value = $valueInput.val();

                        if (value != undefined && value.length) {
                            data["subject"] = value;

                            // Mark it as found
                            found = true;
                        }
                    }
                });
            }
        }

        return data;
    }

    /**
     * Shows "required for test" notification by default. If you supply another message, shows it instead.
     *
     * @param $targetEl
     * @param notificationMessage If defined, this message will be shown. Otherwise, a default message will be shown.
     */
    function notify($targetEl, notificationMessage) {
        if(typeof $.fn.notify != 'function') {
            console.error("NotifyJS is not defined.");
            return;
        }

        if(notificationMessage == undefined || !notificationMessage.length) notificationMessage = wpcc.required_for_test;

        // Find the closest label
        var $label = $targetEl.closest("tr").find("label").first(),
            $notificationEl = $label.length ? $label : $targetEl;

        $(document).find('body').stop().animate({
            scrollTop: $notificationEl.first().offset().top - $(window).height()/4
        }, 500, 'swing');

        $notificationEl.notify(notificationMessage, {
            position: 'top'
        });
    }

    /**
     * Hide test result content container
     */
    $(document).on('click', '.hide-test-results', function($event) {
        $event.preventDefault();

        // Find closest test results
        var $self = $(this);

        // Hide it
        $self.closest(".test-results").addClass("hidden");
    });

    /*
     * Load/refresh translation languages
     */
    $containerMetaBox.on('click', selectorLoadTranslationLanguages, function($event) {
        $event.preventDefault();

        var $self       = $(this),
            data        = $self.data("wcc"),
            serviceType = data["serviceType"],
            selectors   = data["selectors"],
            requestType = data["requestType"],
            ajaxData    = {}
        ;

        // If it is still loading, do not try to load it twice.
        if($self.hasClass("loading")) return;

        for(var key in selectors) {
            if(!selectors.hasOwnProperty(key)) continue;

            var selector = selectors[key],
                $targetEl = $(selector),
                val = $targetEl.val();

            // If value of the element does not exist, notify the user and stop.
            if(!val.length) {
                notify($targetEl, wpcc.required);
                return;
            }

            ajaxData[key] = val;
        }

        // Prepare the data that will be sent via an AJAX request
        var preparedAjaxData = {};
        preparedAjaxData[serviceType] = ajaxData;
        preparedAjaxData["requestType"] = requestType;
        preparedAjaxData["isOption"] = $(".wcc-general-settings").length ? 1 : 0;

        // Add loading class to the button.
        $self.addClass("loading");

        // Make the request
        $.post(ajaxurl, {
                wcc_nonce: $wccNonce.val(),
                action: pageActionKey,
                data: preparedAjaxData
            })
            .done(function(response) {
                if(response == undefined || !response || response.view == undefined || (response.errors != undefined && response.errors.length)) {
                    notify($self, wpcc.an_error_occurred);
                    console.log(response);

                    if(response.view != undefined) {
                        // Show the errors
                        var $resultContainer = $self.closest("td").find(".test-results");
                        var $contentContainer = $resultContainer.find(".content");
                        $contentContainer.html(response.view);
                        $resultContainer.removeClass("hidden");
                    }

                    return;
                }

                //l(response);

                // Show the select elements with the languages
                var $viewFrom = $(response.view.from),
                    $viewTo = $(response.view.to),
                    keyFrom = $viewFrom.find("select").first().attr("name"),
                    keyTo = $viewTo.find("select").first().attr("name")
                ;

                $("label[for='" + keyFrom + "']").closest("tr").find("td:nth-child(2)").html(response.view.from);
                $("label[for='" + keyTo + "']").closest("tr").find("td:nth-child(2)").html(response.view.to);

                // Flash the backgrounds of the select elements to inform the user that the request has been successful.
                flashBackground($("#" + keyFrom));
                flashBackground($("#" + keyTo));

            })
            .fail(function(response) {
                // Notify that the request has failed.
                notify($self, wpcc.an_error_occurred + ": " + response.responseText);
                console.log(response);
            })
            .always(function() {
                // Remove loading class
                $self.removeClass("loading");
            });
    });

    /**
     * Handle tabs
     */
    $containerMetaBox.on('click', '.nav-tab-wrapper > a', function($event) {
        $event.preventDefault();
        activateTab($(this).data("tab"));

    });

    /**
     * Show/hide labels
     */
    $containerMetaBox.on('click', '.info-button', function($event) {
        $event.preventDefault();

        // Show closest info text
        var $infoText = $(this).parent().find('.info-text').first();
        if($infoText.hasClass('hidden')) {
            $infoText.removeClass('hidden');
        } else {
            $infoText.addClass('hidden');
        }
    });

    /**
     * Show/hide dependants of "list type" checkbox
     */
    $containerMetaBox.on("change", "input[type=checkbox]", function() {
        handleCheckboxDependants($(this));
    });

    // Trigger change on checkboxes when the page is ready
    $(document).ready(function() {
        $containerMetaBox.find("input[type=checkbox]").each(function() {
            handleCheckboxDependants($(this));
        });
    });

    /**
     * Toggle info texts
     */
    var infoTextsHidden = true;
    $containerMetaBox.on("click", ".toggle-info-texts", function($event) {
        $event.preventDefault();

        // Find all info texts and show/hide
        $containerMetaBox.find(".info-text").each(function() {
            if(infoTextsHidden) {
                $(this).removeClass("hidden");
            } else {
                $(this).addClass("hidden");
            }
        });

        infoTextsHidden = !infoTextsHidden;
    });

    /*
        COPY TO CLIPBOARD
     */

    // Initialize the clipboard
    if(typeof Clipboard == 'function') {
        $(document).ready(function () {
            var selector = '.input-button-container > button';

            // No need to initialize the clipboard if the elements we need do not exist in the page.
            if(!$(selector).length) return;

            var clipboard = new Clipboard(selector);

            /**
             * Inform the user that the button's code is copied or not copied
             * @param $checkbox
             */
            clipboard.on('success', function (e) {
                flashTooltip($(e.trigger), wpcc.copied);
                e.clearSelection();
            });

            /**
             * When there is an error, the text becomes selected. Hence, the user can just use a shortcut to copy the text
             */
            clipboard.on('error', function (e) {
                var os = navigator.platform;
                var shortcut = os.indexOf("Mac") != -1 ? "⌘-C" : "Ctrl + C";
                flashTooltip($(e.trigger), wpcc.press_to_copy.format(shortcut));

            });

            /**
             * Shows the specified title as the element's tooltip, and then changes the tooltip to its original value.
             * Hence, the user will see the original title when the tooltip is shown next time.
             * @param $element
             * @param flashTitle
             */
            function flashTooltip($element, flashTitle) {
                var originalTitle = $element.attr("data-original-title");
                $element
                    .attr('data-original-title', flashTitle)
                    .tooltip('fixTitle')
                    .tooltip('show')
                    // Set the original title but do not show it. The user will see the original title at next hover
                    .attr('data-original-title', originalTitle)
                    .tooltip('fixTitle');
            }
        });
    }

    /**
     * Prevent default action for the buttons used for copying texts.
     */
    $containerMetaBox.on('click', '.input-button-container > button', function($event) {
        $event.preventDefault();
    });

    /*
     END COPY TO CLIPBOARD
     */

    /**
     * Load/clear general settings when its button is clicked
     */
    $(document).on('click', selectorLoadGeneralSettingsButton, handleLoadClearGeneralSettings);
    $(document).on('click', selectorClearGeneralSettingsButton, handleLoadClearGeneralSettings);

    function handleLoadClearGeneralSettings($e) {
        $e.preventDefault();

        var $self = $(this),
            id = $self.attr("id");

        // Do not proceed if this is currently loading.
        if($self.hasClass("loading")) return;
        $self.addClass("loading");

        $.post(ajaxurl, {
                wcc_nonce: $wccNonce.val(),
                action: pageActionKey,
                data: {
                    cmd: ("#" + id) == selectorClearGeneralSettingsButton ? "clearGeneralSettings" : "loadGeneralSettings"
                }
            })
            .done(function(response){
                var view = response.view;

                // Replace the current settings with the ones in the response.
                // Trigger 'change' on checkboxes so that the dependants can be shown or hidden accordingly.
                $(selectorTabGeneralSettings).html(view).find("[type=checkbox]").trigger('change');

            })
            .fail(function(response) {
                console.log(response);
            })
            .always(function() {
                // Remove loading class
                $self.removeClass("loading");
            });
    }

    /**
     * Disable sorting (being moved around) of the meta boxes having not-sortable class
     * @see http://wordpress.stackexchange.com/a/73806/87173
     */
    if(typeof window.jQuery.ui != 'undefined' && typeof $.fn.sortable == 'function') {
        $(document).ready(function () {
            $(".meta-box-sortables")
            // define the cancel option of sortable to ignore sortable element
            // for boxes with '.not-sortable' css class
                .sortable('option', 'cancel', '.not-sortable .hndle, :input, button')
                // and then refresh the instance
                .sortable('refresh');

            // Make "multiple" inputs sortable
            $(".inputs").sortable({
                placeholder: "sortable-placeholder",
                handle: '.wcc-sort',
                items: ' > .input-group',
                axis: "y",
                cursor: "move",
                start: function(e, ui) {
                    // Make placeholder's height the same as the current item's height
                    ui.placeholder.height(ui.helper.outerHeight());
                }
            });
        });
    }

    /**
     * Select the code for exporting settings
     */
    $("#_post_export_settings").focus(function() {
        var $this = $(this);
        $this.select();

        // Work around Chrome's little problem
        $this.mouseup(function() {
            // Prevent further mouseup intervention
            $this.unbind("mouseup");
            return false;
        });
    });

    /**
     * Validate the form before submitting
     */
    $form.on('submit', function($e) {
        validateForm($e);

        // Add current scroll position to hidden active tab input's value
        var $hiddenActiveTabInput = $(selectorInputActiveTab);
        if($hiddenActiveTabInput.length && $hiddenActiveTabInput.first().val()) {
            var val = $hiddenActiveTabInput.first().val();
            var newVal = val + "|" + document.documentElement.scrollTop;
            $hiddenActiveTabInput.val(newVal);
        }
    });

    /**
     * Validate the form
     * @param $event The event
     */
    function validateForm($event) {
        // If the import textarea has a value, do not validate.
        var $importInput = $(selectorInputImport);
        if($importInput.length && $importInput.val().length > 0) {
            removeErrorsFromAllTabs();
            return;
        }

        var $categoryMapContainer = $(selectorCategoryMap),
            $inputMainUrl         = $("#_main_page_url"),
            $passwordsContainer   = $(selectorInputContainerPasswords),
            errorElements        = [];

        $errorAlert.addClass("hidden");
        var hasError = false;
        removeErrorsFromAllTabs();

        // Validate category map
        if($categoryMapContainer.length) {
            // Check if any URL is added more than once
            var urls = [];
            var errorCategoryMap = false;

            $categoryMapContainer.find('.input-group').each(function() {
                $(this).removeClass(clsHasError);
            });

            $categoryMapContainer.find("input[type=text]").each(function() {
                var $self = $(this);
                if(urls.indexOf($self.val()) == -1) {
                    urls.push($self.val());
                } else {
                    $self.closest(".input-group").addClass(clsHasError);
                    if(!errorCategoryMap) errorCategoryMap = true;
                }

                // Check if any category map URL is empty
                if(!$self.val().length) {
                    $self.closest(".input-group").addClass(clsHasError);
                    if(!errorCategoryMap) errorCategoryMap = true;
                }
            });

            if(errorCategoryMap) {
                hasError = true;

                // Add this among error elements.
                errorElements.push($categoryMapContainer);
            }
        }

        // Validate main url
        if($inputMainUrl.length) {
            $inputMainUrl.closest(".input-group").removeClass(clsHasError);
            if(!$inputMainUrl.val().length) {
                hasError = true;
                $inputMainUrl.closest(".input-group").addClass(clsHasError);

                // Add this among error elements.
                errorElements.push($inputMainUrl);
            }
        }

        // Validate passwords
        if($passwordsContainer.length) {
            var cbChangePassword = $("#_wpcc_change_password");

            // If the checkbox required for changing the password is checked, validate the pw fields.
            if(cbChangePassword != undefined && cbChangePassword[0].checked) {
                $passwordsContainer.each(function () {
                    $(this).closest(".input-group").removeClass(clsHasError);
                });
                var passwordOld = null,
                    password1 = null,
                    password2 = null;
                $passwordsContainer.find("input[type=password]").each(function () {
                    if (passwordOld == null) {
                        passwordOld = true;
                    } else if (password1 == null) {
                        password1 = $(this).val();

                    } else if (password2 == null) {
                        password2 = $(this).val();

                        if (password1 != password2) {
                            $(this).closest(".input-group").addClass(clsHasError);
                            password1 = password2 = null;
                            hasError = true;

                            // Add this among error elements.
                            errorElements.push($(this));
                        }
                    }
                });
            }
        }

        if(hasError) {

            // Tab errors are not shown for some reason. However, setting them with 1 ms delay works OK.
            setTimeout(function() {
                // Show tab errors
                for(var i in errorElements) {
                    if(!errorElements.hasOwnProperty(i)) continue;
                    setTabError(errorElements[i], true);
                }
            }, 1);

            $errorAlert.removeClass("hidden");
            $event.preventDefault();
        }
    }

    /**
     * Set error indicator for a tab
     * @param $element The element which caused an error
     * @param hasError true if the tab should have an error mark, false otherwise
     */
    function setTabError($element, hasError) {
        var tabId = $element.closest('.tab').attr("id");
        var $tab = $containerTabs.find("[data-tab='#" + tabId + "']");
        if(!hasError) {
            $tab.removeClass(clsHasError);
        } else {
            $tab.addClass(clsHasError);
        }
    }

    /**
     * Removes error classes from all tabs
     */
    function removeErrorsFromAllTabs() {
        $containerTabs.find('.nav-tab').each(function() {
            $(this).removeClass(clsHasError);
        });
    }

    /**
     * Activate a tab
     * @param tabSelector Should be a tab ID and start with "#"
     */
    function activateTab(tabSelector) {
        var $tab = $containerTabs.find('[data-tab="' + tabSelector + '"]');
        if(!$tab.length || $tab.hasClass("hidden")) return;

        // First deactivate all tabs
        $containerMetaBox.find(".tab").addClass("hidden");
        $containerTabs.find("a").removeClass("nav-tab-active");

        // Now activate the selected tab
        var elementId = $tab.data("tab");

        $(elementId).removeClass("hidden");
        $tab.addClass("nav-tab-active");

        // Store active tab ID in a hidden input. We will use the hidden input value to reactive the tab when the
        // user comes back to the page after saving the settings.
        $(selectorInputActiveTab).val(elementId.replace("#", ''));

        // Change window hash without affecting browser history. By this way, going back won't result in a tab change.
        history.replaceState(undefined, undefined, elementId.replace("#", "#_"));
    }

    /**
     * Handles URL hash changes. E.g. activates tabs.
     */
    function handleURLHash() {
        var hash = window.location.hash;
        if(hash && hash.indexOf("#_") === 0) {
            activateTab(hash.replace("#_", "#"));
        }
    }

    /**
     * Activate the tab that was active before saving the settings
     */
    $(document).ready(function() {
        var $input = $(selectorInputActiveTab);
        if(!$input.length || !$input.first().val()) return;

        var values = $input.first().val().split("|");
        if(values.length !== 2) return;

        var tabId = "#" + values[0],
            scrollPos = values[1];

        // Activate the tab and move the page to the previous scroll position
        activateTab(tabId);
        document.documentElement.scrollTop = scrollPos;
    });

    function handleCheckboxDependants($checkbox) {
        // Get the elements depending on this checkbox.
        var dependants = $checkbox.data("dependants");
        if(dependants == undefined || !dependants) return;

        // If checked, show the dependants.
        if($checkbox.is(":checked")) {
            for(i = 0; i < dependants.length; i++) $(dependants[i]).removeClass("hidden");

        // Otherwise, hide 'em.
        } else {
            for(i = 0; i < dependants.length; i++) $(dependants[i]).addClass("hidden");
        }
    }

    /**
     * Adds a new input group to an input group container
     * @param $inputGroupContainer
     * @return New input group
     */
    function addNewInputGroup($inputGroupContainer) {
        // Create a clone of the input group
        var $inputGroup = $inputGroupContainer.find(".input-group").first().clone();

        /*
         HANDLE THE DATA KEY
         */

        // First, find max data key
        var maxDataKey = 0;
        $inputGroupContainer.find('.input-group').each(function() {
            var $self = $(this);
            if($self.data("key") != undefined && $self.data("key") > maxDataKey) {
                maxDataKey = $self.data("key");
            }
        });

        // Hold current data key
        var currentDataKey = $inputGroup.data("key");
        var newDataKey = maxDataKey + 1;

        // Set the new data key
        $inputGroup.attr("data-key", newDataKey);   // This will update the HTML.
        $inputGroup.data("key", newDataKey);        // This makes the actual change.

        var html = $inputGroup.html();
        $inputGroup.html(html.replace(new RegExp("\\[" + currentDataKey + "\\]", "g"), "[" + newDataKey + "]"));

        /* END DATA KEY */

        // Remove the values of the inputs
        $inputGroup.find("input").each(function() {
            $(this).val("");
        });

        $inputGroup.find("textarea").each(function () {
            $(this).html("");
        });

        $inputGroup.find("input[type=checkbox]").each(function() {
            $(this).prop('checked', false);
        });

        // Append it to the container
        $inputGroupContainer.append($inputGroup);

        // Check for tooltip and initialize it
        if(typeof $.fn.tooltip == 'function')
            $inputGroup.find('[data-toggle="tooltip"]').tooltip();

        return $inputGroup;
    }

    /**
     * Adds required settings to the data that will be sent via AJAX.
     * @param data
     * @returns {*}
     */
    function addSettingsToAjaxData(data) {
        var $useCustomSettingsCheckbox = $("#_do_not_use_general_settings"),
            $useUtf8Checkbox = $("#_wpcc_make_sure_encoding_utf8");

        if(!$useCustomSettingsCheckbox.length) return data;

        // If the user wants to use custom general settings
        if($useCustomSettingsCheckbox[0].checked) {
            // Add whether the user wants to use UTF-8 or not to the data
            data["useUtf8"] = $useUtf8Checkbox.first()[0].checked ? 1 : 0;

        } else {
            data["useUtf8"] = -1;
        }

        return data;
    }

    /**
     * Escapes special regex chars
     *
     * @param str
     * @returns {*}
     * @see http://stackoverflow.com/a/1144788/2883487
     */
    function escapeRegExp(str) {
        return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
    }

    /**
     * Flash the background color of an object
     * @param {object} $element Target element
     */
    function flashBackground($element) {
        $element.stop().css("background-color", "#b8ea84")
            .animate({ backgroundColor: "#FFFFFF"}, 1000);
    }

    // Export some functions
    WPCC_POST_SETTINGS.addSettingsToAjaxData = addSettingsToAjaxData;

});