var app = angular
	.module('rssme', [])
	.config(['$httpProvider', function($httpProvider) {
		$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
	}]);


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


    $scope.changePage = function(iNewPage){
    	$scope.iPage = iNewPage;
    	getItems();
    }
    $scope.changeFeed = function(iNewFeed){
    	$scope.iFeedId = iNewFeed;
    	$scope.iPage = 1;
    	getItems();
    }

    $scope.home = function(){
    	$scope.iFeedId = undefined;
    	$scope.iPage = 1;
    	getItems();
    }

	getItems();
});