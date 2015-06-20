@extends('layouts.center')


@section('centered_content')
	<h2>manage</h2>
	<?php
		$oFeeds = App\Feeds\Feed::all();

		foreach($oFeeds as $oFeed){
			echo $oFeed->url, "<br/>";
		}
	?>

@endsection