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
	            	->get();

	        ?>

			@foreach($oaItems as $oItem)

				<a href="{{$oItem->url}}">{{$oItem->title}}</a><br/>


			@endforeach
/*

				$oUser = Auth::user();

				$oUser->load('feeds.feedItems');

				print_r($oUser->feeds->feedItems);exit();

				foreach($oUser->feeds->feedItems as $oItem)
				{
					echo $oItem->title, "<br/>";

					//print_r($oItem->feeds);
				}
				// Auth::user()->feedItems;
*/
				/*
				$oaItems = $oaItems->feeds->feedItems;

				echo count($oaItems), " items";

				print_r($oaItems);
				*/
			?>
		</div>
	</div>

@endsection