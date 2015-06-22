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
            }else{
                $iFeedId = $oFeed->id;
            }

            $oUserFeed = new UserFeed;
            $oUserFeed->feed_id = $iFeedId;
            $oUserFeed->user_id = Auth::id();
            $oUserFeed->save();
            
            return redirect('/feeds/manage');
        }
    }

    public static function delete($id)
    {
        // delete the pivot relation  
        $oFeedUser = UserFeed::where("feed_id", $id)->where("user_id", Auth::id())->first();

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

        $oaFeedItems = DB::table('feed_user')
                ->leftJoin('feeditems', function($join)
                    {
                        $join->on('feed_user.feed_id', '=', 'feeditems.feed_id')
                        ->where('feed_user.user_id', '=', Auth::id());
                    })
                ->leftJoin('feeds', "feeds.id", "=", "feed_user.feed_id")
                ->orderBy('feeditems.pubDate', 'desc')
                ->select(['feeditems.url as url', 'feeditems.title as title', 'feeds.url as feedurl', 'feeditems.pubDate as date'])
                    ->simplePaginate(20);

        return view('app.home', ['oaFeedItems' => $oaFeedItems]);
    }

    public static function pullAll()
    {
        $oaFeeds = Feed::all();

        foreach ($oaFeeds as $oFeed) {
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

            $iItemsFetched = 0;

            $bStopImport = false;
                
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
                    $oFeedItem->save();
                    echo "save item: ", $oFeedItem->guid, "<br/>";

                    $iItemsFetched++;
                }else{
                    //echo "skipped an item?<br/>";
                }
            }

            $oFeed->hit_count = $oFeed->hit_count + 1;
            $oFeed->item_count = $oFeed->item_count + $iItemsFetched;

            $mytime = Carbon::now();

            $oFeed->lastPulled = $mytime->toDateTimeString();
            $oFeed->save();

            echo "<hr/>";

        }
    }
}