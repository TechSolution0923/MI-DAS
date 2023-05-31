<?php
	/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
	$canSeeMargins = canSeeMargins();
	$canEditNotes = canEditNotes();
	$canEditTerms = canEditTerms();
	$curryear = date('Y');
?>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1> Products</h1>
	<ol class="breadcrumb">
	<li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
	<li><a href="<?= base_url(); ?>products"><i class="fa fa-dashboard"></i> Products</a></li>
	</ol>
</section>

<section class="content">
	<div class="nav-tabs-custom left-tab">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#prodPAC1" role="tab" data-toggle="tab" aria-expanded="false">PAC1</a></li>
			<li role="presentation" class=""><a href="#prodPAC2" role="tab" data-toggle="tab" aria-expanded="false">PAC2</a></li>
			<li role="presentation" class=""><a href="#prodPAC3" role="tab" data-toggle="tab" aria-expanded="false">PAC3</a></li>
			<li role="presentation" class=""><a href="#prodPAC4" role="tab" data-toggle="tab" aria-expanded="false">PAC4</a></li>
			<li role="presentation" class=""><a href="#prodSAprod" role="tab" data-toggle="tab" aria-expanded="false">Products</a></li>
		</ul>
		<input type="hidden" name="mm" id="chk_input" value="PAC1">
		<div class="tab-content">
<?php
		for ($pac_no = 1; $pac_no < 5; $pac_no++)
		{
?>
			<div class="<?=$pac_no == 1 ? "active" : "" ?> tab-pane" id="prodPAC<?= $pac_no ?>">
				<div class="row">
					<div class="col-xs-10 col-sm-8 col-md-8 col-lg-10">
						<table id="product-pac<?= $pac_no ?>-table" class="table example-table table-bordered table-striped">
							<thead>
								<tr>
									<th class="">Code</th>
									<th class="">Description</th>
									<th class="sum">Sales YTD</th>
									<th class="sum">Qty YTD</th>
									<th class="sum">Sales Diff %</th>
									<th class="sum">Qty Diff %</th>
									<th class="sum">Sales <?= $curryear - 1; ?></th>
									<th class="sum">Qty <?= $curryear - 1; ?></th>
									<th class="sum">Sales <?= $curryear - 2; ?></th>
									<th class="sum">Qty <?= $curryear - 2; ?></th>
									<th class="sum">Sales MTD</th>
									<th class="sum">Qty MTD</th>
<?php
									if ($canSeeMargins)
									{
?>
										<th class="sum">GM% MTD</th>
										<th class="sum">GM% YTD</th>
<?php
									}
?>
									<th style="display: none;">costsmtd</th>
									<th style="display: none;">costsytd</th>
									<th style="display: none;">salesmtd</th>
								</tr>
							</thead>
							<tbody></tbody>
							<tfoot>
								<tr>
									<th id="1">Code</th>
									<th id="2">Description</th>
									<th id="3">Sales YTD</th>
									<th id="4">Qty YTD</th>
									<th id="5">Sales Diff %</th>
									<th id="6">Qty Diff %</th>
									<th id="7">Sales <?= $curryear - 1; ?></th>
									<th id="8">Qty <?= $curryear - 1; ?></th>
									<th id="9">Sales <?= $curryear - 2; ?></th>
									<th id="10">Qty <?= $curryear - 2; ?></th>
									<th id="11">Sales MTD</th>
									<th id="12">Qty MTD</th>
<?php
									if ($canSeeMargins)
									{
?>
										<th id="13">GM% MTD</th>
										<th id="14">GM% YTD</th>
<?php
									}
?>
								</tr>
								<tr style="background-color: #e1e1e1; font-style: italic; font-weight: bold; color: black;">
									<td class="1"></td>
									<td class="2"></td>
									<td class="3"></td>
									<td class="4"></td>
									<td class="5"></td>
									<td class="6"></td>
									<td class="7"></td>
									<td class="8"></td>
									<td class="9"></td>
									<td class="10"></td>
									<td class="11"></td>
									<td class="12"></td>
<?php
									if ($canSeeMargins)
									{
?>
										<td class="13"></td>
										<td class="14"></td>
<?php
									}
?>
								</tr>
							</tfoot>
						</table>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->
<?php
		}
?>
			<div class="tab-pane" id="prodSAprod">
				<div class="row">
					<div class="col-xs-10">
						<table id="product-table" class="table prodSAprod-example-table table-bordered table-striped" style="width: 100%;">
							<thead>
								<tr>
									<th>Code</th>
									<th>PAC4</th>
									<th>Description</th>
									<th>Free Qty</th>
									<th>Order Qty</th>
									<th>Sales YTD</th>
									<th>Qty YTD</th>
									<th>Sales Diff %</th>
									<th>Qty Diff %</th>
									<th>Sales <?= $curryear-1; ?></th>
									<th>Qty <?= $curryear-1; ?></th>
									<th>Sales <?= $curryear-2; ?></th>
									<th>Qty <?= $curryear-2; ?></th>
									<th>Sales MTD</th>
									<th>Qty MTD</th>
<?php
									if ($canSeeMargins)
									{
?>
										<th>GM% MTD</th>
										<th>GM% YTD</th>
<?php
									}
?>
								</tr>
							</thead>

							<tbody>
							</tbody>

							<tfoot>
								<tr>
									<th>Code</th>
									<th>PAC4</th>
									<th>Description</th>
									<th>Free Qty</th>
									<th>Order Qty</th>
									<th>Sales YTD</th>
									<th>Qty YTD</th>
									<th>Sales Diff %</th>
									<th>Qty Diff %</th>
									<th>Sales <?= $curryear-1; ?></th>
									<th>Qty <?= $curryear-1; ?></th>
									<th>Sales <?= $curryear-2; ?></th>
									<th>Qty <?= $curryear-2; ?></th>
									<th>Sales MTD</th>
									<th>Qty MTD</th>
<?php
									if ($canSeeMargins)
									{
?>
										<th>GM% MTD</th>
										<th>GM% YTD</th>
<?php
									}
?>
								</tr>
								<tr style="background-color: #e1e1e1; font-style: italic; font-weight: bold; color: black;" class="total-row">
									<td class="">Total</td>
									<td class="nototal"></td>
									<td class="nototal"></td>
									<td class="nototal" data-value="freeqty"></td>
									<td class="nototal" data-value="purchaseqty"></td>
									<td class="hastotal" data-value="salesytd"></td>
									<td class="hastotal" data-value="qtyytd"></td>
									<td class="hastotal" data-value="sales_diff"></td>
									<td class="hastotal" data-value="qty_diff"></td>
									<td class="hastotal" data-value="YoY1Sales"></td>
									<td class="hastotal" data-value="YoY1Qty"></td>
									<td class="hastotal" data-value="YoY2Sales"></td>
									<td class="hastotal" data-value="YoY1Qty"></td>
									<td class="hastotal" data-value="salesmtd"></td>
									<td class="hastotal" data-value="qtymtd"></td>
<?php
									if ($canSeeMargins)
									{
?>
										<td class="hastotal" data-value="marginmtdpc"></td>
										<td class="hastotal" data-value="marginytdpc"></td>
<?php
									}
?>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div><!-- /.tab-pane -->
		</div><!-- /.tab-content -->
	</div><!-- /.nav-tabs-custom -->
</section>

<script type="text/javascript">
	$(function()
	{
<?php
		for ($pac_no = 1; $pac_no < 5; $pac_no++)
		{
?>
			$('#product-pac<?= $pac_no ?>-table').DataTable(
			{
				processing      : true,
				serverSide      : true,
				ajax            :
				{
					url      : base_url+"products/fetchProductPAC/<?= $pac_no ?>",
					type     : "post",
					dataSrc: function(json) {
						$("#prodPAC<?= $pac_no ?> .1").html(json.columnTotals["code"]);
						$("#prodPAC<?= $pac_no ?> .2").html(json.columnTotals["description"]);
						$("#prodPAC<?= $pac_no ?> .3").html(json.columnTotals["salesytd"]);
						$("#prodPAC<?= $pac_no ?> .4").html(json.columnTotals["qtyytd"]);
						$("#prodPAC<?= $pac_no ?> .5").html(json.columnTotals["sales_diff"]);
						$("#prodPAC<?= $pac_no ?> .6").html(json.columnTotals["qty_diff"]);
						$("#prodPAC<?= $pac_no ?> .7").html(json.columnTotals["YoY1Sales"]);
						$("#prodPAC<?= $pac_no ?> .8").html(json.columnTotals["YoY1Qty"]);
						$("#prodPAC<?= $pac_no ?> .9").html(json.columnTotals["YoY2Sales"]);
						$("#prodPAC<?= $pac_no ?> .10").html(json.columnTotals["YoY2Qty"]);
						$("#prodPAC<?= $pac_no ?> .11").html(json.columnTotals["salesmtd"]);
						$("#prodPAC<?= $pac_no ?> .12").html(json.columnTotals["qtymtd"]);
<?php
						if ($canSeeMargins)
						{
?>
							$("#prodPAC<?= $pac_no ?> .13").html(json.columnTotals["marginmtdpc"]);
							$("#prodPAC<?= $pac_no ?> .14").html(json.columnTotals["marginytdpc"]);
<?php
						}
?>
						return json.data;
					}
				},
				dom             : 'Bfrtip',
				buttons         :
				[
					{
						extend : 'csv',
						text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
						action : function(e, dt, node, config)
						{
							var searchTerm = $("#product-pac<?=$pac_no?>-table_filter input[type='search']").val();
							document.location.href = base_url + "products/fetchProductPACCsvExport/<?=$pac_no?>/" + searchTerm;
						}
					},
				],
				createdRow: function (row, data, index) {
					if (parseFloat(data['salesytd']) > parseFloat(data['YoY1Sales']))
					{
						$(row).attr("class", `greenrow`);
					}
					else if (parseFloat(data['salesytd']) < parseFloat(data['YoY1Sales']))
					{
						$(row).attr("class", `redrow`);
					}
				},
				columns         :
				[
					{
						data : "code"
					},
					{
						data : "description"
					},
					{
						data: "salesytd",
						render: function (data, type, row) {
							return parseFloat(data).toFixed(2);
						}
					},
					{
						data: "qtyytd",
						render: function (data, type, row) {
							return parseFloat(data).toFixed(2);
						}
					},
					{
						data: "salesdiff",
						render: function (data, type, row) {
							return parseFloat(data).toFixed(2);
						}
					},
					{
						data: "qtydiff",
						render: function (data, type, row) {
							return parseFloat(data).toFixed(2);
						}
					},
					{
						data: "YoY1Sales",
						render: function (data, type, row) {
							return parseFloat(data).toFixed(2);
						}
					},
					{
						data: "YoY1Qty",
						render: function (data, type, row) {
							return parseFloat(data).toFixed(2);
						}
					},
					{
						data: "YoY2Sales",
						render: function (data, type, row) {
							return parseFloat(data).toFixed(2);
						}
					},
					{
						data: "YoY2Qty",
						render: function (data, type, row) {
							return parseFloat(data).toFixed(2);
						}
					},
					{
						data: "salesmtd",
						render: function (data, type, row) {
							return parseFloat(data).toFixed(2);
						}
					},
					{
						data: "qtymtd",
						render: function (data, type, row) {
							return parseFloat(data).toFixed(2);
						}
					},
<?php
					if ($canSeeMargins)
					{
?>
						{
							data: "marginmtdpc",
							render: function (data, type, row) {
								return parseFloat(data).toFixed(2);
							}
						},
						{
							data: "marginytdpc",
							render: function (data, type, row) {
								return parseFloat(data).toFixed(2);
							}
						}
<?php
					}
?>
				],
				displayLength   : 10,
			});
<?php
		}
?>

		getAjaxSAListDataTable('prodSAprod');
	});
</script>
