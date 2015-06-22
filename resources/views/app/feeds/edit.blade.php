@extends('layouts.app')


@section('app_content')
	<form role="form" method="post" name="addfeed" action="/feeds/{{$oUserFeed->id}}">
		{!! csrf_field() !!}
		<div class="form-group">
			<h2>edit feed</h2>
		</div>
		<div class="form-group">
			<input type="text" name="feedname" class="form-control" id="feedname" placeholder="feed name" value="{{$oUserFeed->name}}">
		</div>
		<div class="form-group">
			<input type="text" name="feedurl" class="form-control" id="feedurl" placeholder="feed url" value="{{$oUserFeed->feed->url}}" disabled>
		</div>
		<div class="form-group">
			<button type="submit" name="submit" class="btn btn-success form-control" id="add_button">update</button>
		</div>
	</form>

@endsection