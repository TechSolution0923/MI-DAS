
  <div class="col-md-3"> <!-- Right hand column of dashboard -->
    <!------------------------------------------------------------------------------------------------------------------>
    <!-- HELD IN OMR -->
    <!------------------------------------------------------------------------------------------------------------------>
    <div class="info-box bg-red">
      <span class="info-box-icon"><i class="ion ion-stop"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Held In OMR</span>
        <span class="info-box-number">SL - <?= $currency_symbol; ?><?= $HeldInOMRSL?></span>
        <span class="info-box-number">CR - <?= $currency_symbol; ?><?= $HeldInOMRCR?></span>
      </div>
    </div>
    <!------------------------------------------------------------------------------------------------------------------>
    <!-- WAITING POSTING -->
    <!------------------------------------------------------------------------------------------------------------------>
    <div class="info-box bg-yellow">
      <span class="info-box-icon"><i class="ion ion-ios-pause"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Waiting Posting</span>
        <span class="info-box-number">SL - <?= $currency_symbol; ?><?= $WaitingPostingSL?></span>
        <span class="info-box-number">CR - <?= $currency_symbol; ?><?= $WaitingPostingCR?></span>
      </div>
    </div>
    <!------------------------------------------------------------------------------------------------------------------>
    <!-- POSTED -->
    <!------------------------------------------------------------------------------------------------------------------>
    <div class="info-box bg-green">
      <span class="info-box-icon"><i class="ion ion-ios-play"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Posted</span>
        <span class="info-box-number">SL - <?= $currency_symbol; ?><?= $PostedSL?></span>
        <span class="info-box-number">CR - <?= $currency_symbol; ?><?= $PostedCR?></span>
      </div>
    </div>
  </div> <!-- col-md-3 -->