<!-- This script creates zeroed demand rows for every branch/stock item for the past 5 years. This is so that there are no gaps in the demand history. It leaves existing demand rows as they are

<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');
	ini_set('​max_execution_time', 0); //0=NOLIMIT

	// Start time
	$start_datetime = date('Y-m-d H:i:s');

	error_reporting(E_ALL);	
	
	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));

	// Enable autocommit
	mysqli_autocommit($link, TRUE);

	// Get the current yearmonth
	
	$systemquery = "SELECT curyearmonth FROM system";
	$systemresult = mysqli_query($link, $systemquery) or logerror($systemquery,mysqli_error($link));
	$systemrow = mysqli_fetch_row($systemresult);
	$curyearmonth = $systemrow[0];
	
	// Extract the year and month
	
	$curyear  = substr($curyearmonth,0,4);
	$curmonth = substr($curyearmonth,4,2);

	$fromyear = $curyear - 5; // Going back 5 years / 60 months
	
	$stockitemquery = "SELECT branch, prodcode FROM stock ORDER BY branch, prodcode";
	$stockitemresult = mysqli_query($link, $stockitemquery) or die ("Error in query: $stockitemquery. ".mysqli_error($link));

	while ($stockitemrow = mysqli_fetch_row($stockitemresult)) 
	{	
		$branch 		= $stockitemrow[0];
		$productcode 	= $stockitemrow[1]; 

		for($year = $fromyear; $year <= $curyear; $year++)
		{
			for($month = 1; $month <= 12; $month++)
			{
				// Insert zeroed row. If there is an existing row already there, leave it.
				$demandquery = "INSERT INTO demand(productcode, branch, year, period, type, forecastdemandqty, demandqty, demandnum, demandval, extdemandqty, extdemandnum, extdemandval, intdemandqty, intdemandnum, intdemandval) VALUES('$productcode',$branch, $year, $month, 'M', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0) ON DUPLICATE KEY UPDATE productcode = productcode, branch = branch, year = year, period = period, type = type"; 

				$demandresult = mysqli_query($link, $demandquery) or die ("Error in query: $demandquery. ".mysqli_error($link));
			}
		}
	}
?>