<!-- This script goes through the customer table, and creates a customerreps row for each customer/rep code. There'll only be one per customer

<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	error_reporting(0);	

	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db)  or die ("Unable to connect!"); 
	
	
	$customerquery = "SELECT account, repcode FROM customer ORDER BY account";
	$customerresult = mysqli_query($link, $customerquery) or die ("Error in query: $customerquery. ".mysqli_error($link));
	echo $customerquery;
	
	while ($customerrow = mysqli_fetch_row($customerresult)) 
	{
		$account 	= $customerrow[0];
		$repcode 	= $customerrow[1];
		
		echo "customer ".$account." ";
		
		// ------------------------------------------------------------------------------------------------------------------------------
		// CREATE customerreps ROW FOR EACH REP CODE THE customer HAS
		// ------------------------------------------------------------------------------------------------------------------------------
	 
		if (!$repcode == "")
		{
			$insertquery = "INSERT INTO customerreps VALUES('".$account."', '".$repcode."')";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		}
	}
?>
