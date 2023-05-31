<?php
	// Set the content type
	header('Content-type: application/csv');
	
	// Set the file name option to a filename of your choice.
	header('Content-Disposition: attachment; filename=customerproductsales.csv');
	
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
	
	$year0  = substr($curyearmonth,0,4);
	$month0 = substr($curyearmonth,4,2);

	// disable autocommit
	mysqli_autocommit($link, FALSE);

	// Get the account code if its been passed in
	if (isset($_GET['account'])) $accountcodeparameter = $_GET['account'];

	// Write CSV header row
	fputcsv($f, array("Account Code", "Code Level", "Code","Year Month","Sales Value"));

	$codelevel = "P";
	
	$custquery = "SELECT account FROM customer";

	// If the account code has been passed in, use it
	if (isset($_GET['account'])) $custquery .= " WHERE account = '$accountcodeparameter' ORDER BY account";

	$custresult = mysqli_query($link, $custquery) or logerror($custquery,mysqli_error($link));
	while ($custrow = mysqli_fetch_row($custresult)) 
	{
		$account = $custrow[0];

		$productquery = "SELECT code FROM product";
		$productresult = mysqli_query($link, $productquery) or logerror($productquery,mysqli_error($link));
		while ($productrow = mysqli_fetch_row($productresult)) 
		{
			$productcode = $productrow[0];
		
			// Loop through each monthly iteration on the sales summary table
			for ($x = 0; $x <= 35 ; $x++)
			{
				$month = $month0 - $x;
				$yearoffset = INTVAL((($month/-12)+1));
				$year = $year0 - $yearoffset;
				if ($month <= 0) $month += (12*$yearoffset);
				$yearmonth = ($year * 100) + $month;
				
				$salesfield = "msales".$x;
				
				$query = "SELECT $salesfield FROM customerprodsales WHERE account = '$account' AND prodcode = '$productcode'";
				
				$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
				$row = mysqli_fetch_row($result);
				
				$salesvalue = $row[0];

				// Write to the csv
				fputcsv($f, array($account, $codelevel, $productcode, $yearmonth, $salesvalue));
			}
		}
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