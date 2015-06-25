@extends('layouts.app')


@section('app_content')
	<div class="row">
		<div class="col-xs-2">
			@foreach($oaFeeds as $oFeed)
				<a href="/?feed={{$oFeed->feed_id}}"><span class="circle" style="background-color:{{$oFeed->colour or ''}};"></span> <span>{{$oFeed->name}}</span></a><br/>
			@endforeach
		</div>
		<div class="col-xs-10">


			@if(count($oaFeedItems))

				@foreach($oaFeedItems as $oItem)
					<?php
						$sPic = $oItem->thumb !== '' ? $oItem->thumb : $oItem->feedthumb;

						$oLastHit = new Carbon\Carbon($oItem->date);
						$sSince = $oLastHit->diffForHumans();

					?>

					<a target="_blank" class="feed-item" href="{{$oItem->url}}">
						<div class="row feed-item">


							<div class="col-xs-3">
								<span class="circle" style="background-color:{{$oItem->feed_colour or ''}};"></span>

								<span><img src="{{$sPic}}" class="feed-thumb"/></span>

								<span>{{$oItem->name}}</span>
							</div>
							<div class="col-xs-8">
								<span class="limit ellipsis">{{$oItem->title}}</span>
							</div>
						
							<div class="col-xs-1" title="{{$oLastHit->toDayDateTimeString()}}">
								<span class="limit ellipsis">{{$sSince}}</span>
							</div>
						
						</div>
					</a>
				@endforeach

				<?php echo $oaFeedItems->render(); ?>

			@else
				no feed items yet.. :(
			@endif

		</div>
	</div>
@endsection