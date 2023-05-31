<?php require_once( BASEPATH . "../application/views/common/line_a_vs_line_b_charts.php"); ?>

<ul class="nav nav-tabs pull-right">
  <li class="active" onclick="manage_cookie('threeyearsaleschart','N')" id="threeyearsaleschart_nav"><a href="#threeyearsaleschart" data-toggle="tab"><i class="fa fa-line-chart"></i></a></li>
  <li onclick="manage_cookie('threeyearsaleschart','Y')" id="threeyearsalestable_nav"><a href="#threeyearsalestable" data-toggle="tab"><i class="fa fa-table"></i></a></li>
  <li class="pull-left header"><i class="fa fa-shopping-cart"></i>Sales</li>
</ul>
<div class="tab-content no-padding">

  <div class="tab-pane active" id="threeyearsaleschart" style="position: relative;">
    <div class="row">
      <div class="col-md-9">
        <div class="chart">
          <canvas id="this-year-vs-target" style="height: 250px;"></canvas>
          <canvas id="this-year-cml-vs-target-cml" style="height: 250px;"></canvas>
          <canvas id="this-year-vs-last-year" style="height: 250px;"></canvas>
          <canvas id="this-year-cml-vs-last-year-cml" style="height: 250px;"></canvas>
        </div>
      </div>
      <div class="col-md-1">
        <ul class="chart-legend clearfix">
          <li id="this-year-legend"><i class="fa fa-circle-o text-navy"></i><?= $year0; ?></li>
          <li id="this-year-cml-legend" class="hide"><i class="fa fa-circle-o text-navy"></i><?= $year0; ?> Cml.</li>
          <li id="last-year-legend" class="hide"><i class="fa fa-circle-o text-light-blue"></i><?= $year1; ?></li>
          <li id="last-year-cml-legend" class="hide"><i class="fa fa-circle-o text-light-blue"></i><?= $year1; ?> Cml.</li>
          <li id="before-year-legend" class="hide"><i class="fa fa-circle-o text-gray"></i><?= $year2; ?></li>
          <li id="before-year-cml-legend" class="hide"><i class="fa fa-circle-o text-gray"></i><?= $year2; ?> Cml.</li>
          <li id="target-legend"><i class="fa fa-circle-o text-light-blue"></i>Target</li>
          <li id="target-cml-legend" class="hide"><i class="fa fa-circle-o text-light-blue"></i>Target Cml.</li>
        </ul>
      </div>
      <div class="col-md-2">
        <form>
          <div class="form-group">
            <label for="chose-graph">Choose graph</label>
            <select class="form-control" id="choose-graph">
              <optgroup label="Sales vs Target">
                <option value="this-year-vs-target-option"><?= $year0; ?> vs Target</option>
                <option value="this-year-cml-vs-target-cml-option"><?= $year0; ?> cml. vs Target cml.</option>
              </optgroup>
              <optgroup label="Sales Year Comparison">
                <option value="this-year-vs-last-year-option">Monthly</option>
                <option value="this-year-cml-vs-last-year-cml-option">Cumulative</option>
              </optgroup>
            </select>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="tab-pane" id="threeyearsalestable" style="position: relative;overflow-x: scroll;">

  <?php //print_r($salesTargetForLastThreeYear); ?>
  <table class="table table-striped">
    <?php
    $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
    for ($i = 1; $i < $yearstartmonth; $i++) {
      $tmp = array_shift($months);
      array_push($months, $tmp);
    }
    ?>
    <tr class="border-header">
      <th>Year</th>
      <?php foreach ($months as $month) { ?>
        <th><?php echo $month; ?></th>
      <?php } ?>
      <th>Total</th>
    </tr>
    <?php require_once(BASEPATH.'../application/views/common/twoyearsvstarget.php'); ?>
  </table>

  </div>
</div>