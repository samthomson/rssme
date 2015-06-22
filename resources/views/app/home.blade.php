@extends('layouts.app')


@section('app_content')
	<div class="row">
		<!--<div class="col-xs-3">feeds?</div>-->
		<div class="col-xs-12">
			
			@foreach($oaFeedItems as $oItem)

				<a class="feed-item" href="{{$oItem->url}}">{{$oItem->feedurl}}: {{$oItem->title}}</a><br/>

			@endforeach

			<?php echo $oaFeedItems->render(); ?>

		</div>
	</div>

@endsection