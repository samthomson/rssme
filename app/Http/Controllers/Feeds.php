<?php

namespace App\Http\Controllers;

#use Illuminate\Http\Request;

use App\Feeds\Feed;

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
            $oFeed = new Feed;

            $oFeed->url = Request::get('feedurl');
            $oFeed->user_id = Auth::id();

            $oFeed->save();
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