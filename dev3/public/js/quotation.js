var columns = '';
var searchString = '';

$(function()
{
	if ($("#quotation_list_table").length > 0)
	{
		var table = $("#quotation_list_table").DataTable(
		{
			processing  : true,
			serverSide  : true,
			searchDelay : 1500,
			order       : [[2, "desc"]],
			ajax        :
			{
				url      : `${base_url}quotation/getAllQuotations`,
				type     : "POST",
				error    : function(resp)
				{
					console.log(resp);
				},
			},
			dom         : 'f<"m-0"B>rtip',
			buttons     :
			[
				{
					text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
					action : function(e, dt, node, config)
					{
						let search = { value : dt.search() };
						let columns = {};

						dt.columns().every(function()
						{
							columns[this.index()] = { search : { value : this.search() } };
						});

						let order =
						{
							0 :
							{
								column : dt.order()[0][0],
								dir    : dt.order()[0][1],
							},
						};

						let request =
						{
							search  : search,
							columns : columns,
							order   : order,
						};

						$.ajax(
						{
							url     : `${base_url}quotation/exportQuotations`,
							type    : "POST",
							data    : request,
							success : function(response)
							{
								let blob = new Blob([response]);
								let link = document.createElement('a');
								link.href = window.URL.createObjectURL(blob);
								link.download = "quotations.csv";
								link.click();
							},
						});
					},
				},
			],
		});

		$('#quotation_list_table tfoot th').each(function()
		{
			var title = $(this).text();
			$(this).html(`<input type="text" placeholder="Search ${title}" style="width: 100%;" />`);
		});

		let timer = new Date();

		table.columns().every(function()
		{
			let thisColumn = this;
			let searchInput = $(thisColumn.footer()).find("input");

			searchInput.on("keyup change", function()
			{
				start = new Date();
				let newSearchTerm = $(this).val();
				let prevSearchTerm = thisColumn.search();

				if (newSearchTerm != prevSearchTerm)
				{
					setTimeout(function()
					{
						if ((new Date() - start) > 1500)
						{
							thisColumn.search(newSearchTerm).draw();
						}
					}, 1500);
				}
			});
		});
	}

	if ($("#quotation_detail_table").length > 0)
	{
		let table = $("#quotation_detail_table");
		let dataTable = $("#quotation_detail_table").DataTable(
		{
			pageLength   : 10,
			dom          : 'f<"m-0"B>rtip',
			buttons      :
			[
				{
					text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
					action : function(e, dt, node, config)
					{
						let search = { value : dt.search() };
						let columns = {};

						dt.columns().every(function()
						{
							columns[this.index()] = { search : { value : this.search() } };
						});

						let order =
						{
							0 :
							{
								column : dt.order()[0][0],
								dir    : dt.order()[0][1],
							},
						};

						let request =
						{
							salesOrderNumber : table.data("salesordernumber"),
							search           : search,
							columns          : columns,
							order            : order,
						};

						$.ajax(
						{
							url     : `${base_url}quotation/exportQuotationDetails`,
							type    : "POST",
							data    : request,
							success : function(response)
							{
								let blob = new Blob([response]);
								let link = document.createElement('a');
								link.href = window.URL.createObjectURL(blob);
								link.download = `quotation-details-${request.salesOrderNumber}.csv`;
								link.click();
							}
						});
					}
				},
			],
		});
	}
});
