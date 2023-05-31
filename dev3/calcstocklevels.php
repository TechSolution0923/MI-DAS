<!-- This script calculates the Mean Absolute Deviation (MAD), Safety Stock (SS) and Review Level (RL) for stock items

Steps:
1. Loops through the branch stock items
2. For each stock item, get this year's and previous 3 year's demand history
3. Average the last 12 months demand
4. Go through the demand history again, this time calculating the absolute deviation between the demand and the average
5. Average the deviation to get the MAD
6. Calculate the Safety Stock (SS) 
7. Calculate the Review Level (RL)
8. update the stock item

PARAMETERS
==========
auth		Is this an authorised run of this script Y/N ** THIS IS REQUIRED **
stockitem	Stock item code (optional)
branch		Branch number (optional)
debug		Display debug messages Y/N (optional)

To run this with full options, use a URL like this: calcMAD.php?auth=Y&stockitem=K00003&branch=1&debug=Y 
-->


<?php
    require_once 'dblogin.php';	
	
	date_default_timezone_set('Europe/London');

	// Start time
	$start_datetime = date('Y-m-d H:i:s');

	if (!isset($_GET['auth'])) 
	{
		$filename = basename(__FILE__);
		$logfile = "logfile.txt";
		$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
		$stringData = $start_datetime." ".$filename." Unauthorised access from ".$_SERVER['REMOTE_ADDR']."\n";
		fwrite($fh, $stringData);
		fclose($fh);	
		exit("You are not authorised to run this script");
	}
	
	$stockitemclause = "";
	$stockitemclause1 = "";
	
	// Get the stock item code, if its been passed in
	if (isset($_GET['stockitem'])) 
	{
		$p_stockitemcode = $_GET['stockitem'];
		$stockitemclause = " AND prodcode = '$p_stockitemcode'";
		$stockitemclause1 = " WHERE code = '$p_stockitemcode'";
	}
	
	$branchclause = "";
	$branchclause1 = "";
	
	// Get the branch number, if its been passed in
	if (isset($_GET['branch'])) 
	{
		$p_branch = $_GET['branch'];
		$branchclause = " AND branch = $p_branch";
		$branchclause1 = " WHERE branch = $p_branch";
	}	
	
	// Get the debug flag, if its been passed in
	if (isset($_GET['debug'])) 
	{
		$p_debug = $_GET['debug'];
	}	
	
	$stockitemwhereclause = "";
	
	if (!$p_stockitemcode == "" AND !$p_branch == "") $stockitemwhereclause = " WHERE prodcode = '$p_stockitemcode' AND branch = $p_branch ";
	if (!$p_stockitemcode == "" AND $p_branch == "") $stockitemwhereclause = " WHERE prodcode = '$p_stockitemcode' ";

	//if($p_debug == "Y") echo __LINE__." stock item parameter: ".$p_stockitemcode."<br />\n";
	//if($p_debug == "Y") echo __LINE__." branch parameter: ".$p_branch."<br />\n";
	//if($p_debug == "Y") echo __LINE__." stock item where clause: ".$stockitemwhereclause."<br />\n";
	
	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or die ("Unable to connect!"); 
	
	// Get the current year and previous years
	
	$systemquery = "SELECT curyearmonth FROM system";
	$systemresult = mysqli_query($link, $systemquery) or logerror(__LINE__." ".$systemquery,mysqli_error($link));
	$systemrow = mysqli_fetch_row($systemresult);
	$curyearmonth = $systemrow[0];
	
	$curyear = substr($curyearmonth,0,4);
	$year1 = $curyear - 1;
	$year2 = $curyear - 2;
	$year3 = $curyear - 3;
	$curmonth = substr($curyearmonth,4,2);

	$curyeardemand = array();
	$year1demand = array();
	$year2demand = array();
	$year3demand = array();
	$periodaverage = array();
	$devfromaverage = array();
	
	$numyears = array(); // Number of years with demand for each period
	
	// To store the demand with trend removed
	
	$curyearnotrend = array();
	$year1notrend = array();
	$year2notrend = array();
	$year3notrend = array();
	$periodaveragenotrend = array();
	$devfromavgnotrend = array();
	$monthlymadnotrend = array();
	$deseasonalisedmadnotrend = array();

	// To store the deseasonalised demand
	
	$curyearnoseason = array();
	$year1noseason = array();
	$year2noseason = array();
	$year3noseason = array();
	
	// To store the forecast demand details
	
	$forecastyear = array();
	$forecastperiod = array();
	$forecastdemand = array();

	$baseseries = array();
	
	// Get the system CSF settings. These are the %'s. Will need to get the MAD multiplier later, depending on the ABC class
	
	$imsystemquery = "SELECT acsf, bcsf, ccsf,seasonalityprofilelevel FROM imsystem";
	$imsystemresult = mysqli_query($link, $imsystemquery) or logerror(__LINE__." ".$imsystemquery,mysqli_error($link));
	$imsystemrow = mysqli_fetch_row($imsystemresult);
	
	$acsf = $imsystemrow[0];
	$bcsf = $imsystemrow[1];
	$ccsf = $imsystemrow[2];
	$seasonalityprofilelevel = $imsystemrow[3];
	
	// Get the stock items
	
	$stockitemquery = "SELECT stock.branch, stock.prodcode, stock.leadtime, stock.abcclass, supplier.leadtime, product.pac1code, product.pac2code, product.pac3code, product.pac4code FROM stock LEFT JOIN supplier ON supplier.account = stock.stocksuppcode LEFT JOIN product ON product.code = stock.prodcode $stockitemwhereclause ORDER BY branch, prodcode";
	$stockitemresult = mysqli_query($link, $stockitemquery) or logerror(__LINE__." ".$stockitemquery,mysqli_error($link));

	while ($stockitemrow = mysqli_fetch_row($stockitemresult)) 
	{	
		$branch 		= $stockitemrow[0];
		$productcode 	= $stockitemrow[1]; 
		$stockleadtime  = $stockitemrow[2];
		$abcclass       = $stockitemrow[3];
		$supplierleadtime = $stockitemrow[4];
		$pac1code		= $stockitemrow[5];
		$pac2code		= $stockitemrow[6];
		$pac3code		= $stockitemrow[7];
		$pac4code		= $stockitemrow[8];
		
		// Set the lead time
		
		$leadtime = $stockleadtime;
		
		if($stockleadtime == 0) $leadtime = $supplierleadtime;

		// Get the seasonality profile
		
		switch ($seasonalityprofilelevel)
		{
			case 1:
				$tabname = "pac1seasonality";
				$colname = "pac1code";
				$paccode = $pac1code;
				break;
			case 2:
				$tabname = "pac2seasonality";
				$colname = "pac2code";
				$paccode = $pac2code;
				break;
			case 3:
				$tabname = "pac3seasonality";
				$colname = "pac3code";
				$paccode = $pac3code;
				break;
			case 4:
				$tabname = "pac4seasonality";
				$colname = "pac4code";
				$paccode = $pac4code;
				break;
		}

		// ----------------------------------------------------------------------------------------------------------------
		// GET SEASONALITY BASE SERIES
		// ----------------------------------------------------------------------------------------------------------------
		
		$seasonalityquery = "SELECT baseseries FROM $tabname WHERE branch = $branch AND $colname = '$paccode' ORDER BY period";
		$seasonalityresult = mysqli_query($link, $seasonalityquery) or die ("Error in query: $seasonalityquery. ".mysqli_error($link));

		$x = 1;
		
		while ($seasonalityrow = mysqli_fetch_row($seasonalityresult)) 
		{	
			$baseseries[$x] = $seasonalityrow[0];
			$x++;
		}
		
		// Initialize the arrays with -999999 so we can tell the difference between no data and no demand
		
		for($x=1;$x<=12;$x++)
		{
			$curyeardemand[$x] = -999999;
			$year1demand[$x] = -999999;
			$year2demand[$x] = -999999;
			$year3demand[$x] = -999999;
			$periodaverage[$x] = 0;
			$devfromaverage[$x] = 0;
			$numyears[$x] = 0;
			$curyearnotrend[$x] = -999999;
			$year1notrend[$x] = -999999;
			$year2notrend[$x] = -999999;
			$year3notrend[$x] = -999999;
			$periodaveragenotrend[$x] = 0;
		}

		if($p_debug == "Y") echo __LINE__."Stock Item: $productcode PAC: $paccode <br />\n";

		// Get the demand - this years and previous 3 years
		
		$demandquery = "SELECT year, period, demandqty FROM demand WHERE branch = $branch AND productcode = '$productcode' AND year >= $year3 AND (year * 100) + period < $curyearmonth";
		$demandresult = mysqli_query($link, $demandquery) or logerror(__LINE__." ".$demandquery,mysqli_error($link));	

		$totdemandqty = 0;
				
		while ($demandrow = mysqli_fetch_row($demandresult))
		{
			$year      = $demandrow[0];
			$period    = $demandrow[1];
			$demandqty = $demandrow[2];	
			$totdemandqty += $demandqty;
		
			// Put the demand into the relevant array
			
			switch($year)
			{
				case $curyear:
					$curyeardemand[$period] = $demandqty;
					break;
				case $year1:
					$year1demand[$period] = $demandqty;
					break;
				case $year2:
					$year2demand[$period] = $demandqty;
					break;
				case $year3:
					$year3demand[$period] = $demandqty;
					break;
			}			
		}

		// Average each period in each year, where the demand quantity isn't -999999
		
		$numperiodscuryear = 0; // Number of periods in the year with demand
		$numperiodsyear1 = 0;
		$numperiodsyear2 = 0;
		$numperiodsyear3 = 0;

		$curyeardemandtotal = 0;
		$year1demandtotal = 0;
		$year2demandtotal = 0;
		$year3demandtotal = 0;

		$curyeardemandavg = 0;
		$year1demandavg = 0;
		$year2demandavg = 0;
		$year3demandavg = 0;
		
		$curyeartrendfactor = 0;
		$year1trendfactor = 0;
		$year2trendfactor = 0;
		$year3trendfactor = 0;
		
		for($x=1;$x<=12;$x++)
		{
			// Count the number of years with demand data, increment the period total and calculate the period average
			
			$numyears[$x] = 0;
			$periodtotal = 0;
			
			if($curyeardemand[$x] <> -999999) 
			{
				$numyears[$x]++;
				$numperiodscuryear++;
				$periodtotal += $curyeardemand[$x];
				$curyeardemandtotal += $curyeardemand[$x];
			}
			
			if($year1demand[$x] <> -999999) 
			{
				$numyears[$x]++;
				$numperiodsyear1++;
				$periodtotal += $year1demand[$x];
				$year1demandtotal += $year1demand[$x];
			}

			if($year2demand[$x] <> -999999) 
			{
				$numyears[$x]++;
				$numperiodsyear2++;
				$periodtotal += $year2demand[$x];
				$year2demandtotal += $year2demand[$x];
			}

			if($year3demand[$x] <> -999999) 
			{
				$numyears[$x]++;
				$numperiodsyear3++;
				$periodtotal += $year3demand[$x];
				$year3demandtotal += $year3demand[$x];
			}

			if(!$numyears[$x] == 0) $periodaverage[$x] = $periodtotal / $numyears[$x];
		}

		$curyeardemandavg = $curyeardemandtotal / $numperiodscuryear;
		$year1demandavg = $year1demandtotal / $numperiodsyear1;
		$year2demandavg = $year2demandtotal / $numperiodsyear2;
		$year3demandavg = $year3demandtotal / $numperiodsyear3;

		//if($p_debug == "Y") echo __LINE__." Period: ".$x."<br />\n";
		//if($p_debug == "Y") echo __LINE__." Num Years: ".$numyears."<br />\n";
		//if($p_debug == "Y") echo __LINE__." Num Periods Cur Year: ".$numperiodscuryear."<br />\n";
		//if($p_debug == "Y") echo __LINE__." Num Periods Year 1: ".$numperiodsyear1."<br />\n";
		//if($p_debug == "Y") echo __LINE__." Num Periods Year 2: ".$numperiodsyear2."<br />\n";
		//if($p_debug == "Y") echo __LINE__." Num Periods Year 3: ".$numperiodsyear3."<br />\n";

		// Calculate total average
		
		$totalaverage = 0;

		for($x=1;$x<=12;$x++)
		{
			$totalaverage += $periodaverage[$x];
		}
		
		$totalaverage = $totalaverage / 12;
		
		//if($p_debug == "Y") echo __LINE__." curyeardemand: ".print_r($curyeardemand)."<br />\n";
		//if($p_debug == "Y") echo __LINE__." Year1demand: ".print_r($year1demand)."<br />\n";
		//if($p_debug == "Y") echo __LINE__." Year2demand: ".print_r($year2demand)."<br />\n";
		//if($p_debug == "Y") echo __LINE__." Year3demand: ".print_r($year3demand)."<br />\n";
		if($p_debug == "Y") echo __LINE__." Period Average: ".print_r($periodaverage)."<br />\n";
		if($p_debug == "Y") echo __LINE__." Total Average: $totalaverage <br />\n";
		
		// Get the deviation from average for each period
		
		$totdevfromaverage = 0;
		
		for($x=1;$x<=12;$x++)
		{
			$devfromaverage[$x] = ABS($periodaverage[$x] - $totalaverage);
			$totdevfromaverage += $devfromaverage[$x];
			
			//if($p_debug == "Y") echo __LINE__." Period: $x | $year3 Demand: $year3demand[$x] | $year2 Demand: $year2demand[$x] | $year1 Demand: $year1demand[$x] | $curyear Demand: $curyeardemand[$x] | Period Total: $periodtotal | Period Average: $periodaverage[$x] | Dev. From Average: $devfromaverage[$x] <br />\n";
		}

		//if($p_debug == "Y") echo __LINE__." $year3 Average: $year3demandavg | $year2 Average: $year2demandavg | $year1 Average: $year1demandavg | $curyear Average: $curyeardemandavg <br />\n";
		
		// ----------------------------------------------------------------------------------------------------------------
		// CALCULATE MAD
		// ----------------------------------------------------------------------------------------------------------------
		
		$MAD = $totdevfromaverage / 12;
		
		//if($p_debug == "Y") echo __LINE__." Dev. From Average: ".print_r($devfromaverage)."<br />\n";
		//if($p_debug == "Y") echo __LINE__." MAD: ".$MAD."<br />\n";

		// ----------------------------------------------------------------------------------------------------------------
		// CALCULATE SAFETY STOCK - ONLY IF ABC CLASS ISN'T X
		// ----------------------------------------------------------------------------------------------------------------

		$safetystock = 0;
		
		if($abcclass <> "X")
		{
			// Get the appropriate CSF % and then the MAD multiplier
		
			switch($abcclass)
			{
				case "A":
					$csf = $acsf;
					break;
				case "B":
					$csf = $bcsf;
					break;
				case "C":
					$csf = $ccsf;
					break;
			}
			
			$csfquery = "SELECT multiplyMAD FROM customerservicefactors WHERE servicelevelpc = $csf";
			$csfresult = mysqli_query($link, $csfquery) or logerror(__LINE__." ".$csfquery,mysqli_error($link));
			$csfrow = mysqli_fetch_row($csfresult);
			
			$madmultiplier = $csfrow[0];
			
			$safetystock = $MAD * $madmultiplier * SQRT($leadtime/30); // Dividing leadtime by 30 to bring it to months
		}
		
		// ----------------------------------------------------------------------------------------------------------------
		// CALCULATE REVIEW LEVEL
		// ----------------------------------------------------------------------------------------------------------------
		
		$reviewlevel = ($totalaverage / 30) * $leadtime + $safetystock;

		//if($p_debug == "Y") echo __LINE__." ABC Class: $abcclass A CSF: $acsf B CSF: $bcsf C CSF: $ccsf CSF: $csf MAD Multiplier: $madmultiplier Average Demand: $totalaverage MAD: $MAD Leadtime: $leadtime Safety Stock: $safetystock Review Level: $reviewlevel<br />\n";

		// ----------------------------------------------------------------------------------------------------------------
		// CALCULATE TREND FACTORS
		// ----------------------------------------------------------------------------------------------------------------

		$year1trendfactor = 0;
		$year2trendfactor = 0;
		$year3trendfactor = 0;
		
		$curyeartrendfactor = 1;
		if ($year1demandavg <> 0) $year1trendfactor = $curyeardemandavg / $year1demandavg;
		if ($year2demandavg <> 0) $year2trendfactor = $curyeardemandavg / $year2demandavg;
		if ($year13emandavg <> 0) $year3trendfactor = $curyeardemandavg / $year3demandavg;

		//if($p_debug == "Y") echo __LINE__." $year3 Trend Factor: $year3trendfactor | $year2 Trend Factor: $year2trendfactor | $year1 Trend Factor: $year1trendfactor | $curyear Trend Factor: $curyeartrendfactor <br />\n";

		// ----------------------------------------------------------------------------------------------------------------
		// CALCULATE DEMAND WITHOUT TREND
		// ----------------------------------------------------------------------------------------------------------------

		for($x=1;$x<=12;$x++)
		{	
			$curyearnotrend[$x] = $curyeardemand[$x];
			if($year1demand[$x] <> -999999) $year1notrend[$x] = $year1demand[$x] * $year1trendfactor;
			if($year2demand[$x] <> -999999) $year2notrend[$x] = $year2demand[$x] * $year2trendfactor;
			if($year3demand[$x] <> -999999) $year3notrend[$x] = $year3demand[$x] * $year3trendfactor;
		}

		// ----------------------------------------------------------------------------------------------------------------
		// CALCULATE MONTHLY MAD WITHOUT TREND
		// ----------------------------------------------------------------------------------------------------------------

		// Some values are available from previous MAD calcs such as number of years with demand in the period and number of periods with demand in the year
		
		$curyeartotalnotrend = 0;
		$year1totalnotrend = 0;
		$year2totalnotrend = 0;
		$year3totalnotrend = 0;
		
		for($x=1;$x<=12;$x++)
		{
			$periodtotal = 0;
			
			if($curyearnotrend[$x] <> -999999) 
			{
				$periodtotal += $curyearnotrend[$x];
				$curyeartotalnotrend += $curyearnotrend[$x];
			}
			
			if($year1notrend[$x] <> -999999) 
			{
				$periodtotal += $year1notrend[$x];
				$year1totalnotrend += $year1notrend[$x];
			}

			if($year2notrend[$x] <> -999999) 
			{
				$periodtotal += $year2notrend[$x];
				$year2totalnotrend += $year2notrend[$x];
			}

			if($year3notrend[$x] <> -999999) 
			{
				$periodtotal += $year3notrend[$x];
				$year3totalnotrend += $year3notrend[$x];
			}

			if(!$numyears[$x] == 0) $periodaveragenotrend[$x] = $periodtotal / $numyears[$x];
		}		

		$curyearavgnotrend = $curyeartotalnotrend / $numperiodscuryear;
		$year1avgnotrend = $year1totalnotrend / $numperiodsyear1;
		$year2avgnotrend = $year2totalnotrend / $numperiodsyear2;
		$year3avgnotrend = $year3totalnotrend / $numperiodsyear3;

		// Calculate total average - no trend
		
		$totalaveragenotrend = 0;

		for($x=1;$x<=12;$x++)
		{
			$totalaveragenotrend += $periodaveragenotrend[$x];
		}
		
		$totalaveragenotrend = $totalaveragenotrend / 12;
		
		// Monthly MAD - No Trend
		
		$totdevfromaveragenotrend = 0;
		
		for($x=1;$x<=12;$x++)
		{
			if($curyearnotrend[$x] <> -999999) $devfromavgnotrend[$x] += ABS($curyearnotrend[$x] - $periodaveragenotrend[$x]);
			if($year1notrend[$x] <> -999999) $devfromavgnotrend[$x] += ABS($year1notrend[$x] - $periodaveragenotrend[$x]);
			if($year2notrend[$x] <> -999999) $devfromavgnotrend[$x] += ABS($year2notrend[$x] - $periodaveragenotrend[$x]);
			if($year3notrend[$x] <> -999999) $devfromavgnotrend[$x] += ABS($year3notrend[$x] - $periodaveragenotrend[$x]);
			
			$monthlymadnotrend[$x] = $devfromavgnotrend[$x] / $numyears[$x];
			
			$deseasonalisedmadnotrend[$x] = $monthlymadnotrend[$x] * $baseseries[$x];
						
			//if($p_debug == "Y") echo __LINE__." Period: $x | $year3 No Trend: $year3notrend[$x] | $year2 No Trend: $year2notrend[$x] | $year1 No Trend: $year1notrend[$x] | $curyear No Trend: $curyearnotrend[$x] | Average: $periodaveragenotrend[$x] | Dev. From Average: $devfromavgnotrend[$x] | Monthly MAD: $monthlymadnotrend[$x] | Deseasonalised MAD: $deseasonalisedmadnotrend[$x]<br />\n";
		}

		// ----------------------------------------------------------------------------------------------------------------
		// REMOVE SEASONALITY
		// ----------------------------------------------------------------------------------------------------------------

		$totdemandnoseason = 0;
		
		for($x=1;$x<=12;$x++)
		{	
			if($curyeardemand[$x] <> -999999)
			{
				if($baseseries[$x] <> 0) $curyearnoseason[$x] = $curyearnotrend[$x] / $baseseries[$x];
				$totdemandnoseason += $curyearnoseason[$x];
			}
			
			if($year1demand[$x] <> -999999) 
			{
				if($baseseries[$x] <> 0) $year1noseason[$x] = $year1notrend[$x] / $baseseries[$x];
				$totdemandnoseason += $year1noseason[$x];
			}
			
			if($year2demand[$x] <> -999999) 
			{
				if($baseseries[$x] <> 0) $year2noseason[$x] = $year2notrend[$x] / $baseseries[$x];
				$totdemandnoseason += $year2noseason[$x];
			}
			
			if($year3demand[$x] <> -999999) 
			{
				if($baseseries[$x] <> 0) $year3noseason[$x] = $year3notrend[$x] / $baseseries[$x];
				$totdemandnoseason += $year3noseason[$x];
			}

			//if($p_debug == "Y") echo __LINE__." Period: $x | $year3 No Season: $year3noseason[$x] | $year2 No Season: $year2noseason[$x] | $year1 No Season: $year1noseason[$x] | $curyear No Season: $curyearnoseason[$x] <br />\n";
		}

		$averageseasonaliseddemand = 0;
		
		if ($numperiodscuryear + $numperiodsyear1 + $numperiodsyear2 + $numperiodsyear3 <> 0) $averageseasonaliseddemand = $totdemandnoseason / ( $numperiodscuryear + $numperiodsyear1 + $numperiodsyear2 + $numperiodsyear3 );
		
		//if($p_debug == "Y") echo __LINE__." Average Demand - No Season: $averageseasonaliseddemand<br />\n";
		
		$deseasonalisedtrendfactor = 1;
		
		if( $year3demandavg <> 0 ) $deseasonalisedtrendfactor = ($totalaverage - $year3demandavg)/2/$year3demandavg + 1;

		//if($p_debug == "Y") echo __LINE__." Deseasonalised Trend Factor: $deseasonalisedtrendfactor<br />\n";

		// ----------------------------------------------------------------------------------------------------------------
		// CALCULATE FORECAST DEMAND - 12 MONTHS
		// ----------------------------------------------------------------------------------------------------------------
	
		for($x=1;$x<12;$x++)
		{	
			if($x == 1)
			{
				if($curmonth <> 12)
				{
					$forecastperiod[1] = $curmonth + 1;
					$forecastyear[1] = $curyear;
				}
				else
				{
					$forecastperiod[1] = 1;
					$forecastyear[1] = $curyear + 1;
				}
			}
			else
			{			
				if($forecastperiod[$x - 1] <> 12)
				{
					$forecastperiod[$x] = $forecastperiod[$x - 1] + 1;
					$forecastyear[$x] = $forecastyear[$x - 1];
				}
				else
				{
					$forecastperiod[$x] = 1;
					$forecastyear[$x] = $forecastyear[$x - 1] + 1;
				}
			}
			
			$forecastdemand[$x] = $averageseasonaliseddemand * $deseasonalisedtrendfactor * $baseseries[$forecastperiod[$x]];
			
			$forecastquery = "INSERT INTO demand(productcode, branch, year, period, type, forecastdemandqty) VALUES ('$productcode', $branch, $forecastyear[$x], $forecastperiod[$x], 'M', $forecastdemand[$x]) ON DUPLICATE KEY UPDATE forecastdemandqty = $forecastdemand[$x]";
			$forecastresult = mysqli_query($link, $forecastquery) or logerror(__LINE__." ".$forecastquery,mysqli_error($link));	
			
			//if($p_debug == "Y") echo __LINE__." Forecast Year: $forecastyear[$x] | Forecast Period: $forecastperiod[$x] | Forecast Demand: $forecastdemand[$x]<br />\n";
		}

		// ----------------------------------------------------------------------------------------------------------------
		// UPDATE STOCK ITEM
		// ----------------------------------------------------------------------------------------------------------------
			
		$stockitemupdatequery = "UPDATE stock SET MAD = $MAD, averagedemand = $totalaverage, safetystock = $safetystock, reviewlevel = $reviewlevel WHERE branch = $branch AND prodcode = '$productcode'";
	
		$stockitemupdateresult = mysqli_query($link, $stockitemupdatequery) or logerror(__LINE__." ".$stockitemupdatequery,mysqli_error($link));	
				
	}	// 	while ($stockitemrow = mysqli_fetch_row($stockitemresult))
		
	// ----------------------------------------------------------------------------------------------------------------
	// UPDATE THE LOGFILES
	// ----------------------------------------------------------------------------------------------------------------

	$end_datetime = date('Y-m-d H:i:s');
	$duration = strtotime($end_datetime) - strtotime($start_datetime);
	$minutes = floor($duration / 60);
	$seconds = $duration % 60;
	
	$filename = basename(__FILE__);
	$batch = date('YmdHi'); 

	$query = "INSERT INTO logfile(id, batch, application, started, ended, duration) VALUES (0, '$batch', '$filename', '$start_datetime', '$end_datetime', $duration)";
	$result = mysqli_query($link, $query) or logerror(__LINE__." ".$query,mysqli_error($link));

	$logfile = "logfile.txt";
	$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
	$stringData = $start_datetime." ".$filename." ".$minutes." min(s) ".$seconds." sec(s)\n";
	fwrite($fh, $stringData);
	fclose($fh);			
	
	if($p_debug == "Y") echo "ALL DONE!";

?>
