(function() {
  'use strict';

  angular.module('app.jointCosponsored')
    .directive('jointCosponsored', jointCosponsored);

  function jointCosponsored() {
    return {
      restrict: 'E',
      scope: true,
      link: function() {
        console.log("jointCosponsored Directive Linked");
      },
      templateUrl: 'js/jointCosponsored/jointCosponsoredTable.html',
      controller: 'jointCosponsoredController',
      controllerAs: 'jointCosponsored'
    }
  }
})();
