@extends('layouts.app')


@section('app_content')
	<h2>manage</h2>
	<?php
		//$oFeeds = App\Feeds\Feed::all();
		$oUserFeeds = Auth::user()->userFeeds;
		$oUserFeeds->load('feed');
	?>

	@if(count($oUserFeeds))
		<table class="table table-condensed">

			<thead>
		        <tr>
					<td>Name</td>
					<td>Url</td>
					<td>last checked</td>
					<td>status</td>
					<td>items pulled</td>
					<td>pull now</td>
					<td>edit</td>
					<td>delete</td>
		        </tr>
		    </thead>
		    <tbody>

			@foreach ($oUserFeeds as $oUserFeed)

				<tr>
					<td>{{$oUserFeed->name}}</td>
					<td>{{$oUserFeed->feed->url}}</td>
					<?php
					
						$oLastHit = new Carbon\Carbon($oUserFeed->feed->lastPulled);
						$iSecondsSinceUpdated = $oLastHit->diffInSeconds();

						$sSinceClass = "never";
						$sSinceText = "never";
						$sLastHit = "never";


						if($oUserFeed->feed->lastPulled !== null)
						{
							if($iSecondsSinceUpdated > 14399){
								$sSinceClass = "day_plus";
								$sSinceText = "> 1 day";
							}
							if($iSecondsSinceUpdated < 14400){
								$sSinceClass = "day";
								$sSinceText = "< 1 day";
							}
							if($iSecondsSinceUpdated < 3600){
								$sSinceClass = "recent";
								$sSinceText = "< 1 hr";
							}
							if($iSecondsSinceUpdated < 900){
								$sSinceClass = "active";
								$sSinceText = "< 15 mins";
							}
							$sLastHit = $oLastHit->diffForHumans();
						}
	
					?>
					<td title="{{--$oLastHit->toDayDateTimeString()--}}">{{$sLastHit}}</td>
					<td>
					<span class="label pulled-since {{$sSinceClass}}">{{$sSinceText}}</span></td>
					<td>{{$oUserFeed->feed->item_count}}</td>
					<td><a target="_blank" href="/pullallfeeds/{{$oUserFeed->feed_id}}">pull now</a></td>
					<td><a href="/feeds/{{$oUserFeed->id}}">edit</a></td>
					<td>
						<form action="/feeds/{{$oUserFeed->id}}" method="post" onsubmit="return confirm('you sure?');">
							<input type="hidden" name="_method" value="DELETE">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="submit" value="delete">
						</form>
					</td>
				</tr>

				@endforeach
			</tbody>
		</table>
	@else
		no feeds
	@endif
	<hr/>
	<a href="/feeds/add">add</a>

@endsection
