<?php

namespace App\Models\Feeds;

use Illuminate\Database\Eloquent\Model;

class FeedItem extends Model
{
    //
    protected $table = 'feeditems';

    public function feed()
    {
        return $this->belongsTo('App\Models\Feeds\Feed');
        //return $this->belongsTo('App\Feeds\Feed', 'id', 'feed_id');
    }

    public function userOwner()
    {
    	return $this->belongsTo('App\Models\User');
    }
}
