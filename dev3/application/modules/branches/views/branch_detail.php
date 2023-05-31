<!-- Content Header (Page header) -->
<section class="content-header">
  <h4>
	<?php 
	if(isset($branchDetail['name'])) {
		echo "Edit - ".$branchDetail['name'];
	} else {
		echo "Add new branch";
	}
	
	$disabled = "";
	if("view"!=$type) {		
	} else {
		$disabled = "disabled";
	}
	
	
	?>
 
  </h4>
  <ol class="breadcrumb">
	<li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
	<li><a href="<?php echo base_url(); ?>branches"><i class="fa fa-users"></i> Branches</a></li>
	<li class="active"><?php echo isset($branchDetail['branch'])? $branchDetail['branch'].' - '.$branchDetail['name']:"Add new branch";?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"  class="active"><a href="#details" role="tab" data-toggle="tab" aria-expanded="false">Details</a></li>
			<li role="presentation" class="" id="showiftarget"><a href="#target" role="tab" data-toggle="tab" aria-expanded="false">Targets</a></li>	
			<li role="presentation" class="" id="showifkpi"><a href="#kpi" role="tab" data-toggle="tab" aria-expanded="false">KPI</a></li>		
		</ul>
		<div class="tab-content">

			<!-- Branch First tab start here -->
		  <div class="active tab-pane" id="details">
				<div class="row no-border">
					<?php if("view"==$type) { ?>
						<?php echo form_open('branches/details');?>
						<div class="box-footer">
							<input type="hidden" name="branch" value="<?php echo $branchDetail['branch'];?>" />
							<input type="submit" name="edit" value="Edit" class="btn btn-default" />
							<?php echo anchor('branches/index', '<button type="button" class="btn btn-danger" name="cancel" value="cancel">Cancel</button>');?>
						<?php echo form_close();?>
						</div>
					<?php } ?>
				</div>
				<div class="row">
					
					<?php echo form_open('branches/details', array("role"=>"form", "id"=>"branchFrm"));?>
						<div class="col-xs-8">
							<div class="box-header">
								<?php if("view"!=$type) { ?>
								<button type="submit" class="btn btn-default" name="submit" value="formsubmitted">
									<?php 
									$addnew = false;
									echo "Save";
									if(isset($branchDetail['branch'])) {
										$addnew = false;
									} else {
										$addnew = true;
									}?>
								</button>
								<?php echo anchor('branches/index', '<button type="button" class="btn btn-danger" name="cancel" value="cancel">Cancel</button>');?>
								
								<?php } ?>
							</div>
							
							<div class="form-group">
								<label>Branch</label>
								<?php if(!$ShowBranchIdField) {?>
									<input type="hidden" name="operation" value="edit" />
									<input type="hidden" name="branch" id="branch" value="<?php echo isset($branchDetail['branch'])?$branchDetail['branch']:"";?>">
									<div class="input"><?php echo isset($branchDetail['branch'])?$branchDetail['branch']:"";?></div>
								<?php } else {?>
									<input type="hidden" name="operation" value="add" />
									<input type="text" class="form-control" placeholder="Branch Id" name="branch" id="branch" value="<?php echo isset($branchDetail['branch'])?$branchDetail['branch']:"";?>" required>
									<p class="alert alert-danger fail" style="display:none; padding:1px; margin:0; margin-top:2px;"><strong>OOps! </strong>This branch id is already in use</p>
									<p class="alert alert-success pass" style="display:none; padding:1px; margin:0; margin-top:2px;"><strong>Success!</strong> this branch id is available.</p>
									<?php echo form_error('branch'); ?>
								<?php }?>
								
							</div>
							
							<div class="form-group">
								<label>Name</label>
								<input type="text" class="form-control" placeholder="Branch Name" name="name" id="name" value="<?php echo isset($branchDetail['name'])?$branchDetail['name']:"";?>" required <?php echo $disabled;?>>
								<?php echo form_error('name'); ?>
							</div>
							
							<div class="box-header">
								<?php if("view"!=$type) { ?>
								<button type="submit" class="btn btn-default" name="submit" value="formsubmitted" id="submitnewbranch">
									<?php 
									$addnew = false;
									echo "Save";
									if(isset($branchDetail['branch'])) {
										$addnew = false;
									} else {
										$addnew = true;
									}?>
								</button>
								<?php echo anchor('branches/index', '<button type="button" class="btn btn-danger" name="cancel" value="cancel">Cancel</button>');?>
								
								<?php } ?>
							</div>
							
						</div>
					<?php echo form_close();?>				
				</div>
				
				<div class="row no-border">
					<?php if("view"==$type) { ?>
						<?php echo form_open('branches/details');?>
						<div class="box-footer">
							<input type="hidden" name="branch" value="<?php echo $branchDetail['branch'];?>" />
							<input type="submit" name="edit" value="Edit" class="btn btn-default" />
							<?php echo anchor('branches/index', '<button type="button" class="btn btn-danger" name="cancel" value="cancel">Cancel</button>');?>
						<?php echo form_close();?>
						</div>
					<?php } ?>
				</div>
		  </div>
		 
			<!-- Target -->		 
		  <div class="tab-pane" id="target">
			<div class="row">
				<div class="col-xs-12">
					<div class="content_box"> 
					<?php echo $this->session->flashdata('branch_operation');?>
						<div class="row">
							<div class="col-xs-12">
							  <div class="box">
								<div class="box-body">
									<span id="alertmsg"><?php echo $this->session->flashdata('target_operation');?></span>
								<div class="box-footer no-border">
									<button type="button" class="btn btn-success pull-right" onclick="openAddTargetForm();"><i class="fa fa-fw fa-calendar-plus-o"></i> Add target</button>
									<button type="button" class="btn btn-success pull-left" onclick="openAdduploadTargetForm();"><i class="fa fa-fw fa-calendar-plus-o"></i>Upload target(s)</button>
										<hr>
									<a href="<?= base_url(); ?>/images/branchtarget.csv" download>Sample File (Use Only CSV with headings)</a> 
								</div>	
								  <table class="table table-bordered table-striped branch-list-table users-listing">
									<thead>                                          
										<tr>
											<th>Year/Month</th>
											<th>Sales Target</th>
											<th>Margin OK</th>
											<th>Margin Good</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
									<?php 
									foreach($branchTargets as $row) { ?>
										<tr id="row_<?php echo $row['id'];?>">
											<!-- Year/Month -->
											<td class="branch-link">
												<div class="yearmonth_div"><?php echo $row['yearmonth'];?></div>
												<div class="hidden">
													<input type="number" min="<?php echo date("Y");?>" name="year" class="year width-50" value="<?php echo substr($row['yearmonth'],0,4);?>" /> <i class="fa fa-fw fa-minus" style="color: black; font-weight: normal; font-size: 6px;"></i><input type="number" min="1" max="12" name="month" class="month width-50" value="<?php echo substr($row['yearmonth'],4,2);?>" />													
													
													<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="update(this, '<?=$row['id'];?>',  true);"><i class="glyphicon glyphicon-ok"></i></button>
													
													<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeE(this , 'yearmonth', false);"><i class="glyphicon glyphicon-remove"></i></button>
												</div>
											</td>
											
											<!-- Sales Target -->
											
											<td class="branch-link">
												<div class="salestarget_div"><?php echo $row['salestarget'];?></div>
												<div class="hidden">
													<input type="number" min="0" name="salestarget" class="salestarget height-29" value="<?php echo $row['salestarget'];?>" />
													
													<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="update(this,'<?=$row['id'];?>', false);"><i class="glyphicon glyphicon-ok"></i></button>
													
													<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeE(this, 'salestarget', false);"><i class="glyphicon glyphicon-remove"></i></button>
												</div>
											</td>
											
											<!-- Margin OK-->
											<td class="branch-link">
												<div class="marginok_div"><?php echo $row['marginok'];?></div>
												<div class="hidden">
													<input type="number" name="marginok" class="marginok height-29" value="<?php echo $row['marginok'];?>" />
													
													<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="update(this,'<?=$row['id'];?>', false);"><i class="glyphicon glyphicon-ok"></i></button>
													
													<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeE(this, 'marginok', false);"><i class="glyphicon glyphicon-remove"></i></button>
												</div>
											</td>
											
											<!-- Margin GOOD-->
											<td class="branch-link">
												<div class="margingood_div"><?php echo $row['margingood'];?></div>
												<div class="hidden">
													<input type="number" name="margingood" class="margingood height-29" value="<?php echo $row['margingood'];?>" />
													
													<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="update(this,'<?=$row['id'];?>', false);"><i class="glyphicon glyphicon-ok"></i></button>
													
													<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeE(this, 'margingood', false);"><i class="glyphicon glyphicon-remove"></i></button>
												</div>
											</td>
											
											<!-- DELETE -->
											<td>
												<span id="dlink_<?php echo $row['id'];?>"><a onclick="deletetarget('<?php echo $row['id'];?>');" href="javascript:void(0);" class="transform-link" ><i class="fa fa-fw fa-trash-o"></i></a></span>
											</td>
										</tr>
									<?php } ?>
								</tbody>
									<tfoot>
										<tr>
											<th>Year/Month</th>
											<th>Sales Target</th>
											<th>Margin OK</th>
											<th>Margin Good</th>
											<th class="hidden"></th>											
										</tr>
									</tfoot>
								  </table>
								</div><!-- /.box-body -->
							  </div><!-- /.box -->
							</div><!-- /.col -->
						</div><!-- /.row -->
        
		
					</div>
				</div><!-- /.col -->
			</div><!-- /.row -->
		  </div><!-- /.tab-pane -->

		  <!-- KPI ------------>

	  <div class="tab-pane" id="kpi">
				<div class="row no-border">
					<?php if("view"==$type) { ?>
						<?php echo form_open('branches/details/#editkpi');?>
						<div class="box-footer">
							<input type="hidden" name="branch" value="<?php echo $branchDetail['branch'];?>" />
							<input type="submit" name="edit" value="Edit" class="btn btn-default" />
							<?php echo anchor('branches/index', '<button type="button" class="btn btn-danger" name="cancel" value="cancel">Cancel</button>');?>
						<?php echo form_close();?>
					<?php } ?>
				</div>
				<div class="row">
					<?php echo $this->session->flashdata('kpi_operation');?>
					<?php echo form_open('branches/kpimodify', array("role"=>"form", "id"=>"branchFrm"));?>
						<div class="col-xs-8">
							<div class="box-header">
								<?php if("view"!=$type) { ?>
								<button type="submit" class="btn btn-default" name="submit" value="formsubmitted">
									<?php 
									$addnew = false;
									echo "Save";
									if(isset($branchDetail['branch'])) {
										$addnew = false;
									} else {
										$addnew = true;
									}?>
								</button>
								<?php echo anchor('branches/index', '<button type="button" class="btn btn-danger" name="cancel" value="cancel">Cancel</button>');?>
								
								<?php } ?>
							</div>
							
						
							
							<div class="form-group">
								<label>KPI Threshhold1</label>
								<input type="hidden" name="branch" value="<?php echo $branchDetail['branch'];?>" />
								<input type="text" class="form-control" placeholder="kpi threshold 1" name="kpithreshold1" id="kpithreshold2" value="<?php echo isset($branchDetail['kpithreshold1'])?$branchDetail['kpithreshold1']:"";?>" required <?php echo $disabled;?>>
								<?php echo form_error('kpithreshold1'); ?>
							</div>
							
							<div class="form-group">
								<label>KPI Threshhold2</label>
								<input type="text" class="form-control" placeholder="kpi threshold " name="kpithreshold2" id="kpithreshold2" value="<?php echo isset($branchDetail['kpithreshold2'])?$branchDetail['kpithreshold2']:"";?>" required <?php echo $disabled;?>>
								<?php echo form_error('kpithreshold2'); ?>
							</div>
							<div class="box-header">
								<?php if("view"!=$type) { ?>
								<button type="submit" class="btn btn-default" name="submit" value="formsubmitted" id="submitnewbranch">
									<?php 
									$addnew = false;
									echo "Save";
									if(isset($branchDetail['branch'])) {
										$addnew = false;
									} else {
										$addnew = true;
									}?>
								</button>
								<?php echo anchor('branches/index', '<button type="button" class="btn btn-danger" name="cancel" value="cancel">Cancel</button>');?>
								
								<?php } ?>
							</div>
							
						</div>
					<?php echo form_close();?>				
				</div>
				
				<div class="row no-border">
					<?php if("view"==$type) { ?>
						<?php echo form_open('branches/details/#editkpi');?>
						<div class="box-footer">
							<input type="hidden" name="branch" value="<?php echo $branchDetail['branch'];?>" />
							<input type="submit" name="edit" value="Edit" class="btn btn-default" />
							<?php echo anchor('branches/index', '<button type="button" class="btn btn-danger" name="cancel" value="cancel">Cancel</button>');?>
						<?php echo form_close();?>
						</div>
					<?php } ?>
				</div>
		  </div>

	
<!-- End KPI --->	 
		</div><!-- /.tab-content -->
	  
	  </div><!-- /.nav-tabs-custom -->  
</section><!-- /.content -->

<!-- Hidden form to add new target -->
<section class="hidden-add-target-form">
	<div class="box box-info">
		<div class="box-header with-border">
		  <h3 class="box-title">Add new target</h3>
		</div><!-- /.box-header -->
		<!-- form start -->
		<?php echo form_open('branches/addtarget', array("class"=>"form-horizontal"));?>
		  <div class="box-body">
			<div class="form-group">
			  <label for="year" class="col-sm-2 control-label">Year</label>
			  <div class="col-sm-4">
				<input type="number" min="<?php echo date('Y');?>" class="form-control" id="year" name="year" placeholder="Year" value="<?= date("Y"); ?>" required>
			  </div>
			  <label for="month" class="col-sm-2 control-label">Month</label>
			  <div class="col-sm-4">
				<input type="number" min="1" max="12" class="form-control" id="month" name="month" placeholder="Month" value="<?= date("m"); ?>" required>
			  </div>
			</div>
			<div class="form-group">
			  <label for="target" class="col-sm-2 control-label">Sales Targets</label>
			  <div class="col-sm-10">
				<input type="number" min="0" class="form-control" id="salestarget" name="salestarget" placeholder="Sales target" required>
				
				<input type="hidden" name="branch" value="<?php echo $branchDetail['branch'];?>">
			  </div>
			</div>
			<div class="form-group">
			  <label for="marginok" class="col-sm-2 control-label">Margin OK</label>
			  <div class="col-sm-4">
				<input type="number" min="0" class="form-control" id="marginok" name="marginok" placeholder="Margin ok" required>
			  </div>
			  <label for="margingood" class="col-sm-2 control-label">Margin Good</label>
			  <div class="col-sm-4">
				<input type="number" min="1"  class="form-control" id="margingood" name="margingood" placeholder="Margin good" required>
			  </div>
			</div>
			
		  </div><!-- /.box-body -->
		  <div class="box-footer">
			<button type="button" class="btn btn-default" onclick="closeAddTargetForm();">Cancel</button>
			<button type="submit" class="btn btn-info pull-right">Save</button>
		  </div><!-- /.box-footer -->
		<?php echo form_close();?>
	  </div>
</section>


<section class="hidden-add-uploadtarget-form" style="top:20%:">
	<div class="box box-info">
		<div class="box-header with-border">
		  <h3 class="box-title">Upload target(s)</h3>
		</div><!-- /.box-header -->
		<!-- form start -->
		<?php echo form_open_multipart('branches/uploadtarget', array("class"=>"form-horizontal","accept"=>".csv"));?>
		  <div class="box-body">
			<div class="form-group">
			  <label for="month" class="col-sm-2 control-label">Upload CSV</label>
			  <div class="col-sm-4">
			<input type="file" name="file" class="form-control" required>
			  </div>
			</div>
				<input type="hidden" name="branch" value="<?php echo $branchDetail['branch'];?>">
	
		  </div><!-- /.box-body -->
		  <div class="box-footer">
			<button type="button" class="btn btn-default" onclick="closeAdduploadTargetForm();">Cancel</button>
			<button type="submit" class="btn btn-info pull-right">Upload</button>



				<hr>
				<h5>CSV File format:</h5>
				<h5>Year Month, Sales Target, Margin Ok , Margin Good</h5>
				<hr style="margin-top: 5px; margin-bottom: 5px;">
				<hr style="margin-top: 5px; margin-bottom: 5px;">

					<div class="row">
					<div class="col-sm-2">
					
					
					<h5><strong>Year Month</strong></h5>
					<h5><strong>Sales Target</strong></h5>
					<h5><strong>Marginok</strong></h5>
					<h5><strong>MarginGood</strong></h5>
					</div>
					<div class="col-sm-10">
					
					<h5> Year and month</h5>
					<h5> Sales target</h5>
					<h5> Margin Ok </h5>
					<h5> Margin Good </h5>
					</div>
					</div>

					<hr style="margin-top: 5px; margin-bottom: 5px;">
					<h5>e.g. 201805, 12000,10,20</h5>
					<h5>* Enter data after heading</h5>






		  </div><!-- /.box-footer -->
		<?php echo form_close();?>
	  </div>
</section>










<script>
	var isAdmin = <?php echo $isAdmin;?>;
	var loggedinuserid = '<?php echo $this->session->userdata('userid');?>';
	var branch = '<?php echo $branchDetail['branch'];?>';
</script>

<script>
	$(document).ready(function() {
		var settings = {
			url: base_url+"users/uploadprofileimage/<?php echo $userDetail['userid'];?>",
			dragDrop:false,
			fileName: "userfile",
			allowedTypes:"jpg,jpeg,png,gif",	
			showStatusAfterSuccess: false,
			showProgress: true,
			showFileCounter: false,
			showAbort:false,
			maxFileCount:1,
			showError : true,
			returnType:"json",
			onSuccess:function(files,data,xhr) {
			   var now = new Date().getTime();
			   $(".img-thumbnail").prop('src', data.profilepath+'?'+now);
			   $("#updateimage").val(data.filename);
			},
			showDelete:false,
			deleteCallback: function(data,pd) {
				for(var i=0;i<data.length;i++) {
					$.post(base_url+"users/deleteprofileimage",{op:"delete",name:data[i]},
					function(resp, textStatus, jqXHR) {
						
					});
				 }      
				pd.statusbar.hide(); //You choice to hide/not.
			}
		}
		
		var uploadObj = $("#userfile").uploadFile(settings);		
	});
</script>
<?php if("copy"==$type) {?>
<script>
	$(document).ready(function() {
		var isCheckedCopy = <?php echo isset($userDetail['active']) && ($userDetail['active']==1)?"true":"false";?>;
		checklicense(isCheckedCopy);		
	});
	
	
</script>
<?php }?>