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

</style>
<div class="overlay"></div>
<?php 
/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
$canSeeMargins = canSeeMargins();
$canEditNotes = canEditNotes();
$canEditTerms = canEditTerms();
?>
	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1> <?php echo $prodcode . " - " . $description?>  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="<?php echo base_url(); ?>products"><i class="fa fa-dashboard"></i> Products</a></li>
		<li class="active"><?php echo $prodcode . " - " . $description?></li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content">
	  <div class="nav-tabs-custom">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"  class="active"><a href="#proSalesAnalysis" role="tab" data-toggle="tab" aria-expanded="false">Sales Analysis</a></li>
			<li role="presentation"  class=""><a href="#productStock" role="tab" data-toggle="tab" aria-expanded="false">Stock</a></li>
			<li role="presentation" class=""><a href="#proDetails " role="tab" data-toggle="tab" aria-expanded="false"> Details</a></li>
			<li role="presentation" class=""><a href="#proTargets " role="tab" data-toggle="tab" aria-expanded="false"> Targets</a></li>
		</ul>
		<div class="tab-content">

		  <div class=" active  tab-pane" id="proSalesAnalysis">
			<div class="row">
				<div class="col-xs-12">
					
				  <div class="nav-tabs-custom left-tab">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation"  class="active"><a href="#proCharts" role="tab" data-toggle="tab" aria-expanded="false">Chart</a></li>
						<li role="presentation" class=""><a href="#proCustomers" role="tab" data-toggle="tab" aria-expanded="false">Customers</a></li>
						<!-- <li role="presentation" class=""><a href="#proOrders" role="tab" data-toggle="tab" aria-expanded="false">Orders</a></li> -->
					</ul>
					<input type="hidden" name="mm" id="chk_input" value="">
					<div class="tab-content">
					  <div class="active tab-pane" id="proCharts">
						    <div class="row">
        <div class="col-md-10"> <!-- Main left hand side of dashboard -->
            
            <!------------------------------------------------------------------------------------------------------------------>
            <!-- 3 YEAR SALES CHART -->
            <!------------------------------------------------------------------------------------------------------------------>
            
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs pull-right">
                    <li class="active"><a href="#threeyearsaleschart" data-toggle="tab"><i class="fa fa-line-chart"></i></a></li>
                    <li><a href="#threeyearsalestable" data-toggle="tab"><i class="fa fa-table"></i></a></li>
                
                </ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="threeyearsaleschart" style="position: relative;">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="chart">
                                    <canvas id="SalesChart" style="height:250px"></canvas>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <ul class="chart-legend clearfix">
                                    <li><i class="fa fa-circle-o text-navy"></i> <?php echo $year0?></li>
                                    <li><i class="fa fa-circle-o text-light-blue"></i> <?php echo $year1?></li>
                                    <li><i class="fa fa-circle-o text-gray"></i> <?php echo $year2?></li>
                                </ul>
                            </div>
                        </div>
                    </div> 
                  <!-- class="tab-pane" -->
                    <div class="tab-pane" id="threeyearsalestable" style="position: relative;">
                    
                        <table class="table table-striped">
                            <tr>
                                <th>Year</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mar</th>
                                <th>Apr</th>
                                <th>May</th>
                                <th>Jun</th>
                                <th>Jul</th>
                                <th>Aug</th>
                                <th>Sep</th>
                                <th>Oct</th>
                                <th>Nov</th>
                                <th>Dec</th>
                                <th>Total</th>
                            </tr>
                            <tr>
                                <td><?php echo $year0?></td>
                                <?php echo $year0table ?>
                            </tr>
                            <tr>
                                <td><?php echo $year1?></td>
                                <?php echo $year1table ?>
                            </tr>
                            <tr>
                                <td><?php echo $year2?></td>
                                <?php echo $year2table ?>
                            </tr>
                        </table>
                    
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->      
        </div>  <!-- Main left hand side of dashboard col-md-9 -->

    </div>   </div>  <!-- row -->
 <!-- /.tab-pane -->
					  
					  <div class="tab-pane" id="proCustomers">
						<div class="row">
							<div class="col-xs-10">
								  <table id="new-product-customers" class="table table-bordered table-striped">
									<thead>                                          
										<tr>
											<th></th>
											<th>Name</th>
											<th>Qty MTD</th>
											<th>Sales MTD</th>
											<?php if($canSeeMargins) { ?>
											<th>GM% MTD</th>
											<?php } ?>
											<th>Qty YTD</th>
											<th>Sales YTD</th>
											<?php if($canSeeMargins) { ?>
											<th>GM% YTD</th>
											<?php } ?>
											<th>Sales <?php $curryear= date('Y');echo $curryear-1; ?></th>
                      <th>Sales <?php echo $curryear-2;?></th>
										</tr>
									</thead>
									<tbody>
									<?php 
									foreach($custList as $row){
										echo "<tr>";
										echo "<td>".$row['account']."</td>";
										echo "<td><a href='".site_url("customer/customerDetails/".base64_encode($row['account']))."'>".$row['name']."</a></td>";
										echo "<td>".$row['qtymtd']."</td>";
										echo "<td>".$row['salesmtd']."</td>";
										if($canSeeMargins) { 
											echo "<td>".number_format($row['marginmtdpc'],2)."</td>";
										}
										echo "<td>".$row['qtyytd']."</td>";
										echo "<td>".$row['salesytd']."</td>";
										if($canSeeMargins) { 
											echo "<td>".number_format($row['marginytdpc'],2)."</td>";
										}
										echo "<td>".$row['YoY1Sales']."</td>";
                    echo "<td>".$row['YoY2Sales']."</td>";
										echo "</tr>";
									}
									?>
									
									</tbody>
									
									<tfoot>
									  <tr>
										<th>Account</th>
										<th>Name</th>
										<th>Qty MTD</th>
										<th>Sales MTD</th>
										<?php if($canSeeMargins) { ?>
										<th>GM% MTD</th>
										<?php } ?>
										<th>Qty YTD</th>
										<th>Sales YTD</th>
										<?php if($canSeeMargins) { ?>
										<th>GM% YTD</th>
										<?php } ?>
										<th>Sales <?php $curryear= date('Y');echo $curryear-1; ?></th>
                    <th>Sales <?php echo $curryear-2;?></th>
									  </tr>
									</tfoot>
								  </table>
								
							</div><!-- /.col -->
						  </div><!-- /.row -->
					   
					  </div><!-- /.tab-pane -->
						
					  <div class="tab-pane" id="proOrders">
						<div class="row">
							<div class="col-xs-10">
							  <div class=""> orders
							  </div><!-- /.box -->
							  
							</div><!-- /.col -->
						  </div><!-- /.row -->
					   
					  </div><!-- /.tab-pane -->
						
					</div><!-- /.tab-content -->
				  </div>
					  
				</div><!-- /.col -->
			  </div><!-- /.row -->
		   
		  </div>
		  <div class="tab-pane" id="productStock">
		  	<div class="row">
		  		<div class="col-xs-12">
		  			<table id="product-stock-content" class="table table-bordered table-striped">
		  				<thead>                                          
								<tr>
									<th>Branch</th>
									<th>Name</th>
									<th>Total</th>
									<th>B/order</th>
									<th>Allocated</th>
									<th>Reserved</th>
									<th>F/order</th>
									<th>Free</th>
									<th>Unit</th>
									<th>On Order</th>
									<th>BTB</th>
									<th>Date Expected</th>
									<th>Purchase Qty</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								foreach ($productStockList as $stockData) {
									?>
									<tr>
										<td><?php echo $stockData['branch']; ?></td>
										<td><?php echo $stockData['name']; ?></td>
										<td><?php echo number_format($stockData['totalqty']); ?></td>
										<td><?php echo number_format($stockData['backorderqty']); ?></td>
										<td><?php echo number_format($stockData['allocatedqty']); ?></td>
										<td><?php echo number_format($stockData['reservedqty']); ?></td>
										<td><?php echo number_format($stockData['forwardsoqty']); ?></td>
										<td><?php echo number_format($stockData['freeqty']); ?></td>
										<td><?php echo $stockData['unitofstock']; ?></td>
										<td><?php echo number_format($stockData['purchaseqty']); ?></td>
										<td><?php echo number_format($stockData['backtobackqty']); ?></td>
										<td><?php echo $stockData['dateexpected']; ?></td>
										<td><?php echo number_format($stockData['purchaseqty']); ?></td>
									</tr>
									<?php
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>Branch</th>
									<th>Name</th>
									<th>Total</th>
									<th>B/order</th>
									<th>Allocated</th>
									<th>Reserved</th>
									<th>F/order</th>
									<th>Free</th>
									<th>Unit</th>
									<th>On Order</th>
									<th>BTB</th>
									<th>Date Expected</th>
									<th>Purchase Qty</th>
								</tr>
							</tbody>
		  			</table>
		  		</div>
		  	</div>
			</div>


			<!-- Target start -->
			<div class="tab-pane" id="proTargets">
				<div class="row">
								<div class="col-xs-12">
										<div class="content_box">
												<div class="row">
														<div class="col-xs-12">
																<div class="box">
																		<div class="box-body">
																				<span id="alertmsg"><?php echo $this->session->flashdata('target_operation');?></span>
																				<div class="box-footer no-border">

																						<?php if($mainUserEdirAccess =='1'){ ?>

																								<button type="button" class="btn btn-success pull-left" onclick="openAddTargetForm();"><i class="fa fa-fw fa-calendar-plus-o"></i> Add target</button>

																								<button type="button" class="btn btn-success pull-right" onclick="openAdduploadTargetForm();"><i class="fa fa-fw fa-calendar-plus-o"></i> Upload target(s)</button>
																								<hr>
																								<?php } ?>
																				</div>
																				<table class="table table-bordered table-striped target-list-table target-listing" id="example">
																						<thead>
																								<tr>
																										<th>User Id</th>
																										<th>User Name</th>
																										<th>Year/Month</th>
																										<th>Target</th>
																										<?php if($mainUserEdirAccess=='1'){ ?>
																												<th>Delete</th>
																												<?php } ?>
																								</tr>
																						</thead>
																						<tbody>
																								<?php $t=1; foreach($salestarget as $target){  ?>
																										<tr>
																												<td>
																														<?= $target->userid; ?>
																												</td>
																												<td>
																														<?= $target->username; ?>
																												</td>
																												<td>

																														<?php if($mainUserEdirAccess=='1'){ ?>

																																<div class="ulink" id="ulink_<?php echo $target->id;?>" for="<?php echo $target->id;?>">
																																		<?= $target->yearmonth; ?>
																																</div>
																																<div class="hidden" id="hidden_<?php echo $target->id;?>">

																																		<input type="number" min="<?php echo date(" Y ");?>" name="year" id="year_<?php echo $target->id;?>" value="<?php echo substr($target->yearmonth,0,4);?>" for="<?php echo $target->id;?>" class="width-50" />/
																																		<input type="number" min="1" max="12" name="month" id="month_<?php echo $target->id;?>" value="<?php echo substr($target->yearmonth,4,2);?>" for="<?php echo $target->id;?>" class="width-50" />

																																		<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="updateyearmonth('<?php echo $target->id;?>','<?= $page; ?>','<?= $prodcode; ?>');"><i class="glyphicon glyphicon-ok"></i></button>

																																		<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeediting('<?php echo $target->id;?>', 'yearmonth', false);"><i class="glyphicon glyphicon-remove"></i></button>
																																</div>
																																<?php }else{  echo $target->yearmonth; } ?>

																												</td>
																												<td>

																														<?php if($mainUserEdirAccess=='1'){ ?>
																																<div class="flink" id="flink_<?php echo $target->id;?>" for="<?php echo $target->id;?>">
																																		<?php echo $target->salestarget;?>
																																</div>
																																<div class="hidden" id="fhidden_<?php echo $target->id;?>">
																																		<input type="number" min="0" name="salestarget" id="salestarget_<?php echo $target->id;?>" value="<?php echo $target->salestarget;?>" for="<?php echo $target->id;?>" class="height-29" />

																																		<button type="button" class="btn btn-primary btn-sm editable-submit" onclick="updatesalestarget('<?php echo $target->id;?>','<?= $page; ?>');"><i class="glyphicon glyphicon-ok"></i></button>

																																		<button type="button" class="btn btn-default btn-sm editable-cancel" onclick="closeediting('<?php echo $target->id;?>', 'target', false);"><i class="glyphicon glyphicon-remove"></i></button>
																																</div>
																																<?php }else{  echo $target->salestarget; } ?>

																												</td>

																												<?php if($mainUserEdirAccess=='1'){ ?>
																														<td>
																																<span id="dlink_<?php echo $target->id;?>"><a onclick="deletetarget('<?php echo $target->id;?>', '<?php echo $userDetail['userid'];?>','<?= $page; ?>');" href="javascript:void(0);" class="transform-link" ><i class="fa fa-fw fa-trash-o"></i></a></span>
																														</td>
																														<?php } ?>

																										</tr>
																										<?php } ?>
																						</tbody>
																						<tfoot>
																								<tr>
																										<th>PAC
																												<?= $page; ?>
																										</th>
																										<th>User Name</th>
																										<th>Year/Month</th>
																										<th>Target</th>
																										<?php if($mainUserEdirAccess=='1'){ ?>
																												<th>Delete</th>
																												<?php } ?>
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

										</div>
								</div>
								<!-- /.col -->
						</div>
						<!-- /.row -->
			<!-- Target end -->

			</div>
		  <div class="tab-pane" id="proDetails">
			<div class="row">
				<div class="col-xs-12">
				  
				  <div class="box"> 
					  
					  <div class="col-md-8">
						<div class="panel panel-white">
							<div class="panel-body">
								<form class="form-horizontal">
									<div class="form-group">												
										<label for="ProductCode" class="col-sm-2 control-label">Product Code</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="input-readonly" value="<?php echo $prodcode ?>" readonly>
										</div>
									</div>
									<div class="form-group">
										<label for="Description" class="col-sm-2 control-label">Description</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="input-readonly" value="<?php echo $description ?>" readonly>
										</div>
									</div>
									<div class="form-group">
										<label for="PAC4" class="col-sm-2 control-label">Product Group</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="input-readonly" value="<?php echo $pac4 ?>" readonly>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					  
				  </div><!-- /.box -->
				</div><!-- /.col -->
			  </div><!-- /.row -->
		   
		  </div><!-- /.tab-pane -->
		  <!--tab-pane -->
		    
		</div><!-- /.tab-content -->
	  
	  </div><!-- /.nav-tabs-custom -->
	
	</section><!-- /.content -->
	
<!-- Hidden form to add new target -->
<section class="hidden-add-target-form">
	<div class="box box-info">
		<div class="box-header with-border">
			<h3 class="box-title">Add new target</h3>
		</div>
		<!-- /.box-header -->
		<!-- form start -->
		<?php echo form_open('products/addtargettoproductsalestarget', array("class"=>"form-horizontal"));?>
		<div class="box-body">
			<div class="form-group">
				<label for="year" class="col-sm-2 control-label">Select User</label>
				<div class="col-sm-4">
					<select class="form-control" name="userid">
						<?php foreach($users as $user){ ?>
						<option value="<?= $user->userid; ?>"><?= $user->username; ?></option>
						<?php } ?>
					</select>
				</div>
				<label for="year" class="col-sm-2 control-label">Year</label>
				<div class="col-sm-4">
					<input type="number" min="<?php echo date('Y');?>" class="form-control" id="year"name="year" placeholder="Year" value="<?= date("Y"); ?>" required>
				</div>
				<br><br>
				<label for="month" class="col-sm-2 control-label">Month</label>
				<div class="col-sm-4">
					<input type="number" min="1" max="12" class="form-control" id="month" name="month" value="<?= date("m"); ?>" placeholder="Month" required>
				</div>
			</div>
			<div class="form-group">
				<label for="target" class="col-sm-2 control-label">Target</label>
				<div class="col-sm-10">
					<input type="number" min="0" class="form-control" id="target" name="salestarget" placeholder="target" required>
					<input type="hidden" name="product_code" value="<?php echo $prodcode;?>">
					<input type="hidden" name="page_code" value="<?php echo $page;?>">
				</div>
			</div>
		</div>
		<!-- /.box-body -->
		<div class="box-footer">
			<button type="button" class="btn btn-default" onclick="closeAddTargetForm();">Cancel</button>
			<button type="submit" class="btn btn-info pull-right">Save</button>
		</div>
		<!-- /.box-footer -->
		<?php echo form_close();?>
	</div>
</section>

<section class="hidden-add-uploadtarget-form" style="top:20%;">
	<div class="box box-info">
		<div class="box-header with-border">
			<h3 class="box-title">Upload target(s)</h3>
		</div>
		<!-- /.box-header -->
		<!-- form start -->
		<?php echo form_open_multipart('products/uploadtargettoproductsalestarget', array("class"=>"form-horizontal","accept"=>".csv"));?>
		<div class="box-body">
			<div class="form-group">
				<label for="month" class="col-sm-2 control-label">Upload CSV</label>
				<div class="col-sm-4">
					<input type="file" name="file" class="form-control" required>
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
			<h5>User id, Code, Code type, Year Month, Sales Target</h5>
			<hr style="margin-top: 5px; margin-bottom: 5px;">
			<div class="row">
				<div class="col-sm-2">
					<h5><strong>User id</strong></h5>
					<h5><strong>Code</strong></h5>
					<h5><strong>Code Type</strong></h5>
					<h5><strong>Year Month</strong></h5>
					<h5><strong>Sales Target</strong></h5>
				</div>
				<div class="col-sm-10">
					<h5> MI-DAS user id</h5>
					<h5>PAC code – could be PAC1, PAC2, PAC3 or PAC4</h5>
					<h5>Number (1-4) that says which level the PAC code is or P for product</h5>
					<h5>Year and month</h5>
					<h5>Sales target</h5>
				</div>
			</div>
			<hr style="margin-top: 5px; margin-bottom: 5px;">
			<h5>e.g. 1, P01, 2, 201805, 12000</h5>
			<h5>* Enter data after heading</h5>
		</div>
		<!-- /.box-footer -->
		<?php echo form_close();?>
	</div>
</section>

<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/chartjs/Chart.min.js"></script>
<!-- FastClick -->
<script src="<?php echo $this->config->item('base_folder'); ?>public/plugins/fastclick/fastclick.js"></script>

<!-- page script -->
<script>

  $(function () {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- 3 YEAR SALES CHART
    //--------------

    // Get context with jQuery - using jQuery's .get() method.
    var SalesChartCanvas =$("#SalesChart").get(0).getContext("2d");

    // This will get the first returned node in the jQuery collection.
    var SalesChart = new Chart(SalesChartCanvas);
    var SalesChartData = {
      labels: ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"],
      datasets: [
        {
          label: "<?echo $year0?>",
          fillColor: "#001f3f",
          strokeColor: "#001f3f",
          pointColor: "#001f3f",
          pointStrokeColor: "#001f3f",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(220,220,220,1)",
      data: <?php echo $prodyear0data ?> // [1154047,1210785,1299141,1290804,1103012,1301590,480219,0,0,0,0,0]
    },
        {
          label: "<?echo $year1?>",
          fillColor: "#3c8dbc",
          strokeColor: "#3c8dbc",
          pointColor: "#3c8dbc",
          pointStrokeColor: "#3c8dbc",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(60,141,188,1)",
      data : <?php echo $prodyear1data ?> //[1217843,1194532,1304174,1166936,1145749,1281106,1377170,1172458,1337092,1380822,1326835,1025717]
        },
        {
          label: "<?echo $year2?>",
          fillColor: "#d2d6de",
          strokeColor: "#d2d6de",
          pointColor: "#d2d6de",
          pointStrokeColor: "#d2d6de",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(60,141,188,1)",
      data: <?php echo $prodyear2data ?> //[1066799,968009,1085081,1041893,1104702,1204020,1122788,1029475,1224391,1271400,1153435,1046551]
        }

    ]
    };

    var SalesChartOptions = {
      //Boolean - If we should show the scale at all
      showScale: true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines: false,
      //String - Colour of the grid lines
      scaleGridLineColor: "rgba(0,0,0,.05)",
      //Number - Width of the grid lines
      scaleGridLineWidth: 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines: true,
      //Boolean - Whether the line is curved between points
      bezierCurve: true,
      //Number - Tension of the bezier curve between points
      bezierCurveTension: 0.3,
      //Boolean - Whether to show a dot for each point
      pointDot: false,
      //Number - Radius of each point dot in pixels
      pointDotRadius: 4,
      //Number - Pixel width of point dot stroke
      pointDotStrokeWidth: 1,
      //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
      pointHitDetectionRadius: 20,
      //Boolean - Whether to show a stroke for datasets
      datasetStroke: true,
      //Number - Pixel width of dataset stroke
      datasetStrokeWidth: 2,
      //Boolean - Whether to fill the dataset with a color
      datasetFill: false,
      //String - A legend template
      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
      //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio: true,
      //Boolean - whether to make the chart responsive to window resizing
      responsive: true
    };

    //Create the line chart
    SalesChart.Line(SalesChartData, SalesChartOptions);

  });
	
	/* Function to open the form for adding a target */
var openAddTargetForm = function() {
	console.log("click openAddTargetForm");
	$(".overlay").fadeIn('fast', function() {
		$(".hidden-add-target-form").show();
	});
}


/* Function to close the form for adding a target */
var closeAddTargetForm = function() {
	console.log("click closeAddTargetForm");
	$(".hidden-add-target-form").fadeOut('fast', function() {
		$(".overlay").hide();
	});
}

var openAdduploadTargetForm = function() {
	console.log("click openAdduploadTargetForm");
	$(".overlay").fadeIn('fast', function() {
		$(".hidden-add-uploadtarget-form").show();
	});
}

var closeAdduploadTargetForm = function() {
	console.log("click closeAdduploadTargetForm");
	$(".hidden-add-uploadtarget-form").fadeOut('fast', function() {
		$(".overlay").hide();
	});
}
</script>
