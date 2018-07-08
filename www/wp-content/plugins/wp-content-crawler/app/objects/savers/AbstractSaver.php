<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 18/01/17
 * Time: 09:38
 */

namespace WPCCrawler\objects\savers;


abstract class AbstractSaver {

    /** @var bool True if the URL request has been made after an event is executed, false otherwise. */
    private $requestMade = false;

    /**
     * @param bool $bool True if the request is made, false otherwise.
     */
    protected function setRequestMade($bool) {
        $this->requestMade = $bool;
    }

    /**
     * @return bool See {@link requestMade}
     */
    public function isRequestMade() {
        return $this->requestMade;
    }
}