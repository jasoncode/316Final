(function() {
  'use strict';

  angular.module('app.sponsoredBills')
    .controller('sponsoredBillsController',sponsoredBillsController);

  sponsoredBillsController.$inject = ['$scope', 'dataservice'];
  function sponsoredBillsController($scope, dataservice) {
    var vm = this;
    vm.getBills = getBills;
    vm.hide = hide;
    $scope.processBillNumber = processBillNumber;


    function getBills(){
      return dataservice.getOneSponsored();
    }

    function processBillNumber(number){
      return dataservice.processBillNumber(number);
    }

    function hide(){
      $scope.$parent.control.toggleSponsoredBills();
    }

  }

})();
