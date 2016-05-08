var workoutApp = angular.module('workoutApp', ['infinite-scroll','angularMoment']).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}')
});

var sortBy='rating';
var page=0;
var dataLeft=true;
var searchKey="";
var difficulty=[];
var options = [];

workoutApp.controller('workoutsController', function ($scope, $http) {
    var url = getUrl(page,sortBy,difficulty,searchKey,options);
    $scope.difficultySelected="all";
    loadScope($scope,$http,url);
    
    $scope.equipmentChoices = [
        {"title":"Jogos kamuolys", "index":10},
        {"title":"Dviratis", "index":11},
        {"title":"Suoliukas", "index":12},
        {"title":"Skersinis", "index":14},
        {"title":"Hanteliai", "index":15},
        {"title":"Štanga", "index":16},
        {"title":"Specialūs treniruokliai", "index":17}
    ];

    $scope.typeChoices = [
        {"title":"Jėga", "index":20},
        {"title":"Ištvermė", "index":21},
        {"title":"Vikrumas", "index":22},
        {"title":"Svorio metimas", "index":23},
        {"title":"Svorio priaugimas", "index":23}
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

    $scope.difficultyChoices = [
        {"title":"Labai sunki", "index":5},
        {"title":"Sunki", "index":4},
        {"title":"Vidutinė", "index":3},
        {"title":"Lengva", "index":2},
        {"title":"Labai lengva", "index":1}
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
        $('#equipment a, #muscle a, #type a').on('click', function (event) {
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

        $('#diff a').on('click', function (event) {
            var $target = $(event.currentTarget),
                val = $target.attr('data-value'),
                $inp = $target.find('input'),
                idx;

            if (( idx = difficulty.indexOf(val) ) > -1) {
                difficulty.splice(idx, 1);
                setTimeout(function () {
                    $inp.prop('checked', false)
                }, 0);
            } else {
                difficulty.push(val);
                setTimeout(function () {
                    $inp.prop('checked', true)
                }, 0);
            }
            page=0;
            dataLeft=true;
            var url = getUrl(page,sortBy,difficulty,searchKey,options);
            console.log(url);
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
    var url="/showWorkoutsPage?page=" + page + "&search=" + searchKey + "&sort=" + sortBy;
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
    for(var i=0; i<difficulty.length; i++)
    {
        url+="&difficulty[]=" + difficulty[i];
    }
    return url;
}
