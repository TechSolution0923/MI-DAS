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
	
	//$file = "salesinvoice.csv";
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD TODAYS CUSTOMER INVOICES
	// ------------------------------------------------------------------------------------------------------------------------------
 
	foreach (glob("MI-DAS_salesinvoice*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)
			{
				$docnumber     = $data[0];
				$docdate       = $data[1];
				$orderno       = $data[2];
				$ordtype       = $data[3];
				$datein        = $data[4];
				$headerdatereq = $data[5];
				$account       = $data[6];
				$headerstatus  = $data[7];
				$prodcode      = $data[8];
				$fulldesc      = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[9]);
				$quantity      = $data[10];
				$unitprice     = $data[11];
				$discount1     = $data[12];
				$discount2     = $data[13];
				$sales         = $data[14];
				$vat           = $data[15];
				$datereq       = $data[16];
				$cost          = $data[17];
				$status        = $data[18];
				$custorderno   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[19]);

				if (!$orderno == "")
				{
				  $query = "INSERT INTO salesinvoices(docnumber, docdate, orderno, ordtype, datein, headerdatereq, account, headerstatus, prodcode, fulldesc, quantity, unitprice, discount1, discount2, sales, vat, datereq, cost, status, custorderno) VALUES('".$docnumber."','".$docdate."','".$orderno."','".$ordtype."','".$datein."','".$headerdatereq."','".$account."','".$headerstatus."','".$prodcode."','".$fulldesc."','".$quantity."','".$unitprice."','".$discount1."','".$discount2."','".$sales."','".$vat."','".$datereq."','".$cost."','".$status."','".$custorderno."')";
				  
				  $result = mysqli_query($link, $query)or logerror($query,mysqli_error($link));	
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
