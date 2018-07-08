<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 23/07/17
 * Time: 11:01
 */

namespace WPCCrawler\objects\translation;


use DOMElement;
use DOMNode;
use Google\Cloud\Translate\TranslateClient;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\objects\crawling\DummyBot;
use WPCCrawler\objects\traits\ErrorTrait;
use WPCCrawler\objects\traits\FindAndReplaceTrait;
use WPCCrawler\objects\translation\microsoft\MicrosoftTranslateClient;
use WPCCrawler\Utils;

/**
 * Translates a multidimensional array of texts.
 *
 *        === HOW THIS WORKS ===
 *
 *  1. Flattens given multidimensional text array.
 *  2. Finds texts that are longer than a threshold and divides them into smaller pieces by encapsulating the texts with
 * an HTML element and giving them an ID. The ID is given so that the translated version of the text can be replaced
 * with the original text. In other words, the ID is used to locate a divided text in the source code.
 *  3. Sends flattened and divided texts to a translation API and retrieves translation of the flattened and divided
 * texts.
 *  4. Remaps the flattened texts by assigning text items to their location in the original multidimensional array, by
 * combining divided texts by using their ID along the way.
 *
 * Simply, this class takes a multidimensional array and gives their translated versions.
 *
 * @package WPCCrawler\objects\translation
 */
class TextTranslator {

    use FindAndReplaceTrait;
    use ErrorTrait;

    // TODO: Test huge number of too long texts to see if translation services fail.

    // When translating text by specifying a max length, subtract 300-400 chars from the max possible length. Because,
    // we add IDs and classes to the nodes. E.g. If max allowed value is 10000, write 9600 when calling the translation
    // method.

    /** @var int Maximum length of a string that will be sent to Google Translate API */
    private $googleMaxTextLength = 9600;

    /** @var int Maximum number of texts per batch for Google Translate API */
    private $googleMaxTextCountPerBatch = 2000;

    /** @var int Maximum length of a string that will be sent to Microsoft Translator Text API */
    private $microsoftMaxTextLength = 9600;

    /** @var int Maximum number of texts per batch for Microsoft Translator Text API */
    private $microsoftMaxTextCountPerBatch = 2000;

    /*
     *
     */

    /** @var string Option key used to store previously retrieved supported languages of Google Translate */
    const OPTION_KEY_CACHED_LANGUAGES_GOOGLE_TRANSLATE = "_wpcc_translation_cached_languages_google_translate";

    /** @var string Option key used to store previously retrieved supported languages of Microsoft Translator Text */
    const OPTION_KEY_CACHED_LANGUAGES_MICROSOFT_TRANSLATOR_TEXT = "_wpcc_translation_cached_languages_microsoft_translator_text";

    /*
     *
     */

    /** @var array Texts to be translated. This can be a multi-level array. */
    private $texts;

    /** @var array Stores prepared long texts. Structure: [dotKey => preparedLongText] */
    private $longTextsPrepared = [];

    /** @var DummyBot */
    private $dummyBot;

    /** @var string Format for IDs that will be set to the elements needing to be translated. */
    private $translateNodeIdFormat = "wpcc-translate-%s";

    /** @var string Class name that will be added to the HTML tags that need to be unwrapped after translation. */
    private $classUnwrap = "wpcc-translate-unwrap";

    /** @var string Tag name that will be used to encapsulate divided texts */
    private $translateElementTagName = "wpcc";

    /*
     *
     */

    /** @var string The key that will be used for options in select elements for Google Cloud Translation  */
    const KEY_GOOGLE_CLOUD_TRANSLATION = "google_translate";

    /** @var string The key that will be used for options in select elements for Microsoft Translator Text */
    const KEY_MICROSOFT_TRANSLATOR_TEXT = "microsoft_translator_text";

    /**
     * @param array $texts See {@link $texts}
     */
    public function __construct($texts) {
        $this->texts = $texts;

        $this->dummyBot = new DummyBot([]);
    }

    /**
     * Translate texts from a language to another using Google Cloud Translation API.
     *
     * @param string $projectId ID of the project created on Google Developers Console
     * @param string $apiKey    Public API key of the project
     * @param string $to        The target language to translate to. Must be a valid ISO 639-1 language code.
     * @param string $from      The source language to translate from. Must be a valid ISO 639-1 language code.
     *                          Leave this empty or pass 'detect' to automatically detect the language.
     *
     * @return array Translated texts. The array is structured as:
     *               'source':  ISO 639-1 code of the source language of the raw text
     *               'input':   Raw text
     *               'text':    Translated text
     *               'model':   The model to use for the translation request. May be `nmt` or `base`. Defaults to null.
     *                          Since there is no "model" parameter that can be passed to the function, this will always
     *                          be null.
     */
    public function translateWithGoogle($projectId, $apiKey, $to, $from = '') {
        if($from == 'detect') $from = '';

        // Translate it
        $translate = new TranslateClient([
            'projectId' => $projectId,
            'key'       => $apiKey,
        ]);

        // Clear the errors
        $this->clearErrors();

        $translations = $this->translate(function($chunk) use (&$translate, &$from, &$to) {

            try {
                $translations = $translate->translateBatch($chunk, [
                    'source' => $from,
                    'target' => $to,
                ]);

                return array_column($translations, 'text');

            // Catch the errors and add them to the other errors.
            } catch(\Exception $e) {
                $message = $e->getMessage();
                $response = json_decode($message, true);
                $this->addError(get_class($e), Utils::array_get($response, "error.message", ''), false);
                return [];
            }

        }, $this->googleMaxTextLength, $this->googleMaxTextCountPerBatch);

        return $translations;
    }

    /**
     * Translate texts from a language to another using Microsoft Translator API.
     *
     * @param string $clientSecret Client secret obtained from Microsoft Translator Text API
     * @param string $to           The target language to translate to. Must be a valid ISO 639-1 language code.
     * @param string $from         The source language to translate from. Must be a valid ISO 639-1 language code.
     *                             Leave this empty or pass 'detect' to automatically detect the language.
     * @return array Translations. An array of strings.
     */
    public function translateWithMicrosoft($clientSecret, $to, $from = '') {
        if($from == 'detect') $from = '';

        // Translate it
        $translate = new MicrosoftTranslateClient($clientSecret);

        // Clear the errors
        $this->clearErrors();

        $translations = $this->translate(function($chunk) use (&$translate, &$from, &$to) {

            try {
                return $translate->translateBatch($chunk, $from, $to);

            // Catch the errors and add them to the other errors.
            } catch(\Exception $e) {
                $message = $e->getMessage();
                $this->addError(get_class($e), $message, false);
                return [];
            }

        }, $this->microsoftMaxTextLength, $this->microsoftMaxTextCountPerBatch);

        return $translations;
    }

    /**
     * A helper method to be used to translate {@link $texts}.
     *
     * @param callable $callback             Callback that translates an array of strings. Takes only 1 parameter,
     *                                       which is $chunk, an array of strings. Returns translated $chunk. Number of
     *                                       items and their order in the returned value must be the same as the $chunk.
     *                                       <b>func(array $chunk) { return $translatedChunk; }</b>
     * @param int      $maxTextLength        Maximum length of a string in a chunk. If the length of a provided string
     *                                       exceeds the limit, it will be divided into pieces preserving the integrity
     *                                       of HTML.
     * @param int      $maxTextCountPerBatch Maximum number of texts in a chunk.
     *
     * @return array Translated texts.
     * @throws \Exception
     */
    private function translate($callback, $maxTextLength, $maxTextCountPerBatch) {
        // Prepare the texts
        $flattened          = $this->flattenTexts($this->texts, $maxTextLength);
        $flattenedPrepared  = $this->getTextsToTranslateFromFlattened($flattened);
        $chunks             = array_chunk($flattenedPrepared, $maxTextCountPerBatch);

        $allTranslations = [];

        // Translate each chunk and store the translations in an array.
        foreach($chunks as $chunk) {
            $translations = call_user_func($callback, $chunk);
            $allTranslations = array_merge($allTranslations, array_values($translations));
        }

        // Prepare the translations
        $allTranslations = $this->remapTranslatedTexts($allTranslations, $flattened);

        return $allTranslations;
    }

    /*
     * HELPERS
     */

    /**
     * Flattens a multidimensional texts array. Divides the texts into several pieces if they are longer than the
     * maximum allowed length.
     *
     * @param array      $texts               An array. It can have inner arrays.
     * @param int        $maxValueLength      Max length that an item of the flattened array can have. If this is
     *                                        greater than zero, the value will be separated from end of sentences to
     *                                        satisfy the limit as much as possible.
     * @param null|array $flattened           No need to pass a value to this. Used in recursion.
     * @param null|array $parentDotNotatedKey No need to pass a value to this. Used in recursion.
     * @param int        $depth               No need to pass a value to this. Used in recursion.
     * @return array
     */
    public function flattenTexts($texts, $maxValueLength = 0, $flattened = null, $parentDotNotatedKey = null, $depth = 0) {
        if(!$flattened) $flattened = [];

        foreach($texts as $key => $value) {
            // Prepare the dot key.
            $dotKey = $parentDotNotatedKey ? ($parentDotNotatedKey . "." . $key) : $key;

            // If value is an array, recursively repeat the operation.
            if(is_array($value)) {
                $flattened = $this->flattenTexts($value, $maxValueLength, $flattened, $dotKey, $depth + 1);

            // If we finally reached a non-array value, we can add it to the flattened array.
            } else {
                $length = mb_strlen($value);

                // If there is no need to divide the value into several substrings, we can directly add it.
                if($maxValueLength < 1 || $length <= $maxValueLength) {
                    $flattened[] = [
                        "key"       => $dotKey,
                        "value"     => $value,
                        "length"    => $length,
                    ];

                // Otherwise, let's divide the value into small pieces.
                } else {
                    // Since we want to translate the texts, it is important that we divide the text into small pieces
                    // from the end of the paragraphs or the sentences for a better translation. After separation,
                    // add all the substrings to the flattened array.
                    //
                    // * NOTE THAT * the HTML must be valid after division.
                    //  . We can maybe create a Crawler for this text, find the nodes that contain texts, assign them a
                    // unique ID, and then add each of them to the flattened array one by one, with their unique element
                    // ID. After that, after the translation, we can replace the text nodes with the translated HTML and
                    // remove the IDs.

                    // Create a dummy Crawler
                    $dummyCrawler = $this->dummyBot->createDummyCrawler($value);

                    // Prepare the crawler for translation. Here, the elements that need to be translated are marked with
                    // IDs and classes. This marking is done by considering the maximum length constraint.
                    $nextId = 0;
                    $dummyCrawler->filter("body > div")->each(function($node) use (&$maxValueLength, &$nextId) {
                        /** @var Crawler $node */
                        $this->prepareNodeForTranslation($node->getNode(0), $maxValueLength, $nextId);
                    });

//                    $text = $this->dummyBot->getContentFromDummyCrawler($dummyCrawler);

                    // Find the elements that need to be translated and add them to the flattened array with their IDs.
                    $count = 0;
                    $dummyCrawler->filter(sprintf("[id*=%s]", sprintf($this->translateNodeIdFormat, '')))->each(function($node) use (&$count, &$flattened, &$dotKey) {
                        /** @var Crawler $node */
                        /** @var DOMElement $element */
                        $element = $node->getNode(0);

                        $id = $element->getAttribute("id");
                        $html = Utils::getNodeHTML($node);
                        $length = mb_strlen($html);

                        $flattened[] = [
                            "key"            => $dotKey . "." . $count,
                            "value"          => $html,
                            "length"         => $length,
                            "element_id"     => $id,
                            "parent_dot_key" => $dotKey,
                        ];
                        $count++;
                    });

                    // Store the prepared long text.
                    $text = $this->dummyBot->getContentFromDummyCrawler($dummyCrawler);

                    $this->longTextsPrepared[$dotKey] = $text;
                }
            }

        }

        return $flattened;
    }

    /**
     * Extract to-be-translated texts from the flattened array
     *
     * @param array $flattened A flattened text array retrieved from {@link flattenTexts}
     * @return array An array of texts
     */
    public function getTextsToTranslateFromFlattened($flattened) {
        return array_column($flattened, "value");
    }

    /**
     * @param array $translatedTexts An array of texts, which stores translated values of the items in the flattened
     *                               array.
     * @param array $flattened       A flattened text array retrieved from {@link flattenTexts}
     * @param int   $startIndex      Index of flattened array item that corresponds to the 0th item of $translatedTexts
     * @return array
     * @throws \Exception When $startIndex is not valid.
     */
    public function remapTranslatedTexts($translatedTexts, $flattened, $startIndex = 0) {
        // Get translated flattened array
        $translatedFlattened = $flattened;
        for($i = $startIndex; $i < sizeof($translatedTexts); $i++) {
            if(!isset($flattened[$i])) throw new \Exception("Item with start index {$i} does not exist in flattened array.");

            $translatedFlattened[$i]["value"] = $translatedTexts[$i];
        }

        $texts = $this->expandFlattenedTexts($translatedFlattened);

        return $texts;
    }

    /**
     * Expands a flattened texts array.
     *
     * @param array $flattened     A flattened text array retrieved from {@link flattenTexts}
     * @return array Expanded array
     */
    public function expandFlattenedTexts($flattened) {
        // Recreate the original text array by using the dot-notation keys in flattened array
        $texts = [];
        $combinedContent = null;

        /** @var array $crawlers Stores crawlers for long texts. Structure: [string dot_key => Crawler crawler] */
        $crawlers = [];

        for($i = 0; $i < sizeof($flattened); $i++) {
            $item = $flattened[$i];

            $dotKey         = $item["key"];
            $value          = $item["value"];
            $parentDotKey   = Utils::array_get($item, "parent_dot_key", null);
            $elementId      = Utils::array_get($item, "element_id");

            if($parentDotKey && $elementId) {
                if(!isset($crawlers[$parentDotKey])) {
                    $crawlers[$parentDotKey] = $this->dummyBot->createDummyCrawler(Utils::array_get($this->longTextsPrepared, $parentDotKey));
                }

                /** @var Crawler $crawler */
                $crawler = $crawlers[$parentDotKey];
                $this->dummyBot->findAndReplaceInElementHTML($crawler, ['[id="'. $elementId . '"]'], ".*?", $value, true);

            } else {
                array_set($texts, $dotKey, $value);
            }

        }

        // If there are crawlers, get their content and assign to the related dot key.
        if($crawlers) {
            $idSelector                 = "[id*=" . sprintf($this->translateNodeIdFormat, '') . "]";
            $translateIdRegex           = sprintf($this->translateNodeIdFormat, "[0-9]+");
            $emptyIdRegex               = '(\sid=?(?:\'\'|""))|(\sid)[>\s]';
            $unwrapSelector             = "." . $this->classUnwrap;
            $unwrapFindRegex            = sprintf('^<([^\s>]+).*?class=["\']%1$s["\'].*?>((?:.|\n)*)?<\/\1>$', $this->classUnwrap);
            $unwrapReplaceRegex         = "$2";
            $findUnnecessaryNewLines    = sprintf('(<%1$s.*?>|<\/%1$s>)\n', $this->translateElementTagName);

            foreach($crawlers as $dotKey => $crawler) {
                // Remove added IDs
                $this->dummyBot->findAndReplaceInElementAttribute($crawler, [$idSelector], 'id', $translateIdRegex, '', true);

                // Remove empty ID attributes
                $this->dummyBot->findAndReplaceInElementHTML($crawler, ["[id]"], $emptyIdRegex, "", true);

                // Remove unnecessary new-lines
                $content = $this->dummyBot->getContentFromDummyCrawler($crawler);
                $content = $this->findAndReplaceSingle($findUnnecessaryNewLines, "$1", $content, true, false);
                $crawler = $this->dummyBot->createDummyCrawler($content);

                // Find elements to be unwrapped from their encapsulating tags and create an array storing what to find
                // and with what to replace to unwrap the surrounding tags. We are doing the replacement operation twice.
                // This is because we cannot directly remove surrounding tags. Hence, we need to do it in the raw text
                // instead of in Crawler. So, we find the elements to be unwrapped, get their HTML, remove the surrounding
                // tags, and after this, use this information to get rid of surrounding tags in the raw text of source HTML.
                $findAndReplaces = [];
                $crawler->filter($unwrapSelector)->each(function($node) use (&$findAndReplaces, &$unwrapFindRegex, &$unwrapReplaceRegex) {
                    /** @var Crawler $node */
                    $html = Utils::getNodeHTML($node);
                    $replaced = $this->findAndReplaceSingle($unwrapFindRegex, $unwrapReplaceRegex, $html, true, false);
                    $findAndReplaces[] = [
                        "find"    => $html,
                        "replace" => $replaced
                    ];
                });

                // Get the prepared content
                $content = $this->dummyBot->getContentFromDummyCrawler($crawler);

                // Remove the surrounding tags.
                $content = $this->findAndReplace($findAndReplaces, $content, false);

                // Assign the content to the related key
                array_set($texts, $dotKey, trim($content));
            }
        }

        return $texts;
    }

    /**
     * Prepares the node for text translation. The preparation is done by adding IDs and classes to the elements that
     * need to be translated.
     *
     * @param DOMNode $node
     * @param int     $maxTextLength          Maximum length a text can have.
     * @param int     $nextTranslationId      ID of the next to-be-translated element. Pass a variable for this
     *                                        parameter so that the count can be tracked.
     */
    public function prepareNodeForTranslation($node, $maxTextLength = 0, &$nextTranslationId = 0) {
        // Get HTML of the node and find its length.
        $html = $node->ownerDocument->saveHTML($node);
        $length = mb_strlen($html);
        $lengthTrimmed = mb_strlen(trim($html));

        // If this node has a sibling, process it.
        if($node->nextSibling) $this->prepareNodeForTranslation($node->nextSibling, $maxTextLength, $nextTranslationId);

        // No need to proceed further if this is an empty element. No need to check its children as well.
        if($lengthTrimmed < 1) return;

        // Do not proceed further if this is a comment node. No need to check its children as well.
        if($node->nodeName == '#comment') return;

        $isLong = $length > $maxTextLength;

        // If this is a text node, divide it and wrap each part with p tag so that we can find the parts of the text
        // node after translation.
        if($node->nodeName == '#text') {
            // Divide the text so that length of each part is less than $maxTextLength. Wrap each part with
            // <p id="wpcc-translate-[number]" class="wpcc-translate-unwrap">

            $offsets = [];

            // If this is a long text, divide it.
            if($isLong) {
                $tryCount = 0;
                while(!$offsets) {

                    switch($tryCount) {
                        // 1. Check for new-lines:  \n
                        case 0:
                            preg_match_all('/\n/', $html, $matches, PREG_OFFSET_CAPTURE);
                            break;

                        // 2. Check for ..., !, ?, ., :, ", ', ], ), } etc:     \.{2,}|[.?!:][]\"')}]*
                        case 1:
                            preg_match_all('/\.{2,}|[.?!:][]\"\')}]*/', $html, $matches, PREG_OFFSET_CAPTURE);
                            break;

                        // 3. We could not find a good division location. Just divide the text from locations that satisfy
                        // the max length constraint. This is bad for translation. However, it is better than no translation
                        // at all.
                        case 2:
                            $offsets[] = $maxTextLength - 1;
                            while(true) {
                                $newOffset = $offsets[sizeof($offsets) - 1] + $maxTextLength - 1;
                                $offsets[] = $newOffset;

                                if($newOffset > $length - 1) break;
                            };

                            break 2;

                        // Get ouf of the while loop.
                        default:
                            break 2;
                    }

                    if(isset($matches) && $matches && $matches = $matches[0]) {
                        $offsets = $this->findClosestOffsetFromMatches($matches, $maxTextLength);
                    }

                    // Invalidate the matches.
                    $matches = [];

                    // Increase the try count.
                    $tryCount++;
                }

            // Otherwise, no need to divide. Just add the maximum offset so that the text wont't be divided.
            } else {
                $offsets[] = $length - 1;
            }

            // If there are offsets that we can use to divide the text, let's divide it.
            if($offsets) {
                // Add 0 to the beginning of $offsets.
                array_unshift($offsets, 0);

                // Make sure the last offset is not greater than the max offset there can be.
                if($offsets[sizeof($offsets) - 1] > $length - 1) $offsets[sizeof($offsets) - 1] = $length - 1;

                // Divide the text using the offsets.
                $modifiedText = '';
                for($i = 0; $i < sizeof($offsets) - 1; $i++) {
                    $startOffset = $offsets[$i];
                    $endOffset = $offsets[$i + 1];

                    // Increase start offset by 1 if this is not the first offset. By this way, we avoid using the
                    // same offset for the next item in the loop. This is important because we do not want to duplicate
                    // end-of-sentence chars. E.g. if there is a space at an offset and the offset is used as the end
                    // offset for this one and as the start offset for the next one, the space char is duplicated. To
                    // avoid this, we do not use the same offset twice.
                    if($i > 0 && $startOffset + 1 < $endOffset) $startOffset += 1;

                    $text = mb_substr($html, $startOffset, $endOffset - $startOffset + 1);
                    if(!$text || !trim($text)) continue;

                    $text = sprintf('<%4$s id="%1$s" class="%2$s">%3$s</%4$s>', sprintf($this->translateNodeIdFormat,
                        $nextTranslationId), $this->classUnwrap, $text, $this->translateElementTagName);
                    $nextTranslationId++;

                    $modifiedText .= $text;
                }

                // No need to proceed if modified text is empty.
                if(!$modifiedText) return;

                // Replace the node's text with the modified version.
                // We cannot just change the nodeValue, because it strips HTML tags. To be able to successfully change it,
                // first, create a document fragment. Then, append the newValue to the fragment. Finally, replace the
                // node with the fragment.
                $fragment = $node->ownerDocument->createDocumentFragment();

                // Suppress warnings so that the script keeps running.
                // There may be problems regarding a few characters, such as &amp;, when parsing XML. So, handle the
                // errors to keep the script running.
                if (@$fragment->appendXML($modifiedText)) {
                    $node->parentNode->replaceChild($fragment, $node);

                } else {
                    // Write an error to the error log file.
                    error_log("WPCC - XML is not valid for '" . mb_substr($modifiedText, 0, 250) . "'");
                }

            }

            // We are done with this node.
            return;
        }

        // If this non-text element is not long, just add an ID to it. Make sure this is a DOMElement, because attribute
        // getters and setters are only available for DOMElement.
        if(!$isLong && is_a($node, DOMElement::class)) {
            /** @var DOMElement $node */

            $prevId = $node->getAttribute("id");
            $idToAdd = sprintf($this->translateNodeIdFormat, $nextTranslationId);
            $newId = $prevId ? ($prevId . " " . $idToAdd) : $idToAdd;

            $node->setAttribute("id", $newId);

            // Increase next ID by one.
            $nextTranslationId++;

            // We are done with this node.
            return;
        }

        // If it is long and has children, process them as well.
        if($isLong && $node->hasChildNodes()) {
            $this->prepareNodeForTranslation($node->childNodes->item(0), $maxTextLength, $nextTranslationId);
        }

    }

    /**
     * Finds offsets that can be used to divide a text so that a max-length constraint is satisfied.
     *
     * @param array $matches   An array of arrays. Each inner array must have 2 values. Index 0 stores the matched
     *                         text, and index 1 stores the offset of the matched text.
     * @param int $maxLength   Maximum length of text between two offsets.
     * @return \int[] The offsets
     */
    public function findClosestOffsetFromMatches($matches, $maxLength) {
        $offsets = [];
        $minOffset = 0;
        $maxOffset = $maxLength - 1;

        $closestOffset = 0;
        for($i = 0; $i < sizeof($matches); $i++) {
            $match = $matches[$i];

            // $match[1] is the offset.
            $currentOffset = $match[1];

            // If current offset satisfies the constraints, assign it as the closest offset.
            if($currentOffset < $maxOffset && $currentOffset >= $minOffset) {
                // The matches are ordered by their offset in ascending. So, it is safe to assign the found offset as
                // the closest offset.
                $closestOffset = $currentOffset + mb_strlen($match[0]);

            // Otherwise, we have found the best possible offset that satisfies the max length constraint. So, let's
            // add this offset to the result. Then, continue looking for another offset.
            } else if($currentOffset > $maxOffset && $closestOffset > 0) {
                $offsets[] = $closestOffset;

                $minOffset = $closestOffset + 1;
                $maxOffset = $minOffset + $maxLength - 1;

                // Invalidate the closest offset and continue looking for the next closest offset from the previous
                // match. Because, the previous match might be valid for current constraints.
                $closestOffset = 0;
                $i--;
            }
        }

        // Add the last found closest offset if it is not already added.
        if($closestOffset > 0 && $offsets && $offsets[sizeof($offsets) - 1] != $closestOffset) {
            $offsets[] = $closestOffset;
        }

        return $offsets;
    }

    /*
     * STATIC HELPERS
     */

    /**
     * Get the options that can be used in a select element that the user can select which translation service he/she
     * wants to use.
     *
     * @return array
     */
    public static function getTranslationServiceOptionsForSelect() {
        return [
            TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION  => _wpcc('Google Cloud Translation API'),
            TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT => _wpcc('Microsoft Translator Text API'),
        ];
    }

    /**
     * Get supported languages for translation APIs.
     *
     * @param array $data                The structure is: [
     *                                      TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION  => ["project_id" => '', "api_key" => ''],
     *                                      TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT => ["client_secret" => '']
     *                                   ]
     * @param bool  $fromCacheIfPossible True if you want to get the cached results if they exist. False if you want to
     *                                   get the results by making requests no matter what.
     *
     * @return array The structure is: [
     *                                   TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION  => ["code1" => "Lang 1", "code2" => "Lang 2", ...],
     *                                   TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT => ["code1" => "Lang 1", "code2" => "Lang 2", ...]
     *                                   ]
     */
    public static function getSupportedLanguages($data = [], $fromCacheIfPossible = true) {
        $googleTranslateProjectId       = Utils::array_get($data, TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION . ".project_id");
        $googleTranslateApiKey          = Utils::array_get($data, TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION . ".api_key");
        $microsoftTranslateClientSecret = Utils::array_get($data, TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT . ".client_secret");

        // Initialize the results
        $results = [
            TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION    => [],
            TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT   => [],
            "errors"                                        => [],
        ];

        // Get cached values if they are requested
        $googleTranslateCache           = $fromCacheIfPossible ? get_option(static::OPTION_KEY_CACHED_LANGUAGES_GOOGLE_TRANSLATE, [])           : [];
        $microsoftTranslatorTextCache   = $fromCacheIfPossible ? get_option(static::OPTION_KEY_CACHED_LANGUAGES_MICROSOFT_TRANSLATOR_TEXT, [])  : [];

        /**
         * Prepares languages array as key-value pairs.
         *
         * @param array $languages [0 => ["code" => "langCode1", "name" => "Lang Name 1"], 1 => ["code" => "langCode2", "name" => "Lang Name 2"], ...]
         * @return array ["langCode1" => "Lang Name 1", "langCode2" => "Lang Name 2", ...]
         */
        $prepareLanguages = function($languages) {
            $prepared = [];
            foreach($languages as $lang) $prepared[$lang["code"]] = $lang["name"];

            return $prepared;
        };

        /*
         * GOOGLE TRANSLATE
         */

        if($googleTranslateCache) {
            $results[TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION] = $googleTranslateCache;

        } elseif($googleTranslateProjectId && $googleTranslateApiKey) {
            $translate = new TranslateClient([
                'projectId' => $googleTranslateProjectId,
                'key'       => $googleTranslateApiKey,
            ]);

            try {
                $languages = call_user_func($prepareLanguages, $translate->localizedLanguages(["target" => "en"]));

            } catch(\Exception $e) {
                $languages = [];
                $error = json_decode($e->getMessage(), true);
                $results["errors"][] = sprintf("%s (%s - %s)",
                    Utils::array_get($error, "error.message"),
                    Utils::array_get($error, "error.status"),
                    Utils::array_get($error, "error.code")
                );
            }

            $results[TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION] = $languages;

            // Update the cache
            update_option(static::OPTION_KEY_CACHED_LANGUAGES_GOOGLE_TRANSLATE, $languages, false);
        }

        /*
         * MICROSOFT TRANSLATOR TEXT
         */

        if($microsoftTranslatorTextCache) {
            $results[TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT] = $microsoftTranslatorTextCache;

        } else if($microsoftTranslateClientSecret) {
            $translate = new MicrosoftTranslateClient($microsoftTranslateClientSecret);

            try {
                $languages = call_user_func($prepareLanguages, $translate->localizedLanguages(["target" => "en"]));

            } catch(\Exception $e) {
                $languages = [];
                $results["errors"][] = $e->getMessage();
            }

            $results[TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT] = $languages;

            // Update the cache
            update_option(static::OPTION_KEY_CACHED_LANGUAGES_MICROSOFT_TRANSLATOR_TEXT, $languages, false);
        }

        return $results;
    }

    /**
     * Prepares "from" languages for select by prepending a "detect language" item.
     *
     * @param array $languages
     * @return array
     */
    public static function prepareFromLanguagesForSelect($languages) {
        $detectLanguageItem = ['detect' => _wpcc("Detect language")];
        return $languages ? $detectLanguageItem + $languages : $languages;
    }

    public static function test() {
        /* TEXT TRANSLATOR TEST */

        $longText =
            "<div class=\"news-box\">
               <p>Meksika açıklarında 8.1 büyüklüğünde bir deprem meydana geldi. Depremin ardından Orta Amerika kıyıları için tsunami alarmı verildi. Meksika İçişleri Bakanı depremde en az 5 kişinin hayatını kaybettiğini bildirdi. Ölenlerin ikisinin Chiapas eyaletinden olduğu açıklandı.</p>
               <p>Guatemala devlet başkanı da bir vatandaşının depremde hayatını kaybettiğini söyledi.</p>
               <p>ABD Jeolojik Araştırma Merkezi (USGS) önce 8 olarak açıkladığı depremin büyüklüğünü 8,1 olarak güncelledi. <br> <br>USGS, depremin kaydedildiği derinliği de 69,7 kilometre olarak düzeltti. Derinlik daha önce 33 kilometre olarak duyurulmuştu.</p>
               <p><strong>8 ÜLKE İÇİN TSUNAMİ ALARMI</strong></p>
               <p>Pasifik Tsunami Uyarı Merkezi,, Mekiska Guatemala, Panama, El Salvador, Costa Rica, Nikaragua, Honduras ve Ekvador ülkelerini tsunamiye karşı hazırlıklı olma çağrısında bulundu.</p>
               <p>Başkent sakinleri, panik içinde evlerini terk ederek sokağa döküldü.  </p>
               <!-- /68858259/hurriyet/dunya/body_300x250_1 -->
               <div class=\"adv-main-container collapsed-adv-area\">
                  <div class=\"adv-wrapper\">
                     <div id=\"div-gpt-ad-1468850025565-0\" class=\"dfp-lazy-item ad-title-container\" data-ad-offset=\"20\" data-ad-div-id=\"div-gpt-ad-1468850025565-0\" data-slot-control-value=\"1\" data-inline-ad=\"1\" data-check-responsive=\"0\" data-device-type=\"\">
                        <script type=\"text/javascript\">googletag.cmd.push(function () {googletag.defineSlot('/68858259/hurriyet/dunya/body_300x250_1', [300, 250], 'div-gpt-ad-1468850025565-0').addService(googletag.pubads());googletag.enableServices();});</script>
                     </div>
                  </div>
               </div>
               <p><strong>1985'TEN SONRAKİ EN BÜYÜK DEPREM</strong></p>
               <p>Yaşanan deprem, ülkeyi vuran en büyük deprem olarak da bilinen ve binlerce kişinin ölümüne sebep olan 1985’teki depremden sonra en büyük sarsıntı olduğu açıklandı. Reuters haber ajansına konuşan 31 yaşındaki mimar Carlos Briceno, “Başta gülüp geçtim ama depremin bitmemesi ve ışıkların sönmesiyle ne yapacağımı şaşırdım. Neredeyse düşüyordum” dedi.</p>
               <p>Meksika İçişleri Bakanı ilk gelen bilgilere göre depremde en az 5 kişinin öldüğünü bildirdi.</p>
               <p><strong>ARTÇI SARSINTILAR GERÇEKLEŞTİ</strong></p>
               <p>Büyük depremin merkez üssü Pijijiapan kentinin 123 kilometre uzağı. USGS verilerine göre, depremden sonra birçok artçı sarsıntı da gerçekleşti. Peş peşe meydana gelen artçı depremlerin büyüklükleri 5.7, 5.4, 5.2 ve 4.9.</p>
               <p><span style=\"color: #ff0000;\"><strong>DÜNYANIN EKSENİ KAYDI</strong></span></p>
               <p>11 Mart 2011'de Japonya’yı sarsan 8.9 büyüklüğündeki deprem Dünya’nın dönüş hızını artırmış, günler kısalmıştı. O dönem NASA’dan jeofizikçi Richard Gross 8,9’luk depremin Dünya’nın dönüş hızını artırarak 24 saatlik bir günü 1.8 mikro saniye (saniyenin milyonda biri kadar) kısalttığını belirtmişti. ABD Jeolojik Araştırma Kurumu’ndan Kenneth Hudnut da depremin Japonya’daki ana adada 2,5 metre kadar kayma yarattığını ayrıca Dünya’nın ekseninde 17 santimetre kayma yaşandığı açıklamıştı. Bilim insanlarına göre bir deprem sırasında kütle kayması Ekvator’a ne kadar yakın gerçekleşirse bu durum Dünya’nın dönüş hızını o kadar artırıyor. Dünyada bir gün 24 saat, yani 86.400 saniye sürüyor. Yıl boyunca mevsimsel değişikliklerden ötürü 1 milisaniye (1000 mikro saniye) değişiklik yaşanabiliyor. Uzmanlar, depremin kaya kütlelerini kaydırarak kütle dağılımını değiştirmesi sonucu Dünya’nın ekseninde oluşan kaymayı şöyle açıklıyor: “Bu kayma Dünya’nın dönüşünü birazcık sarsıntılı biçimde etkilese de gezegenimizin uzaydaki eksenine herhangi bir etkisi olmamıştır. Böyle bir değişim ancak Güneşin, Ayın ve gezegenlerin çekim kuvveti gibi dış etkiler sonucunda oluşabilir.” </p>
               <p><img src=\"//i.hurimg.com/i/hurriyet/75/770x0/59b23e0867b0a9b1f4ae80f0\" data-src=\"//i.hurimg.com/i/hurriyet/75/770x0/59b23e0867b0a9b1f4ae80f0\" alt=\"Son dakika: Meksikada 8.1lik depremin ardından alarm verildi\" class=\"lazy-loaded\" data-inline-image=\"true\"></p>
               <p>Başkent Meksiko'da da hissedilen deprem, halk arasında korkuya neden oldu. Başkent sakinleri, panik içinde evlerini terk ederek sokağa döküldü.</p>
               <!-- /68858259/hurriyet/dunya/body_300x250_2 -->
               <div class=\"adv-main-container collapsed-adv-area\">
                  <div class=\"adv-wrapper\">
                     <div id=\"div-gpt-ad-1468850025565-12\" class=\"dfp-lazy-item ad-title-container\" data-ad-offset=\"20\" data-ad-div-id=\"div-gpt-ad-1468850025565-12\" data-slot-control-value=\"1\" data-inline-ad=\"1\" data-check-responsive=\"0\" data-device-type=\"\">
                        <script type=\"text/javascript\">googletag.cmd.push(function () {googletag.defineSlot('/68858259/hurriyet/dunya/body_300x250_2', [300, 250], 'div-gpt-ad-1468850025565-12').addService(googletag.pubads());googletag.enableServices();});</script>
                     </div>
                  </div>
               </div>
               <p>Reuters haber ajansına konuşan görgü tanıkları bölgede büyük bir korku ve panik yaşandığını duyururken, depremin merkez üssünün Pijijiapan kentinin 123 kilometre güneybatısı olduğu belirtildi.</p>
               <twitterwidget class=\"twitter-tweet twitter-tweet-rendered\" id=\"twitter-widget-0\" style=\"position: static; visibility: visible; display: block; transform: rotate(0deg); max-width: 100%; width: 500px; min-width: 220px; margin-top: 10px; margin-bottom: 10px;\" data-tweet-id=\"906043557286424576\"></twitterwidget>
               <p><br><strong>ABD DE KASIRGAYA HAZIRLANIYOR</strong></p>
               <p>Meksika ve komşu ülkeler depremin şokunu atlatmaya çalışırken ABD’de de Irma Kasırgası’na hazırlanıyor. Karayipler’de birçok adayı neredeyse yıkıp geçen ve en az 7 kişinin hayatını kaybettiği Irma Kasırgası’nın etkilerini minimuma indirmek için ABD’de önlem alınmaya başlandı. Florida’da birçok markette raflar boşalırken, stok yapan Floridalılar, suyun kesileceğinin açıklanmasının ardından şişe sulara adeta hücum etti.</p>
               <p><strong><img src=\"//i.hurimg.com/i/hurriyet/75/770x0/57bbf91967b0a930747c6900.jpg\" data-src=\"//i.hurimg.com/i/hurriyet/75/770x0/59b22a8c7af5073348bc5305\" alt=\"Son dakika: Meksikada 8.1lik depremin ardından alarm verildi\" class=\"\" data-inline-image=\"true\"></strong><em>. </em><strong><img src=\"//i.hurimg.com/i/hurriyet/75/350x622/57bbf91967b0a930747c6900.jpg\" data-src=\"//i.hurimg.com/i/hurriyet/75/350x622/59b2411e67b0a9b1f4ae8169\" alt=\"Son dakika: Meksikada 8.1lik depremin ardından alarm verildi\" class=\"\" data-inline-image=\"true\"></strong></p>
               <p><em>Chiapas Eyaleti Valisi Manuel Velasco, yaklaşık 1 dakika süren deprem sonucu San Cristobal'da 3 kişinin yaşamını yitirdiğini açıkladı. </em><em>Velasco, Chiapas'da deprem nedeniyle hastane binaları, evler ve okullarda çatlaklar oluştuğunu ve San Cristobal'da da bir alışveriş merkezinin ağır hasar gördüğünü söyledi.<br></em><strong><br><img src=\"//i.hurimg.com/i/hurriyet/75/770x0/57bbf91967b0a930747c6900.jpg\" data-src=\"//i.hurimg.com/i/hurriyet/75/770x0/59b2524a7af5073348bc5775\" alt=\"Son dakika: Meksikada 8.1lik depremin ardından alarm verildi\" class=\"\" data-inline-image=\"true\"></strong></p>
               <p><em> Velasco, Chiapas'da deprem nedeniyle hastane binaları, evler ve okullarda çatlaklar oluştuğunu ve San Cristobal'da da bir alışveriş merkezinin ağır hasar gördüğünü söyledi.<br></em><strong><br><img src=\"//i.hurimg.com/i/hurriyet/75/770x0/57bbf91967b0a930747c6900.jpg\" data-src=\"//i.hurimg.com/i/hurriyet/75/770x0/59b2524a7af5073348bc5773\" alt=\"Son dakika: Meksikada 8.1lik depremin ardından alarm verildi\" class=\"\" data-inline-image=\"true\"><br></strong><em>Bir gece kulubünde yaşanan panik objektiflere böyle yansıdı. </em><strong><br></strong></p>
               <p><strong><img src=\"//i.hurimg.com/i/hurriyet/75/770x0/57bbf91967b0a930747c6900.jpg\" data-src=\"//i.hurimg.com/i/hurriyet/75/770x0/59b240fd67b0a9b1f4ae8167\" alt=\"Son dakika: Meksikada 8.1lik depremin ardından alarm verildi\" class=\"\" data-inline-image=\"true\"><br></strong></p>
            </div>";

//        $longText = "<div>Lorem ipsum dolor sit amet, <a href='#'>consectetur adipiscing</a> elit. Aliquam <b>congue</b> odio non elit placerat commodo. Mauris gravida nibh nec eros dapibus tristique. Duis semper mauris nec vulputate placerat. Donec ut velit at nisl vehicula lobortis. Integer imperdiet magna justo, non placerat risus tempor quis. Donec venenatis sapien ante, non interdum eros rhoncus ut. Duis rhoncus erat placerat metus suscipit ultrices. Cras a facilisis nisl. Quisque pretium libero sem, sit amet varius neque tempus a. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</div>
//
//Mauris tempor <a href='#'>ultrices</a> sapien <b>volutpat</b> fermentum. Integer vel malesuada eros. Cras et placerat mauris, eu ornare risus. In hac habitasse platea dictumst. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque ullamcorper turpis ligula, sit amet commodo odio feugiat eu. Vestibulum blandit justo a ligula lobortis consequat sed ac enim. Etiam lacinia pulvinar eros, ac malesuada nulla tristique at.
//
//<blockquote>Mauris maximus nisi non velit pulvinar, auctor sollicitudin enim sollicitudin.</blockquote> Nullam eget lectus eu sapien auctor dictum. Duis sed dui vitae quam hendrerit pulvinar. Maecenas maximus ultrices mi, consequat tristique risus posuere in. Vestibulum finibus nisl ut varius vestibulum. Sed gravida fermentum dui vitae porta. Duis bibendum at lacus non tincidunt. In convallis ipsum est. Proin quis dolor commodo, interdum tellus id, varius justo. Morbi sit amet nunc eget ante rutrum vehicula quis ultrices est. Morbi hendrerit erat et cursus dictum. Integer sem est, aliquam tempor venenatis ac, mattis eu purus. Etiam a fermentum elit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.";

        var_dump("Long Text");
        echo "<textarea style='width: 100%; height:300px;'>" . $longText . "</textarea>";

        $texts = [
            "item_1" => "Merhaba, dünya!",
            "item_2" => [
                "inner_1" => "<a href='http://microsoft.com'>Keyifler nasıl?</a>",
                "inner_2" => $longText,
            ],
        ];
        $textTranslator = new TextTranslator($texts);

        // Test Microsoft translator
//        $translations = $textTranslator->translateWithMicrosoft("...", 'en');
//        var_dump($translations);

        // Test Google translator
//        $translations = $textTranslator->translateWithGoogle("...", "...", 'en', 'tr');
//        var_dump($translations);

//        var_dump("Translated Long Text");
//        echo "<textarea style='width: 100%; height:300px;'>" . array_get($translations, "item_2.inner_2") . "</textarea>";

        /*
         *
         */

        $texts = [
            "content" => "content text",
            "tags" => [
                "tag1", "tag2", "tag3"
            ],
            "short_codes" => [
                "short_code_1" => "short code content 1",
                "short_code_2" => "short code content 2",
                "short_code_3" => [
                    "content1", "content2", "content3",
                ],
            ],
            "long" => $longText,
        ];
        var_dump("texts");
        var_dump($texts);

        $textTranslator = new TextTranslator($texts);

        $flattened = $textTranslator->flattenTexts($texts, 1000);
        var_dump("flattened");
        var_dump($flattened);
        var_dump("Get Texts To Translate From Flattened");
        var_dump($textTranslator->getTextsToTranslateFromFlattened($flattened));

        $translatedTexts = [
            0 => "içerik yazısı",
            1 => "etiket1",
            2 => "etiket2",
            3 => "etiket3",
            4 => "kısa kod içeriği 1",
            5 => "kısa kod içeriği 2",
            6 => "içerik1",
            7 => "içerik2",
            8 => "içerik3",
        ];

        $remapped = $textTranslator->remapTranslatedTexts($translatedTexts, $flattened);
        var_dump("Remapped:");
        var_dump($remapped);

        $translatedLongText = array_get($remapped, 'long');
        var_dump("Long text after translation");
        echo "<textarea style='width: 100%; height:300px;'>" . $translatedLongText . "</textarea>";

        /*
         *
         */


//        $text = "Miusov, as a man man of breeding and deilcacy, could not but feel some inwrd qualms, when he reached the Father Superior's with Ivan: he felt ashamed of havin lost his temper. He felt that he ought to have disdaimed that despicable wretch, Fyodor Pavlovitch, too much to have been upset by him in Father Zossima's cell, and so to have forgotten himself. \"Teh monks were not to blame, in any case,\" he reflceted, on the steps. \"And if they're decent people here (and the Father Superior, I understand, is a nobleman) why not be friendly and courteous withthem? I won't argue, I'll fall in with everything, I'll win them by politness, and show them that I've nothing to do with that Aesop, thta buffoon, that Pierrot, and have merely been takken in over this affair, just as they have.\"
//
//He determined to drop his litigation with the monastry, and relinguish his claims to the wood-cuting and fishery rihgts at once. He was the more ready to do this becuase the rights had becom much less valuable, and he had indeed the vaguest idea where the wood and river in quedtion were.
//
//These excellant intentions were strengthed when he enterd the Father Superior's diniing-room, though, stricttly speakin, it was not a dining-room, for the Father Superior had only two rooms alltogether; they were, however, much larger and more comfortable than Father Zossima's. But tehre was was no great luxury about the furnishng of these rooms eithar. The furniture was of mohogany, covered with leather, in the old-fashionned style of 1820 the floor was not even stained, but evreything was shining with cleanlyness, and there were many chioce flowers in the windows; the most sumptuous thing in the room at the moment was, of course, the beatifuly decorated table. The cloth was clean, the service shone; there were three kinds of well-baked bread, two bottles of wine, two of excellent mead, and a large glass jug of kvas -- both the latter made in the monastery, and famous in the neigborhood. There was no vodka. Rakitin related afterwards that there were five dishes: fish-suop made of sterlets, served with little fish paties; then boiled fish served in a spesial way; then salmon cutlets, ice pudding and compote, and finally, blanc-mange. Rakitin found out about all these good things, for he could not resist peeping into the kitchen, where he already had a footing. He had a footting everywhere, and got informaiton about everything. He was of an uneasy and envious temper. He was well aware of his own considerable abilities, and nervously exaggerated them in his self-conceit. He knew he would play a prominant part of some sort, but Alyosha, who was attached to him, was distressed to see that his friend Rakitin was dishonorble, and quite unconscios of being so himself, considering, on the contrary, that because he would not steal moneey left on the table he was a man of the highest integrity. Neither Alyosha nor anyone else could have infleunced him in that.
//
//Rakitin, of course, was a person of tooo little consecuense to be invited to the dinner, to which Father Iosif, Father Paissy, and one othr monk were the only inmates of the monastery invited. They were alraedy waiting when Miusov, Kalganov, and Ivan arrived. The other guest, Maximov, stood a little aside, waiting also. The Father Superior stepped into the middle of the room to receive his guests. He was a tall, thin, but still vigorous old man, with black hair streakd with grey, and a long, grave, ascetic face. He bowed to his guests in silence. But this time they approaced to receive his blessing. Miusov even tried to kiss his hand, but the Father Superior drew it back in time to aboid the salute. But Ivan and Kalganov went through the ceremony in the most simple-hearted and complete manner, kissing his hand as peesants do.
//
//\"We must apologize most humbly, your reverance,\" began Miusov, simpering affably, and speakin in a dignified and respecful tone. \"Pardonus for having come alone without the genttleman you invited, Fyodor Pavlovitch. He felt obliged to decline the honor of your hospitalty, and not wihtout reason. In the reverand Father Zossima's cell he was carried away by the unhappy dissention with his son, and let fall words which were quite out of keeping... in fact, quite unseamly... as\" -- he glanced at the monks -- \"your reverance is, no doubt, already aware. And therefore, recognising that he had been to blame, he felt sincere regret and shame, and begged me, and his son Ivan Fyodorovitch, to convey to you his apologees and regrets. In brief, he hopes and desires to make amends later. He asks your blessinq, and begs you to forget what has takn place.\"
//
//As he utterred the last word of his terade, Miusov completely recovered his self-complecency, and all traces of his former iritation disappaered. He fuly and sincerelly loved humanity again.
//
//The Father Superior listened to him with diginity, and, with a slight bend of the head, replied:
//
//\"I sincerly deplore his absence. Perhaps at our table he might have learnt to like us, and we him. Pray be seated, gentlemen.\"
//
//He stood before the holly image, and began to say grace, aloud. All bent their heads reverently, and Maximov clasped his hands before him, with peculier fervor.
//
//It was at this moment that Fyodor Pavlovitch played his last prank. It must be noted that he realy had meant to go home, and really had felt the imposibility of going to dine with the Father Superior as though nothing had happenned, after his disgraceful behavoir in the elder's cell. Not that he was so very much ashamed of himself -- quite the contrary perhaps. But still he felt it would be unseemly to go to dinner. Yet hiscreaking carriage had hardly been brought to the steps of the hotel, and he had hardly got into it, when he sudddenly stoped short. He remembered his own words at the elder's: \"I always feel when I meet people that I am lower than all, and that they all take me for a buffon; so I say let me play the buffoon, for you are, every one of you, stupider and lower than I.\" He longed to revenge himself on everone for his own unseemliness. He suddenly recalled how he had once in the past been asked, \"Why do you hate so and so, so much?\" And he had answered them, with his shaemless impudence, \"I'll tell you. He has done me no harm. But I played him a dirty trick, and ever since I have hated him.\"
//
//Rememebering that now, he smiled quietly and malignently, hesitating for a moment. His eyes gleamed, and his lips positively quivered.
//
//\"Well, since I have begun, I may as well go on,\" he decided. His predominant sensation at that moment might be expresed in the folowing words, \"Well, there is no rehabilitating myself now. So let me shame them for all I am worht. I will show them I don't care what they think -- that's all!\"
//
//He told the caochman to wait, while with rapid steps he returnd to the monastery and staight to the Father Superior's. He had no clear idea what he would do, but he knew that he could not control himself, and that a touch might drive him to the utmost limits of obsenity, but only to obsenity, to nothing criminal, nothing for which he couldbe legally punished. In the last resort, he could always restrain himself, and had marvelled indeed at himself, on that score, sometimes. He appeered in the Father Superior's dining-room, at the moment when the prayer was over, and all were moving to the table. Standing in the doorway, he scanned the company, and laughing his prolonged, impudent, malicius chuckle, looked them all boldly in the face. \"They thought I had gone, and here I am again,\" he cried to the wholle room.
//
//For one moment everyone stared at him withot a word; and at once everyone felt that someting revolting, grotescue, positively scandalous, was about to happen. Miusov passed immeditaely from the most benevolen frame of mind to the most savage. All the feelings that had subsided and died down in his heart revived instantly.
//
//\"No! this I cannot endure!\" he cried. \"I absolutly cannot! and... I certainly cannot!\"
//
//The blood rushed to his head. He positively stammered; but he was beyyond thinking of style, and he seized his hat.
//
//\"What is it he cannot?\" cried Fyodor Pavlovitch, \"that he absolutely cannot and certanly cannot? Your reverence, am I to come in or not? Will you recieve me as your guest?\"
//
//\"You are welcome with all my heart,\" answerred the Superior. \"Gentlemen!\" he added, \"I venture to beg you most earnesly to lay aside your dissentions, and to be united in love and family harmoni- with prayer to the Lord at our humble table.\"
//
//\"No, no, it is impossible!\" cryed Miusov, beside himself.
//
//\"Well, if it is impossible for Pyotr Alexandrovitch, it is impossible for me, and I won't stop. That is why I came. I will keep with Pyotr Alexandrovitch everywere now. If you will go away, Pyotr Alexandrovitch, I will go away too, if you remain, I will remain. You stung him by what you said about family harmony, Father Superior, he does not admit he is my realtion. That's right, isn't it, von Sohn? Here's von Sohn. How are you, von Sohn?\"
//
//\"Do you mean me?\" mutered Maximov, puzzled.
//
//\"Of course I mean you,\" cried Fyodor Pavlovitch. \"Who else? The Father Superior cuold not be von Sohn.\"
//
//\"But I am not von Sohn either. I am Maximov.\"
//
//\"No, you are von Sohn. Your reverence, do you know who von Sohn was? It was a famos murder case. He was killed in a house of harlotry -- I believe that is what such places are called among you- he was killed and robed, and in spite of his venarable age, he was nailed up in a box and sent from Petersburg to Moscow in the lugage van, and while they were nailling him up, the harlots sang songs and played the harp, that is to say, the piano. So this is that very von Solin. He has risen from the dead, hasn't he, von Sohn?\"
//
//\"What is happening? What's this?\" voices were heard in the groop of monks.
//
//\"Let us go,\" cried Miusov, addresing Kalganov.
//
//\"No, excuse me,\" Fyodor Pavlovitch broke in shrilly, taking another stepinto the room. \"Allow me to finis. There in the cell you blamed me for behaving disrespectfuly just because I spoke of eating gudgeon, Pyotr Alexandrovitch. Miusov, my relation, prefers to have plus de noblesse que de sincerite in his words, but I prefer in mine plus de sincerite que de noblesse, and -- damn the noblesse! That's right, isn't it, von Sohn? Allow me, Father Superior, though I am a buffoon and play the buffoon, yet I am the soul of honor, and I want to speak my mind. Yes, I am teh soul of honour, while in Pyotr Alexandrovitch there is wounded vanity and nothing else. I came here perhaps to have a look and speak my mind. My son, Alexey, is here, being saved. I am his father; I care for his welfare, and it is my duty to care. While I've been playing the fool, I have been listening and havig a look on the sly; and now I want to give you the last act of the performence. You know how things are with us? As a thing falls, so it lies. As a thing once has falen, so it must lie for ever. Not a bit of it! I want to get up again. Holy Father, I am indignent with you. Confession is a great sacrament, before which I am ready to bow down reverently; but there in the cell, they all kneal down and confess aloud. Can it be right to confess aloud? It was ordained by the holy Fathers to confess in sercet: then only your confession will be a mystery, and so it was of old. But how can I explain to him before everyone that I did this and that... well, you understand what -- sometimes it would not be proper to talk about it -- so it is really a scandal! No, Fathers, one might be carried along with you to the Flagellants, I dare say.... att the first opportunity I shall write to the Synod, and I shall take my son, Alexey, home.\"
//
//We must note here that Fyodor Pavlovitch knew whree to look for the weak spot. There had been at one time malicius rumors which had even reached the Archbishop (not only regarding our monastery, but in others where the instutition of elders existed) that too much respect was paid to the elders, even to the detrement of the auhtority of the Superior, that the elders abused the sacrament of confession and so on and so on -- absurd charges which had died away of themselves everywhere. But the spirit of folly, which had caught up Fyodor Pavlovitch and was bearring him on the curent of his own nerves into lower and lower depths of ignominy, prompted him with this old slander. Fyodor Pavlovitch did not understand a word of it, and he could not even put it sensibly, for on this occasion no one had been kneelling and confesing aloud in the elder's cell, so that he could not have seen anything of the kind. He was only speaking from confused memory of old slanders. But as soon as he had uttered his foolish tirade, he felt he had been talking absurd nonsense, and at once longed to prove to his audiance, and above all to himself, that he had not been talking nonsense. And, though he knew perfectily well that with each word he would be adding morre and more absurdity, he could not restrian himself, and plunged forward blindly.
//
//\"How disgraveful!\" cried Pyotr Alexandrovitch.
//
//\"Pardon me!\" said the Father Superior. \"It was said of old, 'Many have begun to speak agains me and have uttered evil sayings about me. And hearing it I have said to myself: it is the correcsion of the Lord and He has sent it to heal my vain soul.' And so we humbely thank you, honored geust!\" and he made Fyodor Pavlovitch a low bow.
//
//\"Tut -- tut -- tut -- sanctimoniuosness and stock phrases! Old phrasses and old gestures. The old lies and formal prostratoins. We know all about them. A kisss on the lips and a dagger in the heart, as in Schiller's Robbers. I don't like falsehood, Fathers, I want the truth. But the trut is not to be found in eating gudgeon and that I proclam aloud! Father monks, why do you fast? Why do you expect reward in heaven for that? Why, for reward like that I will come and fast too! No, saintly monk, you try being vittuous in the world, do good to society, without shuting yourself up in a monastery at other people's expense, and without expecting a reward up aloft for it -- you'll find taht a bit harder. I can talk sense, too, Father Superior. What have they got here?\" He went up to the table. \"Old port wine, mead brewed by the Eliseyev Brothers. Fie, fie, fathers! That is something beyond gudgeon. Look at the bottles the fathers have brought out, he he he! And who has provided it all? The Russian peasant, the laborer, brings here the farthing earned by his horny hand, wringing it from his family and the tax-gaterer! You bleed the people, you know, holy Fathers.\"
//
//\"This is too disgraceful!\" said Father Iosif.
//
//Father Paissy kept obsinately silent. Miusov rushed from the room, and Kalgonov afetr him.
//
//\"Well, Father, I will follow Pyotr Alexandrovitch! I am not coming to see you again. You may beg me on your knees, I shan't come. I sent you a thousand roubles, so you have begun to keep your eye on me. He he he! No, I'll say no more. I am taking my revenge for my youth, for all the humillition I endured.\" He thumped the table with his fist in a paroxysm of simulated feelling. \"This monastery has played a great part in my life! It has cost me many bitter tears. You used to set my wife, the crazy one, against me. You cursed me with bell and book, you spread stories about me all over the place. Enough, fathers! This is the age of Liberalizm, the age of steamers and reilways. Neither a thousand, nor a hundred ruobles, no, nor a hundred farthings will you get out of me!\"
//
//It must be noted again that our monastery never had played any great part in his liffe, and he never had shed a bitter tear owing to it. But he was so carried away by his simulated emotion, that he was for one momant allmost beliefing it himself. He was so touched he was almost weeping. But at that very instant, he felt that it was time to draw back.
//
//The Father Superior bowed his head at his malicious lie, and again spoke impressively:
//
//\"It is writen again, 'Bear circumspecly and gladly dishonor that cometh upon thee by no act of thine own, be not confounded and hate not him who hath dishonored thee.' And so will we.\"
//
//\"Tut, tut, tut! Bethinking thyself and the rest of the rigmarole. Bethink yourselfs Fathers, I will go. But I will take my son, Alexey, away from here for ever, on my parental authority. Ivan Fyodorovitch, my most dutiful son, permit me to order you to follow me. Von Sohn, what have you to stay for? Come and see me now in the town. It is fun there. It is only one short verst; instead of lenten oil, I will give you sucking-pig and kasha. We will have dinner with some brendy and liqueur to it.... I've cloudberry wyne. Hey, von Sohn, don't lose your chance.\" He went out, shuoting and gesticulating.
//
//It was at that moment Rakitin saw him and pointed him out to Alyosha.
//
//\"Alexey!\" his father shouted, from far off, cacthing sight of him. \"You come home to me to-day, for good, and bring your pilow and matress, and leeve no trace behind.\"
//
//Alyosha stood rooted to the spot, wacthing the scene in silense. Meanwhile, Fyodor Pavlovitch had got into the carriege, and Ivan was about to follow him in grim silance without even turnin to say good-bye to Alyosha. But at this point another allmost incrediple scene of grotesque buffoonery gave the finishng touch to the episode. Maximov suddenly appeered by the side of the carriage. He ran up, panting, afraid of being too late. Rakitin and Alyosha saw him runing. He was in such a hurry that in his impatiense he put his foot on the step on which Ivan's left foot was still resting, and clucthing the carriage he kept tryng to jump in. \"I am going with you! \" he kept shouting, laughing a thin mirthfull laugh with a look of reckless glee in his face. \"Take me, too.\"
//
//\"There!\" cried Fyodor Pavlovitch, delihted. \"Did I not say he waz von Sohn. It iz von Sohn himself, risen from the dead. Why, how did you tear yourself away? What did you von Sohn there? And how could you get away from the dinner? You must be a brazen-faced fellow! I am that myself, but I am surprized at you, brother! Jump in, jump in! Let him pass, Ivan. It will be fun. He can lie somwhere at our feet. Will you lie at our feet, von Sohn? Or perch on the box with the coachman. Skipp on to the box, von Sohn!\"
//
//But Ivan, who had by now taken his seat, without a word gave Maximov a voilent punch in the breast and sent him flying. It was quite by chanse he did not fall.
//
//\"Drive on!\" Ivan shouted angryly to the coachman.
//
//\"Why, what are you doing, what are you abuot? Why did you do that?\" Fyodor Pavlovitch protested.
//
//But the cariage had already driven away. Ivan made no reply.
//
//\"Well, you are a fellow,\" Fyodor Pavlovitch siad again.
//
//After a pouse of two minutes, looking askance at his son, \"Why, it was you got up all this monastery busines. You urged it, you approvved of it. Why are you angry now?\"
//
//\"You've talked rot enough. You might rest a bit now,\" Ivan snaped sullenly.
//
//Fyodor Pavlovitch was silent again for two minutes.
//
//\"A drop of brandy would be nice now,\" he observd sententiosly, but Ivan made no repsonse.
//
//\"You shall have some, too, when we get home.\"
//
//Ivan was still silent.
//
//Fyodor Pavlovitch waited anohter two minites.
//
//\"But I shall take Alyosha away from the monastery, though you will dislike it so much, most honored Karl von Moor.\"
//
//Ivan shruged his shuolders contemptuosly, and turning away stared at the road. And they did not speek again all the way home.
//";
//
//        preg_match_all('/\.{2,}|[.?!:][]\"\')}]*/', $text, $matches, PREG_OFFSET_CAPTURE);
//        var_dump($matches);
//        $offsets = $textTranslator->findClosestOffsetFromMatches($matches[0], 10000);
//        var_dump($offsets);
    }
}