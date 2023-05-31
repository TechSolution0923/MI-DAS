var pac2Table = '';
var pac3Table = '';
var pac4Table = '';
var calculatedColumns = {
	4: {
		'columns': [2,6],
		'type': 'percentage'
	},
	5: {
		'columns': [3,7],
		'type': 'percentage'
	},
	10: {
		'columns': [14, 12],
		'type': 'margin'
	},
	11: {
		'columns': [2, 13],
		'type': 'margin'
	}
};
var calculatedColumnsCustomer = {
	4: {
		'columns': [2,3],
		'type': 'percentage'
	},
	7: {
		'columns': [6,11],
		'type': 'margin'
	},
	8: {
		'columns': [2,12],
		'type': 'margin'
	}
};
var exportFunctions = {
dom: 'Bfrtip',

 buttons: [

						

						{

								text: '<span title="Export" class="glyphicon glyphicon-export"></span>',

								action: function ( e, dt, node, config ) {



									var ValuesOfSearch = document.getElementById('chk_input').value;

									var urltogo = base_url+'products/prd1_excel_export/'+ValuesOfSearch;

									document.location.href = urltogo;

								}

						}

				]

};



//var exportFunctions = {};



	

$(function () {

		 $('.nav-tabs a').on('shown.bs.tab', function(event){

		var x = $(event.target).text();         // active tab

	   

   

	  $("#chk_input").val(x);

	 

	});

	var productCustomerTable = $('#new-product-customers').DataTable({
		dom: 'Bfrtip',
		buttons: [
			{ extend: 'csv',  text: '<span title="Export" class="glyphicon glyphicon-export"></span>', title: 'Product Customers'}
		]
	});

	var stockTable=$('#product-stock-content').DataTable( {
	  dom: 'Bfrtip',
	  buttons: [
		  { extend: 'csv',  text: '<span title="Export" class="glyphicon glyphicon-export"></span>', title: 'Product Stock'}
	  ]
  });

$('.target-list-table tfoot th').each( function () {
		var title = $(this).text();
		$(this).html( '<input type="text" style="width:100%" placeholder="Search '+title+'" />' );
	} );
	
 $('#product-stock-content tfoot th:eq(0)').html( '<input type="text" placeholder="Search Branch" style="width:100%" />' );
  $('#product-stock-content tfoot th:eq(1)').html( '<input type="text" placeholder="Search Name" style="width:100%" />' );
  stockTable.columns().every( function () {
	var thatstockTable = this;
		$( 'input', this.footer() ).on( 'keyup change', function () {
	  if ( thatstockTable.search() !== this.value ) {
		  thatstockTable
			  .search( this.value )
			  .draw();
	  }
	});
  });

	productCustomerTable.on('draw', function() {
		productCustomerTable.columns('.sum', {search: 'applied'}).every(function() {
			calculateTotals(this, productCustomerTable, calculatedColumnsCustomer);
		})
	});

});

function calculateTotals(column, table, calculatedColumns) {
	var columnIndex = column.index(),
	sum = false;

	$.each(calculatedColumns, function(key, value) {
		if(key == columnIndex) {
			sum = sumCalculatedColumn(value, table);
		}
	});

	if(!sum) {
		sum = column
		.data()
		.reduce(function (a, b) {
			return (parseFloat(a) + parseFloat(b)).toFixed(2);
		});
	}
	if(isNaN(sum)) {
		sum = 0.00;
	}
	if(!isFinite(sum)) {
		sum = 100.00;
	}

	var id = $(column.footer()).attr('id');
	$(column.footer()).parents('tfoot').find('tr td.'+id).html(sum);
}

function sumCalculatedColumn(settings, table) {
	var first = table.column(settings.columns[0], {search: 'applied'}).data().reduce(function (a, b) {
		return (parseFloat(a) + parseFloat(b)).toFixed(2);
	});
	var second = table.column(settings.columns[1], {search: 'applied'}).data().reduce(function (a, b) {
		return (parseFloat(a) + parseFloat(b)).toFixed(2);
	});

	if(settings.type == 'percentage') {
		var percentage = (first-second)/second*100;

		// if(percentage > 100) {
		// 	percentage -= 100;
		// }
		// else if(percentage < 100)
		// {
		// 	percentage = 100 - percentage;
		// 	percentage = -1 * percentage;
		// }
		return percentage.toFixed(2);
	}

	if(settings.type == 'margin') {
		var percentage  = (first-second) / first*100;
		return percentage.toFixed(2);
	}
}


getAjaxSAList = function(did){

	$.ajax({

		url: "products/getSAList/"+did, 

		success: function(result){

			$("#"+did).html(result);

			//$("."+did+"-example-table").DataTable(exportFunctions);
			

		}

	});

};

var productColumns = '';
var searchString = '';

getAjaxSAListDataTable = function(did)
{
	var table = $("#product-table").DataTable(
	{
		processing : true,
		serverSide : true,
		order      : [[0, "asc"]],
		ajax       :
		{
			url      : `products/getSAList/${did}`,
			type     : "post",
			complete : function(resp)
			{
				productColumns = resp.responseJSON['with'].columns;
				searchString = $('#product-table_wrapper .dataTables_filter input[type="search"]').val();
				$(".dataTables_processing").css("display", "none");

				$(".color-identifier").each(function(k, v)
				{
					var colclass = $(v).attr("data-class");
					$(v).closest("tr").addClass(colclass);
				});

				totalCalculate({}, 'products/getSAListTotalValues/', searchString, productColumns)
			},
			error    : function()
			{
				$("#employee_grid_processing").css("display", "none");
			},
		},
		dom        : 'Bfrtip',
		buttons    :
		[
			{
				text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
				action : function(e, dt, node, config)
				{
					var ValuesOfSearch = getValuesOfSearch();
					var urltogo = `${base_url}products/excel_export/${ValuesOfSearch}`;
					document.location.href = urltogo;
				},
			},
		],
	});

	$('#product-table tfoot th').each(function()
	{
		var title = $(this).text();
		$(this).html(`<input type="text" style="width:100%;" placeholder="Search ${title}" />`);
	});

	// Apply the search

	table.columns().every(function()
	{
		var that = this;

		$('input', this.footer()).on('keyup change', delay(function()
		{
			if (that.search() !== this.value)
			{
				columnSearch(that, this.value);
			}
		}, 500));
	});
};



var getValuesOfSearch = function() {
	var searchKey = $("#product-table").prev().prev().find("input").val();
	return searchKey;
};

var getValuesOfSearch_prd = function(id) {
	var searchKey = id;
	return searchKey;
};


	var addTotalRows = function(table, columns, search) {
		var trElement = document.createElement("tr");
		trElement.id = "productTotalRow";
		trElement.setAttribute("role", "row");
		trElement.classList.add("even");
		trElement.classList.add("totals");
		$.ajax({
			type: 'POST',
			url: "products/getSAListTotalValues/",
			data: {
				'columns': columns,
				'search': {
					'value': search
				}
			},
			success: function(result) {
				for(var rs in result) {
					var tdElement = document.createElement("td");
					tdElement.innerText = result[rs];
					trElement.append(tdElement);
				}
			}
		});

		$('#productTotalRow').remove();
		if($("#productTotalRow").length == 0) {
			$("table#product-table tfoot").append(trElement);
		}
	};

var totalCalculate = function(totals, url, search = "", columns = [])
{
	$("td.nototal:not(:first)").html("");
	$("td.nototal:first").html("Total");

	hasTotal(totals);

	$.ajax(
	{
		url     : base_url+url,
		type    : 'post',
		data    :
		{
			'totals'  : totals,
			'columns' : columns,
			'search'  : { 'value' : search },
		},
		success : function(result)
		{
			if (result.success)
			{
				hasTotal(result.totals);
			}
		}
	});
};

var hasTotal = function(totals)
{
	$("td.hastotal").each(function(key, elem)
	{
		var keyName = $(elem).attr("data-value");
		$(elem).html(parseFloat(totals[keyName]).toFixed(2));
	});
};

var columnSearch = $.fn.dataTable.util.throttle(function (column, val) {
	column.search(val).draw();
}, 2000);

function delay(callback, ms) {
	var timer = 0;
	return function() {
		var context = this, args = arguments;
		clearTimeout(timer);
		timer = setTimeout(function () {
			callback.apply(context, args);
		}, ms || 0);
	};
}