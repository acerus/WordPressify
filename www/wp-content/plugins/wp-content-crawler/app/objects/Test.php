<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 12:16
 */

namespace WPCCrawler\objects;


use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Factory;
use WPCCrawler\objects\crawling\CategoryBot;
use WPCCrawler\objects\crawling\data\CategoryData;
use WPCCrawler\objects\crawling\data\PostData;
use WPCCrawler\objects\crawling\DummyBot;
use WPCCrawler\objects\crawling\PostBot;
use WPCCrawler\objects\traits\FindAndReplaceTrait;
use WPCCrawler\objects\translation\TextTranslator;
use WPCCrawler\Utils;
use WPCCrawler\WPCCrawler;

class Test {

    use FindAndReplaceTrait;

    public static $TEST_TYPE_HREF                               = "test_type_selector_href";
    public static $TEST_TYPE_TEXT                               = "test_type_selector_text";
    public static $TEST_TYPE_HTML                               = "test_type_selector_html";
    public static $TEST_TYPE_SRC                                = "test_type_selector_src";
    public static $TEST_TYPE_FIRST_POSITION                     = "test_type_selector_first_position";
    public static $TEST_TYPE_FIND_REPLACE                       = "test_type_selector_find_replace";
    public static $TEST_TYPE_SELECTOR_ATTRIBUTE                 = "test_type_selector_attribute";
    public static $TEST_TYPE_SOURCE_CODE                        = "test_type_source_code";
    public static $TEST_TYPE_PROXY                              = "test_type_proxy";
    public static $TEST_TYPE_FIND_REPLACE_IN_ELEMENT_ATTRIBUTES = "test_type_find_replace_in_element_attributes";
    public static $TEST_TYPE_EXCHANGE_ELEMENT_ATTRIBUTES        = "test_type_exchange_element_attributes";
    public static $TEST_TYPE_REMOVE_ELEMENT_ATTRIBUTES          = "test_type_remove_element_attributes";
    public static $TEST_TYPE_FIND_REPLACE_IN_ELEMENT_HTML       = "test_type_find_replace_in_element_html";
    public static $TEST_TYPE_FIND_REPLACE_IN_CUSTOM_META        = "test_type_find_replace_in_custom_meta";
    public static $TEST_TYPE_FIND_REPLACE_IN_CUSTOM_SHORT_CODE  = "test_type_find_replace_in_custom_short_code";
    public static $TEST_TYPE_TRANSLATION                        = "test_type_translation";

    private static $MAX_TEST_ITEM = 1000000;

    /**
     * @param string $subject The subject for which find and replace test will be conducted
     * @param string $find What to find
     * @param string|null $replace With what to replace
     * @param bool $regex True if find text is a regular expression.
     * @return string JSON
     */
    public function conductFindReplaceTest($subject, $find, $replace, $regex = false) {
        // First, remove slashes. Since the data comes from AJAX as a JSON object, the slashes are added to supply a valid
        // JSON object.
        $subject    = wp_unslash($subject);

	    // Reset the find-and-replace errors that may exist.
	    $this->resetFindAndReplaceErrors();

	    // Make the replacement.
        $subject = $this->findAndReplaceSingle($find, $replace, $subject, $regex);

        $message = sprintf(
            _wpcc('Test result for find %1$s and replace with %2$s'),
            "<span class='highlight find'>" . htmlspecialchars($find) . "</span>",
            "<span class='highlight replace'>" . htmlspecialchars($replace) . "</span>"
        );

        if($regex) {
            $message .= " " . _wpcc("(as regex)");
        }

        $message .= ':';

        $results = [];
        $results[] = $subject;

	    // If there are errors, add them to the results to inform the user about them.
	    if($errors = $this->getFindAndReplaceErrors()) {
		    foreach($errors as $data) {
			    $error = isset($data["error"]) ? $data["error"] : "";
			    $detail = isset($data["data"]) ? $data["data"] : "";
			    $results[] = _wpcc("Error") . ": " . $error . " => " . $detail;
		    }
	    }

        return json_encode([
            'view'  =>  Utils::view('partials/test-result')
                ->with("results", $results)
                ->with("message", $message)
                ->render(),
            'data'  =>  $results
        ]);
    }

    /**
     * @param int $postId The ID of the site
     * @param string $testType One of the values of the array TestService::$GENERAL_TESTS
     * @param string $testUrlPart The URL
     * @return string A response including rendered blade view which can be directly appended to an HTML element, and data
     */
    public function conductGeneralTest($postId, $testType, $testUrlPart) {
        if(!in_array($testType, array_values(Factory::testController()->getGeneralTestTypes()))) wp_die("Test type is not valid.");

        WPCCrawler::setDoingTest(true);
        $testType = str_replace("test_", "", $testType);

        $settings = get_post_meta($postId);

        $view = $testType . " for site id " . $postId;  // Just a placeholder, in case there is no view.

        switch($testType) {
            case "category":
                $view = $this->testCategory($settings, $testUrlPart, $postId);
                break;
            case "post":
                $view = $this->testPost($settings, $testUrlPart, $postId);
                break;
        }

        return json_encode(['view' => $view]);
    }

    /**
     * @param array  $settings    Settings (post meta array) for the post to be tested
     * @param string $testUrlPart URL
     * @param int    $siteId
     * @return string HTML
     */
    private function testPost($settings, $testUrlPart, $siteId) {
        $info = [];
        $postData = new PostData();

        $start = microtime(true);
        $memory = memory_get_usage();

        // Remove trailing slash from the main url
//        $mainUrl = Utils::getPostMetaValue($settings, '_main_page_url');

        // PREPARE THE URL AND GET THE POST
        $template = '';
        if(!empty($testUrlPart)) {
            $bot = new PostBot($settings, $siteId);
            if($postData = $bot->crawlPost(Utils::prepareUrl($bot->getSiteUrl(), $testUrlPart))) {
                $template = $postData->getTemplate();
            }
        }

        $this->addInfo($info, $memory, $start, $postData);

        return Utils::view('site-tester/test-results')->with([
            'template'          =>  $template,
            'info'              =>  $info,
            'data'              =>  (array) $postData,
            'showSourceCode'    =>  true,
            'templateMessage'   =>  _wpcc('Styling can be different on front page depending on your theme.')
        ])->render();
    }

    /**
     * @param array  $settings Settings (post meta array) for the post to be tested
     * @param string $url
     * @param int    $siteId
     * @return string HTML
     */
    private function testCategory($settings, $url, $siteId) {
        $info = [];
        $categoryData = new CategoryData();
        $template = false;

        $start = microtime(true);
        $memory = memory_get_usage();

        if(!empty($url)) {
            $bot = new CategoryBot($settings, $siteId);
            if($categoryData = $bot->collectUrls(Utils::prepareUrl($bot->getSiteUrl(), $url))) {
                $template = Utils::view('site-tester/category-test')->with([
                    'nextPageUrl'   =>  $categoryData->getNextPageUrl(),
                    'urls'          =>  $categoryData->getPostUrls()
                ])->render();
            }
        }

        $this->addInfo($info, $memory, $start, $categoryData);

        return Utils::view('site-tester/test-results')->with([
            'info'      =>  $info,
            'data'      =>  (array) $categoryData,
            'template'  =>  $template
        ])->render();
    }

    /**
     * Adds time passed, memory used and a few other information to the provided array
     *
     * @param array $info The array to which the new info is added
     * @param int $memoryUsageInitial Memory usage when the operation starts
     * @param int $timeInitial Micro time when the operation starts
     * @param CategoryData|PostData|array $data The data found after the test
     */
    private function addInfo(&$info, $memoryUsageInitial, $timeInitial, $data) {

        // Get common info
        if($data instanceof PostData || $data instanceof CategoryData) {
            if ($nextPageUrl = $data->getNextPageUrl()) {
                $info[_wpcc("Next Page URL")] = sprintf('<a href="%1$s" target="_blank">%1$s</a>', $nextPageUrl);
            }
        }

        // Get info specific to PostData
        if($data instanceof PostData) {
            if ($title              = $data->getTitle())                        $info[_wpcc("Title")]                   = $title;
            if ($date               = $data->getDateCreated())                  $info[_wpcc("Date")]                    = Utils::getDateFormatted($date) . " ({$date})";
            if ($metaKeywords       = $data->getMetaKeywords())                 $info[_wpcc("Meta Keywords")]           = $metaKeywords;
            if ($metaKeywordsAsTags = $data->getMetaKeywordsAsTags())           $info[_wpcc("Meta Keywords As Tags")]   = $metaKeywordsAsTags;
            if ($metaDescription    = $data->getMetaDescription())              $info[_wpcc("Meta Description")]        = $metaDescription;
            if ($data->getExcerpt() && $excerpt = $data->getExcerpt()["data"])  $info[_wpcc("Excerpt")]                 = $excerpt;
            if ($tags               = $data->getTags())                         $info[_wpcc("Tags")]                    = $tags;
            if ($preparedTags       = $data->getPreparedTags())                 $info[_wpcc("Prepared Post Tags")]      = $preparedTags;

            if ($customPostMeta = $data->getCustomMeta()) {
                $preparedCustomPostMeta = [];

                foreach($customPostMeta as $item) {
                    $content = Utils::array_get($item, "data");
                    if(is_array($content)) $content = implode("<hr>", $content);

                    $preparedCustomPostMeta[] = sprintf(_wpcc('<b>Meta key:</b> %1$s, <b>Multiple:</b> %2$s, <b>Content:</b> %3$s'),
                        Utils::array_get($item, "meta_key"),
                        Utils::array_get($item, "multiple") ? _wpcc("Yes") : _wpcc("No"),
                        "<br>" . $content
                    );
                }

                $info[_wpcc("Custom Post Meta")] = $preparedCustomPostMeta;
            }

            // Show featured image link as a real link, and add a preview to be displayed in tooltip.
            if ($thumbnailUrl = $data->getThumbnailUrl()) $info[_wpcc("Featured Image URL")] =
                sprintf('<a href="%1$s" target="_blank" data-toggle="tooltip" data-html="true" title="<img src=\'%1$s\'>">%s</a>', $thumbnailUrl);

            // Show attachment links as real links. Add a preview tooltip if the attachment is an image.
            if ($attachmentData = $data->getAttachmentData()) {
                $attachmentData = array_map(function ($item) {
                    $url = $item["url"];
                    $tooltip = '';
                    if (preg_match('/\.(jpg|JPG|png|PNG|gif|GIF|jpeg|JPEG)/', $url))
                        $tooltip = 'data-html="true" data-toggle="tooltip" title="<img src=\'%1$s\'>"';

                    return (isset($item["gallery_image"]) && $item["gallery_image"] ? _wpcc("Gallery Item") . ": " : '')
                    . sprintf('<a href="%1$s" ' . $tooltip . ' target="_blank">%1$s</a>', $url);
                }, $attachmentData);

                $info[_wpcc("Attachments")] = $attachmentData;
            }

            // Show all next page URLs as a list with test buttons, so that the user can just click the button to test the next page.
            if ($allPageUrls = $data->getAllPageUrls()) {
                $info[_wpcc("Next Page URLs")] = Utils::view('site-tester/urls-with-test')->with([
                    'urls'           => $allPageUrls,
                    'testType'       => 'test_post',
                    'hideThumbnails' => true
                ])->render();
            }

        }

        $info[_wpcc("Memory Used")] = number_format((memory_get_usage() - $memoryUsageInitial) / 1000000, 2) . ' MB';
        $info[_wpcc("Time")]        = number_format((microtime(true) - $timeInitial) * 1000, 2) . ' ms';
    }

    /**
     * Conducts simple tests for settings page selectors.
     *
     * @param string      $url      URL of the site to be crawled
     * @param string      $selector CSS selector to be tested
     * @param string      $testType
     * @param string|null $attr     Attribute to be retrieved. This is used only for certain test types.
     * @param null|string $content  The content on which the selector will be tested. If this is supplied, instead of
     *                              making a request to the URL, the content will be used directly.
     * @param null|bool   $useUtf8  See {@link AbstractBot::__construct}
     * @return string A response including rendered blade view which can be directly appended to an HTML element, and data
     */
    public function conductSelectorTest($url, $selector, $testType = null, $attr = null, $content = null, $useUtf8 = null) {
        // Create a dummy bot to get the client.
        $bot = new PostBot([], null, $useUtf8);

        if(!$content) {
            $crawler = $bot->request($url);
        } else {
            $crawler = new Crawler(wp_unslash($content));
        }

        if(!$testType) $testType = static::$TEST_TYPE_HTML;

        $results = [];

        $abort = false;
        $crawler->filter($selector)->each(function($node, $i) use ($testType, $attr, &$results, $crawler, &$abort) {
            if($abort) return;

            /** @var Crawler $node */
            if($i >= static::$MAX_TEST_ITEM) return;

            $result = false;
            try {
                switch ($testType) {
                    case static::$TEST_TYPE_HREF:
                        $result = $node->attr("href");
                        break;
                    case static::$TEST_TYPE_HTML:
                        $result = Utils::getNodeHTML($node);
                        break;
                    case static::$TEST_TYPE_TEXT:
                        $result = $node->text();
                        break;
                    case static::$TEST_TYPE_SRC:
                        $result = $node->attr("src");
                        break;
                    case static::$TEST_TYPE_FIRST_POSITION:
                        $nodeHtml = Utils::getNodeHTML($node);
                        $result = $nodeHtml ? mb_strpos($crawler->html(), $nodeHtml) : false;
                        break;
                    case static::$TEST_TYPE_SELECTOR_ATTRIBUTE:
                        if($attr) {
                            switch($attr) {
                                case "text":
                                    $result = $node->text();
                                    break;
                                case "html":
                                    $result = Utils::getNodeHTML($node);
                                    break;
                                default:
                                    $result = $node->attr($attr);
                                    break;
                            }
                        }
                        break;
                }
            } catch(\InvalidArgumentException $e) { }

            if($result) {
                if($testType == static::$TEST_TYPE_FIRST_POSITION) {
                    $results[] = Utils::getNodeHTML($node); // Add html of the node for a meaningful result
                    $results[] = $result;
                    $abort = true;
                } else if($result = trim($result)) {
                    $results[] = $result;
                }
            }

        });

        $message = sprintf(
            _wpcc('Test results for %1$s%2$s on %3$s:'),
            "<span class='highlight selector'>" . $selector . "</span>",
            $attr   ? " <span class='highlight attribute'>" . $attr . "</span> "    : '',
            $url    ? "<span class='highlight url'>" . $url . "</span>"             : ''
        );

        return json_encode([
            'view'  =>  Utils::view('partials/test-result')
                ->with("results", $results)
                ->with("message", $message)
                ->render(),
            'data'  =>  $results
        ]);
    }

    /**
     * Get the source code of a URL.
     *
     * @param string $url           URL whose source code is needed
     * @param bool   $removeScripts True if the scripts should be removed from the source code
     * @param bool   $removeStyles  True if the styles should be removed from the source code
     * @param string $cookies       A URL-encoded array of arrays with each inner array having 'key' and 'value' keys
     *                              and their values that will be used as cookie keys and values.
     * @param null|bool   $useUtf8  See {@link AbstractBot::__construct}
     * @return null|string Null or HTML
     */
    public function conductSourceCodeTest($url, $removeScripts = true, $removeStyles = true, $cookies = '', $useUtf8 = null) {
        if(!$url) return null;

        parse_str($cookies, $cookies);
        $bot = new DummyBot([
            '_cookies' => is_array($cookies) && isset($cookies['_cookies']) ? $cookies['_cookies'] : null
        ], null, $useUtf8);

        $crawler = $bot->request($url, "GET");

        if(!$crawler) return null;

        if($removeScripts) $bot->removeElementsFromCrawler($crawler, "script");
        if($removeStyles) $bot->removeElementsFromCrawler($crawler, ["style", "[rel=stylesheet]"]);

        // Get the HTML to be manipulated
        $html = Utils::getNodeHTML($crawler);

        // Remove empty attributes. This is important for CSS selector finder script. It fails when there is an attribute
        // whose attribute consists of only spaces.
        $html = $this->findAndReplaceSingle(
            '<.*?[a-zA-Z-]+=["\']\s+["\'].*?>',
            '',
            $html,
            true
        );

        $parts = parse_url($url);
        $base = (isset($parts['scheme']) && $parts['scheme'] ? $parts['scheme'] : 'http') . '://' . $parts['host'];

        // Set the base URL like this. By this way, relative URLs will be handled correctly.
//        <head><base href='http://base-url.net/' /></head>
        $html = $this->findAndReplaceSingle(
            '(<head>|<head\s[^>]+>)',
            '$1 <base href="' . $base . '">',
            $html,
            true
        );

        return json_encode([
            'html' => $html
        ]);
    }

    /**
     * Test if the proxies work OK.
     *
     * @param string $proxies New-line-separated proxies
     * @param string $testUrl The URL that will be tested with the proxies
     * @return string A response including rendered blade view which can be directly appended to an HTML element, and data
     */
    public function conductProxyTest($proxies, $testUrl) {
        // Create a dummy bot by making sure it will consider the proxies.
        // By this way, we can retrieve the prepared proxy lists from the bot.
        $dummyBot = new DummyBot([
            '_do_not_use_general_settings' => 1,
            '_wpcc_use_proxy' => 1,
            '_wpcc_proxies'   => $proxies,
        ]);

        $protocol = starts_with($testUrl, "https") ? "https" : "http";

        // Get proxy list for this protocol
        $proxyList = $dummyBot->preparedProxyList;

        $results = [];

        if($testUrl) {
            foreach($proxyList as $proxyUrl) {
                try {
                    // If there is a proxy, create a new client with the proxy settings.
                    $dummyBot->createClient($proxyUrl, $protocol);

                    $crawler = $dummyBot->getClient()->request("GET", $testUrl);

                    // Get the response
                    $response = $dummyBot->getClient()->getInternalResponse();

                    // If the response is not OK, this proxy is failed.
                    if($response->getStatus() != 200) {
                        $results[] = _wpcc("Fail") . ": " . $proxyUrl;
                        continue;
                    }

                    // Try to get the HTML content. If this causes an error, we'll catch it and return null.
                    $crawler->html();

                    $results[] = _wpcc("Success") . ": " . $proxyUrl;

                    // For testing. TODO: Remove this.
//                if($tryCount == 0)
//                    throw new \GuzzleHttp\Exception\ConnectException("Testing...", new Request("GET", "httpabc"));

                    // If the connection failed, mark this proxy as failed.
                } catch(\GuzzleHttp\Exception\ConnectException $e) {
                    $results[] = _wpcc("Fail") . ": " . $proxyUrl;

                    // Catch other request exceptions
                } catch(\GuzzleHttp\Exception\RequestException $e) {
                    $results[] = _wpcc("Error") . ": " . $e->getMessage();

                    // Catch all errors
                } catch(\Exception $e) {
                    $results[] = _wpcc("Error") . ": " . $e->getMessage();
                    error_log("Content Crawler - Exception for '{$testUrl}': " . $e->getMessage());
                    break;
                }
            }
        }

        $message = sprintf(
            _wpcc('Test results for %1$s:'),
            "<span class='highlight url'>" . $testUrl . "</span>"
        );

        return json_encode([
            'view'  =>  Utils::view('partials/test-result')
                ->with("results", $results)
                ->with("message", $message)
                ->render(),
            'data'  =>  $results
        ]);
    }

    /**
     * @param string $url
     * @param string $content
     * @param string $selector
     * @param string $attr
     * @param string $find
     * @param string $replace
     * @param bool $regex
     * @param null|bool $useUtf8
     * @return string
     */
    public function conductFindReplaceInElementAttributesTest($url, $content, $selector, $attr, $find, $replace, $regex = false, $useUtf8 = null) {
        return $this->handleHtmlManipulationTest(
            function($crawler, $bot) use (&$selector, &$attr, &$find, &$replace, &$regex) {
                /** @var PostBot $bot */
                $bot->findAndReplaceInElementAttribute($crawler, [$selector], $attr, $find, $replace, $regex);
                return $crawler;
            },
            $url,
            $content,
            $selector,
            $useUtf8,
            sprintf('%1$s %2$s %3$s %4$s %5$s',
                $selector   ? "<span class='highlight selector'>" . $selector . "</span>"                   : '',
                $attr       ? "<span class='highlight attribute'>" . $attr . "</span>"                      : '',
                $find       ? "<span class='highlight find'>" . htmlspecialchars($find) . "</span>"         : '',
                $replace    ? "<span class='highlight replace'>" . htmlspecialchars($replace) . "</span>"   : '',
                $regex      ? _wpcc("(as regex)") : ''
            )
        );
    }

    /**
     * See {@link handleHtmlManipulationTest}
     *
     * @param string    $url
     * @param string    $content
     * @param string    $selector
     * @param           $attr1
     * @param           $attr2
     * @param null|bool $useUtf8
     * @return string
     * @throws \Exception
     */
    public function conductExchangeElementAttributesTest($url, $content, $selector, $attr1, $attr2, $useUtf8 = null) {
        return $this->handleHtmlManipulationTest(
            function($crawler, $bot) use (&$selector, &$attr1, &$attr2) {
                /** @var PostBot $bot */
                $bot->exchangeElementAttributeValues($crawler, [$selector], $attr1, $attr2);
                return $crawler;
            },
            $url,
            $content,
            $selector,
            $useUtf8,
            sprintf('%1$s %2$s %3$s',
                $selector   ? "<span class='highlight selector'>" . $selector . "</span>"   : '',
                $attr1      ? "<span class='highlight attribute'>" . $attr1 . "</span>"     : '',
                $attr2      ? "<span class='highlight attribute'>" . $attr2 . "</span>"     : ''
            )
        );
    }

    /**
     * See {@link handleHtmlManipulationTest}
     *
     * @param string    $url
     * @param string    $content
     * @param string    $selector
     * @param           $attributes
     * @param null|bool $useUtf8
     * @return string
     * @throws \Exception
     */
    public function conductRemoveElementAttributesTest($url, $content, $selector, $attributes, $useUtf8 = null) {
        return $this->handleHtmlManipulationTest(
            function($crawler, $bot) use (&$selector, &$attributes) {
                /** @var PostBot $bot */
                $bot->removeElementAttributes($crawler, [$selector], $attributes);
                return $crawler;
            },
            $url,
            $content,
            $selector,
            $useUtf8,
            sprintf('%1$s %2$s',
                $selector   ? "<span class='highlight selector'>" . $selector . "</span>"       : '',
                $attributes ? "<span class='highlight attribute'>" . $attributes . "</span>"    : ''
            )
        );
    }

    /**
     * See {@link handleHtmlManipulationTest}
     *
     * @param string    $url
     * @param string    $content
     * @param string    $selector
     * @param string    $find
     * @param string    $replace
     * @param bool      $regex
     * @param null|bool $useUtf8
     * @return string
     * @throws \Exception
     */
    public function conductFindReplaceInElementHtmlTest($url, $content, $selector, $find, $replace, $regex = false, $useUtf8 = null) {
        return $this->handleHtmlManipulationTest(
            function($crawler, $bot) use (&$selector, &$find, &$replace, &$regex) {
                /** @var PostBot $bot */
                $bot->findAndReplaceInElementHTML($crawler, [$selector], $find, $replace, $regex);
                return $crawler;
            },
            $url,
            $content,
            $selector,
            $useUtf8,
            sprintf('%1$s %2$s %3$s %4$s',
                $selector   ? "<span class='highlight selector'>" . $selector . "</span>"                   : '',
                $find       ? "<span class='highlight find'>" . htmlspecialchars($find) . "</span>"         : '',
                $replace    ? "<span class='highlight replace'>" . htmlspecialchars($replace) . "</span>"   : '',
                $regex      ? _wpcc("(as regex)") : ''
            )
        );
    }

    /**
     * See {@link handleHtmlManipulationTest}
     *
     * @param string    $key
     * @param string    $url
     * @param string    $content
     * @param string    $selector
     * @param string    $attr
     * @param string    $find
     * @param string    $replace
     * @param bool      $regex
     * @param bool|null $useUtf8
     * @return string
     * @throws \Exception
     */
    public function conductFindReplaceInCustomMetaAndShortCodeTest($key, $url, $content, $selector, $attr, $find, $replace, $regex = false, $useUtf8 = null) {
        $results = [];

        // If there are a URL and a selector, get the content from that URL.
        if($url && $selector) {
            $bot = new PostBot([], null, $useUtf8);

            if($crawler = $bot->request($url)) {

                if($contents = $bot->extractData($crawler, [$selector], $attr ? $attr : 'text', null, false, true)) {
                   foreach($contents as $c) {
                       $results[] = $this->findAndReplaceSingle($find, $replace, $c, $regex);
                   }
                }

            }
        }

        // If there is a content, use it as well.
        if($content) {
            $content = wp_unslash($content);
            $results[] = $this->findAndReplaceSingle($find, $replace, $content, $regex);
        }

        $message = sprintf(_wpcc('Test results for %1$s %2$s %3$s %4$s %5$s %6$s %7$s'),
            sprintf('%1$s %2$s %3$s',
                $url && $selector   ? "<span class='highlight url'>" . $url . "</span>" : '',
                $url && $selector && $content ? _wpcc("and") : '',
                $content ? _wpcc("test code") : ''
            ),
            $key                ? "<span class='highlight key'>" . $key . "</span>"                             : '',
            $selector           ? "<span class='highlight selector'>" . $selector . "</span>"                   : '',
            $attr               ? "<span class='highlight attribute'>" . $attr . "</span>"                      : '',
            $find               ? "<span class='highlight find'>" . htmlspecialchars($find) . "</span>"         : '',
            $replace            ? "<span class='highlight replace'>" . htmlspecialchars($replace) . "</span>"   : '',
            $regex              ? _wpcc("(as regex)") : ''
        );

        return json_encode([
            'view'  =>  Utils::view('partials/test-result')
                ->with("results", $results)
                ->with("message", $message)
                ->render(),
            'data'  =>  $results
        ]);
    }

    /**
     * Conduct a translation test.
     *
     * @param string $text          The text to be translated
     * @param string $serviceType   Type of the service. E.g. TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION or TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT
     * @param string $from          Code of the text's language
     * @param string $to            Target language to which the text will be translated
     * @param array $data           Additional data such as API secret keys. See the method to learn which keys are required.
     *
     * @return string|null JSON or null.
     */
    public function conductTranslationTest($text, $serviceType, $from, $to, $data = []) {
        $text = wp_unslash($text);
        $textTranslator = new TextTranslator([$text]);
        $translated = [];
        $message = '';

        switch($serviceType) {
            case TextTranslator::KEY_GOOGLE_CLOUD_TRANSLATION:
                $projectId  = Utils::array_get($data, "projectId");
                $apiKey     = Utils::array_get($data, "apiKey");

                $translated = $textTranslator->translateWithGoogle($projectId, $apiKey, $to, $from);

                $message = sprintf('<b>%1$s</b>: %2$s, <b>%3$s</b>: %4$s',
                    _wpcc("API Key"), $apiKey, _wpcc("Project ID"), $projectId);

                break;

            case TextTranslator::KEY_MICROSOFT_TRANSLATOR_TEXT:
                $clientSecret = Utils::array_get($data, "clientSecret");

                $translated = $textTranslator->translateWithMicrosoft($clientSecret, $to, $from);

                $message = sprintf('<b>%1$s</b>: %2$s', _wpcc("Client Secret"), $clientSecret);

                break;

            default:
                return null;
        }

        // Add the errors to the result, if there are any
        if($errors = $textTranslator->getErrors()) {
            $descriptions = $textTranslator->getErrorDescriptions();
            $translated = array_merge($descriptions, $translated);
        }

        $message = sprintf('<b>%1$s</b>: %2$s, <b>%3$s</b>: %4$s, %5$s',
            _wpcc("From"), ($from == 'detect' ? _wpcc("Detect") : $from), _wpcc("To"), $to, $message);
        $message = _wpcc("Translation test results with") . " " . $message;

        return json_encode([
            'view'  =>  Utils::view('partials/test-result')
                ->with("results", $translated)
                ->with("message", $message)
                ->render(),
            'data'  =>  $translated
        ]);
    }

    /*
     *
     */

    /**
     * @param callable    $callback        A callable that takes a {@link Crawler} and a {@link PostBot} as parameter
     *                                     and returns a manipulated {@link Crawler}. {@link PostBot} is the bot that
     *                                     is used to get the data from the target URL and it can be used to manipulate
     *                                     the content. <b>E.g. function($crawler, $bot) { return $crawler; }</b>
     * @param string      $url             Target URL on which the test should be conducted. If this does not exist,
     *                                     you must provide a <b>$content</b>.
     * @param string      $content         Target content in which the test should conducted. If this does not exist,
     *                                     you must provide a <b>$url</b>.
     * @param string      $selector        CSS selector that will be used to find elements in the target page/content
     * @param null|bool   $useUtf8         See {@link AbstractBot::__construct}
     * @param string|null $messageLastPart This will be used in place of <b>%2$s</b> here: <b>'Test results for %1$s
     *                                     with %2$s'</b>
     * @param null|string $attr            Target attribute of HTML elements. If this exists, instead of HTML, the
     *                                     target attribute's value will be added to the results.
     * @return string JSON
     * @throws \Exception If $callback is not a valid callback.
     */
    private function handleHtmlManipulationTest($callback, $url, $content, $selector, $useUtf8 = null, $messageLastPart, $attr = null) {
        // Make sure the callback exists and valid.
        if(!is_callable($callback)) throw new \Exception("You must provide a valid callback.");

        $results = [];
        if($selector) {
            if($url || $content) {
                // Create a dummy bot to get the client.
                $bot = new PostBot([], null, $useUtf8);

                $addResults = function ($crawler) use (&$bot, &$results, &$selector, &$callback, &$attr) {
                    $crawler = call_user_func($callback, $crawler, $bot);

                    /** @var Crawler $crawler */
                    $crawler->filter($selector)->each(function ($node, $i) use (&$results, &$attr) {
                        /** @var Crawler $node */
                        $result = $attr ? $node->attr($attr) : Utils::getNodeHTML($node);
                        if($result) $results[] = $result;
                    });
                };

                if($url) {
                    $crawler = $bot->request($url);
                    if($crawler) call_user_func($addResults, $crawler);
                }

                if($content) {
                    $content = wp_unslash($content);

                    // Remove html, body and head tags
                    $content = str_replace(['</html>', '</body>', '</head>'], '', $content);
                    $regexFormat = '<%1$s>|<%1$s\s[^>]+>';
                    $content = $this->findAndReplaceSingle(sprintf($regexFormat, 'html'), '', $content, true);
                    $content = $this->findAndReplaceSingle(sprintf($regexFormat, 'body'), '', $content, true);
                    $content = $this->findAndReplaceSingle(sprintf($regexFormat, 'head'), '', $content, true);

                    // Create a dummy crawler
                    $dummyCrawler = $bot->createDummyCrawler($content);
                    if($dummyCrawler) call_user_func($addResults, $dummyCrawler);
                }

                $message = sprintf(
                    _wpcc('Test results for %1$s with %2$s'),
                    sprintf('%1$s %2$s %3$s',
                        $url ? "<span class='highlight url'>" . $url . "</span>" : '',
                        $url && $content ? _wpcc("and") : '',
                        $content ? _wpcc("test code") : ''
                    ),
                    $messageLastPart ? $messageLastPart : ''
                );

                // Remove unnecessary spaces
                $message = $this->findAndReplaceSingle('\s{2,}', ' ', $message, true);

            } else {
                $message = _wpcc("URL and/or content must exist to conduct the test.");
            }

        } else {
            $message = _wpcc("You must provide a valid CSS selector.");
        }

        return json_encode([
            'view'  =>  Utils::view('partials/test-result')
                ->with("results", $results)
                ->with("message", $message)
                ->render(),
            'data'  =>  $results
        ]);
    }

    /*
     *
     */

    /**
     * Respond to AJAX requests made for testing things.
     *
     * @param array $data
     * @return null|string If request could not be handled, null. Otherwise, JSON.
     */
    public static function respondToTestRequest($data) {
        $testType = Utils::array_get($data, "testType");
        $serializedValues = Utils::array_get($data, "serializedValues");

        if(!$testType) return null;

        // If it exists, get the name of the form item. Some tests do not require a form item.
        $formItemName = $serializedValues ? Utils::array_get($data, "formItemName") : null;

        // If it exists, get the form item values unserialized.
        $formItemValues = null;
        if($formItemName && $serializedValues) {
            // Parse the serialized string to get the values as an array.
            parse_str($serializedValues, $formItemValues);

            // When the serialized string is parsed, the values will be under the name of the form item. So, since we
            // need the values directly, let's extract them.
            $formItemValues = Utils::array_get($formItemValues, $formItemName);

            // If the form item values has only 1 item inside, get it directly. Because, the values are inside the first
            // item.
            if($formItemValues && is_array($formItemValues) && sizeof($formItemValues) == 1) {
                $formItemValues = array_values($formItemValues)[0];
            }
        }
        
        $useUtf8Val = Utils::array_get($data, "useUtf8");
        $useUtf8 = $useUtf8Val == -1 ? null : ($useUtf8Val == 1 ? true : false);

        switch($testType) {
            case Test::$TEST_TYPE_FIND_REPLACE:
                // Here, form item values must be an array.
                if(!$formItemValues || !is_array($formItemValues)) return null;

                return Factory::test()->conductFindReplaceTest(
                    Utils::array_get($data, "subject"),
                    Utils::array_get($formItemValues, "find"),
                    Utils::array_get($formItemValues, "replace"),
                    isset($formItemValues["regex"])
                );

            case Test::$TEST_TYPE_SOURCE_CODE:
                return Factory::test()->conductSourceCodeTest(
                    Utils::array_get($data, "url"),
                    Utils::array_get($data, "removeScripts"),
                    Utils::array_get($data, "removeStyles"),
                    Utils::array_get($data, 'cookies'),
                    $useUtf8
                );

            case Test::$TEST_TYPE_PROXY:
                // Here, form item values must be a string and it must contain the proxies.
                if(!$formItemValues || is_array($formItemValues)) return null;

                return Factory::test()->conductProxyTest(
                    $formItemValues,
                    Utils::array_get($data, "url")
                );

            case Test::$TEST_TYPE_FIND_REPLACE_IN_ELEMENT_ATTRIBUTES:
                if(!$formItemValues || !is_array($formItemValues)) return null;

                return Factory::test()->conductFindReplaceInElementAttributesTest(
                    Utils::array_get($data, "url"),
                    Utils::array_get($data, "subject"),
                    Utils::array_get($formItemValues, "selector"),
                    Utils::array_get($formItemValues, "attr"),
                    Utils::array_get($formItemValues, "find"),
                    Utils::array_get($formItemValues, "replace"),
                    isset($formItemValues["regex"]),
                    $useUtf8
                );

            case Test::$TEST_TYPE_EXCHANGE_ELEMENT_ATTRIBUTES:
                if(!$formItemValues || !is_array($formItemValues)) return null;

                return Factory::test()->conductExchangeElementAttributesTest(
                    Utils::array_get($data, "url"),
                    Utils::array_get($data, "subject"),
                    Utils::array_get($formItemValues, "selector"),
                    Utils::array_get($formItemValues, "attr1"),
                    Utils::array_get($formItemValues, "attr2"),
                    $useUtf8
                );

            case Test::$TEST_TYPE_REMOVE_ELEMENT_ATTRIBUTES:
                if(!$formItemValues || !is_array($formItemValues)) return null;

                return Factory::test()->conductRemoveElementAttributesTest(
                    Utils::array_get($data, "url"),
                    Utils::array_get($data, "subject"),
                    Utils::array_get($formItemValues, "selector"),
                    Utils::array_get($formItemValues, "attr"),
                    $useUtf8
                );

            case Test::$TEST_TYPE_FIND_REPLACE_IN_ELEMENT_HTML:
                if(!$formItemValues || !is_array($formItemValues)) return null;

                return Factory::test()->conductFindReplaceInElementHtmlTest(
                    Utils::array_get($data, "url"),
                    Utils::array_get($data, "subject"),
                    Utils::array_get($formItemValues, "selector"),
                    Utils::array_get($formItemValues, "find"),
                    Utils::array_get($formItemValues, "replace"),
                    isset($formItemValues["regex"]),
                    $useUtf8
                );

            case Test::$TEST_TYPE_FIND_REPLACE_IN_CUSTOM_META:
            case Test::$TEST_TYPE_FIND_REPLACE_IN_CUSTOM_SHORT_CODE:
                if(!$formItemValues || !is_array($formItemValues)) return null;

                return Factory::test()->conductFindReplaceInCustomMetaAndShortCodeTest(
                    Utils::array_get($formItemValues, 'meta_key', Utils::array_get($formItemValues, 'short_code')),
                    Utils::array_get($data, "url"),
                    Utils::array_get($data, "subject"),
                    Utils::array_get($data, "valueSelector"),
                    Utils::array_get($data, "valueSelectorAttr", Utils::array_get($data, "attr")),
                    Utils::array_get($formItemValues, "find"),
                    Utils::array_get($formItemValues, "replace"),
                    isset($formItemValues["regex"]),
                    $useUtf8
                );

            case Test::$TEST_TYPE_TRANSLATION:
                // Here, form item values must be a string and it must contain the test text to be translated.
                if(!$formItemValues || is_array($formItemValues)) return null;

                return Factory::test()->conductTranslationTest($formItemValues,
                    Utils::array_get($data, "serviceType"),
                    Utils::array_get($data, "from"),
                    Utils::array_get($data, "to"),
                    $data
                );

                break;

            default:
                // By default, we will handle all selector tests.
                // Here, form item values must exist.
                if(!$formItemValues) return null;

                // If form item values is not an array, it means it is a string and that string is actually the selector.
                // So, prepare it that way.
                if(!is_array($formItemValues)) {
                    $formItemValues = ["selector" => $formItemValues];
                }

                $attr = Utils::array_get($formItemValues, "attr");
                if(!$attr) $attr = Utils::array_get($data, "attr");

                return Factory::test()->conductSelectorTest(
                    Utils::array_get($data, "url"),
                    Utils::array_get($formItemValues, "selector"),
                    $testType,
                    $attr,
                    Utils::array_get($data, "content"),
                    $useUtf8
                );
        }

        return null;
    }
    
}