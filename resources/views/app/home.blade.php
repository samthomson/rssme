@extends('layouts.app')


@section('app_content')
	<div class="row">
		<!--<div class="col-xs-3">feeds?</div>-->
		<div class="col-xs-12">


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

							<tr class="feed-item">
								<td><img src="{{$oItem->thumb or ''}}" class="feed-thumb"/></td>
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