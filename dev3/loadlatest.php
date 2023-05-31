<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');
	
	error_reporting(E_ALL);	
	
	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));

	// disable autocommit
	mysqli_autocommit($link, FALSE);
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD SALES ANALYSIS CURRENT REP CODE AND PAC CODES WITH THOSE FROM CUSTOMER AND PRODUCT FILES
	// ------------------------------------------------------------------------------------------------------------------------------
	
	$updrepcodequery = "UPDATE salesanalysis INNER JOIN customer ON salesanalysis.account = customer.account SET salesanalysis.currepcode = customer.repcode";
	$updrepcoderesult = mysqli_query($link, $updrepcodequery) or logerror($query,mysqli_error($link));
	
	$updpaccodequery = "UPDATE salesanalysis INNER JOIN product ON salesanalysis.prodcode = product.code SET salesanalysis.curpac1code = product.pac1code, salesanalysis.curpac2code = product.pac2code, salesanalysis.curpac3code = product.pac3code, salesanalysis.curpac4code = product.pac4code";
	$updpaccoderesult = mysqli_query($link, $updpaccodequery) or logerror($query,mysqli_error($link));

	mysqli_commit($link);
	
?>