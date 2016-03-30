var regimeApp = angular.module('regimeApp', []).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}')
});

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

var url = "/showRegimes";

regimeApp.controller('regimesController', function ($scope, $http) {
    $http.get(url).success(function (data) {
        $scope.regimes = data;
    }).error(function () {
        alert('Failed to get api');
    });
});
