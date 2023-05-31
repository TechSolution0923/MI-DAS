<script>
	$(function()
	{
		//------------------------------------------------------------------------------------------------------------------
		//-- This Year Vs Target Chart --
		//------------------------------------------------------------------------------------------------------------------

<?php
		if ($year0ChartValues != null && $targetDataForCurrentYear != null)
		{
			$labels = array("J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D");
			for ($i = 1; $i < $yearstartmonth; $i++) {
				$tmp = array_shift($labels);
				array_push($labels, $tmp);
			}
?>
			var ThisYearVsTargetChartCanvas = $("#this-year-vs-target").get(0).getContext("2d");
			var ThisYearVsTarget = new Chart(ThisYearVsTargetChartCanvas);

			var ThisYearVsTargetChartData =
			{
				labels   : JSON.parse('<?php echo json_encode($labels); ?>'),
				datasets :
				[
					{
						label                : "<?php echo $year0; ?>",
						fillColor            : "#001f3f",
						strokeColor          : "#001f3f",
						pointColor           : "#001f3f",
						pointStrokeColor     : "#001f3f",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(220,220,220,1)",
						data                 : <?php echo $year0ChartValues; ?>
					},
					{
						label                : "<?php echo $year1; ?>",
						fillColor            : "#3c8dbc",
						strokeColor          : "#3c8dbc",
						pointColor           : "#3c8dbc",
						pointStrokeColor     : "#3c8dbc",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(60,141,188,1)",
						data                 : <?php echo $targetDataForCurrentYear; ?>
					}
				]
			};

			var ThisYearVsTargetChartOptions =
			{
				//Boolean - If we should show the scale at all
				showScale                : true,
				//Boolean - Whether grid lines are shown across the chart
				scaleShowGridLines       : false,
				//String - Colour of the grid lines
				scaleGridLineColor       : "rgba(0,0,0,.05)",
				//Number - Width of the grid lines
				scaleGridLineWidth       : 1,
				//Boolean - Whether to show horizontal lines (except X axis)
				scaleShowHorizontalLines : true,
				//Boolean - Whether to show vertical lines (except Y axis)
				scaleShowVerticalLines   : true,
				//Boolean - Whether the line is curved between points
				bezierCurve              : true,
				//Number - Tension of the bezier curve between points
				bezierCurveTension       : 0.3,
				//Boolean - Whether to show a dot for each point
				pointDot                 : false,
				//Number - Radius of each point dot in pixels
				pointDotRadius           : 4,
				//Number - Pixel width of point dot stroke
				pointDotStrokeWidth      : 1,
				//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
				pointHitDetectionRadius  : 20,
				//Boolean - Whether to show a stroke for datasets
				datasetStroke            : true,
				//Number - Pixel width of dataset stroke
				datasetStrokeWidth       : 2,
				//Boolean - Whether to fill the dataset with a color
				datasetFill              : false,
				//String - A legend template
				legendTemplate           : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i = 0; i < datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if (datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
				//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
				maintainAspectRatio      : true,
				//Boolean - whether to make the chart responsive to window resizing
				responsive               : true,
			};

			//Create the line chart
			ThisYearVsTarget.Line(ThisYearVsTargetChartData, ThisYearVsTargetChartOptions);
<?php
		}
?>
		//------------------------------------------------------------------------------------------------------------------
		//-- This Year Cml. Vs Target Cml. Chart --
		//------------------------------------------------------------------------------------------------------------------

<?php
		if ($cumulativeYear0ChartValues != null && $cumulativeTargetDataForCurrentYear != null)
		{
			$labels = array("J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D");
			for ($i = 1; $i < $yearstartmonth; $i++) {
				$tmp = array_shift($labels);
				array_push($labels, $tmp);
			}
?>
			var ThisYearCmlVsTargetCmlChartCanvas = $("#this-year-cml-vs-target-cml").get(0).getContext("2d");
			var ThisYearCmlVsTargetCml = new Chart(ThisYearCmlVsTargetCmlChartCanvas);

			var ThisYearCmlVsTargetCmlChartData =
			{
				labels   : JSON.parse('<?php echo json_encode($labels); ?>'),
				datasets :
				[
					{
						label                : "<?php echo $year0; ?>",
						fillColor            : "#001f3f",
						strokeColor          : "#001f3f",
						pointColor           : "#001f3f",
						pointStrokeColor     : "#001f3f",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(220,220,220,1)",
						data                 : <?php echo $cumulativeYear0ChartValues; ?>
					},
					{
						label                : "<?php echo $year1; ?>",
						fillColor            : "#3c8dbc",
						strokeColor          : "#3c8dbc",
						pointColor           : "#3c8dbc",
						pointStrokeColor     : "#3c8dbc",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(60,141,188,1)",
						data                 : <?php echo $cumulativeTargetDataForCurrentYear; ?>
					}
				]
			};

			var ThisYearCmlVsTargetCmlChartOptions =
			{
				//Boolean - If we should show the scale at all
				showScale                : true,
				//Boolean - Whether grid lines are shown across the chart
				scaleShowGridLines       : false,
				//String - Colour of the grid lines
				scaleGridLineColor       : "rgba(0,0,0,.05)",
				//Number - Width of the grid lines
				scaleGridLineWidth       : 1,
				//Boolean - Whether to show horizontal lines (except X axis)
				scaleShowHorizontalLines : true,
				//Boolean - Whether to show vertical lines (except Y axis)
				scaleShowVerticalLines   : true,
				//Boolean - Whether the line is curved between points
				bezierCurve              : true,
				//Number - Tension of the bezier curve between points
				bezierCurveTension       : 0.3,
				//Boolean - Whether to show a dot for each point
				pointDot                 : false,
				//Number - Radius of each point dot in pixels
				pointDotRadius           : 4,
				//Number - Pixel width of point dot stroke
				pointDotStrokeWidth      : 1,
				//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
				pointHitDetectionRadius  : 20,
				//Boolean - Whether to show a stroke for datasets
				datasetStroke            : true,
				//Number - Pixel width of dataset stroke
				datasetStrokeWidth       : 2,
				//Boolean - Whether to fill the dataset with a color
				datasetFill              : false,
				//String - A legend template
				legendTemplate           : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i = 0; i < datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if (datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
				//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
				maintainAspectRatio      : true,
				//Boolean - whether to make the chart responsive to window resizing
				responsive               : true,
			};

			//Create the line chart
			ThisYearCmlVsTargetCml.Line(ThisYearCmlVsTargetCmlChartData,ThisYearCmlVsTargetCmlChartOptions);
<?php
		}
?>
		//------------------------------------------------------------------------------------------------------------------
		//-- This Year Vs Last Year Chart --
		//------------------------------------------------------------------------------------------------------------------

<?php
		if ($year0ChartValues != null && $year1ChartValues != null && $year2ChartValues != null)
		{
			$labels = array("J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D");
			for ($i = 1; $i < $yearstartmonth; $i++) {
				$tmp = array_shift($labels);
				array_push($labels, $tmp);
			}
?>
			var ThisYearVsLastYearChartCanvas = $("#this-year-vs-last-year").get(0).getContext("2d");
			var ThisYearVsLastYear = new Chart(ThisYearVsLastYearChartCanvas);

			var ThisYearVsLastYearChartData =
			{
				labels   : JSON.parse('<?php echo json_encode($labels); ?>'),
				datasets :
				[
					{
						label                : "<?php echo $year0; ?>",
						fillColor            : "#001f3f",
						strokeColor          : "#001f3f",
						pointColor           : "#001f3f",
						pointStrokeColor     : "#001f3f",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(220,220,220,1)",
						data                 : <?php echo $year0ChartValues; ?>,
					},
					{
						label                : "<?php echo $year1; ?>",
						fillColor            : "#3c8dbc",
						strokeColor          : "#3c8dbc",
						pointColor           : "#3c8dbc",
						pointStrokeColor     : "#3c8dbc",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(60,141,188,1)",
						data                 : <?php echo $year1ChartValues; ?>,
					},
					{
						label                : "<?php echo $year2; ?>",
						fillColor            : "#d2d6de",
						strokeColor          : "#d2d6de",
						pointColor           : "#d2d6de",
						pointStrokeColor     : "#d2d6de",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(0,133,72,1)",
						data                 : <?php echo $year2ChartValues; ?>,
					}
				]
			};

			var ThisYearVsLastYearChartOptions =
			{
				//Boolean - If we should show the scale at all
				showScale                : true,
				//Boolean - Whether grid lines are shown across the chart
				scaleShowGridLines       : false,
				//String - Colour of the grid lines
				scaleGridLineColor       : "rgba(0,0,0,.05)",
				//Number - Width of the grid lines
				scaleGridLineWidth       : 1,
				//Boolean - Whether to show horizontal lines (except X axis)
				scaleShowHorizontalLines : true,
				//Boolean - Whether to show vertical lines (except Y axis)
				scaleShowVerticalLines   : true,
				//Boolean - Whether the line is curved between points
				bezierCurve              : true,
				//Number - Tension of the bezier curve between points
				bezierCurveTension       : 0.3,
				//Boolean - Whether to show a dot for each point
				pointDot                 : false,
				//Number - Radius of each point dot in pixels
				pointDotRadius           : 4,
				//Number - Pixel width of point dot stroke
				pointDotStrokeWidth      : 1,
				//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
				pointHitDetectionRadius  : 20,
				//Boolean - Whether to show a stroke for datasets
				datasetStroke            : true,
				//Number - Pixel width of dataset stroke
				datasetStrokeWidth       : 2,
				//Boolean - Whether to fill the dataset with a color
				datasetFill              : false,
				//String - A legend template
				legendTemplate           : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i = 0; i < datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if (datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
				//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
				maintainAspectRatio      : true,
				//Boolean - whether to make the chart responsive to window resizing
				responsive               : true,
			};

			//Create the line chart
			ThisYearVsLastYear.Line(ThisYearVsLastYearChartData, ThisYearVsLastYearChartOptions);
<?php
		}
?>
		//------------------------------------------------------------------------------------------------------------------
		//-- This Year Cml. Vs Last Year Cml. Chart --
		//------------------------------------------------------------------------------------------------------------------

<?php
		if ($cumulativeYear0ChartValues != null && $cumulativeYear1ChartValues != null && $cumulativeYear2ChartValues != null)
		{
			$labels = array("J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D");
			for ($i = 1; $i < $yearstartmonth; $i++) {
				$tmp = array_shift($labels);
				array_push($labels, $tmp);
			}
?>
			var ThisYearCmlVsLastYearCmlChartCanvas = $("#this-year-cml-vs-last-year-cml").get(0).getContext("2d");
			var ThisYearCmlVsLastYearCml = new Chart(ThisYearCmlVsLastYearCmlChartCanvas);

			var ThisYearCmlVsLastYearCmlChartData =
			{
				labels   : JSON.parse('<?php echo json_encode($labels); ?>'),
				datasets :
				[
					{
						label                : "<?php echo $year0; ?>",
						fillColor            : "#001f3f",
						strokeColor          : "#001f3f",
						pointColor           : "#001f3f",
						pointStrokeColor     : "#001f3f",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(220,220,220,1)",
						data                 : <?php echo $cumulativeYear0ChartValues; ?>
					},
					{
						label                : "<?php echo $year1; ?>",
						fillColor            : "#3c8dbc",
						strokeColor          : "#3c8dbc",
						pointColor           : "#3c8dbc",
						pointStrokeColor     : "#3c8dbc",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(60,141,188,1)",
						data                 : <?php echo $cumulativeYear1ChartValues; ?>
					},
					{
						label                : "<?php echo $year2; ?>",
						fillColor            : "#d2d6de",
						strokeColor          : "#d2d6de",
						pointColor           : "#d2d6de",
						pointStrokeColor     : "#d2d6de",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(0,133,72,1)",
						data                 : <?php echo $cumulativeYear2ChartValues; ?>
					}
				]
			};

			var ThisYearCmlVsLastYearCmlChartOptions =
			{
				//Boolean - If we should show the scale at all
				showScale                : true,
				//Boolean - Whether grid lines are shown across the chart
				scaleShowGridLines       : false,
				//String - Colour of the grid lines
				scaleGridLineColor       : "rgba(0,0,0,.05)",
				//Number - Width of the grid lines
				scaleGridLineWidth       : 1,
				//Boolean - Whether to show horizontal lines (except X axis)
				scaleShowHorizontalLines : true,
				//Boolean - Whether to show vertical lines (except Y axis)
				scaleShowVerticalLines   : true,
				//Boolean - Whether the line is curved between points
				bezierCurve              : true,
				//Number - Tension of the bezier curve between points
				bezierCurveTension       : 0.3,
				//Boolean - Whether to show a dot for each point
				pointDot                 : false,
				//Number - Radius of each point dot in pixels
				pointDotRadius           : 4,
				//Number - Pixel width of point dot stroke
				pointDotStrokeWidth      : 1,
				//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
				pointHitDetectionRadius  : 20,
				//Boolean - Whether to show a stroke for datasets
				datasetStroke            : true,
				//Number - Pixel width of dataset stroke
				datasetStrokeWidth       : 2,
				//Boolean - Whether to fill the dataset with a color
				datasetFill              : false,
				//String - A legend template
				legendTemplate           : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i = 0; i < datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if (datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
				//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
				maintainAspectRatio      : true,
				//Boolean - whether to make the chart responsive to window resizing
				responsive               : true,
			};

			//Create the line chart
			ThisYearCmlVsLastYearCml.Line(ThisYearCmlVsLastYearCmlChartData, ThisYearCmlVsLastYearCmlChartOptions);
<?php
		}
?>
		//------------------------------------------------------------------------------------------------------------------
		//-- Quantity This Year Vs Last Year Chart --
		//------------------------------------------------------------------------------------------------------------------

<?php
		if ($quantityYear0ChartValues != null && $quantityYear1ChartValues != null)
		{
			$labels = array("J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D");
			for ($i = 1; $i < $yearstartmonth; $i++) {
				$tmp = array_shift($labels);
				array_push($labels, $tmp);
			}
?>
			var QuantityThisYearVsLastYearChartCanvas = $("#quantity-this-year-vs-last-year").get(0).getContext("2d");
			var QuantityThisYearVsLastYear = new Chart(QuantityThisYearVsLastYearChartCanvas);

			var QuantityThisYearVsLastYearChartData =
			{
				labels   : JSON.parse('<?php echo json_encode($labels); ?>'),
				datasets :
				[
					{
						label                : "<?php echo $year0; ?>",
						fillColor            : "#001f3f",
						strokeColor          : "#001f3f",
						pointColor           : "#001f3f",
						pointStrokeColor     : "#001f3f",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(220,220,220,1)",
						data                 : <?php echo $quantityYear0ChartValues; ?>
					},
					{
						label                : "<?php echo $year1; ?>",
						fillColor            : "#3c8dbc",
						strokeColor          : "#3c8dbc",
						pointColor           : "#3c8dbc",
						pointStrokeColor     : "#3c8dbc",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(60,141,188,1)",
						data                 : <?php echo $quantityYear1ChartValues; ?>
					},
					{
						label                : "<?php echo $year2; ?>",
                        fillColor            : "#d2d6de",
                        strokeColor          : "#d2d6de",
                        pointColor           : "#d2d6de",
                        pointStrokeColor     : "#d2d6de",
                        pointHighlightFill   : "#fff",
                        pointHighlightStroke : "rgba(0,133,72,1)",
						data                 : <?php echo $quantityYear2ChartValues; ?>
					}
				]
			};

			var QuantityThisYearVsLastYearChartOptions =
			{
				//Boolean - If we should show the scale at all
				showScale                : true,
				//Boolean - Whether grid lines are shown across the chart
				scaleShowGridLines       : false,
				//String - Colour of the grid lines
				scaleGridLineColor       : "rgba(0,0,0,.05)",
				//Number - Width of the grid lines
				scaleGridLineWidth       : 1,
				//Boolean - Whether to show horizontal lines (except X axis)
				scaleShowHorizontalLines : true,
				//Boolean - Whether to show vertical lines (except Y axis)
				scaleShowVerticalLines   : true,
				//Boolean - Whether the line is curved between points
				bezierCurve              : true,
				//Number - Tension of the bezier curve between points
				bezierCurveTension       : 0.3,
				//Boolean - Whether to show a dot for each point
				pointDot                 : false,
				//Number - Radius of each point dot in pixels
				pointDotRadius           : 4,
				//Number - Pixel width of point dot stroke
				pointDotStrokeWidth      : 1,
				//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
				pointHitDetectionRadius  : 20,
				//Boolean - Whether to show a stroke for datasets
				datasetStroke            : true,
				//Number - Pixel width of dataset stroke
				datasetStrokeWidth       : 2,
				//Boolean - Whether to fill the dataset with a color
				datasetFill              : false,
				//String - A legend template
				legendTemplate           : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i = 0; i < datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if (datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
				//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
				maintainAspectRatio      : true,
				//Boolean - whether to make the chart responsive to window resizing
				responsive               : true,
			};

			//Create the line chart
			QuantityThisYearVsLastYear.Line(QuantityThisYearVsLastYearChartData, QuantityThisYearVsLastYearChartOptions);
<?php
		}
?>

		//------------------------------------------------------------------------------------------------------------------
		//-- Quantity This Year Cml. Vs Last Year Cml. Chart --
		//------------------------------------------------------------------------------------------------------------------

<?php
		if ($cumulativeQuantityYear0ChartValues != null && $cumulativeQuantityYear1ChartValues != null)
		{
			$labels = array("J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D");
			for ($i = 1; $i < $yearstartmonth; $i++) {
				$tmp = array_shift($labels);
				array_push($labels, $tmp);
			}
?>
			var QuantityThisYearCmlVsLastYearCmlChartCanvas = $("#quantity-this-year-cml-vs-target-cml").get(0).getContext("2d");
			var QuantityThisYearCmlVsLastYearCml = new Chart(QuantityThisYearCmlVsLastYearCmlChartCanvas);

			var QuantityThisYearCmlVsLastYearCmlChartData =
			{
				labels   : JSON.parse('<?php echo json_encode($labels); ?>'),
				datasets :
				[
					{
						label                : "<?php echo $year0; ?>",
						fillColor            : "#001f3f",
						strokeColor          : "#001f3f",
						pointColor           : "#001f3f",
						pointStrokeColor     : "#001f3f",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(220,220,220,1)",
						data                 : <?php echo $cumulativeQuantityYear0ChartValues; ?>
					},
					{
						label                : "<?php echo $year1; ?>",
						fillColor            : "#3c8dbc",
						strokeColor          : "#3c8dbc",
						pointColor           : "#3c8dbc",
						pointStrokeColor     : "#3c8dbc",
						pointHighlightFill   : "#fff",
						pointHighlightStroke : "rgba(60,141,188,1)",
						data                 : <?php echo $cumulativeQuantityYear1ChartValues; ?>
					},
					{
						label                : "<?php echo $year2; ?>",
                        fillColor            : "#d2d6de",
                        strokeColor          : "#d2d6de",
                        pointColor           : "#d2d6de",
                        pointStrokeColor     : "#d2d6de",
                        pointHighlightFill   : "#fff",
                        pointHighlightStroke : "rgba(0,133,72,1)",
						data                 : <?php echo $cumulativeQuantityYear2ChartValues; ?>
					}
				]
			};

			var QuantityThisYearCmlVsLastYearCmlChartOptions =
			{
				//Boolean - If we should show the scale at all
				showScale                : true,
				//Boolean - Whether grid lines are shown across the chart
				scaleShowGridLines       : false,
				//String - Colour of the grid lines
				scaleGridLineColor       : "rgba(0,0,0,.05)",
				//Number - Width of the grid lines
				scaleGridLineWidth       : 1,
				//Boolean - Whether to show horizontal lines (except X axis)
				scaleShowHorizontalLines : true,
				//Boolean - Whether to show vertical lines (except Y axis)
				scaleShowVerticalLines   : true,
				//Boolean - Whether the line is curved between points
				bezierCurve              : true,
				//Number - Tension of the bezier curve between points
				bezierCurveTension       : 0.3,
				//Boolean - Whether to show a dot for each point
				pointDot                 : false,
				//Number - Radius of each point dot in pixels
				pointDotRadius           : 4,
				//Number - Pixel width of point dot stroke
				pointDotStrokeWidth      : 1,
				//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
				pointHitDetectionRadius  : 20,
				//Boolean - Whether to show a stroke for datasets
				datasetStroke            : true,
				//Number - Pixel width of dataset stroke
				datasetStrokeWidth       : 2,
				//Boolean - Whether to fill the dataset with a color
				datasetFill              : false,
				//String - A legend template
				legendTemplate           : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i = 0; i < datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if (datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
				//Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
				maintainAspectRatio      : true,
				//Boolean - whether to make the chart responsive to window resizing
				responsive               : true,
			};

			//Create the line chart
			QuantityThisYearCmlVsLastYearCml.Line(QuantityThisYearCmlVsLastYearCmlChartData, QuantityThisYearCmlVsLastYearCmlChartOptions);
<?php
		}
?>
	});
</script>

<script>
	//Display the correct graph and legend when menu selection made.
	$(function() {

		//Set the default value of the choose-graph select dropdown
		$("#choose-graph").val("this-year-vs-target-option");

		//Hide all the charts except the this-year-vs-target chart
		$("#this-year-cml-vs-target-cml").addClass("hide");
		$("#this-year-vs-last-year").addClass("hide");
		$("#this-year-cml-vs-last-year-cml").addClass("hide");
		$("#quantity-this-year-vs-last-year").addClass("hide");
		$("#quantity-this-year-cml-vs-target-cml").addClass("hide");

		$("#choose-graph").on('change', function() {
			//Hide all graphs and legends.
			$(".chart canvas").addClass("hide");
			$(".chart-legend li").addClass("hide");

			//Unhide the selected graph and legend.
			if ($(this).val() == "this-year-vs-target-option") {
				$("#this-year-vs-target").removeClass("hide");
				$("#this-year-legend").removeClass("hide");
				$("#target-legend").removeClass("hide");
			}
			else if ($(this).val() == "this-year-cml-vs-target-cml-option") {
				$("#this-year-cml-vs-target-cml").removeClass("hide");
				$("#this-year-cml-legend").removeClass("hide");
				$("#target-cml-legend").removeClass("hide");
			}
			else if ($(this).val() == "this-year-vs-last-year-option") {
				$("#this-year-vs-last-year").removeClass("hide");
				$("#this-year-legend").removeClass("hide");
				$("#last-year-legend").removeClass("hide");
				$("#before-year-legend").removeClass("hide");
			}
			else if ($(this).val() == "this-year-cml-vs-last-year-cml-option") {
				$("#this-year-cml-vs-last-year-cml").removeClass("hide");
				$("#this-year-cml-legend").removeClass("hide");
				$("#last-year-cml-legend").removeClass("hide");
				$("#before-year-cml-legend").removeClass("hide");
			}
			else if ($(this).val() == "quantity-this-year-vs-last-year-option") {
				$("#quantity-this-year-vs-last-year").removeClass("hide");
				$("#this-year-legend").removeClass("hide");
				$("#last-year-legend").removeClass("hide");
				$("#before-year-legend").removeClass("hide");
			}
			else if ($(this).val() == "quantity-this-year-cml-vs-target-cml-option") {
				$("#quantity-this-year-cml-vs-target-cml").removeClass("hide");
				$("#this-year-cml-legend").removeClass("hide");
				$("#last-year-cml-legend").removeClass("hide");
				$("#before-year-cml-legend").removeClass("hide");
			}
		});
	});
</script>