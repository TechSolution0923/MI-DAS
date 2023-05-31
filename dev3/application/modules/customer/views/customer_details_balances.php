<?php
	/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
	$canSeeMargins = canSeeMargins();
	$canEditNotes = canEditNotes();
	$canEditTerms = canEditTerms();
?>
<div class="nav-tabs-custom left-tab">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#AgedSummary" role="tab" data-toggle="tab">Summary</a></li>
		<li role="presentation"><a href="#AgedDetailed" role="tab" data-toggle="tab">Detailed</a></li>
		<li role="presentation"><a href="#AgedMonth0" role="tab" data-toggle="tab"><?= $agedmonth[0]?></a></li>
		<li role="presentation"><a href="#AgedMonth1" role="tab" data-toggle="tab"><?= $agedmonth[1]?></a></li>
		<li role="presentation"><a href="#AgedMonth2" role="tab" data-toggle="tab"><?= $agedmonth[2]?></a></li>
		<li role="presentation"><a href="#AgedMonth3" role="tab" data-toggle="tab"><?= $agedmonth[3]?></a></li>
		<li role="presentation"><a href="#AgedMonth4" role="tab" data-toggle="tab"><?= $agedmonth[4]?></a></li>
		<li role="presentation"><a href="#AgedMonth5" role="tab" data-toggle="tab"><?= $agedmonth[5]?></a></li>
	</ul>
	<div class="tab-content col-sm-8 col-md-10">
		<div role="tabpanel" class="tab-pane active" id="AgedSummary">
			<div class="row m-b-lg">
				<div class="col-md-6">
					<div class="row">
						<form class="form-horizontal">
							<div class="form-group">
								<label for="Credit Limit" class="col-sm-4 control-label">Credit Limit</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-readonly" value="<?= $creditlimit ?>" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="Credit Limit" class="col-sm-4 control-label">Balance</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-readonly" value="<?= $balance ?>" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="Committed Debt" class="col-sm-4 control-label">Committed Debt</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-readonly" value="<?= $committeddebt ?>" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="Potential Debt 30" class="col-sm-4 control-label">Potential Debt  30 Days</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-readonly" value="<?= $potentialdebt1 ?>" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="Potential Debt 3060" class="col-sm-4 control-label">Potential Debt 30-60 Days</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-readonly" value="<?= $potentialdebt2 ?>" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="Potential Debt 60" class="col-sm-4 control-label">Potential Debt > 60 Days</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-readonly" value="<?= $potentialdebt3 ?>" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="Credit Status" class="col-sm-4 control-label">Credit Status</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-readonly" value="<?= $creditstatus ?>" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="Last Payment Date" class="col-sm-4 control-label">Last Payment Date</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-readonly" value="<?= date('d/m/Y', strtotime($lastpaymentdate)) ?>" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="Last Payment Amount" class="col-sm-4 control-label">Last Payment Amount</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-readonly" value="<?= $lastpaymentamount ?>" readonly>
								</div>
							</div>
						</form>
					</div>
				</div>

				<div class="col-md-6" id="Aged Balances">
					<div class="panel panel-white">
						<div class="panel-body">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Month</th>
										<th>Turnover</th>
										<th>Amount</th>
										<th>Collectable</th>
										<th>Overdue</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?= $agedmonth[0]?></td>
										<td><?= $month0sales?></td>
										<td><?= $month0bal?></td>
										<td><?= $month0col?></td>
										<td class="danger"><?= $month0due?></td>
									</tr>
									<tr>
										<td><?= $agedmonth[1]?></td>
										<td><?= $month1sales?></td>
										<td><?= $month1bal?></td>
										<td><?= $month1col?></td>
										<td class="danger"><?= $month1due?></td>
									</tr>
									<tr>
										<td><?= $agedmonth[2]?></td>
										<td><?= $month2sales?></td>
										<td><?= $month2bal?></td>
										<td><?= $month2col?></td>
										<td class="danger"><?= $month2due?></td>
									</tr>
									<tr>
										<td><?= $agedmonth[3]?></td>
										<td><?= $month3sales?></td>
										<td><?= $month3bal?></td>
										<td><?= $month3col?></td>
										<td class="danger"><?= $month3due?></td>
									</tr>
									<tr>
										<td><?= $agedmonth[4]?></td>
										<td><?= $month4sales?></td>
										<td><?= $month4bal?></td>
										<td><?= $month4col?></td>
										<td class="danger"><?= $month4due?></td>
									</tr>
									<tr>
										<td><?= $agedmonth[5]?>+</td>
										<td><?= $month5sales?></td>
										<td><?= $month5bal?></td>
										<td><?= $month5col?></td>
										<td class="danger"><?= $month5due?></td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<th>Totals</th>
										<th></th>
										<th><?= $totalbal?></th>
										<th><?= $totalcol?></th>
										<th class="danger"><?= $totaldue?></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="AgedDetailed">
			<div class="table-responsive">
				<table id="tblageddetail" class="display table example-table balances-custom-detailed-table" style="width: 100%; cellspacing: 0;">
					<thead>
						<tr>
							<th>Doc Date</th>
							<th>Doc No</th>
							<th>Customer Ref</th>
							<th>Other Ref</th>
							<th>Status</th>
							<th>Type</th>
							<th>Due Date</th>
							<th>Total</th>
							<th>Paid</th>
							<th>Outstanding</th>
							<th>Collectable</th>
							<th>Overdue</th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<th>Doc Date</th>
							<th>Doc No</th>
							<th>Customer Ref</th>
							<th>Other Ref</th>
							<th>Status</th>
							<th>Type</th>
							<th>Due Date</th>
							<th>Total</th>
							<th>Paid</th>
							<th>Outstanding</th>
							<th>Collectable</th>
							<th>Overdue</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="AgedMonth0">
			<div class="table-responsive">
				<table id="tblagedmonth0" class="display table order-table" style="width: 100%; cellspacing: 0;">
					<thead>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</thead>

					<tbody>
						<?php
							foreach ($monthlisting[0] as $row)
							{
								extract($row);
								$docdate        = date('d/m/Y', strtotime($docdate));
								$datein         = date('d/m/Y', strtotime($datein));

								$total = $sales + $vat;

								$docheading = "Doc No: ".$docnumber." Date: ".$docdate." Amount: ".$totalamount. " Outstanding: ".$outstandamount;

								$nettprice = $unitprice - (($unitprice / 100) * $discount1);
								$nettprice = $nettprice - (($nettprice / 100) * $discount2);
								$nettprice = number_format($nettprice,2);

								echo "<tr>";
								echo "<td>$docheading</td>";
								echo "<td>$orderno</td>";
								echo "<td>$prodcode</td>";
								echo "<td>$fulldesc</td>";
								echo "<td>$quantity</td>";
								echo "<td>$unitprice</td>";
								echo "<td>$discount1</td>";
								echo "<td>$discount2</td>";
								echo "<td>$nettprice</td>";
								echo "<td>$sales</td>";
								echo "<td>$total</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="AgedMonth1">
			<div class="table-responsive">
				<table id="tblagedmonth1" class="display table order-table" style="width: 100%; cellspacing: 0;">
					<thead>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</thead>

					<tbody>
						<?php
							foreach ($monthlisting[1] as $row)
							{
								extract($row);
								$docdate        = date('d/m/Y', strtotime($docdate));
								$datein         = date('d/m/Y', strtotime($datein));

								$total = $sales + $vat;

								$docheading = "Doc No: ".$docnumber." Date: ".$docdate." Amount: ".$totalamount. " Outstanding: ".$outstandamount;

								$nettprice = $unitprice - (($unitprice / 100) * $discount1);
								$nettprice = $nettprice - (($nettprice / 100) * $discount2);
								$nettprice = number_format($nettprice,2);

								echo "<tr>";
								echo "<td>$docheading</td>";
								echo "<td>$orderno</td>";
								echo "<td>$prodcode</td>";
								echo "<td>$fulldesc</td>";
								echo "<td>$quantity</td>";
								echo "<td>$unitprice</td>";
								echo "<td>$discount1</td>";
								echo "<td>$discount2</td>";
								echo "<td>$nettprice</td>";
								echo "<td>$sales</td>";
								echo "<td>$total</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="AgedMonth2">
			<div class="table-responsive">
				<table id="tblagedmonth2" class="display table order-table" style="width: 100%; cellspacing: 0;">
					<thead>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</thead>

					<tbody>
						<?php
							foreach ($monthlisting[2] as $row)
							{
								extract($row);
								$docdate        = date('d/m/Y', strtotime($docdate));
								$datein         = date('d/m/Y', strtotime($datein));

								$total = $sales + $vat;

								$docheading = "Doc No: ".$docnumber." Date: ".$docdate." Amount: ".$totalamount. " Outstanding: ".$outstandamount;

								$nettprice = $unitprice - (($unitprice / 100) * $discount1);
								$nettprice = $nettprice - (($nettprice / 100) * $discount2);
								$nettprice = number_format($nettprice,2);

								echo "<tr>";
								echo "<td>$docheading</td>";
								echo "<td>$orderno</td>";
								echo "<td>$prodcode</td>";
								echo "<td>$fulldesc</td>";
								echo "<td>$quantity</td>";
								echo "<td>$unitprice</td>";
								echo "<td>$discount1</td>";
								echo "<td>$discount2</td>";
								echo "<td>$nettprice</td>";
								echo "<td>$sales</td>";
								echo "<td>$total</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="AgedMonth3">
			<div class="table-responsive">
				<table id="tblagedmonth3" class="display table order-table" style="width: 100%; cellspacing: 0;">
					<thead>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</thead>

					<tbody>
						<?php
							foreach ($monthlisting[3] as $row)
							{
								extract($row);
								$docdate        = date('d/m/Y', strtotime($docdate));
								$datein         = date('d/m/Y', strtotime($datein));

								$total = $sales + $vat;

								$docheading = "Doc No: ".$docnumber." Date: ".$docdate." Amount: ".$totalamount. " Outstanding: ".$outstandamount;

								$nettprice = $unitprice - (($unitprice / 100) * $discount1);
								$nettprice = $nettprice - (($nettprice / 100) * $discount2);
								$nettprice = number_format($nettprice,2);

								echo "<tr>";
								echo "<td>$docheading</td>";
								echo "<td>$orderno</td>";
								echo "<td>$prodcode</td>";
								echo "<td>$fulldesc</td>";
								echo "<td>$quantity</td>";
								echo "<td>$unitprice</td>";
								echo "<td>$discount1</td>";
								echo "<td>$discount2</td>";
								echo "<td>$nettprice</td>";
								echo "<td>$sales</td>";
								echo "<td>$total</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="AgedMonth4">
			<div class="table-responsive">
				<table id="tblagedmonth4" class="display table order-table" style="width: 100%; cellspacing: 0;">
					<thead>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</thead>

					<tbody>
						<?php
							foreach ($monthlisting[4] as $row)
							{
								extract($row);
								$docdate        = date('d/m/Y', strtotime($docdate));
								$datein         = date('d/m/Y', strtotime($datein));

								$total = $sales + $vat;

								$docheading = "Doc No: ".$docnumber." Date: ".$docdate." Amount: ".$totalamount. " Outstanding: ".$outstandamount;

								$nettprice = $unitprice - (($unitprice / 100) * $discount1);
								$nettprice = $nettprice - (($nettprice / 100) * $discount2);
								$nettprice = number_format($nettprice,2);

								echo "<tr>";
								echo "<td>$docheading</td>";
								echo "<td>$orderno</td>";
								echo "<td>$prodcode</td>";
								echo "<td>$fulldesc</td>";
								echo "<td>$quantity</td>";
								echo "<td>$unitprice</td>";
								echo "<td>$discount1</td>";
								echo "<td>$discount2</td>";
								echo "<td>$nettprice</td>";
								echo "<td>$sales</td>";
								echo "<td>$total</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="AgedMonth5">
			<div class="table-responsive">
				<table id="tblagedmonth5" class="display table order-table" style="width: 100%; cellspacing: 0;">
					<thead>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</thead>

					<tbody>
						<?php
							foreach ($monthlisting[5] as $row)
							{
								extract($row);
								$docdate        = date('d/m/Y', strtotime($docdate));
								$datein         = date('d/m/Y', strtotime($datein));

								$total = $sales + $vat;

								$docheading = "Doc No: ".$docnumber." Date: ".$docdate." Amount: ".$totalamount. " Outstanding: ".$outstandamount;

								$nettprice = $unitprice - (($unitprice / 100) * $discount1);
								$nettprice = $nettprice - (($nettprice / 100) * $discount2);
								$nettprice = number_format($nettprice,2);

								echo "<tr>";
								echo "<td>$docheading</td>";
								echo "<td>$orderno</td>";
								echo "<td>$prodcode</td>";
								echo "<td>$fulldesc</td>";
								echo "<td>$quantity</td>";
								echo "<td>$unitprice</td>";
								echo "<td>$discount1</td>";
								echo "<td>$discount2</td>";
								echo "<td>$nettprice</td>";
								echo "<td>$sales</td>";
								echo "<td>$total</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th>Order</th>
							<th>Order No</th>
							<th>Product</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Discount 1 %</th>
							<th>Discount 2%</th>
							<th>Nett Price</th>
							<th>Value</th>
							<th>Total</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div><!-- /.tab-content -->
</div>

<script type="text/javascript">
	$(function()
	{
		$('.balances-custom-detailed-table').DataTable(
		{
			processing      : true,
			serverSide      : true,
			ajax            :
			{
				url      : base_url+"customer/fetchCustomerDetailsBalance/"+account,
				type     : "post"
			},
			dom     : 'Bfrtip',
			buttons :
			[
				{
					extend : 'csv',
					text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
					title  : 'Customer Balances Detailed',
					action : function(e, dt, node, config)
					{
						var searchTerm = $("#tblageddetail_filter input[type='search']").val();
						document.location.href = base_url + "customer/fetchCustomerDetailsBalanceCsvExport/" + account + "/" + searchTerm;
					}
				}
			],
			columns         :
			[
				{
                    data: "docdate",
                    render: function (data, type, row) {
                        return moment(data).format("DD/MM/YYYY");
                    }
                },
				{
					data : "docnumber"
				},
				{
					data : "custref"
				},
				{
					data : "otherref"
				},
				{
					data : "docstatus"
				},
				{
					data : "doctype"
				},
				{
					data: "duedate",
						render: function (data, type, row) {
							return moment(data).format("DD/MM/YYYY");
						}
                },
				{
					data : "totalamount"
				},
				{
					data : "paidamount"
				},
				{
					data : "outstandamount"
				},
				{
					data : "collectamount"
				},
				{
					data : "overdueamount"
				},
			],
			ordering        : false,
			displayLength   : 10
		});
	});
</script>