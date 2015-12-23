<?php

namespace App\Http\Controllers;

#use Illuminate\Http\Request;

use App\Models\Feeds\Feed;
use App\Models\Feeds\UserFeed;
use App\Models\Feeds\FeedItem;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Request;


use Auth;
use Carbon\Carbon;
use DB;
use App\Library\Helper;
use App\Auto\Task;

use Intervention\Image\Facades\Image;

use DOMDocument;
use FastFeed\Factory;

class Feeds extends Controller
{    

    public static function scheduleFeedPull($iFeedId, $iMinutes = 0)
    {
        $oTask = new Task;
        $oTask->processFrom = Carbon::now()->addMinutes($iMinutes);
        $oTask->job = "pull-feed";
        $oTask->detail = $iFeedId;
        $oTask->save();
    }
    public static function scheduleFeedItemImageScrape($iFeedItemId)
    {
        $oTask = new Task;
        $oTask->processFrom = Carbon::now();
        $oTask->job = "scrape-feed-item-image";
        $oTask->detail = $iFeedItemId;
        $oTask->save();
    }

    public static function scheduleThumbCrunch($sThumbUrl, $iFeedItemId)
    {
        $oTask = new Task;
        $oTask->processFrom = Carbon::now();
        $oTask->job = "crunch-feed-image";
        $oTask->name = $sThumbUrl;
        $oTask->detail = $iFeedItemId;
        $oTask->save();
    }

    public static function test()
    {
        // list of rss feeds
        $saFeedUrls = [
            'github' => 44,
            'nat geo' => 15
        ];


        foreach($saFeedUrls as $sFeedName => $iFeedId)
        {
            self::pullFeed($iFeedId);
        }
    }
    public static function create()
    {
        if (Request::has('feedurl') && Request::has('feedname')){

            self::createUniqueUserFeed(Request::get('feedurl'), Request::get('feedname'));

            return response("ok", 200);
        }else{
            return response("bad credentials", 200);
        }
    }
    private static function createUniqueUserFeed($sFeedUrl, $sFeedName, $bScheduleImmediatePull = true)
    {
        // creates a feed url, if there's no feed for current user for url, create it

        // get id of a feed (new or existing)
        $oFeed = Feed::where("url", $sFeedUrl)->first();

        $iFeedId = -1;

        if(!isset($oFeed)){
            $oFeed = new Feed;

            $oFeed->url = $sFeedUrl;

            $oFeed->save();
            $iFeedId = $oFeed->id;

            // pull it
            if($bScheduleImmediatePull){
                // make sure it's in line to be crawled, unless we're calling this from a test stub
                self::scheduleFeedPull($iFeedId);
            }
        }else{
            $iFeedId = $oFeed->id;
        }

        $oUserFeed = new UserFeed;
        $oUserFeed->feed_id = $iFeedId;
        $oUserFeed->user_id = Auth::id();
        $oUserFeed->name = $sFeedName;
        $oUserFeed->colour = Helper::sRandomUserFeedColour();
        $oUserFeed->save();
    }

    public static function update($iUserFeedId)
    {
        // look up item and
        $oUserFeed = UserFeed::find($iUserFeedId);
        //->with('feed');

        if(isset($oUserFeed)){
            if($oUserFeed->user_id == Auth::id()){
                // feed item found, and owned by logged in user
                if(Request::has('feedname'))
                    $oUserFeed->name = Request::get('feedname');

                $oUserFeed->save();
            }
        }
        return response("ok", 200);
    }

    public static function edit($iUserFeedId)
    {
        // look up item and
        $oUserFeed = UserFeed::find($iUserFeedId);
        //->with('feed');

        if(isset($oUserFeed)){
            if($oUserFeed->user_id == Auth::id()){
                // feed item found, and owned by logged in user
                return view('app.feeds.edit', ['oUserFeed' => $oUserFeed]);
            }
        }
        echo "no";exit();
    }

    public static function delete($iUserFeedId)
    {
        // delete the pivot relation  
        $oFeedUser = UserFeed::where("id", $iUserFeedId)->where("user_id", Auth::id())->first();

        $iFeedId = $oFeedUser->feed_id;

        $oFeedUser->delete();

        // if this user was the last/only with that feed, delete the feed
        $oaFeed = Feed::where("id", $iFeedId)->get();

        if(count($oaFeed) == 1){
            $oaFeed[0]->delete();
        }
        return response("ok", 200);
    }

    public static function feedsAndCategories()
    {
        \DB::enableQueryLog();

        $oQuery = DB::table('feed_user')
            ->join('feeditems', function($join)
            {
                $join->on('feed_user.feed_id', '=', 'feeditems.feed_id')
                    ->where('feed_user.user_id', '=', Auth::id());

                if(Request::has('feed')){
                    $join->where("feeditems.feed_id", "=", Request::get('feed'));
                }
            })
            ->join('feeds', "feeds.id", "=", "feed_user.feed_id");
        $oQuery->orderBy('feeditems.pubDate', 'desc')
            ->select(['feeditems.url as url', 'feeditems.title as title', 'feeds.url as feedurl', 'feeds.id as feed_id', 'feeditems.pubDate as date', 'feed_user.name as name', 'feeditems.thumb as thumb', 'feeds.thumb as feedthumb', 'feed_user.colour as feed_colour']);




        $iPage = Request::input("page", 1);
        $iPerPage = 20;

        $iTotalItems = 0;

        //$maFeedItems = $oQuery->skip(($iPage * $iPerPage)-$iPerPage)->take($iPerPage)->get();
        $maFeedItems = $oQuery->get();

        $iTotalItems = count($maFeedItems);
        $iTotalPages = ceil($iTotalItems / $iPerPage);

        $maFeedItems = array_slice($maFeedItems, ($iPage * $iPerPage)-$iPerPage, $iPerPage);

        //print_r(DB::getQueryLog());


        $oaFeedItems = [];

        foreach ($maFeedItems as $oFeedItem) {

            $sDate = '';
            $oDate = new Carbon($oFeedItem->date);
            if($oDate->isToday())
                // 10:41 pm
                $sDate = $oDate->format('g:i a');
            else
                // Aug 12
                $sDate = $oDate->format('M j');

            array_push($oaFeedItems, 
                [
                "url" => $oFeedItem->url,
                "title" => $oFeedItem->title,
                "feedurl" => $oFeedItem->feedurl,
                "feed_id" => $oFeedItem->feed_id,
                "date" => $sDate,
                "name" => $oFeedItem->name,
                "thumb" => $oFeedItem->thumb !== '' ? /*'http://rssme.samt.st'.*/$oFeedItem->thumb : $oFeedItem->feedthumb,
                "feed_thumb" => $oFeedItem->feedthumb
                ]
                );
        }

        if(Request::has('feed')){
            $oQuery->where("feeds.id", "=", Request::get('feed'));
        }

        $oaFeeds = Auth::user()->userFeeds;
        $oaFeeds->load('feed');

        $oData = [
            'iAvailablePages' => $iTotalPages,
            'iPerPage' => $iPerPage
        ];


        return response()->json([
            'jsonFeedItems' => $oaFeedItems,
            'jsonFeeds' => $oaFeeds,
            'data' => $oData
        ]);

        //return response(['jsonFeedItems' => $oaFeedItems, 'jsonFeeds' => $oaFeeds], 200);
    }
    public  static function serveAngularApp()
    {
        return view('app.home');
    }

    public static function storeThumbForFeedItem($oFeedItem, $sRemoteThumbUrl){
        $iFeedItemId = $oFeedItem->id;
        $sLocalThumbPath = '';

        if(isset($sRemoteThumbUrl)){
            // download locally and make a small thumb, if it's a jpeg
            if(Helper::endsWith(strtolower($sRemoteThumbUrl), '.jpg')){
                try
                {
                    $oImage = @Image::make($sRemoteThumbUrl);
                
                    $oImage->fit(48,32);
                    $sRelPath = DIRECTORY_SEPARATOR.'thumbs'.DIRECTORY_SEPARATOR.$iFeedItemId.'.jpg';
                    $oImage->save(public_path().$sRelPath);

                    $sLocalThumbPath = $sRelPath;

                }
                catch(\Intervention\Image\Exception\NotReadableException $e)
                {
                    echo "<br/>not readable<br/>";
                }
            }
        }
        $oFeedItem->thumb = str_replace(DIRECTORY_SEPARATOR, '/', $sLocalThumbPath);
        $oFeedItem->save();
    }

    public static function scrapeThumbFromFeedItem($iFeedItemId){
        try{
            $oFeedItem = FeedItem::find($iFeedItemId);

            $sUrlToHit = $oFeedItem->url;
            ////echo "scrape: ", $sUrlToHit, "<br/>";
            $page_content = @file_get_contents($sUrlToHit);


            if(!empty($page_content))
            {
                $dom_obj = new DOMDocument();
                libxml_use_internal_errors(true);
                $dom_obj->loadHTML($page_content);
                $meta_val = null;

                foreach($dom_obj->getElementsByTagName('meta') as $meta) {

                    if($meta->getAttribute('property')=='og:image'){ 

                        $meta_val = $meta->getAttribute('content');

                        break;
                    }
                }
                if(isset($meta_val))
                    self::storeThumbForFeedItem($oFeedItem, $meta_val);
                else
                {
                    $oFeedItem->thumb = '';
                    $oFeedItem->save();
                }   
            }

        }catch(Exception $e){
            
        }
    }

    public static function pullFeed($id){
        $oFeed = Feed::find($id);

        if(isset($oFeed))
        {
            try{

                echo "pulling: ", $oFeed->url, "<br/>";

                // get the guid of the last pulled item so we know where to stop

                $oFeedItem = $oFeed->feedItems->first();

                // stop at null, unless we have some feed items already, then stop at most recent
                $sStopAt = null;
                if(isset($oFeedItem)){
                    // there are already items from this feed
                    $sStopAt = $oFeedItem->guid;
                }

                $context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));

                
                $xmlFeed = @file_get_contents($oFeed->url, false, $context);

                $iItemsFetched = 0;

                if(!empty($xmlFeed))
                {
                    $oScrapedFeed = Helper::getFeedStructureFromXML($oFeed, $xmlFeed, $sStopAt);

                    $iItemsFetched += count($oScrapedFeed->aoItems);
                    if(isset($oScrapedFeed->thumb))
                        $oFeed->thumb = $oScrapedFeed->thumb;



                }else{
                    // todo: failed to fetch feed
                }

                $oFeed->lastPulledCount = $iItemsFetched;

                $oFeed->hit_count = $oFeed->hit_count + 1;
                $oFeed->item_count = $oFeed->item_count + $iItemsFetched;

                $mytime = Carbon::now();

                $oFeed->lastPulled = $mytime->toDateTimeString();
                $oFeed->save();
            }catch(Exception $e){
                echo "fetching feed (", $oFeed->id, ") ", $oFeed->url, " failed", "<br/>";
            }
        }
    }

    public static function pullAll()
    {
        $oaFeeds = Feed::all();

        foreach ($oaFeeds as $oFeed) {
            self::pullFeed($oFeed->id);
            echo "<hr/>";
        }
    }

    public static function removeColonsFromRSS($feed) {
        // pull out colons from start tags
        // (<\w+):(\w+>)
        $pattern = '/(<\w+):(\w+>)/i';
        $replacement = '$1$2';
        $feed = preg_replace($pattern, $replacement, $feed);
        // pull out colons from end tags
        // (<\/\w+):(\w+>)
        $pattern = '/(<\/\w+):(\w+>)/i';
        $replacement = '$1$2';
        $feed = preg_replace($pattern, $replacement, $feed);
        return $feed;
    }


}