<?php

namespace App\Feeds;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    protected $table = 'feeds';


    public function feedItems()
    {
    	return $this->hasMany('App\Feeds\FeedItem')->orderBy('pubDate', 'desc');
    }
}
