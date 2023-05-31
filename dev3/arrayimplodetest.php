<!-- This file populates an array and tests the implode function

<?php
	date_default_timezone_set('Europe/London');

	$repcodes = array();
	
	$repcodes[0] = "01";
	$repcodes[1] = "02";
	$repcodes[2] = "03";
	
	print_r($repcodes);
	
		$reparr = $repcodes;

		if(!empty($reparr)) {
			echo "its here: "."'".implode("','", $reparr)."'";
			
		} else {
			echo "Its empty";
		}

?>
