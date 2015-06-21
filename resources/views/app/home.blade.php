@extends('layouts.app')


@section('app_content')
	<div class="row">
		<!--<div class="col-xs-3">feeds?</div>-->
		<div class="col-xs-12">
			<?php
				$oaItems = Auth::user()->feedItems;

				echo count($oaItems), " items";
			?>
		</div>
	</div>

@endsection