<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/08/16
 * Time: 11:33
 */

namespace WPCCrawler\objects\crawling\data;


use WPCCrawler\Utils;

class PostData {

    /**
     * NOTE: Translate more wisely. For example, instead of translating listNumbers, listTitles, etc., just translate
     * the final post template. By this way, the number of chars to be translated will be less, hence, less money will
     * be spent for the translation service.
     *
     * @var array Stores the names of the fields that can be translated. The values indicate the translatable keys of
     *      the array-valued fields. E.g. if the value of "attachmentData" field has an array value, whose each item
     *      has translatable values in "title" and "alt" keys, ["attachmentData" => ["title", "alt"]] indicates this.
     *      Make sure the value is defined for a translatable field. If you do not specify a value, the field won't be
     *      considered as translatable. So, ["title"] is not translatable, while ["title" => ""] is translatable. An
     *      empty value means the value of the field is entirely translatable.
     */
    const TRANSLATABLE_FIELDS = [
        "title"                 => "",
        "excerpt"               => "data",
        "template"              => "",
        "preparedTags"          => "",
        "metaKeywords"          => "",
        "metaDescription"       => "",
        "customMeta"            => "data",
        "attachmentData"        => ["title", "alt"],
    ];

    /** @var bool */
    private $paginate;

    /** @var string */
    private $nextPageUrl;

    /** @var array */
    private $allPageUrls;

    /*
     *
     */

    /** @var string */
    private $title;

    /** @var array */
    private $excerpt;

    /** @var array */
    private $contents;

    /** @var string */
    private $dateCreated;

    /** @var array */
    private $shortCodeData;

    /** @var array */
    private $tags;

    /** @var array */
    private $preparedTags;

    /*
     * LIST
     */

    /** @var int */
    private $listStartPos;

    /** @var array */
    private $listNumbers;

    /** @var array */
    private $listTitles;

    /** @var array */
    private $listContents;

    /*
     * META
     */

    /** @var string */
    private $metaKeywords;

    /** @var array */
    private $metaKeywordsAsTags;

    /** @var string */
    private $metaDescription;

    /*
     *
     */

    /** @var string */
    private $thumbnailUrl;

    /** @var array */
    private $sourceUrls;

    /** @var array */
    private $attachmentData;

    /*
     *
     */

    /** @var array */
    private $customMeta;

    /** @var string */
    private $template;

    /** @var array */
    private $errors;

    /*
     * GETTERS AND SETTERS
     */

    /**
     * @return boolean
     */
    public function isPaginate() {
        return $this->paginate;
    }

    /**
     * @param boolean $paginate
     */
    public function setPaginate($paginate) {
        $this->paginate = $paginate;
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
    public function getAllPageUrls() {
        return $this->allPageUrls;
    }

    /**
     * @param array $allPageUrls
     */
    public function setAllPageUrls($allPageUrls) {
        $this->allPageUrls = $allPageUrls;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function getExcerpt() {
        return $this->excerpt;
    }

    /**
     * @param array $excerpt
     */
    public function setExcerpt($excerpt) {
        $this->excerpt = $excerpt;
    }

    /**
     * @return array
     */
    public function getContents() {
        return $this->contents;
    }

    /**
     * @param array $contents
     */
    public function setContents($contents) {
        $this->contents = $contents;
    }

    /**
     * @return string
     */
    public function getDateCreated() {
        return $this->dateCreated;
    }

    /**
     * @param $dateCreated
     */
    public function setDateCreated($dateCreated) {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return array
     */
    public function getShortCodeData() {
        return $this->shortCodeData;
    }

    /**
     * @param array $shortCodeData
     */
    public function setShortCodeData($shortCodeData) {
        $this->shortCodeData = $shortCodeData;
    }

    /**
     * @return array
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags) {
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function getPreparedTags() {
        return $this->preparedTags;
    }

    /**
     * @param array $preparedTags
     */
    public function setPreparedTags($preparedTags) {
        $this->preparedTags = $preparedTags;
    }

    /**
     * @return int
     */
    public function getListStartPos() {
        return $this->listStartPos;
    }

    /**
     * @param int $listStartPos
     */
    public function setListStartPos($listStartPos) {
        $this->listStartPos = $listStartPos;
    }

    /**
     * @return array
     */
    public function getListNumbers() {
        return $this->listNumbers;
    }

    /**
     * @param array $listNumbers
     */
    public function setListNumbers($listNumbers) {
        $this->listNumbers = $listNumbers;
    }

    /**
     * @return array
     */
    public function getListTitles() {
        return $this->listTitles;
    }

    /**
     * @param array $listTitles
     */
    public function setListTitles($listTitles) {
        $this->listTitles = $listTitles;
    }

    /**
     * @return array
     */
    public function getListContents() {
        return $this->listContents;
    }

    /**
     * @param array $listContents
     */
    public function setListContents($listContents) {
        $this->listContents = $listContents;
    }

    /**
     * @return string
     */
    public function getMetaKeywords() {
        return $this->metaKeywords;
    }

    /**
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords) {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return array
     */
    public function getMetaKeywordsAsTags() {
        return $this->metaKeywordsAsTags;
    }

    /**
     * @param array $metaKeywordsAsTags
     */
    public function setMetaKeywordsAsTags($metaKeywordsAsTags) {
        $this->metaKeywordsAsTags = $metaKeywordsAsTags;
    }

    /**
     * @return string
     */
    public function getMetaDescription() {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription) {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return string
     */
    public function getThumbnailUrl() {
        return $this->thumbnailUrl;
    }

    /**
     * @param string $thumbnailUrl
     */
    public function setThumbnailUrl($thumbnailUrl) {
        $this->thumbnailUrl = $thumbnailUrl;
    }

    /**
     * @return array
     */
    public function getSourceUrls() {
        return $this->sourceUrls;
    }

    /**
     * @param array $sourceUrls
     */
    public function setSourceUrls($sourceUrls) {
        $this->sourceUrls = $sourceUrls;
    }

    /**
     * @return array
     */
    public function getAttachmentData() {
        return $this->attachmentData;
    }

    /**
     * @param array $attachmentData
     */
    public function setAttachmentData($attachmentData) {
        $this->attachmentData = $attachmentData;
    }

    /**
     * Deletes previously saved attachments.
     */
    public function deleteAttachments() {
        if($this->getAttachmentData()) {
            foreach($this->getAttachmentData() as $attachmentData) {
                Utils::deleteFile($attachmentData["path"]);
            }
        }
    }

    /**
     * @return array
     */
    public function getCustomMeta() {
        return $this->customMeta;
    }

    /**
     * @param array $customMeta
     */
    public function setCustomMeta($customMeta) {
        $this->customMeta = $customMeta;
    }

    /**
     * @return string
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template) {
        $this->template = $template;
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