(function() {
  'use strict';

  angular.module('app.twoCategories')
    .directive('twoCategories', twoCategories);

  function twoCategories() {
    return {
      restrict: 'E',
      scope: {
      },
      link: function() {

      },
      templateUrl: 'js/twoCategories/twoCategoriesTable.html',
      controller: 'twoCategoriesController',
      controllerAs: 'twoCategories'
    }
  }
})();
