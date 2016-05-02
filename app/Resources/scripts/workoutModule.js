var workoutApp = angular.module('workoutApp', ['infinite-scroll','angularMoment']).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}')
});

var sortBy='rating';
var page=0;
var dataLeft=true;
var searchKey="";
var difficulty='all';
var options = [];

workoutApp.filter('showDifficulty', function()
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

workoutApp.controller('workoutsController', function ($scope, $http) {
    var url = getUrl(page,sortBy,difficulty,searchKey,options);
    $scope.difficultySelected="all";
    loadScope($scope,$http,url);
    
    $scope.equipmentChoices = [
        {"title":"Kamuolys", "index":10},
        {"title":"Dviratis", "index":11},
        {"title":"Vienaratis", "index":12},
        {"title":"Vienaragis", "index":13}
    ];

    $scope.typeChoices = [
        {"title":"Jėga", "index":20},
        {"title":"Ištvermė", "index":21},
        {"title":"Vikrumas", "index":22},
        {"title":"Svorio metimas", "index":23},
        {"title":"Svorio priauginimas", "index":23}
    ];

    $scope.muscleChoices = [
        {"title":"Nugara", "index":30},
        {"title":"Pečiai", "index":31},
        {"title":"Krūtinė", "index":32},
        {"title":"Bicepsas", "index":33},
        {"title":"Tricepsas", "index":34},
        {"title":"Dilbis", "index":35},
        {"title":"Pilvo presas", "index":36},
        {"title":"Kojos", "index":37}
    ];

    $scope.loadMore = function()
    {
        var search = document.getElementById('search').value;
        var scrollTop = (window.pageYOffset !== undefined) ? window.pageYOffset : (document.documentElement || document.body.parentNode || document.body).scrollTop;
        if(scrollTop!=0) {
            page++;
            var url = getUrl(page,sortBy,difficulty,searchKey,options);
            document.getElementsByTagName("BODY")[0].className="lowOpacity";
            if(dataLeft) {
                $http.get(url).success(function (data) {
                    var data = data;
                    if (data.length == 0) {
                        dataLeft = false;
                    }
                    for (var i = 0; i < data.length; i++) {
                        $scope.workouts.push(data[i]);
                    }
                })
            }
            document.getElementsByTagName("BODY")[0].className="";
        }
    }

    $scope.sortByRatings = function()
    {
        page=0;
        dataLeft=true;
        sortBy='rating';
        var url = getUrl(page,sortBy,difficulty,searchKey,options);
        loadScope($scope,$http,url);
    }
    
    $scope.sortByDate = function()
    {
        page=0;
        dataLeft=true;
        sortBy='date';
        var url = getUrl(page,sortBy,difficulty,searchKey,options);
        loadScope($scope,$http,url);
    }

    $scope.difficultyChange = function()
    {
        page=0;
        dataLeft=true;
        difficulty = document.getElementById('selectBox').value;
        var url = getUrl(page,sortBy,difficulty,searchKey,options);
        loadScope($scope,$http,url);
    }

    $scope.searchInput = function(param)
    {
        searchKey = param.searchKey;
        page=0;
        dataLeft=true;
        var url = getUrl(page,sortBy,difficulty,searchKey,options);
        loadScope($scope,$http,url);
    }

    $scope.loadBest = function()
    {
        document.getElementsByTagName("BODY")[0].className="lowOpacity";
        $http.get("showWorkoutsPage?page=0").success(function (data) {
            $scope.bestWorkouts = data;
            $scope.length = data.length;
        });
        document.getElementsByTagName("BODY")[0].className="";
    }

    $(document).ready(function() {
        $('.dropdown-menu a').on('click', function (event) {
            var $target = $(event.currentTarget),
                val = $target.attr('data-value'),
                $inp = $target.find('input'),
                idx;

            if (( idx = options.indexOf(val) ) > -1) {
                options.splice(idx, 1);
                setTimeout(function () {
                    $inp.prop('checked', false)
                }, 0);
            } else {
                options.push(val);
                setTimeout(function () {
                    $inp.prop('checked', true)
                }, 0);
            }
            page=0;
            dataLeft=true;
            var url = getUrl(page,sortBy,difficulty,searchKey,options);
            loadScope($scope,$http,url);
            $(event.target).blur();
            return false;
        });
    });
});


function loadScope(scope,http,url)
{
    document.getElementsByTagName("BODY")[0].className="lowOpacity";
    http.get(url).success(function (data) {
        scope.workouts = data;
        scope.length = data.length;
    });
    document.getElementsByTagName("BODY")[0].className="";
}

function getUrl(page,sortBy,difficulty,searchKey,options)
{
    if(difficulty=='all')
    {
        difficulty="";
    }
    var url="/showWorkoutsPage?page=" + page + "&difficulty=" + difficulty + "&search=" + searchKey + "&sort=" + sortBy;
    for(var i=0; i<options.length; i++)
    {
        switch(options[i][0])
        {
            case "1":
                url+= "&equipment[]=" + options[i][1];
                break;
            case "2":
                url+="&type[]=" + options[i][1];
                break;
            case "3":
                url+="&muscle[]=" + options[i][1]
                break;
        }
    }
    return url;
}
