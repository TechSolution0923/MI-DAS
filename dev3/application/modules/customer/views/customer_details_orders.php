<?php
	/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
	$canSeeMargins = canSeeMargins();
	$canEditNotes = canEditNotes();
	$canEditTerms = canEditTerms();
?>
<table id="customer-orders-list" class="table table-bordered table-striped" style="width:0px;">
	<thead>
		<tr>
			<th>Order</th>
			<th>Date In</th>
			<th>Product</th>
			<th>Description</th>
			<th>Quantity</th>
			<th>Unit Price</th>
			<th>Discount 1 %</th>
			<th>Discount 2 %</th>
			<th>Nett Price</th>
			<th>Value</th>
			<th>Required</th>
			<th>Status</th>
		</tr>
	</thead>

	<tbody>
<?php
/*		foreach ($result as $row)
		{
			extract($row);
			$datein        = date('d/m/Y', strtotime($datein));
			$headerdatereq = date('d/m/Y', strtotime($headerdatereq));
			$datereq       = date('d/m/Y', strtotime($datereq));

			$orderheading = "Order No: ".$orderno." Entered: ".$datein." Required: ".$headerdatereq. " Customer Ord No: ".$custorderno;

			$nettprice = $unitprice - (($unitprice / 100) * $discount1);
			$nettprice = $nettprice - (($nettprice / 100) * $discount2);
			$nettprice = number_format($nettprice, 2);
?>
			<tr>
				<td><?= $orderheading; ?></td>
				<td><?= $datein; ?></td>
				<td><?= $prodcode; ?></td>
				<td><?= $fulldesc; ?></td>
				<td><?= $quantity; ?></td>
				<td><?= $unitprice; ?></td>
				<td><?= $discount1; ?></td>
				<td><?= $discount2; ?></td>
				<td><?= $nettprice; ?></td>
				<td><?= $sales; ?></td>
				<td><?= $datereq; ?></td>
				<td><?= $status; ?></td>
			</tr>
<?php
		}
*/?>
	</tbody>

	<tfoot>
		<tr>
			<th>Order</th>
			<th>Date In</th>
			<th>Product</th>
			<th>Description</th>
			<th>Quantity</th>
			<th>Unit Price</th>
			<th>Discount 1 %</th>
			<th>Discount 2 %</th>
			<th>Nett Price</th>
			<th>Value</th>
			<th>Required</th>
			<th>Status</th>
		</tr>
	</tfoot>
</table>

<script type="text/javascript">
	$(function()
	{
		var customerOrderTable = $('#customer-orders-list').DataTable(
		{
			processing      : true,
			serverSide      : true,
			ajax            :
			{
				url      : base_url+"customer/fetchCustomerOrders/" + account,
				type     : "post",
				complete : function()
				{

				},
			},
			dom             : 'Bfrtip',
			buttons         :
			[
				{
					extend : 'csv',
					text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
					action : function(e, dt, node, config)
					{
						var searchTerm = $("#customer-orders-list_filter input[type='search']").val();
						document.location.href = base_url + "customer/fetchCustomerOrdersCsvExport/" + account + "/" + searchTerm;
					}
				},
			],
			columns         :
			[
				{
					data : "orderheading",
					name : "OrderHeading",
				},
				{
					data : "datein",
					name : "DateIn",
				},
				{
					data : "prodcode",
					name : "ProductCode",
				},
				{
					data : "fulldesc",
					name : "Description",
				},
				{
					data : "quantity",
					name : "Quantity",
				},
				{
					data : "unitprice",
					name : "UnitPrice",
				},
				{
					data : "discount1",
					name : "Discount1",
				},
				{
					data : "discount2",
					name : "Discount2",
				},
				{
					data : "nettprice",
					name : "NettPrice",
				},
				{
					data : "sales",
					name : "SalesValue",
				},
				{
					data : "datereq",
					name : "DateRequired",
				},
				{
					data : "status",
					name : "Status",
				},
			],
			columnDefs      :
			[
				{
					"visible" : false,
					"targets" : 0,
				}
			],
			ordering        : false,
			displayLength   : 25,
			drawCallback    : function(settings)
			{
				var api = this.api();
				var rows = api.rows({page : 'current'}).nodes();
				var last = null;

				api.column(0, {page : 'current'}).data().each(function(group, i)
				{
					if (last !== group)
					{
						$(rows).eq(i).before('<tr class="group"><td colspan="11">'+group+'</td></tr>');
						last = group;
					}
				});
			}
		});

		$('#customer-orders-list tfoot th').last().html('<input type="text" placeholder="Search By Status" style="width: 100%;" />');

		customerOrderTable.columns().every(function()
		{
			var thatcolumn = this;

			$('input', this.footer()).on('keyup change', function()
			{
				if (thatcolumn.search() !== this.value)
				{
					thatcolumn.search(this.value).draw();
					$("html, body").animate({scrollTop : 0}, 'slow');
				}
			});
		});
	});
</script>
