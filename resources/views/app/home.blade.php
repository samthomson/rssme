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
				<div class="table-responsive">
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

									$oLastHit = new Carbon\Carbon($oItem->date);
									$sSince = $oLastHit->diffForHumans();

									$sStartLink = '<a target="_blank" class="feed-item" href="'.$oItem->url.'"><span>';
									$sEndLink = '</span></a>';
								?>
								<tr class="feed-item">


									<td><?php echo $sStartLink; ?><span class="circle" style="background-color:{{$oItem->feed_colour or ''}};"></span></td>
									<td><?php echo $sStartLink; ?><img src="{{$sPic}}" class="feed-thumb"/><?php echo $sEndLink; ?></td>
									<td><?php echo $sStartLink; ?>{{$oItem->name}}<?php echo $sEndLink; ?></td>
									<td><?php echo $sStartLink; ?>
								{{$oItem->title}}<?php echo $sEndLink; ?></td>
								
									<td title="{{$oLastHit->toDayDateTimeString()}}"><?php echo $sStartLink; ?>{{$sSince}}<?php echo $sEndLink; ?></td>
								
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<?php echo $oaFeedItems->render(); ?>

			@else
				no feed items yet.. :(
			@endif

		</div>
	</div>
@endsection