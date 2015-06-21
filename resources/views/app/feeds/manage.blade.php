@extends('layouts.center')


@section('centered_content')
	<h2>manage</h2>
	<?php
		//$oFeeds = App\Feeds\Feed::all();
		$oFeeds = Auth::user()->feeds;
	?>

	@if(count($oFeeds))
		@foreach ($oFeeds as $oFeed)
			{{$oFeed->url}}
			<form action="/feeds/{{$oFeed->id}}" method="post">
				<input type="hidden" name="_method" value="DELETE">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="submit" value="delete">
			</form><br/>
		@endforeach
	@else
		no feeds
	@endif

@endsection