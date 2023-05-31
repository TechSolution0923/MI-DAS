<!-- 
Get demand for last 12 complete periods

Steps:
1. Deseasonalise demand using selected seasonality profile
2. Calculate deseasonalised forecast demand
3. Calculate MAD
4. Calculate moving average

PARAMETERS
==========
stockitem	Stock item code (optional)
branch		Branch number (optional)
debug		Display debug messages Y/N (optional)

To run this with full options, use a URL like this: calcforecast.php?stockitem=K00003&branch=1&debug=Y 
-->


<?php
    require_once 'dblogin.php';	
	
	date_default_timezone_set('Europe/London');

	// Start time
	$start_datetime = date('Y-m-d H:i:s');

	$stockitemclause = "";
	
	// Get the stock item code, if its been passed in
	if (isset($_GET['stockitem'])) 
	{
		$p_stockitemcode = $_GET['stockitem'];
		$stockitemclause = " AND s.prodcode = '$p_stockitemcode'";
	}
	
	$branchclause = "";
	
	// Get the branch number, if its been passed in
	if (isset($_GET['branch'])) 
	{
		$p_branch = $_GET['branch'];
		$branchclause = " AND s.branch = $p_branch";
	}
	
	// Get the debug flag, if its been passed in
	if (isset($_GET['debug'])) 
	{
		$p_debug = $_GET['debug'];
	}
	
	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or die ("Unable to connect!"); 

	// Get the current yearmonth
	
	$systemquery = "SELECT curyearmonth FROM system";
	$systemresult = mysqli_query($link, $systemquery) or logerror($systemquery,mysqli_error($link));
	
	$systemrow = mysqli_fetch_row($systemresult);
	$curyearmonth = $systemrow[0];

	$a_demand = array();					// Last 12 months demand
	$a_baseseries = array();				// Seasonality profile base series
	$a_deseasonaliseddemand = array();		// Deseasonalised demand
	$a_deseasonalisedforecast = array();	// Deseasonalised forecast demand
	$a_movingaverage = array();				// Moving average
	
	// Get the seasonality profile level
	
	$imsystemquery = "SELECT seasonalityprofilelevel FROM imsystem";
	$imsystemresult = mysqli_query($link, $imsystemquery) or die ("Error in query: $imsystemquery. ".mysqli_error($link));

	$imsystemrow = mysqli_fetch_row($imsystemresult);
	$seasonalityprofilelevel = $imsystemrow[0];
	
	$alpha = 0.20;
	$MADsmoothingfactor = 0.15;
	
	// Go through each stock item

	$stockitemquery = "SELECT s.branch, s.prodcode, p.pac1code, p.pac2code, p.pac3code, p.pac4code FROM stock AS s LEFT JOIN product AS p ON p.code = s.prodcode WHERE p.code = s.prodcode $stockitemclause $branchclause ORDER BY s.branch, s.prodcode";
	$stockitemresult = mysqli_query($link, $stockitemquery) or die ("Error in query: $stockitemquery. ".mysqli_error($link));
	
	if($p_debug == "Y") echo "stockitemquery: ".$stockitemquery."<br />\n";

	while ($stockitemrow = mysqli_fetch_row($stockitemresult)) 
	{	
		$branch 		= $stockitemrow[0];
		$productcode 	= $stockitemrow[1]; 
		$pac1code	 	= $stockitemrow[2]; 
		$pac2code	 	= $stockitemrow[3]; 
		$pac3code	 	= $stockitemrow[4]; 
		$pac4code	 	= $stockitemrow[5]; 

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

		if($p_debug == "Y") echo "seasonalityquery: ".$seasonalityquery."<br />\n";
		
		$x = 0;
		
		while ($seasonalityrow = mysqli_fetch_row($seasonalityresult)) 
		{	
			$a_baseseries[$x] = $seasonalityrow[0];
			$x++;
		}

		if($p_debug == "Y") echo "baseseries array: ";
		if($p_debug == "Y") print_r($a_baseseries);
		if($p_debug == "Y") echo "<br />\n";

		// ----------------------------------------------------------------------------------------------------------------
		// GET LAST 12 MONTHS DEMAND EXCLUDING CURRENT PERIOD
		// ----------------------------------------------------------------------------------------------------------------
				
		$demandquery = "SELECT demandqty FROM demand WHERE branch = $branch AND productcode = '$productcode' AND (year * 100) + period <> $curyearmonth ORDER BY year DESC, period DESC LIMIT 12";
		$demandresult = mysqli_query($link, $demandquery) or die ("Error in query: $demandquery. ".mysqli_error($link));		

		$totdemandqty = 0;
		
		// Initialise the arrays
		
		for($x=0;$x < 12; $x++)
		{
			$a_demand[$x] = 0;
			$a_deseasonaliseddemand[$x] = 0;
			$a_deseasonalisedforecast[$x] = 0;
			$a_movingaverage[$x] = 0;
		}
		
		// Get the last 12 demands and load into the demand array
		
		$x = 0;
		
		while ($demandrow = mysqli_fetch_row($demandresult))
		{
			$a_demand[$x] = $demandrow[0];
			$x++;
		}		
		
		if($p_debug == "Y")
		{
			echo "Demand array: ";
			print_r($a_demand);
			echo "<br />\n";
		}
		// ----------------------------------------------------------------------------------------------------------------
		// DESEASONALISE THE DEMAND. IF THE DEMAND IS 0, THE BASE SERIES WILL BE ZERO SO NOT CALCULATING.
		// ----------------------------------------------------------------------------------------------------------------
		
		for($x=0;$x < 12; $x++)
		{
			if(!$a_deseasonaliseddemand[$x] == 0) $a_deseasonaliseddemand[$x] = round($a_demand[$x] / $a_baseseries[$x],2);
		}

		if($p_debug == "Y")
		{
			echo "Deseasonalised demand array: ";
			print_r($a_deseasonaliseddemand);
			echo "<br />\n";
		}
		
		// ----------------------------------------------------------------------------------------------------------------
		// DESEASONALISE THE FORECAST DEMAND
		// ----------------------------------------------------------------------------------------------------------------

		$oldest3avg = round(($a_deseasonaliseddemand[9] + $a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[11]) / 3,2);

		if($p_debug == "Y")	echo "Oldest 3 average: ".$oldest3avg."<br />\n";
		
		$a_deseasonalisedforecast[11] = round($alpha * $a_deseasonaliseddemand[10] + (1 - $alpha) * $oldest3avg,2);
		$a_deseasonalisedforecast[10] = round($alpha * $a_deseasonaliseddemand[9] + (1 - $alpha) * $a_deseasonalisedforecast[11],2);
		$a_deseasonalisedforecast[9] = round($alpha * $a_deseasonaliseddemand[8] + (1 - $alpha) * $a_deseasonalisedforecast[10],2);
		$a_deseasonalisedforecast[8] = round($alpha * $a_deseasonaliseddemand[8] + (1 - $alpha) * $a_deseasonalisedforecast[9],2);
		$a_deseasonalisedforecast[7] = round($alpha * $a_deseasonaliseddemand[7] + (1 - $alpha) * $a_deseasonalisedforecast[8],2);
		$a_deseasonalisedforecast[6] = round($alpha * $a_deseasonaliseddemand[6] + (1 - $alpha) * $a_deseasonalisedforecast[7],2);
		$a_deseasonalisedforecast[5] = round($alpha * $a_deseasonaliseddemand[5] + (1 - $alpha) * $a_deseasonalisedforecast[6],2);
		$a_deseasonalisedforecast[4] = round($alpha * $a_deseasonaliseddemand[4] + (1 - $alpha) * $a_deseasonalisedforecast[5],2);
		$a_deseasonalisedforecast[3] = round($alpha * $a_deseasonaliseddemand[3] + (1 - $alpha) * $a_deseasonalisedforecast[4],2);
		$a_deseasonalisedforecast[2] = round($alpha * $a_deseasonaliseddemand[2] + (1 - $alpha) * $a_deseasonalisedforecast[3],2);
		$a_deseasonalisedforecast[1] = round($alpha * $a_deseasonaliseddemand[1] + (1 - $alpha) * $a_deseasonalisedforecast[2],2);
		$a_deseasonalisedforecast[0] = round($alpha * $a_deseasonaliseddemand[0] + (1 - $alpha) * $a_deseasonalisedforecast[1],2);
		
		if($p_debug == "Y")
		{
			echo "Deseasonalised forecast demand array: ";
			print_r($a_deseasonalisedforecast);
			echo "<br />\n";
		}

		// ----------------------------------------------------------------------------------------------------------------
		// AVERAGE DEVIATION ON OLDEST 3 DESEASONALISED FORECAST DEMANDS
		// ----------------------------------------------------------------------------------------------------------------
		
		$average = round(($oldest3avg + $a_deseasonalisedforecast[11] + $a_deseasonalisedforecast[10]) / 3,2);
		$dev1 = round(abs($average - $oldest3avg),2);
		$dev2 = round(abs($average - $a_deseasonalisedforecast[11]),2);
		$dev3 = round(abs($average - $a_deseasonalisedforecast[10]),2);
		$avedev = round(($dev1 + $dev2 + $dev3) / 3,2);

		if($p_debug == "Y")	echo "Average: ".$average."<br />\n";
		if($p_debug == "Y")	echo "Dev 1: ".$dev1."<br />\n";
		if($p_debug == "Y")	echo "Dev 2: ".$dev2."<br />\n";
		if($p_debug == "Y")	echo "Dev 3: ".$dev3."<br />\n";
		if($p_debug == "Y")	echo "Average Deviation: ".$avedev."<br />\n";

		// ----------------------------------------------------------------------------------------------------------------
		// MAD CALCULATION I (IN EVERY WAY!)
		// ----------------------------------------------------------------------------------------------------------------
		
		$MAD1 = abs($a_deseasonaliseddemand[0] - $a_deseasonalisedforecast[1]) * $MADsmoothingfactor + (1 - $MADsmoothingfactor) * 
		(abs($a_deseasonaliseddemand[1] - $a_deseasonalisedforecast[2]) * $MADsmoothingfactor + (1 - $MADsmoothingfactor) * 
		(abs($a_deseasonaliseddemand[2] - $a_deseasonalisedforecast[3]) * $MADsmoothingfactor + (1 - $MADsmoothingfactor) * 
		(abs($a_deseasonaliseddemand[3] - $a_deseasonalisedforecast[4]) * $MADsmoothingfactor + (1 - $MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[4] - $a_deseasonalisedforecast[5]) * $MADsmoothingfactor + (1 - $MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[5] - $a_deseasonalisedforecast[6]) * $MADsmoothingfactor + (1 - $MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[6] - $a_deseasonalisedforecast[7]) * $MADsmoothingfactor + (1 - $MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[7] - $a_deseasonalisedforecast[8]) * $MADsmoothingfactor + (1 - $MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[8] - $a_deseasonalisedforecast[9]) * $MADsmoothingfactor + (1 - $MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[9] - $a_deseasonalisedforecast[10]) * $MADsmoothingfactor + (1 - $MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[10] - $a_deseasonalisedforecast[11]) * $MADsmoothingfactor + (1 - $MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[11] - $average) * $MADsmoothingfactor+(1-$MADsmoothingfactor) * $avedev)))))))))));
		
		if($p_debug == "Y")	echo "MAD 1: ".$MAD1."<br />\n";

		// ----------------------------------------------------------------------------------------------------------------
		// UPDATE THE STOCK ITEM WITH THE MAD
		// ----------------------------------------------------------------------------------------------------------------
		
		$stockupdatequery = "UPDATE stock SET mad = $MAD1 WHERE branch = $branch AND prodcode = '$productcode'";
		$stockupdateresult = mysqli_query($link, $stockupdatequery) or die ("Error in query: $stockupdatequery. ".mysqli_error($link));

		// ----------------------------------------------------------------------------------------------------------------
		// MOVING AVERAGE
		// ----------------------------------------------------------------------------------------------------------------

		$averagedsd = ($a_deseasonaliseddemand[10] +  $a_deseasonaliseddemand[9]) / 3; // Average oldest deseasonalised demand
		$a_movingaverage[11] = (3 * $a_deseasonaliseddemand[10] + $averagedsd) / 4;
		$a_movingaverage[10] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9]) / 2;
		$a_movingaverage[9] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9]) / 3;
		$a_movingaverage[8] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9]) / 3;
		$a_movingaverage[7] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9] + $a_deseasonaliseddemand[8]) / 4;
		$a_movingaverage[7] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9] + $a_deseasonaliseddemand[8] + $a_deseasonaliseddemand[7]) / 5;
		$a_movingaverage[6] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9] + $a_deseasonaliseddemand[8] + $a_deseasonaliseddemand[7] + $a_deseasonaliseddemand[6]) / 6;
		$a_movingaverage[5] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9] + $a_deseasonaliseddemand[8] + $a_deseasonaliseddemand[7] + $a_deseasonaliseddemand[6] + $a_deseasonaliseddemand[5]) / 7;
		$a_movingaverage[4] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9] + $a_deseasonaliseddemand[8] + $a_deseasonaliseddemand[7] + $a_deseasonaliseddemand[6] + $a_deseasonaliseddemand[5] + $a_deseasonaliseddemand[4]) / 8;
		$a_movingaverage[3] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9] + $a_deseasonaliseddemand[8] + $a_deseasonaliseddemand[7] + $a_deseasonaliseddemand[6] + $a_deseasonaliseddemand[5] + $a_deseasonaliseddemand[4] + $a_deseasonaliseddemand[3]) / 9;
		$a_movingaverage[2] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9] + $a_deseasonaliseddemand[8] + $a_deseasonaliseddemand[7] + $a_deseasonaliseddemand[6] + $a_deseasonaliseddemand[5] + $a_deseasonaliseddemand[4] + $a_deseasonaliseddemand[3] + $a_deseasonaliseddemand[2]) / 10;
		$a_movingaverage[1] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9] + $a_deseasonaliseddemand[8] + $a_deseasonaliseddemand[7] + $a_deseasonaliseddemand[6] + $a_deseasonaliseddemand[5] + $a_deseasonaliseddemand[4] + $a_deseasonaliseddemand[3] + $a_deseasonaliseddemand[2] + $a_deseasonaliseddemand[1]) / 11;
		$a_movingaverage[0] = ($a_deseasonaliseddemand[10] + $a_deseasonaliseddemand[9] + $a_deseasonaliseddemand[8] + $a_deseasonaliseddemand[7] + $a_deseasonaliseddemand[6] + $a_deseasonaliseddemand[5] + $a_deseasonaliseddemand[4] + $a_deseasonaliseddemand[3] + $a_deseasonaliseddemand[2] + $a_deseasonaliseddemand[1] + $a_deseasonaliseddemand[0]) / 12;

		if($p_debug == "Y")
		{
			echo "Moving average array: ";
			print_r($a_movingaverage);
			echo "<br />\n";
		}

		// ----------------------------------------------------------------------------------------------------------------
		// MAD CALCULATION II (IN EVERY WAY!)
		// ----------------------------------------------------------------------------------------------------------------
		
		$MAD2 = abs($a_deseasonaliseddemand[0] - $a_movingaverage[1]) * $MADsmoothingfactor+(1-$MADsmoothingfactor) * 
		(abs($a_deseasonaliseddemand[1] - $a_movingaverage[2]) * $MADsmoothingfactor+(1-$MADsmoothingfactor) * 
		(abs($a_deseasonaliseddemand[2] - $a_movingaverage[3]) * $MADsmoothingfactor+(1-$MADsmoothingfactor) * 
		(abs($a_deseasonaliseddemand[3] - $a_movingaverage[4]) * $MADsmoothingfactor+(1-$MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[4] - $a_movingaverage[5]) * $MADsmoothingfactor+(1-$MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[5] - $a_movingaverage[6]) * $MADsmoothingfactor+(1-$MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[6] - $a_movingaverage[7]) * $MADsmoothingfactor+(1-$MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[7] - $a_movingaverage[8]) * $MADsmoothingfactor+(1-$MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[8] - $a_movingaverage[9]) * $MADsmoothingfactor+(1-$MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[9] - $a_movingaverage[10]) * $MADsmoothingfactor+(1-$MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[10] - $a_movingaverage[11]) * $MADsmoothingfactor+(1-$MADsmoothingfactor) *
		(abs($a_deseasonaliseddemand[11] - $average) * $MADsmoothingfactor+(1-$MADsmoothingfactor) * $avedev)))))))))));
		
		if($p_debug == "Y")
		{
			echo "MAD 2: ".$MAD2."<br />\n";
		}
				
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
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

	$logfile = "logfile.txt";
	$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
	$stringData = $start_datetime." ".$filename." ".$minutes." min(s) ".$seconds." sec(s)\n";
	fwrite($fh, $stringData);
	fclose($fh);	
	
	
	function logerror($query,$error)
	{
		$email = "kieran" . "@" . "kk-cs" . ".co.uk";
		
		$errormsg = "Hi Kieran, an error just occurred in ".__FILE__.". The query was '".$query."' and the error was '".$error."'<BR>";
		
		error_log($errormsg);
		error_log($errormsg,1,$email);
		
		return true;
	}	
?>
