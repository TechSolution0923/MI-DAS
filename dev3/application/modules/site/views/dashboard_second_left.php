<ul class="nav nav-tabs pull-right">
  <li class="active" onclick="manage_cookie('salestodaydonutcharts','N')"><a href="#salestodaydonutcharts" data-toggle="tab" ><i class="fa fa-pie-chart"></i></a></li>
  <li onclick="manage_cookie('salestodaydonutcharts','Y')" id="salestodaytables_nav"><a href="#salestodaytables" data-toggle="tab"><i class="fa fa-table" ></i></a></li>
  <li class="pull-left header"><i class="fa fa-inbox"></i>Today's Orders</li>
</ul>
<div class="tab-content no-padding">
  <div class="tab-pane active" id="salestodaydonutcharts" style="position: relative;">
    <div class="row">
      <div class="col-md-4">
        <p class="text-center">
          <strong>By Type</strong>
        </p>
        <div class="chart">
          <canvas id="TodaysOrdersByType"></canvas>
        </div>
      </div>
      <div class="col-md-2">
        <ul class="chart-legend clearfix">
          <?= $todaysordersbytypelegend ?>
        </ul>
      </div>
      <div class="col-md-4">
        <p class="text-center">
          <strong>By Status</strong>
        </p>
        <div class="chart">
          <canvas id="TodaysOrdersByStatus"></canvas>
        </div>
      </div>
      <div class="col-md-2">
        <ul class="chart-legend clearfix">
          <?= $todaysordersbystatuslegend ?>
        </ul>
      </div>
    </div> <!-- row -->
  </div>
  <div class="tab-pane" id="salestodaytables" style="position: relative;">
    <div class="row">
      <div class="box-body">
        <div class="col-md-6">
          <table class="table table-striped">
            <tr>
              <th>By Type</th>
              <th>Description</th>
              <th style="text-align: right">Value</th>
            </tr>
            <?= $todaysordersbytypetable?>
          </table>
        </div>
        <div class="col-md-6">
          <table class="table table-striped">
            <tr>
              <th>By Status</th>
              <th>Description</th>
              <th style="text-align: right">Value</th>
            </tr>
            <?= $todaysordersbystatustable?>
          </table>
        </div>
      </div> <!-- class="box-body" -->
    </div> <!-- class="row" -->
  </div> <!-- class="tab-pane" -->
</div> <!-- class="tab-content no-padding" -->