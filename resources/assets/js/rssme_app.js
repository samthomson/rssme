var app = angular.module('rssme', []);

app.controller('MainUI', function($scope, $http) {
    $http.get("http://rssme.dev/app/user/feedsandcategories")
    .success(function(response) {
    	$scope.feeds = response.jsonFeeds;
    	$scope.feeditems = response.jsonFeedItems;
    });
});