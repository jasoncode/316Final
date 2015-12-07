(function() {
  'use strict';

  angular.module('app.cosponsoredBills')
    .controller('cosponsoredBillsController',cosponsoredBillsController);

  cosponsoredBillsController.$inject = ['$scope', 'dataservice'];
  function cosponsoredBillsController($scope, dataservice) {
    var vm = this;
    vm.getBills = getBills;
    vm.hide = hide;
    $scope.processBillNumber = processBillNumber;


    function getBills(){
      return dataservice.getOneCosponsored();
    }

    function processBillNumber(number){
      return dataservice.processBillNumber(number);
    }

    function hide(){
      $scope.$parent.control.toggleCosponsoredBills();
    }


  }

})();
