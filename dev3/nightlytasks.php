<?php
	date_default_timezone_set('Europe/London');

	ini_set('memory_limit', '1G');
	
	include ('calcseasonality.php');
	include ('calcstocklevels.php');
	include ('calcABC.php');

	return true;
	
	function logerror($query,$error)
	{
		$email = "kieran" . "@" . "kk-cs" . ".co.uk";
		
		$errormsg = "Hi Kieran, an error just occurred in ".__FILE__.". The query was '".$query."' and the error was '".$error."'<BR>";
		
		error_log($errormsg);
		error_log($errormsg,1,$email);
		
		return true;
	}	

?>
