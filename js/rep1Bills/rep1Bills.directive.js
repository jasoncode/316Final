(function() {
  'use strict';

  angular.module('app.rep1Bills')
    .directive('rep1Bills', rep1Bills);

  function rep1Bills() {
    return {
      restrict: 'E',
      scope: true,
      link: function() {
      },
      templateUrl: 'js/rep1Bills/rep1BillsTable.html',
      controller: 'rep1BillsController',
      controllerAs: 'rep1Bills'
    }
  }
})();
