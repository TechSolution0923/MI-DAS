<?php 
  /* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
  $canSeeMargins = canSeeMargins();
  $canEditNotes = canEditNotes();
  $canEditTerms = canEditTerms();
  ?>
<style>
  .widthlow {
  width : 100%;
  }
</style>

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

.ui-tooltip
{
    position: absolute;
    top: 34px;
    z-index: 99;
    background: #4dc0ef;
    left: 15px;
    height: 60px;
    overflow-y: auto;
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

#customersales {
  display: block;
}

#customercontacts {
  display: none;
}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1> Customers  </h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Customers</li>
  </ol>
</section>
<?php echo form_open('customer/index'); ?>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      
      <div style="float:none; height:1em"></div>
      <div class="box">
      <div class="box-body" id="customersales">
        <!--
			    Account, Name, Sales YTD, Sales Last Year (2018), Diff % (from step 2), Sales 2017, Sales MTD, GM% MTD (if visible), GM% YTD (if visible), Post Code and User Def. 1? I’m taking the Qty MTD and Qty YTD columns off the list
			  -->
					<button type="button" id="uploadTargetsBulk" class="btn btn-success pull-right" onclick="openAdduploadTargetForm();"><i class="fa fa-fw fa-calendar-plus-o"></i> Upload target(s)</button>
          <table class="table table-bordered table-striped" id="customer_list_table">
            <thead>
              <tr>
                <th>Account</th>
                <th>Name</th>
                <th>Sales YTD</th>
                <th>Sales <?php $curryear= date('Y');echo $curryear-1; ?> YTD</th>
                <th>Sales <?php $curryear= date('Y');echo $curryear-1; ?></th>
                <th>Diff %</th>
                <th>Sales <?php echo $curryear-2;?> YTD</th>
                <th>Sales <?php echo $curryear-2;?></th>
                <th>Sales MTD</th>
                <?php if($canSeeMargins) { ?>
                <th>GM% MTD</th>
                <?php } ?>
                <?php if($canSeeMargins) { ?>
                <th>GM% YTD</th>
                <?php } ?>
                <th>Post Code</th>
                <th>User Def. 1</th>
                <th>Rep. Code</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th>Account</th>
                <th>Name</th>
                <th>Sales YTD</th>
                <th>Sales <?php $curryear= date('Y');echo $curryear-1; ?> YTD</th>
                <th>Sales <?php $curryear= date('Y');echo $curryear-1; ?></th>
                <th>Diff %</th>
                <th>Sales <?php echo $curryear-2;?> YTD</th>
                <th>Sales <?php echo $curryear-2;?></th>
                <th>Sales MTD</th>
                <?php if($canSeeMargins) { ?>
                <th>GM% MTD</th>
                <?php } ?>
                <?php if($canSeeMargins) { ?>
                <th>GM% YTD</th>
                <?php } ?>
                <th>Post Code</th>
                <th>User Def. 1</th>
                <th>Rep. Code</th>
              </tr>
              <tr style="background-color: #e1e1e1;font-style: italic;font-weight: bold;color: black;" class="total-row">
                <td class="nototal">Account</td>
                <td class="nototal">Name</td>
                <td class="hastotal" data-value="sales-ytd">Sales YTD</td>
                <td class="hastotal" data-value="sales-lastyear">Sales <?php $curryear= date('Y');echo $curryear-1; ?> YTD</td>
                <td class="hastotal" data-value="ysales1">Sales <?php $curryear= date('Y');echo $curryear-1; ?></td>
                <td class="hastotal" data-value="diff">Diff %</td>
                <td class="hastotal" data-value="sales-last-to-lastyear">Sales <?php echo $curryear-2;?> YTD</td>
                <td class="hastotal" data-value="ysales2">Sales <?php echo $curryear-2;?></td>
                <td class="hastotal" data-value="sales-mtd">Sales MTD</td>
                <?php if($canSeeMargins) { ?>
                <td class="hastotal" data-value="total_gm_mtd">GM% MTD</td>
                <?php } ?>
                <?php if($canSeeMargins) { ?>
                <td class="hastotal" data-value="total_gm_ytd">GM% YTD</td>
                <?php } ?>
                <td class="nototal">Post Code</td>
                <td class="nototal">User Def. 1</td>
                <td class="nototal">Rep. Code</td>
              </tr>
            </tfoot>
          </table>
        </div>
        <!-- /.box-body -->
      </div>


      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>
<!-- /.content -->
<?php echo form_close(); ?>



<section class="hidden-add-uploadtarget-form" style="top:20%;">
	<div class="box box-info">
		<div class="box-header with-border">
			<h3 class="box-title">Upload target(s)</h3>
		</div>
		<!-- /.box-header -->
		<!-- form start -->
		<?php echo form_open_multipart('customer/uploadtargettoproductsalestarget', array("class"=>"form-horizontal", "onSubmit"=>"return TargetUploadFormSubmitted(this);", "id"=>"uploadtargettoproductsalestarget", "enctype"=>"multipart/form-data"));?>
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
        <input type="hidden" name="account" id="account" value="<?php echo $account; ?>" aria-hidden="true" tabindex="-1" />
        <!-- / Hidden elements -->

		<?php echo form_close();?>
	</div>
</section>

