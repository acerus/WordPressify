function l(variable) { console.log(variable); }

/*
 * GLOBAL VARIABLES
 */

var
    /** Stores variables for developer tools. The name is the short for "dev tools vars". */
    dtv = {},

    // Define objects
    devTools,
    iframeHandler,
    addressBar,
    cssSelectorToolbar,
    optionsToolbar,
    sidebarHandler;

jQuery(function($) {

    /*
     * DEFINE REQUIRED VARIABLES
     */

    dtv.postId = null;
    dtv.$inputDevToolsState = $('input[name=_dev_tools_state]').first();
    dtv.$currentDevToolsButton = null; // Stores the last clicked DEV tools button
    dtv.lightboxTitleSelector = '.lightbox-title';
    dtv.devToolsButtonSelector = '.wcc-dev-tools';
    dtv.devToolsContentContainerSelector = '.dev-tools-content-container';
    dtv.devToolsContentSelector = '.dev-tools-content';
    dtv.lightboxSelector = '.featherlight';
    dtv.lightboxContainerSelector = '.featherlight-content';
    dtv.toolbarSelector = dtv.lightboxContainerSelector + " " + dtv.devToolsContentSelector + " .toolbar";
    dtv.iframeSelector = dtv.lightboxContainerSelector + " " + dtv.devToolsContentSelector + " iframe.source";
    dtv.$wccNonce = $("#wcc_nonce");

    // Hover class is added to the elements in the target page's source code when they are hovered
    dtv.hoverClass = "wpcc-element-hovered";

    // Hover style will be added to the target page's source code. This can be used to assign styles to the hover class.
    //hoverStyle: ".wpcc-element-hovered {-webkit-box-shadow: inset 0px 0px 2px 2px rgba(255,0,0,1); -moz-box-shadow: inset 0px 0px 2px 2px rgba(255,0,0,1);box-shadow: inset 0px 0px 2px 2px rgba(255,0,0,1);}"
    //hoverStyle: "img.wpcc-element-hovered{border: 2px solid #ff4400 !important;} .wpcc-element-hovered {top:0 !important;left:0 !important;right:0 !important;bottom:0 !important;background-color:rgba(255, 0, 0, 0.6) !important;z-index:9999 !important;-webkit-box-shadow: inset 0px 0px 2px 2px rgba(255,0,0,1) !important; -moz-box-shadow: inset 0px 0px 2px 2px rgba(255,0,0,1) !important;box-shadow: inset 0px 0px 2px 2px rgba(255,0,0,1) !important;}";
    dtv.hoverStyleSelector = "#iframe-style";

    // An array used to cache the source codes of the URLs
    dtv.urlCache = [];

    // Stores the instance of the lightbox to keep it as a singleton.
    dtv.$lightboxInstance = null;

    dtv.addressBarSelector = dtv.toolbarSelector + " .address-bar";
    dtv.backButtonSelector = dtv.addressBarSelector + " .back";
    dtv.forwardButtonSelector = dtv.addressBarSelector + " .forward";
    dtv.refreshButtonSelector = dtv.addressBarSelector + " .refresh";
    dtv.goButtonSelector = dtv.addressBarSelector + " .go";
    dtv.urlInputSelector = dtv.addressBarSelector + " input";
    dtv.urlInputId = '_dt_toolbar_url';

    // Selectors for CSS selector tools
    dtv.cssSelectorToolsContainerSelector = dtv.lightboxContainerSelector + ' .css-selector-tools';
    dtv.cssInputSelector = dtv.lightboxContainerSelector + ' .css-selector-input input';
    dtv.cssInputId = '_dt_toolbar_css_selector';
    dtv.cssTestSelector = dtv.lightboxContainerSelector + ' .css-selector-test';
    dtv.cssClearHighlightsSelector = dtv.lightboxContainerSelector + ' .css-selector-clear-highlights';
    dtv.cssRemoveElementsSelector = dtv.lightboxContainerSelector + ' .css-selector-remove-elements';
    dtv.cssShowAlternativesSelector = dtv.lightboxContainerSelector + ' .css-selector-show-alternatives';
    dtv.cssUseButtonSelector = dtv.lightboxContainerSelector + ' .css-selector-use';

    dtv.toolbarTestResultsContainerSelector = dtv.lightboxContainerSelector + ' .test-results';
    dtv.toolbarTestResultsContentContainerSelector = dtv.toolbarTestResultsContainerSelector + ' .content';

    dtv.iframeStatusSelector = dtv.lightboxContainerSelector + " .iframe-status";

    dtv.sidebarSelector = dtv.lightboxContainerSelector + " .sidebar";
    dtv.sidebarCloseSelector = dtv.sidebarSelector + " .sidebar-close";
    dtv.sidebarOpenSelector = '.sidebar-open';
    dtv.sidebarOpenedClass = 'opened';
    dtv.sidebarSectionClass = 'sidebar-section';
    dtv.sidebarSectionContentClass = 'section-content';
    dtv.sidebarSectionHistoryClass = 'history';
    dtv.sidebarSectionUsedSelectorsClass = 'used-selectors';
    dtv.sidebarSectionAlternativeSelectorsClass = 'alternative-selectors';
    dtv.sidebarSectionSelector = dtv.sidebarSelector + " ." + dtv.sidebarSectionClass;
    dtv.sidebarSectionTitleContainerSelector = dtv.sidebarSectionSelector + " .section-title";
    dtv.sidebarSectionTitleSelector = dtv.sidebarSectionTitleContainerSelector + " > span";
    dtv.sidebarSectionContentSelector = dtv.sidebarSectionSelector + " ." + dtv.sidebarSectionContentClass;

    dtv.btnClearHistorySelector = dtv.lightboxSelector + ' .clear-history';

    dtv.toggleExpandClass = 'toggleExpand';
    dtv.sidebarSectionToggleExpandSelector = dtv.sidebarSelector + ' .' + dtv.toggleExpandClass;
    dtv.sidebarSectionExpandedClass = 'expanded';

    dtv.settingsMetaBoxSelector = '.wcc-settings-meta-box';
    dtv.classCssSelector = 'selector';
    dtv.classUrl = 'url';

    dtv.classOptionsToolbar = 'options';
    dtv.optionsToolbarSelector = dtv.lightboxSelector + " ." + dtv.classOptionsToolbar;
    dtv.optHoverSelectSelector = dtv.optionsToolbarSelector + " .toggle-hover-select";
    dtv.optTargetHTMLTagClass = 'target-html-tag';
    dtv.optTargetHTMLTagSelector = dtv.optionsToolbarSelector + " ." + dtv.optTargetHTMLTagClass;
    dtv.optUseTestButtonBehaviorSelector = dtv.optionsToolbarSelector + " .test-button-behavior";
    dtv.optUseImmediatelySelector = dtv.optionsToolbarSelector + " .use-immediately";
    dtv.optRemoveScriptsSelector = dtv.optionsToolbarSelector + " .remove-scripts";
    dtv.optRemoveStylesSelector = dtv.optionsToolbarSelector + " .remove-styles";

    /** Stores the last highlighted element */
    dtv.$lastHighlighted = null;

    dtv.multipleSpaceRegex = new RegExp("\\s{2,}", "g");
    dtv.regexClassNameStartingWithDash = new RegExp("\\.(-[^\\s.#\\[]+)", "g");
    dtv.bracketClassNameRegex = new RegExp('\\[class="([^"]+)"\\]', 'g');

    /** Stores the last XHR made for source code retrieval */
    dtv.lastUnfinishedSourceCodeXHR = null;
    dtv.isAborted = false;

    /*
     * INITIALIZE OBJECTS
     */

    devTools            = new DEVTools();
    iframeHandler       = new IFrameHandler();
    addressBar          = new AddressBar();
    cssSelectorToolbar  = new CSSSelectorToolbar();
    optionsToolbar      = new OptionsToolbar();
    sidebarHandler      = new SidebarHandler();

    /*
     * HANDLE USER EVENTS
     */

    /**
     * Show the developer tools lightbox when its button is clicked
     */
    $(document).on('click', dtv.devToolsButtonSelector, function($e) {
        $e.preventDefault();

        var $self = $(this),
            data = $self.data("wcc"),
            urlSelector = null,
            $contentContainer = $(dtv.devToolsContentContainerSelector);

        // Assign the post ID.
        dtv.postId = $contentContainer.data("wcc")["postId"];

        // Assign the current DEV tools button.
        dtv.$currentDevToolsButton = $self;

        // Get the URL selector
        if(data["urlSelector"] != undefined) urlSelector = data["urlSelector"];

        // Get the test URL
        var url = $(urlSelector).val();

        // Show lightbox and then load the content into the iframe.
        devTools.showLightboxWithContent(null, url);
    });

    // Close the lightbox when the lightbox title is clicked
    $(document).on('click', dtv.lightboxTitleSelector, devTools.closeLightbox);

    // Listen to the button click events
    $(document).on('click', dtv.backButtonSelector, addressBar.onClickBack);
    $(document).on('click', dtv.forwardButtonSelector, addressBar.onClickForward);
    $(document).on('click', dtv.refreshButtonSelector, addressBar.onClickRefresh);
    $(document).on('click', dtv.goButtonSelector, addressBar.onClickGo);

    $(document).on('click', dtv.cssTestSelector, cssSelectorToolbar.onClickTest);
    $(document).on('click', dtv.cssUseButtonSelector, cssSelectorToolbar.onClickUseCssSelector);
    $(document).on('click', dtv.cssClearHighlightsSelector, cssSelectorToolbar.onClearHighlights);
    $(document).on('click', dtv.cssRemoveElementsSelector, cssSelectorToolbar.onRemoveElements);
    $(document).on('click', dtv.cssShowAlternativesSelector, cssSelectorToolbar.onShowAlternatives);

    $(document).on('click', dtv.sidebarCloseSelector, sidebarHandler.onCloseSidebar);
    $(document).on('click', dtv.lightboxContainerSelector + " " + dtv.sidebarOpenSelector, sidebarHandler.onOpenSidebar);
    $(document).on('click', dtv.sidebarSectionToggleExpandSelector, sidebarHandler.onToggleExpand);
    $(document).on('click', dtv.btnClearHistorySelector, addressBar.onClickClearHistory);

    // Toggle expand status when sidebar section title is clicked
    $(document).on('click', dtv.sidebarSectionTitleSelector, function() {
        $(this).closest("." + dtv.sidebarSectionClass).find("." + dtv.toggleExpandClass).first().click();
    });

    $(document).on('click', dtv.sidebarSelector + ' .' + dtv.classUrl, sidebarHandler.onClickHistoryUrl);
    $(document).on('click', dtv.sidebarSelector + ' .' + dtv.classCssSelector, sidebarHandler.onClickCssSelector);
    $(document).on('hover', dtv.sidebarSelector + ' .' + dtv.classCssSelector, sidebarHandler.onHoverCssSelector);

    $(document).on('click', dtv.optHoverSelectSelector, optionsToolbar.onClickToggleHoverSelect);
    $(document).on('change', dtv.optRemoveScriptsSelector, addressBar.onClickRefresh);
    $(document).on('change', dtv.optRemoveStylesSelector, addressBar.onClickRefresh);

    // Try to show the found elements when the user is typing the selector
    $(document).on('keyup change', dtv.cssInputSelector, function() {
        var $self = $(this),
            val = $self.val()
        ;

        // Do not proceed if the value is not valid.
        if(val == undefined || !val.length) {
            // Clear the highlights
            iframeHandler.clearHighlights();
            return;
        }

        iframeHandler.clearHighlights();
        iframeHandler.highlight(val);
    });

    // Listen to the changes on target HTML tag input
    $(document).on('keyup change', dtv.optTargetHTMLTagSelector, optionsToolbar.onChangeTargetHTMLTagInput);

    // Keyboard
    $(document).on('keydown', dtv.devToolsContentSelector, function(e) {
        //l(e.type + ": " + e.which);

        // The user is typing
        if($(e.target).is(':input')) {
            var $target = $(e.target);

            if(e.which == 13) {
                // If the enter is pressed in CSS input, press the test button.
                if($target.attr("id") == dtv.cssInputId) $(dtv.cssTestSelector).click();

                // If the enter is pressed in URL input, press the go button.
                if($target.attr("id") == dtv.urlInputId) $(dtv.goButtonSelector).click();
            }

            return;
        }

        // Let the SidebarHandler handle the key press.
        sidebarHandler.handleKeyPress(e);
    });

    /*
     * Respond to screen size changes
     */
    $(window).resize(function() {
        iframeHandler.setIframeHeight();
    });

});