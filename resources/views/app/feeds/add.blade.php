@extends('layouts.app')


@section('app_content')
	<form role="form" method="post" name="addfeed" action="/feeds/add">
		{!! csrf_field() !!}
		<div class="form-group">
			<h2>add feed</h2>
		</div>
		<div class="form-group">
			<input type="text" name="feedurl" class="form-control" id="feedurl" placeholder="feed url">
		</div>
		<div class="form-group">
			<button type="submit" name="submit" class="btn btn-success form-control" id="add_button">add</button>
		</div>
	</form>

@endsection