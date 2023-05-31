
<?php
$threeData = canSeeThreeInfo();
$canSeeProjectedSales = $threeData['seeprojectedsales'];
$canSeeProjectedSalesYear = $threeData['seeprojectedsalesyear'];
$canSeeOrderFulfillment = $threeData['seeorderfulfillment'];

	$currency_symbol = $this->config->item("currency_symbol"); ?>
	<section class="content-header">
	<h1> Dashboard </h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Dashboard </li>
	</ol>
	</section>
	<!-- Main content -->
	<section class="content content-dashboard">
	<!-- Info boxes -->
	<div class="row">
		<div class="<?php if (!!$canSeeMargins) { ?>col-md-3 col-sm-6 col-xs-12<?php } else {?>col-md-6<?php }?>">
			<?php
			if ($dailysalespc < $G_kpithreshold1) $class="bg-red";
			if ($dailysalespc >= $G_kpithreshold1 AND $dailysalespc < $G_kpithreshold2) $class="bg-yellow";
			if ($dailysalespc >= $G_kpithreshold2) $class="bg-green";
			if (empty($G_DailySalesTarget)) $class="bg-green";
		?>
		<div class="info-box <?= $class?>" id="sales-previous-daydrill-report">
			<a style="color: white;text-decoration: none;" href="<?= base_url().'site/daydrillreport'; ?>">
			<span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
			<div class="info-box-content">
				<span class="info-box-title">Sales for <?= $lastsalesdate ?> </span>
				<span class="info-box-number"><?= $currency_symbol; ?><?= number_format($dailysales)?></span>
				<div class="progress">
					<div class="progress-bar" style="width: <?= $dailysalespc?>% !important;"></div>
				</div>
				<span class="progress-description">
					<?= number_format($dailysalespc,0)?>% of target (<?= $currency_symbol; ?><?= number_format($G_DailySalesTarget)?>)
				</span>
			</div>
				</a>
					</div>
				</div>
		<!-- /.info-box -->
	<?php
			if ($dailymarginpc < $G_MarginOk) $class="bg-red";
			if ($dailymarginpc >= $G_MarginOk AND $dailymarginpc < $G_MarginGood) $class="bg-yellow";
			if ($dailymarginpc >= $G_MarginGood) $class="bg-green";
			if (empty($G_MarginOk) && empty($G_MarginGood)) { $class="bg-green"; }
		?>
		<?php if (!!$canSeeMargins) { ?>
		<!-- /.col -->
		<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box <?= $class?>">
			<span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
			<div class="info-box-content">
			<span class="info-box-title">Margin for <?= $lastsalesdate?></span>
			<span class="info-box-number"><?= $currency_symbol; ?><?= number_format($dailymargin)?></span>
			<div class="progress">
				<div class="progress-bar" style="width: <?= $dailymarginpc?>%"></div>
			</div>
				<span class="progress-description">
					<?= number_format($dailymarginpc,2)?>%
				</span>
			</div>
			</div>
		</div>
		<!-- /.col -->
		<?php } ?>
		<!-- fix for small devices only -->
		<div class="<?php if (!!$canSeeMargins) { ?>col-md-3 col-sm-6 col-xs-12<?php } else {?>col-md-6<?php }?>">
					<?php
			if ($monthlysalespc < $G_kpithreshold1) $class="bg-red";
			if ($monthlysalespc >= $G_kpithreshold1 AND $monthlysalespc < $G_kpithreshold2) $class="bg-yellow";
			if ($monthlysalespc >= $G_kpithreshold2) $class="bg-green";
			if (empty($G_MonthlySalesTarget)) $class="bg-green";
		?>
				<div class="info-box <?= $class ?>">
				<a style="color: white;text-decoration: none;" href="<?= base_url().'site/salesmtdreport'; ?>">
			<span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
				<div class="info-box-content">
				<span class="info-box-title">Sales MTD</span>
			<span class="info-box-number"><?= $currency_symbol; ?><?= number_format($monthlysales)?></span>
			<div class="progress">
				<div class="progress-bar" style="width: <?= number_format($monthlysalespc)?>%"></div>
			</div>
				<span class="progress-description">
					<?= number_format($monthlysalespc)?>% of target (<?= $currency_symbol; ?><?= number_format($G_MonthlySalesTarget)?>)
				</span>
			</div>
				</a>
			<!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
		</div>
		<!-- /.col -->
		<!-- /.col -->
		<?php if (!!$canSeeMargins) { ?>
		<div class="col-md-3 col-sm-6 col-xs-12">
		<!-- Colour code the graphic based on margin ok and good values -->
		<?php
			if ($monthlymarginpc < $G_MarginOk) $class="bg-red";
			if ($monthlymarginpc >= $G_MarginOk AND $monthlymarginpc < $G_MarginGood) $class="bg-yellow";
			if ($monthlymarginpc >= $G_MarginGood) $class="bg-green";
			if (empty($G_MarginOk) && empty($G_MarginGood)) { $class="bg-green"; }
		?>
		<div class="info-box <?= $class ?>">
			<span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
			<div class="info-box-content">
			<span class="info-box-title">Margin MTD</span>
			<span class="info-box-number"><?= $currency_symbol; ?><?= number_format($monthlymargin)?></span>
			<div class="progress">
				<div class="progress-bar" style="width: <?= number_format($monthlymarginpc,2)?>%"></div>
			</div>
				<span class="progress-description">
					<?= number_format($monthlymarginpc,2)?>%
				</span>
			</div>
			<!-- /.info-box-content -->
		</div>
		<!-- /.info-box -->
		</div>
		<?php } ?>
		<!-- /.col -->
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="<?php if (!!$canSeeOMR) {?>col-md-9<?php } else {?> col-md-12<?php }?>"> <!-- Main left hand side of dashboard -->
			<div class="row">	<!-- Today's and outstanding orders row -->
				<!------------------------------------------------------------------------------------------------------------------>
				<!-- TODAYS ORDERS - BY TYPE & BY STATUS -->
				<!------------------------------------------------------------------------------------------------------------------>
				<div class="col-md-8">
					<div class="nav-tabs-custom">
						<!-- Tabs within a box -->
						<ul class="nav nav-tabs pull-right">
							<li class="active" onclick="manage_cookie('salestodaydonutcharts','N')"><a href="#salestodaydonutcharts" data-toggle="tab" ><i class="fa fa-pie-chart"></i></a></li>
							<li onclick="manage_cookie('salestodaydonutcharts','Y')" id="salestodaytables_nav"><a href="#salestodaytables" data-toggle="tab"><i class="fa fa-table" ></i></a></li>
							<li class="pull-left header"><i class="fa fa-inbox"></i>Today's Orders</li>
						</ul>
						<div class="tab-content no-padding">
							<div class="tab-pane active" id="salestodaydonutcharts" style="position: relative;">
								<div class="row">
									<div class="col-md-4">
										<p class="text-center">
											<strong>By Type</strong>
										</p>
										<div class="chart">
											<canvas id="TodaysOrdersByType"></canvas>
										</div>
									</div>
									<div class="col-md-2">
										<ul class="chart-legend clearfix">
											<?= $todaysordersbytypelegend ?>
										</ul>
									</div>
									<div class="col-md-4">
										<p class="text-center">
											<strong>By Status</strong>
										</p>
										<div class="chart">
											<canvas id="TodaysOrdersByStatus"></canvas>
										</div>
									</div>
									<div class="col-md-2">
										<ul class="chart-legend clearfix">
											<?= $todaysordersbystatuslegend ?>
										</ul>
									</div>
								</div> <!-- row -->
							</div>
							<div class="tab-pane" id="salestodaytables" style="position: relative;">
								<div class="row">
									<div class="box-body">
										<div class="col-md-6">
											<table class="table table-striped">
												<tr>
													<th>By Type</th>
													<th>Description</th>
													<th style="text-align: right">Value</th>
												</tr>
												<?= $todaysordersbytypetable?>
											</table>
										</div>
										<div class="col-md-6">
											<table class="table table-striped">
												<tr>
													<th>By Status</th>
													<th>Description</th>
													<th style="text-align: right">Value</th>
												</tr>
												<?= $todaysordersbystatustable?>
											</table>
										</div>
									</div> <!-- class="box-body" -->
								</div> <!-- class="row" -->
							</div> <!-- class="tab-pane" -->
						</div> <!-- class="tab-content no-padding" -->
					</div> <!-- class="nav-tabs-custom" -->
				</div> <!-- col-md-8 -->
				<!------------------------------------------------------------------------------------------------------------------>
				<!-- OUTSTANDING ORDERS BY STATUS DONUT CHART -->
				<!------------------------------------------------------------------------------------------------------------------>
				<div class="col-md-4">
					<div class="nav-tabs-custom">
						<!-- Tabs within a box -->
						<ul class="nav nav-tabs pull-right">
							<li class="active"><a href="#outstandingordersdonutchart" data-toggle="tab" onclick="manage_cookie('outstandingordersdonutchart','N')" ><i class="fa fa-pie-chart"></i></a></li>
							<li onclick="manage_cookie('outstandingordersdonutchart','Y')" id="outstandingorderstable_nav"> <a href="#outstandingorderstable" data-toggle="tab"><i class="fa fa-table"></i></a></li>
							<li class="pull-left header" style="padding:0 4px;"><i class="fa fa-hourglass-end"></i>Outstanding Orders</li>
						</ul>
						<div class="tab-content no-padding">
							<div class="tab-pane active" id="outstandingordersdonutchart" style="position: relative;">
								<div class="row">
									<div class="col-md-8">
										<p class="text-center">
											<strong>By Status</strong>
										</p>
										<div class="chart">
											<canvas id="OutstandingOrdersByStatus"></canvas>
										</div>
									</div>
									<div class="col-md-4">
										<ul class="chart-legend clearfix">
											<?= $outstandingordersbystatuslegend ?>
										</ul>
									</div>
								</div>
							</div> <!-- class= "tab-pane" -->
							<div class="tab-pane" id="outstandingorderstable" style="position: relative;">
								<div class="box-body">
									<table class="table table-striped">
										<tr>
											<th>By Status</th>
											<th>Description</th>
											<th style="text-align: right">Value</th>
										</tr>
										<?= $outstandingordersbystatustable ?>
									</table>
								</div> <!-- class="box-body" -->
							</div> <!-- class="tab-pane" -->
						</div> <!-- tab-content -->
					</div> <!-- nav-tabs-custom -->
				</div> <!-- col-md-4 -->
			</div> <!-- Today's and outstanding orders row -->
			<!------------------------------------------------------------------------------------------------------------------>
			<!-- 3 YEAR SALES CHART -->
			<!------------------------------------------------------------------------------------------------------------------>
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs pull-right">
					<li class="active" onclick="manage_cookie('threeyearsaleschart','N')" id="threeyearsaleschart_nav"><a href="#threeyearsaleschart" data-toggle="tab"><i class="fa fa-line-chart"></i></a></li>
					<li onclick="manage_cookie('threeyearsaleschart','Y')" id="threeyearsalestable_nav"><a href="#threeyearsalestable" data-toggle="tab"><i class="fa fa-table"></i></a></li>
					<li class="pull-left header"><i class="fa fa-shopping-cart"></i>Sales</li>
				</ul>
				<div class="tab-content no-padding">
					<div class="tab-pane active" id="threeyearsaleschart" style="position: relative;">
						<div class="row">
							<div class="col-md-9">
								<div class="chart">
									<canvas id="this-year-vs-target" style="height: 250px;"></canvas>
									<canvas id="this-year-cml-vs-target-cml" style="height: 250px;"></canvas>
									<canvas id="this-year-vs-last-year" style="height: 250px;"></canvas>
									<canvas id="this-year-cml-vs-last-year-cml" style="height: 250px;"></canvas>
								</div>
							</div>
							<div class="col-md-1">
								<ul class="chart-legend clearfix">
									<li id="this-year-legend"><i class="fa fa-circle-o text-navy"></i><?= $year0; ?></li>
									<li id="this-year-cml-legend" class="hide"><i class="fa fa-circle-o text-navy"></i><?= $year0; ?> Cml.</li>
									<li id="last-year-legend" class="hide"><i class="fa fa-circle-o text-light-blue"></i><?= $year1; ?></li>
									<li id="last-year-cml-legend" class="hide"><i class="fa fa-circle-o text-light-blue"></i><?= $year1; ?> Cml.</li>
									<li id="before-year-legend" class="hide"><i class="fa fa-circle-o text-gray"></i><?= $year2; ?></li>
									<li id="before-year-cml-legend" class="hide"><i class="fa fa-circle-o text-gray"></i><?= $year2; ?> Cml.</li>
									<li id="target-legend"><i class="fa fa-circle-o text-light-blue"></i>Target</li>
									<li id="target-cml-legend" class="hide"><i class="fa fa-circle-o text-light-blue"></i>Target Cml.</li>
								</ul>
							</div>
							<div class="col-md-2">
								<form>
									<div class="form-group">
										<label for="chose-graph">Choose graph</label>
										<select class="form-control" id="choose-graph">
											<optgroup label="Sales vs Target">
												<option value="this-year-vs-target-option"><?= $year0; ?> vs Target</option>
												<option value="this-year-cml-vs-target-cml-option"><?= $year0; ?> cml. vs Target cml.</option>
											</optgroup>
											<optgroup label="Sales Year Comparison">
												<option value="this-year-vs-last-year-option">Monthly</option>
												<option value="this-year-cml-vs-last-year-cml-option">Cumulative</option>
											</optgroup>
										</select>
									</div>
								</form>
							</div>
						</div>
					</div> <!-- class="tab-pane" -->
					<div class="tab-pane" id="threeyearsalestable" style="position: relative;overflow-x: scroll;">
					<?php //print_r($salesTargetForLastThreeYear); ?>
						<table class="table table-striped">
							<?php
								$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
								for ($i = 1; $i < $yearstartmonth; $i++) {
									$tmp = array_shift($months);
									array_push($months, $tmp);
								}
							?>
							<tr class="border-header">
								<th>Year</th>
								<?php foreach ($months as $month) { ?>
								<th><?php echo $month; ?></th>
								<?php } ?>
								<th>Total</th>
							</tr>
							<?php require_once(BASEPATH.'../application/views/common/twoyearsvstarget.php'); ?>
						</table>
					</div>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
			<?php // if ($_SERVER['REMOTE_ADDR']=='115.112.129.194'){?>
			<div class="row" >
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title">PAC Sales vs Target</h3>
						</div>
						<div class="box-body">
							<div class="">
								<table class="table table-striped" id="example">
									<thead>
										<tr>
											<th>PAC</th>
											<th>Description</th>
											<th>Sales MTD</th>
											<th>Target</th>
											<th>Progress</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($pac1salestarget as $pac1){
										$pac_progress= round($getSalesTotalMonthWise[$pac1->paccode]*100/$pac1->salestarget,2);
										if ($pac1->salestarget=="")
										{
											$pac1->salestarget=0;
										}
										if ($getSalesTotalMonthWise[$pac1->paccode]=="")
										{
											$getSalesTotalMonthWise[$pac1->paccode]=0;
										}
										if ($pac_progress=="")
										{
											$pac_progress=0;
										}
										if ($pac_progress==""||$pac_progress<=30)
										{
										$class="danger";
										}
										elseif ($pac_progress<=30 )
										{
											$class="danger";
										}
										elseif ($pac_progress>30 && $pac_progress<=60)
										{
											$class="warning";
										}
										else
										{
											$class="success";
										}
										?>
										<?php if ($pac1->paccode!=''){?>
										<tr>
											<td><?= $pac1->paccode; ?></td>
											<td><a href="<?= base_url(); ?>products/details2/<?= $pac1->tabl; ?>/<?=$pac1->paccode; ?> "><?= $pac1->description; ?></a></td>
											<td><?= $getSalesTotalMonthWise[$pac1->paccode]; ?></td>
											<td><?= $pac1->salestarget; ?></td>
											<td>
											<div class="progress" style="height:5px;">
											<div class="progress-bar progress-bar-<?= $class; ?>" style="width:<?= $pac_progress; ?>% !important;"></div>
											</div>
											<span class="progress-description">
											<?= $pac_progress; ?>%
											</span>
											</td>
										</tr>
										<?php } ?>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<a href="<?= base_url(); ?>/products/pacsalestargetdata" class="btn btn-info" >See All</a>
						</div>
					</div>
				</div>
			</div>
			<?php // } ?>

			<div class="row">
<?php
				/* ------------------------------------------------------------------------------------------------------------------
				---- QUOTATIONS x PAC1 ----
				------------------------------------------------------------------------------------------------------------------- */
?>
				<div class="col-md-8">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title">Quotations x PAC1</h3>
						</div>
						<div class="box-body" style="max-height: 350px; overflow-y: auto;">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>PAC 1</th>
										<th>Description</th>
										<th>Value this Month</th>
										<th>Qty this Month</th>
									</tr>
								</thead>
								<tbody>
<?php
									$rowCount = count($currentMonthPac1QuoteConversions);
									$rowNumber = 0;
									foreach ($currentMonthPac1QuoteConversions as $currentMonthPac1QuoteConversion)
									{
										//If the last row display the total
										if ($rowCount === ++$rowNumber)
										{
?>
											<tr style="background-color: #e1e1e1; font-style: italic; font-weight: bold; color: black;" class="total-row">
												<td>Total</td>
												<td></td>
												<td class="text-left"><?= number_format($currentMonthPac1QuoteConversion['value_this_month'], 2); ?></td>
												<td class="text-left"><?= number_format($currentMonthPac1QuoteConversion['quantity_this_month'], 0); ?></td>
											</tr>
<?php
										}
										else
										{
?>
											<tr>
												<td><?= $currentMonthPac1QuoteConversion['code']; ?></td>
												<td><?= $currentMonthPac1QuoteConversion['description']; ?></td>
												<td class="text-left"><?= number_format($currentMonthPac1QuoteConversion['value_this_month'], 2); ?></td>
												<td class="text-left"><?= number_format($currentMonthPac1QuoteConversion['quantity_this_month'], 0); ?></td>
											</tr>
<?php
										}
									}
?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

<?php
				/* ------------------------------------------------------------------------------------------------------------------
				---- SALES PIPELINE ----
				------------------------------------------------------------------------------------------------------------------- */
?>
				<div class="col-md-4">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title">Sales Pipeline</h3>
						</div>
						<div class="box-body" style="max-height: 350px; overflow-y: auto;">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Stage</th>
										<th class="text-right">Value</th>
										<th class="text-right">%</th>
									</tr>
								</thead>
								<tbody>
<?php
									$rowCount = count($salesPipelineStages);
									$rowNumber = 0;

									foreach ($salesPipelineStages as $salesPipelineStage)
									{
										//If the last row display the total
										if ($rowCount === ++$rowNumber)
										{
?>
											<tr style="background-color: #e1e1e1; font-style: italic; font-weight: bold; color: black;" class="total-row">
												<td>Total</td>
												<td class="text-right"><?= number_format($salesPipelineStage['value'], 2); ?></td>
												<td class="text-right"></td>
											</tr>
<?php
										}
										else
										{
?>
											<tr>
												<td><?= $salesPipelineStage['description']; ?></td>
												<td class="text-right"><?= number_format($salesPipelineStage['value'], 2); ?></td>
												<td class="text-right"><?= number_format($salesPipelineStage['percentage'], 2); ?>%</td>
											</tr>
<?php
										}
									}
?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<!------------------------------------------------------------------------------------------------------------------>
				<!-- PROJECTED SALES MONTH CHART -->
				<!------------------------------------------------------------------------------------------------------------------>
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
			</div>
			<?php if ($canSeeOrderFulfillment): ?>
				<div class="row">
					<div class="col-md-12"
						<!-- ORDER FULFULMENT - ENTERED TODAY AND AT WDL OR COM -->
						<div class="box box-primary">
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
							<!-- /.box-body -->
						</div>
					</div>
						<!-- /.box -->
				</div>
			<?php endif; ?>
		</div> <!-- Main left hand side of dashboard col-md-9 -->
		<?php if (!!$canSeeOMR) { ?>
		<div class="col-md-3"> <!-- Right hand column of dashboard -->
			<!------------------------------------------------------------------------------------------------------------------>
			<!-- HELD IN OMR -->
			<!------------------------------------------------------------------------------------------------------------------>
			<div class="info-box bg-red">
				<span class="info-box-icon"><i class="ion ion-stop"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Held In OMR</span>
					<span class="info-box-number">SL - <?= $currency_symbol; ?><?= $HeldInOMRSL?></span>
					<span class="info-box-number">CR - <?= $currency_symbol; ?><?= $HeldInOMRCR?></span>
				</div>
			</div>
			<!------------------------------------------------------------------------------------------------------------------>
			<!-- WAITING POSTING -->
			<!------------------------------------------------------------------------------------------------------------------>
			<div class="info-box bg-yellow">
				<span class="info-box-icon"><i class="ion ion-ios-pause"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Waiting Posting</span>
					<span class="info-box-number">SL - <?= $currency_symbol; ?><?= $WaitingPostingSL?></span>
					<span class="info-box-number">CR - <?= $currency_symbol; ?><?= $WaitingPostingCR?></span>
				</div>
			</div>
			<!------------------------------------------------------------------------------------------------------------------>
			<!-- POSTED -->
			<!------------------------------------------------------------------------------------------------------------------>
			<div class="info-box bg-green">
				<span class="info-box-icon"><i class="ion ion-ios-play"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Posted</span>
					<span class="info-box-number">SL - <?= $currency_symbol; ?><?= $PostedSL?></span>
					<span class="info-box-number">CR - <?= $currency_symbol; ?><?= $PostedCR?></span>
				</div>
			</div>
		</div> <!-- col-md-3 -->
		<?php } ?>
	</div> <!-- row -->
	</section>
	<!-- Main row -->
	<div class="row">
		<!-- Left col -->
		<div class="col-md-8">
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
	</section>
	<!-- /.content -->
<script src="<?= $this->config->item('base_folder'); ?>public/plugins/datatables/datatables.min.js"></script>
	<script>
		function manage_cookie(cookie_name,cookie_value)
		{
			// alert(cookie_name);
			// alert(cookie_value);
				// if (cookie_name=='threeyearsaleschart' && cookie_value=='N')
				// {
				//	var SalesChartCanvas = $("#SalesChart").get(0).getContext("2d");
				//	// This will get the first returned node in the jQuery collection.
				//	var SalesChart = new Chart(SalesChartCanvas);
				//	SalesChart.data.labels.push(label);
				//	SalesChart.data.datasets.forEach((dataset) => {
				//	dataset.data.push(data);
				//	});
				//	Chart.update();
				// }
			$.ajax({
			type: "POST",
			dataType: "html",
			url: "<?= base_url(); ?>/site/manage_cookie",
			data: {cookie_name:cookie_name,cookie_value:cookie_value},
			success: function(data) {
			//alert(data);
			}
			});
		}
		</script>
		<script type="text/javascript">
$(document).ready(function() {
	$('#example').DataTable({
		"PAC": [[ 3, "desc" ]],
		"paging":	false,
	"searching": false,
		"info":	false
	} );
} );
</script>