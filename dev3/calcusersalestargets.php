<!-- This script calculates the user sales targets for the current year (Jan-Dec) from sales last year. It assumes this script is being run in January of the new year
<!-- The target is the sales for the branch/period + 5%. This was written for LSK who have no sales reps, so target goes against user 1

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
	
	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or die ("Unable to connect!"); 

	// Enable autocommit
	mysqli_autocommit($link, TRUE);

	// Get the current yearmonth

	$query = "SELECT curyearmonth FROM system";
	$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error($link));

	$row = mysqli_fetch_row($result);
	$curyearmonth = $row[0];
	
	$curyear = substr($curyearmonth,0,4);
	$curmonth = substr($curyearmonth,4,2);

	//========================================================================================================
	// DERIVE USER SALES FROM CUSTOMERSALES
	//========================================================================================================
	
	$customersalesquery = "SELECT sum(msales1), sum(msales2), sum(msales3), sum(msales4), sum(msales5), sum(msales6), sum(msales7), sum(msales8), sum(msales9), sum(msales10), sum(msales11), sum(msales12), sum(msales13) FROM customersales";

	$customersalesresult = mysqli_query($link, $customersalesquery) or logerror(__LINE__." ".$customersalesquery,mysqli_error($link));

	$salestarget = array();
	
	while ($customersalesrow = mysqli_fetch_row($customersalesresult)) 
	{	
		$salestarget[12]	= $customersalesrow[0] * 1.05;
		$salestarget[11]	= $customersalesrow[1] * 1.05;
		$salestarget[10]	= $customersalesrow[2] * 1.05;
		$salestarget[9]		= $customersalesrow[3] * 1.05;
		$salestarget[8]		= $customersalesrow[4] * 1.05;
		$salestarget[7]		= $customersalesrow[5] * 1.05;
		$salestarget[6]		= $customersalesrow[6] * 1.05;
		$salestarget[5]		= $customersalesrow[7] * 1.05;
		$salestarget[4]		= $customersalesrow[8] * 1.05;
		$salestarget[3]		= $customersalesrow[9] * 1.05;
		$salestarget[2]		= $customersalesrow[10] * 1.05;
		$salestarget[1]		= $customersalesrow[11] * 1.05;

		for($x=1; $x <= 12; $x++)
		{
			$yearmonth = $curyear.str_pad($x,2,"0", STR_PAD_LEFT); // Make the year month, padding the month

			//========================================================================================================
			// CREATE USER SALES TARGETS
			//========================================================================================================
			
			$insertquery = "INSERT INTO usersalestarget(userid, yearmonth, salestarget) VALUES(1, $yearmonth, $salestarget[$x])
			ON DUPLICATE KEY UPDATE salestarget = $salestarget[$x]"; 

			$insertresult = mysqli_query($link, $insertquery) or logerror(__LINE__." ".$insertquery,mysqli_error($link));
		}
	}

	function logerror($query,$error)
	{
		$email = "kieran" . "@" . "kk-cs" . ".co.uk";
		
		$errormsg = "Hi Kieran, an error just occurred in ".__FILE__.". The query was '".$query."' and the error was '".$error."'<BR>";
		
		error_log($errormsg);
		error_log($errormsg,1,$email);
		
		return true;
	}
?>
