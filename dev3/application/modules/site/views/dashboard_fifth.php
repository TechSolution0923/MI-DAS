<div class="box-header with-border">
  <h3 class="box-title">Quotations x PAC1</h3>
</div>
<div class="box-body" style="max-height: 350px; overflow-y: auto;">
  <table class="table table-striped">
    <thead>
    <tr>
      <th>PAC 1</th>
      <th>Description</th>
      <th>Value this Month</th>
      <th>Qty this Month</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $rowCount = count($currentMonthPac1QuoteConversions);
    $rowNumber = 0;
    foreach ($currentMonthPac1QuoteConversions as $currentMonthPac1QuoteConversion)
    {
      //If the last row display the total
      if ($rowCount === ++$rowNumber)
      {
        ?>
        <tr style="background-color: #e1e1e1; font-style: italic; font-weight: bold; color: black;" class="total-row">
          <td>Total</td>
          <td></td>
          <td class="text-left"><?= number_format($currentMonthPac1QuoteConversion['value_this_month'], 2); ?></td>
          <td class="text-left"><?= number_format($currentMonthPac1QuoteConversion['quantity_this_month'], 0); ?></td>
        </tr>
        <?php
      }
      else
      {
        ?>
        <tr>
          <td><?= $currentMonthPac1QuoteConversion['code']; ?></td>
          <td><?= $currentMonthPac1QuoteConversion['description']; ?></td>
          <td class="text-left"><?= number_format($currentMonthPac1QuoteConversion['value_this_month'], 2); ?></td>
          <td class="text-left"><?= number_format($currentMonthPac1QuoteConversion['quantity_this_month'], 0); ?></td>
        </tr>
        <?php
      }
    }
    ?>
    </tbody>
  </table>
</div>