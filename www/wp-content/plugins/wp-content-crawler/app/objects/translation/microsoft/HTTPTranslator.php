<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/07/17
 * Time: 05:58
 */

namespace WPCCrawler\objects\translation\microsoft;

use Exception;

/**
 * Processes Microsoft Translator Text API request.
 *
 * @see https://github.com/MicrosoftTranslator/HTTP-Code-Samples/blob/master/PHP/TranslateMethod.php
 * @see https://github.com/MicrosoftTranslator/HTTP-Code-Samples/tree/master/PHP
 */
class HTTPTranslator {

    const CONTENT_TYPE_TEXT_PLAIN   = "text/plain";
    const CONTENT_TYPE_TEXT_XML     = "text/xml";
    const CONTENT_TYPE_TEXT_HTML    = "text/html";

    /**
     * Creates an XML string that can be sent via cURL to detect the language of an array of strings
     *
     * @param array $inputStrArr
     * @return string XML
     * @throws Exception
     */
    function createDetectArrayReqXML($inputStrArr) {
        $requestXml = '<ArrayOfstring xmlns="http://schemas.microsoft.com/2003/10/Serialization/Arrays" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';

        if(sizeof($inputStrArr) > 0){
            foreach($inputStrArr as $str)
                $requestXml .= "<string>$str</string>";

        } else {
            throw new Exception('$inputStrArr array is empty.');
        }

        $requestXml .= '</ArrayOfstring>';

        return $requestXml;
    }

    /**
     * Creates an XML string that can be sent via cURL to translate an array of strings
     *
     * @param string $fromLanguage Optional Source language code. If this is empty, the language will be automatically
     *                             detected.
     * @param string $toLanguage   Required. A string representing the language code to translate the text into.
     * @param string $contentType  The format of the text being translated. The supported formats are "text/plain"
     *                             (default), "text/xml" and "text/html". Any HTML needs to be a well-formed, complete
     *                             element. You can pass {@link HTTPTranslator::CONTENT_TYPE_TEXT_PLAIN},
     *                             {@link HTTPTranslator::CONTENT_TYPE_TEXT_XML} or
     *                             {@link HTTPTranslator::CONTENT_TYPE_TEXT_HTML} constants.
     * @param array  $inputStrArr  An array of strings that should be translated
     * @return string XML
     */
    public function createTranslateArrayReqXML($fromLanguage = '', $toLanguage, $contentType, $inputStrArr) {
        // Create the XML string for passing the values.
        $xmlns = "http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2";
        $requestXml = "<TranslateArrayRequest>".
            "<AppId/>" .
            "<From>$fromLanguage</From>" .
            "<Options>" .
                "<Category xmlns=\"{$xmlns}\" />" .
                "<ContentType xmlns=\"{$xmlns}\">$contentType</ContentType>" .
                "<ReservedFlags xmlns=\"{$xmlns}\" />" .
                "<State xmlns=\"{$xmlns}\" />" .
                "<Uri xmlns=\"{$xmlns}\" />" .
                "<User xmlns=\"{$xmlns}\" />" .
            "</Options>" .
            "<Texts>";

        foreach ($inputStrArr as $inputStr) {
            // Prepare the texts by escaping HTML. Since we will send the data as XML, raw HTML codes causes problems.
            $inputStr = esc_html($inputStr);
            $requestXml .= "<string xmlns=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\">$inputStr</string>";
        }

        $requestXml .= "</Texts>".
            "<To>$toLanguage</To>" .
            "</TranslateArrayRequest>";

        return $requestXml;
    }

    /**
     * Creates XML for language name retrieval request
     *
     * @param array $languageCodes Language code or an array of language codes.
     *
     * @return string XML
     * @throws Exception When supplied language codes array is empty.
     */
    public function createLanguageNamesReqXML($languageCodes) {
        //Create the Request XML.
        $requestXml = '<ArrayOfstring xmlns="http://schemas.microsoft.com/2003/10/Serialization/Arrays" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        if(sizeof($languageCodes) > 0){
            foreach($languageCodes as $codes)
                $requestXml .= "<string>$codes</string>";
        } else {
            throw new Exception('$languageCodes array is empty.');
        }
        $requestXml .= '</ArrayOfstring>';
        return $requestXml;
    }

    /**
     * Create and execute the HTTP CURL request.
     *
     * @param string $url        HTTP URL
     * @param string $authHeader Authorization header string
     * @param string $postData   Data to post
     * @return string
     * @throws Exception
     */
    public function curlRequest($url, $authHeader, $postData = '') {
        // Initialize the Curl Session.
        $ch = curl_init();

        // Set the Curl url.
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set the HTTP HEADER Fields.
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($authHeader, "Content-Type: text/xml"));

        // CURLOPT_RETURNTRANSFER - TRUE to return the transfer as a string of the return value of curl_exec().
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // CURLOPT_SSL_VERIFYPEER - Set FALSE to stop cURL from verifying the peer's certificate.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if($postData) {
            // Set HTTP POST Request.
            curl_setopt($ch, CURLOPT_POST, true);
            // Set data to POST in HTTP "POST" Operation.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        // Execute the  cURL session.
        $curlResponse = curl_exec($ch);

        // Get the Error Code returned by Curl.
        $curlErrno = curl_errno($ch);

        if($curlErrno) {
            $curlError = curl_error($ch);
            throw new Exception($curlError);
        }

        // Close a cURL session.
        curl_close($ch);

        return $curlResponse;
    }
}