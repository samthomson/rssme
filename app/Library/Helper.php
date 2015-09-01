<?php

namespace App\Library;


use App\Feeds\FeedItem;

use Carbon\Carbon;
use DOMDocument;

use App\Library\Helper;

use App\Http\Controllers\Feeds;

class Helper
{    

    public static function sRandomUserFeedColour()
    {
        // define a bunch of colours

        $saColours = [
        'turquoise' => '1abc9c',
        'emerald' => '2ecc71',
        'peter river' => '3498db',
        'amethyst' => '9b59b6',
        'wet asphalt' => '34495e',        
        'green sea' => '16a085',
        'nephritus' => '27ae60',
        'belize hole' => '2980b9',
        'wisteria' => '8e44ad',
        'midnight blue' => '2c3e50',        
        'sun flower' => 'f1c40f',
        'carrot' => 'e67e22',
        'alizarin' => 'e74c3c',
        'clouds' => 'ecf0f1',
        'concrete' => '95a5a6',        
        'orange' => 'f39c12',
        'pumpkin' => 'd35400',
        'pomegranite' => 'c0392b',
        'silver' => 'bdc3c7',
        'asbestos' => '7f8c8d'
        ];

        $saKeys = array_keys($saColours);

        return $saColours[$saKeys[mt_rand(0, count($saColours))]];
    }

    public static function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }
    public static function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    public static function getFeedStructureFromXML($oRssFeed, $sXMLString, $sStopAt)
    {
        // called when parsing rss feeds, tries to convert a mixed xml string into a structured object
        $oScrapedFeed = new \StdClass();
        $oScrapedFeed->aoItems = [];

        $xmlFeed = simplexml_load_string($sXMLString);

        $bStopImport = false;

        $sFeedtype = "1.0";

        $oType = $xmlFeed->attributes()->version;

        if(isset($oType))
            if($oType == "2.0")
                $sFeedtype = $oType;

        switch($sFeedtype)
        {
            case "1.0":

                if(isset($xmlFeed->entry))
                {
                    foreach ($xmlFeed->entry as $oItem) {

                        echo "item", "<br/>";
                        if ($sStopAt !== null) {
                            //echo "last: ", $sStopAt, "<br/>";
                            if ((string)$sStopAt === (string)$oItem->id) {
                                // skip this itemoces
                                echo "<strong>", "skip these items", $oItem->id, "</strong>", "<br/>";
                                $bStopImport = true;
                            }
                        }

                        $oExistingItemAlready = FeedItem::where("guid", "=", (string)$oItem->id)->first();

                        if (isset($oExistingItemAlready)) {
                            $bStopImport = true;
                        }

                        if (!$bStopImport) {

                            $oTempFeedItem = new \StdClass;

                            $oFeedItem = new FeedItem;
                            $oFeedItem->title = $oItem->title;
                            $oFeedItem->url = XMLHelper::sXMLAttributeValue($oItem->link, 'href');
                            $oFeedItem->guid = $oItem->id;

                            $cdFeedPubDate = new Carbon($oItem->updated);
                            $oFeedItem->pubDate = $cdFeedPubDate->toDateTimeString();

                            $oThumbItem = $oItem->{'media:thumbnail'};

                            $bPic = false;
                            $sPicURL = '';

                            if (isset($oItem->children('media', true)->thumbnail)) {

                                if (isset($oItem->children('media', true)->thumbnail->attributes()->url)) {
                                    $bPic = true;
                                    $sPicURL = $oItem->children('media', true)->thumbnail->attributes()->url;
                                }
                            }

                            if (!$bPic) {
                                if (isset($oItem->enclosure)) {
                                    if (isset($oItem->enclosure['url'])) {
                                        $oFeedItem->thumb = $oItem->enclosure['url'];
                                        $bPic = true;
                                    }
                                }
                            }

                            // still no pic? resort to scanning for img in item
                            if (!$bPic) {
                                preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $oItem->asXml(), $matches);
                                foreach ($matches[1] as $key => $value) {
                                    $sPicURL = $value;
                                    $bPic = true;
                                    break;
                                }
                            }

                            // still no pic? look for og:image in downloaded webpage...!

                            $oFeedItem->thumb = '';

                            $oFeedItem->feed_id = $oRssFeed->id;
                            $oFeedItem->save();

                            array_push($oScrapedFeed->aoItems, $oFeedItem);

                            if (!$bPic) {

                                //self::scrapeThumbFromFeedItem($oFeedItem->id);
                                Feeds::scheduleFeedItemImageScrape($oFeedItem->id);

                            } else {
                                Feeds::scheduleThumbCrunch($sPicURL, $oFeedItem->id);
                            }


                        } else {
                            //echo "skipped an item?<br/>";
                        }
                    }
                }
                break;
            case "2.0":
                // look for feed image
                if(isset($xmlFeed->channel->image->url)){
                    $oFeed->thumb = $xmlFeed->channel->image->url;
                }
                // look for feed items
                if(isset($xmlFeed->channel->item))
                {
                    foreach ($xmlFeed->channel->item as $oItem) {

                        echo "item", "<br/>";
                        if ($sStopAt !== null) {
                            //echo "last: ", $sStopAt, "<br/>";
                            if ((string)$sStopAt === (string)$oItem->guid) {
                                // skip this itemoces
                                echo "<strong>", "skip these items", $oItem->guid, "</strong>", "<br/>";
                                $bStopImport = true;
                            }
                        }

                        $oExistingItemAlready = FeedItem::where("guid", "=", (string)$oItem->guid)->first();

                        if (isset($oExistingItemAlready)) {
                            $bStopImport = true;
                        }

                        if (!$bStopImport) {

                            $oTempFeedItem = new \StdClass;

                            $oFeedItem = new FeedItem;
                            $oFeedItem->feed_id = $oFeed->id;
                            $oFeedItem->title = $oItem->title;
                            $oFeedItem->url = $oItem->link;
                            $oFeedItem->guid = $oItem->guid;

                            $cdFeedPubDate = new Carbon($oItem->pubDate);
                            $oFeedItem->pubDate = $cdFeedPubDate->toDateTimeString();

                            $oThumbItem = $oItem->{'media:thumbnail'};

                            $bPic = false;
                            $sPicURL = '';

                            if (isset($oItem->children('media', true)->thumbnail)) {

                                if (isset($oItem->children('media', true)->thumbnail->attributes()->url)) {
                                    $bPic = true;
                                    $sPicURL = $oItem->children('media', true)->thumbnail->attributes()->url;
                                }
                            }

                            if (!$bPic) {
                                if (isset($oItem->enclosure)) {
                                    if (isset($oItem->enclosure['url'])) {
                                        $oFeedItem->thumb = $oItem->enclosure['url'];
                                        $bPic = true;
                                    }
                                }
                            }

                            // still no pic? resort to scanning for img in item
                            if (!$bPic) {
                                preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $oItem->asXml(), $matches);
                                foreach ($matches[1] as $key => $value) {
                                    $sPicURL = $value;
                                    $bPic = true;
                                    break;
                                }
                            }

                            // still no pic? look for og:image in downloaded webpage...!

                            $oFeedItem->thumb = '';

                            $oFeedItem->feed_id = $oRssFeed->id;
                            $oFeedItem->save();

                            array_push($oScrapedFeed->aoItems, $oFeedItem);

                            if (!$bPic) {

                                //self::scrapeThumbFromFeedItem($oFeedItem->id);
                                self::scheduleFeedItemImageScrape($oFeedItem->id);

                            } else {
                                self::scheduleThumbCrunch($sPicURL, $oFeedItem->id);
                            }


                        } else {
                            //echo "skipped an item?<br/>";
                        }
                    }
                }
                break;
        }




        return $oScrapedFeed;
    }

}

class XMLHelper
{

    public static function sXMLAttributeValue($oObject, $sAttribute)
    {
        if(isset($oObject[$sAttribute]))
            return (string) strtolower(trim($oObject[$sAttribute]));
    }
    public static function sXMLValueByAttribute($xmlParent, $sSearchNodeName, $sSearchAttribute)
    {
        foreach($xmlParent->{$sSearchNodeName} as $xmlNode)
        {
            if(isset($xmlNode['Type'])) {
                if((string)$xmlNode['Type'] === $sSearchAttribute) {

                    return (string)$xmlNode;

                }
            }
        }
    }
    public static function sXMLValue($xmlNode)
    {
        return (string)strtolower(trim($xmlNode));
    }
}