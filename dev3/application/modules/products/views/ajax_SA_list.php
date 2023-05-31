<?php 
/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
die('111');
$canSeeMargins = canSeeMargins();
$canEditNotes = canEditNotes();
$canEditTerms = canEditTerms();
?>
<div class="row">
	<div class="col-xs-10">
		  <table class="table <?php echo $elementId;?>example-table table-bordered table-striped">
			<thead>                                          
				<tr>
					<th>Code</th>
					<th>Description</th>
					<th>Qty MTD</th>
					<th>Sales MTD</th>
					<?php if($canSeeMargins) {?>
					<th>GM% MTD</th>
					<?php } ?>
					<th>Qty YTD</th>
					<th>Sales YTD</th>
					<?php if($canSeeMargins) { ?>
					<th>GM% YTD</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php foreach($prodsanalcust as $val) {
				$qtymtd = 0;
				$salesmtd = 0;
				$costmtd  = 0;
				$marginmtdpc = 0;
				$qtyytd = 0;
				$salesytd = 0;
				$costytd = 0;
				$marginytdpc = 0;
				$code = "";
				$code        = $val['code'];
				$description = $val['description'];
				$quantity    = $val['quantity_sum'];
				$sales       = $val['sales_sum'];
				$cost        = $val['cost_sum'];
				$yearmonth   = $val['yearmonth'];
																			
				if ($yearmonth == $curyearmonth)  
				{	
					$qtymtd   = $qtymtd + $quantity;
					$salesmtd = $salesmtd + $sales;
					$costmtd  = $costmtd + $cost;
				}

				// No sales found, set to 0
												
				if (IS_NULL($qtymtd)) $qtymtd = 0;
				if (IS_NULL($salesmtd)) $salesmtd = 0;
				if (IS_NULL($costmtd)) $costmtd = 0;
												
				$marginmtd = $salesmtd - $costmtd;
												
				if ($salesmtd != 0)
				{
					$marginmtdpc = ($marginmtd / $salesmtd) * 100;
					$marginmtdpc = number_format($marginmtdpc,2);
				}
				else
				{
					$marginmtdpc = 0;
				}
											
				$qtyytd   = $qtyytd + $quantity;
				$salesytd = $salesytd + $sales;
				$costytd = $costytd + $cost;

				// No sales found, set to 0
												
				if (IS_NULL($qtyytd)) $qtyytd = 0;
				if (IS_NULL($salesytd)) $salesytd = 0;
				if (IS_NULL($costytd)) $costytd = 0;
												
				$marginytd = $salesytd - $costytd;
												
				if ($salesytd != 0)
				{
					$marginytdpc = ($marginytd / $salesytd) * 100;
					$marginytdpc = number_format($marginytdpc,2);														
				}
				else
				{
					$marginytdpc = 0;
				}
				echo "<tr>";
				echo "<td>$code</td>";
				echo "<td><a href='$lnk/$code'>$description</a></td>";
				echo "<td>$qtymtd</td>";
				echo "<td>$salesmtd</td>";
				if($canSeeMargins) {
					echo "<td>$marginmtdpc</td>";
				}
				echo "<td>$qtyytd</td>";
				echo "<td>$salesytd</td>";
				if($canSeeMargins) {
					echo "<td>$marginytdpc</td>";
				}
				echo "</tr>";
			}
				?>
			  
			</tbody>
			<tfoot>
			  <tr>
				<th>Code</th>
				<th>Description</th>
				<th>Qty MTD</th>
				<th>Sales MTD</th>
				<?php if($canSeeMargins) {?>
				<th>GM% MTD</th>
				<?php } ?>
				<th>Qty YTD</th>
				<th>Sales YTD</th>
				<?php if($canSeeMargins) { ?>
				<th>GM% YTD</th>
				<?php } ?>
			  </tr>
			</tfoot>
		  </table>
		
	</div><!-- /.col -->
</div><!-- /.row -->
