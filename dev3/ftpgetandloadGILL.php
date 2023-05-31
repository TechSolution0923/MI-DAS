<?php

	date_default_timezone_set('Europe/London');

	ini_set('memory_limit', '1G');
	ini_set('​max_execution_time', 0); //0=NOLIMIT


	// Want the customer name and address hidden for this import
	$anonymous = "FALSE";

	// Set the batch number for the log file. Like 202202211400
	
	$batch = date('YmdHi'); 
	
	$ordersloaded = "N"; // Used to determine if new quotes have been imported and therefore whether or not to run the orders kpis. Set in loadorders.php
	$quotesloaded = "N"; // Used to determine if new quotes have been imported and therefore whether or not to run the quotes kpis. Set in loadquotes.php
	$stockloaded  = "N"; // Used to determine if new stock has been loaded. Set in loadstock_s.php and loadstock_p.php
	$salesanalloaded = "N"; // Used to determine if sales analysis has been loaded and if so, run loadreplastsales
	
	$message_count = 0; // This is set in the extract script and counts the number of messages downloaded. If none are downloaded, there is no need to continue

	include ('ftpgetfilesGILL.php');
	include ('ftploadcustomers.php');	// This must be done before the sales analysis is loaded
	include ('ftploadsuppliers.php');
	include ('ftploadproducts.php');	// This must be done before the sales analysis is loaded
	include ('ftploadcontacts.php');
	include ('ftploadexchrate.php');	// This must be done before the sales analysis is loaded
	include ('ftploadsalesanal.php');
	include ('ftploadcustomerbals.php');
	include ('ftploadagedtrans.php');
	include ('ftploadorders.php');
	include ('ftploadpipelinestages.php');
	include ('ftploadquotes.php');
	include ('ftploadsalesinvoice.php');
	include ('ftploadtermsheader.php');
	include ('ftploadtermsgroup.php');
	include ('ftploadtermsproduct.php');
	include ('ftploadpac1.php');
	include ('ftploadpac2.php');
	include ('ftploadpac3.php');
	include ('ftploadpac4.php');
	include ('ftploadsalesrep.php');
	include ('ftploadproddiscgroup.php');
	//include ('loadlatest.php'); 			// Disabling this because it takes too long to run and the latest values are set when the sales analysis is imported.
	//include ('loadtodayskpis.php'); 		// Now included in loadsalesorderkpis.php
	//include ('loadoutstandingkpis.php'); 	// Now included in loadsalesorderkpis.php
	include ('ftploaddailykpis.php');
	//include ('loadsnapshot.php');			// Now included in loadsalesorderkpis.php

	if($ordersloaded == "Y" or $quotesloaded == "Y") // Only re-do the sales order and quote kpis if new data has arrived.
	{
		include ('loadsalesorderkpis.php');
	}

	include ('ftploadstock.php');
	include ('ftploadstock_s.php');
	include ('ftploadstock_p.php');

	if($stockloaded == "Y") // Only re-do the product stock if new data has arrived.
	{
		include ('loadproductstock.php');
	}

	if($salesanalloaded == "Y") // Only if sales analysis have been loaded
	{
		include ('loadreplastsales.php');
	}
	
	//include ('loadMIDASPIPELINEkpi.php'); // Now included in loadsalesorderkpis.php
	//include ('loadMIDASQUOTESkpi.php'); // Now included in loadsalesorderkpis.php
	//include ('loadreplastsales.php');// This is now being done in loadsalesanal, as it only needs to be done once per day

	function logerror($query,$error)
	{
		$email = "kieran" . "@" . "kk-cs" . ".co.uk";
		
		$errormsg = "Hi Kieran, an error just occurred in ".__FILE__.". The query was '".$query."' and the error was '".$error."'<BR>";
		
		error_log($errormsg);
		error_log($errormsg,1,$email);
		
		return true;
	}	
?>
