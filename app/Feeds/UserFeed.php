<?php

namespace App\Feeds;

use Illuminate\Database\Eloquent\Model;

class UserFeed extends Model
{
    protected $table = 'feed_user';
    public $timestamps = false;

    public function feed()
    {
    	return $this->hasOne('App\Feeds\Feed', 'id', 'feed_id');
    }
}
