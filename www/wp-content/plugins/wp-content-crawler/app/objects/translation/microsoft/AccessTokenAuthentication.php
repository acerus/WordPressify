<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 25/07/17
 * Time: 05:58
 */

namespace WPCCrawler\objects\translation\microsoft;

/**
 * Retrieves access token to be used to get authenticated in Microsoft Translator Text API
 *
 * @see https://github.com/MicrosoftTranslator/HTTP-Code-Samples/blob/master/PHP/TranslateMethod.php
 */
class AccessTokenAuthentication {

    /**
     * Get the access token by making a cURL request
     *
     * @param string $azureKey Subscription key for Microsoft Translator Text API.
     * @return string
     */
    function getToken($azureKey) {
        $url = 'https://api.cognitive.microsoft.com/sts/v1.0/issueToken';
        $ch = curl_init();
        $dataString = json_encode('{body}');

        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($dataString),
                'Ocp-Apim-Subscription-Key: ' . $azureKey
            )
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $strResponse = curl_exec($ch);
        curl_close($ch);

        return $strResponse;
    }
}