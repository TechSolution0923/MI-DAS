<?php

	$account  = $_REQUEST["a"];

    require_once 'dblogin.php';	
	
	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or die ("Unable to connect!"); 

    date_default_timezone_set('Europe/London');

	// Get the years
	
	$year0 = date("Y");
	$year1 = $year0 - 1;
	$year2 = $year0 - 2;
	
	$thismonth = date("m");
	
	$graphlabel0 = $year0;
	$graphlabel1 = $year1;
	$graphlabel2 = $year2;

	
	// --------------------------------------------------------------------------------------------------------------------------------------------------
	// 3 YEAR SALES CHART
	// --------------------------------------------------------------------------------------------------------------------------------------------------

	// Build the query string ... selecting only the fields we need from the customer sales table
	
	$lastfieldno = 24 + ($thismonth-1);
	
	$query = "SELECT ";
	
	$y = 0;
	
	for ($x = $lastfieldno; $x >= 0; $x-- )
	{
		if (!$y == 0)
		{
			$query .= ", ";	// Add a comma to the end if this isnt the first time in
		}
		$query .= "msales$x";	// Add the sales field
		$y++;
	}
	
	$query .= " FROM customersales WHERE account LIKE '$account'";

	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));
	
	// Loop through the selected columns and put them into the sales array
	
	for ($x = 0; $x <= 35; $x++)
	{ 
		$sales[$x] = 0;
	}
	
	while ($row = mysqli_fetch_row($result)) 
	{
		for ($x = 0;$x < count($row);$x++)
		{
			$sales[$x] = $row[$x];
		}
	}
	
	// The following three sections are needed because maybe there is no historical data, but still want the graph to show 0 sales
	// Build the $year2data string for the chart

	$year2data = "[";
	$y = 0;
	$year2total = 0;
	for ($x = 0; $x <= 11; $x++)
	{ 
		$year2data .= "$sales[$x]";
		$year2table .= "<td>".number_format($sales[$x])."</td>";
		$year2total += $sales[$x];
		if ($x != 11) $year2data .= ",";
		$y = $y + 1;
	} 
	$year2data .= "]";
	$year2table .= "<td>".number_format($year2total)."</td>";

	// Build the $year1data string for the chart
	
	$year1data = "[";
	$y = 0;
	$year1total = 0;
	for ($x = 12; $x <= 23; $x++)
	{ 
		$year1data .= "$sales[$x]";
		$year1table .= "<td>".number_format($sales[$x])."</td>";
		$year1total += $sales[$x];
		if ($x != 23) $year1data .= ",";
		$y = $y + 1;
	} 
	$year1data .= "]";
	$year1table .= "<td>".number_format($year1total)."</td>";

	// Build the $year0data string for the chart
	
	$year0data = "[";
	$y = 0;
	$year0total = 0;
	for ($x = 24; $x <= 35; $x++)
	{ 
		$year0data .= "$sales[$x]";
		$year0table .= "<td>".number_format($sales[$x])."</td>";
		$year0total += $sales[$x];
		if ($x != 35) $year0data .= ",";
		$y = $y + 1;
	} 
	$year0data .= "]";
	$year0table .= "<td>".number_format($year0total)."</td>";

	// free result set memory 
    mysqli_free_result($result); 
        
    // close connection 
    mysqli_close($link);

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MI-DAS | Management Information Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="./AdminLTE-2.3.3/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="./AdminLTE-2.3.3/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="./AdminLTE-2.3.3/dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

   <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Customer Sales Analysis Graph
      </h1>

    </section>

    <!-- Main content -->
    <section class="content">

	
      <!-- /.row -->
	  
	<div class="row">
		<div class="col-md-12">	<!-- Main left hand side of dashboard -->
			
			<!------------------------------------------------------------------------------------------------------------------>
			<!-- 3 YEAR SALES CHART -->
			<!------------------------------------------------------------------------------------------------------------------>
			
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs pull-right">
					<li class="active"><a href="#threeyearsaleschart" data-toggle="tab"><i class="fa fa-line-chart"></i></a></li>
					<li><a href="#threeyearsalestable" data-toggle="tab"><i class="fa fa-table"></i></a></li>
					<li class="pull-left header"><i class="fa fa-shopping-cart"></i>Sales</li>
				</ul>
				<div class="tab-content no-padding">
					<div class="tab-pane active" id="threeyearsaleschart" style="position: relative;">
						<div class="row">
							<div class="col-md-10">
								<div class="chart">
									<canvas id="SalesChart" style="height:250px"></canvas>
								</div>
							</div>
							<div class="col-md-2">
								<ul class="chart-legend clearfix">
									<li><i class="fa fa-circle-o text-navy"></i> <?php echo $year0?></li>
									<li><i class="fa fa-circle-o text-light-blue"></i> <?php echo $year1?></li>
									<li><i class="fa fa-circle-o text-gray"></i> <?php echo $year2?></li>
								</ul>
							</div>
						</div>
					</div>	<!-- class="tab-pane" -->
					<div class="tab-pane" id="threeyearsalestable" style="position: relative;">
					
						<table class="table table-striped">
							<tr>
								<th>Year</th>
								<th>Jan</th>
								<th>Feb</th>
								<th>Mar</th>
								<th>Apr</th>
								<th>May</th>
								<th>Jun</th>
								<th>Jul</th>
								<th>Aug</th>
								<th>Sep</th>
								<th>Oct</th>
								<th>Nov</th>
								<th>Dec</th>
								<th>Total</th>
							</tr>
							<tr>
								<td><?php echo $year0?></td>
								<?php echo $year0table ?>
							</tr>
							<tr>
								<td><?php echo $year1?></td>
								<?php echo $year1table ?>
							</tr>
							<tr>
								<td><?php echo $year2?></td>
								<?php echo $year2table ?>
							</tr>
						</table>
					
					</div>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->		
		</div>	<!-- Main left hand side of dashboard col-md-9 -->

	</div>	<!-- row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.3.3
    </div>
    <strong>Copyright &copy; 2016 <a href="http://www.kk-cs.co.uk">Kieran Kelly Consultancy Services Ltd.</a>.</strong> All rights
    reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane" id="control-sidebar-home-tab">
        <!-- /.control-sidebar-menu -->


        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.0 -->
<script src="./AdminLTE-2.3.3/plugins/jQuery/jQuery-2.2.0.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="./AdminLTE-2.3.3/bootstrap/js/bootstrap.min.js"></script>
<!-- ChartJS 1.0.1 -->
<script src="./AdminLTE-2.3.3/plugins/chartjs/Chart.min.js"></script>
<!-- FastClick -->
<script src="./AdminLTE-2.3.3/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="./AdminLTE-2.3.3/dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="./AdminLTE-2.3.3/dist/js/demo.js"></script>

<!-- page script -->
<script>
  $(function () {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- 3 YEAR SALES CHART
    //--------------

    // Get context with jQuery - using jQuery's .get() method.
    var SalesChartCanvas = $("#SalesChart").get(0).getContext("2d");
    // This will get the first returned node in the jQuery collection.
    var SalesChart = new Chart(SalesChartCanvas);

    var SalesChartData = {
      labels: ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"],
      datasets: [
        {
          label: "<?echo $year0?>",
          fillColor: "#001f3f",
          strokeColor: "#001f3f",
          pointColor: "#001f3f",
          pointStrokeColor: "#001f3f",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(220,220,220,1)",
		  data: <?php echo $year0data ?> // [1154047,1210785,1299141,1290804,1103012,1301590,480219,0,0,0,0,0]
		},
        {
          label: "<?echo $year1?>",
          fillColor: "#3c8dbc",
          strokeColor: "#3c8dbc",
          pointColor: "#3c8dbc",
          pointStrokeColor: "#3c8dbc",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(60,141,188,1)",
		  data : <?php echo $year1data ?> //[1217843,1194532,1304174,1166936,1145749,1281106,1377170,1172458,1337092,1380822,1326835,1025717]
        },
        {
          label: "<?echo $year2?>",
          fillColor: "#d2d6de",
          strokeColor: "#d2d6de",
          pointColor: "#d2d6de",
          pointStrokeColor: "#d2d6de",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(60,141,188,1)",
		  data: <?php echo $year2data ?> //[1066799,968009,1085081,1041893,1104702,1204020,1122788,1029475,1224391,1271400,1153435,1046551]
        }

		]
    };

    var SalesChartOptions = {
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
    SalesChart.Line(SalesChartData, SalesChartOptions);

  });
  
  


</script>
</body>
</html>
