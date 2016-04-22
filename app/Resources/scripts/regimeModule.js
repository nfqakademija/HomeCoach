var regimeApp = angular.module('regimeApp', ['infinite-scroll','angularMoment']).config(function($interpolateProvider){
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
    var dataLeft=true;
    var searchKey="";
    var difficulty='all';
    var url = "/showRegimesPage/"+page +"/" + sortBy + "/" + difficulty;
    $scope.difficultySelected="all";
    loadScope($scope,$http,url);

    $scope.loadMore = function()
    {
        var search = document.getElementById('search').value;
        var scrollTop = (window.pageYOffset !== undefined) ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;
        if(scrollTop!=0) {
            page++;
            var url = "/showRegimesPage/" + page +"/" + sortBy + "/" + difficulty;
            document.getElementsByTagName("BODY")[0].className="lowOpacity";
            if(dataLeft) {
                $http.get(url).success(function (data) {
                    var data = data;
                    if (data.length == 0) {
                        dataLeft = false;
                    }
                    for (var i = 0; i < data.length; i++) {
                        $scope.regimes.push(data[i]);
                    }
                }).error(function () {
                    alert('Failed to get api');
                });
            }
            document.getElementsByTagName("BODY")[0].className="";
        }
    }

    $scope.sortByRatings = function()
    {
        page=0;
        dataLeft=true;
        sortBy='rating';
        var url = "/showRegimesPage/"+page + "/" +sortBy + "/" + difficulty;
        loadScope($scope,$http,url);
    }
    
    $scope.sortByDate = function()
    {
        page=0;
        dataLeft=true;
        sortBy='date';
        var url = "/showRegimesPage/" + page + "/" +sortBy + "/" + difficulty;
        loadScope($scope,$http,url);
    }

    $scope.difficultyChange = function()
    {
        page=0;
        dataLeft=true;
        difficulty = document.getElementById('selectBox').value;
        console.log(difficulty);
        var url = "/showRegimesPage/" + page + "/" +sortBy + "/" + difficulty;
        loadScope($scope,$http,url);
    }

    $scope.searchInput = function(param)
    {
        console.log(param.searchKey);
    }
});

function loadScope(scope,http,url)
{
    document.getElementsByTagName("BODY")[0].className="lowOpacity";
    http.get(url).success(function (data) {
        scope.regimes = data;
    }).error(function () {
        alert('Failed to get api');
    });
    document.getElementsByTagName("BODY")[0].className="";
}