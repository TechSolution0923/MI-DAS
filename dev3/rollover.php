<!-- This routine goes through the sales summary tables and rolls the days, months and years over if required. This should run just after midnight as it checks the current day and month -->

<?php
    require_once 'dblogin.php';	
	
	date_default_timezone_set('Europe/London');

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');

	// Start time
	$start_datetime = date('Y-m-d H:i:s');

	error_reporting(E_ALL);	
	
	// open connection 
    $link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));

	// Delete sales order rows older than 3 years
	$deletesalesordersquery = "DELETE FROM salesorders WHERE DATEDIFF(CURDATE(),datein) > 1098";
	$deletesalesordersresult = mysqli_query($link, $deletesalesordersquery) or logerror($deletesalesordersquery,mysqli_error($link));

	// Delete sales invoice rows older than 3 years
	$deletesalesinvoicesquery = "DELETE FROM salesinvoices WHERE DATEDIFF(CURDATE(),docdate) > 1098";
	$deletesalesinvoicesresult = mysqli_query($link, $deletesalesinvoicesquery) or logerror($deletesalesinvoicesquery,mysqli_error($link));

	// Delete sales analysis rows older than 3 years
	$deletesalesanalysisquery = "DELETE FROM salesanalysis WHERE DATEDIFF(CURDATE(),date) > 1464";
	$deletesalesanalysisresult = mysqli_query($link, $deletesalesanalysisquery) or logerror($deletesalesanalysisresult,mysqli_error($link));

	$logfile = "logfile.txt";

	// Get the current day and month

	$current_day 	= date("d");
	$current_month 	= date("m");
	
	$month_query = "SET mquantity35 = mquantity34, msales35 = msales34, mtarget35 = mtarget34, mcost35 = mcost34, mmargin35 = mmargin34, mmarginpc35 = mmarginpc34, 
						mquantity34 = mquantity33, msales34 = msales33, mtarget34 = mtarget33, mcost34 = mcost33, mmargin34 = mmargin33, mmarginpc34 = mmarginpc33, 
						mquantity33 = mquantity32, msales33 = msales32, mtarget33 = mtarget32, mcost33 = mcost32, mmargin33 = mmargin32, mmarginpc33 = mmarginpc32, 
						mquantity32 = mquantity31, msales32 = msales31, mtarget32 = mtarget31, mcost32 = mcost31, mmargin32 = mmargin31, mmarginpc32 = mmarginpc31, 
						mquantity31 = mquantity30, msales31 = msales30, mtarget31 = mtarget30, mcost31 = mcost30, mmargin31 = mmargin30, mmarginpc31 = mmarginpc30,
						mquantity30 = mquantity29, msales30 = msales29, mtarget30 = mtarget29, mcost30 = mcost29, mmargin30 = mmargin29, mmarginpc30 = mmarginpc29,
						mquantity29 = mquantity28, msales29 = msales28, mtarget29 = mtarget28, mcost29 = mcost28, mmargin29 = mmargin28, mmarginpc29 = mmarginpc28,
						mquantity28 = mquantity27, msales28 = msales27, mtarget28 = mtarget27, mcost28 = mcost27, mmargin28 = mmargin27, mmarginpc28 = mmarginpc27,
						mquantity27 = mquantity26, msales27 = msales26, mtarget27 = mtarget26, mcost27 = mcost26, mmargin27 = mmargin26, mmarginpc27 = mmarginpc26,
						mquantity26 = mquantity25, msales26 = msales25, mtarget26 = mtarget25, mcost26 = mcost25, mmargin26 = mmargin25, mmarginpc26 = mmarginpc25,
						mquantity25 = mquantity24, msales25 = msales24, mtarget25 = mtarget24, mcost25 = mcost24, mmargin25 = mmargin24, mmarginpc25 = mmarginpc24,
						mquantity24 = mquantity23, msales24 = msales23, mtarget24 = mtarget23, mcost24 = mcost23, mmargin24 = mmargin23, mmarginpc24 = mmarginpc23,
						mquantity23 = mquantity22, msales23 = msales22, mtarget23 = mtarget22, mcost23 = mcost22, mmargin23 = mmargin22, mmarginpc23 = mmarginpc22,
						mquantity22 = mquantity21, msales22 = msales21, mtarget22 = mtarget21, mcost22 = mcost21, mmargin22 = mmargin21, mmarginpc22 = mmarginpc21,
						mquantity21 = mquantity20, msales21 = msales20, mtarget21 = mtarget20, mcost21 = mcost20, mmargin21 = mmargin20, mmarginpc21 = mmarginpc20,
						mquantity20 = mquantity19, msales20 = msales19, mtarget20 = mtarget19, mcost20 = mcost19, mmargin20 = mmargin19, mmarginpc20 = mmarginpc19,
						mquantity19 = mquantity18, msales19 = msales18, mtarget19 = mtarget18, mcost19 = mcost18, mmargin19 = mmargin18, mmarginpc19 = mmarginpc18,
						mquantity18 = mquantity17, msales18 = msales17, mtarget18 = mtarget17, mcost18 = mcost17, mmargin18 = mmargin17, mmarginpc18 = mmarginpc17,
						mquantity17 = mquantity16, msales17 = msales16, mtarget17 = mtarget16, mcost17 = mcost16, mmargin17 = mmargin16, mmarginpc17 = mmarginpc16,
						mquantity16 = mquantity15, msales16 = msales15, mtarget16 = mtarget15, mcost16 = mcost15, mmargin16 = mmargin15, mmarginpc16 = mmarginpc15,
						mquantity15 = mquantity14, msales15 = msales14, mtarget15 = mtarget14, mcost15 = mcost14, mmargin15 = mmargin14, mmarginpc15 = mmarginpc14,
						mquantity14 = mquantity13, msales14 = msales13, mtarget14 = mtarget13, mcost14 = mcost13, mmargin14 = mmargin13, mmarginpc14 = mmarginpc13,
						mquantity13 = mquantity12, msales13 = msales12, mtarget13 = mtarget12, mcost13 = mcost12, mmargin13 = mmargin12, mmarginpc13 = mmarginpc12,
						mquantity12 = mquantity11, msales12 = msales11, mtarget12 = mtarget11, mcost12 = mcost11, mmargin12 = mmargin11, mmarginpc12 = mmarginpc11,
						mquantity11 = mquantity10, msales11 = msales10, mtarget11 = mtarget10, mcost11 = mcost10, mmargin11 = mmargin10, mmarginpc11 = mmarginpc10,
						mquantity10 = mquantity9,  msales10 = msales9,  mtarget10 = mtarget9,  mcost10 = mcost9,  mmargin10 = mmargin9,  mmarginpc10 = mmarginpc9,
						mquantity9  = mquantity8,  msales9  = msales8,  mtarget9  = mtarget8,  mcost9  = mcost8,  mmargin9  = mmargin8,  mmarginpc9 = mmarginpc8,
						mquantity8  = mquantity7,  msales8  = msales7,  mtarget8  = mtarget7,  mcost8  = mcost7,  mmargin8  = mmargin7,  mmarginpc8 = mmarginpc7,
						mquantity7  = mquantity6,  msales7  = msales6,  mtarget7  = mtarget6,  mcost7  = mcost6,  mmargin7  = mmargin6,  mmarginpc7 = mmarginpc6,
						mquantity6  = mquantity5,  msales6  = msales5,  mtarget6  = mtarget5,  mcost6  = mcost5,  mmargin6  = mmargin5,  mmarginpc6 = mmarginpc5,
						mquantity5  = mquantity4,  msales5  = msales4,  mtarget5  = mtarget4,  mcost5  = mcost4,  mmargin5  = mmargin4,  mmarginpc5 = mmarginpc4,
						mquantity4  = mquantity3,  msales4  = msales3,  mtarget4  = mtarget3,  mcost4  = mcost3,  mmargin4  = mmargin3,  mmarginpc4 = mmarginpc3,
						mquantity3  = mquantity2,  msales3  = msales2,  mtarget3  = mtarget2,  mcost3  = mcost2,  mmargin3  = mmargin2,  mmarginpc3 = mmarginpc2,
						mquantity2  = mquantity1,  msales2  = msales1,  mtarget2  = mtarget1,  mcost2  = mcost1,  mmargin2  = mmargin1,  mmarginpc2 = mmarginpc1,
						mquantity1  = mquantity0,  msales1  = msales0,  mtarget1  = mtarget0,  mcost1  = mcost0,  mmargin1  = mmargin0,  mmarginpc1 = mmarginpc0,
						mquantity0  = 0,           msales0  = 0,        mtarget0  = 0,         mcost0  = 0,       mmargin0  = 0,         mmarginpc0 = 0";
	
	$year_query = ",yquantity3 = yquantity2, ysales3 = ysales2, ytarget3 = ytarget2, ycost3 = ycost2, ymargin3 = ymargin2, ymarginpc3 = ymarginpc2,
					yquantity2 = yquantity1, ysales2 = ysales1, ytarget2 = ytarget1, ycost2 = ycost1, ymargin2 = ymargin1, ymarginpc2 = ymarginpc1,
					yquantity1 = yquantity0, ysales1 = ysales0, ytarget1 = ytarget0, ycost1 = ycost0, ymargin1 = ymargin0, ymarginpc1 = ymarginpc0,
					yquantity0 = 0, ysales0 = 0, ytarget0 = 0, ycost0 = 0, ymargin0 = 0, ymarginpc0 = 0";
					
	// If its the first day of the month move the months along in the sales summary tables
	
	if ($current_day == 1){
		
		// Move the months along in the following tables
		
		// customerpac1sales
		
		$query = "UPDATE customerpac1sales ". $month_query;
		if ($current_month == 1){
			$query = $query . $year_query;
		}
		
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

		// customerpac2sales
		
		$query = "UPDATE customerpac2sales ". $month_query;
		if ($current_month == 1){
			$query = $query . $year_query;
		}
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

		// customerpac3sales
		
		$query = "UPDATE customerpac3sales ". $month_query;
		if ($current_month == 1){
			$query = $query . $year_query;
		}
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

		// customerpac4sales
		
		$query = "UPDATE customerpac4sales ". $month_query;
		if ($current_month == 01){
			$query = $query . $year_query;
		}
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

		// customerprodsales
		
		$query = "UPDATE customerprodsales ". $month_query;
		if ($current_month == 1){
			$query = $query . $year_query;
		}
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

		// customersales
		
		$query = "UPDATE customersales ". $month_query;
		if ($current_month == 1){
			$query = $query . $year_query;
		}
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

		// productsales
		
		$query = "UPDATE productsales ". $month_query;
		if ($current_month == 1){
			$query = $query . $year_query;
		}
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

		// pac1sales
		
		$query = "UPDATE pac1sales ". $month_query;
		if ($current_month == 1){
			$query = $query . $year_query;
		}
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

		// pac2sales
		
		$query = "UPDATE pac2sales ". $month_query;
		if ($current_month == 1){
			$query = $query . $year_query;
		}
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

		// pac3sales
		
		$query = "UPDATE pac3sales ". $month_query;
		if ($current_month == 1){
			$query = $query . $year_query;
		}
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

		// pac4sales
		
		$query = "UPDATE pac4sales ". $month_query;
		if ($current_month == 1){
			$query = $query . $year_query;
		}
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

		// repsales
		
		$query = "UPDATE repsales ". $month_query;
		if ($current_month == 1){
			$query = $query . $year_query;
		}
		$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
		
		// Increment the current yearmonth
	
		$systemquery = "SELECT curyearmonth FROM system";
		$systemresult = mysqli_query($link, $systemquery) or logerror($systemquery,mysqli_error($link));
		$systemrow = mysqli_fetch_row($systemresult);
		$curyearmonth = $systemrow[0];
		
		// Extract the year and month
		
		$curyear  = substr($curyearmonth,0,4);
		$curmonth = substr($curyearmonth,4,2);
		
		// Increment
		
		if ($curmonth==12)
		{
			$newmonth = 1;
			$newyear = $curyear + 1;
		}
		else
		{
			$newmonth = $curmonth + 1;
			$newyear = $curyear;
		}
		
		$newyearmonth = ($newyear * 100) + $newmonth;

		$systemquery = "UPDATE system SET curyearmonth = $newyearmonth";
		$systemresult = mysqli_query($link, $systemquery) or logerror($systemquery,mysqli_error($link));
		
		// If this is the start of the month, copy the error log into a sub folder
		
		// Create the errorlogs folder if it doesnt already exist
		
		$errorlogfolder = "errorlogs";
		
		if (!file_exists($errorlogfolder))
		{
			mkdir($errorlogfolder, 0777);
		}
		
		$errorlog = "error_log";
		$newerrorlog = $errorlogfolder."/".$errorlog.date('Y-m-d');
		rename ($errorlog, $newerrorlog); 
		
		// Create the logfile folder if it doesnt already exist
		
		$logfilefolder = "logfiles";
		
		if (!file_exists($logfilefolder))
		{
			mkdir($logfilefolder, 0777);
		}
		
		$newlogfile = $logfilefolder."/".$logfile.date('Y-m-d');
		rename ($logfile, $newlogfile); 		
		
	} // 	if ($current_day == 1){

	// End time and duration and write to the logfile table
	$end_datetime = date('Y-m-d H:i:s');
	$duration = strtotime($end_datetime) - strtotime($start_datetime);
	$minutes = floor($duration / 60);
	$seconds = $duration % 60;

	// Set the batch number for the log file. Like 202202211400	
	$batch = date('YmdHi'); 
	
	$filename = basename(__FILE__);

	$query = "INSERT INTO logfile(id, batch, application, started, ended, duration) VALUES (0, '$batch', '$filename', '$start_datetime', '$end_datetime', $duration)";
	$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

	$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
	$stringData = $start_datetime." ".$filename." ".$minutes." min(s) ".$seconds." sec(s)\n";
	fwrite($fh, $stringData);
	fclose($fh);

	function logerror($query,$error)
	{
		$email = "kieran" . "@" . "kk-cs" . ".co.uk";
		
		$errormsg = "Hi Kieran, an error just occurred in ".__FILE__.". The query was '".$query."' and the error was '".$error."'";
		
		error_log($errormsg);
		error_log($errormsg,1,$email);
		
		return true;
	}
	
?>
