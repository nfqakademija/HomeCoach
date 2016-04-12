var regimeApp = angular.module('regimeApp', ['infinite-scroll']).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}')
});

var sortBy='rating';

regimeApp.filter('showDifficulty', function()
{
    return function(input)
    {
        switch(input)
        {
            case 1:
                return 'Labai lengva';
                break;
            case 2:
                return 'Lengva';
                break;
            case 3:
                return 'Vidutinė';
                break;
            case 4:
                return 'Sunki';
                break;
            case 5:
                return "Labai sunki";
                break;
        }
    };
});

regimeApp.controller('regimesController', function ($scope, $http) {
    var page=0;
    var url = "/showRegimesPage/"+page +"/" + sortBy;
    $http.get(url).success(function (data) {
        $scope.regimes = data;
    }).error(function () {
        alert('Failed to get api');
    });

    $scope.loadMore = function()
    {
        var search = document.getElementById('search').value;
        var scrollTop = (window.pageYOffset !== undefined) ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;
        if(scrollTop!=0) {
            page++;
            var url = "/showRegimesPage/" + page +"/" + sortBy;
            $http.get(url).success(function (data) {
                var data = data;
                for (var i = 0; i < data.length; i++) {
                    $scope.regimes.push(data[i]);
                }
            }).error(function () {
                alert('Failed to get api');
            });
        }
    }

    $scope.sortByRatings = function()
    {
        page=0;
        sortBy='rating';
        console.log(sortBy);
        var url = "/showRegimesPage/"+page + "/" +sortBy;
        $http.get(url).success(function (data) {
            $scope.regimes = data;
        }).error(function () {
            alert('Failed to get api');
        });
    }
    
    $scope.sortByDate = function()
    {
        page=0;
        sortBy='date';
        console.log(sortBy);
        var url = "/showRegimesPage/" + page + "/" +sortBy;
        $http.get(url).success(function (data) {
            $scope.regimes = data;
        }).error(function () {
            alert('Failed to get api');
        });
    }
});

