(function() {
  'use strict';

  angular.module('app.cosponsoredBills')
    .directive('cosponsoredBills', cosponsoredBills);

  function cosponsoredBills() {
    return {
      restrict: 'E',
      scope: true,
      link: function() {},
      templateUrl: 'js/cosponsoredBills/cosponsoredBillsTable.html',
      controller: 'cosponsoredBillsController',
      controllerAs: 'cosponsoredBills'
    }
  }
})();
