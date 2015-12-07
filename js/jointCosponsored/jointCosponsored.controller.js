(function() {
  'use strict';

  angular.module('app.jointCosponsored')
    .controller('jointCosponsoredController',jointCosponsoredController);

  jointCosponsoredController.$inject = ['$scope', 'dataservice'];
  function jointCosponsoredController($scope, dataservice) {
    var vm = this;
    vm.getBills = getBills;
    vm.hide = hide;
    $scope.processBillNumber = processBillNumber;


    function getBills(){
      return dataservice.getJointCosponsoredBills();
    }

    function processBillNumber(number){
      return dataservice.processBillNumber(number);
    }

    function hide(){
      $scope.$parent.control.toggleJointCosponsored();
    }


  }

})();
