<div class="box-header with-border">
  <i class="fa fa-truck"></i><h3 class="box-title">Order Fulfilment - Same Day (%)</h3>
  <div class="box-tools pull-right">
    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
  </div>
</div>
<div class="box-body">
  <div class="chart">
    <canvas id="OrderFulfillSameDay" style="height:250px"></canvas>
  </div>
</div>

<script>
  $(function () {
    //--------------
    //- ORDER FULFILMENT - ORDERS ENTERED TODAY AND AT WDL OR COM
    //--------------
    if ($("#OrderFulfillSameDay").length)
    {
      // Get context with jQuery - using jQuery's .get() method.
      var OrderFulfillSameDayCanvas = $("#OrderFulfillSameDay").get(0).getContext("2d");
      // This will get the first returned node in the jQuery collection.
      var OrderFulfillSameDay = new Chart(OrderFulfillSameDayCanvas);

      var OrderFulfillSameDayData = {
        labels: <?php echo $OrdersFulfilledGraphLabel ?>,
        datasets: [
          {
            label: "% Fulfilled",
            fillColor: "#000000",
            strokeColor: "#000000",
            pointColor: "#000000",
            pointStrokeColor: "#000000",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: <?php echo $OrdersFulfilledGraph ?>        },
          //        {
          //          label: "Digital Goods",
          //          fillColor: "rgba(60,141,188,0.9)",
          //          strokeColor: "rgba(60,141,188,0.8)",
          //          pointColor: "#3b8bba",
          //          pointStrokeColor: "rgba(60,141,188,1)",
          //          pointHighlightFill: "#fff",
          //          pointHighlightStroke: "rgba(60,141,188,1)",
          //          data: [2660, 1931, 1346, 2161, 1939, 1658, 1739, 1299, 1876, 1848, 1815, 1414, 1285, 1805, 1722, 1642, 1613, 1245, 1779, 1470, 1750, 2145, 2044, 2269, 2063, 1716, 1663, 1346, ]
          //        }
        ]
      };

      var OrderFulfillSameDayOptions = {
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
    var PieData = <?php echo $todaysordersbytypedata ?>;
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
    var PieData = <?php echo $todaysordersbystatusdata?>;

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
    var PieData = <?php echo $outstandingordersbystatusdata?>;
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
  })
</script>