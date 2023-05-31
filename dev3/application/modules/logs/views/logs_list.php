		<!-- Content Header (Page header) -->
        <section class="content-header">
          <h1> Logs  </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Logs</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
			  <div class="row">
				<div class="col-xs-12">
				  <div class="box">
					<div class="box-body">
					  <table class="table table-bordered table-striped log-list-table users-listing">
						<thead>                                          
							<tr>
								
								<th style="width:15%;">User</th>
								<th style="width:5%;">Type</th>
								<th style="width:25%;">Date & Time</th>
								<th>Description</th>
							</tr>
						</thead>	
						<tfoot>
							<tr>
								
								<th>User</th>
								<th>Type</th>
								<th>Date & Time</th>
								<th>Description</th>
							</tr>
						</tfoot>
						<tbody>
						<?php foreach($logs as $row) { ?>
							<tr>
					
								<td><?php echo $row['userid'];?> - <?php echo $row['firstname'];?> <?php echo $row['surname'];?></td>
								<td><?php echo $row['type'];?></td>
								<td><?php echo date("Y-m-d h:i:s", strtotime($row['time']));?></td>
								<td>
									<a href="<?php echo site_url("logs/detail/".$row['id']);?>"><?php echo $row['description'];?></a>
								</td>
							</tr>
						<?php } ?>
					</tbody>
					
					  </table>
					</div><!-- /.box-body -->
				  </div><!-- /.box -->
				</div><!-- /.col -->
			  </div><!-- /.row -->
			</section><!-- /.content -->
