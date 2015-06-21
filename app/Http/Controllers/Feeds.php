<?php

namespace App\Http\Controllers;

#use Illuminate\Http\Request;

use App\Feeds\Feed;
use App\Feeds\UserFeed;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Request;

use Auth;

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
}