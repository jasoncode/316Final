(function() {
  'use strict';

  angular.module('app.cosponsors')
    .directive('cosponsors', cosponsors);

  function cosponsors() {
    return {
      restrict: 'E',
      scope: {
      },
      link: function() {

      },
      templateUrl: 'js/cosponsors/cosponsorsTable.html',
      controller: 'cosponsorsController',
      controllerAs: 'cosponsors'
    }
  }
})();
