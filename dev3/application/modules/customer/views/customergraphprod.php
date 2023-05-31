<?php $currency_symbol = $this->config->item("currency_symbol"); ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>MI-DAS | Management Information Dashboard</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.6 -->
	<link rel="stylesheet" href="<?= $this->config->item('base_folder'); ?>public/bootstrap/css/bootstrap.min.css">
	<!-- Font Awesome -->

	 <script src="<?= $this->config->item('base_folder'); ?>public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
	 <script>
var base_url = '<?= $this->config->item('base_folder'); ?>';
</script> 
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
	<!-- Theme style -->
	<!--link rel="stylesheet" href="./AdminLTE-2.3.3/dist/css/AdminLTE.min.css"-->
	<!-- AdminLTE Skins. Choose a skin from the css/skins
			 folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="<?= $this->config->item('base_folder'); ?>public/css/skins/_all-skins.min.css">
	<link rel="stylesheet" href="<?php echo $this->config->item('base_folder'); ?>public/css/twoyearsaleschart.css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>


	<![endif]-->
<style>
.tab-content{padding-top:40px;} 
.text-navy{color:#001f3f !important}
.text-light-blue{color:#3c8dbc !important}
.text-gray{color:#d2d6de !important}
</style>
	<script>
			$(document).ready(function(){
				//Examples of how to assign the Colorbox event to elements
				//$(".iframe").colorbox({iframe:true, width:"90%", height:"90%"});
				
				//Example of preserving a JavaScript event for inline calls.
			});
		</script>
</head>
<body class="hold-transition skin-blue sidebar-mini" >
<div class="wrapper" style="background-color:white!important">

	 <!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->


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
					<li class="active"><a href="#threeyearsaleschart" data-toggle="tab" onclick="manage_cookie('threeyearsalesanalysisproductschart','N')"><i class="fa fa-line-chart"></i></a></li>
					<li onclick="manage_cookie('threeyearsalesanalysisproductschart','Y')" id="threeyearsalesanalysisproductstable_nav"><a href="#threeyearsalestable" data-toggle="tab" class="threeyearsalestable-link"><span class="threeyearsalestable-sales"><?= $currency_symbol; ?></span> / <span class="threeyearsalestable-quantities">Qty&nbsp;&nbsp;</span><i class="fa fa-table"></i></a></li>
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
									<canvas id="quantity-this-year-vs-last-year" style="height: 250px;"></canvas>
									<canvas id="quantity-this-year-cml-vs-target-cml" style="height: 250px;"></canvas>
								</div>
							</div>
							<div class="col-md-1">
								<ul class="chart-legend clearfix">
								<li id="this-year-legend"><i class="fa fa-circle-o text-navy"></i><?= $year0; ?></li>
									<li id="this-year-cml-legend" class="hide"><i class="fa fa-circle-o text-navy"></i><?= $year0; ?> Cml.</li>
									<li id="last-year-legend" class="hide"><i class="fa fa-circle-o text-light-blue"></i><?= $year1; ?></li>
									<li id="last-year-cml-legend" class="hide"><i class="fa fa-circle-o text-light-blue"></i><?= $year1; ?> Cml.</li>
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
											<optgroup label="Sales vs Last Year">
												<option value="this-year-vs-last-year-option"><?= $year0; ?> vs <?= $year1; ?></option>
												<option value="this-year-cml-vs-last-year-cml-option"><?= $year0; ?> cml. vs <?= $year1; ?> cml.</option>
											</optgroup>
											<optgroup label="Quantity vs Last Year">
												<option value="quantity-this-year-vs-last-year-option"><?= $year0; ?> vs <?= $year1; ?></option>
												<option value="quantity-this-year-cml-vs-target-cml-option"><?= $year0; ?> cml. vs <?= $year1; ?> cml.</a></option>
											</optgroup>
										</select>
									</div>
								</form>
							</div>
						</div>
					</div>	<!-- class="tab-pane" -->
					<div class="tab-pane" id="threeyearsalestable" style="position: relative;">
						<table class="table table-striped sales-toggle hide">
							<tr class="border-header">
								<th>Year</th>
								<th>Jan (<?= $currency_symbol; ?>)</th>
								<th>Feb (<?= $currency_symbol; ?>)</th>
								<th>Mar (<?= $currency_symbol; ?>)</th>
								<th>Apr (<?= $currency_symbol; ?>)</th>
								<th>May (<?= $currency_symbol; ?>)</th>
								<th>Jun (<?= $currency_symbol; ?>)</th>
								<th>Jul (<?= $currency_symbol; ?>)</th>
								<th>Aug (<?= $currency_symbol; ?>)</th>
								<th>Sep (<?= $currency_symbol; ?>)</th>
								<th>Oct (<?= $currency_symbol; ?>)</th>
								<th>Nov (<?= $currency_symbol; ?>)</th>
								<th>Dec (<?= $currency_symbol; ?>)</th>
								<th>Total (<?= $currency_symbol; ?>)</th>
							</tr>
							<?php require_once(BASEPATH.'../application/views/common/twoyearsvstarget.php'); ?>
						</table>
						<table class="table table-striped quantities-toggle">
							<tr class="border-header">
								<th>Year</th>
								<th>Jan (Qty)</th>
								<th>Feb (Qty)</th>
								<th>Mar (Qty)</th>
								<th>Apr (Qty)</th>
								<th>May (Qty)</th>
								<th>Jun (Qty)</th>
								<th>Jul (Qty)</th>
								<th>Aug (Qty)</th>
								<th>Sep (Qty)</th>
								<th>Oct (Qty)</th>
								<th>Nov (Qty)</th>
								<th>Dec (Qty)</th>
								<th>Total (Qty)</th>
							</tr>
							<?php require_once(BASEPATH.'../application/views/common/quantitytwoyearsvstarget.php'); ?>
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

<!-- ./wrapper -->
<script src="<?= $this->config->item('base_folder'); ?>public/plugins/jQuery/jQuery-2.2.0.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?= $this->config->item('base_folder'); ?>public/bootstrap/js/bootstrap.min.js"></script>
<!-- ChartJS 1.0.1 -->
<script src="<?= $this->config->item('base_folder'); ?>public/plugins/chartjs/Chart.min.js"></script>
<!-- FastClick -->
<script src="<?= $this->config->item('base_folder'); ?>public/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?= $this->config->item('base_folder'); ?>public/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?= $this->config->item('base_folder'); ?>public/js/demo.js"></script>

<?php require_once(BASEPATH.'../application/views/common/line_a_vs_line_b_charts.php'); ?>

<script type="text/javascript">
	$(function()
	{
		$(".threeyearsalestable-link").on("click",function() {
			if ($(".quantities-toggle").hasClass("hide")) {
				$(".quantities-toggle").removeClass("hide");
				$(".sales-toggle").addClass("hide");
				$(".threeyearsalestable-quantities").css("color", "red");
				$(".threeyearsalestable-sales").css("color", "black");
			}
			else if ($(".sales-toggle").hasClass("hide")) {
				$(".sales-toggle").removeClass("hide");
				$(".quantities-toggle").addClass("hide");
				$(".threeyearsalestable-sales").css("color", "red");
				$(".threeyearsalestable-quantities").css("color", "black");
			}
		})
	});
</script>

<script>
	function manage_cookie(cookie_name,cookie_value)
	{
		$.ajax({
		type: "POST",
		dataType: "html",
		url: "<?= base_url(); ?>/customer/manage_cookie",
		data: {cookie_name:cookie_name,cookie_value:cookie_value},
		success: function(data) {
		}
		});
	}
</script>

<?php if($threeyearsalesanalysisproductschart=='Y'){ ?>
	<script>
		$(function () {
			$("#threeyearsalesanalysisproductstable_nav a").click();
		});
	</script>
<?php }  ?>

</body>
</html>
