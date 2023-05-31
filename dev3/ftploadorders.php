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
	
	//$file = "orders.csv";
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD SALES ORDERS - INSERT/UPDATE
	// ------------------------------------------------------------------------------------------------------------------------------
  
	foreach (glob("MI-DAS_orders*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			$ordersloaded = "Y"; // Set this flag so that orders kpis will be recalculated in loadsalesorderkpis.php
   
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)
			{
				$orderno       = $data[0];
				$ordtype       = $data[1];
				$datein        = $data[2];
				$headerdatereq = $data[3];
				$account       = $data[4];
				$headerstatus  = $data[5];
				$quotereason   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[6]);
				$quotefolldate = $data[7];
				$quoteexpidate = $data[8];
				$quotevalue    = $data[9];
				$prodcode      = $data[10];
				$fulldesc      = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[11]);
				$quantity      = $data[12];
				$unitprice     = $data[13];
				$discount1     = $data[14];
				$discount2     = $data[15];
				$sales         = $data[16];
				$datereq       = $data[17];
				$cost          = $data[18];
				$status        = $data[19];
				$custorderno   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[20]);
				$lineno		   = $data[21];
				$branch		   = $data[22];
				$repcode	   = $data[23];
				$snapdate		= $data[24];
				$snapstatus		= $data[25];
				$progress		= $data[26];
				$wysiwyg		= $data[27];
				$postdate		= $data[28];
				$pipelinestage	= $data[29];

				if (!$orderno == "")
				{
					$query = "INSERT INTO salesorders(orderno, ordtype, datein, headerdatereq, account, headerstatus, quotereason, quotefolldate, quoteexpidate, quotevalue, prodcode, fulldesc, quantity, unitprice, discount1, discount2, sales, datereq, cost, status, custorderno, lineno, branch, repcode, snapdate, snapstatus, progress, wysiwyg, postdate, pipelinestage ) VALUES($orderno,'$ordtype','$datein','$headerdatereq','$account','$headerstatus','$quotereason','$quotefolldate','$quoteexpidate',$quotevalue,'$prodcode','$fulldesc',$quantity,$unitprice,$discount1,$discount2,$sales,'$datereq',$cost,'$status','$custorderno', $lineno, $branch, '$repcode', '$snapdate', $snapstatus, '$progress', '$wysiwyg', '$postdate', '$pipelinestage' ) ON DUPLICATE KEY UPDATE ordtype = '$ordtype', headerstatus = '$headerstatus', quotereason = '$quotereason', quotefolldate = '$quotefolldate', quoteexpidate = '$quoteexpidate', quotevalue = $quotevalue, quantity = $quantity, unitprice = $unitprice, discount1 = $discount1, discount2 = $discount2, sales = $sales, datereq = '$datereq', cost = $cost, status = '$status', custorderno = '$custorderno', repcode = '$repcode', snapdate = '$snapdate', snapstatus = $snapstatus, progress = '$progress', wysiwyg = '$wysiwyg', postdate = '$postdate', pipelinestage = '$pipelinestage'";
				  
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
			
			// $batch comes from the extractandloadscript

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