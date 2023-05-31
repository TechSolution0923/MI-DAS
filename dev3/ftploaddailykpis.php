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

	// Enable autocommit
	mysqli_autocommit($link, TRUE);
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD DAILY KPIs - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------

	//$file = "dailykpis.csv";

	foreach (glob("MI-DAS_dailykpis*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			$today = date('Y-m-d');
			
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)	
			{
				$identifier	= $data[0];
				$period   	= $data[1];
				$level   	= $data[2];
				$analysis   = $data[3];
				$date      	= $data[4];
				$actual1    = $data[5];
				$actual2    = $data[6];
				$actual3  	= $data[7];
				$actual4  	= $data[8];
				$target1    = $data[9];
				$target2    = $data[10];
				$target3    = $data[11];
				$target4    = $data[12];

				if (!$identifier == "")
				{
					$query = "INSERT INTO kpidata(identifier, period, level, analysis, kpidata.date, actualvalue1, actualvalue2, actualvalue3, actualvalue4, targetvalue1, targetvalue2, targetvalue3, targetvalue4) 
							VALUES ('$identifier', $period, '$level', '$analysis', '$date', $actual1, $actual2, $actual3, $actual4, $target1, $target2, $target3, $target4) ON DUPLICATE KEY UPDATE actualvalue1 = $actual1, actualvalue2 = $actual2, actualvalue3 = $actual3, actualvalue4 = $actual4";
							
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

			// ------------------------------------------------------------------------------------------------------------------------------
			// UPDATE SYSTEM FILE - DATE AND TIME KPIS UPDATED
			// ------------------------------------------------------------------------------------------------------------------------------

			$query = "UPDATE system SET kpislastupdated = now()";
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));	
		}
	} // foreach
	
	mysqli_commit($link);
?>