<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/07/17
 * Time: 06:11
 */

namespace WPCCrawler\objects\translation\microsoft;
use WPCCrawler\objects\traits\FindAndReplaceTrait;

/**
 * Interacts with Microsoft Translator Text API
 *
 * @package WPCCrawler\objects\translation\microsoft
 */
class MicrosoftTranslateClient {

    use FindAndReplaceTrait;

    /**
     * @var string Option key that is used to cache access token data.
     */
    const OPTION_KEY_MICROSOFT_TRANSLATE_ACCESS_TOKENS = '_wpcc_microsoft_translator_text_access_tokens';

    /*
     *
     */

    /** @var string URL to which a request will be made to translate an array of strings */
    private $urlTranslateArray = "https://api.microsofttranslator.com/v2/Http.svc/TranslateArray";

    /** @var string URL to which a request will be made to get supported language codes */
    private $urlGetLanguagesForTranslate = "http://api.microsofttranslator.com/V2/Http.svc/GetLanguagesForTranslate";
    /** @var string URL to which a request will be made to get names of support languages */
    private $urlGetLanguageNamesFormat = "http://api.microsofttranslator.com/V2/Http.svc/GetLanguageNames?locale=%s";

    /** @var string Client secret. This will be used to get an access token when required. */
    private $clientSecret;

    /** @var int For how many seconds an access token is valid. This is decided by Microsoft. */
    private $accessTokenValidForSeconds = 600;

    /*
     *
     */

    /**
     * @param string $clientSecret {@link $clientSecret}
     * @throws \Exception When the client secret is not valid.
     */
    public function __construct($clientSecret) {
        if(!$clientSecret) throw new \Exception("You have to provide a valid client secret.");

        $this->clientSecret = $clientSecret;
    }

    /**
     * Translates an array of strings from a language into another
     *
     * @param array  $texts An array of strings that should be translated. The total of all texts to be translated must
     *                      not exceed 10000 characters. The maximum number of array elements is 2000.
     * @param string $from  The source language to translate from. Must be a valid ISO 639-1 language code. Leave this
     *                      empty to automatically detect the language.
     * @param string $to    The target language to translate to. Must be a valid ISO 639-1 language code.
     * @return array
     * @throws \Exception If the translation did not succeed
     */
    public function translateBatch($texts, $from = '', $to) {
        $httpTranslator = new HTTPTranslator();

        // Create request XML
        $requestXml = $httpTranslator->createTranslateArrayReqXML($from, $to, HTTPTranslator::CONTENT_TYPE_TEXT_HTML, $texts);

        $curlResponse = $httpTranslator->curlRequest($this->urlTranslateArray, $this->getAuthHeader(), $requestXml);

        // Interprets a string of XML into an object
        $xmlObj = simplexml_load_string($curlResponse);

        if(!isset($xmlObj->TranslateArrayResponse)) throw new \Exception($curlResponse);

        // Create an array that stores the translations.
        $translations = [];
        foreach($xmlObj->TranslateArrayResponse as $translatedArrObj){
            $translations[] = (string) $translatedArrObj->TranslatedText;
        }

        return $translations;
    }

    /**
     * Get supported languages.
     *
     * @param array $options Structure: ["target" => "target_lang_code] e.g. ["target" => "en"]
     * @return array Structure is: [0 => ["code" => "language1_code", "name" => "language1_name], 1 => ["code" => "language2_code", "name" => "language2_name], ...]
     * @throws \Exception
     */
    public function localizedLanguages($options = []) {
        $targetLangCode = $options && isset($options["target"]) && $options["target"] ? $options["target"] : "en";

        $httpTranslator = new HTTPTranslator();

        // Get supported language codes
        $curlResponse = $httpTranslator->curlRequest($this->urlGetLanguagesForTranslate, $this->getAuthHeader());
        $xmlObjLanguageCodes = simplexml_load_string($curlResponse);

        if(!isset($xmlObjLanguageCodes->string)) {
            // Microsoft sends an HTML code as response. Typical Microsoft. So, let's add spaces before each opening of
            // HTML tags and strip tags. By this way, we have a slightly formatted text.
            $curlResponse = $this->findAndReplaceSingle("(<[^\/].*?>)", "$1 ", $curlResponse, true);
            throw new \Exception(trim(strip_tags($curlResponse)));
        }

        // Collect the language codes
        $languageCodes = [];
        foreach($xmlObjLanguageCodes->string as $language){
            $languageCodes[] = (string) $language;
        }

        // Get names of the language codes
        $requestXml = $httpTranslator->createLanguageNamesReqXML($languageCodes);
        $url = sprintf($this->urlGetLanguageNamesFormat, $targetLangCode);
        $curlResponse = $httpTranslator->curlRequest($url, $this->getAuthHeader(), $requestXml);
        $xmlObjLanguageNames = simplexml_load_string($curlResponse);

        $result = [];
        $i = 0;
        foreach($xmlObjLanguageNames->string as $language) {
            $result[] = [
                "code" => $languageCodes[$i],
                "name" => (string) $language,
            ];

            $i++;
        }

        return $result;
    }

    /*
     * PRIVATE METHODS
     */

    /**
     * Get authentication header string that can be used when making request to the API.
     *
     * @return string
     */
    private function getAuthHeader() {
        return "Authorization: Bearer " . $this->getAccessToken();
    }

    /**
     * Get the access token by making a cURL request or use cached access token if it is still valid.
     *
     * @return string Access token
     */
    private function getAccessToken() {
        $nowTimestamp = time();

        // First, get the cached access token data
        $cachedAccessTokenData = get_option(static::OPTION_KEY_MICROSOFT_TRANSLATE_ACCESS_TOKENS, []);
        if($cachedAccessTokenData && isset($cachedAccessTokenData[$this->clientSecret])
            && $tokenData = $cachedAccessTokenData[$this->clientSecret]) {

            $tokenRetrievalTimestamp = $tokenData["timestamp"];
            $token = $tokenData["token"];

            $diffSeconds = $nowTimestamp - $tokenRetrievalTimestamp;

            // If there is a token and it is still valid, return it.
            if($diffSeconds < $this->accessTokenValidForSeconds && $token) return $token;
        }

        // We could not find a valid access token retrieved before.

        // Create the AccessTokenAuthentication object.
        $authObj = new AccessTokenAuthentication();

        // Get the Access token.
        $accessToken = $authObj->getToken($this->clientSecret);

        // Update the access token data.
        $cachedAccessTokenData[$this->clientSecret] = [
            "timestamp" => $nowTimestamp,
            "token"     => $accessToken,
        ];

        // Make sure the option will be autoloaded to run less number of DB queries.
        update_option(static::OPTION_KEY_MICROSOFT_TRANSLATE_ACCESS_TOKENS, $cachedAccessTokenData, true);

        return $accessToken;
    }

}