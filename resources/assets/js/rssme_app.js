var app = angular
	.module('rssme', [])
	.config(['$httpProvider', function($httpProvider) {
		$httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
	}]);


app.controller('MainUI', function($scope, $http, $interval) {


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

	// edit feeds
	$scope.iFeedEditing = -1;
	$scope.iFeedUpdating = -1;


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

	$scope.manageFeeds = function() {
		$('#modalManageFeeds').modal('show');
	};
	$scope.editFeed = function(iFeedKey) {
		$scope.iFeedEditing = iFeedKey;
	};
	$scope.updateFeed = function(iFeedKey) {

		$scope.iFeedUpdating = iFeedKey;

		$http({
			method: "POST",
			url: "/app/feeds/" + $scope.feeds[iFeedKey].id,
			params: {
				'feedname': $scope.feeds[iFeedKey].name
			}
		}).then(function(response) {

			if(response.status == 200)
			{
				$scope.iFeedEditing = -1;
				// success, hide loading
				$scope.iFeedUpdating = -1;
				// fetch items again so we can see new feed in left
				$scope.getItems();
			}
		}, (function(response){
			$scope.iFeedUpdating = -1;
		}));
	};
	$scope.cancelEdit = function(iFeedId) {
		$scope.iFeedEditing = -1;
	};
	$scope.deleteFeed = function(iFeedKey) {
		if(confirm('are you sure?'))
		{
			$scope.iFeedUpdating = iFeedKey;
			$http({
				method: "DELETE",
				url: "/app/feeds/" + $scope.feeds[iFeedKey].id
			}).then(function(response) {

				if(response.status == 200)
				{
                    $scope.iFeedUpdating = -1;
					$scope.getItems();
				}
			}, (function(response){
				$scope.iFeedUpdating = -1;
			}));
		}
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
		// for ten secs
		$interval(function(){
			$scope.closeFeedback();
		},10000);
	};



    $scope.resetAddFeedForm = function() {
        $('#modalAddFeed').modal('hide');
        $scope.addfeed_name = '';
        $scope.addfeed_url = '';
    };
	$scope.closeFeedback = function(){
		$scope.bFeedbackType = 'success';
		$scope.bFeedbackShowing = false;
		$scope.sFeedbackMessage = '';
	};


    $scope.changePage = function(iNewPage){
    	$scope.iPage = iNewPage;
    	$scope.getItems();
    };
    $scope.changeFeed = function(iNewFeed){
    	$scope.iFeedId = iNewFeed;
    	$scope.iPage = 1;
    	$scope.getItems();
    };

    $scope.home = function(){
    	$scope.iFeedId = undefined;
    	$scope.iPage = 1;
    	$scope.getItems();
    };

	$scope.getItems();
});