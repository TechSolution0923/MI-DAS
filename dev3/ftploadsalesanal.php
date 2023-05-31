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
	
	$systemquery = "SELECT curyearmonth FROM system";
	$systemresult = mysqli_query($link, $systemquery) or logerror($systemquery,mysqli_error($link));
	$systemrow = mysqli_fetch_row($systemresult);
	$curyearmonth = $systemrow[0];
	
	// Extract the year and month
	
	$year0  = substr($curyearmonth,0,4);
	$month0 = substr($curyearmonth,4,2);

	//$year0 = date("Y");
	$year1 = $year0 - 1;
	$year2 = $year1 - 1;
	$year3 = $year2 - 1;
	//$month0 = date("m");
	
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

	foreach (glob("MI-DAS_salesanal*.csv") as $file) 
	{
		//$file = "salesanal.csv";
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			$salesanalrows = 0;
			while (($data = fgetcsv($handle, 500, ";")) !== FALSE)	
			{
				$branch    = $data[0];
				$account   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[1]);
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
				
				if (!$branch == "")
				{
					
					// Get the current sales rep code and currency code
					$custquery = "SELECT repcode,currency FROM customer WHERE account = '$account'";
					$custresult = mysqli_query($link, $custquery) or logerror($custquery,mysqli_error($link));
					$custrow = mysqli_fetch_row($custresult);
					$custrepcode 	= $custrow[0];
					$currency 		= $custrow[1];
					
					// Get the current PAC codes
					$pacquery = "SELECT pac1code, pac2code, pac3code, pac4code FROM product WHERE code = '$prodcode'";
					$pacresult = mysqli_query($link, $pacquery) or logerror($pacquery,mysqli_error($link));
					$pacrow = mysqli_fetch_row($pacresult);
					$pac1 = $pacrow[0];				
					$pac2 = $pacrow[1];				
					$pac3 = $pacrow[2];				
					$pac4 = $pacrow[3];				

					// Get the exchange rate. Its the last rate up to the date of this transasction
					$ratequery = "SELECT er1.rate, er1.effectivedate FROM exchangerates as er1 WHERE currency = '$currency' and er1.effectivedate = (select max(er2.effectivedate) from exchangerates as er2 where er2.currency ='$currency' AND er2.effectivedate <= '$date')";
					
					//$ratequery = "SELECT rate FROM exchangerates WHERE currency = '$currency'";
					
					$rateresult = mysqli_query($link, $ratequery) or logerror($ratequery,mysqli_error($link));
					$exchangerate = 1; // Set the default exchange rate in case there are none in the table.
					
					while ($raterow = mysqli_fetch_row($rateresult))
					{
						$exchangerate = $raterow[0];
					}
					
					// Apply the exchange rate
					$sales = $sales * $exchangerate;
					$cost = $cost * $exchangerate;

					$margin    = $sales - $cost;
					
					if ($custrepcode == "") $custrepcode = "9999";
					
					$query = "INSERT INTO salesanalysis(branch, account, repcode, pac1, pac2, pac3, pac4, prodcode, quantity, unit, sales, cost, date, orderno, invoiceno, yearmonth,   
							 ordtype, magic,exchrate, currency) 
							VALUES($branch,'$account','$custrepcode','$pac1','$pac2','$pac3','$pac4','$prodcode',$quantity,'$unit',$sales,$cost,'$date',$orderno,$invoiceno,$yearmonth,'$ordtype',$magic,$exchangerate,'$currency')";

					$result = mysqli_query($link,$query) or logerror($query,mysqli_error($link));
					$salesanalrows++;
							  
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
					
					// Only update if the age is <= 35

					if ($fieldno >= 0 AND $fieldno <= 35) {	

						// Customer Sales		
						$insertquery = "INSERT INTO customersales(account, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$custrepcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE repcode = '$custrepcode', $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));

						// Customer Product Sales		
						$insertquery = "INSERT INTO customerprodsales(account, prodcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$prodcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));

						// Customer PAC1 Sales		
						$insertquery = "INSERT INTO customerpac1sales(account, pac1code, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$pac1', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));

						// Customer PAC2 Sales		
						$insertquery = "INSERT INTO customerpac2sales(account, pac2code, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$pac2', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));

						// Customer PAC3 Sales		
						$insertquery = "INSERT INTO customerpac3sales(account, pac3code, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$pac3', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));

						// Customer PAC4 Sales		
						$insertquery = "INSERT INTO customerpac4sales(account, pac4code, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$pac4', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));

						// Product Sales		
						$insertquery = "INSERT INTO productsales(prodcode, branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$prodcode', $branch, '$custrepcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));
						
						// PAC1 Sales		
						$insertquery = "INSERT INTO pac1sales(pac1code, branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$pac1', $branch, '$custrepcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));

						// PAC2 Sales		
						$insertquery = "INSERT INTO pac2sales(pac2code, branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$pac2', $branch, '$custrepcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));
						
						// PAC3 Sales		
						$insertquery = "INSERT INTO pac3sales(pac3code, branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$pac3', $branch, '$custrepcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));

						// PAC4 Sales		
						$insertquery = "INSERT INTO pac4sales(pac4code, branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$pac4', $branch, '$custrepcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));

						// Rep Sales		
						$insertquery = "INSERT INTO repsales(branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ($branch, '$custrepcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));


					}
					
					if ($yearmonth == $curyearmonth){

						// Get the day number of the date
				
						$dayno = date("d",strtotime($date));
					
						// The field name will be 'sales'<DD> where DD is the day number e.g. sales13
						
						$dsalesfield = "day".$dayno."sales";
						
						$insertquery = "INSERT INTO dailysales(branch, repcode, yearmonth, $dsalesfield) VALUES ($branch, '$custrepcode', $yearmonth, $sales) ON DUPLICATE KEY UPDATE $dsalesfield = $dsalesfield + $sales";
						$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));
					}
				}
			}
			fclose($handle);

			// Update customer sales margin %'s
			
			$query = "UPDATE customersales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
			mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
			mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
			mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
			mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
			mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
			mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

			$affectedrows = mysqli_affected_rows($link);

			if ($affectedrows > 0) // Only write the logfile if rows affected.
			{
				$logfile = "logfile.txt";
				$fh = fopen($logfile, 'a') or die("Cant open logfile");
				$stringData = date('Y-m-d_Hia')."Customer Sales Margin - ".$affectedrows." rows affected\n";
				fwrite($fh, $stringData);
				fclose($fh);	
			}	
					
			// Update customer pac1 sales margin %'s
			
			$query = "UPDATE customerpac1sales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
			mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
			mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
			mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
			mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
			mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
			mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

			$affectedrows = mysqli_affected_rows($link);

			if ($affectedrows > 0) // Only write the logfile if rows affected.
			{
				$logfile = "logfile.txt";
				$fh = fopen($logfile, 'a') or die("Cant open logfile");
				$stringData = date('Y-m-d_Hia')."Customer PAC1 Sales Margin - ".$affectedrows." rows affected\n";
				fwrite($fh, $stringData);
				fclose($fh);	
			}	
					
			// Update customer pac2 sales margin %'s
			
			$query = "UPDATE customerpac2sales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
			mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
			mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
			mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
			mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
			mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
			mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

			$affectedrows = mysqli_affected_rows($link);

			if ($affectedrows > 0) // Only write the logfile if rows affected.
			{
				$logfile = "logfile.txt";
				$fh = fopen($logfile, 'a') or die("Cant open logfile");
				$stringData = date('Y-m-d_Hia')."Customer PAC2 Sales Margin - ".$affectedrows." rows affected\n";
				fwrite($fh, $stringData);
				fclose($fh);	
			}	
			
			// Update customer pac3 sales margin %'s
			
			$query = "UPDATE customerpac3sales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
			mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
			mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
			mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
			mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
			mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
			mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

			$affectedrows = mysqli_affected_rows($link);

			if ($affectedrows > 0) // Only write the logfile if rows affected.
			{
				$logfile = "logfile.txt";
				$fh = fopen($logfile, 'a') or die("Cant open logfile");
				$stringData = date('Y-m-d_Hia')."Customer PAC3 Sales Margin - ".$affectedrows." rows affected\n";
				fwrite($fh, $stringData);
				fclose($fh);	
			}	
			
			// Update customer pac4 sales margin %'s
			
			$query = "UPDATE customerpac4sales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
			mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
			mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
			mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
			mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
			mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
			mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

			$affectedrows = mysqli_affected_rows($link);

			if ($affectedrows > 0) // Only write the logfile if rows affected.
			{
				$logfile = "logfile.txt";
				$fh = fopen($logfile, 'a') or die("Cant open logfile");
				$stringData = date('Y-m-d_Hia')."Customer PAC4 Sales Margin - ".$affectedrows." rows affected\n";
				fwrite($fh, $stringData);
				fclose($fh);	
			}	
			
			// Update customer product sales margin %'s
			
			$query = "UPDATE customerprodsales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
			mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
			mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
			mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
			mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
			mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
			mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

			$affectedrows = mysqli_affected_rows($link);

			if ($affectedrows > 0) // Only write the logfile if rows affected.
			{
				$logfile = "logfile.txt";
				$fh = fopen($logfile, 'a') or die("Cant open logfile");
				$stringData = date('Y-m-d_Hia')."Customer Product Sales Margin - ".$affectedrows." rows affected\n";
				fwrite($fh, $stringData);
				fclose($fh);	
			}	
			
			// Update product sales margin %'s
			
			$query = "UPDATE productsales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
			mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
			mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
			mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
			mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
			mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
			mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));			
			
			$affectedrows = mysqli_affected_rows($link);

			if ($affectedrows > 0) // Only write the logfile if rows affected.
			{
				$logfile = "logfile.txt";
				$fh = fopen($logfile, 'a') or die("Cant open logfile");
				$stringData = date('Y-m-d_Hia')."Product Sales Margin - ".$affectedrows." rows affected\n";
				fwrite($fh, $stringData);
				fclose($fh);	
			}	 
			
			$newfilename="processed/".$file.date('m-d-Y_hia');
			rename ($file, $newfilename); 

			if ($salesanalrows > 0) // Only write the logfile if rows affected.
			{
				$logfile = "logfile.txt";
				$fh = fopen($logfile, 'a') or die("Cant open logfile");
				$stringData = date('Y-m-d_Hia')." Sales Analysis - ".$salesanalrows." rows affected\n";
				fwrite($fh, $stringData);
				fclose($fh);	
			}	

		}
	}	// 	foreach (glob("salesanal*.csv") as $file) 
	
	mysqli_commit($link);
	
	mysqli_close($link);
?>