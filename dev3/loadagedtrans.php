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
	
	// $file = "agedtrans.csv";
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD AGED TRANSACTIONS - INSERT OR UPDATE
	// ------------------------------------------------------------------------------------------------------------------------------
 
	foreach (glob("agedtrans*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)
			{
				$account        = $data[0];
				$docdate        = $data[1];
				$docnumber      = $data[2];
				$custref        = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[3]);
				$otherref       = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[4]);
				$docstatus      = $data[5];
				$doctype        = $data[6];
				$duedate        = $data[7];
				$totalamount    = $data[8];
				$paidamount     = $data[9];
				$outstandamount = $data[10];
				$collectamount  = $data[11];
				$overdueamount  = $data[12];
				$magic			= $data[13];
					
				if (!$account == "")
				{
				  $query = "INSERT INTO agedtrans(account, docdate, docnumber, custref, otherref, docstatus, doctype, duedate, totalamount, paidamount, outstandamount, collectamount, overdueamount, magic) VALUES('$account','$docdate','$docnumber','$custref','$otherref','$docstatus','$doctype','$duedate',$totalamount,$paidamount,$outstandamount,$collectamount,$overdueamount, $magic) ON DUPLICATE KEY UPDATE docstatus = '$docstatus', duedate = '$duedate', paidamount = $paidamount, outstandamount = $outstandamount, collectamount = $collectamount, overdueamount = $overdueamount";

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

			// End time and duration and write to the logfile table
			$end_datetime = date('Y-m-d H:i:s');
			$duration = strtotime($end_datetime) - strtotime($start_datetime);
			$minutes = floor($duration / 60);
			$seconds = $duration % 60;
			
			$filename = basename(__FILE__);

			$query = "INSERT INTO logfile(id, batch, application, started, ended, duration) VALUES (0, '$batch', '$filename', '$start_datetime', '$end_datetime', $duration)";
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