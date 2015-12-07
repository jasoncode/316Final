(function() {
  'use strict';

  angular.module('app.rep2Bills')
    .directive('rep2Bills', rep2Bills);

  function rep2Bills() {
    return {
      restrict: 'E',
      scope: true,
      link: function() {
      },
      templateUrl: 'js/rep2Bills/rep2BillsTable.html',
      controller: 'rep2BillsController',
      controllerAs: 'rep2Bills'
    }
  }
})();
