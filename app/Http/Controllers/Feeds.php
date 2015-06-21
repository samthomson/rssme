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


            Auth::user()->feeds()->save($oUserFeed);
            //$oUserFeed->user_id = Auth::id();


            
            return redirect('/');
        }
    }

    public static function delete($id)
    {
        $oFeed = Auth::user()->feeds()->where("id", $id);
        if($oFeed){
            $oFeed->delete();
        }
        return redirect('/feeds/manage');
    }
}