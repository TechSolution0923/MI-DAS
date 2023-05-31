<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');
	
	error_reporting(E_ALL);	
	
	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));

	// disable autocommit
	mysqli_autocommit($link, FALSE);

	$today = date('Y-m-d');
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// CALCULATE OUTSTANDING KPIs *** NOTE THIS DOESNT READ A CSV FILE ***
	// ------------------------------------------------------------------------------------------------------------------------------

	$query = "DELETE FROM kpidata WHERE identifier LIKE 'MIDASOUTST%' AND date = '$today'";
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
	
	$affectedrows = mysqli_affected_rows($link);

	if ($affectedrows > 0) // Only write the logfile if rows affected.
	{
		$logfile = "logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = date('Y-m-d_Hia')." Outstanding KPIs - ".$affectedrows." rows deleted\n";
		fwrite($fh, $stringData);
		fclose($fh);	
	}	 
	
	$query = "SELECT status, ordtype, branch, repcode, sales FROM salesorders WHERE status IN ('PRO','PUR','SBO','PIK','IBT','HLD', 'WDL') AND ordtype in ('SL','CR')"; 
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

	$affectedrows = 0;
	
	while ($row = mysqli_fetch_row($result)) 
	{	
		$status		= $row[0];
		$ordtype	= $row[1];
		$branch		= $row[2];
		$repcode	= $row[3];
		$sales		= $row[4];

		// Negate sales value for CR and BI (as they're positive values in the sales order table)
		if ($ordtype == 'CR' or $ordtype == "BI") $sales = $sales * - 1;

		$identifier = "MIDASOUTST" . $status;	// E.g. MIDASOUTSTCOM, MIDASOUTSTPIK etc.
		
		$insertquery = "INSERT INTO kpidata( id, identifier, period, level, analysis, date, actualvalue1) VALUES (0, '$identifier', 1, '$branch', '$repcode', '$today', $sales) ON DUPLICATE KEY UPDATE actualvalue1 = actualvalue1 + $sales"; 
		
		$insertresult = mysqli_query($link, $insertquery) or logerror($query,mysqli_error($link));	

		$affectedrows++;	// Not using the mysqli_affected_rows function here because it returns 2 if the row is updated
	}	

	if ($affectedrows > 0) // Only write the logfile if rows affected.
	{
		$logfile = "logfile.txt";
		$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
		$stringData = date('Y-m-d_Hia')." Outstanding KPIs - ".$affectedrows." rows inserted\n";
		fwrite($fh, $stringData);
		fclose($fh);	
	}	 
	
	mysqli_commit($link);
	
?>