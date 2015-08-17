var app = angular
	.module('rssme', [])
	.config(['$httpProvider', function($httpProvider) {
		$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
	}]);


app.controller('MainUI', function($scope, $http) {


	$scope.feeds = [];
	$scope.feeditems = [];

	$scope.iPage = 1;
	$scope.iFeedId = undefined;
	$scope.bLoggedIn = false;
	$scope.bSomethingLoading = true;

	// login / register forms
	$scope.email = '';
	$scope.password = '';


    var getItems = function(){

    	// set loading
		$scope.bSomethingLoading = true;
	    $http({
	    	method: "GET",
	    	url: "/app/user/feedsandcategories",
	    	params: {
	    		page: $scope.iPage,
	    		feed: $scope.iFeedId
	    	}
	    })
	    .then(function(response) {
				$scope.feeds = response.data.jsonFeeds;
	    		$scope.feeditems = response.data.jsonFeedItems;

				// end loading
				$scope.bSomethingLoading = false;
				// user may be logged in or out now
				$scope.bLoggedIn = response.status == 200 ? true : false;
	    },(function(){
				$scope.bSomethingLoading = false;
		}));
    };

	$scope.login = function(){
		$scope.bSomethingLoading = true;
		// parse form and submit
		$http({
			method: "POST",
			url: "/app/auth/login",
			params: {
				'email': $scope.email,
				'password': $scope.password
			}
		}).then(function(response) {

			if(response.status == 200)
			{
				$scope.bLoggedIn = true;
                // now fetch items
                getItems();
			}
			// end loading
			$scope.bSomethingLoading = false;
		}, (function(response){
			$scope.bSomethingLoading = false;
		}));
	};

	$scope.logout = function(){
		$scope.bSomethingLoading = true;
		// parse form and submit
		$http({
			method: "POST",
			url: "/app/auth/logout"
		})
			.success(function(response) {

				if(response.status == 200)
				{
					$scope.bLoggedIn = false;
				}
				// end loading
				$scope.bSomethingLoading = false;
				// user may be logged in or out now
				$scope.bLoggedIn = (response.status == 200 ? true : false);
			})
			.error(function(){
				$scope.bSomethingLoading = false;
			});
	};


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