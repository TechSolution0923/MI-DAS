<!-- This script goes through the user table, and creates a userreps row for each user/rep code

<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	error_reporting(0);	

	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));
	
	
	$userquery = "SELECT userid, repcode, repcode_2, repcode_3, repcode_4, repcode_5, repcode_6, repcode_7, repcode_8, repcode_9, repcode_10 FROM users ORDER BY userid";
	$userresult = mysqli_query($link, $userquery) or die ("Error in query: $userquery. ".mysqli_error($link));
	
	while ($userrow = mysqli_fetch_row($userresult)) 
	{
		$userid 	= $userrow[0];
		$repcode_1 	= $userrow[1];
		$repcode_2 	= $userrow[2];
		$repcode_3 	= $userrow[3];
		$repcode_4 	= $userrow[4];
		$repcode_5 	= $userrow[5];
		$repcode_6 	= $userrow[6];
		$repcode_7 	= $userrow[7];
		$repcode_8 	= $userrow[8];
		$repcode_9 	= $userrow[9];
		$repcode_10 = $userrow[10];
		
		echo "User ".$userid." ";
		
		// ------------------------------------------------------------------------------------------------------------------------------
		// CREATE USERREPS ROW FOR EACH REP CODE THE USER HAS
		// ------------------------------------------------------------------------------------------------------------------------------
	 
		if (!$repcode_1 == "")
		{
			$insertquery = "INSERT INTO userreps VALUES($userid, '".$repcode_1."')";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		}
		
		if (!$repcode_2 == "")
		{
			$insertquery = "INSERT INTO userreps VALUES($userid, '".$repcode_2."')";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		}
				
		if (!$repcode_3 == "")
		{
			$insertquery = "INSERT INTO userreps VALUES($userid, '".$repcode_3."')";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		}
				
		if (!$repcode_4 == "")
		{
			$insertquery = "INSERT INTO userreps VALUES($userid, '".$repcode_4."')";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		}
		
		if (!$repcode_5 == "")
		{
			$insertquery = "INSERT INTO userreps VALUES($userid, '".$repcode_5."')";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		}
		
		if (!$repcode_6 == "")
		{
			$insertquery = "INSERT INTO userreps VALUES($userid, '".$repcode_6."')";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		}
		
		if (!$repcode_7 == "")
		{
			$insertquery = "INSERT INTO userreps VALUES($userid, '".$repcode_7."')";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		}
		
		if (!$repcode_8 == "")
		{
			$insertquery = "INSERT INTO userreps VALUES($userid, '".$repcode_8."')";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		}
		
		if (!$repcode_9 == "")
		{
			$insertquery = "INSERT INTO userreps VALUES($userid, '".$repcode_9."')";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		}
		
		if (!$repcode_10 == "")
		{
			$insertquery = "INSERT INTO userreps VALUES($userid, '".$repcode_10."')";
			$insertresult = mysqli_query($link, $insertquery) or die ("Error in query: $insertquery. ".mysqli_error($link));
		}
	
	}
?>
