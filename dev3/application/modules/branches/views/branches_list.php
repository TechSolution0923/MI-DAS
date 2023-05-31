		<!-- Content Header (Page header) -->
        <section class="content-header">
          <h1> Branches </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Branches</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">		  
		  <?php echo form_open('branches/details');?>
			<input type="hidden" name="branchid" value="0" />
			<input type="hidden" name="operation" value="add" />
			<button type="submit" name="addnew" class="btn btn-success pull-right bottom5"><i class="fa fa-fw fa-plus-circle"></i>Add new branch</button>			
		  <?php echo form_close();?>
			
			  <div class="row">
				<div class="col-xs-12">
				  <div class="box">
					<div class="box-body">
						<?php echo $this->session->flashdata('branch_operation');?>
						
					  <table class="table table-bordered table-striped branch-list-table">
						<thead>                                          
							<tr>
								<th width="10">Branch</th>
								<th>Name</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($branchesDetail as $row) { 
								$loggedinuserid = $this->session->userdata('userid');
								$canSubmit = '';
						?>
							<tr>
								<td>
									<?php echo form_open('branches/details', array('id'=>'frm'.$row['branch']));?>
										<input type="hidden" name="branch" value="<?php echo $row['branch'];?>" />
										<input type="hidden" name="type" value="view" />
										<span onclick="<?php echo $canSubmit;?>document.getElementById('frm<?php echo $row['branch'];?>').submit();" class="transform-link"><?php echo $row['branch'];?></span>
									<?php echo form_close();?>
								</td>
								<td>
									<div class="ulink" id="ulink_<?php echo $row['branch'];?>" for="<?php echo $row['branch'];?>"><?php echo $row['name'];?></div>
									<div class="hidden" id="hidden_<?php echo $row['branch'];?>"><input type="text" name="name" id="name_<?php echo $row['branch'];?>" value="<?php echo $row['name'];?>" for="<?php echo $row['branch'];?>" style="width: 60%;" class="form-control input-sm" />
									
									<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="changeBranchName('<?php echo $row['branch'];?>');"><i class="glyphicon glyphicon-ok"></i></button>
									<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeediting('<?php echo $row['branch'];?>', 'link', false);"><i class="glyphicon glyphicon-remove"></i></button>
									</div>
								</td>
								<td>
									<span>									
										<?php echo form_open('branches/details');?>
										<input type="hidden" name="branch" value="<?php echo $row['branch'];?>" />
										<input type="hidden" name="type" value="view" />
										<button type="submit" name="edit" class="transform-link"><i class="fa fa-fw fa-info"></i></button>
										<?php echo form_close();?>
									</span>
									
									<span><a onclick="return confirm('Are you sure to delete this branch?');" href="<?php echo site_url("branches/delete/".$row['branch']);?>" class="transform-link" ><i class="fa fa-fw fa-trash-o"></i></a></span>
								</td>
							</tr>
						<?php } ?>
					</tbody>
						<tfoot>
							<tr>
								<th width="10">Branch</th>
								<th>Name</th>
								<th class="hidden">Actions</th>
							</tr>
						</tfoot>
					  </table>
					</div><!-- /.box-body -->
				  </div><!-- /.box -->
				</div><!-- /.col -->
			  </div><!-- /.row -->
			</section><!-- /.content -->
<script>
	var isAdmin = <?php echo $isAdmin;?>;
	var loggedinuserid = '<?php echo $this->session->userdata('userid');?>';
</script>