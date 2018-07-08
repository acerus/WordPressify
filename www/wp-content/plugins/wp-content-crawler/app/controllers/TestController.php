<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 14:58
 */

namespace WPCCrawler\controllers;


use GuzzleHttp\Psr7\Request;
use WPCCrawler\Constants;
use WPCCrawler\Factory;
use WPCCrawler\objects\page\AbstractMenuPage;
use WPCCrawler\objects\translation\PostTranslator;
use WPCCrawler\objects\translation\TextTranslator;
use WPCCrawler\Utils;

class TestController extends AbstractMenuPage {

    private static $GENERAL_TESTS;

    /**
     * @return string Menu title for the page
     */
    public function getMenuTitle() {
        return _wpcc('Tester');
    }

    /**
     * @return string Page title
     */
    public function getPageTitle() {
        return _wpcc('Tester');
    }

    /**
     * @return string Slug for the page
     */
    public function getPageSlug() {
        return 'site-tester';
    }

    /**
     * Get view for the page.
     *
     * @return mixed Not-rendered blade view for the page
     */
    public function getView() {
        /*
         * TESTS:
         * TODO: Remove this.
         */

//        Factory::schedulingService()->executeEventCollectUrls(true);

//        // Test URL preparation
//        $urls = [
//            "index1.html" => "http://www.abc.com",
//            "index2.html" => "http://www.abc.com/",
//            "abc/index3.html" => "http://www.abc.com/",
//            "/abc/index3.html" => "http://www.abc.com/",
//            "index4.html" => "http://www.abc.com/test/a.html",
//            "index5.html" => "http://www.abc.com/test/b/c.html",
//            "http://test.com/index5.html" => "http://www.abc.com/test/b/c.html",
//        ];
//
//        foreach($urls as $part => $currentUrl) {
//            var_dump(Utils::prepareUrl("http://www.test.com", $part, $currentUrl));
//        }

//        $urlTuple = Factory::databaseService()->getUrlById(21631);
//        $postSaver = new PostSaver();
//        $postSaver->executePostRecrawl($urlTuple);

//        Factory::schedulingService()->executeEventCollectUrls(true);
//        Factory::schedulingService()->executeEventCrawlPost(true);
//        Factory::schedulingService()->executeEventRecrawlPost(true);
//        Factory::schedulingService()->executeEventDeletePosts(true);

//        /*
//         *
//         */
//
//        $bot = new DummyBot(Settings::getAllGeneralSettings());
//        $crawler = $bot->request("http://www.hurriyet.com.tr/aziz-yildirim-silivride-40371178");
//
//        $dumpImageElements = function() use (&$crawler) {
//            $imageElements = [];
//            $crawler->filter("[itemProp=articleBody] img")->each(function($node) use (&$imageElements) {
//                $imageElements[] = Utils::getNodeHTML($node);
//            });
//
//            $crawler->filter("[itemProp=articleBody] .wpcc-image")->each(function($node) use (&$imageElements) {
//                $imageElements[] = Utils::getNodeHTML($node);
//            });
//
//            var_dump($imageElements);
//        };
//
//        echo "<textarea style='width: 100%; height: 500px;'>" . Utils::getNodeHTML($crawler->filter("[itemProp=articleBody]")->first()) . "</textarea>";
//        $dumpImageElements();
//
//        $bot->exchangeElementAttributeValues($crawler, ['[itemProp=articleBody] img[data-src]'], 'src', 'data-src');
//        $bot->removeElementAttributes($crawler, ['[itemProp=articleBody] img'], 'data-src,alt,    class  , data-inline-image, width, height, itemprop ');
//        $bot->findAndReplaceInElementAttribute($crawler, ['[itemProp=articleBody] img'], 'src', '([0-9]+)x([0-9]+)', '-$1-replaced-$2-', true);
//        $bot->findAndReplaceInElementAttribute($crawler, ['[itemProp=articleBody] img'], 'data-wpcc', '', 'test', true);
//        $bot->findAndReplaceInElementAttribute($crawler, ['[itemProp=articleBody] img'], 'class', '(.*)', '$1 wpcc-image', true);
//        $bot->findAndReplaceInElementHTML($crawler, ['[itemProp=articleBody] .wpcc-image'], '<img', '<div', true);
//        $bot->findAndReplaceInElementHTML($crawler, ['[itemProp=articleBody] .wpcc-image'], '', 'Hi! <b>Test</b>', true);
//
//        $dumpImageElements();
//        echo "<textarea style='width: 100%; height: 500px;'>" . Utils::getNodeHTML($crawler->filter("[itemProp=articleBody]")->first()) . "</textarea>";

//        var_dump(Factory::databaseService()->setUrlDeleted([79892, 79891, 79890, 79889]));
//        var_dump(Factory::databaseService()->getOldPostIdsForSite(15823, 6 * 60, 10));

//        $dates = [
//            // The date that'll be taken    The human readable date on the target site
//            '2017-01-18T17:40:59+00:00' => 'January 18, 2017 17:40',
//            '2017-01-15T09:06:11Z'      => '15 Ocak 2017 - 12:06',
//            '2017-02-09T02:07:44+11:00' => '9 February 17',
//            '2017-02-27T14:42:00+03:00' => '27.02.2017 - 14:42',
//            '2017-02-23T22:22:08.755Z'  => 'Feb 24',
//            '2017-01-19'                => 'Jan 19, 2017',
//            '2017-01-19 14:08:37'       => 'Jan 19, 2017',
//            '25 Feb 2017 | 14:00 GMT'   => '25 Feb 2017 | 14:00 GMT',
//        ];
//        $format = '<tr><td>%1$s</td> <td>%2$s</td> <td>%3$s</td></tr>';
//        $dateFormat = 'Y-m-d H:i:s';
//
//        echo "<table style='border-spacing: 10px;'><thead style='font-weight: bold;'>" . sprintf($format, "Target", "Value on target site", "Parsed") . "</thead><tbody>";
//        foreach($dates as $date => $expected) {
//            $dt = new DateTime();
//            $dt->setTimestamp(strtotime($date));
//
//            echo sprintf($format, $date, $expected, date($dateFormat, strtotime($date)) . " - DateTime: " . $dt->format($dateFormat) . " Timestamp: " . strtotime($date));
//
//        }
//        echo "</tbody></table>";
//
//        $arr = [10, -10, 20, -50];
//        var_dump($arr[array_rand($arr)] . " minute");

//        $fileUrl = 'http://www.spiethamerica.com/getmetafile/53677a1e-d2b5-4a0c-91c9-c6f2eb424a67/1407210-Ergojet-Vault-Table;.aspx/?maxsidesize=10000';
//        var_dump(basename($fileUrl));
//        var_dump(Utils::saveMedia($fileUrl));

//        Utils::saveMedia("https://olxro-ring02.akamaized.net/images_mercadorro/127167318_3_644x461_inchiriez-apartament-central-2-camere_rev003.jpg");

//        TextTranslator::test();

        /*
         *
         */

        // Register assets
        Factory::assetManager()->addTooltip();
        Factory::assetManager()->addSiteTester();

        // Get available sites
        $sites = get_posts(['post_type' => Constants::$POST_TYPE, 'numberposts' => -1]);

        return Utils::view('site-tester/main')->with([
            'sites' => $sites
        ]);
    }

    public function handleAJAX() {
        $data = parent::handleAJAX();

        // Show the test results
        echo Factory::test()->conductGeneralTest($data["site_id"], $data["test_type"], $data["test_url_part"]);
        return;
    }

    /*
     * HELPERS
     */

    /**
     * Get general test types. This method exists, because translations are not ready before the page renders.
     *
     * @return array General test types as title,value pair
     */
    public function getGeneralTestTypes() {
        if(!static::$GENERAL_TESTS) static::$GENERAL_TESTS = [
            _wpcc('Post Page')          =>  'test_post',
            _wpcc('Category Page')      =>  'test_category',
        ];

        return static::$GENERAL_TESTS;
    }
}