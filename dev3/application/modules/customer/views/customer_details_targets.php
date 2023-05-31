<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-body">
				<span id="alertmsg"><?= $this->session->flashdata('target_operation'); ?></span>
				<div class="box-footer no-border">
<?php
					if ($mainUserEditAccess == '1')
					{
?>
						<button type="button" class="btn btn-success pull-left" onclick="openAddTargetForm();"><i class="fa fa-fw fa-calendar-plus-o"></i> Add target</button>
<?php
					}
?>
				</div>
				<table class="" id="customeTargetData">
					<thead>
						<tr>
							<th>Code type</th>
							<th>Code</th>
							<th>Year/Month</th>
							<th>Target</th>
<?php
							if ($mainUserEditAccess == '1')
							{
?>
								<th>Delete</th>
<?php
							}
?>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Code type</th>
							<th>Code</th>
							<th>Year/Month</th>
							<th>Target</th>
<?php
							if ($mainUserEditAccess == '1')
							{
?>
								<th>Delete</th>
<?php
							}
?>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>

<style>
.overlay {
	width: 100%;
	height: 100%;
	background-color: rgba(0,0,0,0.50);
	position: fixed;
	z-index: 1030;
	top: 0;
	display:none;
}

.hidden-add-target-form {
	width: 50%;
	height:190px;
	background-color: #fff;
	position: fixed;
	z-index: 1031;
	top: 30%;
	left: 25%;
	display:none;
}
.hidden-add-uploadtarget-form {
	width: 50%;
	height:190px;
	background-color: #fff;
	position: fixed;
	z-index: 1031;
	top: 30%;
	left: 25%;
	display:none;
}

.orientation-custom-css {
	position: absolute !important;
	top: 10px;
	right: 20px;
	cursor:pointer;
}

#usageofsearch {
	position: absolute;
	padding: 5px;
	border: 1px solid #4dc0ef;
	background: #fff;
	z-index: 2;
	right: 35px;
	top: 15px;
	display: none;
}

#searchedcodes {
	position: absolute;
	border: 1px solid #caced0;
	background: #fff;
	z-index: 2;
	display: block;
	width: 88%;
	outline: 0px;
}

#searchedcodes ul {
	list-style: none;
	padding-left: 12px;
}

#searchedcodes ul li:hover{/*
	font-weight:bold;
	font-style:italic;
	cursor:pointer;*/
}

#ui-id-1 {
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

#ui-id-a {
	list-style: none;
	position: absolute;
	z-index: 99;
	background: #4dc0ef;
	height: 120px;
	overflow-y: scroll;
	width: 213px;
	outline:0px;
}

#helptext {
	border:0;
	display:none;
}

#helptext:focus {
	outline: 1px dotted #777;
}

.noborder:focus {
	outline: none;
}

#loading_image {display:none;}
</style>
<!-- Hidden form to add new target -->
<section class="hidden-add-target-form" onClick="closeSuggestions();">
	<div class="box box-info">
		<div class="box-header with-border">
			<h3 class="box-title">Add new target for <?= $customername; ?></h3>
		</div>
		<!-- /.box-header -->
		<!-- form start -->
		<?= form_open('customer/addtargettocustomersalestarget', array("class"=>"form-horizontal", "id"=>"targettocustomersalestargetFrm", "onSubmit"=>"return TargetFormSubmitted(event);"));?>
		<div class="box-body">
			<p id="placeforAlerts"></p>
			<div class="form-group">
				<label for="codetype" class="col-sm-2 control-label">Code Type</label>
				<div class="col-sm-4">
					<select class="form-control" name="codetype" id="id_codetype" onChange="executeAutoComplete(event);">
						<option selected disabled value="none">-- Select code type --</option>
						<option value="0">0</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="P">P</option>
					</select>
				</div>
				<label for="code" class="col-sm-2 control-label  code-type-hidable">Code</label>
				<div class="col-sm-4  code-type-hidable" id="searchElem">
					<input type="text" class="form-control" id="id_code" name="code" placeholder="Eg. P01 or 500009 etc." maxlength="20" required disabled>
					<button class="glyphicon glyphicon-search orientation-custom-css" id="helptext" aria-label="search help" onClick="return searchDb();" onkeydown="return searchDbKey(event);"></button>
					<img src="../../public/images/scp-ajax-loader.gif" id="loading_image" class="orientation-custom-css"/>
					<div id="searchedcodes" aria-atomic="true" aria-live="polite" onClick="selectOption(event);" onkeydown="selectOptionKey(event);"></div>
				</div>
				<br><br>
				<label for="year" class="col-sm-2 control-label">Year</label>
				<div class="col-sm-4">
					<input type="number" min="<?= date('Y');?>" class="form-control" id="year"name="year" placeholder="Year" value="<?= date("Y"); ?>" required>
				</div>
				<label for="month" class="col-sm-2 control-label">Month</label>
				<div class="col-sm-4">
					<input type="number" min="1" max="12" class="form-control" id="month" name="month" value="<?= date("m"); ?>" placeholder="Month" required>
				</div>
			</div>
			<div class="form-group">
				<label for="target" class="col-sm-2 control-label">Target</label>
				<div class="col-sm-10">
					<input type="number" min="0" class="form-control" id="target" name="salestarget" placeholder="target" required>
					<input type="hidden" name="product_code" value="<?= $prodcode;?>">
					<input type="hidden" name="page_code" value="<?= $page;?>">
				</div>
			</div>
		</div>
		<!-- /.box-body -->
		<div class="box-footer">
			<button type="button" class="btn btn-default" onclick="closeAddTargetForm();">Cancel</button>
			<button type="submit" class="btn btn-info pull-right">Save</button>
		</div>
		<!-- /.box-footer -->
		
		<!-- Hidden elements -->
		<input type="hidden" name="account" value="<?= $account; ?>" aria-hidden="true" tabindex="-1" />
		<!-- / Hidden elements -->
		<?= form_close();?>
	</div>
</section>

<section class="hidden-add-uploadtarget-form" style="top:20%;">
	<div class="box box-info">
		<div class="box-header with-border">
			<h3 class="box-title">Upload target(s)</h3>
		</div>
		<!-- /.box-header -->
		<!-- form start -->
		<?= form_open_multipart('customer/uploadtargettoproductsalestarget', array("class"=>"form-horizontal", "onSubmit"=>"return TargetUploadFormSubmitted(this);", "id"=>"uploadtargettoproductsalestarget", "enctype"=>"multipart/form-data"));?>
		<div class="box-body">
			<div class="form-group">
				<label for="month" class="col-sm-2 control-label">Upload CSV</label>
				<div class="col-sm-4">
					<input type="file" name="targetcsv" id="targetcsv" class="form-control" required />
				</div>
				<div class="col-sm-12">
				</div>
			</div>
		</div>
		<!-- /.box-body -->
		<div class="box-footer">
			<button type="button" class="btn btn-default" onclick="closeAdduploadTargetForm();">Cancel</button>
			<button type="submit" class="btn btn-info pull-right">Upload</button>
			<hr>
			<h5>CSV File format:</h5>
			<h5>Account Code, Code Level, Code, Year Month, Sales Target</h5>
			<hr style="margin-top: 5px; margin-bottom: 5px;">
			<div class="row">
				<div class="col-sm-2">
					<h5><strong>Account Code</strong></h5>
					<h5><strong>Code Level</strong></h5>
					<h5><strong>Code</strong></h5>
					<h5><strong>Year Month</strong></h5>
					<h5><strong>Sales Target</strong></h5>
				</div>
				<div class="col-sm-10">
					<h5>Customer account code</h5>
					<h5>0 for customer, (1-4) for the PAC level or P for product</h5>
					<h5>PAC or Product Code</h5>
					<h5>Year and month</h5>
					<h5>Sales target</h5>
				</div>
			</div>
			<hr style="margin-top: 5px; margin-bottom: 5px;">
			<h5>e.g. A0001, 2, P01, 201805, 12000</h5>
			<h5>* Enter data after heading</h5>
		</div>
		<!-- /.box-footer -->
		
		<!-- Hidden elements -->
		<input type="hidden" name="account" id="account" value="<?= $account; ?>" aria-hidden="true" tabindex="-1" />
		<!-- / Hidden elements -->

		<?= form_close();?>
	</div>
</section>

<script type="text/javascript">
	$("select#id_codetype").val("0");
	$("select#id_codetype").change();
</script>