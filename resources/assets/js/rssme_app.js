var app = angular.module('rssme', []);


app.controller('MainUI', function($scope, $http) {

	$scope.iPage = 1;
	$scope.iFeedId = undefined;


    var getItems = function(){

    	// set loading
	    $http({
	    	method: "GET",
	    	url: "/app/user/feedsandcategories",
	    	params: {
	    		page: $scope.iPage,
	    		feed: $scope.iFeedId
	    	}
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
    $scope.$watch('iFeedId', function(){
    	getItems();
    });

    $scope.browseFeed = function(iFeedId){
    	$scope.iFeedId = iFeedId;
    }

	getItems();
});