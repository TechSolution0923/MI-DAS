<?php 
	/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
	$canSeeMargins = canSeeMargins();
	$canEditNotes = canEditNotes();
	$canEditTerms = canEditTerms();
?>
<style>
	.widthlow
	{
		width : 100%;
	}

	.overlay
	{
		width: 100%;
		height: 100%;
		background-color: rgba(0,0,0,0.50);
		position: fixed;
		z-index: 1030;
		top: 0;
		display: none;
	}

	.hidden-add-target-form
	{
		width: 50%;
		height: 190px;
		background-color: #fff;
		position: fixed;
		z-index: 1031;
		top: 30%;
		left: 25%;
		display: none;
	}

	.hidden-add-uploadtarget-form
	{
		width: 50%;
		height: 190px;
		background-color: #fff;
		position: fixed;
		z-index: 1031;
		top: 30%;
		left: 25%;
		display: none;
	}

	.orientation-custom-css
	{
		position: absolute !important;
		top: 10px;
		right: 20px;
		cursor: pointer;
	}

	#usageofsearch
	{
		position: absolute;
		padding: 5px;
		border: 1px solid #4dc0ef;
		background: #fff;
		z-index: 2;
		right: 35px;
		top: 15px;
		display: none;
	}

	#searchedcodes
	{
		position: absolute;
		border: 1px solid #caced0;
		background: #fff;
		z-index: 2;
		display: block;
		width: 88%;
		outline: 0px;
	}

	#searchedcodes ul
	{
		list-style: none;
		padding-left: 12px;
	}

	#searchedcodes ul li:hover
	{
/*		font-weight:bold;
		font-style:italic;
		cursor:pointer;*/
	}

	#ui-id-1
	{
		list-style: none;
		position: absolute;
		top: 34px;
		z-index: 99;
		background: #4dc0ef;
		left: 15px;
		height: 120px;
		overflow-y: scroll;
		width: 213px;
	}

	#ui-id-a
	{
		list-style: none;
		position: absolute;
		z-index: 99;
		background: #4dc0ef;
		height: 120px;
		overflow-y: scroll;
		width: 213px;
		outline: 0px;
	}

	#helptext
	{
		border: 0;
		display: none;
	}

	#helptext:focus
	{
		outline: 1px dotted #777;
	}

	.noborder:focus
	{
		outline: none;
	}

	#loading_image
	{
		display: none;
	}

	#customersales
	{
		display: block;
	}

	#customercontacts
	{
		display: none;
	}
</style>
<!-- Content Header (Page header) -->
<?= form_open('customer/index'); ?>
<!-- Main content -->
<section class="">
	<div class="">
		<div class="">
			<div style="float: none; height: 1em;"></div>
			<div class="">
				<div class="box-body" id="customersales">
					<table class="table table-bordered table-striped" id="customer_contact_table">
						<thead>
							<tr>
								<th>Contact number</th>
								<th>Customer name</th>
								<th>Contact type</th>
								<th>Job title</th>
								<th>Sensitive Contact</th>
								<th>Do not communicate</th>
								<th>Phone 1 description</th>
								<th>Phone 1 number</th>
								<th>Phone 2 description</th>
								<th>Phone 2 number</th>
								<th>Email 1 description</th>
								<th>Email address 1</th>
								<th>Email 2 description</th>
								<th>Email address 2</th>
								<th></th>
							</tr>
						</thead>

						<tbody>
						</tbody>

						<tfoot>
							<tr>
								<th>Contact number</th>
								<th>Customer name</th>
								<th>Contact type</th>
								<th>Job title</th>
								<th>Sensitive Contact</th>
								<th>Do not communicate</th>
								<th>Phone 1 description</th>
								<th>Phone 1 number</th>
								<th>Phone 2 description</th>
								<th>Phone 2 number</th>
								<th>Email 1 description</th>
								<th>Email address 1</th>
								<th>Email 2 description</th>
								<th>Email address 2</th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<?= form_close(); ?>

<!-- Modal -->
<div id="CustomerDetail" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h1 class="modal-title"><span id="contactNumber"><< Contact name loading ...>></span></h1>
			</div>
			<div class="modal-body">
				<div id="contactDetail">Loading ...</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function()
	{
		var table;
		var populateCustomerContacts = function()
		{
			if (!!table)
			{
				table.destroy();
			}
			
			/* Customer contact list table */
			table = $("#customer_contact_table").DataTable(
			{
				"processing" : true,
				"serverSide" : true,
				"order"      : [[0, "asc"]],
				"ajax"       :
				{
					url      : base_url+"customer/fetchCustomerContacts/"+account,
					type     : "post",
					complete : function()
					{
						$(".dataTables_processing").css("display","none");
						$("#customer_contact_table tr").each(function(k, v)
						{
							classname = $(v).find('a').attr('data-class');
							$(this).addClass(classname);
						});
					},
					error    : function()
					{
						$("#employee_grid_processing").css("display","none");
					},
				},
				dom          : 'Bfrtip',
				buttons      :
				[
					{
						text   : '<span title="Export" class="glyphicon glyphicon-export"></span>',
						action : function (e, dt, node, config)
						{
							var ValuesOfSearch = $("#customer_contact_table_filter > label > input").val();
							var urltogo = base_url+'customer/excel_export_contacts/'+account+'/'+ValuesOfSearch;
							console.log("URL Used to export: ", urltogo);
							document.location.href = urltogo;
						},
					},
				]
			});
		};

		populateCustomerContacts();
	});
</script>
