
<?php
$threeData = canSeeThreeInfo();
$canSeeProjectedSales = 1;
$canSeeProjectedSalesYear = 1;
$canSeeOrderFulfillment = 1;

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
	<div class="row" id="dashboard-first">

	</div>


	<!-- /.row -->
		<div class="row">
			<div class="<?php if (!!$canSeeOMR) {?>col-md-9<?php } else {?> col-md-12<?php }?>"> <!-- Main left hand side of dashboard -->
				<div class="row">	<!-- Today's and outstanding orders row -->
					<!------------------------------------------------------------------------------------------------------------------>
					<!-- TODAYS ORDERS - BY TYPE & BY STATUS -->
					<!------------------------------------------------------------------------------------------------------------------>
					<div class="col-md-8">
						<div class="nav-tabs-custom" id="dashboard-second-left">

						</div> <!-- class="nav-tabs-custom" -->
					</div> <!-- col-md-8 -->
					<!------------------------------------------------------------------------------------------------------------------>
					<!-- OUTSTANDING ORDERS BY STATUS DONUT CHART -->
					<!------------------------------------------------------------------------------------------------------------------>
					<div class="col-md-4">
						<div class="nav-tabs-custom" id="dashboard-second-right">

						</div> <!-- nav-tabs-custom -->
					</div> <!-- col-md-4 -->
				</div> <!-- Today's and outstanding orders row -->


				<div class="nav-tabs-custom" id="dashboard-third">

					<!-- /.box-body -->
				</div>



				<!-- /.box -->
				<?php // if ($_SERVER['REMOTE_ADDR']=='115.112.129.194'){?>
				<div class="row" >
					<div class="col-md-12">
						<div class="box box-primary" id="dashboard-fourth">
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
						<div class="box box-primary" id="dashboard-fifth">

						</div>
					</div>

					<?php
					/* ------------------------------------------------------------------------------------------------------------------
					---- SALES PIPELINE ----
					------------------------------------------------------------------------------------------------------------------- */
					?>

					<!-- sixth dashboard -->
					<div class="col-md-4">
						<div class="box box-primary" id="dashboard-sixth">
						</div>
					</div>
				</div>

				<div class="row" id="dashboard-seventh">
				</div>




	<?php if ($canSeeOrderFulfillment): ?>
		<div class="row">
			<div class="col-md-12"
			<!-- ORDER FULFULMENT - ENTERED TODAY AND AT WDL OR COM -->
			<div class="box box-primary" id="dashboard-eighth">

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