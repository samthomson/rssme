@extends('layouts.master')

@section('content')
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" ng-click="home()" id="home_link"><span class="brand rss">RSS</span><span class="brand me">me</span></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="/feeds/manage"><i class="fa fa-list"></i> feeds</a></li>
        <li><a href="/feeds/add"><i class="fa fa-plus"></i> Add</a></li>


        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" alt="{{Auth::user()->email}}"><i class="fa fa-user"></i> Account <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/auth/logout"><i class="fa fa-sign-out"></i> logout</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<div class="container-fluid">
	@yield('app_content')
</div>
@endsection