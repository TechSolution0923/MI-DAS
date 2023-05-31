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

	// disable autocommit
	mysqli_autocommit($link, FALSE);

	$arrName1 = array("Alpha","Arnold","Apple","Beta","Bridge","Banana","Charlie","Chelsea","Carrot","Delta","Damson","Dagenham","Echo","Essex","Elderberry","Foxtrot","Franklin","Fig","Grape","Golf","Gerald","Hotel","Hainault","Hawthorn","India","Index","Imbe","Juliet","Justin","Jerusalem","Kilo","Kelly","Kiwi","Lima","Liam","Lime","Mike","Moven","Mango","November","Nightly","Nectarine","Oscar","Open","Orange","Papa","Peter","Peach","Quebec","Queensland","Quince","Romeo","Ranger","Radish","Sierra","Stanley","Strawberry","Tango","Tiger","Tomato","Uniform","Unique","Ugli","Victor","Value","Vine","Whiskey","World","Watermelon","Zulu","Zebra","Zucchini");
	
	$arrName2 = array("Agricultural Contractors","Builders Merchants","Ceramic Tiles","Drainage Contractors","Electrical","Forestry","Golf Course","Hotels","Insulation Partners","Joiners","Key Cutting Services","Land Reclaimation","Musical Instruments","Northern Contractors","Open Services","Plumbing","Quay Services","Roofing","Sealants","Thermal Installations","Underfloor Heating","Voltage","Windows","Xray Services","Yard Clearance","Zoning","Plumbing & Heating","Electrical Contractors","Tools","Autospares","Electrical Wholesalers","Industrial Equipment","Decorating","Building Supplies","Tile Merchants","Electronics","Automotive Ltd","Aggregates","Bathrooms","Timber Merchants Ltd","Associates","Industries","Bridging Contractors","Farm Machinery","Construction Services","Design and Construction","Commercial Builders","Developments","Engineering and Construction","Roofing and Construction","Construction Group","Construction Company","Architecture and Renovation Group","Paving and Construction","Interior Construction","Construction Management","Concrete Construction","Electrical Construction","Builders","Quality Construction","Mechanical","Home Builders","Concrete Services","Marine Construction","Custom Construction","Home Building");
	
	$arrName3 = array("Ltd.","PLC","LLP","");
	
	
	$arrAddress1 = array("Abberley Mews","Baber Drive","Cabinet Way","Dace Road","Eagle Court","Factory Road","Gables Close","Hackney Road","Iceland Road","Jackets Lane","Kara Way","Laburnum Close","Mabley Street","Naish Court","Oak Avenue","Packington Square","Quad Road","Rabbit Roe","Sable Street","Tabley Road","Udall Street","Vale Grove","Waddington Way","Yardley Lane","Zennor Road"); // Address 1
	
	$arrAddress2 = array("Abbotsbury","Bagshot","Camberley","Danby","Ealing","Fakenham","Gateshead","Hadrians Wall","Ifield","Jersey","Keighley","Lacock","Macclesfield","Newark","Oakamoor","Paddington","Ramsey","Salford","Tamar Valley","Uckfield","Vauxhall","Wadebridge","Yarmouth"); // Address 2
	
	$arrAddress3 = array("Avon","Bedfordshire","City of Brighton and Hove","Cambridgeshire","Derbyshire","East Suffolk","Gloucester","Hampshire","Isle of Wight","Kent","Lancashire","Merseyside","Norfolk","Oxfordshire","Rutland","Shropshire","Tyne and Wear","Warwickshire","Yorkshire"); // Counties
	
	$arrPostCodes = array("AB","BA","CA","DA","EC","FK","GL","HA","IG","KA","LA","ME","NE","OL","PA","RG","SA","TA","UB","WA","YO","ZE"); // Post Codes

	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD SUPPLIERS - INSERT OR UPDATE
	// ------------------------------------------------------------------------------------------------------------------------------

	foreach (glob("MI-DAS_suppliers*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			while (($data = fgetcsv($handle, 750, ";")) !== FALSE)	
			{
				$account    = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[0]);
				$name       = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[1]);	// Get rid of any funky characters
				$address1   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[2]);
				$address2   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[3]);
				$address3   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[4]);
				$address4   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[5]);
				$address5   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[6]);
				$postcode   = $data[7];
				$phone      = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[8]);
				$fax        = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[9]);
				$email1     = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[10]);
				$leadtime      = $data[11];
				$minordervalue = $data[12];
				
				if (!$account == "")
				{
					if($anonymous == "TRUE")	// $anonymous set in extractandload.php
					{
						
						$randIndex1 =	array_rand($arrName1);
						$randIndex2 =	array_rand($arrName2);
						$randIndex3 =	array_rand($arrName3);
						$name		=	$arrName1[$randIndex1]." ".$arrName2[$randIndex2]." ".$arrName3[$randIndex3]; // Get random company name
		
						$randIndex 	= array_rand($arrAddress1);
						$houseno 	= rand(1,100);
						
						$address1 	= $houseno." ".$arrAddress1[$randIndex]; // Random house number and street name
						
						$randIndex 	= array_rand($arrAddress2);
						$address2	= $arrAddress2[$randIndex]; // Random address 2
						
						$randIndex 	= array_rand($arrAddress3);
						$address3	= $arrAddress3[$randIndex]; // Random address 3

						$address4	= "";
						$address5	= "";

						$randIndex 	= array_rand($arrPostCodes);
						$postcode	= $arrPostCodes[$randIndex].rand(1,20)." ".rand(0,9).chr(rand(65,90)).chr(rand(65,90)); // Random post code
						$phone		=	"0".rand(100,999)." ".rand(100000,999999);
						$email1		=	"info@".$arrName1[$randIndex1].".com";
						
					}
					
					$query = "INSERT INTO supplier(account, name, address1, address2, address3, address4, address5, postcode, phone, fax, email1, leadtime, minordervalue) VALUES('$account','$name','$address1','$address2','$address3','$address4','$address5','$postcode','$phone','$fax','$email1',$leadtime, $minordervalue) ON DUPLICATE KEY UPDATE name = '$name', address1 = '$address1', address2 = '$address2', address3 = '$address3', address4 = '$address4', address5 = '$address5', postcode = '$postcode', phone = '$phone', fax = '$fax', email1 = '$email1', leadtime = $leadtime, minordervalue = $minordervalue ";
				  
					$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
				}
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
	
	mysqli_commit($link);	
?>