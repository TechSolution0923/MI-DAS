<?php
	date_default_timezone_set('Europe/London');

	ini_set('memory_limit', '1G');

	// Want the customer name and address hidden for this import
	$anonymous = "FALSE";
	
	// Set the batch number for the log file. Like 202202211400
	
	$batch = date('YmdHi'); 
	
	$ordersloaded = "N"; // Used to determine if new quotes have been imported and therefore whether or not to run the orders kpis. Set in loadorders.php
	$quotesloaded = "N"; // Used to determine if new quotes have been imported and therefore whether or not to run the quotes kpis. Set in loadquotes.php
	$stockloaded  = "N"; // Used to determine if new stock has been loaded. Set in loadstock_s.php and loadstock_p.php
	$salesanalloaded = "N";
	
	include ('extractemailattachments.php');
	include ('loadexchrate.php');
	include ('loadpipelinestages.php');
	include ('loadcustomers.php');
	include ('loadproducts.php');
	include ('loadspecials.php');
	include ('loadcontacts.php');
	include ('loadsalesanal.php');
	include ('loadcustomerbals.php');
	include ('loadagedtrans.php');
	include ('loadorders.php');
	include ('loadquotes.php');
	include ('loadpurchaseorders.php');
	include ('loadsalesinvoice.php');
	include ('loadtermsheader.php');
	include ('loadtermsgroup.php');
	include ('loadtermsproduct.php');
	include ('loadpac1.php');
	include ('loadpac2.php');
	include ('loadpac3.php');
	include ('loadpac4.php');
	include ('loadproddiscgroup.php');
	include ('loadsalesrep.php');
	//include ('loadlatest.php'); // Disabling this because it takes too long to run and the latest values are set when the sales analysis is imported.
	//include ('loadtodayskpis.php'); // Now included in loadsalesorderkpis.php
	//include ('loadoutstandingkpis.php'); // Now included in loadsalesorderkpis.php
	include ('loaddailykpis.php');
	//include ('loadsnapshot.php');// Now included in loadsalesorderkpis.php
	
	if($ordersloaded == "Y" or $quotesloaded == "Y") // Only re-do the sales order and quote kpis if new data has arrived.
	{
		include ('loadsalesorderkpis.php');
	}
	include ('loadstock.php');

	if($stockloaded == "Y") // Only re-do the product stock if new data has arrived.
	{
		include ('loadproductstock.php');
	}

	//include ('loadMIDASQUOTESkpi.php'); // Now included in loadsalesorderkpis.php

	if($salesanalloaded == "Y") // Only if sales analysis have been loaded
	{
		include ('loadreplastsales.php');
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
