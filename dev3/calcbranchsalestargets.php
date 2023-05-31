<!-- This script calculates the branch sales targets for the current year (Jan-Dec) from sales last year. It assumes this script is being run in January of the new year
<!-- The target is the sales for the branch/period + 5%

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
	// DERIVE BRANCH SALES FROM PRODUCTSALES
	//========================================================================================================
	
	$productsalesquery = "SELECT productsales.branch, sum(msales1), sum(msales2), sum(msales3), sum(msales4), sum(msales5), sum(msales6), sum(msales7), sum(msales8), sum(msales9), sum(msales10), sum(msales11), sum(msales12), sum(msales13), marginok, margingood FROM productsales LEFT JOIN branch ON branch.branch = productsales.branch GROUP BY 1 ORDER BY 1";

	$productsalesresult = mysqli_query($link, $productsalesquery) or logerror(__LINE__." ".$productsalesquery,mysqli_error($link));

	$salestarget = array();
	
	while ($productsalesrow = mysqli_fetch_row($productsalesresult)) 
	{	
		$branch 			= $productsalesrow[0];
		$salestarget[12]	= $productsalesrow[1] * 1.05;
		$salestarget[11]	= $productsalesrow[2] * 1.05;
		$salestarget[10]	= $productsalesrow[3] * 1.05;
		$salestarget[9]		= $productsalesrow[4] * 1.05;
		$salestarget[8]		= $productsalesrow[5] * 1.05;
		$salestarget[7]		= $productsalesrow[6] * 1.05;
		$salestarget[6]		= $productsalesrow[7] * 1.05;
		$salestarget[5]		= $productsalesrow[8] * 1.05;
		$salestarget[4]		= $productsalesrow[9] * 1.05;
		$salestarget[3]		= $productsalesrow[10] * 1.05;
		$salestarget[2]		= $productsalesrow[11] * 1.05;
		$salestarget[1]		= $productsalesrow[12] * 1.05;
		$marginok			= $productsalesrow[13];
		$margingood 		= $productsalesrow[14];

		for($x=1; $x <= 12; $x++)
		{
			$yearmonth = $curyear.str_pad($x,2,"0", STR_PAD_LEFT); // Make the year month, padding the month

			//========================================================================================================
			// CREATE BRANCH SALES TARGETS
			//========================================================================================================

			$insertquery = "INSERT INTO branchsalestarget(branch, yearmonth, salestarget, marginok, margingood) VALUES($branch, $yearmonth, $salestarget[$x], $marginok, $margingood)
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
