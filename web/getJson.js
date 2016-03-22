
var restaurantApp = angular.module('regimeApp', []).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
});

var url = "/showRegimes";

restaurantApp.controller('regimesController', function ($scope, $http) {
    $http.get(url).success(function (data) {
        $scope.regimes = data;
    }).error(function () {
        alert('Failed to get api');
    });
});
