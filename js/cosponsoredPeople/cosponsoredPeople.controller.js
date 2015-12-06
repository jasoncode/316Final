(function() {
  'use strict';

  angular.module('app.cosponsoredPeople')
    .controller('cosponsoredPeopleController', cosponsoredPeopleController);

  cosponsoredPeopleController.$inject = ['$scope', 'dataservice'];

  function cosponsoredPeopleController($scope, dataservice) {
    var vm = this;
    vm.getAllCosponsored = getAllCosponsored;
    vm.hide = hide;

    function getAllCosponsored() {
      return dataservice.getAllCosponsored();
    }


    function hide() {
      $scope.$parent.control.toggleCosponsored();
    }

  }

})();
