<!------------------------------------------------------------------------------------------------------------------>
<!-- PROJECTED SALES MONTH CHART -->
<!------------------------------------------------------------------------------------------------------------------>
<?php
  $threeData = canSeeThreeInfo();
  $canSeeProjectedSales = $threeData['seeprojectedsales'];
  $canSeeProjectedSalesYear = $threeData['seeprojectedsalesyear'];
  $canSeeOrderFulfillment = $threeData['seeorderfulfillment'];
?>
<?php if ($canSeeProjectedSales): ?>
  <?php echo ($canSeeProjectedSalesYear) ? '<div class="col-md-6">' : '<div class="col-md-12">'; ?>
  <!-- Colour code the graphic based on kpi thresholds -->
  <?php
  $currmonth=date('n',time());
  $fcounter=$currmonth+1;
  if ($projmonthsalespc < $G_kpithreshold1) $class="box-danger";
  if ($projmonthsalespc >= $G_kpithreshold1 AND $projmonthsalespc < $G_kpithreshold2) $class="box-warning";
  if ($projmonthsalespc >= $G_kpithreshold2) $class="box-success";
  if (empty($projmonthsalespc)) { $class="box-success"; }
  ?>
  <div class="box <?= $class?>">
    <!-- Colour code the graphic based on kpi thresholds -->
    <?php
    if ($projmonthsalespc < $G_kpithreshold1) $class="bg-red";
    if ($projmonthsalespc >= $G_kpithreshold1 AND $projmonthsalespc < $G_kpithreshold2) $class="bg-yellow";
    if ($projmonthsalespc >= $G_kpithreshold2) $class="bg-green";
    if (empty($projmonthsalespc)) { $class="bg-green"; }
    ?>
    <div class="box-header with-border <?= $class?>">
      <i class="fa fa-line-chart"></i><h3 class="box-title">Projected Sales</h3>
      <div class="pull-right">
        <i stat="prev" style="cursor:pointer;" id="left-month-circle" class="fa fa-arrow-circle-o-left "></i>
        <span class="box-title">(<span id="month-year-representer"><?= date('M Y',time()); ?></span>)</span>
        <i stat="next" style="cursor:pointer;display: none;" id="right-month-circle" class=" fa fa-arrow-circle-o-right"></i>
        <input type="hidden" value="<?= date('Y-m-01',time()); ?>" id="curr-datemonth-indicator2"/>
      </div>
      <div class="box-tools pull-right">
        <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
      </div>
    </div>
    <center><i id="spinner-cust" style="display:none;" class="fa fa-gear fa-spin" style="font-size:24px"></i></center>
    <div class="box-body">
      <div class="row">
        <div class="col-md-10">
          <div class="chart">
            <canvas id="ProjectedSalesForMonthChart" style="height:250px"></canvas>
          </div>
        </div>
        <div class="col-md-2">
          <ul class="chart-legend clearfix">
            <li><i class="fa fa-circle-o text-gray"></i> Actual</li>
            <li><i class="fa fa-circle-o text-black"></i> Target</li>
            <!-- Colour code the graphic based on kpi thresholds -->
            <?php
            if ($projmonthsalespc < $G_kpithreshold1) $class="text-red";
            if ($projmonthsalespc >= $G_kpithreshold1 AND $dailysalespc < $G_kpithreshold2) $class="text-yellow";
            if ($projmonthsalespc >= $G_kpithreshold2) $class="text-green";
            if (empty($projmonthsalespc)) { $class="text-green"; }
            ?>
            <li><i id="month-projected-color" class="fa fa-circle-o <?= $class?>"></i> Projected</li>
          </ul>
        </div>
      </div>
    </div>
    <!-- /.box-body -->
  </div>
  <!-- /.box -->
  </div>
<?php endif; ?>
<!------------------------------------------------------------------------------------------------------------------>
<!-- PROJECTED SALES YEAR CHART -->
<!------------------------------------------------------------------------------------------------------------------>
<?php if ($canSeeProjectedSalesYear): ?>
  <?php echo ($canSeeProjectedSales) ? '<div class="col-md-6">' : '<div class="col-md-12">'; ?>
  <!-- Colour code the graphic based on kpi thresholds -->
  <?php
  if ($projyearsalespc < $G_kpithreshold1) $class="box-danger";
  if ($projyearsalespc >= $G_kpithreshold1 AND $projyearsalespc < $G_kpithreshold2) $class="box-warning";
  if ($projyearsalespc >= $G_kpithreshold2) $class="box-success";
  if (empty($projyearsalespc)) { $class="box-success"; }
  ?>
  <div class="box <?= $class?>">
    <!-- Colour code the graphic based on kpi thresholds -->
    <?php
    if ($projyearsalespc < $G_kpithreshold1) $class="bg-red";
    if ($projyearsalespc >= $G_kpithreshold1 AND $projyearsalespc < $G_kpithreshold2) $class="bg-yellow";
    if ($projyearsalespc >= $G_kpithreshold2) $class="bg-green";
    if (empty($projyearsalespc)) { $class="bg-green"; }
    ?>
    <div class="box-header with-border <?= $class?>">
      <i class="fa fa-line-chart"></i><h3 class="box-title">Projected Sales - Year</h3>
      <div class="box-tools pull-right">
        <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
      </div>
    </div>
    <div class="box-body">
      <div class="row">
        <div class="col-md-10">
          <div class="chart">
            <canvas id="ProjectedSalesForYearChart" style="height:250px"></canvas>
          </div>
        </div>
        <div class="col-md-2">
          <ul class="chart-legend clearfix">
            <li><i class="fa fa-circle-o text-gray"></i> Actual</li>
            <li><i class="fa fa-circle-o text-black"></i> Target</li>
            <!-- Colour code the graphic based on kpi thresholds -->
            <?php
            if ($projyearsalespc < $G_kpithreshold1) $class="text-red";
            if ($projyearsalespc >= $G_kpithreshold1 AND $projyearsalespc < $G_kpithreshold2) $class="text-yellow";
            if ($projyearsalespc >= $G_kpithreshold2) $class="text-green";
            if (empty($projyearsalespc)) { $class="text-green"; }
            ?>
            <li><i class="fa fa-circle-o <?= $class?>"></i> Projected</li>
          </ul>
        </div>
      </div>
    </div>
    <!-- /.box-body -->
  </div>
  <!-- /.box -->
  </div>
<?php endif; ?>
<script>
  $(function (){
    if ($("#ProjectedSalesForMonthChart").length)
    {
      // Get context with jQuery - using jQuery's .get() method.
      var ProjectedSalesForMonthChartCanvas = $("#ProjectedSalesForMonthChart").get(0).getContext("2d");
      // This will get the first returned node in the jQuery collection.
      var ProjectedSalesForMonthChart = new Chart(ProjectedSalesForMonthChartCanvas);
      var ProjectedSalesForMonthChartData = {
        labels: <?php echo $ProjectedSalesMonthGraphLabel ?>,
        datasets: [
          {
            label: "Actual",
            fillColor: "#d2d6de", // Gray
            strokeColor: "#d2d6de",
            pointColor: "#d2d6de",
            pointStrokeColor: "#d2d6de",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: <?php echo $ProjectedSalesMonthGraphActual?> // [156538, 217208, 266948, 266948, 266948, 341905, 411450, 474286, 540821, 591018, 591018, 591018, 654755, 718922, 777295, 825292, 875581, 875581, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
          },
          {
            label: "Target",
            fillColor: "#000000",
            strokeColor: "#000000",
            pointColor: "#000000",
            pointStrokeColor: "#000000",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(60,141,188,1)",
            data : <?php echo $ProjectedSalesMonthGraphTarget?> //[41667, 83333, 125000, 166667, 208333, 250000, 291667, 333333, 375000, 416667, 458333, 500000, 541667, 583333, 625000, 666667, 708333, 750000, 791667, 833333, 875000, 916667, 958333, 1000000, 1041667, 1083333, 1125000, 1166667, 1208333, 1250000 ]
          },
          {
            label: "Projected",
            fillColor: <?php if(empty($projmonthsalespc)){ echo "'#00a65a'"; }elseif(empty($projmonthsalespc)){ $re= "'#00a65a'"; }elseif ($projmonthsalespc < $G_kpithreshold1) { echo "'#dd4b39'"; } elseif ($projmonthsalespc >= $G_kpithreshold1 AND $projmonthsalespc < $G_kpithreshold2) {echo "'#f39c12'";} elseif ($projmonthsalespc > $G_kpithreshold2) {echo "'#00a65a'";}else{echo "'#00000'";}?>,
            strokeColor: <?php if(empty($projmonthsalespc)){ echo "'#00a65a'"; }elseif ($projmonthsalespc < $G_kpithreshold1) { echo "'#dd4b39'"; } elseif ($projmonthsalespc >= $G_kpithreshold1 AND $projmonthsalespc < $G_kpithreshold2) {echo "'#f39c12'";} elseif ($projmonthsalespc > $G_kpithreshold2) {echo "'#00a65a'";}else{echo "'#00000'";} ?>,
            pointColor: <?php if(empty($projmonthsalespc)){ echo "'#00a65a'"; }elseif ($projmonthsalespc < $G_kpithreshold1) { echo "'#dd4b39'"; } elseif ($projmonthsalespc >= $G_kpithreshold1 AND $projmonthsalespc < $G_kpithreshold2) {echo "'#f39c12'";} elseif ($projmonthsalespc > $G_kpithreshold2) {echo "'#00a65a'";}else{echo "'#00000'";}?>,
            pointStrokeColor: <?php if(empty($projmonthsalespc)){ echo "'#00a65a'"; }elseif ($projmonthsalespc < $G_kpithreshold1) { echo "'#dd4b39'"; } elseif ($projmonthsalespc >= $G_kpithreshold1 AND $projmonthsalespc < $G_kpithreshold2) {echo "'#f39c12'";} elseif ($projmonthsalespc > $G_kpithreshold2) {echo "'#00a65a'";}else{echo "'#00000'";}?>,
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(60,141,188,1)",
            data: <?php echo $ProjectedSalesMonthGraphProjected?> //[51505,103010,154514,206019,257524,309029,360533,412038,463543,515048,566552,618057,669562,721067,772571,824076,875581,927086,978591,1030095,1081600,1133105,1184610,1236114,1287619,1339124,1390629,1442133,1493638,1545143]
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

    }


    //--------------
    //- PROJECTED SALES FOR YEAR CHART
    //--------------
    if ($("#ProjectedSalesForYearChart").length)
    {
      // Get context with jQuery - using jQuery's .get() method.
      var ProjectedSalesForYearChartCanvas = $("#ProjectedSalesForYearChart").get(0).getContext("2d");
      // This will get the first returned node in the jQuery collection.
      var ProjectedSalesForYearChart = new Chart(ProjectedSalesForYearChartCanvas);
      <?php
      $labels = array("J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D");
      for ($i = 1; $i < $yearstartmonth; $i++) {
        $tmp = array_shift($labels);
        array_push($labels, $tmp);
      }
      ?>

      var ProjectedSalesForYearChartData = {
        labels: JSON.parse('<?php echo json_encode($labels); ?>'),
        datasets: [
          {
            label: "Actual",
            fillColor: "#d2d6de", // Gray
            strokeColor: "#d2d6de",
            pointColor: "#d2d6de",
            pointStrokeColor: "#d2d6de",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data:  <?php echo $ProjectedSalesYearGraphActual?> // [1154047, 2364833, 3663974, 4954779, 6186292, 7488464, 7968665, 0, 0, 0, 0, 0 ]
          },
          {
            label: "Target",
            fillColor: "#000000",
            strokeColor: "#000000",
            pointColor: "#000000",
            pointStrokeColor: "#000000",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(60,141,188,1)",
            data : <?php echo $ProjectedSalesYearGraphTarget?> // [1250000, 2500000, 3750000, 5000000, 6250000, 7500000, 8750000, 10000000, 11250000, 12500000, 13750000, 15000000 ]
          },
          {
            label: "Projected",
            fillColor: <?php if(empty($projyearsalespc)){ echo "'#00a65a'"; }elseif ($projyearsalespc < $G_kpithreshold1) { echo "'#dd4b39'"; } elseif ($projyearsalespc >= $G_kpithreshold1 AND $projyearsalespc < $G_kpithreshold2) {echo "'#f39c12'";} elseif ($projyearsalespc > $G_kpithreshold2) {echo "'#00a65a'";} else{ echo "'#fff'";}?>,
            strokeColor: <?php if(empty($projyearsalespc)){ echo "'#00a65a'"; }elseif ($projyearsalespc < $G_kpithreshold1) { echo "'#dd4b39'"; } elseif ($projyearsalespc >= $G_kpithreshold1 AND $projyearsalespc < $G_kpithreshold2) {echo "'#f39c12'";} elseif ($projyearsalespc > $G_kpithreshold2) {echo "'#00a65a'";}else{ echo "'#fff'";}?>,
            pointColor: <?php if(empty($projyearsalespc)){ echo "'#00a65a'"; }elseif ($projyearsalespc < $G_kpithreshold1) { echo "'#dd4b39'"; } elseif ($projyearsalespc >= $G_kpithreshold1 AND $projyearsalespc < $G_kpithreshold2) {echo "'#f39c12'";} elseif ($projyearsalespc > $G_kpithreshold2) {echo "'#00a65a'";}else{ echo "'#fff'";}?>,
            pointStrokeColor: <?php if(empty($projyearsalespc)){ echo "'#00a65a'"; }elseif ($projyearsalespc < $G_kpithreshold1) { echo "'#dd4b39'"; } elseif ($projyearsalespc >= $G_kpithreshold1 AND $projyearsalespc < $G_kpithreshold2) {echo "'#f39c12'";} elseif ($projyearsalespc > $G_kpithreshold2) {echo "'#00a65a'";}else{ echo "'#fff'";}?>,
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(60,141,188,1)",
            data: <?php echo $ProjectedSalesYearGraphProjected?>
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
  })



</script>
