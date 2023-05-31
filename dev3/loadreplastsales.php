<?php
	date_default_timezone_set('Europe/London');

    require_once 'dblogin.php';	
	
	date_default_timezone_set('Europe/London');

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');

	// Start time
	$start_datetime = date('Y-m-d H:i:s');	

	error_reporting(E_ALL);		
		
	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or die ("Unable to connect!"); 
	
	// Enable autocommit
	mysqli_autocommit($link, TRUE);

	$branchquery = "SELECT branch FROM branch";
	$branchresult = mysqli_query($link, $branchquery) or logerror($branchquery,mysqli_error($link));
		
	while ($branchrow = mysqli_fetch_row($branchresult)) 
	{
	$branch   = $branchrow[0];

	$repquery = "SELECT repcode FROM salesrep";
	$represult = mysqli_query($link, $repquery) or logerror($repquery,mysqli_error($link));
		
	while ($reprow = mysqli_fetch_row($represult)) 
		{
		$repcode   = $reprow[0];
		
		$maxdatequery = "SELECT MAX(date) FROM salesanalysis WHERE date <= '".date('Y-m-d')."' AND branch = $branch and repcode = '$repcode'";
		$maxdateresult = mysqli_query($link, $maxdatequery) or logerror($maxdatequery,mysqli_error($link));
		
		while ($maxdaterow = mysqli_fetch_row($maxdateresult)) 
			{	
			$maxdate   = $maxdaterow[0];
		
			$replastsalesquery = "SELECT SUM(sales), SUM(cost) FROM salesanalysis WHERE date = '$maxdate' AND branch = $branch and repcode = '$repcode'";
			$replastsalesresult = mysqli_query($link, $replastsalesquery) or logerror($replastsalequery,mysqli_error($link));
		
			while ($replastsalesrow = mysqli_fetch_row($replastsalesresult)) 
				{	
				$replastsales   = $replastsalesrow[0];
				$replastcost	= $replastsalesrow[1];
				
				if($replastsales != "")
					{
					$insertquery = "INSERT INTO replastsales(branch, repcode, date, sales, cost) VALUES ($branch,'$repcode','$maxdate',$replastsales,$replastcost) ON DUPLICATE KEY UPDATE date = '$maxdate', sales = $replastsales, cost = $replastcost";
					$insertresult = mysqli_query($link, $insertquery) or logerror($insertquery,mysqli_error($link));
					}
				}
			}
		}
	}
	
	// End time and duration and write to the logfile table
	$end_datetime = date('Y-m-d H:i:s');
	$duration = strtotime($end_datetime) - strtotime($start_datetime);
	$minutes = floor($duration / 60);
	$seconds = $duration % 60;
	
	$filename = basename(__FILE__);

	$query = "INSERT INTO logfile(id, batch, application, started, ended, duration) VALUES (0, '$batch', '$filename', '$start_datetime', '$end_datetime', $duration)";
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

	$logfile = "logfile.txt";
	$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
	$stringData = $start_datetime." ".$filename." ".$minutes." min(s) ".$seconds." sec(s)\n";
	fwrite($fh, $stringData);
	fclose($fh);
		
?>