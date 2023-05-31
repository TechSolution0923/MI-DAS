<ul class="nav nav-tabs pull-right">
  <li class="active"><a href="#outstandingordersdonutchart" data-toggle="tab" onclick="manage_cookie('outstandingordersdonutchart','N')" ><i class="fa fa-pie-chart"></i></a></li>
  <li onclick="manage_cookie('outstandingordersdonutchart','Y')" id="outstandingorderstable_nav"> <a href="#outstandingorderstable" data-toggle="tab"><i class="fa fa-table"></i></a></li>
  <li class="pull-left header" style="padding:0 4px;"><i class="fa fa-hourglass-end"></i>Outstanding Orders</li>
</ul>
<div class="tab-content no-padding">
  <div class="tab-pane active" id="outstandingordersdonutchart" style="position: relative;">
    <div class="row">
      <div class="col-md-8">
        <p class="text-center">
          <strong>By Status</strong>
        </p>
        <div class="chart">
          <canvas id="OutstandingOrdersByStatus"></canvas>
        </div>
      </div>
      <div class="col-md-4">
        <ul class="chart-legend clearfix">
          <?= $outstandingordersbystatuslegend ?>
        </ul>
      </div>
    </div>
  </div> <!-- class= "tab-pane" -->
  <div class="tab-pane" id="outstandingorderstable" style="position: relative;">
    <div class="box-body">
      <table class="table table-striped">
        <tr>
          <th>By Status</th>
          <th>Description</th>
          <th style="text-align: right">Value</th>
        </tr>
        <?= $outstandingordersbystatustable ?>
      </table>
    </div> <!-- class="box-body" -->
  </div> <!-- class="tab-pane" -->
</div>