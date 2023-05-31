<div class="box-header with-border">
  <h3 class="box-title">Sales Pipeline</h3>
</div>
<div class="box-body" style="max-height: 350px; overflow-y: auto;">
  <table class="table table-striped">
    <thead>
    <tr>
      <th>Stage</th>
      <th class="text-right">Value</th>
      <th class="text-right">%</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $rowCount = count($salesPipelineStages);
    $rowNumber = 0;

    foreach ($salesPipelineStages as $salesPipelineStage)
    {
      //If the last row display the total
      if ($rowCount === ++$rowNumber)
      {
        ?>
        <tr style="background-color: #e1e1e1; font-style: italic; font-weight: bold; color: black;" class="total-row">
          <td>Total</td>
          <td class="text-right"><?= number_format($salesPipelineStage['value'], 2); ?></td>
          <td class="text-right"></td>
        </tr>
        <?php
      }
      else
      {
        ?>
        <tr>
          <td><?= $salesPipelineStage['description']; ?></td>
          <td class="text-right"><?= number_format($salesPipelineStage['value'], 2); ?></td>
          <td class="text-right"><?= number_format($salesPipelineStage['percentage'], 2); ?>%</td>
        </tr>
        <?php
      }
    }
    ?>
    </tbody>
  </table>
</div>
