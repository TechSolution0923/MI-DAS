var is_overdue = true;
var columnSearch = $.fn.dataTable.util.throttle(function(column, val)
{
	column.search(val).draw();
}, 2000);

function delay(callback, ms)
{
	var timer = 0;

	return function()
	{
		var context = this, args = arguments;
		clearTimeout(timer);

		timer = setTimeout(function()
		{
			callback.apply(context, args);
		}, ms || 0);
	};
}

var exportFunctions =
{
	dom     : 'Bfrtip',
	buttons :
	[
		{
			text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
			action : function(e, dt, node, config)
			{
				var ValuesOfSearch = '';
				var ind=document.getElementById('chk_input').value;
				var urltogo = base_url+'customer/prd_excel_export/'+account+'/'+ind+'/'+ValuesOfSearch;
				document.location.href = urltogo;
			},
		}
	],
};

function triggerCustomerProductTable()
{
	if ('undefined' === typeof account)
	{
		account = "";
	}

	$(function()
	{
		// Setup - add a text input to each footer cell
		$('#product-example-table tfoot th').each(function()
		{
			var title = $(this).text();
			$(this).html('<input type="text" placeholder="Search '+title+'" style="width: 100%;" />');
		});

		// DataTable
		var customerColumns = '';
		var searchString = '';

		var table_prd = $(".product-example-table").DataTable(
		{
			processing : true,
			serverSide : true,
			order      : [[0, "asc"]],
			ajax       :
			{
				url      : `${base_url}customer/fetchCustomerProductSalesAnalysis/${account}`,
				type     : "post",
				complete : function(resp)
				{
					customerColumns = resp.responseJSON['with'].columns;
					searchString = $('#product-example-table_wrapper .dataTables_filter input[type="search"]').val();
					$(".dataTables_processing").css("display", "none");

					totals =
					{
						"sales_ytd"    : "...",
						"qty_ytd"      : "...",
						"sales_diff"   : "...",
						"qty_diff"     : "...",
						"sales_y1"     : "...",
						"qty_y1"       : "...",
						"sales_y2"     : "...",
						"qty_y2"       : "...",
						"sales_mtd"    : "...",
						"qty_mtd"      : "...",
						"total_gm_mtd" : "...",
						"total_gm_ytd" : "...",
					};

					totalCalculate(totals, 'customer/productTotals', searchString, customerColumns);
				},
				error    : function()
				{
					$("#employee_grid_processing").css("display", "none");
				},
			},
			createdRow : function(row, data, dataIndex, cells)
			{
				if (data[5] > 0)
				{
					$(row).addClass("greenrow");
				}
				else if (data[5] < 0)
				{
					$(row).addClass("redrow");
				}
			},
			dom        : 'Bfrtip',
			buttons    :
			[
				{
					text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
					action : function(e, dt, node, config)
					{
						var ValuesOfSearch = getSearchKeyCustomerProducts();
						var urltogo = `${base_url}customer/excel_export_customer_products/${account}/${ValuesOfSearch}`;

						document.location.href = urltogo;
					},
				},
			],
		});

		// Apply the search
		table_prd.columns().every(function()
		{
			var that = this;

			$('input', this.footer()).on('keyup change', delay(function()
			{
				if (that.search() !== this.value && (this.value.length >= 2 || this.value.length == 0))
				{
					columnSearch(that, this.value);
				}
			}, 500));
		});
	});
}

function getSearchKeyCustomerProducts()
{
	return $("#DataTables_Table_0_filter > label > input").val();
}

var getValuesOfSearch;

$(document).ajaxStop(function()
{
	console.log("ajax requests complete");
});

$(function()
{
	$('.nav-tabs-custom a').click('shown.bs.tab', function(event)
	{
		var x = $(event.target).text(); // active tab
		$("#chk_input").val(x);
	});

	// $(".product-example-table").DataTable(exportFunctions);
	triggerCustomerProductTable();

	var pac1Table = $(".pro1PAC1-example-table").DataTable(exportFunctions);
	var pac2Table = $(".pro1PAC2-example-table").DataTable(exportFunctions);
	var pac3Table = $(".pro1PAC3-example-table").DataTable(exportFunctions);
	var pac4Table = $(".pro1PAC4-example-table").DataTable(exportFunctions);

	var calculatedColumns =
	{
		4  :
		{
			'columns' : [2, 6],
			'type'    : 'percentage',
		},
		5  :
		{
			'columns' : [3, 7],
			'type'    : 'percentage',
		},
		10 :
		{
			'columns' : [14, 12],
			'type'    : 'margin',
		},
		11 :
		{
			'columns' : [2, 13],
			'type'    : 'margin',
		},
	};

	pac1Table.on('draw', function()
	{
		pac1Table.columns('.sum', {search: 'applied'}).every(function()
		{
			calculateTotals(this, pac1Table, calculatedColumns);
		});
	});

	pac2Table.on('draw', function()
	{
		pac2Table.columns('.sum', {search: 'applied'}).every(function()
		{
			calculateTotals(this, pac2Table, calculatedColumns);
		});
	});

	pac3Table.on('draw', function()
	{
		pac3Table.columns('.sum', {search: 'applied'}).every(function()
		{
			calculateTotals(this, pac3Table, calculatedColumns);
		});
	});

	pac4Table.on('draw', function()
	{
		pac4Table.columns('.sum', {search: 'applied'}).every(function()
		{
			calculateTotals(this, pac4Table, calculatedColumns);
		});
	});

	function calculateTotals(column, table, calculatedColumns)
	{
		var columnIndex = column.index();
		var sum = false;

		$.each(calculatedColumns, function(key, value)
		{
			if (key == columnIndex)
			{
				sum = sumCalculatedColumn(value, table);
			}
		});

		if (!sum)
		{
			sum = column.data().reduce(function(a, b)
			{
				return (parseFloat(a) + parseFloat(b)).toFixed(2);
			});
		}

		if (isNaN(sum))
		{
			sum = 0.00;
		}

		if (!isFinite(sum))
		{
			sum = 100.00;
		}

		var id = $(column.footer()).attr('id');
		// $(column.footer()).parents('tfoot').find('tr td.'+id).html(sum);
		// SD: have commented out the above since this does not take into account the ProRata adjustment c.f. KDS-2668
		// totals therefore always show the total of everything, not just for the filtered results
	}

	function sumCalculatedColumn(settings, table)
	{
		var first = table.column(settings.columns[0], {search: 'applied'}).data().reduce(function(a, b)
		{
			return (parseFloat(a) + parseFloat(b)).toFixed(2);
		});

		var second = table.column(settings.columns[1], {search: 'applied'}).data().reduce(function(a, b)
		{
			return (parseFloat(a) + parseFloat(b)).toFixed(2);
		});

		if (settings.type == 'percentage')
		{
			var percentage = (first-second)/second*100;

			// if (percentage > 100) {
			// 	percentage -= 100;
			// }
			// else if (percentage < 100)
			// {
			// 	percentage = 100 - percentage;
			// 	percentage = -1 * percentage;
			// }
			return percentage.toFixed(2);
		}

		if (settings.type == 'margin')
		{
			var margin = (first-second)/first*100;
			return margin.toFixed(2);
		}
	}

		/* Customer sales analysis table */


	var customerColumns = '';
	var searchString = '';
	var table = $("#customer_list_table").DataTable(
	{
		processing : true,
		serverSide : true,
		order      : [[ 0, "asc" ]],
		ajax       :
		{
			url      : base_url+"customer/fetchCustomerSalesAnalysis",
			type     : "post",
			complete : function(resp)
			{
				customerColumns = resp.responseJSON['with'].columns;
				searchString = $('#customer_list_table_wrapper .dataTables_filter input[type="search"]').val();
				$(".dataTables_processing").css("display", "none");

				$("#customer_list_table tr").each(function(k,v)
				{
					classname = $(v).find('a').attr('data-class');
					$(this).addClass(classname);
				});

				totals =
				{
					"sales_ytd": "...",
					"sales_lastyear": "...",
					"ysales1": "...",
					"sales_last_to_lastyear": "...",
					"ysales2": "...",
					"sales_mtd": "...",
					"diff": "...",
					"total_gm_mtd": "...",
					"total_gm_ytd": "..."
				};

				totalCalculate(totals, 'customer/totals/', searchString, customerColumns);
			},
			error    : function()
			{
				$("#employee_grid_processing").css("display", "none");
			},
		},
		dom        : 'Bfrtip',
		stateSave: true,
		buttons    :
		[
			{
				text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
				action : function(e, dt, node, config)
				{
					var ValuesOfSearch = getValuesOfSearch();
					var urltogo = base_url+'customer/excel_export/'+ValuesOfSearch;
					console.log("URL Used to export: ",urltogo);
					document.location.href = urltogo;
				},
			},
			{
				extend: 'colvis',
				text: '<span title="Column Visibility" class="glyphicon glyphicon-cog"></span>',
				className: 'btn btn-default'
			},
		],
	});

	getValuesOfSearch = function()
	{
		var data = '';
		var qlength = $('input[type="text"], input[type="search"]').length;
		var iteration_watch = 1;

		$('input[type="text"], input[type="search"]').each(function(key,val)
		{
			var ks = $(this).val();

			if ("" == ks)
			{
				ks = "nosearchedvalue";
			}

			if (iteration_watch < qlength)
			{
				data += ks+'/';
			}
			else
			{
				data += ks;
			}

			iteration_watch++;
		});

		return data;
	};

	$('#customer_list_table tfoot th').each( function()
	{
		var title = $(this).text();
		$(this).html('<input type="text" style="width:100%" placeholder="Search '+title+'" />');
	});

	// Apply the search

	table.columns().every(function()
	{
		var that = this;
		$('input', this.footer()).on('keyup change', delay(function()
		{
			if (that.search() !== this.value && (this.value.length >= 2 || this.value.length == 0))
			{
				columnSearch(that, this.value)
			}
		}, 500));
	});

	/* END Customer sales analysis table */

	setTimeout(function()
	{
		// Customer Sales Analysis Orders
		// $('.balances-custom-detailed-table').DataTable(
		// {
		// 	dom     : 'Bfrtip',
		// 	buttons :
		// 	[
		// 		{
		// 			extend : 'csv',
		// 			text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
		// 			title  : 'Customer Balances Detailed',
		// 		}
		// 	]
		// });

		// var customerQuotesTable = $("#customer-quotes-table").DataTable(
		// {
		// 	"dom"           : 'Bfrtip',
		// 	"buttons"       :
		// 	[
		// 		{
		// 			"extend" : 'csv',
		// 			"text"   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
		// 		}
		// 	],
		// 	"order"         : [[0, 'desc']],
		// 	"displayLength" : 25,
		// });

			// var table = $('.order-table').DataTable({

			// 	dom: 'Bfrtip',

		 //  buttons: [

			// 	{ extend: 'csv',  text: '<span title="Export" class="glyphicon glyphicon-export"></span>' }

		 //  ],

			// 	"columnDefs": [

			// 		{ "visible": false, "targets": 0 }

			// 	],

			// 	"order": [[ 1, 'desc' ]],

			// 	"displayLength": 25,

			// 	"drawCallback": function ( settings ) {

			// 		var api = this.api();

			// 		var rows = api.rows( {page:'current'} ).nodes();

			// 		var last=null;



			// 		api.column(0, {page:'current'} ).data().each( function ( group, i ) {

			// 			if ( last !== group ) {

			// 				$(rows).eq( i ).before('<tr class="group"><td colspan="10">'+group+'</td></tr>');



			// 				last = group;

			// 			}

			// 		} );

			// 	}

			// } );
			//custom on change search for status
			// var customerOrderTable = $('#customer-orders-list').DataTable({

			// 	dom: 'Bfrtip',

		 //  buttons: [

			// 	{ extend: 'csv',  text: '<span title="Export" class="glyphicon glyphicon-export"></span>' }

		 //  ],

			// 	"columnDefs": [

			// 		{ "visible": false, "targets": 0 }

			// 	],

			// 	"ordering": false,

			// 	"displayLength": 25,

			// 	"drawCallback": function ( settings ) {

			// 		var api = this.api();

			// 		var rows = api.rows( {page:'current'} ).nodes();

			// 		var last=null;



			// 		api.column(0, {page:'current'} ).data().each( function ( group, i ) {

			// 			if ( last !== group ) {

			// 				$(rows).eq( i ).before('<tr class="group"><td colspan="10">'+group+'</td></tr>');



			// 				last = group;

			// 			}

			// 		} );

			// 	}

			// } );
			// $('#customer-orders-list tfoot th').last().html('<input type="text" placeholder="Search By Status" style="width:100%" />');
			// customerOrderTable.columns().every( function () {
			// 	var thatcolumn = this;

			// 	$( 'input', this.footer() ).on( 'keyup change', function () {
			// 		if ( thatcolumn.search() !== this.value ) {
			// 			thatcolumn
			// 				.search( this.value )
			// 				.draw();
			// 			$("html, body").animate({ scrollTop: 0 }, 'slow');
			// 		}
			// 	} );
			// } );
			//end custom on change search for status

			// $('.order-table tbody').on( 'click', 'tr.group', function () {

			// 	var currentOrder = table.order()[0];

			// 	if ( currentOrder[0] === 2 && currentOrder[1] === 'asc' ) {

			// 		table.order( [ 0, 'desc' ] ).draw();

			// 	}

			// 	else {

			// 		table.order( [ 0, 'asc' ] ).draw();

			// 	}

			// } );


		// 	var table = $('.order-table-term').DataTable({

		// 		dom: 'Bfrtip',
	 // buttons: [
		// 		//'csv'
		// 		{ extend: 'csv',  text: '<span title="Export" class="glyphicon glyphicon-export"></span>' }
		//   ],


		// 		"columnDefs": [

		// 			{ "visible": false, "targets": 0 }

		// 		],

		// 		"order": [[ 0, 'asc' ]],

		// 		"displayLength": 25,

		// 		"drawCallback": function ( settings ) {

		// 			var api = this.api();

		// 			var rows = api.rows( {page:'current'} ).nodes();

		// 			var last=null;



		// 			api.column(0, {page:'current'} ).data().each( function ( group, i ) {

		// 				if ( last !== group ) {

		// 					$(rows).eq( i ).before(

		// 						'<tr class="group"><td colspan="6">'+group+'</td></tr>'

		// 					);



		// 					last = group;

		// 				}

		// 			} );

		// 		}

		// 	} );
		// 	$('body').on('change','#product-term-filter',function(){
		// 	var selectedValue = $(this).val();
		// 	$('#term-cust-prod-table').DataTable().column(1).search(selectedValue).draw();
		// });
		// $('body').on('change','#group-term-filter',function(){
		// 	var selectedValueGrp = $(this).val();
		// 	$('#term-cust-group-table').DataTable().column(1).search(selectedValueGrp).draw();
		// });


		// 	// Customer Group Terms - Order by the grouping

		// 	$('.order-table-term tbody').on( 'click', 'tr.group', function () {

		// 		var currentOrder = table.order()[0];

		// 		if ( currentOrder[0] === 2 && currentOrder[1] === 'asc' ) {

		// 			table.order( [ 0, 'desc' ] ).draw();

		// 		}

		// 		else {

		// 			table.order( [ 0, 'asc' ] ).draw();

		// 		}

		// 	} );

		}, 3000);





		/* editable */
		
		populateTaskTable(null);
			



	});



	function ProcessingExport(action) {

		if ('show'==action) {

			$(".dt-button").show();

			$(".notification").remove();

		} else {

			$(".dt-button").hide();

			notification_html = "<div class='notification'>Processing please wait!!</div>";

			$(".dt-buttons").append(notification_html);

		}

	}

	vik_ajax = function(did, params)
	{
		let fnc = params.action;
		let acId = params.accountId;
		let edtID = params.editId;

		var datastring = fnc+"/"+acId;

		$.ajax(
		{
			async    : true,
			type     : 'POST',
			url      : base_url+'customer/'+datastring,
			datatype : 'text',
			success  : function(response)
			{
				$('#'+did).find('.content_box').html(response).promise().done(function()
				{
					if ("targets" == did)
					{
						populateCustomerTarget();
					}
				});

				$.fn.editable.defaults.mode = 'inline';

				if (edtID == 'internaltext')
				{
					if (!canEditNotes)
					{
						removeHyperlinkOnNote();
						return false;
					}

					$('#internaltext').editable(
					{
						type    : 'textarea',
						title   : 'Enter username',
						success : function(response, newValue)
						{
							//alert("Hello"+newValue);
						},
						display : function(value, sourceData)
						{
							$(this).html(value);
						}
					});

					$('#internaltext').on('save', function(e, params)
					{
						//alert('Saved value: ' + params.newValue + '/' + $('#CustTextModalDataAcnt').val());
						getInternalTextSave('&val='+params.newValue + '&custId=' + $('#CustTextModalDataAcnt').val());
						//params.input.$input.val(parseFloat(params.newValue).toFixed(2));
					});
				}

				if (edtID == 'discnt')
				{
					if (!canEditTerms)
					{
						removeHyperlinkOnDiscounts();
						return false;
					}

					$('.discnt').editable(
					{
						type      : 'text',
						title     : 'Enter username',
						pk        : 1,
						placement : 'top',
						name      : 'discount',
						title     : 'Enter discount %',
						validate  : function(value)
						{
							if ($.trim(value) == '')
							{
								return 'This field is required';
							}

							var regexp = new RegExp('^[0-9]+(\.[0-9]{1,2})?$');
							var regexp2 = new RegExp('^-[0-9]+(\.[0-9]{1,2})?$');

							if (!regexp.test(value) && !regexp2.test(value))
							{
								return 'This value is not valid';
							}
						},
						success   : function(response, newValue)
						{
							//alert("Hello");
						},
						display   : function(value, sourceData)
						{
							value = parseFloat(value).toFixed(2);
							$(this).html(value);
						}
					});

					$('.discnt').on('save', function(e, params)
					{
						var dscID = $(this).attr('id');
						var terms = (dscID.indexOf('termsproduct') != -1) ? 'termsproduct' : 'termsgroup';
						//alert('Saved value: ' + params.newValue + '/' + $('#CustTextModalDataAcnt').val() + '/' + $(this).attr('id'));
						if (terms == 'termsgroup')
						{
							getTermGroup(dscID, params.newValue);
						}
						else
						{
							getTermProduct(this, params.newValue);
						}
						//getInternalTextSave('&val='+params.newValue + '&custId=' + $('#CustTextModalDataAcnt').val());
						//params.input.$input.val(parseFloat(params.newValue).toFixed(2));
					});
				}

				// $(".product-example-table").DataTable(exportFunctions);
				// $(".pro1PAC1-example-table").DataTable(exportFunctions);
				// $(".pro1PAC2-example-table").DataTable(exportFunctions);
				// $(".pro1PAC3-example-table").DataTable(exportFunctions);
				// $(".pro1PAC4-example-table").DataTable(exportFunctions);
			}
		});
	};



	getAjaxSAList = function(did){

		$.ajax({

			url: "products/getSAList/",

			success: function(result){

				$("#"+did).html(result);



			}

		});

	};



	getInternalTextSave =  function(parms){

		$.ajax({

			url: base_url+'customer/saveInternalText/',

			data: parms,

			type : 'POST',

			datatype: 'text',

			success: function(result){



			}

		});

	};



	getTermGroup =  function(did,newVal){

		parms = 'p='+did+'&acID='+$('#CustTextModalDataAcnt').val()+'&newVal='+newVal;

		$.ajax({

			url: base_url+'customer/saveGroupDiscount/',

			data: parms,

			type : 'POST',

			datatype: 'text',

			success: function(result){



			}

		});

	};



	getTermProduct =  function(thisObj,value){

		//alert('Product = '+ did +  '/' + value);

		did = $(thisObj).attr('id');

		var datastring = 'p='+did+'&newVal='+value;

		var baseprice = 0;

		var netprice = 0;

		var discount1 = 0;

		var discount2 = 0;

		//alert($(thisObj).parent().html());

		if (did.indexOf('discount1')!=-1){

			discount1 = value;

			discount2 = $(thisObj).parents('td').next('td').text(); // Get the net price from the link

			var baseprice = $(thisObj).parents('td').prev('td').text(); // Get the base price from the link

		} else {

			discount1 = $(thisObj).parents('td').prev('td').text(); // Get the net price from the link

			discount2 = value;

			var baseprice = $(thisObj).parents('td').prev('td').prev('td').text(); // Get the base price from the link

		}

		var baseprice2 = parseFloat(baseprice) - parseFloat(parseFloat(baseprice)/100 * parseFloat(discount1));

		var netprice = parseFloat(baseprice2) - parseFloat(parseFloat(baseprice2)/100 * parseFloat(discount2));



		if (did.indexOf('discount1')!=-1){

			$(thisObj).parents('td').next('td').next('td').text(parseFloat(netprice).toFixed(2)); // Put the net price

		} else {

			$(thisObj).parents('td').next('td').text(parseFloat(netprice).toFixed(2)); // Put the net price

		}



		datastring += "&netPrice="+parseFloat(netprice).toFixed(2);



		$.ajax({

			url: base_url+'customer/saveProductDiscount/',

			data: datastring,

			type : 'POST',

			datatype: 'text',

			success: function(result){



			}

		});

	};



	/* Function to remove the hyperlink from the edit notes link. */

	function removeHyperlinkOnNote() {

		var text = $("a#internaltext").text();

		if (""==text) {

			text = "Empty";

		}



		$("a#internaltext").after("<span>"+text+"</span>");

		$("a#internaltext").remove();

	}



	/* Function to remove the hyperlink from the discounts. */

	function removeHyperlinkOnDiscounts() {

		$("a.discnt").each(function(value, key) {

			var val = $(this).text();

			$(this).after("<span>"+val+"</span>");

			$(this).remove();

		});

	}



	/* Function to load the products of a customer. */

	function loadCustomerProducts() {

		$.ajax({

			url: base_url+'customer/drawCustomerProductsDetails/'+customer_account+'/'+customer_startthisyearrmonth+'/'+customer_curyearmonth,

			type : 'GET',

			datatype: 'html',

			success: function(result){

				$("#customerproductsdetails").html(result);

				datatablecall();

			}

		});

	}



	function serialize(obj) {

		var out = [];

		for(i=0; i<obj.length;i++) {

			out.push(obj[i]);

		}

		return out;

	}

	/* Function to open the form for adding a target */
	var openAddTargetForm = function() {
		$(".overlay").fadeIn('fast', function() {
			$(".hidden-add-target-form").show();
		});
	}

	var openAdduploadTargetForm = function() {
		$(".overlay").fadeIn('fast', function() {
			$(".hidden-add-uploadtarget-form").show();
		});
	}

	var closeAddTargetForm = function() {
		return $(".hidden-add-target-form").fadeOut('fast', function() {
			$(".overlay").hide();
		}).promise();
	}

	var closeAdduploadTargetForm = function() {
		return $(".hidden-add-uploadtarget-form").fadeOut('fast', function() {
			$(".overlay").hide();
		}).promise();
	}

	var showhelptext = function() {
		document.getElementById("usageofsearch").style.display = "block";
	}

	var hidehelptext = function() {
		document.getElementById("usageofsearch").style.display = "none";
	}

	var selectOption = function(e) {
		let selectedOption = e.target.innerText;
		document.getElementById("id_code").value = selectedOption;
	}

	var selectOptionKey = function(e) {
		if (13==e.keyCode) {
			selectOption(e);
			closeSuggestions();
		}

		if (40==e.keyCode) {
			if (!!e.target.nextElementSibling) {
				e.target.nextElementSibling.focus();
			} else {
				document.querySelector("#searchedcodes ul").firstChild.nextElementSibling.focus();
			}
			
		}

		if (38==e.keyCode) {
			if (!!e.target.previousElementSibling) {
				e.target.previousElementSibling.focus();
			} else {
				document.querySelector("#searchedcodes ul").lastChild.previousElementSibling.focus();
			}
			
		}
	}

	var searchDbKey = function(e) {
		if (13==e.keyCode) {
			e.preventDefault();
			console.log(e.target)
			searchDb();
		}
		return false;
	}

	var searchDb = function() {
		document.getElementById("helptext").style.display = "none";
		document.getElementById("loading_image").style.display = "block";
		let keyword = document.getElementById("id_code").value;
		let id_codetype_val = $("#id_codetype").val();
		if ("P"==id_codetype_val) {
			$.ajax({
				url: base_url+'customer/searchproductcodes/'+keyword,
				type : 'GET',
				datatype: 'html',
				success: function(result){
					document.getElementById("searchedcodes").innerHTML = result;
					document.getElementById("searchedcodes").style.display = "block";
					
				}
			}).promise().done(function(){
				document.getElementById("ui-id-a").focus();
				document.getElementById("helptext").style.display = "block";
				document.getElementById("loading_image").style.display = "none";
			});
		} else {
			document.getElementById("helptext").style.display = "block";
			document.getElementById("loading_image").style.display = "none";
			document.getElementById("helptext").style.display = "none";
		}
		return false;
	}

	var closeSuggestions = function() {
		document.getElementById("searchedcodes").style.display = "none";
	}

	var searchCode = function(evt) {
		if (40==evt.keyCode) {
			document.querySelector("#searchedcodes ul li").focus();
		} else {
			document.getElementById("searchedcodes").style.display = "block";
			let codetype = document.getElementById("id_codetype").value;
			let searchkey = document.getElementById("id_code").value; 
			$.ajax({
				url: base_url+'customer/searchcodes/'+codetype+'/'+searchkey,
				type : 'GET',
				datatype: 'html',
				success: function(result){
					document.getElementById("searchedcodes").innerHTML = result;
				}
			});
		}
	}

	var codeArray = {
		"1": [],
		"2": [],
		"3": [],
		"4": [],
		"P": []
	};

	var enableCodeTypeField = function(id, bol) {
		if (!!bol) {
			document.getElementById(id).value = "";
			$(".code-type-hidable").fadeIn();
		} else {
			$(".code-type-hidable").fadeOut(function(){
				document.getElementById(id).value = "none";
			});
		}
	};

	var executeAutoComplete = function(evt) {
		let data = codeArray[evt.target.value];
		let appendtoId = "searchElem";
		let id = "id_code";
		$("#"+id).val("");
		if ("none"==evt.target.value) {
			document.getElementById("id_code").setAttribute("disabled", true);
			enableCodeTypeField(id, true);
		} else {
			document.getElementById("id_code").removeAttribute("disabled");
			if (0==evt.target.value) {
				enableCodeTypeField(id, false);
			} else {
				enableCodeTypeField(id, true);
			}
			
			
		}
		if ("P"!==evt.target.value) {
			if (0!==evt.target.value) {
				autocompleteExecutor(id, appendtoId, data);
			} else {}
			let removable = document.getElementById("removableMessage");
			if (!!removable) {
				removable.remove();
			}
			document.getElementById("helptext").style.display = "none";
		} else {
			let alertElement = document.createElement("p");
			data = [];
			autocompleteExecutor(id, appendtoId, data);
			alertElement.classList.add("alert");
			alertElement.classList.add("alert-info");
			alertElement.setAttribute("id", "removableMessage");
			alertElement.innerText = "Products list is very large. Please type atleast 2 characters and hit the search icon to search.";
			document.getElementById("placeforAlerts").appendChild(alertElement);
			document.getElementById("helptext").style.display = "block";
		}
		
	}

	var populateCodeArray = function() {
		let searchkey = "";
		$.ajax({
			url: base_url+'customer/searchcodes',
			type : 'GET',
			datatype: 'json',
			success: function(result){
				codeArray = result;
			}
		});
	}

	var TargetFormSubmitted = function(e) {
		let form = e.target;
		var formEl = document.forms.targettocustomersalestargetFrm;
		var formData = new FormData(formEl);
		var dataArray = {
			"codetype" : formData.get('codetype'),
			"code": formData.get('code').split(":")[0],
			"year": formData.get('year'),
			"month": formData.get('month'),
			"salestarget": formData.get('salestarget'),
			"account": formData.get('account')
		};

		var url = base_url+'customer/addCustomerTargetData';
		$.ajax({
			method: "POST",
			url: url,
			data: dataArray,
			success: addDataSuccess
		});
		return false;
	}

	var TargetUploadFormSubmitted = function(obj) {
		try {
			let formEl = document.querySelector("#uploadtargettoproductsalestarget");
			let formData = new FormData(formEl);
			let data = {
				"account": formData.get('account'),
				"targetcsv" : formData.get('targetcsv')
			};
			let url = base_url+'customer/uploadCustomerTargetData';
			fetch(url, {method: "POST", body: formData}).then(response => {
				if (response.status==200) {
					uploadDataSuccess();
				}
			});
			
		} catch(e) {
			console.warn("Err:", e);
		}
		return false;
	}

	var uploadDataSuccess = function() {
		var successMsg = "Targets data for the customer is uploaded successfully! Please wait while the data is updated in the table.";
		closeModal("closeAdduploadTargetForm", function(){
			notify(successMsg);
			$("#targetcsv").val("");
		});
	}

	var addDataSuccess = function(result) {
		var successMsg = "Target data for the customer is added successfully! Please wait while the data is updated in the table.";
		var errorMsg = "Err: Target data for the customer could not be added!";
		closeModal("closeAddTargetForm", function(){
			if ("completed"==result.status) {
				notify(successMsg);
			} else {
				notify(errorMsg);
			}
		});
	}

	var closeModal = function(classSelector, callbackFn) {
		(window[classSelector]()).then(function(){
			callbackFn();
		});
	}
	var notify = function(msg) {
		if (!!document.getElementById("targetDataAddResult")) {
			document.getElementById("targetDataAddResult").remove();
		}
		let pElem = document.createElement("p");
		pElem.setAttribute("id", "targetDataAddResult");
		pElem.classList.add("alert");
		pElem.classList.add("alert-success");
		pElem.innerText = msg;
		document.querySelector("#alertmsg").appendChild(pElem);
		populateCustomerTarget();
	}

	var customerSalesTargetDataTable;

	var populateCustomerTarget = function()
	{
		if (!!customerSalesTargetDataTable)
		{
			customerSalesTargetDataTable.destroy();
		}

		customerSalesTargetDataTable = $("#customeTargetData").DataTable(
		{
			ajax         :
			{
				url     : `${base_url}customer/customerTargetPopulate/${account}`,
				type    : "POST",
				destroy : true,
			},
			initComplete : function(settings, json)
			{
				$("#alertmsg").fadeOut("slow");

				if (!!document.getElementById("targetDataAddResult"))
				{
					document.getElementById("targetDataAddResult").remove();
				}

				adjustTable(this);
			},
			columns      : columns,
		});
	}

	// var table;
	// var populateCustomerContacts = function() {
	// 	if (!!table) {
	// 		table.destroy();
	// 	}
		
	// 	/* Customer contact list table */
	// 	table = $("#customer_contact_table").DataTable({
	// 		"processing": true,
	// 		"serverSide": true,
	// 		"order": [[ 0, "asc" ]],
	// 		"ajax" : {
	// 			url : base_url+"customer/fetchCustomerContacts/"+account,
	// 			type: "post",
	// 			complete: function(){
	// 				$(".dataTables_processing").css("display","none");
	// 				$("#customer_contact_table tr").each(function(k,v){
	// 					classname = $(v).find('a').attr('data-class');
	// 					$(this).addClass(classname);
	// 				});
	// 			},
	// 			error: function(){
	// 				$("#employee_grid_processing").css("display","none");
	// 			}

	// 		},
	// 		dom: 'Bfrtip',
	// 		buttons: [{
	// 			text: '<span title="Export" class="glyphicon glyphicon-export"></span>',
	// 			action: function ( e, dt, node, config ) {
	// 				var ValuesOfSearch = $("#customer_contact_table_filter > label > input").val();
	// 				var urltogo = base_url+'customer/excel_export_contacts/'+account+'/'+ValuesOfSearch;
	// 				console.log("URL Used to export: ",urltogo);
	// 				document.location.href = urltogo;
	// 			}
	// 		}]
	// 	});
	// };

	var adjustTable = function(obj) {
		$("table.dataTable").css({"width":"100%"});
		$(".dataTable th").css({"padding":"10px"});
		$(obj).addClass("table table-bordered table-striped target-list-table target-listing");
	}

	var deletetarget = function(evt) {
		let deleteLink = evt.target.parentNode.parentNode;
		let codetype = deleteLink.getAttribute('data-codetype');
		let id = deleteLink.getAttribute('data-id');
		var url = base_url+'customer/deleteCustomerTargetData/'+codetype+'/'+id;
		let canDelete = confirm("This action will delete the selected sales target data. The data is unrecoverable later.\nPlease cancel this action if you are not sure.");
		if (!canDelete){} else {
			$.ajax({
				method: "delete",
				url: url,
				success: deleteDataSuccess
			});
		}
		
		return false;
	}

	var deleteDataSuccess = function(result) {
		if (!!result.complete) {
			populateCustomerTarget();
		} else {
			console.warn("something went wrong. Could not delete the resource!!");
		}
		
	}

	var closeQery = function() {
		let visible = $("#query_panel").is(":visible");
		if (!visible) {
			$("#query_panel").slideDown("slow");
		} else {
			$("#query_panel").slideUp("slow");
		}
	}

	let OpenEditForm = function(target_id, editField, updated){
		let displayig_id = editField+"_display_"+target_id;
		let editing_id = editField+"_"+target_id;

		if (!isDiplayed(displayig_id)) {
			if (!!updated) {
				console.log("updated", updated, editField);
				if ("yearmonth"!=editField) {
					document.querySelector("#"+displayig_id).innerText = updated.salestarget;
				} else {
					document.querySelector("#"+displayig_id).innerText = updated.yearmonth;
				}
			}
			document.querySelector("#"+displayig_id).classList.remove("hidden");
			document.querySelector("#"+editing_id).classList.add("hidden");
		} else {
			document.querySelector("#"+displayig_id).classList.add("hidden");
			document.querySelector("#"+editing_id).classList.remove("hidden");
		}
	}

	let isDiplayed = function(element_id) {console.log(element_id);
		return !!document.querySelector("#"+element_id).offsetParent;
	}

	var codetypetable = ["customersalestarget", "customerpac1salestarget", "customerpac2salestarget", "customerpac3salestarget", "customerpac4salestarget"];
	let changeSalestarget = function(id, codetype) {
		var newSalesTarget = document.getElementById("salestarget_"+id).value;
		var table = "";
		if ("P"!=codetype) {
			table = codetypetable[codetype];
		} else {
			table = "customerproductsalestarget"
		}
		updateTargetData({"id":id, "table":table, "salestarget":newSalesTarget});
	}

	let changeYearMonth = function(id, codetype) {
		var newYear = document.getElementById("year_"+id).value;
		var newMonth = document.getElementById("month_"+id).value;
		var table = "";
		if ("P"!=codetype) {
			table = codetypetable[codetype];
		} else {
			table = "customerproductsalestarget"
		}
		var newYearMonth = newYear+""+newMonth;
		updateTargetData({"id":id, "table":table, "yearmonth":newYearMonth});
	}

	let updateTargetData = function(data) {
		$.ajax({
			url: base_url+'customer/saveTarget/',
			data: data,
			type : 'POST',
			success: function(result){
				if (result) {
					var editField = Object.keys(data)[2];
					if (editField=="salestarget") {
						editField = editField+"_edit"
					}
					OpenEditForm(data.id, editField, data);
				} else {
					alert("Something went wrong!");
				}
			}
		});
	}

	$(function()
	{
		$("#customer_list_table_filter").after($("#uploadTargetsBulk"));

		$("#customer_repsTab").click(function()
		{
			if (!jQuery.isEmptyObject(crepsTable))
			{
				crepsTable.destroy();
			}

			let params = $(this).data("params");
			let accountId = params.accountId;

			populateCustomerRepsTable(accountId);
		});

		$("#showSalesRep").parent().click(function()
		{
			var user_id = $(this).attr("data-user");

			if (!jQuery.isEmptyObject(crepsTable))
			{
				crepsTable.destroy();
			}

			populateCustomerRepsTable();
		});
	});

	function clickedOnTab(e)
	{
		let targetTab = $(e.target.hash);

		if (targetTab.length > 0)
		{
			if (targetTab.find(".loading-placeholder").length > 0)
			{
				let params = JSON.parse(e.target.dataset.params);

				vik_ajax(targetTab.attr("id"), params);
			}
		}
	}

	function openPopupDetail(contactno) {	
		$("#contactNumber").html("Loading ...");
		$("#contactDetail").html("Loading ...");
		$.ajax({
			url: base_url+'customer/contactdetails/'+contactno,
			type : 'GET',
			success: function(result){
				if (result) {		
					resultHtml = result.split("[ONLYNAME]");
					$("#contactDetail").html(resultHtml[0]);
					$("#contactNumber").html(resultHtml[1]);
				} else {
					console.error("Something went wrong!");
				}
			}
		});
		return false;
	}

	var optionsForModal = {};
	var openModal = function(modalDOMSelector, dataClass) {
		var options = optionsForModal;
		$(modalDOMSelector).modal(options).promise().done(function(){
			afterModalOpened(this, dataClass);
			$("#backToDetails").hide().addClass("hidden");		
		});
		return false;
	}

	var afterModalOpened = function(that, dataClass) {
		$(that).find(".modal-title").text("...");
		$(that).find(".modal-body").html("...");
		$.ajax({
			url: base_url+'tasks/'+dataClass,	
			type : 'GET',
			success: function(form){	
				$(that).find(".modal-title").text(form.title);
				$(that).find(".modal-body").html(form.body);
				getAllAccounts();
				var splitDataClass = dataClass.split("/");
				if ('edit'!=splitDataClass[2]) {
					$("#backToDetails").attr("data-edit", "0");
				} else {
					$("#backToDetails").attr("data-edit", "1");
				}
				$("#backToDetails").attr("data-taskid", splitDataClass[1]);
				autoCompleteBind();
				badge();
			}
		});
	};

	var deleteRecordFromDB = function(taskid) {
		$.ajax({
			url: base_url+'tasks/deleteRecord/'+taskid,
			type : 'delete',
			success: function(response){
				badge();
				if ('success'==response.status) {
					showTaskOperationStatus(statusHidden=false, statusTitle="Success!", statusMessage=response.deleted, statusClass=response.status);
					populateTaskTable(null);
				} else if ('warning'==response.status) {
					showTaskOperationStatus(statusHidden=false, statusTitle="Warning!", statusMessage=response.deleted, statusClass=response.status);
					populateTaskTable(null);
				} else {
					showTaskOperationStatus(statusHidden=false, statusTitle="Error!", statusMessage=response.deleted, statusClass=response.status);
				}	
			}
		});
		return false;
	}

	function taskform(evt){
		evt.stopPropagation();
		var uploadeddoc = "";
		if (!!document.getElementById("documents")) {
			uploadeddoc = document.getElementById("documents").files[0];
		}
		let formData = new FormData();
		var dataObj = document.newTaskForm;
		var inputs = {
			"account" : dataObj.account.value,
			"userid" : dataObj.userid.value,
			"contactno" : dataObj.contactno.value,
			"date" : dataObj.date.value,
			"completed" : dataObj.completed.checked,
			"description" : dataObj.description.value,
			"notes" : dataObj.notes.value,
			"taskid" : dataObj.taskid.value
		};
		formData.append("account", inputs.account);
		formData.append("userid", inputs.userid);
		formData.append("contactno", inputs.contactno);
		formData.append("date", inputs.date);
		formData.append("completed", inputs.completed);
		formData.append("description", inputs.description);
		formData.append("notes", inputs.notes);
		if (""!=uploadeddoc) {
			formData.append("uploadeddoc", uploadeddoc);
		}
		
		formData.append("taskid", inputs.taskid);
		var urltogo = base_url+'tasks/addTask';
		fetch(urltogo, {
			method: "POST", 
			body: formData
		}).then(function (result){   
			console.log(result.body);                               
			if (200==result.status) {
				showTaskOperationStatus(statusHidden=false, statusTitle="Success!", statusMessage="The new data is created successfully.", statusClass="success");
			} else {
				showTaskOperationStatus(statusHidden=false, statusTitle="Error!", statusMessage="Data could not be created.", statusClass="danger");
			}
			$("button.close").trigger("click");
			populateTaskTable(null);
			badge();
		}).then(function (data){
			console.log(data); 
		}); 
		return false;
	}
	/* #tasksTable */

	function uploadform(evt, taskid){
		evt.stopPropagation();
		let uploadeddoc = document.getElementById("documents").files[0];
		let formData = new FormData();
		var dataObj = document.newTaskForm;
		formData.append("uploadeddoc", uploadeddoc);
		formData.append("taskid", taskid);
		var urltogo = base_url+'tasks/addFile';
		fetch(urltogo, {
			method: "POST", 
			body: formData
		}).then(function (result){                                 
			if (200==result.status) {
				goToUploadedFiles("1", taskid);
				var account = getLastUrlSegment();
				populateTaskTable(account);
			}
		}).then(function (data){
			console.log(data); 
		}); 
		return false;
	}

	/* Task table */
	var taskTable;
	var populateTaskTable = function(account)
	{
		/* TODO: UPDATE FUNCTION & REFERENCES TO OMIT PASSING IN 'account' SINCE WHATEVER IS PASSED IN IS NEVER USED! */
		var overdue = $(".task-list-selectors.active").attr("data-overdue");
		var func = "true" != overdue ? 'fetchTasks' : 'fetchOverdueTasks';

		if ($("#accountId").length == 1)
		{
			account = $("#accountId").text();
		}
		else
		{
			var currloc = location.href.split("/");
			var currlocLength = currloc.length;
			var currLocClass = currloc[currlocLength - 2];
			var currAcc = currloc[currlocLength - 1];
			account = "customerDetails" == currLocClass ? currAcc : null;
		}

		$(".dataTables_processing").css("display", "block");

		if (!!taskTable)
		{
			taskTable.destroy();
		}
		
		taskTable = $("#tasksTable").DataTable(
		{
			processing : true,
			serverSide : true,
			order      : [[ 0, "asc" ]],
			ajax       :
			{
				url      : `${base_url}tasks/${func}/${account}`,
				type     : "post",
				complete : function()
				{
					$(".dataTables_processing").css("display", "none");

					$("span.formatted-date").each(function(k, v)
					{
						var dataclass = $(this).attr("data-class");
						$(this).parent().parent().addClass(dataclass);
					});

					col = !account ? 6 : 4;

					taskTable.column(col).visible(func != 'fetchOverdueTasks');
				},
				error    : function(err)
				{
					console.warn("Err:", err);
					$(".dataTables_processing").css("display", "none");
				},
			},
			dom        : 'Bfrtip',
			buttons    :
			[
				{
					text    : '<span title="Export" class="glyphicon glyphicon-export"></span>',
					action  : function(e, dt, node, config)
					{
						var ValuesOfSearch = $("#tasksTable_filter > label > input").val();
						var urltogo = `${base_url}tasks/excel_export_tasks/${account}/${ValuesOfSearch}`;
						console.log("URL Used to export: ", urltogo);
						document.location.href = urltogo;
					},
				},
			],
		});
	}

	var crepsTable= {};

	var populateCustomerRepsTable = function(accountId = null)
	{
		var user_id = null;
		var showSalesRep = $("#showSalesRep");

		if (!jQuery.isEmptyObject(showSalesRep))
		{
			user_id = showSalesRep.attr("data-user");
		}

		var url = undefined != user_id ? `${base_url}users/repcodes/${user_id}` : `${base_url}customer/repcodes/${accountId}`;
		
		crepsTable = $("#crepsTable").DataTable(
		{
			processing : true,
			serverSide : true,
			paging     : false,
			searching  : false,
			info       : false,
			ordering   : false,
			order      : [[ 0, "asc" ]],
			ajax       :
			{
				url      : url,
				type     : "post",
				complete : function()
				{
					$(".dataTables_processing").css("display", "none");
				},
				error    : function(err)
				{
					console.warn("Err:", err);
					$(".dataTables_processing").css("display", "none");
				},
			 },
		});
	}

	var showMore = function(id) {
		var fullDescriptionVisible = $("#desc-"+id+" .full-desc").is(":visible");
		console.log(fullDescriptionVisible);
		if (!fullDescriptionVisible) {
			$("#desc-"+id+" .full-desc").removeClass("hidden").promise().done(function(){
				$("#desc-"+id).next().attr("title", "show short description");
				$("#desc-"+id).next().children(".show-full").html("[show less]");
			});
		} else {
			$("#desc-"+id+" .full-desc").addClass("hidden").promise().done(function(){
				$("#desc-"+id).next().attr("title", "show full description");
				$("#desc-"+id).next().children(".show-full").html("[show more]");
			});
		}
		
	}

	var showTaskOperationStatus = function(statusHidden, statusTitle, statusMessage, statusClass) {
		$("#status").text(statusTitle).promise().done(function(){
			$("#statusMessage").text(statusMessage).promise().done(function(){
				
				$("#statusAndMessage").removeClass("alert-primary").removeClass("alert-secondary").removeClass("alert-success").removeClass("alert-danger").removeClass("alert-warning").removeClass("alert-info").removeClass("alert-light").removeClass("alert-dark");

				if (!!statusClass) {
					$("#statusAndMessage").addClass("alert-"+statusClass);
				}

				if (!statusHidden) {
					$("#statusAndMessage").removeClass("hidden");
				} else {
					$("#statusAndMessage").addClass("hidden");
				}
			});
		});
	};

	function addTask(taskid) {
		openModal('#addTaskModal', 'newTask/'+taskid);
		setTimeout(function() {
			fetchContacts();
		  }, 500);
	}

	var readRecord = function(taskid, evt) {
		evt.stopPropagation();
		evt.preventDefault();
		openModal('#addTaskModal', 'viewTask/'+taskid+"/onlyview");
		return false;
	};

	var updateRecord = function(taskid, evt) {
		evt.stopPropagation();
		evt.preventDefault();
		openModal('#addTaskModal', 'viewTask/'+taskid+"/edit");
		return false;
	};

	var deleteRecord = function(taskid, evt) {
		evt.stopPropagation();
		evt.preventDefault();
		var confirmDelete = confirm("Are you sure to delete the task #"+taskid+"?");
		if (!confirmDelete) {

		} else {
			deleteRecordFromDB(taskid);
		}
		
		return false;
	};

	var closemessage = function() {
		$("#status").text("...").promise().done(function(){
			$("#statusMessage").text("...").promise().done(function(){
				$("#statusAndMessage").addClass("hidden").removeClass("alert-success").removeClass("alert-danger");
			});
		});
	}

	var checkthecheckbox = function(status){
		var delay = 10;
		if (status) {
			setTimeout(function(){
				$("#completed").attr("checked");
			}, delay);
			console.log("checked");
		} else {
			setTimeout(function(){
				$("#completed").removeAttr("checked");
			}, delay);
			console.log("removed checked");
		}
	}

	var onActivate = function(evt, account, full) {
	//	createTableHeader(full);
		var delay = 10;
		setTimeout(function(){
			if (!account) {
				populateTaskTable(null);
			} else {
				populateTaskTable(account);
			}
		}, delay);
	}

	var deleteFile = function(filepath, filename, id, evt) {
		evt.stopPropagation();
		var confirmation = confirm("You are processing to delete the file "+filename+"\nClick \"OK\" to proceed or \"Cancel\" to avoid deleting the file.");
		if (confirmation) {
			$.ajax({
				url: base_url+'tasks/deletefile/'+filepath+"/"+filename,
				type : 'delete',
				success: function(response){
					if ('success'==response.status) {
						$("#"+id).children().fadeOut("slow", function(){
							$(this).remove().promise().done(function(){
								$("#"+id).html("<div class='alert alert-danger'>"+response.deleted+"</div>");
								var account = getLastUrlSegment();
								populateTaskTable(account);
							});
						})
					} 
				}
			});
		}	
		return false;
	}
	/*
	$(document).on("click", "button.delete-file-button", function(evt){
		console.log("enent", evt);
		evt.stopPropagation();
		evt.preventDefault();
		var filepath = $(this).attr("data-task");
		var filename = $(this).attr("data-doc");
		var id = $(this).attr("data-li-id");
		return deleteFile(filepath, filename, id, evt);
	});*/

	var deleteConfirmation = function(taskid, doc, id, evt){
		return deleteFile(taskid, doc, id, evt);
	}

	var goToUploadedFiles = function(edit, taskid){
		$("#myLargeModalLabel").html("");
		$("#addTaskModal div.modal-body").html("...");
		$("#backToDetails").removeClass("hidden").fadeIn();
		var isEditing = "0";
		if (!edit) {
			$("#backToDetails").attr("data-edit", "0");
			isEditing = "0";
		} else {
			$("#backToDetails").attr("data-edit", "1");
			isEditing = "1";
		}
		$("#backToDetails").attr("data-taskid", taskid);
		$.ajax({
			url: base_url+'tasks/uploadedDocumentsList/'+taskid+'/'+isEditing,	
			type : 'GET',
			success: function(list){	
				$("#addTaskModal").find(".modal-body").html(list.body);
			}
		});
	}

	var openAttachments = function(taskid, event) {
		var options = optionsForModal;
		$('#addTaskModal').modal(options).promise().done(function(){
			goToUploadedFiles(1, taskid);	
		});
		return false;
	};

	var hidePreviousBtn = function() {
		var taskid = $("#backToDetails").attr("data-taskid");
		var edit = $("#backToDetails").attr("data-edit");
		var isEdit = '1'!=edit?"onlyview":"edit";
		var dataClass = 'viewTask/'+taskid+'/'+isEdit;
		$("#addTaskModal div.modal-body").html("...");
		$("#backToDetails").fadeOut(function(){
			$(this).addClass("hidden");
		});
		afterModalOpened($('#addTaskModal'), dataClass);
	}

	var createTableHeader = function(full){
		var td = {"taskid":null, "account":null, "name":null, "date":null, "complete":null, "description":null, "actions":null};
		td.taskid = createTD("Task ID");
		td.account = createTD("Account");
		td.name = createTD("Name");
		td.date = createTD("Date");
		td.complete = createTD("Complete");
		td.description = createTD("Description");
		td.actions = createTD("Actions", "100");
		var tr = createTRRow(full, td);
		$(".table_head").html(tr);
	}

	var createTD = function(text, width) {
		var tdElem = document.createElement("td");
		tdElem.innerText = text;
		if (!!width) {
			tdElem.width = width;
		}
		return tdElem;
	}

	var createTRRow = function(full, td) {
		var tr = document.createElement("tr");
		for(index in td) {
			if (!full) {
				if ("complete"!=index) {
					tr.append(td[index]);
				}
			} else {
				tr.append(td[index]);
			}
		}
		return tr;
	}

	var stateChange = function(taskid, account) {
		var buttonElem = $("#chkbx_"+taskid);
		var completed = buttonElem.attr("data-iscomplete");
		var complete = ("true"!=completed);
		var action = complete?"mark the task as completed":"mark the task as incomplete";
		var confirmation = confirm("Press OK to "+action);
		if (!confirmation) {
			return false;
		}
		$.ajax({
			url: base_url+'tasks/completed/',
			type : 'post',
			data: {"taskid":taskid, "complete":complete},
			success: function(response){
				badge();
				populateTaskTable(account);
			}
		});
		return false;
	}

	var getLastUrlSegment = function()
	{
		var urlArray1 = (window.location.href).replace("https://", "").replace("http://", "").split("/");
		var account = urlArray1[urlArray1.length - 1];
		var regExpNum = /[0-9]+/;

		if (regExpNum.test(account))
		{
		}
		else
		{
			account = null;
		}

		return account;
	}

	var allAccountsObject = {};
	var getAllAccounts = function () {	
		$.ajax({
			url: base_url+'tasks/allAccounts/',
			type : 'GET',
			success: function(result){
				allAccountsObject = result;
				autoCompleteBind();
			}
		});
		return false;
	}

	var fetchContacts = function() {
		$.ajax({
			url: base_url+'tasks/fetchContacts/'+$("#account").val()+"/"+$("#contactno").attr("data-value"),
			type : 'GET',
			success: function(result){
				$("#contactno").remove();
				var splittedResult = result.split("|");
				if (splittedResult[1]!="0") {
					var selectBox = document.createElement("select");
					selectBox.classList.add("form-control");
					selectBox.id = "contactno";
					selectBox.name = "contactno";
					selectBox.innerHTML = splittedResult[0];
					$("[for='contactno']").after(selectBox);
				} else {
					var textBox = document.createElement("input");
					textBox.classList.add("form-control");
					textBox.id = "contactno";
					textBox.name = "contactno";
					$("[for='contactno']").after(textBox);
				}
				$("#contactno_warning").remove().promise().done(function(){
					$("#contactno").removeAttr("disabled");
				});
			}
		});
		return false;
	}

	var autoCompleteBind = function(callbackFn) {
		$("#account_selector").keyup(function(evt){
			if (!(evt.keyCode==37 || evt.keyCode==38 || evt.keyCode==39 || evt.keyCode==40 || evt.keyCode==8 || evt.keyCode==13 || evt.keyCode==9)) {
				$("#account").val($(this).val());
			}
			if (evt.keyCode==8) {
				var allVal = $(this).val().split("-")[0].replace("(", "").replace(")", "");
				$("#account").val(allVal.trim());
			}
		});
		$("#account_selector").blur(function(){
			$("#contactno_warning").remove()
			$("#contactno").attr("disabled", true);
			$("#contactno").before("<span class='bg-warning' id='contactno_warning'><i class='glyphicon glyphicon-exclamation-sign'></i>Please wait while the contacts for the selected account is being fetched from the database.</span>");
			fetchContacts();
		});
		try{
			if (!jQuery.isEmptyObject($("#account_selector"))) {
				$("#account_selector").autocomplete({
					minLength:3,
					source: allAccountsObject,
					appendTo: "#Suggestions",
					focus: function( event, ui ) {
						$( "#account_selector" ).val( ui.item.label );
						$( "#account" ).val( ui.item.value );
						return false;
					},
					select: function( event, ui ) {
						$( "#account_selector" ).val( ui.item.label );
						$( "#account" ).val( ui.item.value );
						return false;
					}
				});
				console.log("autocomplete is bound successfully with the account field now!");
				$("#account_selector").attr("disabled", false);
			}
		} catch(e) {
			console.warn(e);
		}
	  }

	  var deleteCustomerRep = function(evt)
	  {
		confirmation = confirm(`You are about to delete repcode ${$(evt.target).attr("data-repcode")}`);

		console.log(account);

		var url = 0 == account ? `${base_url}customer/deleteurep/${$(evt.target).attr("data-repcode")}/${$("#showSalesRep").attr("data-user")}` : `${base_url}customer/deleterep/${$(evt.target).attr("data-repcode")}/${$(evt.target).attr("data-account")}`;

		if (confirmation)
		{
			$.ajax(
			{
				url     : url,
				type    : 'delete',
				success : function(result)
				{
					crepsTable.destroy();
					populateCustomerRepsTable(account);
				},
			});
		}
	}

	var customerRepform = function(evt)
	{
		evt.preventDefault();
		evt.stopPropagation();
		var account = $(evt.target).find("#account").val();
		var repcodeElem = $(evt.target).find("#repcode");
		var repcode = repcodeElem.val();
		var repuserid = 0;

		if (0 == account)
		{
			repuserid = $("#showSalesRep").attr("data-user");
		}

		$("#repcodeError").remove();

		if ("" == $.trim(repcode))
		{
			var errorSpanElem = errorSpan("repcodeError", "<i class='fa fa-fw fa-exclamation' aria-label='error'></i> The repcode field can not be blank");
			repcodeElem.after(errorSpanElem);
		}
		else
		{
			if (0 == account)
			{
				addUserRepcode(repcode, repuserid, repcodeElem);
			}
			else
			{
				addRepcode(repcode, account, repcodeElem);
			}
		}
		
		return false;
	}

	var errorSpan = function(id, message) {
		var spanElem = document.createElement("span");
		spanElem.id = id;
	//	spanElem.classList.add("alert");
	//	spanElem.classList.add("alert-danger");
		spanElem.innerHTML = message;
		return spanElem;
	}

	var addRepcode = function(repcode, account, repcodeElem)
	{
		$.ajax(
		{
			url     : `${base_url}tasks/addCustomerRepcode/`,
			type    : 'post',
			data    :
			{
				repcode : repcode,
				account : account,
			},
			success : function(result)
			{
				if (result.success)
				{
					$("#addRepModal").modal("toggle");
					crepsTable.destroy();
					populateCustomerRepsTable(account);
				}
				else
				{
					var errorSpanElem = errorSpan("repcodeError", "<i class='fa fa-fw fa-exclamation' aria-label='error'></i> "+result.message);
					repcodeElem.after(errorSpanElem);
				}
			},
		});
	};

	var addUserRepcode = function(repcode, userid, repcodeElem) {
		$.ajax({
			url: base_url+'tasks/addUserRepcode/',
			type : 'post',
			data: {"repcode":repcode, "userid":userid},
			success: function(result){
				if (result.success) {
					$("#addRepModal").modal("toggle");
					crepsTable.destroy();
					populateCustomerRepsTable();
				} else {
					var errorSpanElem = errorSpan("repcodeError", "<i class='fa fa-fw fa-exclamation' aria-label='error'></i> "+result.message);
					repcodeElem.after(errorSpanElem);
				}
			}
		});
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
				totals  : totals,
				columns : columns,
				search  : { value : search },
			},
			success : function(result)
			{
				if (result.success)
				{
					hasTotal(result.totals);
				}
				else
				{
					var errorSpanElem = errorSpan("repcodeError", "<i class='fa fa-fw fa-exclamation' aria-label='error'></i> "+result.message);
					repcodeElem.after(errorSpanElem);
				}
			}
		});
	};

	var hasTotal = function(totals)
	{
		$("td.hastotal").each(function(key, elem)
		{
			var keyName = $(elem).attr("data-value").replace(/-/g, "_");
			$(elem).html(parseFloat(totals[keyName]).toFixed(2));
		});
	};
