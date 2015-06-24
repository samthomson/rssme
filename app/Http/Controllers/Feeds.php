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
use App\Library;

use DOMDocument;

class Feeds extends Controller
{    

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
                self::pullFeed($iFeedId);
            }else{
                $iFeedId = $oFeed->id;
            }

            $oUserFeed = new UserFeed;
            $oUserFeed->feed_id = $iFeedId;
            $oUserFeed->user_id = Auth::id();
            $oUserFeed->name = Request::has('feedname') ? Request::get('feedname') : '[feed]';
            $oUserFeed->colour = Library\Helper::sRandomUserFeedColour();
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
                ->select(['feeditems.url as url', 'feeditems.title as title', 'feeds.url as feedurl', 'feeditems.pubDate as date', 'feed_user.name as name', 'feeditems.thumb as thumb', 'feeds.thumb as feedthumb', 'feed_user.colour as feed_colour']);
/*

                     ->select(DB::raw('count(*) as user_count, status'))
                     ->where('status', '<>', 1)
                     ->groupBy('status')
*/
        if(Request::has('feed')){
            $oQuery->where("feeds.id", "=", Request::get('feed'));
        }

        $oaFeedItems = $oQuery->simplePaginate(20);

        ////$oaFeeds = Auth::user()->feeds;

        $oaFeeds = Auth::user()->userFeeds;
        $oaFeeds->load('feed');


        return view('app.home', ['oaFeedItems' => $oaFeedItems, 'oaFeeds' => $oaFeeds]);
    }

    public static function scrapeThumbFromFeedItem($iFeedItemId){
        $oFeedItem = FeedItem::find($iFeedItemId);

        $page_content = file_get_contents($oFeedItem->url);


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
        $oFeedItem->thumb = $meta_val;
        $oFeedItem->save();

        /*
        if(!$bPic){
            $sHtml = '';

            $sHtml = file_get_contents($oItem->link);

            preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $sHtml, $matches);
            foreach ($matches[1] as $key=>$value) {
                $oFeedItem->thumb = $value;
                $bPic = true;
                echo $oFeedItem->thumb, "<br/>";
                break;
            }
        }
        */
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

                
                $xmlFeed = file_get_contents($oFeed->url, false, $context);
                $xmlFeed = simplexml_load_string($xmlFeed);


                //$xmlFeed = simplexml_load_file($oFeed->url);


                $iItemsFetched = 0;

                $bStopImport = false;

                if(isset($xmlFeed->channel->image->url)){
                    $oFeed->thumb = $xmlFeed->channel->image->url;
                }
                    
                foreach($xmlFeed->channel->item as $oItem){

                    if($sStopAt !== null){
                        //echo "last: ", $sStopAt, "<br/>";
                        if((string)$sStopAt === (string)$oItem->guid){
                            // skip this item
                            echo "<strong>", "skip these items", $oItem->guid, "</strong>", "<br/>";
                            $bStopImport = true;
                        }
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

                        if(isset($oItem->children('media', true)->thumbnail)){

                            if(isset($oItem->children('media', true)->thumbnail->attributes()->url)){
                                $oFeedItem->thumb = $oItem->children('media', true)->thumbnail->attributes()->url;
                                $bPic = true;
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
                                $oFeedItem->thumb = $value;
                                $bPic = true;
                                break;
                            }
                        }

                        // still no pic? look for og:image in downloaded webpage...!
                        /*
                        e.g.

<meta property="og:image" content="http://assets.atlasobscura.com/media/W1siZiIsInVwbG9hZHMvYXNzZXRzLzI4OGFiOWE2N2FhYjkyNjgyYl8yNTgzNjU5NjEzXzUxNGM2YWUzYjhfYi5qcGciXSxbInAiLCJ0aHVtYiIsIjYwMHhcdTAwM2UiXSxbInAiLCJjb252ZXJ0IiwiLXF1YWxpdHkgOTEiXV0/image.jpg"/>

                        */

                        $oFeedItem->save();
                        if(!$bPic){

                            self::scrapeThumbFromFeedItem($oFeedItem->id);
                            
                            $bPic = true;
                        }

                        if($bPic){
                            // download it locally as a thumb
                        }

                        //echo "save item: ", $oFeedItem->guid, "<br/>";

                        $iItemsFetched++;
                    }else{
                        //echo "skipped an item?<br/>";
                    }
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