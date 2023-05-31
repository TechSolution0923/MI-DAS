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

	// disable autocommit
	mysqli_autocommit($link, FALSE);

	//$file = "contacts.csv";

    // Setup random contact details
	
	$arrTitle = array("Mr","Ms"); // Titles
	
	$arrMsFirstNames = array("Ann","Betty","Christine","Dorothy","Elizabeth","Frances","Georgina","Harriet","Irene","Jasmine","Katy","Lisa","Mary","Nora","Olivia","Petra","Queenie","Rose","Sarah","Tina","Ursula","Vera","Wendy","Xyla","Yolanda","Zara"); // Girls first names
	
	$arrMrFirstNames = array("Alan","Brian","Charles","David","Edward","Frank","Gerald","Harry","Ian","James","Keith","Liam","Mike","Norman","Oscar","Peter","Quentin","Ray","Sean","Tom","Urien","Victor","William","Xavier","Zach"); // Boys first names
	
	$arrSurnames = array("Smith","Jones","Williams","Taylor","Davies","Evans","Thomas","Johnson","Roberts","Walker","Robinson","Thompson","White","Hughes","Edwards","Green","Lewis","Wood","Harris","Martin","Jackson","Clarke"); // Surnames
	
	$arrAddress1 = array("Abberley Mews","Baber Drive","Cabinet Way","Dace Road","Eagle Court","Factory Road","Gables Close","Hackney Road","Iceland Road","Jackets Lane","Kara Way","Laburnum Close","Mabley Street","Naish Court","Oak Avenue","Packington Square","Quad Road","Rabbit Roe","Sable Street","Tabley Road","Udall Street","Vale Grove","Waddington Way","Yardley Lane","Zennor Road"); // Address 1
	
	$arrAddress2 = array("Abbotsbury","Bagshot","Camberley","Danby","Ealing","Fakenham","Gateshead","Hadrians Wall","Ifield","Jersey","Keighley","Lacock","Macclesfield","Newark","Oakamoor","Paddington","Ramsey","Salford","Tamar Valley","Uckfield","Vauxhall","Wadebridge","Yarmouth"); // Address 2
	
	$arrAddress3 = array("Avon","Bedfordshire","City of Brighton and Hove","Cambridgeshire","Derbyshire","East Suffolk","Gloucester","Hampshire","Isle of Wight","Kent","Lancashire","Merseyside","Norfolk","Oxfordshire","Rutland","Shropshire","Tyne and Wear","Warwickshire","Yorkshire"); // Counties
	
	$arrPostCodes = array("AB","BA","CA","DA","EC","FK","GL","HA","IG","KA","LA","ME","NE","OL","PA","RG","SA","TA","UB","WA","YO","ZE"); // Post Codes
	

	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD CONTACTS - INSERT OR UPDATE
	// ------------------------------------------------------------------------------------------------------------------------------
	
	foreach (glob("MI-DAS_contacts*.csv") as $file) 
	{
		if (($handle = @fopen($file, "r")) !== FALSE) 
		{
			
			// Delete all existing contact rows because entire contact file is being exported from K8
			
			$query = "TRUNCATE customercontact"; 
			$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
			
			mysqli_commit($link);
			
			$logfile = "logfile.txt";
			$fh = fopen($logfile, 'a') or die("Cant open logfile");
			$stringData = date('Y-m-d_Hia')." Contacts - Truncated\n";
			fwrite($fh, $stringData);
			fclose($fh);	
			
			$affectedrows = 0;
			
			while (($data = fgetcsv($handle, 750, ";")) !== FALSE)	
			{
				$contactno  = $data[0];
				$account	= preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[1]);
				$title		= $data[2];
				$firstname  = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[3]);	// Get rid of any funky characters
				$surname  	= preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[4]);	// Get rid of any funky characters
				$contacttype= $data[5];
				$jobtitle  	= preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[6]);	// Get rid of any funky characters
				$address1   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[7]);
				$address2   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[8]);
				$address3   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[9]);
				$address4   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[10]);
				$address5   = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[11]);
				$postcode   = $data[12];
				$sensitivecontact	= $data[13];
				$donotcommunicate   = $data[14];
				$phone1desc = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[15]);
				$phone2desc = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[16]);
				$phone3desc = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[17]);
				$phone4desc = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[18]);
				$phone1no   = $data[19];
				$phone2no   = $data[20];
				$phone3no   = $data[21];
				$phone4no   = $data[22];
				$email1desc = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[23]);
				$email2desc = preg_replace("/[^a-zA-Z0-9-\/\s]/", "",$data[24]);
				$emailaddress1 = preg_replace("/[^a-zA-Z0-9-_@.\/\s]/", "",$data[25]);
				$emailaddress2 = preg_replace("/[^a-zA-Z0-9-_@.\/\s]/", "",$data[26]);
				
				if (!$contactno == "")
				{
					if($anonymous == "TRUE")	// $anonymous set in extractandload.php
					{
						$randIndex 	=	array_rand($arrTitle);
						$title		=	$arrTitle[$randIndex]; // Get random title
						
						// Depending on title, get random first names
						
						if ($title == 'Ms'){
							$randIndex = array_rand($arrMsFirstNames);
							$firstname = $arrMsFirstNames[$randIndex];
						} else {
							$randIndex = array_rand($arrMrFirstNames);
							$firstname = $arrMrFirstNames[$randIndex];	
						}

						$randIndex 	= array_rand($arrSurnames);
						$surname 	= $arrSurnames[$randIndex]; // Random Surname
						
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

						$phone1no	=	"0".rand(100,999)." ".rand(100000,999999);
						$phone2no	=	"0".rand(100,999)." ".rand(100000,999999);
						$phone3no	=	"07".rand(10,99)." ".rand(100000,999999);
						$phone3no	=	"07".rand(10,99)." ".rand(100000,999999);
						$emailaddress1		=	$firstname."@customer.com";
						$emailaddress2		=	$firstname.".".$surname."@customer.com";
					}
					
					$query = "INSERT INTO customercontact(contactno, account, title, firstname, surname, contacttype, jobtitle, address1, address2, address3, address4, address5, postcode, sensitivecontact, donotcommunicate, phone1desc, phone2desc, phone3desc, phone4desc, phone1no, phone2no, phone3no, phone4no, email1desc, email2desc, emailaddress1, emailaddress2) VALUES($contactno, '$account', '$title', '$firstname', '$surname', '$contacttype', '$jobtitle', '$address1','$address2','$address3','$address4','$address5','$postcode', $sensitivecontact, $donotcommunicate, '$phone1desc', '$phone2desc', '$phone3desc', '$phone4desc', '$phone1no', '$phone2no', '$phone3no', '$phone4no','$email1desc','$email2desc','$emailaddress1','$emailaddress2') ON DUPLICATE KEY UPDATE title = '$title', firstname = '$firstname', surname = '$surname', contacttype = '$contacttype', jobtitle = '$jobtitle', address1 = '$address1', address2 = '$address2', address3 = '$address3', address4 = '$address4', address5 = '$address5', postcode = '$postcode', sensitivecontact = $sensitivecontact, donotcommunicate = $donotcommunicate, phone1desc = '$phone1desc', phone2desc = '$phone2desc', phone3desc = '$phone3desc', phone4desc = '$phone4desc', phone1no = '$phone1no', phone2no = '$phone2no', phone3no = '$phone3no', phone4no = '$phone4no', email1desc = '$email1desc', email2desc = '$email2desc', emailaddress1 = '$emailaddress1', emailaddress2 = '$emailaddress2'";
				  
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