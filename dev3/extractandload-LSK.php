<?php

	ini_set('memory_limit', '1G');

	// Want the customer name and address hidden for this import
	$anonymous = "FALSE";
	
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
	include ('loadsalesinvoice.php');
	include ('loadtermsheader.php');
	include ('loadtermsgroup.php');
	include ('loadtermsproduct.php');
	include ('loadpac1.php');
	include ('loadpac2.php');
	include ('loadpac3.php');
	include ('loadpac4.php');
	include ('loadproddiscgroup.php');
	include ('loadlatest.php');
	include ('loadtodayskpis.php');
	include ('loadoutstandingkpis.php');
	include ('loaddailykpis.php');
	include ('loadsnapshot.php');
	include ('loadstock_s.php');
	include ('loadstock_p.php');
	include ('loadproductstock.php');
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
