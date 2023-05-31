<!-- This is the FTP version of the load script which recursively goes through all matching files 

<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');
	
	// Start time
	$start_datetime = date('Y-m-d H:i:s');
	error_reporting(E_ALL);	
	
	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));
	
	// $file = "products.csv";

    // ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD STOCK ITEMS - INSERT OR UPDATE
	// ------------------------------------------------------------------------------------------------------------------------------

	foreach (glob("MI-DAS_stock*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{	
			$stockloaded = "Y"; // Set this flag so that ftpgetandload.php knows it has to run loadproductstock.php

			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)	
			{
				$branch       	= $data[0];
				$prodcode       = $data[1];
				$totalqty		= $data[2];
				$backorderqty	= $data[3];
				$allocatedqty   = $data[4];
				$reservedqty  	= $data[5];
				$forwardsoqty   = $data[6];
				$freeqty 		= $data[7];
				$unitofstock 	= $data[8];
				$purchaseqty    = $data[9];
				$backtobackqty  = $data[10];
				$forwardpoqty   = $data[11];
				$avgcost		= $data[12];
				$sfcast			= $data[13];
				$minimum		= $data[14];
				$maximum		= $data[15];
				$value			= $data[16];
				$stocksup		= $data[17];
				$topupsup		= $data[18];
				$lastlead1		= $data[19];
				$lastlead2		= $data[20];
				$lastlead3 		= $data[21];
				$leadtime		= $data[22];

				if (!$branch == "")
				{
					$query = "INSERT INTO stock(branch, prodcode, totalqty, backorderqty, allocatedqty, reservedqty, forwardsoqty, freeqty, unitofstock, purchaseqty, backtobackqty, forwardpoqty, avgcost, K8sfcast, K8minimum, K8maximum, stockvalue, stocksuppcode, topupsuppcode, lastleadtime1, lastleadtime2, lastleadtime3, leadtime) VALUES($branch, '$prodcode', $totalqty, $backorderqty, $allocatedqty, $reservedqty, $forwardsoqty, $freeqty, '$unitofstock', $purchaseqty, $backtobackqty, $forwardpoqty, $avgcost, $sfcast, $minimum, $maximum, $value, '$stocksup', '$topupsup', $lastlead1, $lastlead2, $lastlead3, $leadtime) ON DUPLICATE KEY UPDATE totalqty = $totalqty, backorderqty = $backorderqty, allocatedqty = $allocatedqty, reservedqty = $reservedqty, forwardsoqty = $forwardsoqty, freeqty = $freeqty, unitofstock = '$unitofstock', purchaseqty = $purchaseqty, backtobackqty = $backtobackqty, forwardpoqty = $forwardpoqty, avgcost = $avgcost, K8sfcast = $sfcast, K8minimum = $minimum, K8maximum = $maximum, stockvalue = $value, stocksuppcode = '$stocksup', topupsuppcode = '$topupsup', lastleadtime1 = $lastlead1,  lastleadtime2 = $lastlead2,  lastleadtime3 = $lastlead3, leadtime = $leadtime";	
				  
					$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
				  
					// Instead of running loadproduct stock, which takes a long time, just for this item, sum the stock levels and update the product file
				  
					$stockquery = "SELECT SUM(totalqty), SUM(backorderqty), SUM(allocatedqty), SUM(reservedqty), SUM(forwardsoqty), SUM(freeqty), SUM(purchaseqty), SUM(backtobackqty), SUM(forwardpoqty) FROM stock WHERE prodcode = '$prodcode'";

					$stockqueryresult = mysqli_query($link, $stockquery) or logerror($stockquery,mysqli_error($link));

					// Update the product
		
					$productquery = "UPDATE product SET totalqty = $totalqty, backorderqty = $backorderqty, allocatedqty = $allocatedqty, reservedqty = $reservedqty, forwardsoqty = $forwardsoqty, freeqty = $freeqty, purchaseqty = $purchaseqty, backtobackqty = $backtobackqty, forwardpoqty = $forwardpoqty WHERE code = '$prodcode'";
		
					$productresult = mysqli_query($link, $productquery) or logerror($query,mysqli_error($link));
				}
			}
			fclose($handle);

			// Create the processed folder if it doesnt already exist
			
			$processedfolder = "processed";
			
			if (!file_exists($processedfolder))
			{
				mkdir($processedfolder, 0777);
			}

			$newfilename = $processedfolder."/".$file.date('m-d-Y_Hia');

			rename ($file, $newfilename); 

			// End time and duration and write to the logfile table
			$end_datetime = date('Y-m-d H:i:s');
			$duration = strtotime($end_datetime) - strtotime($start_datetime);
			$minutes = floor($duration / 60);
			$seconds = $duration % 60;
			
			$filename = basename(__FILE__);

			$query = "INSERT INTO logfile(id, application, started, ended, duration) VALUES (0, '$filename', '$start_datetime', '$end_datetime', $duration)";
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

			$logfile = "logfile.txt";
			$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
			$stringData = $start_datetime." ".$filename." ".$minutes." min(s) ".$seconds." sec(s)\n";
			fwrite($fh, $stringData);
			fclose($fh);	
		}
	} // foreach

	mysqli_commit($link);
	

?>
