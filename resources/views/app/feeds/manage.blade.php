@extends('layouts.app')


@section('app_content')
	<h2>manage</h2>
	<?php
		//$oFeeds = App\Feeds\Feed::all();
		$oFeeds = Auth::user()->feeds;
	?>

	@if(count($oFeeds))
		<table class="table table-condensed">
			<thead>
		        <tr>
					<td>Url</td>
					<td>last checked</td>
					<td>status</td>
					<td>items pulled</td>
					<td>delete</td>
		        </tr>
		    </thead>
		@foreach ($oFeeds as $oFeed)


			<tbody>
				<tr>
					<td>{{$oFeed->url}}</td>
					<?php
						$oLastHit = new Carbon\Carbon($oFeed->lastPulled);
						$iSecondsSinceUpdated = $oLastHit->diffInSeconds();#

						$sSinceClass = "never";
						$sSinceText = "never";
						$sLastHit = "never";


						if($oFeed->lastPulled !== null)
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
						}else{

						}


						

					?>
					<td title="{{--$oLastHit->toDayDateTimeString()--}}">{{$sLastHit}}</td>
					<td>
					<span class="label pulled-since {{$sSinceClass}}">{{$sSinceText}}</span></td>
					<td>{{$oFeed->item_count}}</td>
					<td>
						<form action="/feeds/{{$oFeed->id}}" method="post" onsubmit="return confirm('you sure?');">
							<input type="hidden" name="_method" value="DELETE">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="submit" value="delete">
						</form>
					</td>
				</tr>
			</tbody>

			
			<br/>

		@endforeach
		</table>
	@else
		no feeds
	@endif
	<hr/>
	<a href="/feeds/add">add</a>

@endsection
