		<!-- Content Header (Page header) -->
        <section class="content-header">
          <h1> Users </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Users</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">		  
		  <?php echo form_open('users/details');?>
			<input type="hidden" name="userid" value="0" />
			<?php if($isAdmin) { ?>
			<button type="submit" name="addnew" class="btn btn-success pull-right bottom5"><i class="fa fa-fw fa-plus-circle"></i>Add user</button>
			<?php } ?>
		  <?php echo form_close();?>
			  <div class="row">
				<div class="col-xs-12">
				  <div class="box">
					<div class="box-body">
						<?php echo $this->session->flashdata('user_operation');?>
						
					  <table class="table table-bordered table-striped users-list-table users-listing">
						<thead>                                          
							<tr>
								<th width="10">Id</th>
								<th>Surname</th>
								<th>First Name</th>
								<th>Email Address</th>
								<th width="90">User Type</th>
								<th width="10">Active</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($usersDetail as $row) { 
								$loggedinuserid = $this->session->userdata('userid');
								$canSubmit = 'return false; ';
								if($isAdmin || $row['userid']==$loggedinuserid) {
									$canSubmit = '';
								}
						?>
							<tr>
								<td>
									<?php echo form_open('users/details', array('id'=>'frm'.$row['userid']));?>
										<input type="hidden" name="userid" value="<?php echo $row['userid'];?>" />
										<input type="hidden" name="type" value="view" />
										<span onclick="<?php echo $canSubmit;?>document.getElementById('frm<?php echo $row['userid'];?>').submit();" class="transform-link"><?php echo $row['userid'];?></span>
									<?php echo form_close();?>
								</td>
								<td>
									<div class="ulink" id="ulink_<?php echo $row['userid'];?>" for="<?php echo $row['userid'];?>"><?php echo $row['surname'];?></div>
									<div class="hidden" id="hidden_<?php echo $row['userid'];?>"><input type="text" name="surname" id="surname_<?php echo $row['userid'];?>" value="<?php echo $row['surname'];?>" for="<?php echo $row['userid'];?>" style="width: 60%;" class="form-control input-sm" />
									
									<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="changeSurname('<?php echo $row['userid'];?>');"><i class="glyphicon glyphicon-ok"></i></button>
									<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeediting('<?php echo $row['userid'];?>', 'link', false);"><i class="glyphicon glyphicon-remove"></i></button>
									</div>
								</td>
								<td>
									<div class="flink" id="flink_<?php echo $row['userid'];?>" for="<?php echo $row['userid'];?>"><?php echo $row['firstname'];?></div>
									<div class="hidden" id="fhidden_<?php echo $row['userid'];?>"><input type="text" name="firstname" id="firstname_<?php echo $row['userid'];?>" value="<?php echo $row['firstname'];?>" for="<?php echo $row['userid'];?>" style="width: 60%;" class="form-control input-sm" />
									
									<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="changeFirstname('<?php echo $row['userid'];?>');"><i class="glyphicon glyphicon-ok"></i></button>
									<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeediting('<?php echo $row['userid'];?>', 'target', false);"><i class="glyphicon glyphicon-remove"></i></button>
									</div>
								</td>
								<td>
									<div class="elink" id="elink_<?php echo $row['userid'];?>" for="<?php echo $row['userid'];?>"><?php echo $row['email'];?></div>
									<div class="hidden" id="ehidden_<?php echo $row['userid'];?>"><input type="text" name="email" id="email_<?php echo $row['userid'];?>" value="<?php echo $row['email'];?>" for="<?php echo $row['userid'];?>"  style="width: 60%;" class="form-control input-sm" />									
									
									<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="changeEmail('<?php echo $row['userid'];?>');"><i class="glyphicon glyphicon-ok"></i></button>
									<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeediting('<?php echo $row['userid'];?>', 'email', false);"><i class="glyphicon glyphicon-remove"></i></button>
									</div>							
								</td>
								<td><?php echo $row['usertype'];?></td>
								<td><?php echo 0!=intval($row['active'])?"Y":"N";?></td>
								<td>
									<?php if($isAdmin || $row['userid']==$loggedinuserid) { ?>
									<span>
										<?php echo form_open('users/details');?>
										<input type="hidden" name="userid" value="<?php echo $row['userid'];?>" />
										<button type="submit" name="edit" class="transform-link"><i class="fa fa-fw fa-pencil"></i></button>
										<?php echo form_close();?>
									</span>
									<?php } ?>
									<?php if($isAdmin) { ?>
									<span>
										<?php echo form_open('users/details');?>
										<input type="hidden" name="userid" value="<?php echo $row['userid'];?>" />
										<input type="hidden" name="type" value="copy" />
										<button type="submit" name="copy" class="transform-link"><i class="fa fa-fw fa-copy"></i></button>
										<?php echo form_close();?>
									</span>
									<?php 
										if($loggedinuserid!=$row['userid']) { ?>
										<span><a onclick="return confirm('Are you sure to delete this user?');" href="<?php echo site_url("users/delete/".$row['userid']);?>" class="transform-link" ><i class="fa fa-fw fa-trash-o"></i></a></span>
										<?php } ?>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
						<tfoot>
							<tr>
								<th width="10">Id</th>
								<th>Surname</th>
								<th>First Name</th>
								<th>Email Address</th>
								<th width="90">User Type</th>
								<th width="10">Active</th>
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