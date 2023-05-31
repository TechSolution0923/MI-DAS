<style type="text/css">
	.m-0 .dt-buttons
	{
		margin: 0px 0px 0px 15px;
	}
</style>
<section class="content-header">
	<ol class="breadcrumb">
		<li><a href="<?= base_url(); ?>"><i class="fa fa-dashboard"></i>Home</a></li>
		<li><a href="<?= base_url()."quotation"; ?>">Quotations</a></li>
		<li class="active"><?= $sales_order_number; ?></li>
	</ol>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div style="float:none; height:1em"></div>
			<div class="box">
				<div class="box-body" id="quotation_detail">
					<table class="table table-bordered table-striped" id="quotation_detail_table" data-salesordernumber="<?= $sales_order_number; ?>">
						<thead>
							<tr>
								<th>Product</th>
								<th>Description</th>
								<th>Quantity</th>
								<th>Unit Price</th>
								<th>Discount 1 (%)</th>
								<th>Discount 2 (%)</th>
								<th>Nett Price</th>
								<th>Value</th>
							</tr>
						</thead>

						<tbody>
<?php
							foreach ($products as $product)
							{
?>
								<tr>
									<td><?= $product['prodcode']; ?></td>
									<td><?= $product['description']; ?></td>
									<td><?= $product['quantity']; ?></td>
									<td><?= $product['unitprice']; ?></td>
									<td><?= $product['discount1']; ?></td>
									<td><?= $product['discount2']; ?></td>
									<td><?= number_format($product['nettprice'], 2); ?></td>
									<td><?= $product['sales']; ?></td>
								</tr>
<?php
							}
?>
						</tbody>

						<tfoot>
							<tr>
								<th>Product</th>
								<th>Description</th>
								<th>Quantity</th>
								<th>Unit Price</th>
								<th>Discount 1 (%)</th>
								<th>Discount 2 (%)</th>
								<th>Nett Price</th>
								<th>Value</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
