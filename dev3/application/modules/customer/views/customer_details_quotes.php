<?php
	/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
	$canSeeMargins = canSeeMargins();
	$canEditNotes = canEditNotes();
	$canEditTerms = canEditTerms();
?>
<div class="box-body">
	<table id="customer-quotes-table" class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>Quotation No.</th>
				<th>Cust Order No.</th>
				<th>Reason</th>
				<th>Value</th>
				<th>Date In</th>
				<th>Follow Up Date</th>
				<th>Expiry Date</th>
			</tr>
		</thead>
		<tbody></tbody>
		<tfoot>
			<tr>
				<th>Quotation No.</th>
				<th>Cust Order No.</th>
				<th>Reason</th>
				<th>Value</th>
				<th>Date In</th>
				<th>Follow Up Date</th>
				<th>Expiry Date</th>
			</tr>
		</tfoot>
	</table>
</div>

<script type="text/javascript">
	$(function()
	{
		$('#customer-quotes-table').DataTable(
		{
			processing      : true,
			serverSide      : true,
			ajax            :
			{
				url      : base_url+"customer/fetchCustomerQuotes/"+account,
				type     : "post"
			},
			dom             : 'Bfrtip',
			buttons         :
			[
				{
					extend : 'csv',
					text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
					action : function(e, dt, node, config)
					{
						var searchTerm = $("#customer-quotes-table_filter input[type='search']").val();
						document.location.href = base_url + "customer/fetchCustomerQuotesCsvExport/" + account + "/" + searchTerm;
					}
				},
			],
			columns         :
			[
				{
					data : "orderno"
				},
				{
					data : "custorderno"
				},
				{
					data : "quotereason"
				},
				{
					data : "quotevalue"
				},
				{
                    data: "datein",
                    render: function (data, type, row) {
                        return moment(data).format("DD/MM/YYYY");
                    }
                },
				{
                    data: "quotefolldate",
                    render: function (data, type, row) {
                        return moment(data).format("DD/MM/YYYY");
                    }
                },
				{
                    data: "quoteexpidate",
                    render: function (data, type, row) {
                        return moment(data).format("DD/MM/YYYY");
                    }
                }
			],
			ordering        : false,
			displayLength   : 25
		});
	});
</script>