@extends('layouts.center')


@section('centered_content')
    <div id="" class="row">

    	<div class="col-xs-6">
			<form role="form" method="post" name="register" action="/auth/register">
				{!! csrf_field() !!}
				<div class="form-group">
					<h2>register</h2>
				</div>
				<div class="form-group">
					<input type="email" name="email" class="form-control" id="register_email" placeholder="email">
				</div>
				<div class="form-group">
					<input type="password" name="password" class="form-control" id="register_password" placeholder="password">
				</div>
				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-warning form-control" id="login_button">register</button>
				</div>
			</form>
		</div>

		<div class="col-xs-6">
			<form role="form" method="post" name="login" action="/auth/login">
				{!! csrf_field() !!}
				<div class="form-group">
					<h2>login</h2>
				</div>
				<div class="form-group">
					<input type="email" name="email" class="form-control" id="login_email" placeholder="email">
				</div>
				<div class="form-group">
					<input type="password" name="password" class="form-control" id="login_password" placeholder="password">
				</div>
				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-success form-control" id="login_button">login</button>
				</div>
			</form>
		</div>
	</div>

@endsection