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
	
	// Enable autocommit
	mysqli_autocommit($link, TRUE);
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD Exchange Rate - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
	foreach (glob("MI-DAS_exchrates*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			// Delete all existing exchange rate rows
			
			$query = "TRUNCATE exchangerates"; 
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
			
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)
			{
				$currency		= $data[0];
				$rate      		= $data[1];
				$effectivedate	= $data[2];

				if (!$currency == "")
				{
					$query = "INSERT INTO exchangerates(currency, rate, effectivedate) VALUES('$currency',$rate,'$effectivedate') ON DUPLICATE KEY UPDATE rate = $rate";
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
?>