<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 23/07/17
 * Time: 09:28
 */

namespace WPCCrawler\objects\translation;
use WPCCrawler\Factory;
use WPCCrawler\objects\crawling\data\PostData;
use WPCCrawler\objects\traits\SettingsTrait;

/**
 * Translates a post from a language to another one
 *
 * @package WPCCrawler\objects\translation
 */
class PostTranslator {

    use SettingsTrait;

    /** @var PostData A PostData instance to be translated */
    private $postData;

    /**
     * @param array $settings Post meta of a Site type post. (get_post_meta($siteId))
     * @param PostData $postData {@link postData}
     * @throws \Exception When provided post data is not valid or it is empty.
     */
    public function __construct($settings, $postData) {
        if(!$postData || !is_a($postData, PostData::class))
            throw new \Exception("Provided PostData instance is not valid. You need to pass a valid PostData instance.");

        // Set the settings so that we can reach them later.
        $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());

        $this->postData = $postData;
    }

    /**
     * Translates the post according to the settings
     *
     * @return null|PostData Translated post data or null.
     * @throws \Exception If the translation has failed
     */
    public function translate() {
        // Prepare the texts to be translated, and create a TextTranslator.
        $texts = $this->prepareTextsToTranslate();

        // If there are no texts to translate, no need to proceed.
        if(!$texts) return $this->postData;

        $textTranslator = new TextTranslator($texts);

        // Translate the texts using the specified service.
        $selectedTranslationService = $this->getSetting('_wpcc_selected_translation_service');
        switch($selectedTranslationService) {
            case TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION:
                $googleTranslateProjectId = $this->getSetting('_wpcc_translation_google_translate_project_id');
                $googleTranslateApiKey    = $this->getSetting('_wpcc_translation_google_translate_api_key');
                $googleTranslateFrom      = $this->getSetting('_wpcc_translation_google_translate_from');
                $googleTranslateTo        = $this->getSetting('_wpcc_translation_google_translate_to');

                $translatedTexts = $textTranslator->translateWithGoogle($googleTranslateProjectId, $googleTranslateApiKey,
                        $googleTranslateTo, $googleTranslateFrom);

                break;

            case TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT:
                $microsoftTranslateClientSecret = $this->getSetting('_wpcc_translation_microsoft_translate_client_secret');
                $microsoftTranslateFrom         = $this->getSetting('_wpcc_translation_microsoft_translate_from');
                $microsoftTranslateTo           = $this->getSetting('_wpcc_translation_microsoft_translate_to');

                $translatedTexts = $textTranslator->translateWithMicrosoft($microsoftTranslateClientSecret,
                    $microsoftTranslateTo, $microsoftTranslateFrom);
                break;

            default:
                error_log("WPCC - Selected translation service is not valid: {$selectedTranslationService}");
                return null;
        }

        // If the translation is not successful, throw an error.
        $translationErrors = $textTranslator->getErrors();
        if($translationErrors || sizeof($texts) != sizeof($translatedTexts)) {
            // Get the errors if there are any
            $details = '';
            if($translationErrors) {
                $descriptions = $textTranslator->getErrorDescriptions();
                $details = "\n" . json_encode(["errors" => $translationErrors, "descriptions" => $descriptions]);
            }

            // Throw an exception
            throw new \Exception("WPCC - Texts could not be translated." . $details);
        }

        // Assign translated texts to the PostData instance.
        $this->prepareTranslatedPostData($translatedTexts);

        return $this->postData;
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * Prepares an array of key-value pairs using the texts that need to be translated in {@link postData}
     *
     * @return array An array of key-value pairs created from {@link postData}
     */
    private function prepareTextsToTranslate() {
        $texts = [];

        // Collect the values of the translatable fields
        foreach(PostData::TRANSLATABLE_FIELDS as $fieldName => $translatableKeys) {
            // Create the name of the method. E.g. if the field name is "title", the getter is "getTitle".
            $methodName = "get" . ucfirst($fieldName);

            // If the method does not exist in PostData, continue with the next field name.
            if(!method_exists($this->postData, $methodName)) continue;

            // Get the value of the field
            $fieldValue = $this->postData->$methodName();

            // If there is no valid field value, proceed with the next field name.
            if(!$fieldValue) continue;

            // If there are translatable keys and the value of the field is an array, put only the values of the translatable
            // fields to the $texts array.
            if($translatableKeys && is_array($fieldValue)) {
                // If the targetKeys is not an array, parse it to array.
                if(!is_array($translatableKeys)) $translatableKeys = [$translatableKeys];

                // Check each item of the field value
                foreach($fieldValue as $innerKey => $innerValue) {
                    if(!$innerValue) continue;

                    // The logic below assumes that the depth of the array can be at most 2. Hence, it does not follow
                    // an iterative procedure.

                    // If the inner value is not an array
                    if(!is_array($innerValue)) {
                        // Check if the innerKey is translatable and not numeric
                        if(in_array($innerKey, $translatableKeys) && !is_numeric($innerValue)) {
                            // Create the key and add the innerValue to the texts
                            $key = "{$fieldName}|{$innerKey}";
                            $texts[$key] = $innerValue;
                        }

                    // If the inner value is an array
                    } else {
                        // Check each key of innerValue if it is translatable
                        foreach($translatableKeys as $translatableKey) {
                            if(!isset($innerValue[$translatableKey]) || !($translatableVal = $innerValue[$translatableKey])) continue;

                            // No need to translate a value that is numeric.
                            if(is_numeric($translatableVal)) continue;

                            // Create the key for this item. We use pipes as separators. This is because dot notation will
                            // cause some unwanted behaviors in TextTranslator.
                            $key = "{$fieldName}|{$innerKey}|{$translatableKey}";

                            // Store the translatable value under the prepared key.
                            $texts[$key] = $translatableVal;
                        }
                    }
                }

            } else {
                $texts[$fieldName] = $fieldValue;
            }
        }

        return $texts;
    }

    /**
     * Assigns translated texts to the {@link postData} instance.
     *
     * @param array $translatedTexts An array of texts that were translated.
     */
    private function prepareTranslatedPostData($translatedTexts) {
        $translatableFieldNames = array_keys(PostData::TRANSLATABLE_FIELDS);

        // Assign the translated values to postData.
        foreach($translatedTexts as $key => $translatedValue) {
            $keyParts = explode("|", $key);
            $fieldName = $keyParts[0];

            // If the key is not one of the translatable fields, continue with the next key in the translatedTexts.
            if(!in_array($fieldName, $translatableFieldNames)) continue;

            // Create the name of the method. E.g. if the field name is "title", the setter is "setTitle".
            $ucFirstFieldName = ucfirst($fieldName);
            $getterMethodName = "get" . $ucFirstFieldName;
            $setterMethodName = "set" . $ucFirstFieldName;

            // If the method does not exist in PostData, continue with the next key.
            if(!method_exists($this->postData, $getterMethodName) || !method_exists($this->postData, $setterMethodName)) continue;

            // If there are more than one key parts, it means the field value is an array and its specific items are
            // translatable.
            if(sizeof($keyParts) > 1) {
                $originalValue = $this->postData->$getterMethodName();

                // The original value must be an array.
                if(!is_array($originalValue)) continue;

                // Create the dot-notated key for the translatable item of the originalValue array.
                $translatableInnerDotKey = implode(".", array_slice($keyParts, 1));

                // Assign the translated value in the original value.
                array_set($originalValue, $translatableInnerDotKey, $translatedValue);

                // Set the translated value.
                $this->postData->$setterMethodName($originalValue);

            // Otherwise, we can directly assign the translated value.
            } else {
                // Call the setter with the translated value.
                $this->postData->$setterMethodName($translatedValue);
            }
        }

    }
}