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
	
	//$file = "specials.csv";

    // ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD SPECIALS - INSERT OR UPDATE
	// ------------------------------------------------------------------------------------------------------------------------------

	foreach (glob("specials*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)	
			{
				$code        = $data[0];
				$description = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[1]);
				$pac1        = $data[2];
				$pac2        = $data[3];
				$pac3        = $data[4];
				$pac4        = $data[5];
				
				if (!$code == "")
				{
					$query = "INSERT INTO product(code, description, pac1code, pac2code, pac3code, pac4code) VALUES('$code','$description','$pac1','$pac2','$pac3','$pac4') ON DUPLICATE KEY UPDATE description = '$description', pac1code = '$pac1', pac2code = '$pac2', pac3code = '$pac3', pac4code = '$pac4'";
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
	}
?>
