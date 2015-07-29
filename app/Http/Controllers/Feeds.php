<?php

namespace App\Http\Controllers;

#use Illuminate\Http\Request;

use App\Feeds\Feed;
use App\Feeds\UserFeed;
use App\Feeds\FeedItem;

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
        //self::scrapeThumbFromFeedItem(9);

        // re-schedule feeds to be crawled

        $iMinOffset = 0;
        foreach(Feed::all() as $oFeed)
        {
            self::scheduleFeedPull($oFeed->id, $iMinOffset);
            $iMinOffset++;
        }
    }
    public static function create()
    {
        // 
        if (Request::has('feedurl')){

            // get id of a feed (new or existing)
            $oFeed = Feed::where("url", Request::get('feedurl'))->first();

            $iFeedId = -1;

            if(!isset($oFeed)){
                $oFeed = new Feed;

                $oFeed->url = Request::get('feedurl');
                
                $oFeed->save();
                $iFeedId = $oFeed->id;

                // pull it
                self::scheduleFeedPull($iFeedId);
            }else{
                $iFeedId = $oFeed->id;
            }

            $oUserFeed = new UserFeed;
            $oUserFeed->feed_id = $iFeedId;
            $oUserFeed->user_id = Auth::id();
            $oUserFeed->name = Request::has('feedname') ? Request::get('feedname') : '[feed]';
            $oUserFeed->colour = Helper::sRandomUserFeedColour();
            $oUserFeed->save();
            
            return redirect('/feeds/manage');
        }
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
        return redirect('/feeds/manage');
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
        $oFeedUser = UserFeed::where("feed_id", $iUserFeedId)->where("user_id", Auth::id())->first();

        $iFeedId = $oFeedUser->id;

        $oFeedUser->delete();

        // if this user was the last/only with that feed, delete the feed
        $oaFeed = Feed::where("id", $iFeedId)->get();

        if(count($oaFeed) == 1){
            $oaFeed[0]->delete();
        }
        return redirect('/feeds/manage');
    }

    public static function feedsAndCategories()
    {
        $oQuery = DB::table('feed_user')
                ->join('feeditems', function($join)
                    {
                        $join->on('feed_user.feed_id', '=', 'feeditems.feed_id')
                        ->where('feed_user.user_id', '=', Auth::id());
                    })
                ->join('feeds', "feeds.id", "=", "feed_user.feed_id")
                ->orderBy('feeditems.pubDate', 'desc')
                ->select(['feeditems.url as url', 'feeditems.title as title', 'feeds.url as feedurl', 'feeds.id as feed_id', 'feeditems.pubDate as date', 'feed_user.name as name', 'feeditems.thumb as thumb', 'feeds.thumb as feedthumb', 'feed_user.colour as feed_colour']);

        if(Request::has('feed')){
            $oQuery->where("feeds.id", "=", Request::get('feed'));
        }

        $iPage = Request::input("page", 1);
        $iPerPage = 20;

        $maFeedItems = $oQuery->skip($iPage * $iPerPage)->take($iPerPage)->get();

        $oaFeedItems = [];

        foreach ($maFeedItems as $oFeedItem) {

            array_push($oaFeedItems, 
                [
                "url" => $oFeedItem->url,
                "title" => $oFeedItem->title,
                "feedurl" => $oFeedItem->feedurl,
                "feed_id" => $oFeedItem->feed_id,
                "date" => (new Carbon($oFeedItem->date))->diffForHumans(),
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


        return response()->json(['jsonFeedItems' => $oaFeedItems, 'jsonFeeds' => $oaFeeds]);

        //return response(['jsonFeedItems' => $oaFeedItems, 'jsonFeeds' => $oaFeeds], 200);
    }

    public static function makeHome()
    {
        // get users feeds, send to view

        $oQuery = DB::table('feed_user')
                ->join('feeditems', function($join)
                    {
                        $join->on('feed_user.feed_id', '=', 'feeditems.feed_id')
                        ->where('feed_user.user_id', '=', Auth::id());
                    })
                ->join('feeds', "feeds.id", "=", "feed_user.feed_id")
                ->orderBy('feeditems.pubDate', 'desc')
                ->select(['feeditems.url as url', 'feeditems.title as title', 'feeds.url as feedurl', 'feeds.id as feed_id', 'feeditems.pubDate as date', 'feed_user.name as name', 'feeditems.thumb as thumb', 'feeds.thumb as feedthumb', 'feed_user.colour as feed_colour']);

        if(Request::has('feed')){
            $oQuery->where("feeds.id", "=", Request::get('feed'));
        }

        $oaFeedItems = $oQuery->simplePaginate(30);

        ////$oaFeeds = Auth::user()->feeds;

        $oaFeeds = Auth::user()->userFeeds;
        $oaFeeds->load('feed');


        return view('app.home', ['oaFeedItems' => $oaFeedItems, 'oaFeeds' => $oaFeeds]);
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
                    $xmlFeed = simplexml_load_string($xmlFeed);

                    $bStopImport = false;

                    if(isset($xmlFeed->channel->image->url)){
                        $oFeed->thumb = $xmlFeed->channel->image->url;
                    }
                        
                    foreach($xmlFeed->channel->item as $oItem){

                        if($sStopAt !== null){
                            //echo "last: ", $sStopAt, "<br/>";
                            if((string)$sStopAt === (string)$oItem->guid){
                                // skip this itemoces
                                echo "<strong>", "skip these items", $oItem->guid, "</strong>", "<br/>";
                                $bStopImport = true;
                            }
                        }

                        $oExistingItemAlready = FeedItem::where("guid", "=", (string)$oItem->guid)->first();

                        if(isset($oExistingItemAlready)){
                            $bStopImport = true;
                        }

                        if(!$bStopImport){                    

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

                            if(isset($oItem->children('media', true)->thumbnail)){

                                if(isset($oItem->children('media', true)->thumbnail->attributes()->url)){
                                    $bPic = true;
                                    $sPicURL = $oItem->children('media', true)->thumbnail->attributes()->url;
                                }
                            }

                            if(!$bPic){
                                if(isset($oItem->enclosure)){
                                    if(isset($oItem->enclosure['url'])){
                                        $oFeedItem->thumb = $oItem->enclosure['url'];
                                        $bPic = true;
                                    }
                                }
                            }

                            // still no pic? resort to scanning for img in item
                            if(!$bPic){
                                preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $oItem->asXml(), $matches);
                                foreach ($matches[1] as $key=>$value) {
                                    $sPicURL = $value;
                                    $bPic = true;
                                    break;
                                }
                            }

                            // still no pic? look for og:image in downloaded webpage...!
                            
                            $oFeedItem->thumb = '';
                            $oFeedItem->save();

                            if(!$bPic){

                                //self::scrapeThumbFromFeedItem($oFeedItem->id);
                                self::scheduleFeedItemImageScrape($oFeedItem->id);
                                
                            }else{
                                self::scheduleThumbCrunch($sPicURL, $oFeedItem->id);                                                       
                            }


                            //echo "save item: ", $oFeedItem->guid, "<br/>";

                            $iItemsFetched++;
                        }else{
                            //echo "skipped an item?<br/>";
                        }
                    }
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