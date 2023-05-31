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
	// CALCULATE MIDASQUOTE KPIs FROM SALES ORDERS
	// ------------------------------------------------------------------------------------------------------------------------------

	$query = "DELETE FROM kpidata WHERE identifier LIKE '%MIDASQUOTE%' AND date = '$today'";
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));


	$query = "SELECT branch, repcode, pac1code,SUM(sales) FROM salesorders LEFT JOIN product ON product.code = salesorders.prodcode where ordtype = 'QT' AND status NOT IN('DEL','LOS') AND pipelinestage IN ('01','02','03','04') AND quotefolldate BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-t')."' GROUP BY repcode, pac1code"; 
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

	$affectedrows = 0;

    // Get the values by rep and pac1 code
	
	while ($row = mysqli_fetch_row($result)) 
	{	
		$branch		= $row[0];
		$repcode	= $row[1];
		$pac1code	= $row[2];
		$sales		= $row[3];
			
		// By type
		
		$identifier = "MIDASQUOTES" . $pac1code;	// E.g. MIDASQUOTESP, MIDASQUOTESZ etc.
		
		$insertquery = "INSERT INTO kpidata( id, identifier, period, level, analysis, date, actualvalue1) VALUES (0, '$identifier', 1, '$branch', '$repcode', '$today', $sales) 
					ON DUPLICATE KEY UPDATE actualvalue1 = $sales"; 
		
		$insertresult = mysqli_query($link, $insertquery) or logerror($query,mysqli_error($link));	
			
		$affectedrows++;
	}

    // Count the number of orders by rep and pac1 code

	$query = "SELECT branch, repcode, pac1code,COUNT(DISTINCT orderno) FROM salesorders LEFT JOIN product ON product.code = salesorders.prodcode where ordtype = 'QT' AND status NOT IN('DEL','LOS') AND pipelinestage IN ('01','02','03','04') AND quotefolldate BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-t')."' GROUP BY repcode, pac1code"; 
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

	while ($row = mysqli_fetch_row($result)) 
	{	
		$branch		= $row[0];
		$repcode	= $row[1];
		$pac1code	= $row[2];
		$numquotes	= $row[3];
			
		// By type
		
		$identifier = "MIDASQUOTES" . $pac1code;	// E.g. MIDASQUOTESP, MIDASQUOTESZ etc.
		
		$insertquery = "INSERT INTO kpidata( id, identifier, period, level, analysis, date, actualvalue2) VALUES (0, '$identifier', 1, '$branch', '$repcode', '$today', $numquotes) 
					ON DUPLICATE KEY UPDATE actualvalue2 = $numquotes"; 
		
		$insertresult = mysqli_query($link, $insertquery) or logerror($query,mysqli_error($link));	
			
		$affectedrows++;
	}

	
	mysqli_commit($link);
	
	$affectedrows = mysqli_affected_rows($link);

	if ($affectedrows > 0) // Only write the logfile if rows affected.
	{
		$logfile = "logfile.txt";
		$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
		$stringData = date('Y-m-d_Hia')." Daily KPIs - ".$affectedrows." rows inserted\n";
		fwrite($fh, $stringData);
		fclose($fh);	
	}	 
?>