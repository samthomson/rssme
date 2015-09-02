

<!-- Stored in resources/views/layouts/master.blade.php -->

<html>
    <head>
        <title>rss - @yield('title', 'news aggregator')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
        <!--<link href="{{ asset("css/app.css") }}" rel="stylesheet"-->
        <link rel="stylesheet" href="{{ elixir('css/all.css') }}">

        <link rel='stylesheet' id='g_font-css'  href='http://fonts.googleapis.com/css?family=Noto+Sans%3A400%2C700%2C400italic%2C700italic&#038;ver=3.5.1' type='text/css' media='all' />

        <link href='http://fonts.googleapis.com/css?family=PT+Sans+Caption:700' rel='stylesheet' type='text/css'>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    </head>
    <body ng-app="rssme">
        <div ng-controller="MainUI">

            @include('partials.modals')

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
                      <li>
                          <div id="fixed_output">
                              <div class="alert alert-@{{bFeedbackType}} alert-dismissible" role="alert" ng-show="bFeedbackShowing">
                                  <button ng-click="bFeedbackShowing = false" type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                  @{{sFeedbackMessage}}
                              </div>
                          </div>
                      </li>
                  </ul>


                    <ul class="nav navbar-nav navbar-right" ng-show="bLoggedIn">
                      <li><a ng-click="manageFeeds()"><i class="fa fa-list"></i> feeds</a></li>
                      <li><a ng-click="addFeed()"><i class="fa fa-plus"></i> Add</a></li>


                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" alt="account menu"><i class="fa fa-user"></i> Account <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li><a ng-click="logout()"><i class="fa fa-sign-out"></i> logout</a></li>
                        </ul>
                      </li>
                    </ul>

                </div><!-- /.navbar-collapse -->
              </div><!-- /.container-fluid -->
            </nav>
            <div class="container-fluid">



              <div ng-show="bLoggedIn && !bSomethingLoading">

                <div class="row">

                  <div class="col-xs-2 hidden-xs">

                    <a class="feed_link" ng-repeat="feed in feeds" ng-click="changeFeed(feed.feed_id)">
                      <span>@{{feed.name}}</span>
                      <br/>
                    </a>
                  </div>
                  <div class="col-xs-12 col-sm-10">


                    <a ng-repeat="feeditem in feeditems" target="_blank" class="feed-item" href="@{{feeditem.url}}">
                      <div class="row feed-item">
                        <div class="col-xs-2 col-sm-1">
                          <img class="feed-thumb" ng-src="@{{feeditem.thumb}}" />
                        </div>

                        <div class="col-xs-0 col-sm-1 hidden-xs">@{{feeditem.name}}</div>

                        <div class="col-xs-10 col-sm-9">@{{feeditem.title}}</div>

                        <div class="col-xs-0 col-sm-1 hidden-xs">@{{feeditem.date}}</div>
                      </div>
                    </a>

                    <div class="row">
                      <div class="col-xs-6">
                        <a class="pagination btn btn-primary form-control" ng-click="changePage(iPage - 1)" ng-show="iPage > 1"><i class="fa fa-caret-left"></i> newer</a>
                      </div>
                      <div class="col-xs-6">
                        <a class="pagination btn btn-primary form-control" ng-click="changePage(iPage + 1)" ng-show="iPage < iPagesAvailable">older <i class="fa fa-caret-right"></i></a>
                      </div>
                    </div>

                  </div>
                </div>
              </div>




              <div ng-show="bLoggedIn == false && !bSomethingLoading">
                <div class="row">

                  @include('app.login')
                </div>
              </div>

                <div id="loading" ng-show="bSomethingLoading"><i class="fa fa-spinner fa-spin"></i> loading</div>

            </div>

        </div>
        <script type="text/javascript" src="{{ elixir('js/all.js') }}"></script>
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-66970414-1', 'auto');
            ga('send', 'pageview');

        </script>
    </body>
</html>



