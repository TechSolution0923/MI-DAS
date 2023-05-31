<!-- This is the FTP version of the load script which recursively goes through all matching files 

<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');
	
	// Start time
	$start_datetime = date('Y-m-d H:i:s');
	error_reporting(E_ALL);	
	
	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));
	
	// $file = "products.csv";

    // ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD PRODUCTS - INSERT OR UPDATE
	// ------------------------------------------------------------------------------------------------------------------------------

	foreach (glob("MI-DAS_products*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			$affectedrows = 0;
			while (($data = fgetcsv($handle, 750, ";")) !== FALSE)	
			{
				$code        = $data[0];
				$description = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[1]);
				$pac1        = $data[2];
				$pac2        = $data[3];
				$pac3        = $data[4];
				$pac4        = $data[5];
				$suppliercode1 = $data[6];
				$suppliercode2 = $data[7];
				$replacementcost   = $data[8];
				$currencycode      = $data[9];
				$costeffectivefrom = $data[10];
				$buyingunit        = $data[11];
				$packsize          = $data[12];
				$minimumorderqty   = $data[13];
				$costunit          = $data[14];
				$basecostprice     = $data[15];
				$netcostprice1     = $data[16];
				$netcostprice2     = $data[17];
				$netcostprice3     = $data[18];
				$netcostprice4     = $data[19];
				$rebatediscount1   = $data[20];
				$rebatediscount2   = $data[21];
				$rebatediscount3   = $data[22];
				$rebatediscount4   = $data[23];
				$stockunit         = $data[24];
				$saleunitfactor1   = $data[25];
				$saleunitcode1     = $data[26];
				$conversionfactor1 = $data[27];
				$saleunitfactor2   = $data[28];
				$saleunitcode2     = $data[29];
				$conversionfactor2 = $data[30];
				$saleunitfactor3   = $data[31];
				$saleunitcode3     = $data[32];
				$conversionfactor3 = $data[33];
				$saleunitfactor4   = $data[34];
				$saleunitcode4     = $data[35];
				$conversionfactor4 = $data[36];
				$saleunitfactor5   = $data[37];
				$saleunitcode5     = $data[38];
				$conversionfactor5 = $data[39];
				$saleunitfactor6   = $data[40];
				$saleunitcode6     = $data[41];
				$conversionfactor6 = $data[42];
			
				if (!$code == "")
				{
					$query = "INSERT INTO product(code, description, pac1code, pac2code, pac3code, pac4code, unitofstock, suppliercode1, suppliercode2, replacementcost, currencycode, costeffectivefrom, buyingunit, packsize, minimumorderqty, costunit, basecostprice, netcostprice1, netcostprice2, netcostprice3, netcostprice4, rebatediscount1, rebatediscount2, rebatediscount3, rebatediscount4, saleunitfactor1, saleunitcode1, conversionfactor1, saleunitfactor2, saleunitcode2, conversionfactor2, saleunitfactor3, saleunitcode3, conversionfactor3, saleunitfactor4, saleunitcode4, conversionfactor4, saleunitfactor5, saleunitcode5, conversionfactor5, saleunitfactor6, saleunitcode6, conversionfactor6) VALUES('$code','$description','$pac1','$pac2','$pac3','$pac4', '$stockunit', '$suppliercode1', '$suppliercode2', $replacementcost, '$currencycode', '$costeffectivefrom', '$buyingunit', $packsize, $minimumorderqty, '$costunit', $basecostprice, $netcostprice1, $netcostprice2, $netcostprice3, $netcostprice4, $rebatediscount1, $rebatediscount2, $rebatediscount3, $rebatediscount4, $saleunitfactor1, '$saleunitcode1', $conversionfactor1, $saleunitfactor2, '$saleunitcode2', $conversionfactor2, $saleunitfactor3, '$saleunitcode3', $conversionfactor3, $saleunitfactor4, '$saleunitcode4', $conversionfactor4, $saleunitfactor5, '$saleunitcode5', $conversionfactor5, $saleunitfactor6, '$saleunitcode6', $conversionfactor6) ON DUPLICATE KEY UPDATE description = '$description', pac1code = '$pac1', pac2code = '$pac2', pac3code = '$pac3', pac4code = '$pac4', unitofstock = '$stockunit', suppliercode1 = '$suppliercode1', suppliercode2 = '$suppliercode2', replacementcost = $replacementcost, currencycode = '$currencycode', costeffectivefrom = '$costeffectivefrom', buyingunit = '$buyingunit', packsize = $packsize, minimumorderqty = $minimumorderqty, costunit = '$costunit', basecostprice = $basecostprice, netcostprice1 = $netcostprice1, netcostprice2 = $netcostprice2, netcostprice3 = $netcostprice3, netcostprice4 = $netcostprice4, rebatediscount1 = $rebatediscount1, rebatediscount2 = $rebatediscount2, rebatediscount3 = $rebatediscount3, rebatediscount4 = $rebatediscount4, saleunitfactor1 = $saleunitfactor1, saleunitcode1 = '$saleunitcode1', conversionfactor1 = $conversionfactor1, saleunitfactor2 = $saleunitfactor2, saleunitcode2 = '$saleunitcode2', conversionfactor2 = $conversionfactor2, saleunitfactor3 = $saleunitfactor3, saleunitcode3 = '$saleunitcode3', conversionfactor3 = $conversionfactor3, saleunitfactor4 = $saleunitfactor4, saleunitcode4 = '$saleunitcode4', conversionfactor4 = $conversionfactor4, saleunitfactor5 = $saleunitfactor5, saleunitcode5 = '$saleunitcode5', conversionfactor5 = $conversionfactor5, saleunitfactor6 = $saleunitfactor6, saleunitcode6 = '$saleunitcode6', conversionfactor6 = $conversionfactor6";
					$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
					
					$affectedrows++; // Not using the mysqli_affected_rows function here because ON DUPLICATE KEY returns a value of 2 if updated
				}
			}
			fclose($handle);

			// Create the processed folder if it doesnt already exist
			
			$processedfolder = "processed";
			
			if (!file_exists($processedfolder))
			{
				mkdir($processedfolder, 0777);
			}

			$newfilename = $processedfolder."/".$file.date('m-d-Y_Hia');

			rename ($file, $newfilename); 

			// End time and duration and write to the logfile table
			$end_datetime = date('Y-m-d H:i:s');
			$duration = strtotime($end_datetime) - strtotime($start_datetime);
			$minutes = floor($duration / 60);
			$seconds = $duration % 60;
			
			$filename = basename(__FILE__);

			$query = "INSERT INTO logfile(id, application, started, ended, duration) VALUES (0, '$filename', '$start_datetime', '$end_datetime', $duration)";
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));

			$logfile = "logfile.txt";
			$fh = fopen($logfile, 'a') or fopen($logfile, 'w');
			$stringData = $start_datetime." ".$filename." ".$minutes." min(s) ".$seconds." sec(s)\n";
			fwrite($fh, $stringData);
			fclose($fh);
		}
	} // foreach

	mysqli_commit($link);
	
?>
