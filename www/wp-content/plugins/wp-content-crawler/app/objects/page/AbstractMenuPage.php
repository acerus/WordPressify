<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 07/06/16
 * Time: 14:19
 */

namespace WPCCrawler\objects\page;
use WPCCrawler\Constants;
use WPCCrawler\Factory;

/**
 * A parent class that can be used to avoid routines when creating a submenu page for the plugin
 * @package WPTSPostTools\objects
 */
abstract class AbstractMenuPage extends AbstractPageHandler {

    private $pageHookSuffix = null;

    /**
     * @param bool $menuPage    If true, the menu item will be a parent menu item.
     * @param bool $doNotAdd    If true, menu page won't be added. This can be used to show/hide a menu page depending
     *                          on settings.
     */
    public function __construct($menuPage = false, $doNotAdd = false) {
        $wptslm = Factory::wptslmClient();

        if(!$wptslm->isUserCool() || $doNotAdd) return;

        // Create the menu page
        add_action('admin_menu', function() use ($menuPage) {
            $menuNameColor = $this->getMenuNameColor();

            // Prepare common parameters for add_menu_page and add_submenu_page. add_submenu_page has only 1 more parameter.
            $functionParams = [
                $this->getMenuTitle(),
                $menuNameColor ? "<span style='color: {$menuNameColor}'>" . $this->getMenuTitle() . "</span>" : $this->getMenuTitle(),
                Constants::$ALLOWED_USER_CAPABILITY,
                $this->getFullPageSlug(),
                function () {
                    $view = $this->getView();
                    // Add a page action key that can be used when creating nonces and a hidden action for AJAX requests
                    $view->with([
                        'pageActionKey' => $this->getPageActionKey(),
                        'pageTitle'     => $this->getPageTitle(),
                    ]);
                    echo $view->render();
                }
            ];

            if(!$menuPage) {
                // We want this menu item to be a submenu page. Add the missing parameter and call the function.
                $this->pageHookSuffix = call_user_func_array('add_submenu_page', array_merge(
                    ['edit.php?post_type=' . Constants::$POST_TYPE],
                    $functionParams
                ));
            } else {
                // We want this menu item to be a parent menu item.
                $this->pageHookSuffix = call_user_func_array('add_menu_page', $functionParams);
            }
        });

        // Construct the parent to handle POST and AJAX requests.
        parent::__construct();
    }

    /**
     * @return string Menu title for the page
     */
    public abstract function getMenuTitle();

    /**
     * @return string Page title
     */
    public abstract function getPageTitle();

    /**
     * Get view for the page.
     * @return mixed Not-rendered blade view for the page
     */
    public abstract function getView();

    /**
     * Get hook suffix for the page. This can be used for actions such as <b>load-$hook</b>.
     *
     * @return string|false Page hook suffix or false.
     */
    public function getPageHookSuffix() {
        return $this->pageHookSuffix;
    }

    /**
     * Override this method to change menu name color.
     *
     * @return string Color of menu name. E.g. #ff4400
     */
    protected function getMenuNameColor() {
        return null;
    }
}