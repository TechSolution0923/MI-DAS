<div class="box-header with-border">
  <h3 class="box-title">PAC Sales vs Target</h3>
</div>
<div class="box-body">
  <div class="">
    <table class="table table-striped" id="example">
      <thead>
      <tr>
        <th>PAC</th>
        <th>Description</th>
        <th>Sales MTD</th>
        <th>Target</th>
        <th>Progress</th>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($pac1salestarget as $pac1){
        $pac_progress= round($getSalesTotalMonthWise[$pac1->paccode]*100/$pac1->salestarget,2);
        if ($pac1->salestarget=="")
        {
          $pac1->salestarget=0;
        }
        if ($getSalesTotalMonthWise[$pac1->paccode]=="")
        {
          $getSalesTotalMonthWise[$pac1->paccode]=0;
        }
        if ($pac_progress=="")
        {
          $pac_progress=0;
        }
        if ($pac_progress==""||$pac_progress<=30)
        {
          $class="danger";
        }
        elseif ($pac_progress<=30 )
        {
          $class="danger";
        }
        elseif ($pac_progress>30 && $pac_progress<=60)
        {
          $class="warning";
        }
        else
        {
          $class="success";
        }
        ?>
        <?php if ($pac1->paccode!=''){?>
          <tr>
            <td><?= $pac1->paccode; ?></td>
            <td><a href="<?= base_url(); ?>products/details2/<?= $pac1->tabl; ?>/<?=$pac1->paccode; ?> "><?= $pac1->description; ?></a></td>
            <td><?= $getSalesTotalMonthWise[$pac1->paccode]; ?></td>
            <td><?= $pac1->salestarget; ?></td>
            <td>
              <div class="progress" style="height:5px;">
                <div class="progress-bar progress-bar-<?= $class; ?>" style="width:<?= $pac_progress; ?>% !important;"></div>
              </div>
              <span class="progress-description">
												<?= $pac_progress; ?>%
												</span>
            </td>
          </tr>
        <?php } ?>
      <?php } ?>
      </tbody>
    </table>
  </div>
  <a href="<?= base_url(); ?>/products/pacsalestargetdata" class="btn btn-info" >See All</a>
</div>