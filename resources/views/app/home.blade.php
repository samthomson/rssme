@extends('layouts.app')


@section('app_content')
	<div class="row" ng-app="rssme" ng-controller="MainUI">
        
		<div class="col-xs-2 hidden-xs">

			<a ng-repeat="feed in feeds" href="/?feed=@{{feed.feed_id}}">
				<span>@{{feed.name}}</span>
			</a>
			<br/>
		</div>
		<div class="col-xs-12 col-sm-10">

			<a ng-repeat="feeditem in feeditems" target="_blank" class="feed-item" href="@{{feeditem.url}}">
				<div class="row feed-item">


					<div class="col-xs-1">
						<span class="feed-thumb-wrapper"><img src="{{--$sPic--}}" class="feed-thumb"/></span>
					</div>
					<div class="col-xs-1 col-sm-1">
						
						<a href="/?feed=@{{feeditem.feed_id}}"><span class="hidden-xs">@{{feeditem.name}}</span></a>
					</div>
					
					<div class="col-xs-10 col-sm-9">
						<span class="limit ellipsis">@{{feeditem.title}}</span>
					</div>
					<div class="col-xs-0 col-sm-1 hidden-xs" title="{{--feeditem.toDayDateTimeString()--}}">
						<span class="limit ellipsis">{{--$sSince--}}</span>
					</div>
				</div>
			</a>

		</div>
	</div>
@endsection