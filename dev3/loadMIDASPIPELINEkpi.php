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
	// CALCULATE MIDASPIPELINE KPIs FROM SALES ORDERS
	// ------------------------------------------------------------------------------------------------------------------------------

	$query = "SELECT branch, repcode, pipelinestage, SUM(sales) FROM salesorders LEFT JOIN product ON product.code = salesorders.prodcode where ordtype = 'QT' AND status NOT IN('DEL','LOS') AND pipelinestage IN ('01','02','03','04') GROUP BY repcode, pipelinestage"; 
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

	$affectedrows = 0;
	
	while ($row = mysqli_fetch_row($result)) 
	{	
		$branch		= $row[0];
		$repcode	= $row[1];
		$pipelinestage	= $row[2];
		$sales		= $row[3];
			
		// By type
		
		$identifier = "MIDASPIPELINE" . $pipelinestage;	// E.g. MIDASQUOTESP, MIDASQUOTESZ etc.
		
		$insertquery = "INSERT INTO kpidata( id, identifier, period, level, analysis, date, actualvalue1) VALUES (0, '$identifier', 1, '$branch', '$repcode', '$today', $sales) 
					ON DUPLICATE KEY UPDATE actualvalue1 = $sales"; 
		
		$insertresult = mysqli_query($link, $insertquery) or logerror($query,mysqli_error($link));	
			
		$affectedrows++;
	}
	
	mysqli_commit($link);
	
	$affectedrows = mysqli_affected_rows($link);

	if ($affectedrows > 0) // Only write the logfile if rows affected.
	{
		$logfile = "logfile.txt";
		$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
		$stringData = date('Y-m-d_Hia')." Pipeline KPI - ".$affectedrows." rows inserted\n";
		fwrite($fh, $stringData);
		fclose($fh);	
	}	 
?>