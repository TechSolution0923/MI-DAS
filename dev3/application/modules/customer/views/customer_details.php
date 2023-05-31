<?php
	/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
	$canSeeMargins = canSeeMargins();
	$canEditNotes = canEditNotes();
	$canEditTerms = canEditTerms();
	$curryear = date('Y');
?>
<style>
	.bs-example-modal-lg
	{
		display: none !important;
	}

	.border-target
	{
		border-bottom: 2px solid;
	}

	.border-target-top
	{
		border-bottom: 2px solid;
		border-top: 2px solid;
	}

	.bg-red-full
	{
		background: #dd4b39;
	}

	.bg-yellow-full
	{
		background: #f39c12;
	}

	.bg-green-full
	{
		background: #00a65a;
	}

	.fa.red-arrow
	{
		margin-left: 10px;
		font-size: 18px;
		color: #dd4b39;
	}

	.fa.yellow-arrow
	{
		margin-left: 10px;
		font-size: 18px;
		color: #f39c12;
	}

	.fa.green-arrow
	{
		margin-left: 10px;
		font-size: 18px;
		color: #00a65a;
	}
</style>

<!-- Modal popup for graphs ... only displays when link is clicked -->
<div class="modal fade bs-example-modal-lg" style="display: none !important;" id="GraphModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myLargeModalLabel">&nbsp;</h4>
			</div>
			<div class="modal-body">
				<div id="flot2" style="min-height:200px;"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<input type="hidden" values="" id="chk_input">
<!-- End modal popup -->
<!-- Content Header (Page header) -->
<section class="content-header">
	<h4><?php $raccount = $account; echo $account." - ".$nameaddress?></h4>
	<ol class="breadcrumb">
		<li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="<?= base_url(); ?>customer"><i class="fa fa-dashboard"></i> Customers</a></li>
		<li class="active"><?= $account." - ".$customername?></li>
	</ol>
</section>
<!-- Main content -->
<section class="content">
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs" role="tablist" onclick="clickedOnTab(event);">
			<li role="presentation" class="active"><a href="#proSalesAnalysis" id="proSalesAnalysisTab" role="tab" data-toggle="tab" aria-expanded="false">Sales Analysis</a></li>
			<li role="presentation" class=""><a href="#pacsalesvstarget" id="pacsalesvstargetTab" role="tab" data-toggle="tab" aria-expanded="false" data-params='{"action" : "customerPACSalesVsTarget/<?= date("Y").date("m"); ?>", "accountId" : "<?= base64_encode($account); ?>"}'>Sales vs. Target</a></li>
			<li role="presentation" class=""><a href="#proBalances" id="proBalancesTab" role="tab" data-toggle="tab" aria-expanded="false" data-params='{"action" : "customerDetailsBalance", "accountId" : "<?= base64_encode($account); ?>", "editId" : "internaltext"}'>Balances</a></li>
			<li role="presentation" class=""><a href="#proQuotes" id="proQuotesTab" role="tab" data-toggle="tab" aria-expanded="false" data-params='{"action" : "customerDetailsQuotes", "accountId" : "<?= base64_encode($account); ?>"}'>Quotes</a></li>
			<li role="presentation" class=""><a href="#proOrders" id="proOrdersTab" role="tab" data-toggle="tab" aria-expanded="false" data-params='{"action" : "customerDetailsOrders", "accountId" : "<?= base64_encode($account); ?>"}'>Orders</a></li>
			<li role="presentation" class=""><a href="#proTerms" id="proTermsTab" role="tab" data-toggle="tab" aria-expanded="false" data-params='{"action" : "customerDetailsTerms", "accountId" : "<?= base64_encode($account); ?>", "editId" : "discnt"}'>Terms</a></li>
			<li role="presentation" class=""><a href="#proDetails" id="proDetailsTab" role="tab" data-toggle="tab" aria-expanded="false" data-params='{"action" : "customerDetailsDetail", "accountId" : "<?= base64_encode($account); ?>"}'>Details</a></li>
			<li role="presentation" class=""><a href="#targets" id="targetsTab" role="tab" data-toggle="tab" aria-expanded="false" data-params='{"action" : "customerPACTargets", "accountId" : "<?= base64_encode($account); ?>"}'>Targets</a></li>
			<li role="presentation" class=""><a href="#customer_contacts" id="customer_contactsTab" role="tab" data-toggle="tab" aria-expanded="false" data-params='{"action" : "customerContacts", "accountId" : "<?= base64_encode($account); ?>", "editId" : "internaltext"}'>Contacts</a></li>
			<li role="presentation" class=""><a href="#customer_tasks" id="customer_tasksTab" role="tab" data-toggle="tab" aria-expanded="false">Tasks</a></li>
			<li role="presentation" class=""><a href="#customer_reps" id="customer_repsTab" role="tab" data-toggle="tab" aria-expanded="false" data-params='{"accountId" : "<?= base64_encode($account); ?>"}'>Sales Reps</a></li>
		</ul>
		<div class="tab-content">
			<div class="active tab-pane" id="proSalesAnalysis">
				<div class="row">
					<div class="col-xs-12">
						<div class="nav-tabs-custom left-tab">
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active"><a href="#pro1Charts" role="tab" data-toggle="tab" aria-expanded="false">Charts</a></li>
								<li role="presentation" class=""><a href="#pro1PAC1" role="tab" data-toggle="tab" aria-expanded="false">PAC1</a></li>
								<li role="presentation" class=""><a href="#pro1PAC2" role="tab" data-toggle="tab" aria-expanded="false">PAC2</a></li>
								<li role="presentation" class=""><a href="#pro1PAC3" role="tab" data-toggle="tab" aria-expanded="false">PAC3</a></li>
								<li role="presentation" class=""><a href="#pro1PAC4" role="tab" data-toggle="tab" aria-expanded="false">PAC4</a></li>
								<li role="presentation" class=""><a href="#pro1Products" role="tab" data-toggle="tab" aria-expanded="false">Products</a></li>
								<li role="presentation" class=""><a href="#pro1Orders" role="tab" data-toggle="tab" aria-expanded="false">Orders</a></li>
							</ul>
							<div class="tab-content">
								<div class="active tab-pane" id="pro1Charts">
									<div class="row">
										<div class="col-xs-12 col-sm-8 col-md-10">
											<div class="active tab-pane" id="pro1Charts">
												<div class="row">
													<div class="col-xs-12 col-sm-12 col-md-10">
														<div class="nav-tabs-custom">
															<ul class="nav nav-tabs pull-right">
																<li class="active"><a href="#threeyearsaleschart" data-toggle="tab" onclick="manage_cookie('twoyearsalesanalysischart','N')"><i class="fa fa-line-chart"></i></a></li>
																<li onclick="manage_cookie('twoyearsalesanalysischart','Y')" id="twoyearsalesanalysistable_nav"><a href="#threeyearsalestable" data-toggle="tab"><i class="fa fa-table"></i></a></li>
																<li class="pull-left header"><i class="fa fa-shopping-cart"></i>Sales</li>
															</ul>
															<div class="tab-content no-padding">
																<div class="tab-pane active" id="threeyearsaleschart" style="position: relative;">
																	<div class="row">
																		<div class="col-xs-12 col-sm-8 col-md-9">
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
																</div>
																<div class="tab-pane" id="threeyearsalestable" style="position: relative; overflow: unset;">
																	<table class="table table-striped">
																		<?php
																			$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
																			for ($i = 1; $i < $yearstartmonth; $i++) {
																				$tmp = array_shift($months);
																				array_push($months, $tmp);
																			}
																		?>
																		<tr class="border-target-top">
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
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
<?php
								for ($p = 1; $p <= 4; $p++) /* For loop to generate the PAC1, PAC2, PAC3 and PAC4 */
								{
									$currentPac = "r_pac".$p;
									$r_pac = $$currentPac;
?>
									<div class="tab-pane" id="pro1PAC<?= $p; ?>">
										<div class="row">
											<div class="col-xs-12 col-sm-8 col-md-10">
												<table class="table pro1PAC<?= $p; ?>-example-table table-bordered table-striped">
													<thead>
														<tr>
															<th>Code</th>
															<th>Description</th>
															<th class="sum">Sales YTD</th>
															<th class="sum">Qty YTD</th>
															<th class="sum">Sales Diff %</th>
															<th class="sum">Qty Diff %</th>
															<th class="sum">Sales <?= $curryear - 1; ?></th>
															<th class="sum">Qty <?= $curryear - 1; ?></th>
															<th class="sum">Sales <?= $curryear - 2; ?></th>
															<th class="sum">Qty <?= $curryear - 2; ?></th>
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

													<tbody>
<?php
														$totals = displayPAC($r_pac, $canSeeMargins, $p, $account);
?>
													</tbody>

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
<?php
															if ($canSeeMargins)
															{
?>
																<th id="11">GM% MTD</th>
																<th id="12">GM% YTD</th>
<?php
															}
?>
														</tr>
														<tr style="background-color: #e1e1e1; font-style: italic; font-weight: bold; color: black;" class="total-row">
															<td class="nototal 1">Total</td>
															<td class="nototal 2"></td>
															<td class="hastotals 3"><?= $totals['salesytd']; ?></td>
															<td class="hastotals 4"><?= $totals['qtyytd']; ?></td>
															<td class="hastotals 5"><?= getDiffPercentageFormatted($totals['salesytd'], $totals['YoY1Sales'] + $totals['YoY1ProRataAdjustment']); ?></td>
															<td class="hastotals 6"><?= getDiffPercentageFormatted($totals['qtyytd'], $totals['YoY1Qty']); ?></td>
															<td class="hastotals 7"><?= number_format($totals['YoY1Sales'] + $totals['YoY1ProRataAdjustment'], 2); ?></td>
															<td class="hastotals 8"><?= $totals['YoY1Qty']; ?></td>
															<td class="hastotals 9"><?= $totals['YoY2Sales']; ?></td>
															<td class="hastotals 10"><?= $totals['YoY2Qty']; ?></td>
<?php
															if ($canSeeMargins)
															{
?>
																<td class="hastotals 11"><?= getDiffMargin($totals['salesmtd'], $totals['costsmtd']); ?></td>
																<td class="hastotals 12"><?= getDiffMargin($totals['salesytd'], $totals['costsytd']); ?></td>
<?php
															}
?>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
									</div>
<?php
								}
?>
								<div class="tab-pane" id="pro1Products">
									<div class="row">
										<div class="col-xs-12 col-sm-8 col-md-10">
											<table class="table product-example-table table-bordered table-striped" id="product-example-table">
												<thead>
													<tr>
														<th>Code</th>
														<th>PAC4</th>
														<th>Description</th>
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
														<td class="hastotal" data-value="sales-ytd"></td>
														<td class="hastotal" data-value="qty-ytd"></td>
														<td class="hastotal" data-value="sales-diff"></td>
														<td class="hastotal" data-value="qty-diff"></td>
														<td class="hastotal" data-value="sales-y1"></td>
														<td class="hastotal" data-value="qty-y1"></td>
														<td class="hastotal" data-value="sales-y2"></td>
														<td class="hastotal" data-value="qty-y2"></td>
														<td class="hastotal" data-value="sales-mtd"></td>
														<td class="hastotal" data-value="qty-mtd"></td>
<?php
														if ($canSeeMargins)
														{
?>
															<td class="hastotal" data-value="total-gm-mtd"></td>
															<td class="hastotal" data-value="total-gm-ytd"></td>
<?php
														}
?>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>

								<div class="tab-pane" id="pro1Orders">
									<div class="row">
										<div class="col-xs-12 col-sm-8 col-md-10">
											<table class="table order-table table-bordered table-striped sales-analysis-orders" style="width: 100% !important;">
												<thead>
													<tr>
														<th>Order No</th>
														<th>Date</th>
														<th>Product</th>
														<th>Description</th>
														<th>Quantity</th>
														<th>Value</th>
														<th>Invoice</th>
													</tr>
												</thead>
												<tbody></tbody>
												<tfoot>
													<tr>
														<th>Order No</th>
														<th>Date</th>
														<th>Product</th>
														<th>Description</th>
														<th>Quantity</th>
														<th>Value</th>
														<th>Invoice</th>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="proBalances">
				<div class="row">
					<div class="col-xs-12">
						<div class="content_box">
							<div class="loading-placeholder">
								<span>Loading Balances...</span><span><img src="../../public/images/loading-gears-animation-11-2.gif" /></span>
							</div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->

			<div class="tab-pane" id="proQuotes">
				<div class="row">
					<div class="col-xs-12">
						<div class="content_box">
							<div class="loading-placeholder">
								<span>Loading Quotes...</span><span><img src="../../public/images/loading-gears-animation-11-2.gif" /></span>
							</div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->

			<div class="tab-pane" id="proOrders">
				<div class="row">
					<div class="col-xs-12">
						<div class="content_box box-body">
							<div class="loading-placeholder">
								<span>Loading Orders...</span><span><img src="../../public/images/loading-gears-animation-11-2.gif" /></span>
							</div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->

			<div class="tab-pane" id="proTerms">
				<div class="row">
					<div class="col-xs-12">
						<div class="content_box">
							<div class="loading-placeholder">
								<span>Loading Terms...</span><span><img src="../../public/images/loading-gears-animation-11-2.gif" /></span>
							</div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->

			<div class="tab-pane" id="proDetails">
				<div class="row">
					<div class="col-xs-12">
						<div class="content_box">
							<div class="loading-placeholder">
								<span>Loading Details...</span><span><img src="../../public/images/loading-gears-animation-11-2.gif" /></span>
							</div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->

			<div class="tab-pane" id="targets">
				<div class="row">
					<div class="col-xs-12">
						<div class="content_box">
							<div class="loading-placeholder">
								<span>Loading Targets...</span><span><img src="../../public/images/loading-gears-animation-11-2.gif" /></span>
							</div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->

			<div class="tab-pane" id="customer_contacts">
				<div class="row">
					<div class="col-xs-12">
						<div class="content_box">
							<div class="loading-placeholder">
								<span>Loading Contacts...</span><span><img src="../../public/images/loading-gears-animation-11-2.gif" /></span>
							</div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->

			<div class="tab-pane" id="customer_tasks">
				<div class="row">
					<div class="col-xs-12">
						<div class="content_box">
							<div class="btn-group btn-group-toggle" data-toggle="buttons">
								<label class="btn btn-secondary task-list-selectors active" data-overdue="true" onclick="onActivate(event, '<?= $account; ?>');">
									<input type="radio" name="options" id="option1" autocomplete="off" checked /> Overdue Tasks
								</label>
								<label class="btn btn-secondary task-list-selectors" data-overdue="false" onclick="onActivate(event, '<?= $account; ?>');">
									<input type="radio" name="options" id="option2" autocomplete="off" /> All Tasks
								</label>
							</div>

							<button type="submit" name="addnew" class="btn btn-success pull-right bottom5" onclick="return addTask('<?= $account; ?>');"><i class="fa fa-fw fa-plus-circle"></i>Add task</button>

							<?= form_open('tasks/index'); ?>

							<div class="row">
								<div class="col-xs-12">
									<div class="box">
										<div class="box-body">
											<div class="alert alert-success alert-dismissible hidden" id="statusAndMessage">
												<a href="#" class="close" aria-label="close" onclick="closemessage();">&times;</a>
												<strong id="status">Success!</strong> <span id="statusMessage">Indicates a successful or positive action.</span>
											</div>

											<table class="table table-bordered table-striped" id="tasksTable">
												<thead>
													<tr>
														<th>Task ID</th>
														<th>Contact name</th>
														<th>Date</th>
														<th>Description</th>
														<th>Completed</th>
														<th width="100">Actions</th>
													</tr>
												</thead>

												<tbody>
												</tbody>

												<tfoot>
													<tr>
														<th>Task ID</th>
														<th>Contact name</th>
														<th>Date</th>
														<th>Description</th>
														<th>Completed</th>
														<th width="100">Actions</th>
													</tr>
												</tfoot>
											</table>
										</div><!-- /.box-body -->
									</div><!-- /.box -->
								</div><!-- /.col -->
							</div><!-- /.row -->

							<?= form_close(); ?>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->

			<div class="tab-pane" id="customer_reps">
				<div class="row">
					<div class="col-xs-12">
						<div class="content_box">
							<button type="submit" name="addnew" class="btn btn-success pull-right bottom5" onclick="return openModal('#addRepModal', 'newRep/<?= base64_encode($account); ?>');"><i class="fa fa-fw fa-plus-circle"></i>Add Sales Rep</button>

							<?= form_open('customer/repcodes'); ?>

							<div class="row">
								<div class="col-xs-12">
									<div class="box">
										<div class="box-body">
											<table class="table table-bordered table-striped" id="crepsTable">
												<thead>
													<tr>
														<th>Rep code</th>
														<th>Name</th>
														<th width="100">Actions</th>
													</tr>
												</thead>

												<tbody>
												</tbody>

												<tfoot>
													<tr>
														<th>Rep code</th>
														<th>Name</th>
														<th width="100">Actions</th>
													</tr>
												</tfoot>
											</table>
										</div><!-- /.box-body -->
									</div><!-- /.box -->
								</div><!-- /.col -->
							</div><!-- /.row -->

							<?= form_close(); ?>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->

			<div class="tab-pane" id="pacsalesvstarget">
				<div class="row">
					<div class="col-xs-12">
						<div class="content_box">
							<div class="loading-placeholder">
								<span>Customer PAC Sales vs Target</span><span><img src="../../public/images/loading-gears-animation-11-2.gif" /></span>
							</div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.tab-pane -->
		</div><!-- /.tab-content -->
	</div><!-- /.nav-tabs-custom -->

	<input type="hidden" id="CustTextModalDataAcnt" value="<?= $account?>">

	<!--Modal HTML-->
	<div class="modal fade bd-example-modal-lg" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<a href="javascript:hidePreviousBtn();" class="btn btn-info btn-sm previous hidden" id="backToDetails" data-taskid="" data-edit="">&raquo; Task Details</a>
					<h4 class="modal-title" id="myLargeModalLabel">...</h4>
				</div>
				<div class="modal-body">
					...
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade bd-example-modal-sm" id="addRepModal" tabindex="-1" role="dialog" aria-labelledby="myLargeRepModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myLargeRepModalLabel">Add new repcode to customer</h4>
				</div>
				<div class="modal-body">
					...
				</div>
			</div>
		</div>
	</div>

<?php
	$year0dataString = '['.implode(',', $year0data).']';
	$year1dataString = '['.implode(',', $year1data).']';
	$year0dataCmlString = '[';
	$runningTotal = 0;

	foreach ($year0data as $item)
	{
		$runningTotal+= $item;
		$year0dataCmlString.= $runningTotal.',';
	}

	$year0dataCmlString = rtrim($year0dataCmlString, ',');
	$year0dataCmlString.= ']';
?>

	<!-- ChartJS 1.0.1 -->
	<script src="<?= $this->config->item('base_folder'); ?>public/plugins/chartjs/Chart.min.js"></script>
	<!-- FastClick -->
	<script src="<?= $this->config->item('base_folder'); ?>public/plugins/fastclick/fastclick.js"></script>
	
	<?php require_once(BASEPATH.'../application/views/common/line_a_vs_line_b_charts.php'); ?>

	<script>
		var account = '<?= $this->uri->segment(3); ?>';
		var yearmonth = '<?= date("Y").date("m"); ?>';

		$(function()
		{
			// vik_ajax('proQuotes','customerDetailsQuotes','<?= $account; ?>','');
			// vik_ajax('pacsalesvstarget','customerPACSalesVsTarget/'+yearmonth,'<?= $account; ?>','');
			// vik_ajax('proOrders','customerDetailsOrders','<?= $account; ?>','');
			// vik_ajax('proTerms','customerDetailsTerms','<?= $account; ?>','discnt');
			// vik_ajax('proDetails','customerDetailsDetail','<?= $account; ?>','');
			// vik_ajax('targets','customerPACTargets','<?= $account; ?>','');
			// vik_ajax('proBalances','customerDetailsBalance','<?= $account; ?>','internaltext');
			// vik_ajax('customer_contacts','customerContacts','<?= $account; ?>','internaltext');
			flot1();

			// Graph Modal - this handles pac1-4 and product graphs by means of the level code. The called php file determines the WHERE clause accordingly.

			$('#GraphModal').on('shown.bs.modal', function(event)
			{
				var link        = $(event.relatedTarget); // Link that triggered the modal
				var account     = link.data('account'); // Extract data from data-* attributes
				var level       = link.data('level');
				var code        = link.data('code');
				var description = link.data('description');
				var heading     = code + " - " + description;

				$(".modal-header #myLargeModalLabel").text( heading );

				var datastring = "account=" + account + "&level=" + level + "&code=" + code;
				var modal = $(this);

				$.ajax(
				{
					async: false,
					type: "POST",
					url: base_url+"customer/customerModelGraph/",
					data: datastring,
					datatype: "json",
					cache: false,
					success: function(data)
					{
						//console.log(data);
						returndata = JSON.parse(data);
						console.log(returndata);
						modalgraphdata0 = eval(returndata[0]);
						modalgraphdata1 = eval(returndata[1]);
						modalgraphdata2 = eval(returndata[2]);

						// MODAL GRAPH

						flot2(modalgraphdata0,modalgraphdata1,modalgraphdata2);
					},
					error: function(result, status, error)
					{
						console.log(err);
					},
					complete: function(result, status)
					{
					},
				});
			});
		});

		flot1 = function()
		{
			var data0 = <?= $year0data; ?>;
			var data1 = <?= $year1data; ?>;

			// console.log(data0); //debug
			var dataset =
			[
				{
					label: <?= "'".$graphlabel0."'"; ?>,
					data: data0,
					color: "#22BAA0",
					lines: { show: true },
					shadowSize: 0,
				},
				{
					data: data0,
					color: "#fff",
					lines: { show: false },
					points:
					{
						show: true,
						fill: true,
						radius: 4,
						fillColor: "#22BAA0",
						lineWidth: 2,
					},
					curvedLines: { apply: false },
					shadowSize: 0,
				},
				{
					label: <?= "'".$graphlabel1."'"; ?>,
					data: data1,
					color: "#5FE8D0",
					lines: { show: true },
					shadowSize: 0,
				},
				{
					data: data1,
					color: "#fff",
					lines: { show: false },
					curvedLines: { apply: false },
					points:
					{
						show: true,
						fill: true,
						radius: 4,
						fillColor: "#5FE8D0",
						lineWidth: 2,
					},
					shadowSize: 0,
				}
			];

			var ticks = [[0, "J"], [1, "F"], [2, "M"], [3, "A"], [4, "M"], [5, "J"], [6, "J"], [7, "A"], [8,"S"], [9,"O"], [10,"N"], [11,"D"]]; // CR0001 <= $ticks?>

			if ($('#flot1').length)
			{
				var plot1 = $.plot("#flot1", dataset,
				{
					series:
					{
						color: "#14D1BD",
						lines:
						{
							show: true,
							fill: 0.2,
						},
						shadowSize: 0,
						curvedLines:
						{
							apply: false,
							active: false,
						},
					},
					xaxis: { ticks: ticks },
					legend: { show: true },
					grid:
					{
						color: "#AFAFAF",
						hoverable: true,
						borderWidth: 0,
						backgroundColor: '#FFF',
					},
					tooltip: true,
					tooltipOpts:
					{
						content: "Sales: £%y",
						defaultTheme: false,
					}
				});
			}
		};

		var flot2 = function (modalgraphdata0, modalgraphdata1, modalgraphdata2)
		{
			var modalgraphdataset =
			[
				{
					label: <?= "'".$graphlabel0."'"; ?>,
					data: eval(modalgraphdata0),
					color: "#22BAA0",
					lines: { show: true },
					shadowSize: 0,
				},
				{
					data: eval(modalgraphdata0),
					color: "#fff",
					lines: { show: false },
					points:
					{
						show: true,
						fill: true,
						radius: 4,
						fillColor: "#22BAA0",
						lineWidth: 2,
					},
					curvedLines: { apply: false },
					shadowSize: 0,
				},
				{
					label: <?= "'".$graphlabel1."'"; ?>,
					data: eval(modalgraphdata1),
					color: "#5FE8D0",
					lines: { show: true },
					shadowSize: 0,
				},
				{
					data: eval(modalgraphdata1),
					color: "#fff",
					lines: { show: false },
					curvedLines: { apply: false },
					points:
					{
						show: true,
						fill: true,
						radius: 4,
						fillColor: "#5FE8D0",
						lineWidth: 2,
					},
					shadowSize: 0,
				},
				{
					label: <?= "'".$graphlabel2."'"; ?>,
					data: eval(modalgraphdata2),
					color: "#E2E2E2",
					lines:
					{
						show: true,
						fill: 0.2,
					},
					shadowSize: 0,
				},
				{
					data: eval(modalgraphdata2),
					color: "#fff",
					lines: { show: false },
					curvedLines: { apply: false },
					points:
					{
						show: true,
						fill: true,
						radius: 4,
						fillColor: "#E2E2E2",
						lineWidth: 2,
					},
					shadowSize: 0,
				}
			];

			var ticks = [[0, "J"], [1, "F"], [2, "M"], [3, "A"], [4, "M"], [5, "J"], [6, "J"], [7, "A"], [8,"S"], [9,"O"], [10,"N"], [11,"D"]]; // CR0001 <= $ticks?>

			if ($('#flot2').length)
			{
				var modalgraphplot1 = $.plot("#flot2", modalgraphdataset,
				{
					series:
					{
						color: "#14D1BD",
						lines:
						{
							show: true,
							fill: 0.2,
						},
						shadowSize: 0,
						curvedLines:
						{
							apply: false,
							active: false,
						},
					},
					xaxis: { ticks: ticks },
					legend: { show: true },
					grid:
					{
						color: "#AFAFAF",
						hoverable: true,
						borderWidth: 0,
						backgroundColor: '#FFF'
					},
					tooltip: true,
					tooltipOpts:
					{
						content: "Sales: £%y",
						defaultTheme: false,
					}
				});
			}
		};

		function autocompleteExecutor(id, appendtoId, data)
		{
			console.log(!!data);

			if (!!data)
			{
				try
				{
					$("#"+id).autocomplete(
					{
						source: data,
						appendTo: "#"+appendtoId,
						cacheLength: 0,
					});
				}
				catch(e)
				{
					console.warn("Err:", e);
				}
			}
		}

		var edittargets = <?= $edittargets; ?>;

		var columns =
		[
			{ "data": "codetype" },
			{ "data": "code" },
			{ "data": "yearmonth" },
			{ "data": "salestarget" },
			{ "data": "delete" },
		];

		if (0 == edittargets)
		{
			columns =
			[
				{ "data": "codetype" },
				{ "data": "code" },
				{ "data": "yearmonth" },
				{ "data": "salestarget" },
			];
		}
	</script>

<script type="text/javascript">
	$(function()
	{
		$('.sales-analysis-orders').DataTable(
		{
			processing      : true,
			serverSide      : true,
			ajax            :
			{
				url      : base_url + "customer/fetchCustomerSalesAnalysisOrders/" + account,
				type     : "post"
			},
			dom     : 'Bfrtip',
			buttons :
			[
				{
					extend : 'csv',
					text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
					title  : 'Sales Analysis Orders',
					action : function(e, dt, node, config)
					{
						var searchTerm = $("#pro1Orders input[type='search']").val();
						document.location.href = base_url + "customer/fetchCustomerSalesAnalysisOrdersCsvExport/" + account + "/" + searchTerm;
					}
				}
			],
			columns         :
			[
				{
					data : "orderno"
				},
				{
                    data: "date",
                    render: function (data, type, row) {
                        return moment(data).format("DD/MM/YYYY");
                    }
                },
				{
					data : "prodcode"
				},
				{
					data : "description"
				},
				{
					data : "quantity"
				},
				{
					data : "sales"
				},
				{
					data : "invoiceno"
				}
			],
			ordering        : false,
			displayLength   : 25
		});
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