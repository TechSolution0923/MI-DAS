<?php
	// Set the content type
	header('Content-type: application/csv');
	
	// Set the file name option to a filename of your choice.
	header('Content-Disposition: attachment; filename=usersales.csv');
	
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

	// Get the MI-DAS user id if its been passed in
	if (isset($_GET['userid'])) $useridparameter = $_GET['userid'];

	// Write CSV header row
	fputcsv($f, array("User ID", "Year Month","Sales Value"));

	$codelevel = 0;
	$code = "";
	
	$userquery = "SELECT userid FROM users";

	// If the user id has been passed in, use it
	if (isset($_GET['userid'])) $userquery .= " WHERE userid = $useridparameter ORDER BY userid";

	$userresult = mysqli_query($link, $userquery) or logerror($userquery,mysqli_error($link));
	while ($userrow = mysqli_fetch_row($userresult)) 
	{
		$userid = $userrow[0];

		// Get the reps for this user
		$userrepquery = "SELECT repcode FROM userreps WHERE userid = $userid";
		$userrepresult = mysqli_query($link, $userrepquery) or logerror($userrepquery,mysqli_error($link));
		
		$userrepclause = "";
		
		while ($userreprow = mysqli_fetch_row($userrepresult)) 
		{
			$repcode = $userreprow[0];
			
			if ($userrepclause != "") $userrepclause .= ","; // Not the first time in 
			
			$userrepclause .= "'".$repcode."'"; // Add the repcode in quotes
		}
		
		// Loop through each monthly iteration on the sales summary table
		for ($x = 0; $x <= 35 ; $x++)
		{
			$month = $month0 - $x;
			$yearoffset = INTVAL((($month/-12)+1));
			$year = $year0 - $yearoffset;
			if ($month <= 0) $month += (12*$yearoffset);
			$yearmonth = ($year * 100) + $month;
			
			$salesfield = "msales".$x;
			
			// Get total of sales for the month for all customers associated with the user
			$query = "SELECT SUM($salesfield) FROM customersales LEFT JOIN customerreps ON customerreps.account = customersales.account WHERE customerreps.repcode IN (".$userrepclause.")";
			
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
			$row = mysqli_fetch_row($result);
			
			$salesvalue = $row[0];

			// Write to the csv
			fputcsv($f, array($userid, $yearmonth, $salesvalue));
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