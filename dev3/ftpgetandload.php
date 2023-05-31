<?php

	ini_set('memory_limit', '1G');

	// Want the customer name and address hidden for this import
	$anonymous = "FALSE";
	
	include ('ftpgetfiles.php');
	include ('ftploadcustomers.php');	// This must be done before the sales analysis is loaded
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
	include ('loadlatest.php'); 			// This isnt actually a data load, its an assignment of the current codes to the transactions
	include ('loadtodayskpis.php'); 		// This isnt a data load either, it calculates the KPIs from order data
	include ('loadoutstandingkpis.php'); 	// This one too calculates the KPI data from the order data
	include ('ftploaddailykpis.php');
	include ('loadsnapshot.php');			// This one too calculates the KPI data from the order data
	include ('ftploadstock_s.php');
	include ('ftploadstock_p.php');
	include ('loadproductstock.php');		// This updates the product stock levels from the branch stock levels
	include ('loadMIDASPIPELINEkpi.php');
	include ('loadMIDASQUOTESkpi.php');
	include ('loadreplastsales.php');

	function logerror($query,$error)
	{
		$email = "kieran" . "@" . "kk-cs" . ".co.uk";
		
		$errormsg = "Hi Kieran, an error just occurred in ".__FILE__.". The query was '".$query."' and the error was '".$error."'<BR>";
		
		error_log($errormsg);
		error_log($errormsg,1,$email);
		
		return true;
	}	
?>
