(function() {
  'use strict';


  angular
    .module('app.dataservice')
    .service('dataservice', dataservice);

  function dataservice($http) {
    var oneSponsored = [];
    var oneCosponsored = [];
    var allCosponsors = [];
    var allCosponsored = [];
    var rep1Bills = [];
    var rep2Bills = [];
    var jointCosponsored = [];
    var twoCategories = [];
    
    return {
      getOneSponsored: getOneSponsored,
      setOneSponsored: setOneSponsored,
      getOneCosponsored: getOneCosponsored,
      setOneCosponsored: setOneCosponsored,
      getAllCosponsors: getAllCosponsors,
      setAllCosponsors: setAllCosponsors,
      getAllCosponsored: getAllCosponsored,
      setAllCosponsored: setAllCosponsored,
      getRep1Bills: getRep1Bills,
      setRep1Bills: setRep1Bills,
      getRep2Bills: getRep2Bills,
      setRep2Bills: setRep2Bills,
      getJointCosponsoredBills: getJointCosponsoredBills,
      setJointCosponsoredBills: setJointCosponsoredBills,
      getTwoCategories: getTwoCategories,
      setTwoCategories: setTwoCategories,
      processBillNumber: processBillNumber
    }

    function getOneSponsored(){
      return oneSponsored;
    }

    function setOneSponsored(sponsored){
      oneSponsored = sponsored;
    }

    function getOneCosponsored(){
      return oneCosponsored;
    }

    function setOneCosponsored(cosponsored){
      oneCosponsored = cosponsored;
    }

    function getAllCosponsors(){
      return allCosponsors;
    }

    function setAllCosponsors(cosponsors){
      allCosponsors = cosponsors;
    }

    function getAllCosponsored(){
      return allCosponsored;
    }

    function setAllCosponsored(cosponsored){
      allCosponsored = cosponsored;
    }

    function getRep1Bills(){
      return rep1Bills;
    }

    function setRep1Bills(bills){
      rep1Bills = bills;
    }

    function getRep2Bills(){
      return rep2Bills;
    }

    function setRep2Bills(bills){
      rep2Bills = bills;
    }

    function getJointCosponsoredBills(){
      return jointCosponsored;
    }

    function setJointCosponsoredBills(bills){
      jointCosponsored = bills;
    }

    function getTwoCategories(){
      return twoCategories;
    }

    function setTwoCategories(categories){
      twoCategories = categories;
    }

    function processBillNumber(number){
      var startInfo = findBillStart(number);
      var start = startInfo[0];
      var startLength = startInfo[1];
      var mid = number.substring(startLength, number.indexOf('-'));
      var end = ' (' + number.substring(number.indexOf('-')+1, number.length-1) + 'th' + ')';
      return start + mid + end;
    }

    function findBillStart(number){
      var vals = [];
      if(number.startsWith('sconres')){
        vals[0] = 'S. Con. Res. ';
        vals[1] = 7;
        return vals;
      }
      if(number.startsWith('sjres')){
        vals[0] = 'S. J. Res. ';
        vals[1] = 5;
        return vals;
      }
      if(number.startsWith('sres')){
        vals[0] = 'S. Res. ';
        vals[1] = 4;
        return vals;
      }
      if(number.startsWith('s')){
        vals[0] = 'S. ';
        vals[1] = 1;
        return vals;
      }
      if(number.startsWith('hconres')){
        vals[0] = 'H.Con.Res. ';
        vals[1] = 7;
        return vals;
      }
      if(number.startsWith('hjres')){
        vals[0] = 'H.J.Res. ';
        vals[1] = 5;
        return vals;
      }
      if(number.startsWith('hres')){
        vals[0] = 'H.Res. ';
        vals[1] = 4;
        return vals;
      }
      if(number.startsWith('hr')){
        vals[0] = 'H.R.';
        vals[1] = 2;
        return vals;
      }
    }

  }
})();
