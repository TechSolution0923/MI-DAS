<?php
	/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
	$canSeeMargins = canSeeMargins();
	$canEditNotes = canEditNotes();
	$canEditTerms = canEditTerms();
?>
<div class="nav-tabs-custom left-tab">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation"  class="active"><a href="#proTproduct" role="tab" data-toggle="tab" aria-expanded="false">Product</a></li>
		<li role="presentation" class=""><a href="#proTgroup" role="tab" data-toggle="tab" aria-expanded="false">Group</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="proTproduct">
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-10">
					<div class="pro_filter_select">
						<select class="form-control" style=" margin-bottom: 2%;width: 150px;" id="product-term-filter">
							<option value="">All</option>
							<option value="NS">Standard</option>
							<option value="NP">Special</option>
							<option value="NC">Contract</option>
						</select>
					</div>
					<table id="term-cust-prod-table" class="table order-table-term table-bordered table-striped" style="width: 100%;">
						<thead>
							<tr>
								<th>Term</th>
								<th>Type</th>
								<th>Product</th>
								<th>Description</th>
								<th>Base Price</th>
								<th>Discount 1 %</th>
								<th>Discount 2 %</th>
								<th>Nett Price</th>
							</tr>
						</thead>
						<tbody>
<?php
							foreach ($t_product as $row)
							{
								extract($row);

								$effectivefrom = date('d/m/Y', strtotime($effectivefrom));
								$effectiveto   = date('d/m/Y', strtotime($effectiveto));

								if ($termtype == "NS") $termtypedesc = "Standard Terms";
								if ($termtype == "NP") $termtypedesc = "Special Terms";
								if ($termtype == "NC") $termtypedesc = "Contract Terms";

								$termheading = $termtypedesc."-".$termcode."-".$termdescription."-".$effectivefrom."-".$effectiveto;
?>
								<tr id='prodtermsrow'>
									<td><?= $termheading; ?></td>
									<td><?= $termtype; ?></td>
									<td><?= $prodcode; ?></td>
									<td><?= $description; ?></td>
									<td id='baseprice'><?= $baseprice; ?></td>
									<td><a xeditable href='javascript:;' data-baseprice="<?= $baseprice; ?>" id="termsproduct:discount1:unique:<?= $unique; ?>" data-unique="<?= $unique; ?>" data-type='text' data-pk="<?= $unique; ?>" data-name='discount1' data-ng-model='discount1' data-placement='left' data-placeholder='Required' data-showbuttons='true' data-original-title='Enter discount 1' class='editable discnt editable-click'><?= $discount1; ?></a></td>
									<td><a xeditable href='javascript:;' id="termsproduct:discount2:unique:<?= $unique; ?>" data-unique="<?= $unique; ?>" data-type='text' data-pk="<?= $unique; ?>" data-name='discount2' data-ng-model='discount2' data-placement='left' data-placeholder='Required' data-showbuttons='true' data-original-title='Enter discount 2' class='editable discnt editable-click'><?= $discount2; ?></a></td>
									<td id='nettprice'><?= $nettprice; ?></td>
								</tr>
<?php
							}
?>

						</tbody>

						<tfoot>
							<tr>
								<th>Term</th>
								<th>Type</th>
								<th>Product</th>
								<th>Description</th>
								<th>Base Price</th>
								<th>Discount 1 %</th>
								<th>Discount 2 %</th>
								<th>Nett Price</th>
							</tr>
						</tfoot>
					</table>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.tab-pane -->

		<div class="tab-pane" id="proTgroup">
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-10">
					<div class="pro_filter_select">
						<select class="form-control" style="margin-bottom: 2%; width: 150px;" id="group-term-filter">
							<option value="">All</option>
							<option value="NS">Standard</option>
							<option value="NP">Special</option>
							<option value="NC">Contract</option>
						</select>
					</div>
					<table id="term-cust-group-table" class="table order-table-term table-bordered table-striped">
						<thead>
							<tr>
								<th>Term</th>
								<th>Type</th>
								<th>Disc Grp</th>
								<th>Description</th>
								<th>Discount 1 %</th>
								<th>Discount 2 %</th>
							</tr>
						</thead>
						<tbody>
<?php
							foreach ($t_group as $row)
							{
								extract($row);

								$effectivefrom = date('d/m/Y', strtotime($effectivefrom));
								$effectiveto   = date('d/m/Y', strtotime($effectiveto));

								if ($termtype == "NS") $termtypedesc = "Standard Terms";
								if ($termtype == "NP") $termtypedesc = "Special Terms";
								if ($termtype == "NC") $termtypedesc = "Contract Terms";

								$termheading = $termtypedesc."-".$termcode."-".$termdescription."-".$effectivefrom."-".$effectiveto;
?>
								<tr>
									<td><?= $termheading; ?></td>
									<td><?= $termtype; ?></td>
									<td><?= $discgroupcode; ?></td>
									<td><?= $description; ?></td>
									<td><a xeditable href='javascript:;' id="termsgroup:discount1:unique:<?= $unique; ?>" data-unique="<?= $unique; ?>" data-type='text' data-pk="<?= $unique; ?>" data-name='discount1' data-ng-model='discount1' data-placement='left' data-placeholder='Required' data-showbuttons='true' data-original-title='Enter discount 1' class='editable discnt editable-click'><?= $discount1; ?></a></td>
									<td><a xeditable href='javascript:;' id="termsgroup:discount2:unique:<?= $unique; ?>" data-unique="<?= $unique; ?>" data-type='text' data-pk="<?= $unique; ?>" data-name='discount2' data-ng-model='discount2' data-placement='left' data-placeholder='Required' data-showbuttons='true' data-original-title='Enter discount 2' class='editable discnt editable-click'><?= $discount2; ?></a></td>
								</tr>
<?php
							}
?>
						</tbody>

						<tfoot>
							<tr>
								<th>Term</th>
								<th>Type</th>
								<th>Disc Grp</th>
								<th>Description</th>
								<th>Discount 1 %</th>
								<th>Discount 2%</th>
							</tr>
						</tfoot>
					</table>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.tab-pane -->
	</div><!-- /.tab-content -->
</div>

<script type="text/javascript">
	$(function()
	{
		var table = $('.order-table-term').DataTable(
		{
			dom             : 'Bfrtip',
			buttons         :
			[
				{
					extend : 'csv',
					text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
				},
			],
			"columnDefs"    :
			[
				{
					"visible" : false,
					"targets" : 0,
				},
			],
			"order"         : [[ 0, 'asc' ]],
			"displayLength" : 25,
			"drawCallback"  : function(settings)
			{
				var api = this.api();
				var rows = api.rows({page : 'current'}).nodes();
				var last = null;

				api.column(0, {page : 'current'}).data().each(function(group, i)
				{
					if (last !== group)
					{
						$(rows).eq(i).before('<tr class="group"><td colspan="6">'+group+'</td></tr>');

						last = group;
					}
				});
			},
		});

		$('body').on('change', '#product-term-filter', function()
		{
			var selectedValue = $(this).val();
			$('#term-cust-prod-table').DataTable().column(1).search(selectedValue).draw();
		});

		$('body').on('change', '#group-term-filter', function()
		{
			var selectedValueGrp = $(this).val();
			$('#term-cust-group-table').DataTable().column(1).search(selectedValueGrp).draw();
		});

		// Customer Group Terms - Order by the grouping
		$('.order-table-term tbody').on('click', 'tr.group', function()
		{
			var currentOrder = table.order()[0];

			if (currentOrder[0] === 2 && currentOrder[1] === 'asc')
			{
				table.order([0, 'desc']).draw();
			}
			else
			{
				table.order([0, 'asc']).draw();
			}
		});
	});
</script>
