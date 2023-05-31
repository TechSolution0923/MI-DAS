<!-- Content Header (Page header) -->
<section class="content-header">
  <h4> System Log </h4>
  <ol class="breadcrumb">
	<li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
	<li><a href="<?php echo base_url(); ?>logs"><i class="fa fa-users"></i> System Logs List</a></li>
	<li class="active">Log Detail</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"  class="active"><a href="#details" role="tab" data-toggle="tab" aria-expanded="false">Details</a></li>	
		</ul>
		<div class="tab-content">
		  <div class="active tab-pane" id="details">
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group">
					  <label>User</label>
					  <!--<input type="text" class="form-control" placeholder="User Id" name="userid" id="userid" value="<?php echo $log['userid'];?>" required />
					  -->
					  <input type="text" class="form-control" placeholder="User name" name="username" id="username" value="<?php echo $log['userid'];?> - <?php echo $log['firstname']." ".$log['surname'];?>" required />	
					  <div class="clear"></div>
					</div>						

					<div class="form-group">
					  <label>Type</label>
					  <input type="text" class="form-control" placeholder="User Type" name="type" id="type" value="<?php echo $log['type'];?>" required />
					</div>				

					<div class="form-group">
					  <label>Date & Time</label>
					  <input type="text" class="form-control" placeholder="Date & Time" name="datetime" id="datetime" value="<?php echo $log['time'];?>" required />
					</div>

					<div class="form-group">
					  <label>Description</label>
					   <input type="text" class="form-control" name="deascription" id="deascription" value="<?php echo trim($log['description']);?>" required />
					</div>
					<div class="box-footer">
						<?php echo anchor('logs/index', '<button type="button" class="btn btn-danger" name="backtologs" value="backtologs">Back to logs</button>');?>
					</div>
				</div>
			</div>
		  </div>
		</div>  
	</div>
</section><!-- /.content -->