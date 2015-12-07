// modelSearch.js
(function() {
  'use strict';

  angular
    .module('app.control')
    .controller('Control', Control);

  Control.$inject = ['$scope', '$http', 'dataservice'];

  function Control($scope, $http, dataservice) {
    var vm = this;

    vm.representative1 = null;
    vm.representative2 = null;
    vm.showSecondName = false;
    vm.showOneSponsorship = false;
    vm.showTwoSponsorship = false;
    vm.showSponsoredBills = false;
    vm.showCosponsoredBills = false;
    vm.showCosponsors = false;
    vm.showCosponsored = false;
    vm.showRep1Bills = false;
    vm.showRep2Bills = false;
    vm.showJointCosponsored = false;
    vm.showTwoCategories = false;
    vm.runOnePerson = runOnePerson;
    vm.compare = compare;
    vm.toggleSponsoredBills = toggleSponsoredBills;
    vm.toggleCosponsoredBills = toggleCosponsoredBills;
    vm.toggleCosponsors = toggleCosponsors;
    vm.toggleCosponsored = toggleCosponsored;
    vm.toggleRep1Bills = toggleRep1Bills;
    vm.toggleRep2Bills = toggleRep2Bills;
    vm.toggleJointCosponsored = toggleJointCosponsored;
    vm.toggleSecondName = toggleSecondName;
    vm.toggleTwoCategories = toggleTwoCategories;
    vm.compareButtonText = "Compare";

    $scope.processBillNumber = processBillNumber;

    function processBillNumber(number) {
      var startInfo = findBillStart(number);
      var start = startInfo[0];
      var startLength = startInfo[1];
      var mid = number.substring(startLength, number.indexOf('-'));
      var end = ' (' + number.substring(number.indexOf('-') + 1) + 'th' + ')';
      return start + mid + end;
    }


    function toggleSponsoredBills() {
      vm.showSponsoredBills = !vm.showSponsoredBills;
    }

    function toggleCosponsoredBills() {
      vm.showCosponsoredBills = !vm.showCosponsoredBills;
    }

    function toggleCosponsors() {
      vm.showCosponsors = !vm.showCosponsors;
    }

    function toggleCosponsored() {
      vm.showCosponsored = !vm.showCosponsored;
    }

    function toggleRep1Bills() {
      vm.showRep1Bills = !vm.showRep1Bills;
    }

    function toggleRep2Bills() {
      vm.showRep2Bills = !vm.showRep2Bills;
    }

    function toggleJointCosponsored() {
      vm.showJointCosponsored = !vm.showJointCosponsored;
    }

    function toggleSecondName() {
      vm.showSecondName = !vm.showSecondName;
      vm.compareButtonText = vm.showSecondName ? "Hide Comparison" : "Compare"
    }

    function toggleTwoCategories(){
      vm.showTwoCategories = !vm.showTwoCategories;
    }
    function runOnePerson() {
      vm.representative1 = document.getElementById("autocomplete1").value;
      vm.showSponsoredBills = false;
      vm.showCosponsoredBills = false;
      vm.seeBillsText = "See Bills";
      vm.seeCosponsoredBillsText = "See Bills";
      var first = vm.representative1.split(' ')[0];
      var last = vm.representative1.split(' ')[1];
      repVsPresident(first, last);
      repControversialCount(first, last);
      sponsor(first, last);
      contributions(first,last);
    }

    function compare() {
      vm.representative1 = document.getElementById("autocomplete1").value;
      vm.representative2 = document.getElementById("autocomplete2").value;
      var rep1FirstName = vm.representative1.split(' ')[0];
      var rep1LastName = vm.representative1.split(' ')[1];
      var rep2FirstName = vm.representative2.split(' ')[0];
      var rep2LastName = vm.representative2.split(' ')[1];
      rep1VsRep2(rep1FirstName, rep1LastName, rep2FirstName, rep2LastName);
      compareSponsor(rep1FirstName, rep1LastName, rep2FirstName, rep2LastName);
      twoCategories(rep1FirstName, rep1LastName, rep2FirstName, rep2LastName);
    };

    function rep1VsRep2(rep1FirstName, rep1LastName, rep2FirstName, rep2LastName) {
      //Holds the agreed/disagreed vote counts in order from 2011 - 2014
      var agreeCountArray;
      var disagreeCountArray;
      var countArray;

      var rep1 = rep1FirstName + ' ' + rep1LastName;
      var rep2 = rep2FirstName + ' ' + rep2LastName;
      $.post('php/repVsRepVoteCount.php', {
          rep1First: rep1FirstName,
          rep1Last: rep1LastName,
          rep2First: rep2FirstName,
          rep2Last: rep2LastName
        },

        function(data) {
          countArray = JSON.parse(data); //convert the JSON back into an array

          agreeCountArray = countArray[0];
          disagreeCountArray = countArray[1];

          //negative values for the graph
          for (var i = 0; i < disagreeCountArray.length; i++) {
            disagreeCountArray[i] = -(disagreeCountArray[i]);
          }

          createRepVsRepChart(agreeCountArray, disagreeCountArray, rep1, rep2);

        });


    }

    function createRepVsRepChart(agreeCountArray, disagreeCountArray, rep1Name, rep2Name) {
      var years = ["2011", "2012", "2013", "2014"];
      $('#repVsRepContainer').highcharts({
        chart: {
          type: 'column'
        },
        title: {
          text: 'Votes by ' + rep1Name + ', ' + rep2Name
        },
        subtitle: {
          text: 'Source: <a href="http://www.govtrack.us/developers">Govtrack.us</a>'
        },
        xAxis: [{
          title: {
            text: 'Year'
          },
          categories: years,
          reversed: false,
          labels: {
            step: 1
          }
        }, { // mirror axis on right side
          opposite: true,
          reversed: false,
          categories: years,
          linkedTo: 0,
          labels: {
            step: 1
          }
        }],
        yAxis: {
          title: {
            text: 'Votes'
          },
          labels: {
            formatter: function() {
              return Math.abs(this.value);
            }
          }
        },

        plotOptions: {
          series: {
            stacking: 'normal'
          }
        },

        tooltip: {
          formatter: function() {
            return '<b>' + this.series.name + 's in year ' + this.point.category + '</b><br/>' +
              'Count: ' + Highcharts.numberFormat(Math.abs(this.point.y), 0);
          }
        },

        series: [{
          name: 'Disagree',
          data: disagreeCountArray.reverse()
        }, {
          name: 'Agree',
          data: agreeCountArray.reverse()
        }]
      });

      viewByMonthButtons(rep1Name, rep2Name);

    }

    function viewByMonthButtons(rep1Name, rep2Name) {
      var grid = document.getElementById('repVsRepContainer');
      var labels = grid.getElementsByClassName("highcharts-axis-labels highcharts-xaxis-labels")[0];
      var years = labels.getElementsByTagName("text");
      console.log("YEARS: " + years);
      for (var i = 0; i < years.length; i++) {
        var yearElement = years[i];
        var year = yearElement.innerHTML;
        yearElement.addEventListener("click", phpByMonthDisplay.bind(null, year, rep1Name, rep2Name));
        //Things to make the button appear and look pretty later
        /*year = years[i].innerHTML;
        years[i].innerHTML = '';
        var yearButton = document.createElement('button');
        yearButton.innerHTML=year;
        years[i].appendChild(yearButton);*/
      }

    }

    function phpByMonthDisplay(yearInput, rep1Name, rep2Name) {
      var rep1FirstName = rep1Name.split(' ')[0];
      var rep1LastName = rep1Name.split(' ')[1];
      var rep2FirstName = rep2Name.split(' ')[0];
      var rep2LastName = rep2Name.split(' ')[1];
      var agreeArr;
      var disagreeArr;
      $.post('php/repVsRepByMonth.php', {
          rep1First: rep1FirstName,
          rep1Last: rep1LastName,
          rep2First: rep2FirstName,/
          rep2Last: rep2LastName,
          year: yearInput
        },
        function(data) {
          data = JSON.parse(data);
          var agreeArr = data[0];
          var disagreeArr = data[1];

          formatMonthArrays(agreeArr, disagreeArr, rep1Name, rep2Name, yearInput);
          console.log(agreeArr);
          console.log(disagreeArr);
        });
    }

    function formatMonthArrays(agreeArr, disagreeArr, rep1Name, rep2Name, year) {
      var fixedAgreeArr = Array.apply(null, new Array(12)).map(Number.prototype.valueOf, 0);
      var fixedDisagreeArr = Array.apply(null, new Array(12)).map(Number.prototype.valueOf, 0);
      for (var i = 0; i < agreeArr.length - 1; i = i + 2) {
        var month = agreeArr[i];
        var value = agreeArr[i + 1];
        fixedAgreeArr[month - 1] = value;
      }
      for (var i = 0; i < disagreeArr.length - 1; i = i + 2) {
        var month = disagreeArr[i];
        var value = disagreeArr[i + 1];
        fixedDisagreeArr[month - 1] = -value;
      }
      createRepVsRepByMonthDisplay(fixedAgreeArr, fixedDisagreeArr, rep1Name, rep2Name, year);
    }

    function tempObj(value, isAgree) {
      this.value = value;
      this.isAgree = isAgree;
    }

    function calculateControversyScore(currentArray)
    {

      currentArray = currentArray.slice(2);  //remove the vote_id and use only the integers
      var max = Math.max.apply(null, currentArray);
      var sum = currentArray.reduce(function(pv, cv) { return pv + cv; }, 0);

      return max/sum;
    }

    function repControversialCount(firstName, lastName) {
      $.post('php/controversy.php', {
          rep1First: firstName,
          rep1Last: lastName
        },

        function(data) {

          data = JSON.parse(data);
          var agreeArr = data[0];
          var disagreeArr = data[1];
          var unionArray = new Array();

          for (var i = 0; i < agreeArr.length; i++) {
            var currentArray = agreeArr[i];
            var score = calculateControversyScore(currentArray);
            var temp = new tempObj(score, true);
            unionArray.push(temp);
          }

          for (var i = 0; i < disagreeArr.length; i++) {
            var currentArray = agreeArr[i];
            var score = calculateControversyScore(currentArray);
            var temp = new tempObj(score, false);
            unionArray.push(temp);
          }

          unionArray.sort(function(x, y) {
            return y.value-x.value;
          });

          parseControversialData(unionArray, firstName + " " + lastName);
        });
    }

      function parseControversialData(unionArray, repName) {

      var step = Math.floor(unionArray.length / 20);

      var parsedDataArr = new Array();

      for (var j = 0; j < 20; j++) {
        var stepStart = j * step;
        var numAgree = 0;

        for (var i = stepStart; i < (stepStart + step); i++) {
          var tempObj = unionArray[i];

          if (tempObj.isAgree) {
            numAgree++;
          }
        }

        var finalRatio = numAgree / step;
        parsedDataArr.push(finalRatio);

      }
      console.log(parsedDataArr);
      createControversyDisplay(parsedDataArr, repName)
    }

    function createControversyDisplay(probabilityArray, repName) {
      $(function() {
        $('#controversialGraph').highcharts({
          title: {
            text: 'Controversial Votes Position',
            x: -20 //center
          },
          xAxis: {
            categories: ['5%', '10%', '15%', '20%', '25%', '30%', '35%', '40%', '45%', '50%', '55%', '60%', '65%', '70%', '75%', '80%', '85%', '90%', '95%', '100%']
          },
          yAxis: {
            title: {
              text: 'Probability of agreeing with the party'
            },
            min: 0,
            plotLines: [{
              value: 0,
              width: 1,
              color: '#808080'
            }]
          },
          tooltip: {
            valueSuffix: ''
          },
          legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
          },
          series: [{
            name: repName,
            data: probabilityArray
          }]
        });
      });
    }

    function createRepVsRepByMonthDisplay(agree, disagree, rep1Name, rep2Name, year) {
      var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

      $('#repVsRepByMonthContainer').highcharts({
        chart: {
          type: 'column'
        },
        title: {
          text: 'Votes by ' + rep1Name + ', ' + rep2Name + '( ' + year + ' )'
        },
        subtitle: {
          text: 'Source: <a href="http://www.govtrack.us/developers">Govtrack.us</a>'
        },
        xAxis: [{
          title: {
            text: 'Month'
          },
          categories: months,
          reversed: false,
          labels: {
            step: 1
          }
        }, { // mirror axis on right side
          opposite: true,
          reversed: false,
          categories: months,
          linkedTo: 0,
          labels: {
            step: 1
          }
        }],
        yAxis: {
          title: {
            text: 'Votes'
          },
          labels: {
            formatter: function() {
              return Math.abs(this.value);
            }
          }
        },

        plotOptions: {
          series: {
            stacking: 'normal'
          }
        },

        tooltip: {
          formatter: function() {
            return '<b>' + this.series.name + 's in month ' + this.point.category + '</b><br/>' +
              'Count: ' + Highcharts.numberFormat(Math.abs(this.point.y), 0);
          }
        },

        series: [{
          name: 'Disagree',
          data: disagree
        }, {
          name: 'Agree',
          data: agree
        }]
      });
    }

    function repVsPresident(firstName, lastName) {
      $.post('php/repVsPres.php', {
          rep1First: firstName,
          rep1Last: lastName
        },
        function(data) {
          var presArray = JSON.parse(data);
          var agree = presArray[0];
          var disagree = presArray[1];
          for (var i = 0; i < disagree.length; i++) {
            disagree[i] = -disagree[i];
          }
          createRepVsPresident(agree, disagree, firstName + ' ' + lastName);
        })
    }

    function createRepVsPresident(agreeArray, disagreeArray, rep1Name) {

      var years = ["2012", "2013"];
      $(document).ready(function() {
        $('#repVsPres').highcharts({
          chart: {
            type: 'column'
          },
          title: {
            text: "Votes by " + rep1Name + " compared to the President's position"
          },
          subtitle: {
            text: 'Source: <a href="http://www.govtrack.us/developers">Govtrack.us</a>'
          },
          xAxis: [{
            title: {
              text: 'Year'
            },
            categories: years,
            reversed: false,
            labels: {
              step: 1
            }
          }, { // mirror axis on right side
            opposite: true,
            reversed: false,
            categories: years,
            linkedTo: 0,
            labels: {
              step: 1
            }
          }],
          yAxis: {
            title: {
              text: 'Votes'
            },
            labels: {
              formatter: function() {
                return Math.abs(this.value);
              }
            }
          },

          plotOptions: {
            series: {
              stacking: 'normal'
            }
          },

          tooltip: {
            formatter: function() {
              return '<b>' + this.series.name + 's in year ' + this.point.category + '</b><br/>' +
                'Count: ' + Highcharts.numberFormat(Math.abs(this.point.y), 0);
            }
          },

          series: [{
            name: 'Disagree',
            data: disagreeArray
          }, {
            name: 'Agree',
            data: agreeArray
          }]
        });
      });
    }

    function sponsor(first, last) {
      $http({
        method: 'POST',
        url: 'php/oneSponsorship.php',
        data: {
          repFirst: first,
          repLast: last
        }
      }).then(function(response) {
        createSponsor(response.data);
      })
    }

    function createSponsor(sponsor) {

      vm.showTwoSponsorship = false;
      dataservice.setOneSponsored(sponsor[0]);
      vm.countOneSponsored = sponsor[1][0];
      dataservice.setOneCosponsored(sponsor[2]);
      vm.countOneCosponsored = sponsor[3][0];
      vm.totalCosponsorCount = sponsor[4][0];
      vm.uniqueCosponsorCount = sponsor[5][0];
      //console.log('Please: ' + sponsor[6]);
      dataservice.setAllCosponsors(sponsor[6]);
      vm.topCosponsors = sponsor[7];
      vm.topCosponsored = sponsor[8];
      dataservice.setAllCosponsored(sponsor[9]);
      vm.cosponsoredCount = sponsor[9].length;

      vm.showOneSponsorship = true;
    }

    function compareSponsor(r1First, r1Last, r2First, r2Last) {
      vm.showOneSponsorship = false;
      $http({
        method: 'POST',
        url: 'php/compareSponsorship.php',
        data: {
          rep1First: r1First,
          rep1Last: r1Last,
          rep2First: r2First,
          rep2Last: r2Last
        }
      }).then(function(response) {
        createCompareSponsor(response.data);
      })
    }


    function createCompareSponsor(compareSponsor) {
      dataservice.setRep1Bills(compareSponsor[0]);
      vm.rep1BillsCount = compareSponsor[1][0];
      dataservice.setRep2Bills(compareSponsor[2]);
      vm.rep2BillsCount = compareSponsor[3][0];
      dataservice.setJointCosponsoredBills(compareSponsor[4]);
      vm.cosponsoredBillsCount = compareSponsor[5][0];
      vm.showTwoSponsorship = true;
    }

    function twoCategories(r1First, r1Last, r2First, r2Last){
      $http({
        method: 'POST',
        url: 'php/twoCategories.php',
        data: {
          rep1First: r1First,
          rep1Last: r1Last,
          rep2First: r2First,
          rep2Last: r2Last
        }
      }).then(function(response) {
        createTwoCategories(response.data);
      })
    }

    function createTwoCategories(data){
      dataservice.setTwoCategories(data);
      vm.topCategories = [];
      for(var i = 0; i < 10; i ++){
        vm.topCategories.push(data[i]);
      }
    }

    function contributions(first, last) {

      $.post('php/contributions.php', {
          rep1First: first,
          rep1Last: last
        },

        function(data) {
          var contributions_arr = JSON.parse(data);
          parseContributionsData(contributions_arr, first, last);
        });
    }

      Number.prototype.formatMoney = function(c, d, t){
            var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
           return s + '$' + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };

    function category(name, agree, disagree, total, donation)
    {
      this.name = name;
      this.agree = agree;
      this.disagree = disagree;
      this.total = total;
      this.donation = donation;
    }

    function parseContributionsData(contributions_arr, first, last)
    {
      var categoriesArray = new Array();

      for (var i = 0; i < contributions_arr.length; i++)
      {
        var categoryName = contributions_arr[i];
        var agreeCount = contributions_arr[i+1];
        var disagreeCount = - contributions_arr[i+2];
        var totalCount = contributions_arr[i+3];
        var contributionAmount = contributions_arr[i+4];
        var newOrg = new category(categoryName, agreeCount, disagreeCount, totalCount, contributionAmount);
        categoriesArray.push(newOrg);
        i = i + 4;
      }

      categoriesArray.sort(function(x, y)
      {
        return y.donation - x.donation;
      });

      createContributionGraph(first + " " + last, categoriesArray)
    }

    function createContributionGraph(rep1Name, categoriesArray)
    {
      var organizations = new Array();
      var agreeArray = new Array();
      var disagreeArray = new Array();
      var donations = new Array();

      for (var i = 0; i < categoriesArray.length; i++)
      {
        var category = categoriesArray[i];
        organizations.push(category.name);
        agreeArray.push(category.agree);
        disagreeArray.push(category.disagree);
        donations.push(category.donation);

      }

                  $('#contributionsGraph').highcharts({
                  chart: {
                    type: 'column'
                  },
                  title: {
                    text: "Votes by " + rep1Name + " compared to the top donors"
                  },
                  subtitle: {
                    text: 'Source: <a href="http://www.govtrack.us/developers">Govtrack.us</a>'
                  },
                  xAxis: [{
                    title: {
                      text: 'Organization'
                    },
                    categories: organizations,
                    reversed: false,
                    labels: {
                     rotation: -45,
                      step: 1
                    }
                  }],
                  yAxis: [//primary y-axis
                    {
                    title: {
                      text: 'Bills'
                    },
                    labels: {
                      formatter: function() {
                      return Math.abs(this.value);
                      }
                    }
                  }, {
                    title: {
                      text: 'Donation Amount'
                    },
                    opposite: true,
                    labels: {
                      formatter: function() {
                      return this.value.formatMoney(0, '.', ',');
                      },
                    }
                  }],
                  tooltip: {
                            shared: true
                        },
                  plotOptions: {
                    series: {
                      stacking: 'normal'
                    }
                  },

                  tooltip: {

                    formatter: function() {
                      var temp = this.key;
                      for (var i = 0; i < categoriesArray.length; i++)
                      {
                        var category = categoriesArray[i];

                        if (temp == category.name)
                        {
                          return '<b>'+ 'Amount: ' + category.donation.formatMoney(0, '.', ',') + '</b>';
                        }

                      }
                    }
                  },

                  series: [{
                    name: 'Disagree',
                    data: disagreeArray
                  }, {
                    name: 'Agree',
                    data: agreeArray
                  }, {
                    name: 'Donation Amount',
                    type: 'spline',
                    yAxis: 1,
                    data: donations,
                    tooltip: {
                        formatter: function() {
                            var temp = this.key;
                            for (var i = 0; i < categoriesArray.length; i++)
                            {
                              var category = categoriesArray[i];

                              if (temp == category.name)
                              {
                                return '<b>'+ 'Amount: ' + category.donation + '</b>';
                              }

                            }
                          }
                    }
        }
                  ]
                });
    }



  }
})();
