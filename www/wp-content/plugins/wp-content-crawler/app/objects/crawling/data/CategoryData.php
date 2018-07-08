<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/08/16
 * Time: 15:04
 */

namespace WPCCrawler\objects\crawling\data;


class CategoryData {

    /** @var array */
    private $postUrls;

    /** @var array */
    private $thumbnails;

    /** @var string */
    private $nextPageUrl;

    /** @var array */
    private $errors;

    /*
     * GETTERS AND SETTERS
     */

    /**
     * @return array
     */
    public function getPostUrls() {
        return $this->postUrls;
    }

    /**
     * @param array $postUrls
     */
    public function setPostUrls($postUrls) {
        $this->postUrls = $postUrls;
    }

    /**
     * @return array
     */
    public function getThumbnails() {
        return $this->thumbnails;
    }

    /**
     * @param array $thumbnails
     */
    public function setThumbnails($thumbnails) {
        $this->thumbnails = $thumbnails;
    }

    /**
     * @return string
     */
    public function getNextPageUrl() {
        return $this->nextPageUrl;
    }

    /**
     * @param string $nextPageUrl
     */
    public function setNextPageUrl($nextPageUrl) {
        $this->nextPageUrl = $nextPageUrl;
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors) {
        $this->errors = $errors;
    }

}