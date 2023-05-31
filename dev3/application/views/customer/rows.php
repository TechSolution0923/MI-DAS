<?php 
/* Code included to make the value of variables $canSeeMargins, $canEditNotes and $canEditTerms available. */
//echo "<pre>";print_r($result); echo "</pre>";
$canSeeMargins = canSeeMargins();
$canEditNotes = canEditNotes();
$canEditTerms = canEditTerms();

$firsttime   = 1;
$qtymtd      = 0;
$salesmtd    = 0;
$costmtd     = 0;
$marginmtdpc = 0;
$qtyytd      = 0;
$salesytd    = 0;
$costytd     = 0;
$marginytdpc = 0;
$x=0;
foreach($result as $row) {
	// If this is a different account and its not the first row, print the details
	if ($row['account'] != $account AND $firsttime != 1)
	{
		echo "<tr>";
		echo "<td>$account</td>";
		echo "<td><a href='$lnk$account'>$name</a></td>";
		echo "<td>$postcode</td>";
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
		$qtymtd      = 0;
		$salesmtd    = 0;
		$costmtd     = 0;
		$marginmtdpc = 0;
		$qtyytd      = 0;
		$salesytd    = 0;
		$costytd     = 0;
		$marginytdpc = 0;
	}
	
	
	$firsttime = 0;													
	$account   = $row['account'];
	$name      = $row['name'];
	$postcode  = $row['postcode'];
	$quantity  = $row['quantity'];
	$sales     = $row['sales'];
	$cost      = $row['cost'];
	$yearmonth = $row['yearmonth'];

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
		$marginmtdpc = number_format($marginmtdpc);
	}
	else
	{
		$marginmtdpc = 0;
	}
	
	$qtyytd   = $qtyytd + $quantity;
	$salesytd = $salesytd + $sales;
	$costytd  = $costytd + $cost;

	// No sales found, set to 0
	
	if (IS_NULL($qtyytd)) $qtyytd = 0;
	if (IS_NULL($salesytd)) $salesytd = 0;
	if (IS_NULL($costytd)) $costytd = 0;
	
	$marginytd = $salesytd - $costytd;
	
	if ($salesytd != 0)
	{
		$marginytdpc = ($marginytd / $salesytd) * 100;
		$marginytdpc = number_format($marginytdpc);														
	}
	else
	{
		$marginytdpc = 0;
	}
}

// Print the last row

echo "<tr>";
echo "<td>$account</td>";
echo "<td><a href='customer.php?a=$account'>$name</a></td>";
echo "<td>$postcode</td>";
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
?>