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

	// disable autocommit
	mysqli_autocommit($link, FALSE);
	
	$file = "orders.csv";
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD PURCHASE ORDERS - INSERT/UPDATE
	// ------------------------------------------------------------------------------------------------------------------------------

	foreach (glob("MI-DAS_purchord*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 	
		{	
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)
			{
				$branch           = $data[0];
				$orderno          = $data[1];
				$suppliercode     = $data[2];
				$prodcode         = $data[3];
				$sequence         = $data[4];
				$orderquantity    = $data[5];
				$unitcode         = $data[6];
				$price            = $data[7];
				$cost             = $data[8];
				$receivedquantity = $data[9];
				$dateactivated    = $data[10];
				$dateexpected     = $data[11]; 
				$datereceived     = $data[12];
				$status           = $data[13];

				if (!$orderno == "")
				{
					$query = "INSERT INTO purchaseorders( branch, orderno, suppliercode, prodcode, sequence, orderquantity, unitcode, price, cost, receivedquantity, dateactivated, dateexpected, datereceived, status ) VALUES($branch, $orderno,'$suppliercode','$prodcode',$sequence, $orderquantity,'$unitcode',$price, $cost, $receivedquantity,'$dateactivated','$dateexpected','$datereceived','$status') ON DUPLICATE KEY UPDATE receivedquantity = $receivedquantity, datereceived = '$datereceived', status = '$status'";
				  
					$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
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
	}
?>