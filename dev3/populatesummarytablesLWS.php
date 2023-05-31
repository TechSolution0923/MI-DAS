<!-- This routine goes through the sales analysis and populates the sales summary tables -->
<!-- It loops through the customers, and then each customers sales analysis. This is to keep the result set size down as it was initially running out of memory -->

<!-- THIS FILE IS THE LWS VERSION AS IT USES THE SALESANALYSIS.REPCODE COLUMN, RATHER THAN THE CURREPCODE COLUMN.

<?php
    require_once 'dblogin.php';	
	
	date_default_timezone_set('Europe/London');
	
	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or die ("Unable to connect!"); 

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
	
	$query = "TRUNCATE TABLE customerpac1sales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));
	
	$query = "TRUNCATE TABLE customerpac2sales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$query = "TRUNCATE TABLE customerpac3sales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$query = "TRUNCATE TABLE customerpac4sales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$query = "TRUNCATE TABLE customerprodsales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$query = "TRUNCATE TABLE customersales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$query = "TRUNCATE TABLE productsales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$query = "TRUNCATE TABLE pac1sales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$query = "TRUNCATE TABLE pac2sales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$query = "TRUNCATE TABLE pac3sales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$query = "TRUNCATE TABLE pac4sales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$query = "TRUNCATE TABLE repsales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$query = "TRUNCATE TABLE dailysales";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$customerquery = "SELECT account FROM customer ORDER BY account";
	$customerresult = mysqli_query($link, $customerquery) or die ("Error in query: $customerquery. ".mysqli_error($link));
	
	while ($customerrow = mysqli_fetch_row($customerresult)) 
	{
		$repcode = $customerrow[0];

		// 12/11/2018 Selecting the customer rep code so that the current rep code is used to populate the summary tables, rather than the rep code from the trxmonth
		
		echo $account." "; //debug
		
		// Go through the sales analysis for this customer

		$salesanalysisquery = "SELECT prodcode, curpac1code, curpac2code, curpac3code, curpac4code, yearmonth, quantity, sales, cost, salesanalysis.branch, date, repcode FROM salesanalysis WHERE account = '$account' ORDER BY yearmonth";
		$salesanalysisresult = mysqli_query($link, $salesanalysisquery) or die ("Error in query: $salesanalysisquery. ".mysqli_error($link));

		$x = 0;
		
		while ($row = mysqli_fetch_row($salesanalysisresult)) 
		{	
	
			$product   = $row[0];
			$pac1      = $row[1];
			$pac2      = $row[2];
			$pac3      = $row[3];
			$pac4      = $row[4];
			$yearmonth = $row[5];
			$quantity  = $row[6];
			$sales     = $row[7];
			$cost      = $row[8];
			$branch    = $row[9];
			$date      = $row[10];
			$repcode   = $row[11];
			$margin    = $sales - $cost;

			// If the rep code is blank, give it a value because the SQL fails if its blank
			
			if ($repcode == "") $repcode = "----";
			
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
				// 12/11/2018 Putting $repcode into the file rather than the rep code from the trx. This is to prevent duplicate rows 
				
				$insertquery = "INSERT INTO customersales(account, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$repcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Customer Product Sales		
				$insertquery = "INSERT INTO customerprodsales(account, prodcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$product', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Customer PAC1 Sales		
				$insertquery = "INSERT INTO customerpac1sales(account, pac1code, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$pac1', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Customer PAC2 Sales		
				$insertquery = "INSERT INTO customerpac2sales(account, pac2code, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$pac2', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Customer PAC3 Sales		
				$insertquery = "INSERT INTO customerpac3sales(account, pac3code, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$pac3', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Customer PAC4 Sales		
				$insertquery = "INSERT INTO customerpac4sales(account, pac4code, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$account', '$pac4', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Product Sales		
				// 12/11/2018 Putting $repcode into the file rather than the rep code from the trx. This is to prevent duplicate rows 

				$insertquery = "INSERT INTO productsales(prodcode, branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$product', $branch, '$repcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
				
				// PAC1 Sales		
				// 12/11/2018 Putting $repcode into the file rather than the rep code from the trx. This is to prevent duplicate rows 

				$insertquery = "INSERT INTO pac1sales(pac1code, branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, 
								$ymarginfield) 
								VALUES ('$pac1', $branch, '$repcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) 
								ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, 
								$mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, 
								$ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// PAC2 Sales		
				// 12/11/2018 Putting $repcode into the file rather than the rep code from the trx. This is to prevent duplicate rows 

				$insertquery = "INSERT INTO pac2sales(pac2code, branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$pac2', $branch, '$repcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
				
				// PAC3 Sales		
				// 12/11/2018 Putting $repcode into the file rather than the rep code from the trx. This is to prevent duplicate rows 

				$insertquery = "INSERT INTO pac3sales(pac3code, branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$pac3', $branch, '$repcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// PAC4 Sales		
				// 12/11/2018 Putting $repcode into the file rather than the rep code from the trx. This is to prevent duplicate rows 

				$insertquery = "INSERT INTO pac4sales(pac4code, branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ('$pac4', $branch, '$repcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Rep Sales		
				$insertquery = "INSERT INTO repsales(branch, repcode, $mquantityfield, $msalesfield, $mcostfield, $mmarginfield, $yquantityfield, $ysalesfield, $ycostfield, $ymarginfield) VALUES ($branch, '$repcode', $quantity, $sales, $cost, $margin, $quantity, $sales, $cost, $margin) ON DUPLICATE KEY UPDATE $mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin, $yquantityfield = $yquantityfield + $quantity, $ysalesfield = $ysalesfield + $sales, $ycostfield = $ycostfield + $cost, $ymarginfield = $ymarginfield + $margin";
				$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));


			}
			
			// dailysales is stored historcally now 
			
			// if ($yearmonth == $curyearmonth){

				// Get the day number of the date
		
				$dayno = date("d",strtotime($date));
			
				// The field name will be 'sales'<DD> where DD is the day number e.g. sales13
				
				$dsalesfield = "day".$dayno."sales";
				
				$insertquery = "INSERT INTO dailysales(branch,repcode,yearmonth,$dsalesfield) VALUES ($branch,'$repcode', $yearmonth, $sales) ON DUPLICATE KEY UPDATE $dsalesfield = $dsalesfield + $sales";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
			//}
		} // 		while ($row = mysqli_fetch_row($salesanalysisresult)) 
	} // 	while ($customerrow = mysqli_fetch_row($customerresult)) 

	
    // Update customer sales margin %'s
	
	$query = "UPDATE customersales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
	mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
	mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
	mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
	mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
	mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
	mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));
	
    // Update customer pac1 sales margin %'s
	
	$query = "UPDATE customerpac1sales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
	mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
	mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
	mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
	mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
	mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
	mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));
	
    // Update customer pac2 sales margin %'s
	
	$query = "UPDATE customerpac2sales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
	mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
	mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
	mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
	mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
	mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
	mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

    // Update customer pac3 sales margin %'s
	
	$query = "UPDATE customerpac3sales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
	mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
	mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
	mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
	mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
	mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
	mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

    // Update customer pac4 sales margin %'s
	
	$query = "UPDATE customerpac4sales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
	mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
	mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
	mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
	mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
	mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
	mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

    // Update customer product sales margin %'s
	
	$query = "UPDATE customerprodsales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
	mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
	mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
	mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
	mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
	mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
	mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));	

    // Update product sales margin %'s
	
	$query = "UPDATE productsales SET mmarginpc0 = (mmargin0/msales0)*100, mmarginpc1 = (mmargin1/msales1)*100,mmarginpc2 = (mmargin2/msales2)*100,mmarginpc3 = (mmargin3/msales3)*100,mmarginpc4 = (mmargin4/msales4)*100,mmarginpc5 = (mmargin5/msales5)*100,
	mmarginpc6 = (mmargin6/msales6)*100, mmarginpc7 = (mmargin7/msales7)*100,mmarginpc8 = (mmargin8/msales8)*100,mmarginpc9 = (mmargin9/msales9)*100,mmarginpc10 = (mmargin10/msales10)*100,mmarginpc11 = (mmargin11/msales11)*100,
	mmarginpc12 = (mmargin12/msales12)*100,mmarginpc13 = (mmargin13/msales13)*100,mmarginpc14 = (mmargin14/msales14)*100,mmarginpc15 = (mmargin15/msales15)*100,mmarginpc16 = (mmargin16/msales16)*100,mmarginpc17 = (mmargin17/msales17)*100,mmarginpc18 = (mmargin18/msales18)*100,
	mmarginpc19 = (mmargin19/msales19)*100,mmarginpc20 = (mmargin20/msales20)*100,mmarginpc21 = (mmargin21/msales21)*100,mmarginpc22 = (mmargin22/msales22)*100,mmarginpc23 = (mmargin23/msales23)*100,
	mmarginpc24 = (mmargin24/msales24)*100,mmarginpc25 = (mmargin25/msales25)*100,mmarginpc26 = (mmargin26/msales26)*100,mmarginpc27 = (mmargin27/msales27)*100,mmarginpc28 = (mmargin28/msales28)*100,
	mmarginpc29 = (mmargin29/msales29)*100,mmarginpc30 = (mmargin30/msales30)*100,mmarginpc31 = (mmargin31/msales31)*100,mmarginpc32 = (mmargin32/msales32)*100,mmarginpc33 = (mmargin33/msales33)*100,
	mmarginpc34 = (mmargin34/msales34)*100,mmarginpc35 = (mmargin35/msales35)*100,ymarginpc0 = (ymargin0/ysales0)*100,ymarginpc1 = (ymargin1/ysales1)*100,ymarginpc2 = (ymargin2/ysales2)*100,ymarginpc3 = (ymargin3/ysales3)*100";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));		
?>
