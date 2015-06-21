<?php

namespace App\Feeds;

use Illuminate\Database\Eloquent\Model;

class FeedItem extends Model
{
    //
    protected $table = 'feeditems';

    public function feed()
    {
    	return $this->belongsToMany('App\Feeds\Feed');
    }
}
