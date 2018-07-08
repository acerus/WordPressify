<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 23:56
 */

namespace WPCCrawler\objects\traits;


trait FindAndReplaceTrait {

    private $_frErrors = [];

    /**
     * Reset find and replace errors.
     */
    protected function resetFindAndReplaceErrors() {
        $this->_frErrors = [];
    }

    /**
     * Get errors caused by find and replace operations
     * @return array    An array of errors. Each error has "error", which is error text, and "data", which is an info
     *                  including what to find and with what to replace, keys.
     */
    protected function getFindAndReplaceErrors() {
        return $this->_frErrors;
    }

    /**
     * Applies replacement to the given subject.
     *
     * @param array  $findAndReplaces An array of arrays. Inner array should have:
     *                                <b>"regex":    </b> (bool)    If this key <u>exists</u>, then search will be
     *                                performed as regular expression. If not, a normal search will be done.
     *                                <b>"find":     </b> (string)  What to find
     *                                <b>"replace":  </b> (string)  Replacement for what is found
     * @param string $subject         The subject to which finding and replacing will be applied
     * @param bool   $trim            True if you want the final result to be trimmed. Otherwise, false.
     * @return string The subject with all of the replacements are done
     */
    protected function findAndReplace($findAndReplaces, $subject, $trim = true) {
        if($findAndReplaces && !empty($findAndReplaces)) {
            foreach ($findAndReplaces as $fr) {
                if(!isset($fr["find"]) || (empty($fr["find"]) && $fr["find"] !== "0")) continue;
                if (isset($fr['regex'])) {
                    $r = preg_replace('/' . $fr['find'] . '/', $fr['replace'], $subject);

                    if($r !== null) {
                        $subject = $r;
                    } else {
                        $error = '';
                        switch(preg_last_error()) {
                            case PREG_INTERNAL_ERROR:
                                $error = "Internal error";
                                break;
                            case PREG_NO_ERROR:
                                $error = "No error";
                                break;
                            case PREG_BACKTRACK_LIMIT_ERROR:
                                $error = "Backtrack limit error";
                                break;
                            case PREG_RECURSION_LIMIT_ERROR:
                                $error = "Recursion limit error";
                                break;
                            case PREG_BAD_UTF8_OFFSET_ERROR:
                                $error = "Bad UTF8 offset error";
                                break;
                            case PREG_BAD_UTF8_ERROR:
                                $error = "Bad UTF8 error";
                                break;
                            case PREG_JIT_STACKLIMIT_ERROR:
                                $error = "JIT stacklimit error";
                                break;
                            default:
                                $error = "Unknown error";
                                break;
                        }

                        $this->_frErrors[] = [
                            "error" =>  $error,
                            "data"  =>  _wpcc("Find") . ": " . $fr["find"] . " | " . _wpcc("Replace") . ": " . $fr["replace"]
                        ];
                    }
                } else {
                    $subject = str_replace($fr['find'], $fr['replace'], $subject);
                }
            }
        }

        return $trim ? trim($subject) : $subject;
    }

    /**
     * Find and replace something in a subject.
     *
     * @param string $find    Find
     * @param string $replace Replace
     * @param string $subject The text to be searched
     * @param bool   $regex   True if find string is a regex.
     * @param bool   $trim    True if you want the final result to be trimmed. Otherwise, false.
     * @return string
     */
    protected function findAndReplaceSingle($find, $replace, $subject, $regex = false, $trim = true) {
        $fr = $this->createFindReplaceConfig($find, $replace, $regex);
        return $this->findAndReplace([$fr], $subject, $trim);
    }

    /**
     * Create a find-and-replace config that can be used with {@link findAndReplace} and {@link findAndreplaceSingle}
     *
     * @param string $find      Find
     * @param string $replace   Replace
     * @param bool   $regex     True if find string is a regex.
     * @return array
     */
    public function createFindReplaceConfig($find, $replace, $regex = false) {
        $fr = [];
        $fr['find'] = $find;
        $fr['replace'] = $replace;
        if($regex) $fr['regex'] = true;
        return $fr;
    }
}