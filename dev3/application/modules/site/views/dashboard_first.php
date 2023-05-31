
<div class="<?php if (!!$canSeeMargins) { ?>col-md-3 col-sm-6 col-xs-12<?php } else {?>col-md-6<?php }?>">
  <?php
  if ($dailysalespc < $G_kpithreshold1) $class="bg-red";
  if ($dailysalespc >= $G_kpithreshold1 AND $dailysalespc < $G_kpithreshold2) $class="bg-yellow";
  if ($dailysalespc >= $G_kpithreshold2) $class="bg-green";
  if (empty($G_DailySalesTarget)) $class="bg-green";
  ?>
  <div class="info-box <?= $class?>" id="sales-previous-daydrill-report">
    <a style="color: white;text-decoration: none;" href="<?= base_url().'site/daydrillreport'; ?>">
      <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
      <div class="info-box-content">
        <span class="info-box-title">Sales for <?= $lastsalesdate ?> </span>
        <span class="info-box-number"><?= $currency_symbol; ?><?= number_format($dailysales)?></span>
        <div class="progress">
          <div class="progress-bar" style="width: <?= $dailysalespc?>% !important;"></div>
        </div>
        <span class="progress-description">
					<?= number_format($dailysalespc,0)?>% of target (<?= $currency_symbol; ?><?= number_format($G_DailySalesTarget)?>)
				</span>
      </div>
    </a>
  </div>
</div>
<!-- /.info-box -->
<?php
if ($dailymarginpc < $G_MarginOk) $class="bg-red";
if ($dailymarginpc >= $G_MarginOk AND $dailymarginpc < $G_MarginGood) $class="bg-yellow";
if ($dailymarginpc >= $G_MarginGood) $class="bg-green";
if (empty($G_MarginOk) && empty($G_MarginGood)) { $class="bg-green"; }
?>
<?php if (!!$canSeeMargins) { ?>
  <!-- /.col -->
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box <?= $class?>">
      <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
      <div class="info-box-content">
        <span class="info-box-title">Margin for <?= $lastsalesdate?></span>
        <span class="info-box-number"><?= $currency_symbol; ?><?= number_format($dailymargin)?></span>
        <div class="progress">
          <div class="progress-bar" style="width: <?= $dailymarginpc?>%"></div>
        </div>
        <span class="progress-description">
					<?= number_format($dailymarginpc,2)?>%
				</span>
      </div>
    </div>
  </div>
  <!-- /.col -->
<?php } ?>
<!-- fix for small devices only -->
<div class="<?php if (!!$canSeeMargins) { ?>col-md-3 col-sm-6 col-xs-12<?php } else {?>col-md-6<?php }?>">
  <?php
  if ($monthlysalespc < $G_kpithreshold1) $class="bg-red";
  if ($monthlysalespc >= $G_kpithreshold1 AND $monthlysalespc < $G_kpithreshold2) $class="bg-yellow";
  if ($monthlysalespc >= $G_kpithreshold2) $class="bg-green";
  if (empty($G_MonthlySalesTarget)) $class="bg-green";
  ?>
  <div class="info-box <?= $class ?>">
    <a style="color: white;text-decoration: none;" href="<?= base_url().'site/salesmtdreport'; ?>">
      <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
      <div class="info-box-content">
        <span class="info-box-title">Sales MTD</span>
        <span class="info-box-number"><?= $currency_symbol; ?><?= number_format($monthlysales)?></span>
        <div class="progress">
          <div class="progress-bar" style="width: <?= number_format($monthlysalespc)?>%"></div>
        </div>
        <span class="progress-description">
					<?= number_format($monthlysalespc)?>% of target (<?= $currency_symbol; ?><?= number_format($G_MonthlySalesTarget)?>)
				</span>
      </div>
    </a>
    <!-- /.info-box-content -->
  </div>
  <!-- /.info-box -->
</div>
<!-- /.col -->
<!-- /.col -->
<?php if (!!$canSeeMargins) { ?>
  <div class="col-md-3 col-sm-6 col-xs-12">
    <!-- Colour code the graphic based on margin ok and good values -->
    <?php
    if ($monthlymarginpc < $G_MarginOk) $class="bg-red";
    if ($monthlymarginpc >= $G_MarginOk AND $monthlymarginpc < $G_MarginGood) $class="bg-yellow";
    if ($monthlymarginpc >= $G_MarginGood) $class="bg-green";
    if (empty($G_MarginOk) && empty($G_MarginGood)) { $class="bg-green"; }
    ?>
    <div class="info-box <?= $class ?>">
      <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
      <div class="info-box-content">
        <span class="info-box-title">Margin MTD</span>
        <span class="info-box-number"><?= $currency_symbol; ?><?= number_format($monthlymargin)?></span>
        <div class="progress">
          <div class="progress-bar" style="width: <?= number_format($monthlymarginpc,2)?>%"></div>
        </div>
        <span class="progress-description">
					<?= number_format($monthlymarginpc,2)?>%
				</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
<?php } ?>