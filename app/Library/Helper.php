<?php

namespace App\Library;


use App\Models\Feeds\FeedItem;

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

        $oParsedFeed = self::oXMLStringToFeedObject($sXMLString);

        // go through each feed item and save into our system
        foreach($oParsedFeed->aoItems as $oDataOnlyFeedItem)
        {
            $oFeedItem = new FeedItem;
            $oFeedItem->title = $oDataOnlyFeedItem->title;
            $oFeedItem->url = $oDataOnlyFeedItem->url;
            $oFeedItem->guid = $oDataOnlyFeedItem->guid;

            $oFeedItem->pubDate = $oDataOnlyFeedItem->pubDate;


            $oFeedItem->feed_id = $oRssFeed->id;

            /*
            if ($oParsedFeed->bPic) {
                $oFeedItem->thumb = $oDataOnlyFeedItem->thumb;
            }
            */

            $oFeedItem->save();


            // schedule crunching our scraping thumb..
            if (!$oParsedFeed->bPic) {
                // schedule for image scrape
                Feeds::scheduleFeedItemImageScrape($oFeedItem->id);
            }else{
                // we have a thumbnail but need to make sure it's the correct size
                Feeds::scheduleThumbCrunch($oFeedItem->sPicURL, $oFeedItem->id);
            }


            $oFeedItem->save();
        }

        return $oParsedFeed;
    }

    public static function oXMLStringToFeedObject($sXMLString)
    {
        // take xml feed and turn into structured object

        // remove leading whitespace
        $sXMLString = ltrim($sXMLString);

        $bStopImport = false;
        $xmlFeed = simplexml_load_string($sXMLString);

        $oScrapedFeed = new \StdClass();
        $oScrapedFeed->aoItems = [];
        $oScrapedFeed->sFeedtype = "1.0";

        $oScrapedFeed->bPic = false;
        $oScrapedFeed->sPicURL = '';

                            

        $oType = $xmlFeed->attributes()->version;

        if(isset($oType))
            if($oType == "2.0")
                $oScrapedFeed->sFeedtype = $oType;

        switch($oScrapedFeed->sFeedtype)
        {
            case "1.0":

                if(isset($xmlFeed->entry))
                {
                    foreach ($xmlFeed->entry as $oItem)
                    {                        
                        $oFeedItem = new \StdClass;
                        $oFeedItem->title = $oItem->title;
                        $oFeedItem->url = XMLHelper::sXMLAttributeValue($oItem->link, 'href');
                        $oFeedItem->guid = $oItem->id;

                        $oFeedItem->pubDate = Carbon::parse($oItem->updated)->toDateTimeString();

                        $oThumbItem = $oItem->{'media:thumbnail'};

                        if (isset($oItem->children('media', true)->thumbnail))
                        {
                            if (isset($oItem->children('media', true)->thumbnail->attributes()->url))
                            {
                                // has an actual thumbnail
                                $sPicUrl = (string)$oItem->children('media', true)->thumbnail->attributes()->url;

                                if($sPicUrl !== '')
                                {
                                    $oFeedItem->bPic = true;
                                    $oFeedItem->sPicURL = $sPicUrl;
                                }
                            }
                        }

                        if (!$oScrapedFeed->bPic)
                        {
                            if (isset($oItem->enclosure))
                            {
                                if (isset($oItem->enclosure['url']))
                                {
                                    $sPicUrl = (string)$oItem->enclosure['url'];

                                    if($sPicUrl !== '')
                                    {
                                        $oFeedItem->bPic = true;
                                        $oFeedItem->sPicURL = $sPicUrl;
                                    }

                                }
                            }
                        }

                        // still no pic? resort to scanning for img in item
                        if (!$oScrapedFeed->bPic)
                        {
                            preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $oItem->asXml(), $matches);
                            foreach ($matches[1] as $key => $value)
                            {
                                $sPicUrl = (string)$value;

                                if($sPicUrl !== '')
                                {
                                    $oFeedItem->bPic = true;
                                    $oFeedItem->sPicURL = $sPicUrl;
                                }
                                break;
                            }
                        }

                        array_push($oScrapedFeed->aoItems, $oFeedItem);
                    }
                }

                break;
            case "2.0":
                // look for feed image
                if(isset($xmlFeed->channel->image->url)){
                    $oScrapedFeed->thumb = $xmlFeed->channel->image->url;
                }
                // look for feed items
                if(isset($xmlFeed->channel->item))
                {
                    foreach ($xmlFeed->channel->item as $oItem)
                    {

                        $oFeedItem = new \StdClass;
                        $oFeedItem->title = $oItem->title;
                        $oFeedItem->url = $oItem->link;
                        $oFeedItem->guid = $oItem->guid;

                        $oFeedItem->pubDate = Carbon::parse($oItem->pubDate)->toDateTimeString();

                        $oThumbItem = $oItem->{'media:thumbnail'};

                        if (isset($oItem->children('media', true)->thumbnail)) {

                            if (isset($oItem->children('media', true)->thumbnail->attributes()->url)) {
                                $sPicUrl = (string)$oItem->children('media', true)->thumbnail->attributes()->url;

                                if($sPicUrl !== '')
                                {
                                    $oFeedItem->bPic = true;
                                    $oFeedItem->sPicURL = $sPicUrl;
                                }
                            }
                        }

                        if (!$oScrapedFeed->bPic) {
                            if (isset($oItem->enclosure)) {
                                if (isset($oItem->enclosure['url'])) {

                                    $sPicUrl = (string)$oItem->enclosure['url'];

                                    if($sPicUrl !== '')
                                    {
                                        $oFeedItem->bPic = true;
                                        $oFeedItem->sPicURL = $sPicUrl;
                                    }
                                }
                            }
                        }

                        // still no pic? resort to scanning for img in item
                        if (!$oScrapedFeed->bPic)
                        {
                            preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $oItem->asXml(), $matches);
                            foreach ($matches[1] as $key => $value)
                            {
                                $sPicUrl = (string)$value;

                                if($sPicUrl !== '')
                                {
                                    $oFeedItem->bPic = true;
                                    $oFeedItem->sPicURL = $sPicUrl;
                                }


                                break;
                            }
                        }

                        array_push($oScrapedFeed->aoItems, $oFeedItem);
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