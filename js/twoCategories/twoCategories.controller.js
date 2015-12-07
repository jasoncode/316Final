(function() {
  'use strict';

  angular.module('app.twoCategories')
    .controller('twoCategoriesController', twoCategoriesController);

  twoCategoriesController.$inject = ['$scope', 'dataservice'];

  function twoCategoriesController($scope, dataservice) {
    var vm = this;
    vm.getCategories = getCategories;
    vm.hide = hide;

    function getCategories() {
      return dataservice.getTwoCategories();
    }


    function hide() {
      $scope.$parent.control.toggleTwoCategories();
    }

  }

})();
