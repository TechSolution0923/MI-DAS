<!-- This is the FTP version of the load script which recursively goes through all matching files 

<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');
	
	error_reporting(E_ALL);

	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));
	
	
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
			
			mysqli_commit($link);
			
			$logfile = "logfile.txt";
			$fh = fopen($logfile, 'a') or die("Cant open logfile");
			$stringData = date('Y-m-d_Hia')." Exchange Rates - Truncated\n";
			fwrite($fh, $stringData);
			fclose($fh);	
	
			
			$affectedrows = 0;
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)
			{
				$currency		= $data[0];
				$rate      		= $data[1];
				$effectivedate	= $data[2];

				if (!$currency == "")
				{
					$query = "INSERT INTO exchangerates(currency, rate, effectivedate) VALUES('$currency',$rate,'$effectivedate') ON DUPLICATE KEY UPDATE rate = $rate";
					$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

					$affectedrows++; // Not using the mysqli_affected_rows function here because ON DUPLICATE KEY returns a value of 2 if updated
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
				$stringData = date('Y-m-d_Hia')." Exchange rates - ".$affectedrows." rows inserted\n";
				fwrite($fh, $stringData);
				fclose($fh);	
			}	
		}
	} // foreach
	
	mysqli_commit($link);
	
	mysqli_close($link);

?>
