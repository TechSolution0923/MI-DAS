<!-- Content Header (Page header) -->
<section class="content-header">
  <h4>
	<?php 
	if(isset($companyDetail['name'])) {
		echo "Edit - ".$companyDetail['name'];
	} else {
		echo "Add Company Details";
	}
	
	$type = isset($_POST["type"])?$_POST["type"]:'view';
	
	$disabled = "";
	if("view"!=$type) {		
	} else {
		$disabled = "disabled";
	}
	
	?>
 
  </h4>
  <ol class="breadcrumb">
	<li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
	<li><a href="<?php echo base_url(); ?>company"><i class="fa fa-users"></i> company</a></li>
	<li class="active"><?php echo isset($companyDetail['company'])? $companyDetail['company'].' - '.$companyDetail['name']:"Add Company Details";?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="nav-tabs-custom">
	
		<div class="tab-content">

			<!-- company First tab start here -->

			<div><?php echo isset($message)?$message:"";?></div>
		  <div class="active tab-pane" id="details">
				<div class="row no-border">
					<?php if("view"==$type) { ?>
						<?php echo form_open('company/details');?>
						<div class="box-footer">
							<input type="hidden" name="type" value="edit" />
							<input type="submit" name="edit" value="Edit" class="btn btn-default" />
							<?php echo anchor('company/index', '<button type="button" class="btn btn-danger" name="cancel" value="cancel">Cancel</button>');?>
						<?php echo form_close();?>
						</div>
					<?php } ?>
				</div>
				<div class="row">
					<?php echo $this->session->flashdata('company_operation');?>
					<?php echo form_open('company/details', array("role"=>"form", "id"=>"companyFrm"));?>
						<div class="col-xs-12 col-md-8">
							<div class="box-header">
								<?php if("view"!=$type) { ?>
								<button type="submit" class="btn btn-default" name="submit" value="formsubmitted">
									<?php 
									$addnew = false;
									echo "Save";
									if(isset($companyDetail['company'])) {
										$addnew = false;
									} else {
										$addnew = true;
									}?>
								</button>
								<?php echo anchor('company/index', '<button type="button" class="btn btn-danger" name="cancel" value="cancel">Cancel</button>');?>
								
								<?php } ?>
							</div>
									
							<div class="form-group">
								<label>Company Name</label>
								
								<input type="text" class="form-control" placeholder="Company Name" name="company_name" id="" value="<?php echo isset($name)?$name:"";?>" required  <?php echo $disabled;?> >
								<?php echo form_error('name'); ?>
								
							</div>
							<div class="form-group">
								<label>KPI Threshhold1</label>
								<input type="hidden" name="userid" value="<?php echo isset($userid)?$userid:$user_id;?>" <?php echo $disabled;?> >
								<input type="number" class="form-control" required placeholder="KPI Threshhold 1" name="kpithreshhold1" id="kpithreshhold1" value="<?php echo isset($kpithreshold1)?$kpithreshold1:"";?>"  <?php echo $disabled;?> >
								<?php echo form_error('name'); ?>
								
							</div>
								
							<div class="form-group">
								<label>KPI Threshhold2</label>
								<input type="number" class="form-control" required placeholder="company Name" name="kpithreshhold2" id="kpithreshhold1" value="<?php echo isset($kpithreshold2)?$kpithreshold2:"";?>"  <?php echo $disabled;?> >
								<?php echo form_error('name'); ?>
								
							</div>
							
								
							<div class="form-group">
								<label>Margin Ok</label>

								<input type="hidden" name="subtype" value="<?php echo $subtype; ?>"  <?php echo $disabled;?> >
								<input type="number" class="form-control"  required placeholder="company Name" name="marginok" id="marginok" value="<?php echo isset($marginok)?$marginok:"";?>"  <?php echo $disabled;?> >
								<?php echo form_error('name'); ?>
								
							</div>
							
								
							
							<div class="form-group">
								<label>Margin Good</label>
								<input type="number" class="form-control" required placeholder="company Name" name="margingood" id="margingood" value="<?php echo isset($margingood)?$margingood:"";?>"  <?php echo $disabled;?> >
								<?php echo form_error('name'); ?>
								
							</div>
							
						
							
							<div class="box-header">
								<?php if("view"!=$type) { ?>
								<button type="submit" class="btn btn-default" name="submit" value="formsubmitted" id="submitnewcompany">
									<?php 
									$addnew = false;
									echo "Save";
									if(isset($companyDetail['company'])) {
										$addnew = false;
									} else {
										$addnew = true;
									}?>
								</button>
								<?php echo anchor('company/index', '<button type="button" class="btn btn-danger" name="cancel" value="cancel">Cancel</button>');?>
								
								<?php } ?>
							</div>
							
						</div>
							</div>
							
						</div>
					<?php echo form_close();?>				
				</div>
			
