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
	// CALCULATE SNAPSHOT KPIs FROM SALES ORDERS
	// ------------------------------------------------------------------------------------------------------------------------------

	$query = "DELETE FROM kpidata WHERE ( identifier LIKE 'MIDASHELDOMR%' OR identifier LIKE 'MIDASWAIT%' OR identifier LIKE 'MIDASPOSTED%' ) AND date = '$today'";
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
	
	$affectedrows = mysqli_affected_rows($link);

	if ($affectedrows > 0) // Only write the logfile if rows affected.
	{
		$logfile = "logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = date('Y-m-d_Hia')." Snapshot KPIs - ".$affectedrows." rows deleted\n";
		fwrite($fh, $stringData);
		fclose($fh);	
	}	 

	$query = "SELECT ordtype, repcode, progress, snapstatus, snapdate, sales, branch FROM salesorders WHERE progress IN ('2','4') AND status <> 'DEL' AND (snapdate = '$today' OR snapdate = '0000-00-00')"; 
	
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

	$affectedrows = 0;
	
	while ($row = mysqli_fetch_row($result)) 
	{	
		$ordtype	= $row[0];
		$repcode	= $row[1];
		$progress	= $row[2];
		$snapstatus	= $row[3];
		$snapdate	= $row[4];
		$sales		= $row[5];
		$branch		= $row[6];
		
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
		
			$insertresult = mysqli_query($link, $insertquery) or logerror($query,mysqli_error($link));
			
			$affectedrows++;
		}

	}
	
	mysqli_commit($link);

	if ($affectedrows > 0) // Only write the logfile if rows affected.
	{
		$logfile = "logfile.txt";
		$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
		$stringData = date('Y-m-d_Hia')." Snapshot KPIs - ".$affectedrows." rows inserted\n";
		fwrite($fh, $stringData);
		fclose($fh);	
	}	 
	
?>