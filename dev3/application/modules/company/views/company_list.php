		<!-- Content Header (Page header) -->
        <section class="content-header">
          <h1> company </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">company</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">		  
		  <?php echo form_open('company/details');?>
			<input type="hidden" name="companyid" value="0" />
			<input type="hidden" name="operation" value="add" />
			<button type="submit" name="addnew" class="btn btn-success pull-right bottom5"><i class="fa fa-fw fa-plus-circle"></i>Add new company</button>			
		  <?php echo form_close();?>
			
			  <div class="row">
				<div class="col-xs-12">
				  <div class="box">
					<div class="box-body">
						<?php echo $this->session->flashdata('company_operation');?>
						
					  <table class="table table-bordered table-striped company-list-table">
						<thead>                                          
							<tr>
								<th width="10">company</th>
								<th>Name</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($activeUserList as $row) { 
								$loggedinuserid = $this->session->userdata('userid');
								$canSubmit = '';
						?>
							<tr>
								<td>
									<?php echo form_open('company/details', array('id'=>'frm'.$row['userid']));?>
										<input type="hidden" name="userid" value="<?php echo $row['userid'];?>" />
										<input type="hidden" name="type" value="view" />
										<span ><?php echo $row['userid'];?></span>
									<?php echo form_close();?>
								</td>
								<td>
									<div ><?php echo $row['firstname'].' '.$row['surname']; ;?></div>
									
									
									</div>
								</td>
								<td>
									<span>									
										<?php echo form_open('company/details');?>
											<input type="hidden" name="userid" value="<?php echo $row['userid'];?>" />
										<input type="hidden" name="type" value="view" />
										<button type="submit" name="edit" class="transform-link"><i class="fa fa-fw fa-info"></i></button>
										<?php echo form_close();?>
									</span>
									
								</td>
							</tr>
						<?php } ?>
					</tbody>
						<tfoot>
							<tr>
								<th width="10">company</th>
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