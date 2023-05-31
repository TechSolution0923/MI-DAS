<?php
	/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
	$canSeeMargins = canSeeMargins();
	$canEditNotes = canEditNotes();
	$canEditTerms = canEditTerms();

	$curryear = date('Y');
	$currency_symbol = $this->config->item("currency_symbol");

	function getTotal($result, $year)
	{
		return floatval($result[$year."01"])+floatval($result[$year."02"])+floatval($result[$year."03"])+floatval($result[$year."04"])+floatval($result[$year."05"])+floatval($result[$year."06"])+floatval($result[$year."07"])+floatval($result[$year."08"])+floatval($result[$year."09"])+floatval($result[$year."10"])+floatval($result[$year."11"])+floatval($result[$year."12"]);
	}
?>
<!-- Content Header (Page header) -->
<link rel="stylesheet" href="<?= $this->config->item('base_folder'); ?>application/modules/users/css/mycss.css">
<section class="content-header">
	<h1> <?= $prodcode." - ".$description?>  </h1>
	<ol class="breadcrumb">
		<li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="<?= base_url(); ?>products"><i class="fa fa-dashboard"></i> Products</a></li>
		<li class="active"><?= $prodcode." - ".$description; ?></li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#proCharts" role="tab" data-toggle="tab" aria-expanded="false">Chart</a></li>
			<li role="presentation"  class=""><a href="#productStock" role="tab" data-toggle="tab" aria-expanded="false">Stock</a></li>
			<li role="presentation" class=""><a href="#proCustomers" role="tab" data-toggle="tab" aria-expanded="false">Customers</a></li>
			<li role="presentation" class="" id="showiftarget"><a href="#target" role="tab" data-toggle="tab" aria-expanded="false">Targets</a></li>
		</ul>
		<input type="hidden" name="mm" id="chk_input" value="" />
		<div class="tab-content">
			<div class="active tab-pane" id="proCharts">
				<div class="row">
					<div class="col-md-12"> <!-- Main left hand side of dashboard -->
						<!------------------------------------------------------------------------------------------------------------------>
						<!-- 3 YEAR SALES CHART -->
						<!------------------------------------------------------------------------------------------------------------------>
						<div class="nav-tabs-custom">
							<ul class="nav nav-tabs pull-right">
								<li class="active"><a href="#threeyearsaleschart" data-toggle="tab" onclick="manage_cookie('threeyearproductschart','N')"><i class="fa fa-line-chart"></i></a></li>
								<li onclick="manage_cookie('threeyearproductschart','Y')" id="threeyearproductstable_nav"><a href="#threeyearsalestable" data-toggle="tab" class="threeyearsalestable-link"><span class="threeyearsalestable-sales"><?= $currency_symbol; ?></span> / <span class="threeyearsalestable-quantities">Qty&nbsp;&nbsp;</span><i class="fa fa-table"></i></a></li>
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
								</div>  <!-- class="tab-pane" -->
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
						</div>
					</div>  <!-- Main left hand side of dashboard col-md-9 -->
				</div>  <!-- row -->
			</div><!-- /.tab-pane -->

			<div class="tab-pane" id="productStock">
				<div class="row">
					<div class="col-xs-12">
						<table id="product-stock-content" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Branch</th>
									<th>Name</th>
									<th>Total</th>
									<th>B/order</th>
									<th>Allocated</th>
									<th>Reserved</th>
									<th>F/order</th>
									<th>Free</th>
									<th>Unit</th>
									<th>On Order</th>
									<th>BTB</th>
									<th>Date Expected</th>
									<th>Purchase Qty</th>
								</tr>
							</thead>
							<tbody>
<?php 
								foreach ($productStockList as $stockData)
								{
?>
									<tr>
										<td><?= $stockData['branch']; ?></td>
										<td><?= $stockData['name']; ?></td>
										<td><?= number_format($stockData['totalqty']); ?></td>
										<td><?= number_format($stockData['backorderqty']); ?></td>
										<td><?= number_format($stockData['allocatedqty']); ?></td>
										<td><?= number_format($stockData['reservedqty']); ?></td>
										<td><?= number_format($stockData['forwardsoqty']); ?></td>
										<td><?= number_format($stockData['freeqty']); ?></td>
										<td><?= $stockData['unitofstock']; ?></td>
										<td><?= number_format($stockData['purchaseqty']); ?></td>
										<td><?= number_format($stockData['backtobackqty']); ?></td>
										<td><?= $stockData['dateexpected']; ?></td>
										<td><?= number_format($stockData['purchaseqty']); ?></td>
									</tr>
<?php
								}
?>
							</tbody>
							<tfoot>
								<tr>
									<th>Branch</th>
									<th>Name</th>
									<th>Total</th>
									<th>B/order</th>
									<th>Allocated</th>
									<th>Reserved</th>
									<th>F/order</th>
									<th>Free</th>
									<th>Unit</th>
									<th>On Order</th>
									<th>BTB</th>
									<th>Date Expected</th>
									<th>Purchase Qty</th>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="proCustomers">
				<div class="row">
					<div class="col-xs-12">
						<table id="new-product-customers" class="table table-bordered table-striped">
							<thead>
							<!-- Account, Name, Sales YTD, Sales Last Year (2018), Diff % (from step 2), Sales 2017, Sales MTD, GM% MTD (if visible), GM% YTD (if visible), Post Code and User Def. 1?-->
								<tr>
									<th>Account</th>
									<th>Name</th>
									<th class="sum">Sales YTD</th>
									<th class="sum">Sales <?= $curryear-1; ?></th>
									<th class="sum">Diff %</th>
									<th class="sum">Sales <?= $curryear-2; ?></th>
									<th class="sum">Sales MTD</th>
<?php
									if ($canSeeMargins)
									{
?>
										<th class="sum">GM% MTD</th>
										<th class="sum">GM% YTD</th>
<?php
									}
?>
									<th class="sum">Qty MTD</th>
									<th class="sum">Qty YTD</th>
									<th style="display: none;">Costs MTD</th>
									<th style="display: none;">Costs YTD</th>
								</tr>
							</thead>
							<tbody>
<?php
								$totals = array();

								foreach ($custList as $row)
								{
									$totals['salesytd']+= floatval($row['salesytd']);
									$totals['YoY1Sales']+= floatval($row['YoY1Sales']);
									$totals['YoY2Sales']+= floatval($row['YoY2Sales']);
									$totals['salesmtd']+= floatval($row['salesmtd']);
									$totals['qtymtd']+= floatval($row['qtymtd']);
									$totals['qtyytd']+= floatval($row['qtyytd']);
									$totals['costsmtd']+= floatval($row['costsmtd']);
									$totals['costsytd']+= floatval($row['costsytd']);
									/**
									 * YoY1Sales  salesytd
									 * 0            0
									 * 0            1
									 * 1            0
									 * 1            1
									 */
									$YoY1Sales = intval($row['YoY1Sales']);
									$salesytd = intval($row['salesytd']);
									$diff_percentage = 0;
									$class = "";

									if ($YoY1Sales == 0) 
									{
										if ($salesytd == 0)
										{
											$diff_percentage = "0.00";
											$class = "";
										}
										elseif ($salesytd < 0)
										{
											$diff_percentage = "-100.00";
											$class = "redrow";
										}
										else
										{
											$diff_percentage = "100.00";
											$class = "greenrow";
										}
									}
									else
									{
										$diff_percentage = number_format((($salesytd-$YoY1Sales)*100)/abs($YoY1Sales), 2);
										$class = "";

										if ($diff_percentage < 0)
										{
											$class = "redrow";
										}
										else
										{
											$class = "greenrow";
										}
									}
?>
									<tr class="<?= $class; ?>">
										<td><?= $row['account']; ?></td>
										<td><a href="<?= $lnk.base64_encode($row['account']); ?>"><?= $row['name']; ?></a></td>
										<td><?= $row['salesytd']; ?></td>
										<td><?= $row['YoY1Sales']; ?></td>
										<td><?= $diff_percentage; ?></td>
										<td><?= $row['YoY2Sales']; ?></td>
										<td><?= $row['salesmtd']; ?></td>
<?php
										if ($canSeeMargins)
										{
?>
											<td><?= number_format($row['marginmtdpc'], 2); ?></td>
											<td><?= number_format($row['marginytdpc'], 2); ?></td>
<?php
										}
?>
										<td><?= $row['qtymtd']; ?></td>
										<td><?= $row['qtyytd']; ?></td>
										<td style='display: none;'><?= $row['costsmtd']; ?></td>
										<td style='display: none;'><?= $row['costsytd']; ?></td>
									</tr>
<?php
								}

								if (isset($totals['salesytd']) && isset($totals['YoY1Sales']))
								{
									$percentage = ($totals['salesytd'] / $totals['YoY1Sales']) * 100;
									// if ($percentage > 100)
									// {
									//    $percentage -= 100;
									// }
									// elseif ($percentage < 100)
									// {
									//    $percentage = 100 - $percentage;
									//    $percentage = -1 * abs($percentage);
									// }
									$totals['diff'] = number_format($percentage, 2);
								}

								if (isset($totals['costsmtd']) && isset($totals['salesmtd']))
								{
									$percentage = ($totals['salesmtd'] - $totals['costsmtd']) / $totals['salesmtd'] * 100;
									$totals['gm_mtd'] = number_format($percentage, 2);
								}

								if (isset($totals['costsytd']) && isset($totals['salesytd']))
								{
									$percentage = ($totals['salesytd'] - $totals['costsytd']) / $totals['salesytd'] * 100;
									$totals['gm_ytd'] = number_format($percentage, 2);
								}
?>
							</tbody>

							<tfoot>
								<tr>
									<th id="1">Account</th>
									<th id="2">Name</th>
									<th id="3">Sales YTD</th>
									<th id="4">Sales <?= $curryear-1; ?></th>
									<th id="5">Diff %</th>
									<th id="6">Sales <?= $curryear-2; ?></th>
									<th id="7">Sales MTD</th>
<?php
									if ($canSeeMargins)
									{
?>
										<th id="8">GM% MTD</th>
										<th id="9">GM% YTD</th>
<?php
									}
?>
									<th id="10">Qty MTD</th>
									<th id="11">Qty YTD</th>
									<th style="display: none;">Costs MTD</th>
									<th style="display: none;">Costs YTD</th>
								</tr>
								<tr style="background-color: #e1e1e1; font-style: italic; font-weight: bold; color: black;" class="total-row">
									<td class="1">Total</td>
									<td class="2"></td>
									<td class="3"><?= number_format($totals['salesytd'], 2, '.', ''); ?></td>
									<td class="4"><?= number_format($totals['YoY1Sales'], 2, '.', ''); ?></td>
									<td class="5"><?= $totals['diff']; ?></td>
									<td class="6"><?= number_format($totals['YoY2Sales'], 2, '.', ''); ?></td>
									<td class="7"><?= number_format($totals['salesmtd'], 2, '.', ''); ?></td>
									<td class="8"><?= $totals['gm_mtd']; ?></td>
									<td class="9"><?= $totals['gm_ytd']; ?></td>
									<td class="10"><?= number_format($totals['qtymtd'], 2, '.', ''); ?></td>
									<td class="11"><?= number_format($totals['qtyytd'], 2, '.', ''); ?></td>
									<td style="display: none;"></td>
									<td style="display: none;"></td>
								</tr>
							</tfoot>
						</table>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->

			<div class="tab-pane" id="target">
				<div class="row">
					<div class="col-xs-12">
						<div class="content_box">
							<div class="row">
								<div class="col-xs-12">
									<div class="box">
										<div class="box-body">
											<span id="alertmsg"><?= $this->session->flashdata('target_operation'); ?></span>
											<div class="box-footer no-border">
<?php
												if ($mainUserEdirAccess == '1')
												{
?>
													<button type="button" class="btn btn-success pull-left" onclick="openAddTargetForm();"><i class="fa fa-fw fa-calendar-plus-o"></i> Add target</button>
													<button type="button" class="btn btn-success pull-right" onclick="openAdduploadTargetForm();"><i class="fa fa-fw fa-calendar-plus-o"></i> Upload target(s)</button>
													<hr />
<?php
												}
												/*<a href="<?= base_url(); ?>/images/import_product_target.csv" download>Sample File (Use Only CSV with headings)</a>*/
?>
											</div>
											<table class="table table-bordered table-striped target-list-table target-listing" id="example">
												<thead>
													<tr>
														<th>User Id</th>
														<th>User Name</th>
														<th>Year/Month</th>
														<th>Target</th>
<?php
														if ($mainUserEdirAccess == '1')
														{
?>
															<th>Delete</th>
<?php
															}
?>
													</tr>
												</thead>
												<tbody>
<?php
													$t = 1;

													foreach ($salestarget as $target)
													{
?>
														<tr>
															<td><?= $target->userid; ?></td>
															<td><?= $target->username; ?></td>
															<td>
<?php
																if ($mainUserEdirAccess == '1')
																{
?>
																	<div class="ulink" id="ulink_<?= $target->id; ?>" for="<?= $target->id; ?>"><?= $target->yearmonth; ?></div>
																	<div class="hidden" id="hidden_<?= $target->id; ?>">
																		<input type="number" min="<?= date("Y"); ?>" name="year" id="year_<?= $target->id; ?>" value="<?= substr($target->yearmonth, 0, 4); ?>" for="<?= $target->id; ?>" class="width-50" />/
																		<input type="number" min="1" max="12" name="month" id="month_<?= $target->id; ?>" value="<?= substr($target->yearmonth, 4, 2); ?>" for="<?= $target->id; ?>" class="width-50" />
																		<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="updateyearmonth('<?= $target->id; ?>', '<?= $page; ?>', '<?= $prodcode; ?>');"><i class="glyphicon glyphicon-ok"></i></button>
																		<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeediting('<?= $target->id; ?>', 'yearmonth', false);"><i class="glyphicon glyphicon-remove"></i></button>
																	</div>
<?php
																}
																else
																{
?>
																	<?= $target->yearmonth; ?>
<?php
																}
?>
															</td>
															<td>
<?php
																if ($mainUserEdirAccess == '1')
																{
?>
																	<div class="flink" id="flink_<?= $target->id; ?>" for="<?= $target->id; ?>"><?= $target->salestarget; ?></div>
																	<div class="hidden" id="fhidden_<?= $target->id; ?>">
																		<input type="number" min="0" name="salestarget" id="salestarget_<?= $target->id; ?>" value="<?= $target->salestarget; ?>" for="<?= $target->id; ?>" class="height-29" />
																		<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="updatesalestarget('<?= $target->id; ?>', '<?= $page; ?>');"><i class="glyphicon glyphicon-ok"></i></button>
																		<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeediting('<?= $target->id; ?>', 'target', false);"><i class="glyphicon glyphicon-remove"></i></button>
																	</div>
<?php
																}
																else
																{
?>
																	<?= $target->salestarget; ?>
<?php
																}
?>
															</td>
<?php
															if ($mainUserEdirAccess=='1')
															{
?>
																<td><span id="dlink_<?= $target->id; ?>"><a onclick="deletetarget('<?= $target->id; ?>', '<?= $userDetail['userid']; ?>', '<?= $page; ?>');" href="javascript:void(0);" class="transform-link" ><i class="fa fa-fw fa-trash-o"></i></a></span></td>
<?php
															}
?>
														</tr>
<?php
													}
?>
												</tbody>
												<tfoot>
													<tr>
														<th>PAC<?= $page; ?></th>
														<th>User Name</th>
														<th>Year/Month</th>
														<th>Target</th>
<?php
														if ($mainUserEdirAccess == '1')
														{
?>
															<th>Delete</th>
<?php
														}
?>
													</tr>
												</tfoot>
											</table>
										</div><!-- /.box-body -->
									</div><!-- /.box -->
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div>
		</div><!-- /.tab-content -->
	</div><!-- /.nav-tabs-custom -->
</section><!-- /.content -->

<!-- Hidden form to add new target -->
<section class="hidden-add-target-form">
	<div class="box box-info">
		<div class="box-header with-border">
			<h3 class="box-title">Add new target</h3>
		</div><!-- /.box-header -->
		<!-- form start -->
		<?= form_open('products/addtargettoproductsalestarget', array("class" => "form-horizontal")); ?>
		<div class="box-body">
			<div class="form-group">
				<label for="year" class="col-sm-2 control-label">Select User</label>
				<div class="col-sm-4">
					<select class="form-control" name="userid">
<?php
						foreach ($users as $user)
						{
?>
							<option value="<?= $user->userid; ?>"><?= $user->username; ?></option>
<?php
						}
?>
					</select>
				</div>

				<label for="year" class="col-sm-2 control-label">Year</label>

				<div class="col-sm-4">
					<input type="number" min="<?= date('Y'); ?>" class="form-control" id="year"name="year" placeholder="Year" value="<?= date("Y"); ?>" required />
				</div>
				<br />
				<br />

				<label for="month" class="col-sm-2 control-label">Month</label>

				<div class="col-sm-4">
					<input type="number" min="1" max="12" class="form-control" id="month" name="month" value="<?= date("m"); ?>" placeholder="Month" required />
				</div>
			</div>

			<div class="form-group">
				<label for="target" class="col-sm-2 control-label">Target</label>
				<div class="col-sm-10">
					<input type="number" min="0" class="form-control" id="target" name="salestarget" placeholder="target" required />
					<input type="hidden" name="product_code" value="<?= $prodcode; ?>" />
					<input type="hidden" name="page_code" value="<?= $page; ?>" />
				</div>
			</div>
		</div><!-- /.box-body -->

		<div class="box-footer">
			<button type="button" class="btn btn-default" onclick="closeAddTargetForm();">Cancel</button>
			<button type="submit" class="btn btn-info pull-right">Save</button>
		</div><!-- /.box-footer -->
		<?= form_close(); ?>
	</div>
</section>

<section class="hidden-add-uploadtarget-form" style="top: 20%;">
	<div class="box box-info">
		<div class="box-header with-border">
			<h3 class="box-title">Upload target(s)</h3>
		</div><!-- /.box-header -->
		<!-- form start -->
		<?= form_open_multipart('products/uploadtarget', array("class" => "form-horizontal", "accept" => ".csv")); ?>
		<div class="box-body">
			<div class="form-group">
				<label for="month" class="col-sm-2 control-label">Upload CSV</label>
				<div class="col-sm-4">
					<input type="file" name="file" class="form-control" required />
				</div>
				<div class="col-sm-12">

				</div>
			</div>
		</div><!-- /.box-body -->

		<div class="box-footer">
			<button type="button" class="btn btn-default" onclick="closeAdduploadTargetForm();">Cancel</button>
			<button type="submit" class="btn btn-info pull-right">Upload</button>
			<hr />
			<h5>CSV File format:</h5>
			<h5>User id, PAC, PAC Level, Year Month, Sales Target</h5>
			<hr style="margin-top: 5px; margin-bottom: 5px;" />
			<div class="row">
				<div class="col-sm-2">
					<h5><strong>User id</strong></h5>
					<h5><strong>PAC</strong></h5>
					<h5><strong>PAC Level</strong></h5>
					<h5><strong>Year Month</strong></h5>
					<h5><strong>Sales Target</strong></h5>
				</div>
				<div class="col-sm-10">
					<h5> MI-DAS user id</h5>
					<h5>PAC code – could be PAC1, PAC2, PAC3 or PAC4</h5>
					<h5> Number (1-4) that says which level the PAC code is</h5>
					<h5> Year and month</h5>
					<h5> Sales target</h5>
				</div>
			</div>
			<hr style="margin-top: 5px; margin-bottom: 5px;" />
			<h5>e.g. 1, P01, 2, 201805, 12000</h5>
			<h5>* Enter data after heading</h5>
		</div><!-- /.box-footer -->
		<?= form_close(); ?>
	</div>
</section>

<script type="text/javascript">
	$(function()
	{
		$('#example').DataTable({"PAC" : [[3, "desc"]]});
	});
</script>
<script src="<?= $this->config->item('base_folder'); ?>public/plugins/chartjs/Chart.min.js"></script>
<!-- FastClick -->
<script src="<?= $this->config->item('base_folder'); ?>public/plugins/fastclick/fastclick.js"></script>

<!-- page script -->
<!-- Add target POPUP code start -->
<script type="text/javascript">
	function openAddTargetForm()
	{
		$(".overlay").fadeIn('fast', function()
		{
			$(".hidden-add-target-form").show();
		});
	}

	function closeAddTargetForm()
	{
		$(".hidden-add-target-form").fadeOut('fast', function()
		{
			$(".overlay").hide();
		});
	}

	function openAdduploadTargetForm()
	{
		$(".overlay").fadeIn('fast', function()
		{
			$(".hidden-add-uploadtarget-form").show();
		});
	}

	function closeAdduploadTargetForm()
	{
		$(".hidden-add-uploadtarget-form").fadeOut('fast', function()
		{
			$(".overlay").hide();
		});
	}

	$(function()
	{
		/* Code to show the target if the url have #target appended. */
		if ("#target" == window.location.hash)
		{
			$("#showiftarget a").click();
		}

		if ("#success" == window.location.hash)
		{
			$("#showifkpi a").click();
		}

		if ("#editkpi" == window.location.hash)
		{
			$("#showifkpi a").click();
		}
	});

	$(".ulink").on('click', function()
	{
		var id = $(this).attr('for');
		var text = $(this).text();

		$(this).addClass("hidden");
		$("#hidden_"+id).removeClass("hidden");
		$("#surname_"+id).val(text);
		$("#surname_"+id).focus();
	});

	$(".flink").on('click', function()
	{
		var id = $(this).attr('for');
		var text = $(this).text();

		$(this).addClass("hidden");
		$("#fhidden_"+id).removeClass("hidden");
		$("#firstname_"+id).val(text);
		$("#firstname_"+id).focus();
	});

	function validateYearMonth(year, month)
	{
		var yearRegExp = /[\d]{4}/;
		var monthRegExp = /[\d]{1,2}/;
		var msg = "";
		var valid = true;

		if (!yearRegExp.test(year))
		{
			msg+= "Year is not valid\n";
			valid = false;
		}

		if (!monthRegExp.test(month))
		{
			msg+= "Month is not valid\n";
			valid = false;
		}
		else
		{
			if (month <= 0 || month > 12)
			{
				msg+= "Please select a month between 1 to 12\n";
				valid = false;
			}
		}

		if ("" != msg)
		{
			alert(msg);
		}

		return valid;
	}

	function updateyearmonth(id, page, prodcode)
	{
		var year = $("#year_"+id).val();
		var month = $("#month_"+id).val();
		//alert(id);
		var valid = validateYearMonth(year, month);

		if (valid)
		{
			var parms =
			{
				yearmonth : ''+year+month,
				id        : id,
				page      : page,
				prodcode  : prodcode,
			};

			$.ajax(
			{
				url     : base_url+'products/updateyearmonth/',
				data    : parms,
				type    : 'POST',
				success : function(result)
				{
					if ("success" == result.value)
					{
						closeediting(id, "yearmonth", true);

						if (month < 10)
						{
							month = '0'+month;
						}

						$("#ulink_"+id).text(''+year+month);
					}

					if ("duplicate" == result.value)
					{
						closeediting(id, "yearmonth", false);
						alert("The target for this Year/Month already exists.");
					}

					if ("notsaved" == result.value)
					{
						closeediting(id, "yearmonth", false);
						alert("The Year/Month for this target could not be updated due to some error.\nPlease try again later.");
					}
				}
			});
		}
	}

	function validateSalesTarget(salestarget)
	{
		var salestargetRegExp = /[\d]+/;
		var msg = "";
		var valid = true;

		if (!salestargetRegExp.test(salestarget))
		{
			msg+= "Please enter a valid sales target\n";
			valid = false;
		}

		if ("" != msg)
		{
			alert(msg);
		}

		return valid;
	}

	function updatesalestarget(id, page)
	{
		var salestarget = $("#salestarget_"+id).val();
		var valid = validateSalesTarget(salestarget);

		if (valid)
		{
			var parms =
			{
				salestarget : salestarget,
				id          : id,
				page        : page,
			};

			$.ajax(
			{
				url     : base_url+'products/updatesalestarget/',
				data    : parms,
				type    : 'POST',
				success : function(result)
				{
					if ("success" == result.value)
					{
						closeediting(id, "target", true);
						$("#flink_"+id).text(salestarget);
					}

					if ("notsaved" == result.value)
					{
						closeediting(id, "target", false);
						alert("The target for this Year/Month could not be saved due to some error.\nPlease try again later.");
					}
				}
			});
		}
	}

	function deletetarget(id, userid, page)
	{
		var confirmed = confirm('Are you sure to delete this target?');

		if (confirmed)
		{
			$.ajax(
			{
				method : "DELETE",
				url    : base_url+"products/deletetarget/"+id+"/"+userid+"/"+page,
				async  : true,
			}).done(function(response)
			{
				if (response.deleteresult)
				{
					alertHtml = '<div class="alert alert-danger">Target record deleted successfully!</div>';
					$("#ulink_"+id+", #flink_"+id).removeClass("ulink flink");
					// $("#ulink_"+id+", #flink_"+id+", #dlink_"+id).parent().addClass("italicize");
					// $("#ulink_"+id+", #flink_"+id+", #dlink_"+id).parent().text("deleted");
					//	$('.alert').remove();
					$("#alertmsg").html(alertHtml);
					$("#ulink_"+id).parent().parent().remove();
					//location. reload(true);
					//$("#showiftarget a").click();
					table.reload();
				}
			});
		}
	}

	function closeediting(id, type, saved)
	{
		// alert(id);alert(type);alert(saved);
		var ulink_text = '';

		if ("target" == type) 
		{
			$("#fhidden_"+id).addClass("hidden");

			if (!saved)
			{
				$("#salestarget_"+id).val($("#flink_"+id).text());
			}

			$("#flink_"+id).removeClass("hidden");
		}
		else if ("email" == type)
		{
			$("#ehidden_"+id).addClass("hidden");

			if (!saved)
			{
				elink_text = $("#elink_"+id).text();
				$("#email_"+id).val(elink_text);
			}

			$("#elink_"+id).removeClass("hidden");
		}
		else
		{
			$("#hidden_"+id).addClass("hidden");

			if (!saved)
			{
				ulink_text = $("#ulink_"+id).text();
				year = ulink_text.substr(0, 4);
				month = ulink_text.substr(4, 2);
				$("#year_"+id).val(year);
				$("#month_"+id).val(month);
			}

			$("#ulink_"+id).removeClass("hidden");
		}
	}
</script>

<?php require_once(BASEPATH.'../application/views/common/line_a_vs_line_b_charts.php'); ?>

<div class="overlay"></div>

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

<script src="<?= $this->config->item('base_folder'); ?>application/modules/users/js/jquery.alerts.js"></script>

<script>
	function manage_cookie(cookie_name,cookie_value)
	{
		$.ajax({
		type: "POST",
		dataType: "html",
		url: "<?= base_url(); ?>/products/manage_cookie",
		data: {cookie_name:cookie_name,cookie_value:cookie_value},
		success: function(data) {
		}
		});
	}
</script>

<?php if($threeyearproductschart=='Y'){ ?>
  <script>
    $(function () {
      $("#threeyearproductstable_nav a").click();
    });
  </script>
<?php }  ?>