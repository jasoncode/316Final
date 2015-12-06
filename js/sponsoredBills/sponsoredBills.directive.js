(function() {
  'use strict';

  angular.module('app.sponsoredBills')
    .directive('sponsoredBills', sponsoredBills);

  function sponsoredBills() {
    return {
      restrict: 'E',
      scope: {
      },
      link: function() {

      },
      templateUrl: 'js/sponsoredBills/sponsoredBillsTable.html',
      controller: 'sponsoredBillsController',
      controllerAs: 'sponsoredBills'
    }
  }
})();
