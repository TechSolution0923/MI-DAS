<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	// ini_set('log_errors',1);
	// ini_set('error_log', 'error_log');

	// Start time
	$start_datetime = date('Y-m-d H:i:s');
	
	//error_reporting(E_ALL);	
	
	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));

	// disable autocommit
	mysqli_autocommit($link, FALSE);

	$today = date('Y-m-d');
	$SOM   = date('Y-m-01');

    // Create temporary table to store number of quotes at each PAC 1

	$createquery = "CREATE TEMPORARY TABLE quotecount ( orderno INT(8) NOT NULL, pac1code CHAR(8) NOT NULL, analysis CHAR(30))";
	$createresult = mysqli_query($link, $createquery) or logerror($createquery,mysqli_error($link));

	$indexquery = "ALTER TABLE quotecount ADD PRIMARY KEY (orderno, pac1code, analysis)";
	$indexresult = mysqli_query($link, $indexquery) or logerror($indexquery,mysqli_error($link));
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// CALCULATE SNAPSHOT KPIs FROM SALES ORDERS
	// ------------------------------------------------------------------------------------------------------------------------------

    $isolationlevelquery = "SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED";
	$isolationlevelresult = mysqli_query($link, $isolationlevelquery) or logerror($isolationlevelquery,mysqli_error($link));

	$query = "UPDATE kpidata SET actualvalue1 = 0, actualvalue2 = 0, actualvalue3 = 0, actualvalue4 = 0 WHERE ( identifier LIKE 'MIDASHELDOMR%' OR identifier LIKE 'MIDASWAIT%' OR identifier LIKE 'MIDASPOSTED%' OR identifier LIKE 'MIDASOUTST%' OR identifier LIKE 'MIDASQUOTE%' OR identifier LIKE 'MIDASTODAY%' ) AND date = '$today'";  
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
	
	$orderquery = "SELECT ordtype, repcode, progress, snapstatus, snapdate, sales, branch, status, prodcode, quotefolldate, pipelinestage, datein, orderno FROM salesorders FORCE INDEX(status) WHERE status NOT IN ( 'DEL', 'LOS' )"; 
	$orderresult = mysqli_query($link, $orderquery) or logerror($orderquery,mysqli_error($link));
	
	while ($orderrow = mysqli_fetch_row($orderresult)) 
	{	
		$ordtype		= $orderrow[0];
		$repcode		= $orderrow[1];
		$progress		= $orderrow[2];
		$snapstatus		= $orderrow[3];
		$snapdate		= $orderrow[4];
		$sales			= $orderrow[5];
		$branch			= $orderrow[6];
		$status     	= $orderrow[7];
		$prodcode   	= $orderrow[8];
		$quotefolldate 	= $orderrow[9];
		$pipelinestage	= $orderrow[10];
		$datein			= $orderrow[11];
		$orderno		= $orderrow[12];
	
		if($ordersloaded == "Y") // Only do this if new sales order data exists. This flag is set in loadorders.php
		{
			// -------------
			// SNAPSHOT KPIS
			// -------------
			
			$identifier = "";
			
			// SL awaiting posting
			if ($ordtype == 'SL' and $progress == '2' and $snapstatus == 9) $identifier = "MIDASWAITSL";

			// CR awaiting posting
			if ($ordtype == 'CR' and $progress == '2' and $snapstatus == 9) $identifier = "MIDASWAITCR";

			// SL held in OMR
			if ($ordtype == 'SL' and $progress == '2' and $snapstatus == 7) $identifier = "MIDASHELDOMRSL";

			// CR held in OMR
			if ($ordtype == 'CR' and $progress == '2' and $snapstatus == 7) $identifier = "MIDASHELDOMRCR";
			
			// SL posted
			if ($ordtype == 'SL' and $progress == '4' and $snapstatus == 9 and $snapdate == $today) $identifier = "MIDASPOSTEDSL";

			// CR awaiting posting
			if ($ordtype == 'CR' and $progress == '4' and $snapstatus == 9 and $snapdate == $today) $identifier = "MIDASPOSTEDCR";

			if ($identifier != "")
			{		
				$insertquery = "INSERT INTO kpidata( id, identifier, period, level, analysis, date, actualvalue1) VALUES (0, '$identifier', 1, '$branch', '$repcode', '$today', $sales) 
				ON DUPLICATE KEY UPDATE actualvalue1 = actualvalue1 + $sales"; 
			
				$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));
			}

			// -------------
			// OUTSTANDING KPIS
			// -------------

			if ($status != "COM" and ($ordtype == 'SL' or $ordtype == 'CR')){
				
				$outstandingsales = $sales;
				
				// Negate sales value for CR (as its positive values in the sales order table)
				if ($ordtype == 'CR') $outstandingsales = $sales * - 1;

				$identifier = "MIDASOUTST" . $status;	// E.g. MIDASOUTSTCOM, MIDASOUTSTPIK etc.
			
				$insertquery = "INSERT INTO kpidata( id, identifier, period, level, analysis, date, actualvalue1) VALUES (0, '$identifier', 1, '$branch', '$repcode', '$today', $outstandingsales) ON DUPLICATE KEY UPDATE actualvalue1 = actualvalue1 + $outstandingsales"; 
			
				$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));	
			}
		} // if($ordersloaded == "Y")
		
		// -------------
		// QUOTES KPIS
		// -------------
		
		if($quotesloaded == "Y") // Only do this if new quotes data exists. This flag is set in loadquotes.php
		{

			if ($ordtype == 'QT' and $datein >= $SOM ){ //  and $quotefolldate >= $SOM	and ($pipelinestage == '01' or $pipelinestage == '02' or $pipelinestage == '03' or $pipelinestage == '04')){

				// Get the PAC1 code from the product
				
				$productquery = "SELECT pac1code FROM product WHERE code = '$prodcode'";
				$productresult = mysqli_query($link, $productquery) or logerror($productquery,mysqli_error($link));
			
				$productrow = mysqli_fetch_row($productresult);
				$pac1code	= $productrow[0];

				$identifier = "MIDASQUOTES" . $pac1code;	// E.g. MIDASQUOTESP, MIDASQUOTESZ etc.

				$insertquery = "INSERT INTO kpidata( id, identifier, period, level, analysis, date, actualvalue1) VALUES (0, '$identifier', 1, '$branch', '$repcode', '$today', $sales) ON DUPLICATE KEY UPDATE actualvalue1 = actualvalue1 + $sales"; 
			
				$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));	
				
				$countinsertquery = "INSERT IGNORE INTO quotecount(orderno,pac1code,analysis) VALUES($orderno, '$pac1code','$repcode')";
				$countinsertresult = mysqli_query($link, $countinsertquery) or logerror($countinsertquery,mysqli_error($link));		
			}

			// -------------
			// PIPELINE KPIS
			// -------------
			
			if ($ordtype == 'QT' and ($pipelinestage == '01' or $pipelinestage == '02' or $pipelinestage == '03' or $pipelinestage == '04')){

				// Get the PAC1 code from the product
				
				$productquery = "SELECT pac1code FROM product WHERE code = '$prodcode'";
				$productresult = mysqli_query($link, $productquery) or logerror($productquery,mysqli_error($link));
			
				$productrow = mysqli_fetch_row($productresult);
				$pac1code	= $productrow[0];

				$identifier = "MIDASPIPELINE" . $pipelinestage;	// E.g. MIDASQUOTES01, MIDASQUOTES02 etc.

				$insertquery = "INSERT INTO kpidata( id, identifier, period, level, analysis, date, actualvalue1) VALUES (0, '$identifier', 1, '$branch', '$repcode', '$today', $sales) ON DUPLICATE KEY UPDATE actualvalue1 = actualvalue1 + $sales"; 
			
				$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));	
			}
		} // if($quotessloaded == "Y")

		// -------------
		// TODAY'S KPIS
		// -------------
		
		// There is no check on ordersloaded or quotesloaded because this script is only run if one or other has been loaded, so these kpis do need to be re-done

		if ($datein == $today){

			$outstandingsales = $sales;

			// Negate sales value for CR (as its positive values in the sales order table)
			if ($ordtype == 'CR') $outstandingsales = $sales * - 1;

			$identifier = "MIDASTODAY" . $ordtype;	// E.g. MIDASTODAYSL, MIDASTODAYCR etc.
		
			$insertquery = "INSERT INTO kpidata( id, identifier, period, level, analysis, date, actualvalue1) VALUES (0, '$identifier', 1, '$branch', '$repcode', '$today', $sales) ON DUPLICATE KEY UPDATE actualvalue1 = actualvalue1 + $sales"; 
		
			$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));	

			$identifier = "MIDASTODAY" . $status;	// E.g. MIDASTODAYCOM, MIDASTODAYPIK etc.
		
			$insertquery = "INSERT INTO kpidata( id, identifier, period, level, analysis, date, actualvalue1) VALUES (0, '$identifier', 1, '$branch', '$repcode', '$today', $sales) ON DUPLICATE KEY UPDATE actualvalue1 = actualvalue1 + $sales"; 
		
			$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));	
		}
	}
	
	// When all is done, count the number of quotes at wach PAC1 and update the MIDASQUOTES kpi
	
	$countquery = "SELECT COUNT(*), pac1code, analysis FROM quotecount GROUP BY 2,3"; 
	$countresult = mysqli_query($link, $countquery) or logerror($countquery,mysqli_error($link));
	
	while ($countrow = mysqli_fetch_row($countresult)) 
	{	
		$count		= $countrow[0];
		$pac1code	= $countrow[1];
		$analysis   = $countrow[2];
	
		$identifier = "MIDASQUOTES" . $pac1code;	// E.g. MIDASQUOTESP, MIDASQUOTESZ etc.

		$updatequery = "UPDATE kpidata SET actualvalue2 = $count WHERE identifier = '$identifier' AND analysis = '$analysis'"; 
		$updateresult = mysqli_query($link, $updatequery) or logerror($updatequery,mysqli_error($link));
	}		

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
	
	mysqli_commit($link);
?>