<!-- This routine goes through the sales analysis and populates the demand history table

Steps:
1. Deletes existing demand as per parameters
2. Loops through the branch stock items as per parameters if applicable
3. Create a zero demand row (this is in case there is no sales analysis for the period, we still want a zero demand row)
4. For each stock item, get the sales analysis as per parameters
5. Update the demand


PARAMETERS
==========
auth		Is this an authorised run of this script Y/N ** THIS IS REQUIRED **
yearmonth   Year/Month  ** THIS IS REQUIRED **
stockitem	Stock item code (optional)
branch		Branch number (optional)
debug		Display debug messages Y/N (optional)

To run this with full options, use a URL like this: populatedemandhistory.php?auth=Y&stockitem=K00003&branch=1&yearmonth=202209&debug=Y 

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
	
	$sastockitemclause = "";
	$operator = " WHERE ";
	
	// Get the stock item code, if its been passed in
	if (isset($_GET['stockitem'])) 
	{
		$p_stockitemcode = $_GET['stockitem'];
		$stockitemclause = " WHERE prodcode = '$p_stockitemcode'";
		$sastockitemclause = " WHERE prodcode = '$p_stockitemcode'";
		$demandstockitemclause = " WHERE productcode = '$p_stockitemcode'";
		$operator = " AND ";
	}
	
	$sabranchclause = "";
	
	// Get the branch number, if its been passed in
	if (isset($_GET['branch'])) 
	{
		$p_branch = $_GET['branch'];
		$branchclause = $operator." branch = $p_branch";
		$sabranchclause = $operator." sa.branch = $p_branch";
		$demandbranchclause = $operator." branch = $p_branch";
		$operator = " AND ";			
	}	

	$yearmonthclause = "";
	
	// Get the year and month
	if (isset($_GET['yearmonth'])) 
	{
		$p_yearmonth = $_GET['yearmonth'];
		$sayearmonthclause = $operator." yearmonth = $p_yearmonth";
		
		$year   = substr($p_yearmonth,0,4);
		$month  = intval(substr($p_yearmonth,4,2));
		
		$demandyearmonthclause = $operator." year = ".$year." AND period = ".$month;
	}		
	
	// Get the debug flag, if its been passed in
	if (isset($_GET['debug'])) 
	{
		$p_debug = $_GET['debug'];
	}
	
	$whereclause = "";
	
	// If a selection parameter has been passed in, need the WHERE clause
	
	if(isset($_GET['stockitem']) or isset($_GET['branch']) or isset($_GET['yearmonth'])) $whereclause = " WHERE ";
	
	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or die ("Unable to connect!"); 

	// Enable autocommit
	mysqli_autocommit($link, TRUE);

	//========================================================================================================
	// CLEAR DEMAND AS PER PARAMETERS
	//========================================================================================================
	
	$query = "DELETE FROM demand ".$demandstockitemclause." ".$demandbranchclause." ".$demandyearmonthclause;
	
	if($p_debug == "Y") echo __LINE__." DELETE FROM demand clause: ".$query."<br />\n";

	$result = mysqli_query($link, $query) or logerror(__LINE__." ".$query,mysqli_error($link));

	//========================================================================================================
	// LOOP THROUGH STOCK ITEMS
	//========================================================================================================

	$stockitemquery = "SELECT prodcode, branch FROM stock ORDER BY branch, prodcode ".$branchclause." ".$stockitemclause;

	if($p_debug == "Y") echo __LINE__." Stock item query: ".$stockitemquery."<br />\n";
	
	$stockitemresult = mysqli_query($link, $stockitemquery) or die ("Error in query: $stockitemquery. ".mysqli_error($link));
	
	while ($stockitemrow = mysqli_fetch_row($stockitemresult)) 
	{	
		$productcode = $stockitemrow[0];
		$branch      = $stockitemrow[1];

		if($p_debug == "Y") echo __LINE__." Stock item : ".$productcode."<br />\n";

		//========================================================================================================
		// CREATE ZERO DEMAND HISTORY ROW (IN CASE THERE IS NO SALES ANALYSIS FOR THE PERIOD
		//========================================================================================================

		$demandquery = "INSERT INTO demand(productcode, branch, year, period, type, forecastdemandqty, demandqty, demandnum, demandval, extdemandqty, extdemandnum, extdemandval, intdemandqty, intdemandnum, intdemandval) VALUES('$productcode',$branch, $year, $month, 'M', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)"; 

		$demandresult = mysqli_query($link, $demandquery) or die ("Error in query: $demandquery. ".mysqli_error($link));

		//========================================================================================================
		// GET SALES ANALYSIS FOR SELECTED STOCK ITEM AND UPDATE DEMAND
		//========================================================================================================

		$salesanalysisquery = "SELECT sa.prodcode, sa.branch, sa.yearmonth, sa.quantity, sa.cost, sa.date, cu.accounttype FROM salesanalysis AS sa LEFT JOIN customer AS cu ON cu.account = sa.account WHERE sa.prodcode = '$productcode' AND sa.branch = $branch AND yearmonth = $p_yearmonth";
		
		// if($p_debug == "Y") echo __LINE__."salesanalysisquery: ".$salesanalysisquery."<br />\n";
		
		$salesanalysisresult = mysqli_query($link, $salesanalysisquery) or die ("Error in query: $salesanalysisquery. ".mysqli_error($link));

		$x = 0;
		
		while ($row = mysqli_fetch_row($salesanalysisresult)) 
		{	
			$productcode = $row[0];
			$branch      = $row[1];
			$yearmonth   = $row[2];
			$quantity    = $row[3];
			$cost	     = $row[4];
			$date        = $row[5];
			$accounttype = $row[6];

			// if($p_debug == "Y") echo __LINE__." Product: ".$productcode." branch: ".$branch." yearmonth: ".$yearmonth." quantity: ".$quantity."<br />\n";
			
			$year  = date("Y", strtotime($date));
			$week  = date("W", strtotime($date));
			$month = date("n", strtotime($date));
			
			$extdemandqty = 0;
			$extdemandnum = 0;
			$extdemandval = 0;
			
			$intdemandqty = 0;
			$intdemandnum = 0;
			$intdemandval = 0;
			
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

			$demandquery = "UPDATE demand SET demandqty = demandqty + $quantity, demandnum = demandnum + 1, demandval = demandval + $cost, extdemandqty = extdemandqty + $extdemandqty, extdemandnum = extdemandnum + 1, extdemandval = extdemandval + $extdemandval, intdemandqty = intdemandqty + $intdemandqty, intdemandnum = intdemandnum + 1, intdemandval = intdemandval + $intdemandval ".$demandstockitemclause." WHERE branch = $branch AND productcode = '$productcode' AND year = $year AND period = $month AND type = 'M'";

			// if($p_debug == "Y") echo __LINE__." Demand query: ".$demandquery."<br />\n";

			$demandresult = mysqli_query($link, $demandquery) or die ("Error in query: $demandmquery. ".mysqli_error($link));
		} // 		while ($row = mysqli_fetch_row($salesanalysisresult)) 
	}

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
	
	function logerror($query,$error)
	{
		$email = "kieran" . "@" . "kk-cs" . ".co.uk";
		
		$errormsg = "Hi Kieran, an error just occurred in ".__FILE__.". The query was '".$query."' and the error was '".$error."'<BR>";
		
		error_log($errormsg);
		error_log($errormsg,1,$email);
		
		return true;
	}
?>
