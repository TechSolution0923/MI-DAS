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

	//$file = "termsheader.csv";
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD TERMS HEADERS - INSERT OR UPDATE
	// ------------------------------------------------------------------------------------------------------------------------------
 
	foreach (glob("termsheader*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)
			{
				$unique        = $data[0];
				$termcode      = $data[1];
				$description   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[2]);
				$effectivefrom = $data[3];
				$effectiveto   = $data[4];
				$termtype      = $data[5];
				$currency	   = $data[6];

				if (!$termcode == "")
				{
					if($anonymous == "TRUE")	// $anonymous set in extractandload.php
					{
						switch ($termtype)
						{
							case "NS":
								$description = "Standard Terms";
								break;
							case "NP":
								$description = "Special Terms";
								break;
							case "NC":
								$description = "Contract Terms";
								break;
							default:
								$description = "Terms Description";
						}
					}
					$query = "INSERT INTO termsheader(termcode, description, effectivefrom, effectiveto, termtype, currency) VALUES('$termcode','$description','$effectivefrom','$effectiveto','$termtype','$currency') ON DUPLICATE KEY UPDATE description = '$description', effectivefrom = '$effectivefrom', effectiveto = '$effectiveto', currency = '$currency'";
					
					$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
					
					// Delete the terms group and terms product because if the header has been marked as updated, it could be that it was actually a group or product that was changed, and if so, they will be coming in a CSV file too
					
					$groupquery = "DELETE FROM termsgroup WHERE termcode = '$termcode' AND termtype = '$termtype' AND currency = '$currency'";
					$groupresult = mysqli_query($link, $groupquery) or logerror($query,mysqli_error($link));

					$productquery = "DELETE FROM termsproduct WHERE termcode = '$termcode' AND termtype = '$termtype' AND currency = '$currency'";
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