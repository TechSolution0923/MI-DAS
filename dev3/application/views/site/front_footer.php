
<!--<?php echo date('Y'); ?> &copy; MI-DAS by <a href="http://www.kk-cs.co.uk" target="_blank">Kieran Kelly Consultancy Services Ltd.</a> -->
</footer>
<!-- Control Sidebar -->
<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->
<!-- ChartJS 1.0.1 -->
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/chartjs/Chart.min.js"></script>
<!-- FastClick -->
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo $this->config->item('base_folder'); ?>public/js/app.min.js"></script>
<script src="<?php echo $this->config->item('base_folder'); ?>public/js/demo.js"></script>
<!-- AdminLTE for demo purposes -->
<link rel="stylesheet" href="<?php echo $this->config->item('base_folder'); ?>public/colorbox.css" />
<script src="<?php echo $this->config->item('base_folder'); ?>public/jquery.colorbox.js"></script>



<script>
  $(document).ready(function(){
    //Examples of how to assign the Colorbox event to elements
    $(".iframe").colorbox({iframe:true, width:"100%", height:"100%"});
    //Example of preserving a JavaScript event for inline calls.

  });
</script>
<script>
  function hide_pop(url)
  {
    $.colorbox({width:"100%", height:"100%", iframe:true, href:url});
  }
</script>
<!-- AdminLTE for demo purposes -->


<?php
$year0dataCml = ltrim($year0data, '[');
$year0dataCml = rtrim($year0dataCml, ']');
$year0dataCml = explode(',', $year0dataCml);
$year0dataCmlString = '[';
$runningTotal = 0;
foreach ($year0dataCml as $item)
{
  $runningTotal += $item;
  $year0dataCmlString .= $runningTotal .',';
}
$year0dataCmlString = rtrim($year0dataCmlString, ',');
$year0dataCmlString .= ']';
?>

<?php echo "********************"?>
<?php echo "********************"?>


<script>
    $(function () {

    $.ajax({
        url: base_url + 'site/dashboard_first',
        data: {},
        type: "post",
        //async: false,
        success: function (response) {
          $('#dashboard-first').html(response);
        }
      }
    );
    $.ajax({
        url: base_url + 'site/dashboard_second_left',
        data: {},
        type: 'post',
        //async: false,
        success: function(response) {
          $('#dashboard-second-left').html(response);
        }
      }
    );
    $.ajax({
        url: base_url + 'site/dashboard_second_right',
        data: {},
        type: 'post',
        //async: false,
        success: function(response) {
          $('#dashboard-second-right').html(response);
        }
      }
    );

    $.ajax({
      url: base_url + 'site/dashboard_third',
      data: {},
      type: 'post',
      success: function (response) {
        $('#dashboard-third').html(response);

      }
    });
    $.ajax({
      url: base_url + 'site/dashboard_fourth',
      data: {},
      type: 'post',
      success: function (response) {
        $('#dashboard-fourth').html(response);
        
      }
    });
    $.ajax({
      url: base_url + 'site/dashboard_fifth',
      data: {},
      type: 'post',
      success: function (response) {
        $('#dashboard-fifth').html(response);

      }
    });
    $.ajax({
      url: base_url + 'site/dashboard_sixth',
      data: {},
      type: 'post',
      success: function (response) {
        $('#dashboard-sixth').html(response);

      }
    });

    $.ajax({
      url: base_url + 'site/dashboard_seventh',
      data: {},
      type: 'post',
      success: function (response) {
        $('#dashboard-seventh').html(response);
        $("div.wrapper").css("display", "block");
      }
    });

    $.ajax({
      url: base_url + 'site/dashboard_eighth',
      data: {},
      type: 'post',
      success: function (response) {
        $('#dashboard-eighth').html(response);

      }
    });

      /*$.ajax({
        url: base_url + 'site/get_chart_data',
        data: {},
        type: 'post',
        //async: false,
        success: function (resp) {
          var my_response = JSON.parse(resp);
          console.log("aaa ", $("#ProjectedSalesForMonthChart").length, my_response.ProjectedSalesMonthGraphActual);
          //--------------
          //- PROJECTED SALES FOR MONTH CHART
          //--------------
          if ($("#ProjectedSalesForMonthChart").length)
          {

            // Get context with jQuery - using jQuery's .get() method.
            var ProjectedSalesForMonthChartCanvas = $("#ProjectedSalesForMonthChart").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var ProjectedSalesForMonthChart = new Chart(ProjectedSalesForMonthChartCanvas);
            const realProjectedSalesMonthGraphLabel = my_response.ProjectedSalesMonthGraphLabel.replace(/'/g, "");
            var ProjectedSalesForMonthChartData = {
              labels: realProjectedSalesMonthGraphLabel.split(','),
              datasets: [
                {
                  label: "Actual",
                  fillColor: "#d2d6de", // Gray
                  strokeColor: "#d2d6de",
                  pointColor: "#d2d6de",
                  pointStrokeColor: "#d2d6de",
                  pointHighlightFill: "#fff",
                  pointHighlightStroke: "rgba(220,220,220,1)",
                  data: my_response.ProjectedSalesMonthGraphActual.split(",") // [156538, 217208, 266948, 266948, 266948, 341905, 411450, 474286, 540821, 591018, 591018, 591018, 654755, 718922, 777295, 825292, 875581, 875581, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                },
                {
                  label: "Target",
                  fillColor: "#000000",
                  strokeColor: "#000000",
                  pointColor: "#000000",
                  pointStrokeColor: "#000000",
                  pointHighlightFill: "#fff",
                  pointHighlightStroke: "rgba(60,141,188,1)",
                  data : my_response.ProjectedSalesMonthGraphTarget.split(",") //[41667, 83333, 125000, 166667, 208333, 250000, 291667, 333333, 375000, 416667, 458333, 500000, 541667, 583333, 625000, 666667, 708333, 750000, 791667, 833333, 875000, 916667, 958333, 1000000, 1041667, 1083333, 1125000, 1166667, 1208333, 1250000 ]
                },
                {
                  label: "Projected",
                  fillColor: "#00a65a",
                  strokeColor: '#00a65a',
                  pointColor: '#00a65a',
                  pointStrokeColor: '#00a65a',
                  pointHighlightFill: "#fff",
                  pointHighlightStroke: "rgba(60,141,188,1)",
                  data: my_response.ProjectedSalesMonthGraphProjected.split(",") //[51505,103010,154514,206019,257524,309029,360533,412038,463543,515048,566552,618057,669562,721067,772571,824076,875581,927086,978591,1030095,1081600,1133105,1184610,1236114,1287619,1339124,1390629,1442133,1493638,1545143]
                }

              ]
            };

            var ProjectedSalesForMonthChartOptions = {
              //Boolean - If we should show the scale at all
              showScale: true,
              //Boolean - Whether grid lines are shown across the chart
              scaleShowGridLines: false,
              //String - Colour of the grid lines
              scaleGridLineColor: "rgba(0,0,0,.05)",
              //Number - Width of the grid lines
              scaleGridLineWidth: 1,
              //Boolean - Whether to show horizontal lines (except X axis)
              scaleShowHorizontalLines: true,
              //Boolean - Whether to show vertical lines (except Y axis)
              scaleShowVerticalLines: true,
              //Boolean - Whether the line is curved between points
              bezierCurve: true,
              //Number - Tension of the bezier curve between points
              bezierCurveTension: 0.3,
              //Boolean - Whether to show a dot for each point
              pointDot: false,
              //Number - Radius of each point dot in pixels
              pointDotRadius: 4,
              //Number - Pixel width of point dot stroke
              pointDotStrokeWidth: 1,
              //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
              pointHitDetectionRadius: 20,
              //Boolean - Whether to show a stroke for datasets
              datasetStroke: true,
              //Number - Pixel width of dataset stroke
              datasetStrokeWidth: 2,
              //Boolean - Whether to fill the dataset with a color
              datasetFill: false,
              //String - A legend template
              legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
              //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
              maintainAspectRatio: true,
              //Boolean - whether to make the chart responsive to window resizing
              responsive: true
            };

            //Create the line chart
            monthChart=ProjectedSalesForMonthChart.Line(ProjectedSalesForMonthChartData, ProjectedSalesForMonthChartOptions);

            var thismonthapp=monthChart;
            var acounter=0;
            $('#left-month-circle,#right-month-circle').click(function(){
              var stat=$(this).attr('stat');
              var currDatemonthIndicator=$('#curr-datemonth-indicator2').val();
              $.ajax({
                url: base_url+'site/getprojectedmonthdata/'+stat+'/'+currDatemonthIndicator,
                type: "POST",
                dataType:'json',
                success: function(response){

                  //alert(response);


                  $('#spinner-cust').hide();
                  if(response.ProjectedSalesMonthGraphActual) {
                    thismonthapp.clear().destroy();
                    var ProjectedSalesForMonthChartCanvas2 = $("#ProjectedSalesForMonthChart").get(0).getContext("2d");
                    var ProjectedSalesForMonthChart2 = new Chart(ProjectedSalesForMonthChartCanvas2);
                    var ProjectedSalesForMonthChartData2 = {
                      labels: response.ProjectedSalesMonthGraphLabel.split(','),
                      datasets: [
                        {
                          label: "Actual",
                          fillColor: "#d2d6de", // Gray
                          strokeColor: "#d2d6de",
                          pointColor: "#d2d6de",
                          pointStrokeColor: "#d2d6de",
                          pointHighlightFill: "#fff",
                          pointHighlightStroke: "rgba(220,220,220,1)",
                          data: response.ProjectedSalesMonthGraphActual.split(',')
                        },
                        {
                          label: "Target",
                          fillColor: "#000000",
                          strokeColor: "#000000",
                          pointColor: "#000000",
                          pointStrokeColor: "#000000",
                          pointHighlightFill: "#fff",
                          pointHighlightStroke: "rgba(60,141,188,1)",
                          data : response.ProjectedSalesMonthGraphTarget.split(',')
                        },
                        {
                          label: "Projected",
                          fillColor: response.fillColor,
                          strokeColor: response.strokeColor,
                          pointColor: response.pointColor,
                          pointStrokeColor: response.pointStrokeColor,
                          pointHighlightFill: "#fff",
                          pointHighlightStroke: "rgba(60,141,188,1)",
                          data: response.ProjectedSalesMonthGraphProjected.split(',')
                        }

                      ]
                    };
                    var ProjectedSalesForMonthChartOptions2 = {
                      showScale: true,
                      scaleShowGridLines: false,
                      scaleGridLineColor: "rgba(0,0,0,.05)",
                      scaleGridLineWidth: 1,
                      scaleShowHorizontalLines: true,
                      scaleShowVerticalLines: true,
                      bezierCurve: true,
                      bezierCurveTension: 0.3,
                      pointDot: false,
                      pointDotRadius: 4,
                      pointDotStrokeWidth: 1,
                      pointHitDetectionRadius: 20,
                      datasetStroke: true,
                      datasetStrokeWidth: 2,
                      datasetFill: false,
                      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                      maintainAspectRatio: true,
                      responsive: true
                    };
                    if(acounter>0){
                      upChart.clear().destroy();
                    }
                    upChart=ProjectedSalesForMonthChart2.Line(ProjectedSalesForMonthChartData2,ProjectedSalesForMonthChartOptions2);
                    $('#month-year-representer').html(response.monthyearindicator);
                    $('#curr-datemonth-indicator2').val(response.currdatemonthindicatorCust);
                    // upChart.update();
                    if(response.disablenext == 1) {
                      $('#right-month-circle').hide();
                    } else {
                      $('#right-month-circle').show();
                    }
                    $('#month-projected-color').removeClass().addClass('fa fa-circle-o '+response.projColor);
                    acounter++;
                  }
                },
                beforeSend:function() {
                  $('#spinner-cust').show();
                }
              });
            });


            //--------------
            //- PROJECTED SALES FOR YEAR CHART
            //--------------
            if ($("#ProjectedSalesForYearChart").length)
            {
              // Get context with jQuery - using jQuery's .get() method.
              var ProjectedSalesForYearChartCanvas = $("#ProjectedSalesForYearChart").get(0).getContext("2d");
              // This will get the first returned node in the jQuery collection.
              var ProjectedSalesForYearChart = new Chart(ProjectedSalesForYearChartCanvas);
              const arr = ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"];
              for (let i = 1; i < my_response.yearstartmonth; i++) {
                const temp = arr.shift();
                arr.push(temp);
              }
              /!*var projectedsaleyear_arrary = response.ProjectedSalesYearGraphActual.split(",");
              var projectsaleyear_list = [];
              for (var i = 0; i < projectedsaleyear_arrary.length; i ++)
                projectsaleyear_list.push(Number.parseFloat(projectedsaleyear_arrary[i]))*!/


              const realProjectedSalesYearGraphActual = [];
              for (let j=0; j<12; j++)
                realProjectedSalesYearGraphActual.push(Number.parseFloat(my_response.ProjectedSalesYearGraphActual[j]));
              var ProjectedSalesForYearChartData = {
                labels: arr,
                datasets: [
                  {
                    label: "Actual",
                    fillColor: "#d2d6de", // Gray
                    strokeColor: "#d2d6de",
                    pointColor: "#d2d6de",
                    pointStrokeColor: "#d2d6de",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: realProjectedSalesYearGraphActual// [1154047, 2364833, 3663974, 4954779, 6186292, 7488464, 7968665, 0, 0, 0, 0, 0 ]
                  },
                  {
                    label: "Target",
                    fillColor: "#000000",
                    strokeColor: "#000000",
                    pointColor: "#000000",
                    pointStrokeColor: "#000000",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(60,141,188,1)",
                    data : realProjectedSalesYearGraphActual // [1250000, 2500000, 3750000, 5000000, 6250000, 7500000, 8750000, 10000000, 11250000, 12500000, 13750000, 15000000 ]
                  },
                  {
                    label: "Projected",
                    fillColor:'#00a65a',
                    strokeColor: '#00a65a',
                    pointColor: '#00a65a',
                    pointStrokeColor: '#00a65a',
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(60,141,188,1)",
                    data: realProjectedSalesYearGraphActual
                  }
                ]
              };

              var ProjectedSalesForYearChartOptions = {
                //Boolean - If we should show the scale at all
                showScale: true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: false,
                //String - Colour of the grid lines
                scaleGridLineColor: "rgba(0,0,0,.05)",
                //Number - Width of the grid lines
                scaleGridLineWidth: 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                //Boolean - Whether the line is curved between points
                bezierCurve: true,
                //Number - Tension of the bezier curve between points
                bezierCurveTension: 0.3,
                //Boolean - Whether to show a dot for each point
                pointDot: false,
                //Number - Radius of each point dot in pixels
                pointDotRadius: 4,
                //Number - Pixel width of point dot stroke
                pointDotStrokeWidth: 1,
                //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                pointHitDetectionRadius: 20,
                //Boolean - Whether to show a stroke for datasets
                datasetStroke: true,
                //Number - Pixel width of dataset stroke
                datasetStrokeWidth: 2,
                //Boolean - Whether to fill the dataset with a color
                datasetFill: false,
                //String - A legend template
                legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio: true,
                //Boolean - whether to make the chart responsive to window resizing
                responsive: true
              };

              //Create the line chart
              ProjectedSalesForYearChart.Line(ProjectedSalesForYearChartData, ProjectedSalesForYearChartOptions);
            }

            //--------------
            //- ORDER FULFILMENT - ORDERS ENTERED TODAY AND AT WDL OR COM
            //--------------
            if ($("#OrderFulfillSameDay").length)
            {
              // Get context with jQuery - using jQuery's .get() method.
              var OrderFulfillSameDayCanvas = $("#OrderFulfillSameDay").get(0).getContext("2d");
              // This will get the first returned node in the jQuery collection.
              var OrderFulfillSameDay = new Chart(OrderFulfillSameDayCanvas);

              var ordersFulfilledGraphArr = my_response.OrdersFulfilledGraph.split(',');
              var ordersFulfilledGraphList = [];

              for (let p=0; p<ordersFulfilledGraphArr.length; p++)
                ordersFulfilledGraphList.push(Number.parseFloat(ordersFulfilledGraphArr[p]));

              const realOrdersFulfilledGraphLabel = my_response.OrdersFulfilledGraphLabel.replace(/'/g, "");
              var OrderFulfillSameDayData = {
                labels: realOrdersFulfilledGraphLabel.split(','),
                datasets: [
                  {
                    label: "% Fulfilled",
                    fillColor: "#000000",
                    strokeColor: "#000000",
                    pointColor: "#000000",
                    pointStrokeColor: "#000000",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: ordersFulfilledGraphList
                  }
                ]
              };

              var OrderFulfillSameDayOptions = {
                //Boolean - If we should show the scale at all
                showScale: true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: true,
                //String - Colour of the grid lines
                scaleGridLineColor: "rgba(0,0,0,.05)",
                //Number - Width of the grid lines
                scaleGridLineWidth: 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                //Boolean - Whether the line is curved between points
                bezierCurve: false,
                //Number - Tension of the bezier curve between points
                bezierCurveTension: 0.3,
                //Boolean - Whether to show a dot for each point
                pointDot: false,
                //Number - Radius of each point dot in pixels
                pointDotRadius: 4,
                //Number - Pixel width of point dot stroke
                pointDotStrokeWidth: 1,
                //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                pointHitDetectionRadius: 20,
                //Boolean - Whether to show a stroke for datasets
                datasetStroke: true,
                //Number - Pixel width of dataset stroke
                datasetStrokeWidth: 2,
                //Boolean - Whether to fill the dataset with a color
                datasetFill: false,
                //String - A legend template
                legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio: false,
                //Boolean - whether to make the chart responsive to window resizing
                responsive: true
              };

              //Create the line chart
              OrderFulfillSameDay.Line(OrderFulfillSameDayData, OrderFulfillSameDayOptions);
            }

            //-------------
            //- LINE CHART -
            //--------------
            // var lineChartCanvas = $("#lineChart").get(0).getContext("2d");
            // var lineChart = new Chart(lineChartCanvas);
            // var lineChartOptions = OrderFulfillSameDayOptions;
            // lineChartOptions.datasetFill = false;
            // lineChart.Line(OrderFulfillSameDayData, lineChartOptions);

            //-------------
            //- TODAYS ORDERS BY TYPE DONUT CHART
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var TodaysOrdersByTypeCanvas = $("#TodaysOrdersByType").get(0).getContext("2d");
            var TodaysOrdersByType = new Chart(TodaysOrdersByTypeCanvas);
            var PieData = my_response.todaysordersbytypedata;
            var pieOptions = {
              //Boolean - Whether we should show a stroke on each segment
              segmentShowStroke: true,
              //String - The colour of each segment stroke
              segmentStrokeColor: "#fff",
              //Number - The width of each segment stroke
              segmentStrokeWidth: 2,
              //Number - The percentage of the chart that we cut out of the middle
              percentageInnerCutout: 50, // This is 0 for Pie charts
              //Number - Amount of animation steps
              animationSteps: 100,
              //String - Animation easing effect
              animationEasing: "easeOutExpo",
              //Boolean - Whether we animate the rotation of the Doughnut
              animateRotate: true,
              //Boolean - Whether we animate scaling the Doughnut from the centre
              animateScale: false,
              //Boolean - whether to make the chart responsive to window resizing
              responsive: true,
              // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
              maintainAspectRatio: true,
              //String - A legend template
              legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
            };
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.

            var TodaysOrdersByTypeChart=TodaysOrdersByType.Doughnut(PieData, pieOptions);
            $("#TodaysOrdersByType").click(function(evt){
              var activePoints = TodaysOrdersByTypeChart.getSegmentsAtEvent(evt);
              var ulToRed=base_url+'site/todaysorder/'+activePoints[0].label+'/type';
              location.href=ulToRed;
            });
            //-------------
            //- TODAYS ORDERS BY STATUS DONUT CHART
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var TodaysOrdersByStatusCanvas = $("#TodaysOrdersByStatus").get(0).getContext("2d");
            var TodaysOrdersByStatus = new Chart(TodaysOrdersByStatusCanvas);
            var PieData = my_response.todaysordersbystatusdata;

            var pieOptions = {
              //Boolean - Whether we should show a stroke on each segment
              segmentShowStroke: true,
              //String - The colour of each segment stroke
              segmentStrokeColor: "#fff",
              //Number - The width of each segment stroke
              segmentStrokeWidth: 2,
              //Number - The percentage of the chart that we cut out of the middle
              percentageInnerCutout: 50, // This is 0 for Pie charts
              //Number - Amount of animation steps
              animationSteps: 100,
              //String - Animation easing effect
              animationEasing: "easeOutExpo",
              //Boolean - Whether we animate the rotation of the Doughnut
              animateRotate: true,
              //Boolean - Whether we animate scaling the Doughnut from the centre
              animateScale: false,
              //Boolean - whether to make the chart responsive to window resizing
              responsive: true,
              // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
              maintainAspectRatio: true,
              //String - A legend template
              legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
            };
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            var TodaysOrdersByStatusChart=TodaysOrdersByStatus.Doughnut(PieData, pieOptions);
            $("#TodaysOrdersByStatus").click(function(evt){
              var activePointsStatus = TodaysOrdersByStatusChart.getSegmentsAtEvent(evt);
              var ulToRedStatus=base_url+'site/todaysorder/'+activePointsStatus[0].label+'/status';
              location.href=ulToRedStatus;
            });

            //-------------
            //- OUTSTANDING ORDERS BY STATUS DONUT CHART
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var OutstandingOrdersByStatusCanvas = $("#OutstandingOrdersByStatus").get(0).getContext("2d");
            var OutstandingOrdersByStatus = new Chart(OutstandingOrdersByStatusCanvas);
            var PieData = my_response.outstandingordersbystatusdata;
            var pieOptions = {
              //Boolean - Whether we should show a stroke on each segment
              segmentShowStroke: true,
              //String - The colour of each segment stroke
              segmentStrokeColor: "#fff",
              //Number - The width of each segment stroke
              segmentStrokeWidth: 2,
              //Number - The percentage of the chart that we cut out of the middle
              percentageInnerCutout: 50, // This is 0 for Pie charts
              //Number - Amount of animation steps
              animationSteps: 100,
              //String - Animation easing effect
              animationEasing: "easeOutExpo",
              //Boolean - Whether we animate the rotation of the Doughnut
              animateRotate: true,
              //Boolean - Whether we animate scaling the Doughnut from the centre
              animateScale: false,
              //Boolean - whether to make the chart responsive to window resizing
              responsive: true,
              // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
              maintainAspectRatio: true,
              //String - A legend template
              legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
            };
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            var OutstandingOrdersByStatusChart=OutstandingOrdersByStatus.Doughnut(PieData, pieOptions);
            $("#OutstandingOrdersByStatus").click(function(evt){
              var activePointsOutstandingStatus = OutstandingOrdersByStatusChart.getSegmentsAtEvent(evt);
              var ulToRedOutstandingStatus=base_url+'site/outstandingorder/'+activePointsOutstandingStatus[0].label+'/status';
              location.href=ulToRedOutstandingStatus;
            });
          }
        }});*/
    }
  );
</script>


<script type="text/javascript">
  $(document).ready(function(){


  });




  $(function () {
    <?php if($salestodaydonutcharts=='Y'){ ?>
    //  alert('yes');
    $("#salestodaytables_nav a").click();
    <?php }  ?>


    <?php if($outstandingordersdonutchart=='Y'){ ?>
    //  alert('yes');
    $("#outstandingorderstable_nav a").click();
    <?php }  ?>

    <?php if($threeyearsaleschart=='Y'){ ?>
    //  alert('yes');

    $("#threeyearsaleschart_nav a").click();
    $("#threeyearsalestable_nav a").click();

    <?php }  ?>
  });

</script>
</body>
</html>
