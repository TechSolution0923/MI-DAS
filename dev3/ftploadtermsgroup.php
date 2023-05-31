<!-- This is the FTP version of the load script which recursively goes through all matching files 

<!-- The terms groups are only ever deleted and inserted. If they have been changed in K8, the loadtermsheader script will have delete the termsgroup and this will reinsert. -->

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

	//$file = "termsgroup.csv";
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD TERMS GROUPS - INSERT
	// ------------------------------------------------------------------------------------------------------------------------------
 
	foreach (glob("MI-DAS_termsgroup*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)
			{
				$termcode      = $data[0];
				$discgroupcode = $data[1];
				$termtype      = $data[2];
				$base          = $data[3];
				$discount1     = $data[4];
				$discount2     = $data[5];
				$currency	   = $data[6];

				if (!$termcode == "")
				{
					$query = "INSERT INTO termsgroup(termcode, discgroupcode, termtype, base, discount1, discount2, currency ) VALUES('$termcode','$discgroupcode','$termtype','$base',$discount1,$discount2,'$currency') ON DUPLICATE KEY UPDATE base = '$base', discount1 = $discount1, discount2 = $discount2";
					
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
