@extends('layouts.app')


@section('app_content')
	<div class="row" ng-app="rssme" ng-controller="MainUI">
        
		<div class="col-xs-2 hidden-xs">

			<a ng-repeat="feed in feeds" href="/?feed=@{{feed.feed_id}}">
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

			<hr/>
			Showing page @{{iPage}} <a class="pagination" ng-show="iPage > 1" ng-click="iPage = iPage -1">newer</a> <a class="pagination" ng-click="iPage = iPage  +1">older</a>

		</div>
	</div>
@endsection