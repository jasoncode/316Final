(function() {
  'use strict';

  angular.module('app.cosponsoredPeople')
    .directive('cosponsoredPeople', cosponsoredPeople);

  function cosponsoredPeople() {
    return {
      restrict: 'E',
      scope: {
      },
      link: function() {

      },
      templateUrl: 'js/cosponsoedPeople/cosponsoredPeopleTable.html',
      controller: 'cosponsoredPeopleController',
      controllerAs: 'cosponsored'
    }
  }
})();
