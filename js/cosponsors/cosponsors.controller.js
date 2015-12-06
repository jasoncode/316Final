(function() {
  'use strict';

  angular.module('app.cosponsors')
    .controller('cosponsorsController', cosponsorsController);

  cosponsorsController.$inject = ['$scope', 'dataservice'];

  function cosponsorsController($scope, dataservice) {
    var vm = this;
    vm.getAllCosponsors = getAllCosponsors;
    vm.hide = hide;

    function getAllCosponsors() {
      return dataservice.getAllCosponsors();
    }


    function hide() {
      $scope.$parent.control.toggleCosponsors();
    }

  }

})();
