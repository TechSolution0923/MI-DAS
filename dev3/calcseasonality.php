<!-- 	
This script calculates seasonality profiles from the demand table, depending on the system parameter setting
Only needs to be run once, at the start of each new month

PARAMETERS
==========
auth		Is this an authorised run of this script Y/N ** THIS IS REQUIRED **
level		PAC level (optional)
code		PAC code (optional)
debug		Display debug messages Y/N (optional)

To run this with full options, use a URL like this: calcseasonality.php?&code=P0307&debug=Y 
-->

<?php
    require_once 'dblogin.php';	
	
	date_default_timezone_set('Europe/London');

	// Start time
	$start_datetime = date('Y-m-d H:i:s');

	// ----------------------------------------------------------------------------------------------------------------
	// GET PARAMETERS
	// ----------------------------------------------------------------------------------------------------------------

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
	
	$whereclause = "";
	
	if (isset($_GET['code'])) 
	{
		$code = $_GET['code'];
		$whereclause = " WHERE code = '$code'";
	}
	
	$p_debug = "N";
	
	if (isset($_GET['debug'])) 
	{
		$p_debug = $_GET['debug'];
	}

	// Start time
	$start_datetime = date('Y-m-d H:i:s');

	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or die ("Unable to connect!"); 

	// ----------------------------------------------------------------------------------------------------------------
	// GET IM SETTINGS
	// ----------------------------------------------------------------------------------------------------------------

	$imsystemquery = "SELECT seasonalityprofilelevel FROM imsystem";
	$imsystemresult = mysqli_query($link, $imsystemquery) or logerror(__LINE__." ".$imsystemquery,mysqli_error($link));

	$imsystemrow = mysqli_fetch_row($imsystemresult);
	$seasonalityprofilelevel = $imsystemrow[0];
	
	switch ($seasonalityprofilelevel)
	{
		case 1:
			$tab1name = "pac1";
			$tab2name = "pac1seasonality";
			$colname = "pac1code";
			break;
		case 2:
			$tab1name = "pac2";
			$tab2name = "pac2seasonality";
			$colname = "pac2code";
			break;
		case 3:
			$tab1name = "pac3";
			$tab2name = "pac3seasonality";
			$colname = "pac3code";
			break;
		case 4:
			$tab1name = "pac4";
			$tab2name = "pac4seasonality";
			$colname = "pac4code";
			break;
	}	
		
	// ----------------------------------------------------------------------------------------------------------------
	// GET SYSTEM SETTINGS
	// ----------------------------------------------------------------------------------------------------------------
	
	$systemquery = "SELECT curyearmonth FROM system";
	$systemresult = mysqli_query($link, $systemquery) or logerror(__LINE__." ".$systemquery,mysqli_error($link));
	
	$systemrow = mysqli_fetch_row($systemresult);
	$curyearmonth = $systemrow[0];
	
	// Extract the year and month
	
	$curyear  = substr($curyearmonth,0,4);
	$curmonth = substr($curyearmonth,4,2);
	
	// Go back 4 years, which is 5 including the current year
	
	$fromyear = $curyear - 4;

	// ----------------------------------------------------------------------------------------------------------------
	// FOR EACH BRANCH
	// ----------------------------------------------------------------------------------------------------------------

	$branchquery = "SELECT branch FROM branch ORDER BY branch";
	$branchresult = mysqli_query($link, $branchquery) or logerror(__LINE__." ".$branchquery,mysqli_error($link));

	while ($branchrow = mysqli_fetch_row($branchresult)) 
	{	
		$branch = $branchrow[0];

		// ----------------------------------------------------------------------------------------------------------------
		// FOR EACH PAC
		// ----------------------------------------------------------------------------------------------------------------
			
		$pacquery = "SELECT code FROM $tab1name $whereclause ORDER BY code";
		$pacresult = mysqli_query($link, $pacquery) or logerror(__LINE__." ".$pacquery,mysqli_error($link));

		while ($pacrow = mysqli_fetch_row($pacresult)) 
		{	
			$paccode = $pacrow[0];
			
			// Get the demand for the PAC3 and the year month, oldest first.
			
			$demandquery = "SELECT demand.period, demand.year, SUM(demand.demandqty), ((demand.year * 100) + demand.period) as yearmonth FROM demand LEFT JOIN product ON product.code = demand.productcode WHERE product.$colname = '$paccode' AND branch = $branch AND demand.year >= $fromyear AND (year * 100) + period <> $curyearmonth GROUP BY yearmonth ORDER BY yearmonth ASC LIMIT 60";
		
			$demandresult = mysqli_query($link, $demandquery) or logerror(__LINE__." ".$demandquery,mysqli_error($link));	
			
			// In this two dimensional array the months are in the rows and the years are in the columns. 

			// For example
			
			// 				   Year - 4		Year - 3	 Year -2	  Year - 1	   Current Year
			
			// [0] => Array ( [0] => 128.00 [1] => 66.00 [2] => 47.00 [3] => 76.00 [4] => 64.00 ) 		Jan
			// [1] => Array ( [0] => 77.00 [1] => 61.00 [2] => 49.00 [3] => 21.00 [4] => 104.00 ) 		Feb
			// [2] => Array ( [0] => 141.00 [1] => 80.00 [2] => 64.00 [3] => 70.00 [4] => 33.00 ) 		Mar
			// [3] => Array ( [0] => 130.00 [1] => 37.00 [2] => 8.00 [3] => 32.00 [4] => 12.00 ) 		Apr
			// [4] => Array ( [0] => 116.00 [1] => 78.00 [2] => 35.00 [3] => 43.00 [4] => 16.00 ) 		May
			// [5] => Array ( [0] => 96.00 [1] => 59.00 [2] => 40.00 [3] => 42.00 [4] => 68.00 ) 		Jun
			// [6] => Array ( [0] => 108.00 [1] => 69.00 [2] => 41.00 [3] => 27.00 [4] => 0.00 ) 		Jul
			// [7] => Array ( [0] => 104.00 [1] => 77.00 [2] => 21.00 [3] => 15.00 [4] => 0.00 ) 		Aug
			// [8] => Array ( [0] => 100.00 [1] => 67.00 [2] => 40.00 [3] => 45.00 [4] => 0.00 ) 		Sep
			// [9] => Array ( [0] => 84.00 [1] => 99.00 [2] => 28.00 [3] => 69.00 [4] => 0.00 ) 		Oct
			// [10] => Array ( [0] => 61.00 [1] => 75.00 [2] => 49.00 [3] => 115.00 [4] => 0.00 ) 		Nov
			// [11] => Array ( [0] => 80.00 [1] => 39.00 [2] => 46.00 [3] => 107.00 [4] => 0.00 ) )		Dec

			$demandarray = array(
				array(0,0,0,0,0),	// Jan
				array(0,0,0,0,0),	// Feb
				array(0,0,0,0,0),	// Mar
				array(0,0,0,0,0),	// Apr
				array(0,0,0,0,0),	// May
				array(0,0,0,0,0),	// Jun
				array(0,0,0,0,0),	// Jul
				array(0,0,0,0,0),	// Aug
				array(0,0,0,0,0),	// Sep
				array(0,0,0,0,0),	// Oct
				array(0,0,0,0,0),	// Nov
				array(0,0,0,0,0)	// Dec
			);

			$monthlyaverage 	= array();	// This will have 12 rows, an average for each month
			
			$seasonalpercentage = array();	// The percentage the month is of the total average

			$seasonalbaseseries = array();	// The monthly base series
			
			$x = 0;
			$y = 0;
			
			if (!mysqli_num_rows($demandresult)==0) // Only if there is matching demand history
			{			
				while ($demandrow = mysqli_fetch_row($demandresult))
				{
					$period = $demandrow[0];
					$year	= $demandrow[1];
					$demand = $demandrow[2];
					$yearmonth = $demandrow[3];
								
					$demandarray[$y][$x] = $demand;
					
					if($y < 11)
					{
						$y++;
					}
					else
					{
						$y = 0;
						$x++;
					}
				}			
			}	// if (!mysqli_num_rows($demandhistoryresult)==0)

			$totalaverage = 0;
			
			// Average each month over the years. If the month is future (there are demand rows for future months), the average is /4 rather than /5 years. Current month excluded
			
			for($y = 0; $y < 12; $y++)
			{
				if($y < $curmonth)
				{
					$monthlyaverage[$y] = ($demandarray[$y][0] + $demandarray[$y][1] + $demandarray[$y][2] + $demandarray[$y][3] + $demandarray[$y][4]) / 5;
				}
				else
				{
					$monthlyaverage[$y] = ($demandarray[$y][0] + $demandarray[$y][1] + $demandarray[$y][2] + $demandarray[$y][3] ) / 4;
				}
				
				$totalaverage += $monthlyaverage[$y];			
			}

			// Calculate the seasonal percentage and base series
			
			for($y = 0; $y < 12; $y++)
			{
				if($totalaverage <> 0)
				{
					$seasonalpercentage[$y] = ($monthlyaverage[$y] / $totalaverage) * 100;
					$seasonalbaseseries[$y] = ($monthlyaverage[$y] / $totalaverage) * 12;
				}
				else
				{
					$seasonalpercentage[$y] = 100 / 12;
					$seasonalbaseseries[$y] = 1;
				}
			}

			for($y = 0; $y < 12; $y++)
			{
				$seasonalityinsertquery = "INSERT INTO $tab2name($colname, branch, period, percentage, baseseries) VALUES( '$paccode', $branch, $y + 1, $seasonalpercentage[$y], $seasonalbaseseries[$y]) ON DUPLICATE KEY UPDATE percentage =  $seasonalpercentage[$y], baseseries = $seasonalbaseseries[$y]";

				$seasonalityinsertresult = mysqli_query($link, $seasonalityinsertquery) or logerror(__LINE__." ".$seasonalityinsertquery,mysqli_error($link));
			}
			
			if($p_debug == "Y")
			{
				echo "==================<br />\n";
				echo "BRANCH: ".$branch."<br />\n";
				echo "==================<br />\n";
				
				echo "PAC CODE: ".$paccode."<br />\n";
				
				echo "Demand Array:<br />\n";			//debug
				for($y = 0; $y < 12; $y++)				//debug
				{										//debug
					echo $y." - ".$demandarray[$y][0]." ".$demandarray[$y][1]." ".$demandarray[$y][2]." ".$demandarray[$y][3]." ".$demandarray[$y][4]."<br />\n";	//debug
				}										//debug

				echo "Monthly Average:<br />\n";		//debug
				for($y = 0; $y < 12; $y++)				//debug
				{										//debug
					echo $y." - ".$monthlyaverage[$y]."<br />\n";	//debug
				}										//debug

				echo "Seasonal Percentage:<br />\n";	//debug
				for($y = 0; $y < 12; $y++)				//debug
				{										//debug
					echo $y." - ".$seasonalpercentage[$y]."<br />\n";//debug
				}										//debug

				echo "Seasonal Base Series:<br />\n";	//debug
				for($y = 0; $y < 12; $y++)				//debug
				{										//debug
					echo $y." - ".$seasonalbaseseries[$y]."<br />\n";//debug
				}										//debug
			}
			
		}	// while ($pacrow = mysqli_fetch_row($pac1result)) 
	}	// while ($branchrow = mysqli_fetch_row($branchresult)) 
		
	// End time and duration and write to the logfile table
	
	$end_datetime = date('Y-m-d H:i:s');
	$duration = strtotime($end_datetime) - strtotime($start_datetime);
	$minutes = floor($duration / 60);
	$seconds = $duration % 60;
	
	$filename = basename(__FILE__);
	
	// Set the batch number for the log file. Like 202202211400
	
	$batch = date('YmdHi'); 

	$query = "INSERT INTO logfile(id, batch, application, started, ended, duration) VALUES (0, '$batch', '$filename', '$start_datetime', '$end_datetime', $duration)";
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

	$logfile = "logfile.txt";
	$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
	$stringData = $start_datetime." ".$filename." ".$minutes." min(s) ".$seconds." sec(s)\n";
	fwrite($fh, $stringData);
	fclose($fh);
?>
