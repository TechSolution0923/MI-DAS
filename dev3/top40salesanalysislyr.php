<?php
	// Set the content type
	header('Content-type: application/csv');
	
	// Set the file name option to a filename of your choice.
	header('Content-Disposition: attachment; filename=top40salesanalysislyr.csv');
	
	// Set the encoding
	header("Content-Transfer-Encoding: UTF-8");

	$f = fopen('php://output', 'a'); // Configure fopen to write to the output buffer

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
	
	$curyear  = substr($curyearmonth,0,4) - 1; // This script is for last year
	$curmonth = intval(substr($curyearmonth,4,2)); // Change from 04 to 4 for example

	$prevyear = $curyear - 1;
	
	// disable autocommit
	mysqli_autocommit($link, FALSE);

	// Get the account code if its been passed in
	if (isset($_GET['account'])) $accountcodeparameter = $_GET['account'];

	// Write CSV header row
	fputcsv($f, array("Account Code", "Business Type/Area", "Sales YTD ".$curyear,"Sales YTD ".$prevyear,"Difference","Sales Total ".$prevyear, "Target ".$curyear,
	"A10 High Speed Hand Dryers Sales YTD ".$curyear,"A10 High Speed Hand Dryers Quantity YTD ".$curyear,"A10 High Speed Hand Dryers Sales YTD ".$prevyear, "A10 High Speed Hand Dryers Sales YTD ".$prevyear, "A10 High Speed Hand Dryers Sales ".$prevyear,"A10 High Speed Hand Dryers Quantity ".$prevyear,"A10 Last Sale",
	"B10 Thermal Radiators Sales YTD ".$curyear,"B10 Thermal Radiators Quantity YTD ".$curyear,"B10 Thermal Radiators Sales YTD ".$prevyear,"B10 Thermal Radiators Quantity YTD ".$prevyear,"B10 Thermal Radiators Sales ".$prevyear,"B10 Thermal Radiators Quantity ".$prevyear,"B10 Last Sale",
	"B15 Panel Radiators Sales YTD ".$curyear,"B15 Panel Radiators Quantity YTD ".$curyear,"B15 Panel Radiators Sales YTD ".$prevyear,"B15 Panel Radiators Quantity YTD ".$prevyear,"B15 Panel Radiators Sales ".$prevyear,"B15 Panel Radiators Quantity ".$prevyear,"B15 Last Sale",
	"D10 Ventilation Sales YTD ".$curyear,"D10 Ventilation Quantity YTD ".$curyear,"D10 Ventilation Sales YTD ".$prevyear,"D10 Ventilation Quantity YTD ".$prevyear,"D10 Ventilation Sales ".$prevyear,"D10 Ventilation Radiators Quantity ".$prevyear,"D10 Last Sale",
	"E10 Water Heating Sales YTD ".$curyear,"E10 Water Heating Quantity YTD ".$curyear,"E10 Water Heating Sales YTD ".$prevyear,"E10 Water Heating Quantity YTD ".$prevyear,"E10 Water Heating Sales ".$prevyear,"E10 Water Heating Radiators Quantity ".$prevyear,"E10 Last Sale",
	"F10 Outdoor Heating Sales YTD ".$curyear,"F10 Outdoor Heating Quantity YTD ".$curyear,"F10 Outdoor Heating Sales YTD ".$prevyear,"F10 Outdoor Heating Quantity YTD ".$prevyear,"F10 Outdoor Heating Sales ".$prevyear,"F10 Outdoor Heating Quantity ".$prevyear,"F10 Last Sale",));

	// Build query string for sales and quantity based on current month 
	
	// For example:
	// Apr Mar Feb Jan Dec Nov Oct Sep Aug Jul Jun May Apr Mar Feb Jan
	//   0   1   2   3   4   5   6   7   8   9  10  11  12  13  14  15
	// Only complete months are included so, for YTD, curmonth will be excluded and likewise this month last year, which is month12 is also excluded, so we're always comparing
	// full months

	// SALES YTD
	
	// If the current month is, say 3 i.e. March, in the summary tables, this is 0, and column 3 is then December
	
	$salesthisyearcols = "msales".$curmonth;
	$qtythisyearcols = "mquantity".$curmonth;
	
	for ($x = $curmonth + 1; $x < $curmonth + 12; $x++) // curmonth is the current month from the systems table. This should build something like msales0 + msales1 + msales2 etc up to current month
	{				
		$salesthisyearcols = $salesthisyearcols."+msales".$x;
		$qtythisyearcols = $qtythisyearcols."+mquantity".$x;
	}				

	// SALES YTD LAST YEAR
	
	$saleslastyearcols = "msales".($curmonth + 12);
	$qtylastyearcols = "mquantity".($curmonth + 12);
	
	for ($x = $curmonth + 13; $x < $curmonth + 24; $x++) 
	{				
		$saleslastyearcols = $saleslastyearcols."+msales".$x;
		$qtylastyearcols = $qtylastyearcols."+mquantity".$x;
	}		
		
	// Sales values, quantities and targets are for completed months only. Need to pad the month out again as I converted it to integer above
	
	$custquery = "SELECT customer.account, customer.repcode,customersales.$salesthisyearcols, customersales.$saleslastyearcols, customersales.ysales2 FROM customer LEFT JOIN customersales ON customersales.account = customer.account";

	// If the account code has been passed in, use it
	if (isset($_GET['account'])) $custquery .= " WHERE account = '$accountcodeparameter' ORDER BY account";

	$custresult = mysqli_query($link, $custquery) or logerror($custquery,mysqli_error($link));
	while ($custrow = mysqli_fetch_row($custresult)) 
	{
		$account     = $custrow[0];	// CUSTOMER LEVEL SALES
		$repcode     = $custrow[1];
		$salesytd    = $custrow[2];
		$saleslytd   = $custrow[3];
		$saleslyr    = $custrow[4];
		
		$salestarget = 0;
		
		// Getting the sales target this way as there may not always be a sales target and it was omitting customers ...
		
		// If running the report for a year other than the current year, it'll be for a past year (obvs!) and for the full year
		
		$fromyearmonth = $curyear."01";
		$toyearmonth   = $curyear."12";
		
		$salestargetquery = "SELECT SUM(salestarget) FROM customersalestarget WHERE account = '$account' AND yearmonth BETWEEN $fromyearmonth AND $toyearmonth";

		$salestargetresult = mysqli_query($link, $salestargetquery) or logerror($salestargetquery,mysqli_error($link));
		$salestargetrow = mysqli_fetch_row($salestargetresult) ;
		
		$salestarget = $salestargetrow[0];
		
		$pac2query = "SELECT code FROM pac2 WHERE code IN ('A10','B10','B15','D10','E10','F10')";
		$pac2result = mysqli_query($link, $pac2query) or logerror($pac2query,mysqli_error($link));

		$A10salesytd  = 0;
		$A10qtyytd    = 0;
		$A10saleslytd = 0;
		$A10qtylytd   = 0;
		$A10saleslyr  = 0;
		$A10qtylyr    = 0;
		$A10lastmonth = 0;

		$B10salesytd  = 0;
		$B10qtyytd    = 0;
		$B10saleslytd = 0;
		$B10qtylytd   = 0;
		$B10saleslyr  = 0;
		$B10qtylyr    = 0;
		$B10lastmonth = 0;

		$B15salesytd  = 0;
		$B15qtyytd    = 0;
		$B15saleslytd = 0;
		$B15qtylytd   = 0;
		$B15saleslyr  = 0;
		$B15qtylyr    = 0;
		$B15lastmonth = 0;

		$D10salesytd  = 0;
		$D10qtyytd    = 0;
		$D10saleslytd = 0;
		$D10qtylytd   = 0;
		$D10saleslyr  = 0;
		$D10qtylyr    = 0;
		$D10lastmonth = 0;

		$E10salesytd  = 0;
		$E10qtyytd    = 0;
		$E10saleslytd = 0;
		$E10qtylytd   = 0;
		$E10saleslyr  = 0;
		$E10qtylyr    = 0;
		$E10lastmonth = 0;

		$F10salesytd  = 0;
		$F10qtyytd    = 0;
		$F10saleslytd = 0;
		$F10qtylytd   = 0;
		$F10saleslyr  = 0;
		$F10qtylyr    = 0;
		$F10lastmonth = 0;

		while ($pac2row = mysqli_fetch_row($pac2result)) 
		{
			$pac2code = $pac2row[0];		

			// Use the columns from above
			
			$query = "SELECT $salesthisyearcols, $qtythisyearcols, $saleslastyearcols, $qtylastyearcols, ysales1, yquantity1 FROM customerpac2sales WHERE account = '$account' AND pac2code = '$pac2code'";
			
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
			$row = mysqli_fetch_row($result);
				
			$pac2salesytd  = $row[0]; 	// CUSTOMER PAC2 LEVEL SALES
			$pac2qtyytd    = $row[1];
			$pac2saleslytd = $row[2];
			$pac2qtylytd   = $row[3];
			$pac2saleslyr  = $row[4];
			$pac2qtylyr    = $row[5];

			$lastsalequery = "SELECT MONTHNAME(MAX(date)), YEAR(MAX(date)) FROM salesanalysis WHERE account = '$account' AND pac2 = '$pac2code'";
			$lastsaleresult = mysqli_query($link, $lastsalequery) or logerror($lastsalequery,mysqli_error($link));
			$lastsalerow = mysqli_fetch_row($lastsaleresult);

			$lastsalemonth = strtoupper(substr($lastsalerow[0],0,3));
			$lastsaleyear  = substr($lastsalerow[1],2,2);
			$lastsale = $lastsalemonth."-".$lastsaleyear;
			
			switch ($pac2code)
			{
				case "A10":
					$A10salesytd  = $pac2salesytd;
					$A10qtyytd    = $pac2qtyytd;
					$A10saleslytd = $pac2saleslytd;
					$A10qtylytd   = $pac2qtylytd;
					$A10saleslyr  = $pac2saleslyr;
					$A10qtylyr    = $pac2qtylyr;
					$A10lastmonth = $lastsale;
					break;

				case "B10":
					$B10salesytd  = $pac2salesytd;
					$B10qtyytd    = $pac2qtyytd;
					$B10saleslytd = $pac2saleslytd;
					$B10qtylytd   = $pac2qtylytd;
					$B10saleslyr  = $pac2saleslyr;
					$B10qtylyr    = $pac2qtylyr;
					$B10lastmonth = $lastsale;
					break;
					
				case "B15":
					$B15salesytd  = $pac2salesytd;
					$B15qtyytd    = $pac2qtyytd;
					$B15saleslytd = $pac2saleslytd;
					$B15qtylytd   = $pac2qtylytd;
					$B15saleslyr  = $pac2saleslyr;
					$B15qtylyr    = $pac2qtylyr;
					$B15lastmonth = $lastsale;
					break;

				case "D10":
					$D10salesytd  = $pac2salesytd;
					$D10qtyytd    = $pac2qtyytd;
					$D10saleslytd = $pac2saleslytd;
					$D10qtylytd   = $pac2qtylytd;
					$D10saleslyr  = $pac2saleslyr;
					$D10qtylyr    = $pac2qtylyr;
					$D10lastmonth = $lastsale;
					break;

				case "E10":
					$E10salesytd  = $pac2salesytd;
					$E10qtyytd    = $pac2qtyytd;
					$E10saleslytd = $pac2saleslytd;
					$E10qtylytd   = $pac2qtylytd;
					$E10saleslyr  = $pac2saleslyr;
					$E10qtylyr    = $pac2qtylyr;
					$E10lastmonth = $lastsale;
					break;

				case "F10":
					$F10salesytd  = $pac2salesytd;
					$F10qtyytd    = $pac2qtyytd;
					$F10saleslytd = $pac2saleslytd;
					$F10qtylytd   = $pac2qtylytd;
					$F10saleslyr  = $pac2saleslyr;
					$F10qtylyr    = $pac2qtylyr;
					$F10lastmonth = $lastsale;
					break;
			}
		}
		// Write to the csv
		fputcsv($f, array($account, $repcode, $salesytd, $saleslytd, $salesytd - $saleslytd, $saleslyr, $salestarget, $A10salesytd, $A10qtyytd, $A10saleslytd, $A10qtylytd, $A10saleslyr, $A10qtylyr, $A10lastmonth, $B10salesytd, $B10qtyytd, $B10saleslytd, $B10qtylytd, $B10saleslyr, $B10qtylyr, $B10lastmonth, $B15salesytd, $B15qtyytd, $B15saleslytd, $B15qtylytd, $B15saleslyr, $B15qtylyr, $B15lastmonth, $D10salesytd, $D10qtyytd, $D10saleslytd, $D10qtylytd, $D10saleslyr, $D10qtylyr, $D10lastmonth, $E10salesytd, $E10qtyytd, $E10saleslytd, $E10qtylytd, $E10saleslyr, $E10qtylyr, $E10lastmonth, $F10salesytd, $F10qtyytd, $F10saleslytd, $F10qtylytd, $F10saleslyr, $F10qtylyr, $F10lastmonth ));
	}
	
	mysqli_commit($link);
	
	mysqli_close($link);
	
	// Close the file
	fclose($f);
	
	function logerror($query,$error)
	{
		$email = "kieran" . "@" . "kk-cs" . ".co.uk";
		
		$errormsg = "Hi Kieran, an error just occurred in ".__FILE__.". The query was '".$query."' and the error was '".$error."'<BR>";
		
		error_log($errormsg);
		error_log($errormsg,1,$email);
		
		return true;
	}	
?>