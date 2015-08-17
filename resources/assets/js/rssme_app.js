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

    // add feed
    $scope.addfeed_name = '';
    $scope.addfeed_url = '';

    $scope.bFeedbackShowing = false;
    $scope.bFeedbackType = 'success';
    $scope.sFeedbackMessage = '';


    $scope.getItems = function(){

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
                $scope.getItems();
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

    $scope.addFeed = function() {
        $('#modalAddFeed').modal('show');
    };
    $scope.addFeedSubmit = function() {
        $http({
            method: "POST",
            url: "/app/feeds/add",
            params: {
                'feedname': $scope.addfeed_name,
                'feedurl': $scope.addfeed_url
            }
        }).then(function(response) {

            if(response.status == 200)
            {
                // reset and close modal
                $scope.resetAddFeedForm();
                // successfully added feed, tell user
                $scope.flashFeedback("success", "added feed, it will appear in your feed shortly");
                // fetch items again so we can see new feed in left
                $scope.getItems();
            }
            // end loading
            $scope.bSomethingLoading = false;
        }, (function(response){
            $scope.bSomethingLoading = false;
        }));
    };

    $scope.flashFeedback = function (sType, sMessage){
        $scope.bFeedbackType = sType;
        $scope.bFeedbackShowing = true;
        $scope.sFeedbackMessage = sMessage;
    };

    $scope.resetAddFeedForm = function() {
        $('#modalAddFeed').modal('hide');
        $scope.addfeed_name = '';
        $scope.addfeed_url = '';
    };
    $scope.closeFeedback = function(){
        $scope.bFeedbackType = true;
    };


    $scope.changePage = function(iNewPage){
    	$scope.iPage = iNewPage;
    	$scope.getItems();
    }
    $scope.changeFeed = function(iNewFeed){
    	$scope.iFeedId = iNewFeed;
    	$scope.iPage = 1;
    	$scope.getItems();
    }

    $scope.home = function(){
    	$scope.iFeedId = undefined;
    	$scope.iPage = 1;
    	$scope.getItems();
    }

	$scope.getItems();
});