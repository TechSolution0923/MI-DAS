<!-- This is the FTP version of the load script which recursively goes through all matching files 

<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');
	
	error_reporting(E_ALL);	
	
	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));

	// disable autocommit
	mysqli_autocommit($link, FALSE);
	
	// $file = "stock_s.csv";
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD STOCK - INSERT OR UPDATE
	// ------------------------------------------------------------------------------------------------------------------------------
	
	foreach (glob("MI-DAS_stock_s*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			$affectedrows = 0;

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

				if (!$branch == "")
				{
				  $query = "INSERT INTO stock(branch, prodcode, totalqty, backorderqty, allocatedqty, reservedqty, forwardsoqty, freeqty, unitofstock, purchaseqty, backtobackqty, forwardpoqty) VALUES($branch, '$prodcode', $totalqty, $backorderqty, $allocatedqty, $reservedqty, $forwardsoqty, $freeqty, '$unitofstock', $purchaseqty, $backtobackqty, $forwardpoqty) ON DUPLICATE KEY UPDATE totalqty = $totalqty, backorderqty = $backorderqty, allocatedqty = $allocatedqty, reservedqty = $reservedqty, forwardsoqty = $forwardsoqty, freeqty = $freeqty, purchaseqty = $purchaseqty, backtobackqty = $backtobackqty, forwardpoqty = $forwardpoqty";	
				  
				  $result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
				  
				  $affectedrows++;
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

			if ($affectedrows > 0) // Only write the logfile if rows affected.
			{
				$logfile = "logfile.txt";
				$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
				$stringData = date('Y-m-d_Hia')." Sales Stock - ".$affectedrows." rows affected\n";
				fwrite($fh, $stringData);
				fclose($fh);	
			}	
			
		}
	} // foreach

	mysqli_commit($link);
	
	mysqli_close($link);

?>