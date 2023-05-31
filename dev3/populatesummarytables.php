<!-- This routine goes through the sales analysis and populates the sales summary tables -->
<!-- It loops through the customers, and then each customers sales analysis. This is to keep the result set size down as it was initially running out of memory -->

<?php
    require_once 'dblogin.php';	
	
	date_default_timezone_set('Europe/London');
	
	ini_set('log_errors',1);
	ini_set('display_errors',1);
	ini_set('error_log', 'error_log');
	ini_set('memory_limit', '1G');
	
	// Start time
	$start_datetime = date('Y-m-d H:i:s');

	error_reporting(E_ALL);	
	
	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or die ("Unable to connect!"); 

	// Enable autocommit
	mysqli_autocommit($link, TRUE);
	
	// Get the current yearmonth

	$query = "SELECT curyearmonth FROM system";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$row = mysqli_fetch_row($result);
	$curyearmonth = $row[0];
	
	$curyear = substr($curyearmonth,0,-2);
	$curmonth = substr($curyearmonth,-2);

	// Get the account to rebuild, if its been passed in
	
	$accountclause = "";
	
	if (isset($_GET['account'])) 
	{
		$parameteraccount = $_GET['account'];
		$accountclause = " WHERE account = '$parameteraccount'";
	}

	// Get the yearmonth to rebuild, if its been passed in
	if (isset($_GET['yearmonth'])) 
	{
		$parameteryearmonth = $_GET['yearmonth'];
		
		// Get the year and month of the parameter yearmonth
		$parameteryear  = substr($parameteryearmonth,0,4);
		$parametermonth = substr($parameteryearmonth,4,2);

		// Get the year and month of the current yearmonth
		$currentyear  = substr($curyearmonth,0,4);
		$currentmonth = substr($curyearmonth,4,2);

		// Determine how long ago this was
		$yeardiff  = $currentyear - $parameteryear;
		$monthdiff = $currentmonth - $parametermonth;
		
		// Calculate the age. The column names are like sales0, sales1, sales 2 etc. where 0 is the current month, 1 is current month -1, 2 is current month -2 etc.
		$fieldno = ($yeardiff * 12) + $monthdiff;
		
		$msalesfield    = "msales" . $fieldno;
		$mquantityfield = "mquantity" . $fieldno;
		$mcostfield     = "mcost" . $fieldno;
		$mmarginfield   = "mmargin" . $fieldno;
		$mmarginpcfield = "mmarginpc" . $fieldno;
		
		$customerpac1salesquery = "UPDATE customerpac1sales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$customerpac2salesquery = "UPDATE customerpac2sales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$customerpac3salesquery = "UPDATE customerpac3sales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$customerpac4salesquery = "UPDATE customerpac4sales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$customerprodsalesquery = "UPDATE customerprodsales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$customersalesquery = "UPDATE customersales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$productsalesquery = "UPDATE productsales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$pac1salesquery = "UPDATE pac1sales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$pac2salesquery = "UPDATE pac2sales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$pac3salesquery = "UPDATE pac3sales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$pac4salesquery = "UPDATE pac4sales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$repsalesquery = "UPDATE repsales SET $msalesfield = 0, $mquantityfield = 0, $mcostfield = 0, $mmarginfield = 0, $mmarginpcfield = 0";
		$dailysalesquery = "UPDATE dailysales SET day01sales = 0, day02sales = 0, day03sales = 0, day04sales = 0, day05sales = 0, day06sales = 0, day07sales = 0, day08sales = 0, day09sales = 0, day10sales = 0, day11sales = 0, day12sales = 0, day13sales = 0, day14sales = 0, day15sales = 0, day16sales = 0, day17sales = 0, day18sales = 0, day19sales = 0, day20sales = 0, day21sales = 0, day22sales = 0, day23sales = 0, day24sales = 0, day25sales = 0, day26sales = 0, day27sales = 0, day28sales = 0, day29sales = 0, day30sales = 0, day31sales = 0 WHERE yearmonth = $parameteryearmonth";
		$demandquery = "DELETE FROM demand WHERE year = $parameteryear AND period = $parametermonth";
	}
	else
	{
		$customerpac1salesquery = "TRUNCATE customerpac1sales";
		$customerpac2salesquery = "TRUNCATE customerpac2sales";
		$customerpac3salesquery = "TRUNCATE customerpac3sales";
		$customerpac4salesquery = "TRUNCATE customerpac4sales";
		$customerprodsalesquery = "TRUNCATE customerprodsales";
		$customersalesquery = "TRUNCATE customersales";
		$productsalesquery = "TRUNCATE productsales";
		$pac1salesquery = "TRUNCATE pac1sales";
		$pac2salesquery = "TRUNCATE pac2sales";
		$pac3salesquery = "TRUNCATE pac3sales";
		$pac4salesquery = "TRUNCATE pac4sales";
		$repsalesquery = "TRUNCATE repsales";
		$dailysalesquery = "TRUNCATE TABLE dailysales";
		$demandquery = "TRUNCATE TABLE demand";
	}
		
	$year0 = date("Y");
	$year1 = $year0 - 1;
	$year2 = $year1 - 1;
	$year3 = $year2 - 1;
	$month0 = date("m");
	
	$yearmonth0 = ($year0 * 100) + 1;
	$yearmonth1 = ($year1 * 100) + 1;
	$yearmonth2 = ($year2 * 100) + 1;
	$yearmonth3 = ($year3 * 100) + 1;

	$customerpac1salesresult = mysqli_query($link, $customerpac1salesquery) or die ("Error in query: $customerpac1salesquery. ".mysqli_error($link));
	$customerpac2salesresult = mysqli_query($link, $customerpac2salesquery) or die ("Error in query: $customerpac2salesquery. ".mysqli_error($link));
	$customerpac3salesresult = mysqli_query($link, $customerpac3salesquery) or die ("Error in query: $customerpac3salesquery. ".mysqli_error($link));
	$customerpac4salesresult = mysqli_query($link, $customerpac4salesquery) or die ("Error in query: $customerpac4salesquery. ".mysqli_error($link));
	$customerprodsalesresult = mysqli_query($link, $customerprodsalesquery) or die ("Error in query: $customerprodsalesquery. ".mysqli_error($link));
	$customersalesresult 	= mysqli_query($link, $customersalesquery) or die ("Error in query: $customersalesquery. ".mysqli_error($link));
	$productsalesresult 	= mysqli_query($link, $productsalesquery) or die ("Error in query: $productsalesquery. ".mysqli_error($link));
	$pac1salesresult 		= mysqli_query($link, $pac1salesquery) or die ("Error in query: $pac1salesquery. ".mysqli_error($link));
	$pac2salesresult 		= mysqli_query($link, $pac2salesquery) or die ("Error in query: $pac2salesquery. ".mysqli_error($link));
	$pac3salesresult 		= mysqli_query($link, $pac3salesquery) or die ("Error in query: $pac3salesquery. ".mysqli_error($link));
	$pac4salesresult 		= mysqli_query($link, $pac4salesquery) or die ("Error in query: $pac4salesquery. ".mysqli_error($link));
	$repsalesresult 		= mysqli_query($link, $repsalesquery) or die ("Error in query: $repsalesquery. ".mysqli_error($link));
	$dailysalesresult 		= mysqli_query($link, $dailysalesquery) or die ("Error in query: $dailysalesquery. ".mysqli_error($link));
	$demandresult 			= mysqli_query($link, $demandquery) or die ("Error in query: $demandquery. ".mysqli_error($link));

	// This outer loop is for 35 months, starting with the current month, going back
	
	$searchmonth = $curmonth;
	$searchyear = $curyear;
	$searchyearmonth = ($searchyear * 100) + $searchmonth;
	

	$yearmonthclause = "";
	
	// If the yearmonth has been passed in, only select data for that period and only going round the loop once
	if (isset($_GET['yearmonth'])) 
	{
		$searchyearmonth = $parameteryearmonth;
		$looplimit = 1;
	}
	else
	{
		$looplimit = 36;
		$searchyearmonth = $curyearmonth;
	}

	for($z = 0;$z < $looplimit;$z++)
	{	
		// If not the first time round the loop, recalculate the year month
		
		if(!$z == 0)
		{
			--$searchmonth;
			if($searchmonth == 0) 
			{
				$searchmonth = 12;
				--$searchyear;
			}
			$searchyearmonth = ($searchyear * 100) + $searchmonth;
		}
				
		$salesanalysisquery = "SELECT sa.prodcode, sa.curpac1code, sa.curpac2code, sa.curpac3code, sa.curpac4code, sa.yearmonth, sa.quantity, sa.sales, sa.cost, sa.branch, sa.date, sa.repcode, cu.account, cu.repcode, cu.accounttype FROM salesanalysis AS sa LEFT JOIN customer AS cu ON cu.account = sa.account WHERE sa.yearmonth = $searchyearmonth ORDER BY account";

		$salesanalysisresult = mysqli_query($link, $salesanalysisquery) or die ("Error in query: $salesanalysisquery. ".mysqli_error($link));
		$numrows = mysqli_num_rows($salesanalysisresult);
		
		$rowcount = 0;
		
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
			$account   		= $row[12];
			$custrepcode    = $row[13];
			$accounttype    = $row[14];

			$rowcount++;
			
			echo $searchyearmonth."-".$rowcount."/".$numrows." ";	//debug
			$margin    = $sales - $cost;

			// If the rep code is blank, give it a value because the SQL fails if its blank
			
			if ($custrepcode == "") $custrepcode = "9999";
			
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
			$mmarginpcfield = "mmarginpc" . $fieldno;

			// ------------------------------------------------------------------------------------------------------------------------------
			// UPDATE THE DEMAND HISTORY
			// ------------------------------------------------------------------------------------------------------------------------------

			$extdemandqty = 0;
			$extdemandnum = 0;
			$extdemandval = 0;
			
			$intdemandqty = 0;
			$intdemandnum = 0;
			$intdemandval = 0;

			$year   = date("Y", strtotime($date));
			$week   = date("W", strtotime($date));		
			$month  = date("n", strtotime($date));		
			
			if ($accounttype == "A") {
				$extdemandqty = $quantity;
				$extdemandnum = 1;
				$extdemandval = $cost;
			}

			if ($accounttype == "I") {
				$intdemandqty = $quantity;
				$intdemandnum = 1;
				$intdemandval = $cost;
			}			

			$demandmonthquery = "INSERT INTO demand(productcode, branch, year, period, type, forecastdemandqty, demandqty, demandnum, demandval, extdemandqty, extdemandnum, extdemandval, intdemandqty, intdemandnum, intdemandval) VALUES('$product',$branch, $year, $month, 'M', 0, $quantity, 1, $cost, $extdemandqty, $extdemandnum, $extdemandval, $intdemandqty, $intdemandnum, $intdemandval) ON DUPLICATE KEY UPDATE demandqty = demandqty + $quantity, demandnum = demandnum + 1, demandval = demandval + $cost, extdemandqty = extdemandqty + $extdemandqty, extdemandnum = extdemandnum + $extdemandnum, extdemandval = extdemandval + $extdemandval, intdemandqty = intdemandqty + $intdemandqty, intdemandnum = intdemandnum + $intdemandnum, intdemandval = intdemandval + $intdemandval"; 

			$demandmonthresult = mysqli_query($link, $demandmonthquery) or die ("Error in query: $demandmonthquery. ".mysqli_error($link));
			
			// Only update if the age is <= 3

			if ($fieldno >= 0 AND $fieldno <= 35) {	

				// Customer Sales		
				// 12/11/2018 Putting $custrepcode into the file rather than the rep code from the trx. This is to prevent duplicate rows 
				
				$insertfields = "$mquantityfield, $msalesfield, $mcostfield, $mmarginfield";
				$insertvalues = "$quantity, $sales, $cost, $margin";
				$odkupdates = "$mquantityfield = $mquantityfield + $quantity, $msalesfield = $msalesfield + $sales, $mcostfield = $mcostfield + $cost, $mmarginfield = $mmarginfield + $margin";
				
				$insertquery = "INSERT INTO customersales(account, repcode, $insertfields) VALUES ('$account', '$custrepcode', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Customer Product Sales		
				$insertquery = "INSERT INTO customerprodsales(account, repcode, prodcode, $insertfields) VALUES ('$account', '$custrepcode', '$product', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Customer PAC1 Sales		
				$insertquery = "INSERT INTO customerpac1sales(account, repcode, pac1code, $insertfields) VALUES ('$account', '$custrepcode', '$pac1', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Customer PAC2 Sales		
				$insertquery = "INSERT INTO customerpac2sales(account, repcode, pac2code, $insertfields) VALUES ('$account', '$custrepcode', '$pac2', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Customer PAC3 Sales		
				$insertquery = "INSERT INTO customerpac3sales(account, repcode, pac3code, $insertfields) VALUES ('$account', '$custrepcode', '$pac3', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Customer PAC4 Sales		
				$insertquery = "INSERT INTO customerpac4sales(account, repcode, pac4code, $insertfields) VALUES ('$account', '$custrepcode', '$pac4', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Product Sales		
				// 12/11/2018 Putting $custrepcode into the file rather than the rep code from the trx. This is to prevent duplicate rows 

				$insertquery = "INSERT INTO productsales(prodcode, branch, repcode, $insertfields) VALUES ('$product', $branch, '$custrepcode', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
				
				// PAC1 Sales		
				// 12/11/2018 Putting $custrepcode into the file rather than the rep code from the trx. This is to prevent duplicate rows 

				$insertquery = "INSERT INTO pac1sales(pac1code, branch, repcode, $insertfields) VALUES ('$pac1', $branch, '$custrepcode', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// PAC2 Sales		
				// 12/11/2018 Putting $custrepcode into the file rather than the rep code from the trx. This is to prevent duplicate rows

				$insertquery = "INSERT INTO pac2sales(pac2code, branch, repcode, $insertfields) VALUES ('$pac2', $branch, '$custrepcode', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
				
				// PAC3 Sales		
				// 12/11/2018 Putting $custrepcode into the file rather than the rep code from the trx. This is to prevent duplicate rows 

				$insertquery = "INSERT INTO pac3sales(pac3code, branch, repcode, $insertfields) VALUES ('$pac3', $branch, '$custrepcode', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// PAC4 Sales		
				// 12/11/2018 Putting $custrepcode into the file rather than the rep code from the trx. This is to prevent duplicate rows 

				$insertquery = "INSERT INTO pac4sales(pac4code, branch, repcode, $insertfields) VALUES ('$pac4', $branch, '$custrepcode', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));

				// Rep Sales		
				$insertquery = "INSERT INTO repsales(branch, repcode, $insertfields) VALUES ($branch, '$custrepcode', $insertvalues) ON DUPLICATE KEY UPDATE $odkupdates";
				$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));
			}

			// Get the day number of the date

			$dayno = date("d",strtotime($date));
		
			// The field name will be 'sales'<DD> where DD is the day number e.g. sales13
			
			$dsalesfield = "day".$dayno."sales";
			
			$insertquery = "INSERT INTO dailysales(branch,repcode,yearmonth,$dsalesfield) VALUES ($branch,'$custrepcode', $yearmonth, $sales) ON DUPLICATE KEY UPDATE $dsalesfield = $dsalesfield + $sales";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		
		} // 		while ($row = mysqli_fetch_row($salesanalysisresult)) 
	}	// 		for($z = 0;$z <= $looplimit;$z++)

	// ------------------------------------------------------------------------------------------------------------------------------
	// MARGIN % FIELDS
	// ------------------------------------------------------------------------------------------------------------------------------

	$marginstr = "";
	
	for($x = 0; $x < 36; $x++)
	{
		$marginstr = $marginstr."mmarginpc$x = IFNULL(( mmargin$x / msales$x ) * 100,0),";
	}

	// ------------------------------------------------------------------------------------------------------------------------------
	// YEAR0 FIELDS
	// ------------------------------------------------------------------------------------------------------------------------------

	// Strings to update the yearly fields. Updating all of them as its easier than trying to figure out which ones have changed.
	// For example ysales0 = msales0 + msales1 + msales2 etc.
	
	$yearstr = "ysales0 = ";
	
	for($x = 0; $x < $curmonth; $x++)
	{
		$yearstr .= "msales$x";
		if($x < $curmonth - 1) $yearstr .= " + ";// only put the + in if its not the last column
	}
	
	// Now add the cost columns
	// For example ysales0 = msales0 + msales1 + msales2, ycost0 = mcost0 + mcost1 + mcost2 etc.
	
	$yearstr = $yearstr.", ycost0 = ";
	
	for($x = 0; $x < $curmonth; $x++)
	{
		$yearstr = $yearstr."mcost$x";
		if($x < $curmonth - 1) $yearstr = $yearstr." + ";// only put the + in if its not the last column
	}	

	// Now add the quantity columns
	// For example ysales0 = msales0 + msales1 + msales2, ycost0 = mcost0 + mcost1 + mcost2, yquantity0 = mquantity0 + mquantity1 + mquantity2 etc.
	
	$yearstr = $yearstr.", yquantity0 = ";
	
	for($x = 0; $x < $curmonth; $x++)
	{
		$yearstr = $yearstr."mquantity$x";
		if($x < $curmonth - 1) $yearstr = $yearstr." + ";// only put the + in if its not the last column
	}	

	$yearstr = $yearstr.",";

	// ------------------------------------------------------------------------------------------------------------------------------
	// YEAR1 FIELDS
	// ------------------------------------------------------------------------------------------------------------------------------
	
	$yearstr = $yearstr."ysales1 = ";
	
	for($x = intval($curmonth); $x < $curmonth + 12; $x++)
	{
		$yearstr = $yearstr."msales$x";
		if($x < $curmonth + 11) $yearstr = $yearstr." + ";// only put the + in if its not the last column
	}
		
	$yearstr = $yearstr.", ycost1 = ";
	
	for($x = intval($curmonth); $x < $curmonth + 12; $x++)
	{
		$yearstr = $yearstr."mcost$x";
		if($x < $curmonth + 11) $yearstr = $yearstr." + ";// only put the + in if its not the last column
	}	
	
	$yearstr = $yearstr.", yquantity1 = ";
	
	for($x = intval($curmonth); $x < $curmonth + 12; $x++)
	{
		$yearstr = $yearstr."mquantity$x";
		if($x < $curmonth + 11) $yearstr = $yearstr." + ";// only put the + in if its not the last column
	}	

	$yearstr = $yearstr.",";

	// ------------------------------------------------------------------------------------------------------------------------------
	// YEAR2 FIELDS
	// ------------------------------------------------------------------------------------------------------------------------------
	
	$yearstr = $yearstr."ysales2 = ";
	
	for($x = intval($curmonth) + 12; $x < $curmonth + 23; $x++)
	{
		$yearstr = $yearstr."msales$x";
		if($x < $curmonth + 22) $yearstr = $yearstr." + ";// only put the + in if its not the last column
	}
		
	$yearstr = $yearstr.", ycost2 = ";
	
	for($x = intval($curmonth) + 12; $x < $curmonth + 23; $x++)
	{
		$yearstr = $yearstr."mcost$x";
		if($x < $curmonth + 22) $yearstr = $yearstr." + ";// only put the + in if its not the last column
	}	
	
	$yearstr = $yearstr.", yquantity2 = ";
	
	for($x = intval($curmonth) + 12; $x < $curmonth + 23; $x++)
	{
		$yearstr = $yearstr."mquantity$x";
		if($x < $curmonth + 22) $yearstr = $yearstr." + ";// only put the + in if its not the last column
	}	

	$yearstr = $yearstr.",";

	// ------------------------------------------------------------------------------------------------------------------------------
	// YEAR3 FIELDS
	// ------------------------------------------------------------------------------------------------------------------------------
	
	$yearstr = $yearstr."ysales3 = ";
	
	for($x = intval($curmonth) + 24; $x < 36; $x++)
	{
		$yearstr = $yearstr."msales$x";
		if($x < 35) $yearstr = $yearstr." + ";// only put the + in if its not the last column
	}
		
	$yearstr = $yearstr.", ycost3 = ";
	
	for($x = intval($curmonth) + 24; $x < 36; $x++)
	{
		$yearstr = $yearstr."mcost$x";
		if($x < 35) $yearstr = $yearstr." + ";// only put the + in if its not the last column
	}	
	
	$yearstr = $yearstr.", yquantity3 = ";
	
	for($x = intval($curmonth) + 24; $x < 36; $x++)
	{
		$yearstr = $yearstr."mquantity$x";
		if($x < 35) $yearstr = $yearstr." + ";// only put the + in if its not the last column
	}	

	$yearstr = $yearstr.",";


	// ------------------------------------------------------------------------------------------------------------------------------
	// MARGIN FIELDS
	// ------------------------------------------------------------------------------------------------------------------------------
	
	$yearstr = $yearstr." ymargin0 = ysales0 - ycost0,  ymargin1 = ysales1 - ycost1,  ymargin2 = ysales2 - ycost2,  ymargin3 = ysales3 - ycost3, ymarginpc0 = IFNULL((ymargin0/ysales0) * 100,0),ymarginpc1 = IFNULL((ymargin1/ysales1) * 100,0), ymarginpc2 = IFNULL((ymargin2/ysales2) * 100,0), ymarginpc3 = IFNULL((ymargin3/ysales3) * 100,0)";
	
    // Update customer sales margin %'s

	$querystr = $marginstr.$yearstr;
	
	$query = "UPDATE customersales SET $querystr";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));
	
    // Update customer pac1 sales margin %'s
	
	$query = "UPDATE customerpac1sales SET $querystr";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));
	
    // Update customer pac2 sales margin %'s
	
	$query = "UPDATE customerpac2sales SET $querystr";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

    // Update customer pac3 sales margin %'s
	
	$query = "UPDATE customerpac3sales SET $querystr";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

    // Update customer pac4 sales margin %'s
	
	$query = "UPDATE customerpac4sales SET $querystr";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

    // Update customer product sales margin %'s
	
	$query = "UPDATE customerprodsales SET $querystr";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));	

    // Update product sales margin %'s
	
	$query = "UPDATE productsales SET $querystr";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

    // Update PAC1 sales margin %'s
	
	$query = "UPDATE pac1sales SET $querystr";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

    // Update PAC2 sales margin %'s
	
	$query = "UPDATE pac2sales SET $querystr";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));
	
    // Update PAC3 sales margin %'s
	
	$query = "UPDATE pac3sales SET $querystr";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

    // Update PAC4 sales margin %'s
	
	$query = "UPDATE pac4sales SET $querystr";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));
	
	// End time and duration and write to the logfile table
	$end_datetime = date('Y-m-d H:i:s');
	$duration = strtotime($end_datetime) - strtotime($start_datetime);
	$minutes = floor($duration / 60);
	$seconds = $duration % 60;
	
	$filename = basename(__FILE__);
	$batch = date('YmdHi'); 

	$query = "INSERT INTO logfile(id, batch, application, started, ended, duration) VALUES (0, '$batch', '$filename', '$start_datetime', '$end_datetime', $duration)";
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

	$logfile = "logfile.txt";
	$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
	$stringData = $start_datetime." ".$filename." ".$minutes." min(s) ".$seconds." sec(s)\n";
	fwrite($fh, $stringData);
	fclose($fh);	

	echo " All DONE!";
	
	function logerror($query,$error)
	{
		$email = "kieran" . "@" . "kk-cs" . ".co.uk";
		
		$errormsg = "Hi Kieran, an error just occurred in ".__FILE__.". The query was '".$query."' and the error was '".$error."'<BR>";
		
		error_log($errormsg);
		error_log($errormsg,1,$email);
		
		return true;
	}		
?>
