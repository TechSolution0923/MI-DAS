<!-- This script goes through the customer pac2 sales targets, links out from the customer reps, through user reps to get the user id and create the pac2 sales targets for the dashboard

<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	error_reporting(0);	

	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));
	
	// Clear out existing pac2 sales targets
	
	$truncatepac2salestargetquery = "TRUNCATE TABLE pac2salestarget";
	$truncatepac2salestargetresult = mysqli_query($link, $truncatepac2salestargetquery) or die ("Error in query: $truncatepac2salestargetquery. ".mysqli_error($link));
	
	// Rollup the existing customer pac2 sales targets
	$rolluppac2salestargetquery = "SELECT userreps.userid, customerpac2salestarget.pac2code, customerpac2salestarget.yearmonth, SUM(customerpac2salestarget.salestarget) FROM customerpac2salestarget LEFT JOIN customerreps ON customerreps.account = customerpac2salestarget.account LEFT JOIN userreps ON userreps.repcode = customerreps.repcode GROUP BY 1,2,3 order by 1, 2, 3";
	
	$rolluppac2salestargetresult = mysqli_query($link, $rolluppac2salestargetquery) or die ("Error in query: $rolluppac2salestargetquery. ".mysqli_error($link));
	
	while ($rolluppac2salestargetrow = mysqli_fetch_row($rolluppac2salestargetresult)) 
	{
		$userid 	= $rolluppac2salestargetrow[0];
		$pac2code	= $rolluppac2salestargetrow[1];
		$yearmonth	= $rolluppac2salestargetrow[2];
		$salestarget= $rolluppac2salestargetrow[3];
		
		// ------------------------------------------------------------------------------------------------------------------------------
		// CREATE PAC2 Sales Target Row
		// ------------------------------------------------------------------------------------------------------------------------------
	 

		$insertquery = "INSERT INTO pac2salestarget VALUES(0, $userid, '$pac2code', $yearmonth, $salestarget)";
		$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
	
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
