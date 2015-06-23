@extends('layouts.app')


@section('app_content')
	<div class="row">
		<div class="col-xs-2">
			@foreach($oaFeeds as $oFeed)
				<a href="/?feed={{$oFeed->feed_id}}">{{$oFeed->name}}</a><br/>
			@endforeach
		</div>
		<div class="col-xs-10">


			@if(count($oaFeedItems))
				<table class="table table-condensed">
					<!--
					<thead>
				        <tr>
							<td>thumb</td>
							<td>name</td>
							<td>title</td>
							<td>date</td>
				        </tr>
				    </thead>
				    -->
		    		<tbody>
					
						@foreach($oaFeedItems as $oItem)
							<?php
								$sPic = $oItem->thumb !== '' ? $oItem->thumb : $oItem->feedthumb;
							?>
							<tr class="feed-item">
								<td><img src="{{$sPic}}" class="feed-thumb"/></td>
								<td>{{$oItem->name}}</td>
								<td><a target="_blank" class="feed-item" href="{{$oItem->url}}">{{$oItem->title}}</a></td>
								<?php

									$oLastHit = new Carbon\Carbon($oItem->date);
									$sSince = $oLastHit->diffForHumans();
								?>

								<td title="{{$oLastHit->toDayDateTimeString()}}">{{$sSince}}</td>
							</tr>

						@endforeach
					</tbody>
				</table>

				<?php echo $oaFeedItems->render(); ?>

			@else
				no feed items yet.. :(
			@endif

		</div>
	</div>
@endsection