<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 13/04/16
 * Time: 20:49
 */

/**
 * A replacement function for translatable texts
 *
 * @param string $string Text to be translated
 * @return string|void Translated text
 */
function _wpcc($string) {
    return __($string, \WPCCrawler\Constants::$APP_DOMAIN);
}

/**
 * Returns a translated string that informs about regular expressions
 *
 * @return string
 */
function _wpcc_trans_regex() {
    return _wpcc('You can write plain text or regular expressions. When writing regex, do not put slashes before and
        after the expression, and make sure the checkbox is checked. You can test your regex
        <a href="https://regex101.com/" target="_blank">here</a>.');
}

/**
 * Returns a translated string that informs that saving images increase the memory usage.
 *
 * @return string
 */
function _wpcc_trans_save_image_note() {
    return _wpcc('<b>Note that</b> saving images will increase the time  to save the posts, and hence memory usage.');
}

/**
 * @param string $url Optional URL that will be opened when the text is clicked.
 * @param bool   $openInNewWindow True if the URL should be opened in new window.
 * @return string 'How to get it?' text
 */
function _wpcc_trans_how_to_get_it($url = '', $openInNewWindow = true) {
    $text = _wpcc('How to get it?');

    if($url) {
        $text = sprintf('<a href="%1$s"%3$s>%2$s</a>', $url, $text, !$openInNewWindow ? '' : ' target="_blank"');
    }

    return $text;
}