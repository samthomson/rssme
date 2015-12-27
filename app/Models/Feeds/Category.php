<?php

namespace App\Models\Feeds;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';


    public function owner()
    {
    	return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function userFeeds(){
        return $this->hasMany('App\Models\Feeds\UserFeed', 'category_id', 'id');
    }
}
