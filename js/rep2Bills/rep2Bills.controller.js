(function() {
  'use strict';

  angular.module('app.rep2Bills')
    .controller('rep2BillsController',rep2BillsController);

  rep2BillsController.$inject = ['$scope', 'dataservice'];
  function rep2BillsController($scope, dataservice) {
    var vm = this;
    vm.getBills = getBills;
    vm.hide = hide;
    $scope.processBillNumber = processBillNumber;


    function getBills(){
      return dataservice.getRep2Bills();
    }

    function processBillNumber(number){
      return dataservice.processBillNumber(number);
    }

    function hide(){
      $scope.$parent.control.toggleRep2Bills();
    }


  }

})();
