var app = angular.module('rssme', []);


app.controller('MainUI', function($scope, $http) {

	$scope.iPage = 1;


    var getItems = function(){

    	// set loading

	    $http({
	    	method: "GET",
	    	url: "http://rssme.dev/app/user/feedsandcategories",
	    	params: {page: $scope.iPage}
	    })
	    .success(function(response) {
	    	$scope.feeds = response.jsonFeeds;
	    	$scope.feeditems = response.jsonFeedItems;

	    	// end loading
	    });
    }

    $scope.$watch('iPage', function(){
    	getItems();
    });

	getItems();
});