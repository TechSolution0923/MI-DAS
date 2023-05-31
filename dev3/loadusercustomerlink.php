<?php

	// This scripts loads the user id and rep codes into an array and then goes through the customer file and matches the customers rep codes against the users rep codes.
	// Where there is a match, the user has access to that customer, and the matching rep code is captured
	
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');
	
	error_reporting(E_ALL);	
	
	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link)); 

	// disable autocommit
	mysqli_autocommit($link, FALSE);

	// Go through the customers now and match customers to users
	
	$customerquery = "SELECT account, repcode, repcode_2, repcode_3, repcode_4, repcode_5, repcode_6, repcode_7, repcode_8, repcode_9, repcode_10 FROM customer ORDER BY account";
	$customerresult = mysqli_query($link, $customerquery) or logerror($customerquery,mysqli_error($link));

	$x = 0;
	
	while ($customerrow = mysqli_fetch_row($customerresult)) 
	{	
		$arrCustomerReps = array();
		
		$account			= $customerrow[0];
		
		// Load the customer rep codes into an array
		
		$arrCustomerReps[0]	= $customerrow[1];
		$arrCustomerReps[1]	= $customerrow[2];
		$arrCustomerReps[2]	= $customerrow[3];
		$arrCustomerReps[3]	= $customerrow[4];
		$arrCustomerReps[4]	= $customerrow[5];
		$arrCustomerReps[5]	= $customerrow[6];
		$arrCustomerReps[6]	= $customerrow[7];
		$arrCustomerReps[7]	= $customerrow[8];
		$arrCustomerReps[8]	= $customerrow[9];
		$arrCustomerReps[9]	= $customerrow[10];

		// Populate the user array
		
		$userquery = "SELECT userid, repcode, repcode_2, repcode_3, repcode_4, repcode_5, repcode_6, repcode_7, repcode_8, repcode_9, repcode_10 FROM users ORDER BY userid";
		$userresult = mysqli_query($link, $userquery) or logerror($userquery,mysqli_error($link));

		$x = 0;
		
		while ($userrow = mysqli_fetch_row($userresult)) 
		{
			$arrUserReps = array();
			
			$userid			= $userrow[0];
			
			// Load the user reps into an array
			
			$arrUserReps[0]	= $userrow[1];
			$arrUserReps[1]	= $userrow[2];
			$arrUserReps[2]	= $userrow[3];
			$arrUserReps[3]	= $userrow[4];
			$arrUserReps[4]	= $userrow[5];
			$arrUserReps[5]	= $userrow[6];
			$arrUserReps[6]	= $userrow[7];
			$arrUserReps[7]	= $userrow[8];
			$arrUserReps[8]	= $userrow[9];
			$arrUserReps[9]	= $userrow[10];
			
			// Match the customer rep codes with the user repcodes
			
			$x = 0;
			
			while($arrCustomerReps[$x] <> "")
			{
				$y = 0;
				while($arrUserReps[$y] <> "")
				{
					echo $x.",".$y." Account ".$account." Customer rep code ".$arrCustomerReps[$x]." User ".$userid." User rep code ".$arrUserReps[$y];
					 
					if($arrCustomerReps[$x] == $arrUserReps[$y] AND $arrCustomerReps[$x] <> "") echo " MATCH!";
					
					echo "<br>";
					$y++;
				}
				$x++;
			}

		}
	}

	function logerror($query,$error)
	{
		$email = "kieran" . "@" . "kk-cs" . ".co.uk";
		
		$errormsg = "Hi Kieran, an error just occurred in ".__FILE__.". The query was '".$query."' and the error was '".$error."'";
		
		error_log($errormsg);
		error_log($errormsg,1,$email);
		
		return true;
	}	

?>