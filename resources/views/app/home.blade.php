@extends('layouts.app')


@section('app_content')
	<div class="row" ng-app="rssme" ng-controller="MainUI">
        
		<div class="col-xs-2 hidden-xs">

			<a class="feed_link" ng-repeat="feed in feeds" ng-click="browseFeed(feed.feed_id)">
				<span>@{{feed.name}}</span>
				<br/>
			</a>
		</div>
		<div class="col-xs-12 col-sm-10">

			<a ng-repeat="feeditem in feeditems" target="_blank" class="feed-item" href="@{{feeditem.url}}">
				<div class="row feed-item">
					<div class="col-xs-1">
						<img class="feed-thumb" ng-src="@{{feeditem.thumb}}" />
					</div>

					<div class="col-xs-1 col-sm-1">@{{feeditem.name}}</div>

					<div class="col-xs-10 col-sm-9">@{{feeditem.title}}</div>

					<div class="col-xs-0 col-sm-1 hidden-xs">@{{feeditem.date}}</div>
				</div>
			</a>

			<div>
				<a class="pagination btn btn-default" ng-show="iPage > 1" ng-click="iPage = iPage -1"><i class="fa fa-caret-left"></i> newer</a>
				<a class="pagination btn btn-default" ng-click="iPage = iPage  +1">older <i class="fa fa-caret-right"></i></a>
			</div>

		</div>
	</div>
@endsection