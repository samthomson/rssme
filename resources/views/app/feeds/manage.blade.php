@extends('layouts.center')


@section('centered_content')
	<h2>manage</h2>
	<?php
		//$oFeeds = App\Feeds\Feed::all();
		$oFeeds = Auth::user()->feeds;
	?>

	@if(count($oFeeds))
		@foreach ($oFeeds as $oFeed)
			{{$oFeed->url}} <form>delete</form><br/>
		@endforeach
	@else
		no feeds
	@endif

@endsection