@extends('layouts.app')


@section('app_content')
	<div class="row">
		<!--<div class="col-xs-3">feeds?</div>-->
		<div class="col-xs-12">
			<?php

				$oaItems = DB::table('feeditems')
	            ->join('feed_user', function($join)
	            	{
	            		$join->on('feeditems.feed_id', '=', 'feed_user.feed_id')
	            		->where('feed_user.user_id', '=', Auth::id());
	            	})
	            ->join('feeds', "feeds.id", "=", "feed_user.feed_id")
	            ->orderBy('feeditems.pubDate', 'desc')
	            ->select(['feeditems.url', 'feeditems.title', 'feeds.url as feedurl'])
	            	->get();

	        ?>

			@foreach($oaItems as $oItem)

				<a href="{{$oItem->url}}">{{$oItem->feedurl}} {{$oItem->title}}</a><br/>


			@endforeach

		</div>
	</div>

@endsection