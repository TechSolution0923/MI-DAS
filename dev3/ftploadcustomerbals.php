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

	// disable autocommit
	mysqli_autocommit($link, FALSE);

	//$file = "customerbals.csv";

	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD CUSTOMER BALANCES 
	// ------------------------------------------------------------------------------------------------------------------------------
	
	foreach (glob("MI-DAS_customerbals*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)	
			{
				$account    		= $data[0];
				$committeddebt		= $data[1];
				$potentialdebt1		= $data[2];
				$potentialdebt2		= $data[3];
				$potentialdebt3		= $data[4];
				$lastpaymentdate	= $data[5];
				$lastpaymentamount	= $data[6];

				if (!$account == "")
				{	
					$query = "UPDATE customer SET committeddebt = $committeddebt, potentialdebt1 = $potentialdebt1, potentialdebt2 = $potentialdebt2, potentialdebt3 = $potentialdebt3, lastpaymentdate = '$lastpaymentdate', lastpaymentamount = $lastpaymentamount WHERE account = '$account'";
				  
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