<?php
	foreach (glob("MI-DAS*.csv") as $filename) 
	{
		echo "$filename size " . filesize($filename) . "\r\n";
	}
?>