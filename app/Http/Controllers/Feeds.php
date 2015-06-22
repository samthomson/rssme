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
        $oaFeedItems = DB::table('feeditems')
                ->join('feed_user', function($join)
                    {
                        $join->on('feeditems.feed_id', '=', 'feed_user.feed_id')
                        ->where('feed_user.user_id', '=', Auth::id());
                    })
                ->join('feeds', "feeds.id", "=", "feed_user.feed_id")
                ->orderBy('feeditems.pubDate', 'desc')
                ->select(['feeditems.url', 'feeditems.title', 'feeds.url as feedurl'])
                    ->paginate(20);

        return view('app.home', ['oaFeedItems' => $oaFeedItems]);
    }

    public static function pullAll()
    {
        $oaFeeds = Feed::all();

        foreach ($oaFeeds as $oFeed) {
            echo "pulling: ", $oFeed->url, "<br/>";


            $context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));


            $xmlFeed = file_get_contents($oFeed->url, false, $context);
            $xmlFeed = simplexml_load_string($xmlFeed);

            $iItemsFetched = 0;

            foreach($xmlFeed->channel->item as $oItem){

                $oFeedItem = new FeedItem;
                $oFeedItem->feed_id = $oFeed->id;
                $oFeedItem->title = $oItem->title;
                $oFeedItem->url = $oItem->link;

                $cdFeedPubDate = new Carbon($oItem->pubDate);
                $oFeedItem->pubDate = $cdFeedPubDate->toDateTimeString();
                $oFeedItem->save();

                $iItemsFetched++;
            }

            $oFeed->hit_count = $oFeed->hit_count + 1;
            $oFeed->item_count = $oFeed->item_count + $iItemsFetched;

            $mytime = Carbon::now();

            $oFeed->lastPulled = $mytime->toDateTimeString();
            $oFeed->save();

        }
    }
}