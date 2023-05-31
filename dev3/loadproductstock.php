<!-- This routine goes through the stock table and updates the product stock levels accordingly -->

<?php
    require_once 'dblogin.php';	
	
	date_default_timezone_set('Europe/London');

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');

	// Start time
	$start_datetime = date('Y-m-d H:i:s');
	
	error_reporting(E_ALL);	

	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));

	$stockquery = "SELECT prodcode, SUM(totalqty), SUM(backorderqty), SUM(allocatedqty), SUM(reservedqty), SUM(forwardsoqty), SUM(freeqty), SUM(purchaseqty), SUM(backtobackqty), SUM(forwardpoqty), SUM(totalval) FROM stock GROUP BY prodcode ORDER BY prodcode";
	
	$stockresult = mysqli_query($link, $stockquery) or logerror($query,mysqli_error($link));
		
	while ($stockrow = mysqli_fetch_row($stockresult)) 
	{
		$prodcode 		= $stockrow[0];
		$totalqty 		= $stockrow[1];
		$backorderqty 	= $stockrow[2];
		$allocatedqty 	= $stockrow[3];
		$reservedqty 	= $stockrow[4];
		$forwardsoqty 	= $stockrow[5];
		$freeqty 		= $stockrow[6];
		$purchaseqty 	= $stockrow[7];
		$backtobackqty 	= $stockrow[8];
		$forwardpoqty 	= $stockrow[9];
		$totalval		= $stockrow[10];
		
		// Update the product
		
		$productquery = "UPDATE product SET totalqty = $totalqty, backorderqty = $backorderqty, allocatedqty = $allocatedqty, reservedqty = $reservedqty, forwardsoqty = $forwardsoqty, freeqty = $freeqty, purchaseqty = $purchaseqty, backtobackqty = $backtobackqty, forwardpoqty = $forwardpoqty, totalval = $totalval WHERE code = '$prodcode'";
		
		$productresult = mysqli_query($link, $productquery) or logerror($query,mysqli_error($link));
	}
	
	// End time and duration and write to the logfile table
	$end_datetime = date('Y-m-d H:i:s');
	$duration = strtotime($end_datetime) - strtotime($start_datetime);
	$minutes = floor($duration / 60);
	$seconds = $duration % 60;
	
	$filename = basename(__FILE__);

	// $batch comes from the extractandloadscript

	$query = "INSERT INTO logfile(id, batch, application, started, ended, duration) VALUES (0, '$batch', '$filename', '$start_datetime', '$end_datetime', $duration)";
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

	$logfile = "logfile.txt";
	$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
	$stringData = $start_datetime." ".$filename." ".$minutes." min(s) ".$seconds." sec(s)\n";
	
	fwrite($fh, $stringData);
	fclose($fh);
	
	mysqli_commit($link);
?>
