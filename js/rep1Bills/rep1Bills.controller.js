(function() {
  'use strict';

  angular.module('app.rep1Bills')
    .controller('rep1BillsController',rep1BillsController);

  rep1BillsController.$inject = ['$scope', 'dataservice'];
  function rep1BillsController($scope, dataservice) {
    var vm = this;
    vm.getBills = getBills;
    vm.hide = hide;
    $scope.processBillNumber = processBillNumber;


    function getBills(){
      return dataservice.getRep1Bills();
    }

    function processBillNumber(number){
      return dataservice.processBillNumber(number);
    }

    function hide(){
      $scope.$parent.control.toggleRep1Bills();
    }


  }

})();
