<!-- This is the FTP version of the load script which recursively goes through all matching files 

<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');
	
	error_reporting(E_ALL);	
	
	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));

	// Get the current yearmonth

	$year0 = date("Y");
	$year1 = $year0 - 1;
	$year2 = $year1 - 1;
	$year3 = $year2 - 1;
	$month0 = date("m");
	
	$curyearmonth = ($year0 * 100) + $month0;
	
	$yearmonth0 = ($year0 * 100) + 1;
	$yearmonth1 = ($year1 * 100) + 1;
	$yearmonth2 = ($year2 * 100) + 1;
	$yearmonth3 = ($year3 * 100) + 1;	
	
	// disable autocommit
	mysqli_autocommit($link, FALSE);
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD SALES ANALYSIS - INCREMENTAL
	// ------------------------------------------------------------------------------------------------------------------------------

	foreach (glob("MI-DAS_TESTsalesanal*.csv") as $file) 
	{
		//$file = "salesanal.csv";
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			$salesanalrows = 0;
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)	
			{
				$branch    = $data[0];
				$account   = $data[1];
				$repcode   = $data[2];
				$pac1      = $data[3];
				$pac2      = $data[4];
				$pac3      = $data[5];
				$pac4      = $data[6];
				$prodcode  = $data[7];
				$quantity  = $data[8];
				$unit      = $data[9];
				$sales     = $data[10];
				$cost      = $data[11];
				$date      = $data[12];
				$orderno   = $data[13];
				$invoiceno = $data[14];
				$yearmonth = $data[15];
				$ordtype   = $data[16];
				$magic     = $data[17];
				
				$margin    = $sales - $cost;

				if (!$branch == "")
				{
					
					// Get the current sales rep code
					$custquery = "SELECT repcode FROM customer WHERE account = '$account'";
					$custresult = mysqli_query($link, $custquery) or logerror($custquery,mysqli_error($link));
					$custrow = mysqli_fetch_row($custresult);
					$custrepcode = $custrow[0];
					
					// Get the current PAC codes
					$pacquery = "SELECT pac1code, pac2code, pac3code, pac4code FROM product WHERE code = '$prodcode'";
					$pacresult = mysqli_query($link, $pacquery) or logerror($pacquery,mysqli_error($link));
					$pacrow = mysqli_fetch_row($pacresult);
					
					$pac1 = $pacrow[0];				
					$pac2 = $pacrow[1];				
					$pac3 = $pacrow[2];				
					$pac4 = $pacrow[3];				
					
					if ($custrepcode == "") $custrepcode = "9999";
										  
					// ------------------------------------------------------------------------------------------------------------------------------
					// UPDATE THE SALES SUMMARY TABLES
					// ------------------------------------------------------------------------------------------------------------------------------

					// Get the year and month of the transaction
					$trxyear  = substr($yearmonth,0,4);
					$trxmonth = substr($yearmonth,4,2);

					// Determine how long ago this was
					$yeardiff  = $year0 - $trxyear;
					$monthdiff = $month0 - $trxmonth;
					
					// Calculate the age. The column names are like sales0, sales1, sales 2 etc. where 0 is the current month, 1 is current month -1, 2 is current month -2 etc.
					$fieldno = ($yeardiff * 12) + $monthdiff;
					
					$msalesfield    = "msales" . $fieldno;
					$mquantityfield = "mquantity" . $fieldno;
					$mcostfield     = "mcost" . $fieldno;
					$mmarginfield   = "mmargin" . $fieldno;
					
					echo "msalesfield ".$msalesfield." ";
					echo "fieldno ".$fieldno. " ";

					// echo $yearmonth. " ".$fieldno." ".$mquantityfield."=".$quantity."|";	//debug
					if ($yearmonth >= $yearmonth0) { // Update this years sales fields
						$ysalesfield    = "ysales0";
						$yquantityfield = "yquantity0";
						$ycostfield     = "ycost0";
						$ymarginfield   = "ymargin0";
					}
					
					if ($yearmonth >= $yearmonth1 AND $yearmonth < $yearmonth0 ) { // Update this years - 1 sales fields
						$ysalesfield    = "ysales1";
						$yquantityfield = "yquantity1";
						$ycostfield     = "ycost1";
						$ymarginfield   = "ymargin1";
					}
					
					if ($yearmonth >= $yearmonth2 AND $yearmonth < $yearmonth1 ) { // Update this years - 2 sales fields
						$ysalesfield    = "ysales2";
						$yquantityfield = "yquantity2";
						$ycostfield     = "ycost2";
						$ymarginfield   = "ymargin2";
					}
					
					if ($yearmonth >= $yearmonth3 AND $yearmonth < $yearmonth2 ) { // Update this years - 3 sales fields
						$ysalesfield    = "ysales3";
						$yquantityfield = "yquantity3";
						$ycostfield     = "ycost3";
						$ymarginfield   = "ymargin3";
					}
					
				}
			}
			fclose($handle);
		}
	}	// 	foreach (glob("salesanal*.csv") as $file) 
	
	mysqli_commit($link);
	
?>