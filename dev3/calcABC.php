<!-- This script classifies stock items and products in ABC

Steps:
1. Gets the total demand for the company for the period and updates the KPI
2. Loop through the products getting the product demand and calculates the percentage of total demand and updates the KPI
3. Loop through the branches, getting the branch demand and updating the KPI. Branch demand is loaded into an array for efficiency
4. Loop through the stock items, getting the stock item demand and calculating the percentage of total branch demand and updating the stock item
5. Loop through the products again in descending turnover order to ABC rank

PARAMETERS
==========
auth		Is this an authorised run of this script Y/N ** THIS IS REQUIRED **
stockitem	Stock item code (optional)
branch		Branch number (optional)
debug		Display debug messages Y/N (optional)

To run this with full options, use a URL like this: calcABC.php?auth=Y&stockitem=K00003&branch=1&debug=Y 
-->
<?php
    require_once 'dblogin.php';	
	
	date_default_timezone_set('Europe/London');

	// Start time
	$start_datetime = date('Y-m-d H:i:s');

	// If the auth parameter hasn't been set, exit the script
	
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
		$stockitemclause = " AND code = '$p_stockitemcode'";
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
	
	$productwhereclause = "";
	$branchwhereclause = "";
	$stockitemwhereclause = "";
	$prodcodewhereclause = "";
	
	if (!$p_stockitemcode == "") $productwhereclause = " WHERE code = '$p_stockitemcode' ";
	if (!$p_stockitemcode == "") $prodcodewhereclause = " AND prodcode = '$p_stockitemcode' ";
	if (!$p_branch == "") $branchwhereclause = " WHERE branch = $p_branch ";
	if (!$p_stockitemcode == "" AND !$p_branch == "") $stockitemwhereclause = " WHERE prodcode = '$p_stockitemcode' AND branch = $p_branch ";
	if (!$p_stockitemcode == "" AND $p_branch == "") $stockitemwhereclause = " WHERE prodcode = '$p_stockitemcode' ";
	if ($p_stockitemcode == "" AND !$p_branch == "") $stockitemwhereclause = " WHERE branch = $p_branch";
		
	// Get the debug flag, if its been passed in
	if (isset($_GET['debug'])) 
	{
		$p_debug = $_GET['debug'];
	}

	//if($p_debug == "Y") echo __LINE__." stock item parameter: ".$p_stockitemcode."<br />\n";
	//if($p_debug == "Y") echo __LINE__." branch parameter: ".$p_branch."<br />\n";
	//if($p_debug == "Y") echo __LINE__." product where clause: ".$productwhereclause."<br />\n";
	//if($p_debug == "Y") echo __LINE__." branch where clause: ".$branchwhereclause."<br />\n";
	//if($p_debug == "Y") echo __LINE__." stock item where clause: ".$stockitemwhereclause."<br />\n";

	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or die ("Unable to connect!"); 

	// Enable autocommit
	mysqli_autocommit($link, TRUE);

	$a_branchdemand = array(); // Stores branch demand with branch number as index.
	
	// Get the current yearmonth

	$query = "SELECT curyearmonth FROM system";
	$result = mysqli_query($link, $query) or logerror(__LINE__." ".$query,mysqli_error($link));

	$row = mysqli_fetch_row($result);
	$curyearmonth = $row[0];
	
	// Get the IM settings - ABC %'s and # Months
	
	$query = "SELECT apc, bpc, cpc, months FROM imsystem";
	$result = mysqli_query($link, $query) or logerror(__LINE__." ".$query,mysqli_error($link));

	$row = mysqli_fetch_row($result);
	$apc = $row[0];
	$bpc = $row[1];
	$cpc = $row[2];
	$months = $row[3];
	
	// Go back $months ago
	
	$startyearmonth = date('Ym', strtotime($curyearmonth.' - '.$months.' months'));

	//if($p_debug == "Y") echo __LINE__." Start year month: ".$startyearmonth."<br />\n";
	//if($p_debug == "Y") echo __LINE__." Current year month: ".$curyearmonth."<br />\n";

	$SOM = date('Y-m-01');
	$today = date('Y-m-d');

	$productscount = 0;
	$productswithdemandcount = 0;
	
	// Clear down the existing KPIs

	// BRANCHTURNOVER
	// actualvalue1 = Turnover
	// actualvalue2 = # Items
	// actualvalue3 = # Items With Turnover

	if($p_debug == "Y") echo __LINE__." Clearing down existing KPIs<br />\n";
	
	$query = "UPDATE imkpidata SET actualvalue1 = 0, actualvalue2 = 0, actualvalue3 = 0, actualvalue4 = 0 WHERE identifier IN ('BRANCHTURNOVER','ABCANALYSIS') AND date = '$today'";
	$result = mysqli_query($link, $query) or logerror(__LINE__." ".$query,mysqli_error($link));

	if($p_debug == "Y") echo __LINE__." Cleared down existing KPIs<br />\n";

	// ----------------------------------------------------------------------------------------------------------------
	// CALCULATE CENTRAL DEMAND
	// ----------------------------------------------------------------------------------------------------------------
	
	if($p_debug == "Y") echo __LINE__." Calculating central demand<br />\n";
	
//	$centraldemandquery = "SELECT IFNULL(SUM(demandval),0) FROM demand WHERE (year * 100) + period >= $startyearmonth AND (year * 100) + period < $curyearmonth";
	$centraldemandquery = "SELECT SUM(demval12m) FROM stock";
	$centraldemandresult = mysqli_query($link, $centraldemandquery) or logerror(__LINE__." ".$centraldemandquery,mysqli_error($link));
	
	$centraldemandrow = mysqli_fetch_row($centraldemandresult);		
	$centraldemand = $centraldemandrow[0];

	if($p_debug == "Y") echo __LINE__." Calculated central demand<br />\n";

	//if($p_debug == "Y") echo __LINE__." Central demand: ".$centraldemand."<br />\n";
	
	// ----------------------------------------------------------------------------------------------------------------
	// UPDATE CENTRAL DEMAND KPI
	// ----------------------------------------------------------------------------------------------------------------
	
	$kpiquery = "INSERT INTO imkpidata(identifier,period,branch,distbranch,suppliercode,analysiscode, analysisdesc,date,actualvalue1) VALUES('CENTRALTURNOVER',3,0,0,'',0,'Central Turnover', '$today', $centraldemand) ON DUPLICATE KEY UPDATE actualvalue1 = $centraldemand";
	$kpiresult = mysqli_query($link, $kpiquery) or logerror(__LINE__." ".$kpiquery,mysqli_error($link));	
	
	// ----------------------------------------------------------------------------------------------------------------
	// CALCULATE PRODUCT DEMAND
	// ----------------------------------------------------------------------------------------------------------------

	if($p_debug == "Y") echo __LINE__." Calculating product demand<br />\n";

	$productquery = "SELECT product.code, SUM(stock.demval12m) FROM product LEFT JOIN stock ON stock.prodcode = product.code $productwhereclause GROUP BY product.code ORDER BY product.code";
	$productresult = mysqli_query($link, $productquery) or logerror(__LINE__." ".$productquery,mysqli_error($link));	

	//if($p_debug == "Y") echo __LINE__." productquery: ".$productquery."<br />\n";
	
	while ($productrow = mysqli_fetch_row($productresult)) 
	{			
		$productcode = $productrow[0];
		$productdemand = $productrow[1];

		//if($p_debug == "Y") echo __LINE__." $productcode ";

		$productscount++;
		
		//$productdemandquery = "SELECT IFNULL(SUM(demandval),0) FROM demand WHERE productcode = '$productcode' AND (year * 100) + period >= $startyearmonth AND (year * 100) + period < $curyearmonth";
		//$productdemandresult = mysqli_query($link, $productdemandquery) or logerror(__LINE__." ".$productdemandquery,mysqli_error($link));	

		//$productdemand = 0;
		
		//$productdemandrow = mysqli_fetch_row($productdemandresult);
		//$productdemand = $productdemandrow[0];

		if ($productdemand == NULL) $productdemand = 0; // No demand results in NULL
		
		if(!$productdemand == 0) $productswithdemandcount++;
		
		// ----------------------------------------------------------------------------------------------------------------
		// CALCULATE PRODUCT DEMAND AS PERCENTAGE OF CENTRAL DEMAND
		// ----------------------------------------------------------------------------------------------------------------

		if($centraldemand <> 0 AND $productdemand <> 0)
		{
			$productpercentage = ($productdemand/$centraldemand) * 100;
		}
		else
		{
			$productpercentage = 0;
		}				

		// ----------------------------------------------------------------------------------------------------------------
		// UPDATE PRODUCT ABC TURNOVER AND PERCENTAGE. ABC CLASSIFICATION AND RANKING LATER
		// ----------------------------------------------------------------------------------------------------------------

		$productupdatequery = "UPDATE product SET abcturnover = $productdemand, abcturnoverpc = $productpercentage WHERE code = '$productcode'";
		$productupdateresult = mysqli_query($link, $productupdatequery) or logerror(__LINE__." ".$productupdatequery,mysqli_error($link));	

	} // while ($productrow = mysqli_fetch_row($productresult)) 

	// ----------------------------------------------------------------------------------------------------------------
	// CALCULATE BRANCH DEMAND
	// ----------------------------------------------------------------------------------------------------------------

	$branchdemandquery = "SELECT branch, SUM(demval12m), COUNT(*), COUNT(IF(demval12m > 0,1,NULL)) FROM stock $productwhereclause GROUP BY branch ORDER BY branch";
	$branchdemandresult = mysqli_query($link, $branchdemandquery) or logerror(__LINE__." ".$branchdemandquery,mysqli_error($link));	

	// if($p_debug == "Y") echo __LINE__." branchdemandquery: ".$branchdemandquery."<br />\n";

	if($p_debug == "Y") echo __LINE__." Calculating branch demand<br />\n";
	
	while ($branchdemandrow = mysqli_fetch_row($branchdemandresult)) 
	{			
		$branch 			= $branchdemandrow[0];
		$branchdemand 		= $branchdemandrow[1];
		$numitems			= $branchdemandrow[2];
		$numitemswithdemand = $branchdemandrow[3];
		
		$a_branchdemand[$branch] = $branchdemand;

		// ----------------------------------------------------------------------------------------------------------------
		// UPDATE BRANCH TURNOVER KPI
		// ----------------------------------------------------------------------------------------------------------------

		// BRANCHTURNOVER
		// actualvalue1 = Turnover
		// actualvalue2 = # Items
		// actualvalue3 = # Items With Turnover

		$KPIquery = "INSERT INTO imkpidata(identifier,period,branch,distbranch,suppliercode,analysiscode, analysisdesc,date,actualvalue1,actualvalue2,actualvalue3) VALUES('BRANCHTURNOVER',3,$branch,0,'',$branch,'Branch Number $branch', '$today', $branchdemand, $numitems,$numitemswithdemand) ON DUPLICATE KEY UPDATE actualvalue1 = $branchdemand, actualvalue2 = $numitems, actualvalue3 = $numitemswithdemand";
		$KPIresult = mysqli_query($link, $KPIquery) or logerror(__LINE__." ".$KPIquery,mysqli_error($link));	

//		if($p_debug == "Y") echo __LINE__." KPIquery: ".$KPIquery."<br />\n";

	} // while ($branchrow = mysqli_fetch_row($branchresult)) 

	// if($p_debug == "Y") echo __LINE__."branch demand: ";
	// if($p_debug == "Y") print_r($a_branchdemand);
	// if($p_debug == "Y") echo "<br />\n";

	// ----------------------------------------------------------------------------------------------------------------
	// 5. LOOP THROUGH THE PRODUCTS AGAIN, RANKING THEM
	// ----------------------------------------------------------------------------------------------------------------

	$accumulatedpc = 0;
	$abcrank = 1;

	if($p_debug == "Y") echo __LINE__." Ranking products<br />\n";

	$productquery = "SELECT code, abcturnover, abcturnoverpc FROM product $productwhereclause ORDER BY abcturnover DESC";
	$productresult = mysqli_query($link, $productquery) or logerror(__LINE__." ".$productquery,mysqli_error($link));	

	//if($p_debug == "Y") echo __LINE__." productquery: ".$productquery."<br />\n";
	
	while ($productrow = mysqli_fetch_row($productresult)) 
	{			
		$productcode = $productrow[0];
		$abcturnover = $productrow[1];
		$abcturnoverpc = $productrow[2];

		//if($p_debug == "Y") echo __LINE__." $productcode ";

		// ----------------------------------------------------------------------------------------------------------------
		// 5.A. DETERMINE PRODUCT ABC RANKING
		// ----------------------------------------------------------------------------------------------------------------

		// Accumulate the turnover % and anything under A% is A, between A and B is B and so on.
		
		$accumulatedpc += $abcturnoverpc;
		
		if($accumulatedpc <= $apc )	// e.g. 65%
		{
			$abcclass = "A";
		}
		elseif($accumulatedpc > $apc AND $accumulatedpc <= $apc + $bpc) 	// e.g. between 65% and 90% (65% + 25%)
		{
			$abcclass = "B";
		}
		elseif($abcturnover > 0)
		{
			$abcclass = "C";
		}
		else
		{
			$abcclass = "X";
		}
	
		// ----------------------------------------------------------------------------------------------------------------
		// 5.B. UPDATE PRODUCT ABC RANK AND CLASS
		// ----------------------------------------------------------------------------------------------------------------

		$updatequery = "UPDATE product SET abcrank = $abcrank, abcclass = '$abcclass' WHERE code = '$productcode'";
		$updateresult = mysqli_query($link, $updatequery) or logerror(__LINE__." ".$updatequery,mysqli_error($link));
		
		$abcrank++;

	} // while ($productrow = mysqli_fetch_row($productresult)) 

	// ----------------------------------------------------------------------------------------------------------------
	// LOOP THROUGH BRANCHES, TO LOOP THROUGH STOCK ITEMS
	// ----------------------------------------------------------------------------------------------------------------

	// Need to loop through branches this time because the stock items are ranked within each branch
	
	$branchquery = "SELECT branch FROM branch $branchwhereclause ORDER BY branch";
	$branchresult = mysqli_query($link, $branchquery) or logerror(__LINE__." ".$branchquery,mysqli_error($link));	

	if($p_debug == "Y") echo __LINE__." Ranking stock items <br />\n";

	while ($branchrow = mysqli_fetch_row($branchresult)) 
	{			
		$branch = $branchrow[0];
		
		$numitems = 0;
		$numitemswithdemand = 0;

		// ----------------------------------------------------------------------------------------------------------------
		// LOOP THROUGH STOCK ITEMS
		// ----------------------------------------------------------------------------------------------------------------
	
		$stockitemquery = "SELECT branch, prodcode, demval12m FROM stock WHERE branch = $branch $prodcodewhereclause ORDER BY demval12m DESC";
		$stockitemresult = mysqli_query($link, $stockitemquery) or logerror(__LINE__." ".$stockitemquery,mysqli_error($link));	

		//if($p_debug == "Y") echo __LINE__." stock item query: ".$stockitemquery."<br />\n";

		$accumulatedpc = 0;
		$abcturnover = 0;
		$abcrank = 1;

		while ($stockitemrow = mysqli_fetch_row($stockitemresult))
		{			
			$branch 		= $stockitemrow[0];
			$stockitemcode 	= $stockitemrow[1];
			$demandvalue	= $stockitemrow[2];

			// ----------------------------------------------------------------------------------------------------------------
			// CALCULATE STOCK ITEM DEMAND AS PERCENTAGE OF BRANCH DEMAND
			// ----------------------------------------------------------------------------------------------------------------

			$percentage = 0;
			if($a_branchdemand[$branch] <> 0) $percentage = ($demandvalue/$a_branchdemand[$branch]) * 100;// branch demand has been previously loaded into an array
		
			$numitems++;
			
			if ($abcturnover > 0) $numitemswithdemand++;
			
			// ----------------------------------------------------------------------------------------------------------------
			// DETERMINE STOCK ITEM ABC RANKING
			// ----------------------------------------------------------------------------------------------------------------

			// Accumulate the turnover % and anything under A% is A, between A and B is B and so on.
			
			$accumulatedpc += $percentage;
			
			if($accumulatedpc <= $apc )	// e.g. 65%
			{
				$abcclass = "A";
			}
			elseif($accumulatedpc > $apc AND $accumulatedpc <= $apc + $bpc) 	// e.g. between 65% and 90% (65% + 25%)
			{
				$abcclass = "B";
			}
			elseif($abcturnover > 0)
			{
				$abcclass = "C";
			}
			else
			{
				$abcclass = "X";
			}

			if($p_debug == "Y") echo " $stockitemcode-$abcrank-$abcclass ";
		
			// ----------------------------------------------------------------------------------------------------------------
			// UPDATE STOCK ITEM ABC RANK AND CLASS
			// ----------------------------------------------------------------------------------------------------------------

			$updatequery = "UPDATE stock SET abcrank = $abcrank, abcclass = '$abcclass', abcturnover = $demandvalue WHERE prodcode = '$stockitemcode' AND branch = $branch";
			$updateresult = mysqli_query($link, $updatequery) or logerror(__LINE__." ".$updatequery,mysqli_error($link));
		
			// ----------------------------------------------------------------------------------------------------------------
			// UPDATE THE ABCANALYSIS KPI WITH THE NUMBER OF ITEMS AND TOTAL TURNOVER. PERCENTAGES CALCULATED LATER
			// ----------------------------------------------------------------------------------------------------------------

			// ABCANALYSIS
			// actualvalue1 = Turnover
			// actualvalue2 = % Of Total
			// actualvalue3 = # Items
			// actualvalue4 = % Of Items
			
			$KPIquery = "INSERT INTO imkpidata(identifier,period,branch,distbranch,suppliercode,analysiscode, analysisdesc,date,actualvalue1,actualvalue2,actualvalue3,actualvalue4) VALUES('ABCANALYSIS',3,$branch,0,'','$abcclass','$abcclass Class', '$today', $abcturnover, 0, 1, 0) ON DUPLICATE KEY UPDATE actualvalue1 = actualvalue1 + $abcturnover, actualvalue3 = actualvalue3 + 1";
			$KPIresult = mysqli_query($link, $KPIquery) or logerror(__LINE__." ".$KPIquery,mysqli_error($link));	

			$abcrank++;
		} // while ($stockitemrow = mysqli_fetch_row($stockitemresult))

		// ----------------------------------------------------------------------------------------------------------------
		// 3.B. UPDATE BRANCH TURNOVER KPI - # ITEMS AND # ITEMS WITH DEMAND
		// ----------------------------------------------------------------------------------------------------------------

		// BRANCHTURNOVER
		// actualvalue1 = Turnover
		// actualvalue2 = # Items
		// actualvalue3 = # Items With Turnover

		$KPIquery = "INSERT INTO imkpidata(identifier,period,branch,distbranch,suppliercode,analysiscode, analysisdesc,date,actualvalue2,actualvalue3) VALUES('BRANCHTURNOVER',3,$branch,0,'',$branch,'Branch Number $branch', '$today', $numitems, $numitemswithdemand) ON DUPLICATE KEY UPDATE actualvalue2 = $numitems, actualvalue3 = $numitemswithdemand";
		$KPIresult = mysqli_query($link, $KPIquery) or logerror(__LINE__." ".$KPIquery,mysqli_error($link));	

	} // while ($branchrow = mysqli_fetch_row($branchresult)) 

	// ----------------------------------------------------------------------------------------------------------------
	// UPDATE THE ABCANALYSIS KPI TO SHOW WHAT PROPORTION EACH CLASS IS OF THE TOTAL
	// ----------------------------------------------------------------------------------------------------------------

	// ABCANALYSIS
	// actualvalue1 = Turnover
	// actualvalue2 = % Of Total
	// actualvalue3 = # Items
	// actualvalue4 = % Of Items

	// BRANCHTURNOVER
	// actualvalue1 = Turnover
	// actualvalue2 = # Items
	// actualvalue3 = # Items With Turnover
			
	$abcquery = "UPDATE imkpidata as IM1 LEFT JOIN imkpidata as IM2 ON IM2.branch = IM1.branch AND IM2.date = IM1.date AND IM2.identifier = 'BRANCHTURNOVER'  SET IM1.actualvalue2 = (IM1.actualvalue1 / IM2.actualvalue1) * 100, IM1.actualvalue4 = (IM1.actualvalue3 / IM2.actualvalue3) * 100 WHERE IM1.identifier = 'ABCANALYSIS' AND IM1.date = '$today'";
	$abcresult = mysqli_query($link, $abcquery) or logerror(__LINE__." ".$abcquery,mysqli_error($link));	

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

	if($p_debug == "Y") echo __LINE__." ALL DONE!<br />\n";
	
?>
