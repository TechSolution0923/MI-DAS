<?php
	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	$branchnum = $branchno;
		
	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or die ("Unable to connect!"); 

	// disable autocommit
	mysqli_autocommit($link, FALSE);
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD SALES ANALYSIS - INCREMENTAL
	// ------------------------------------------------------------------------------------------------------------------------------

	if (file_exists("salesanal.csv")) 
	{
		$sanalcsv_file = "salesanal.csv";
		$sanalcsvfile = fopen($sanalcsv_file,"r");
//		$thesanaldata = fgets($sanalcsvfile);
		$i = 0;

		while (!feof($sanalcsvfile))
		{
			$sanalcsv_data[] = fgets($sanalcsvfile, 1024);

			$sanalcsv_array = explode(";", $sanalcsv_data[$i]);
			$sanal = array();
			$sanal['branch']    = $sanalcsv_array[0];
			$sanal['account']   = $sanalcsv_array[1];
			$sanal['repcode']   = $sanalcsv_array[2];
			$sanal['pac1']      = $sanalcsv_array[3];
			$sanal['pac2']      = $sanalcsv_array[4];
			$sanal['pac3']      = $sanalcsv_array[5];
			$sanal['pac4']      = $sanalcsv_array[6];
			$sanal['prodcode']  = $sanalcsv_array[7];
			$sanal['quantity']  = $sanalcsv_array[8];
			$sanal['unit']      = $sanalcsv_array[9];
			$sanal['sales']     = $sanalcsv_array[10];
			$sanal['cost']      = $sanalcsv_array[11];
			$sanal['date']      = $sanalcsv_array[12];
			$sanal['orderno']   = $sanalcsv_array[13];
			$sanal['invoiceno'] = $sanalcsv_array[14];
			$sanal['yearmonth'] = $sanalcsv_array[15];

			if (!$sanal['branch'] == "")
			{
			  $query = "INSERT INTO salesanalysis(branch, account, repcode, pac1, pac2, pac3, pac4, prodcode, quantity, unit, sales, cost, date, orderno, invoiceno, yearmonth) VALUES('".$sanal['branch']."','".$sanal['account']."','".$sanal['repcode']."','".$sanal['pac1']."','".$sanal['pac2']."','".$sanal['pac3']."','".$sanal['pac4']."','".$sanal['prodcode']."','".$sanal['quantity']."','".$sanal['unit']."','".$sanal['sales']."','".$sanal['cost']."','".$sanal['date']."','".$sanal['orderno']."','".$sanal['invoiceno']."','".$sanal['yearmonth']."')";
			  
			  $result = mysqli_query($link,$query);
			  $i++;
			}
		}
		fclose($sanalcsvfile);

		$newfilename="processed/".$sanalcsv_file.date('m-d-Y_hia');
		rename ($sanalcsv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);

		echo "Sales Analysis Uploaded\n ";
	
		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = "Sales summary recreated" . "\n";
		fwrite($fh, $stringData);
		fclose($fh); 
		
		echo "Sales summary loaded ";
	}
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD CUSTOMERS - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
 	if (file_exists("customers.csv")) 
	{
		// Delete all existing customer rows
		
		$query = "DELETE FROM customer"; 
		$result = mysqli_query($link, $query);

		echo "Customers deleted \n";
		
		$custcsv_file = "customers.csv";
		$custcsvfile = fopen($custcsv_file,"r");
//		$thecustdata = fgets($custcsvfile);
		$i = 0;

		while (!feof($custcsvfile))
		{
			$custcsv_data[] = fgets($custcsvfile, 1024);

			$custcsv_array = explode(";", $custcsv_data[$i]);
			$custcsv_array = preg_replace("/[^a-zA-Z0-9-\/\s]/", "", $custcsv_array);
			$customer = array();
			$customer['account']    = $custcsv_array[0];
			$customer['name']       = $custcsv_array[1];
			$customer['address1']   = $custcsv_array[2];
			$customer['address2']   = $custcsv_array[3];
			$customer['address3']   = $custcsv_array[4];
			$customer['address4']   = $custcsv_array[5];
			$customer['address5']   = $custcsv_array[6];
			$customer['postcode']   = $custcsv_array[7];
			$customer['phone']      = $custcsv_array[8];
			$customer['fax']        = $custcsv_array[9];
			$customer['email1']     = $custcsv_array[10];
			$customer['repcode']    = $custcsv_array[11];
			$customer['terms1code'] = $custcsv_array[12];
			$customer['terms2code'] = $custcsv_array[13];
			$customer['terms3code'] = $custcsv_array[14];
			$customer['terms4code'] = $custcsv_array[15];
			$customer['terms5code'] = $custcsv_array[16];
			$customer['creditlimit']       = $custcsv_array[17];
			$customer['committeddebt']     = $custcsv_array[18];
			$customer['potentialdebt1']    = $custcsv_array[19];
			$customer['potentialdebt2']    = $custcsv_array[20];
			$customer['potentialdebt3']    = $custcsv_array[21];
			$customer['creditstatus']      = $custcsv_array[22];
			$customer['lastpaymentdate']   = $custcsv_array[23];
			$customer['lastpaymentamount'] = $custcsv_array[24];
			$customer['dellocn']           = $custcsv_array[25];
			$customer['dellocndesc']       = $custcsv_array[26];
			$customer['internaltext']      = $custcsv_array[27];
			$customer['branch']            = $custcsv_array[28];

			if (!$customer['account'] == "")
			{
			  $customerinsertquery = "INSERT INTO customer(account, name, address1, address2, address3, address4, address5, postcode, phone, fax, email1, repcode, terms1code, terms2code, terms3code, terms4code, terms5code, creditlimit, committeddebt, potentialdebt1, potentialdebt2, potentialdebt3, creditstatus, lastpaymentdate, lastpaymentamount, dellocn, dellocndesc, internaltext, branch) VALUES('".$customer['account']."','".$customer['name']."','".$customer['address1']."','".$customer['address2']."','".$customer['address3']."','".$customer['address4']."','".$customer['address5']."','".$customer['postcode']."','".$customer['phone']."','".$customer['fax']."','".$customer['email1']."','".$customer['repcode']."','".$customer['terms1code']."','".$customer['terms2code']."','".$customer['terms3code']."','".$customer['terms4code']."','".$customer['terms5code']."','".$customer['creditlimit']."','".$customer['committeddebt']."','".$customer['potentialdebt1']."','".$customer['potentialdebt2']."','".$customer['potentialdebt3']."','".$customer['creditstatus']."','".$customer['lastpaymentdate']."','".$customer['lastpaymentamount']."','".$customer['dellocn']."','".$customer['dellocndesc']."','".$customer['internaltext']."','".$customer['branch']."')";
			  
			  $result = mysqli_query($link, $customerinsertquery);

			  $i++;
			}
		}
		
		$newfilename="processed/".$custcsv_file.date('m-d-Y_hia');
		rename ($custcsv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);  

		echo "Customers loaded\n ";
	}

    // ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD PRODUCTS - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
 	if (file_exists("products.csv")) 
	{
		// Delete all existing product rows
		
		$query = "TRUNCATE product"; 
		$result = mysqli_query($link, $query);

		echo "Products deleted \n";
		
		$productcsv_file = "products.csv";
		$productcsvfile = fopen($productcsv_file,"r");
//		$theproductdata = fgets($productcsvfile);
		$i = 0;

		while (!feof($productcsvfile))
		{
			$productcsv_data[] = fgets($productcsvfile, 1024);

			$productcsv_array = explode(";", $productcsv_data[$i]);
			$productcsv_array = preg_replace("/[^a-zA-Z0-9-\/\s]/", "", $productcsv_array);
			
			$code        = $productcsv_array[0];
			$description = $productcsv_array[1];
			$pac1        = $productcsv_array[2];
			$pac2        = $productcsv_array[3];
			$pac3        = $productcsv_array[4];
			$pac4        = $productcsv_array[5];


			if (!$code == "")
			{
				$productinsertquery = "INSERT INTO product(code, description, pac1code, pac2code, pac3code, pac4code) VALUES('".$code."','".$description."','".$pac1."','".$pac2."','".$pac3."','".$pac4."')";
			  
				$result = mysqli_query($link, $productinsertquery);

				$i++;
			}
		}
		
		$newfilename="processed/".$productcsv_file.date('m-d-Y_hia');
		rename ($productcsv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);  

		echo "Products loaded\n ";
	}
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD AGED TRANSACTIONS - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
	if (file_exists("agedtrans.csv")) 
	{
		$query = "DELETE FROM agedtrans"; 
		$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error());

		echo "Deleted from agedtrans ";
		
		$agedcsv_file = "agedtrans.csv";
		$agedcsvfile = fopen($agedcsv_file,"r");
//		$theageddata = fgets($agedcsvfile);
		$i = 0;

		while (!feof($agedcsvfile))
		{
			$agedcsv_data[] = fgets($agedcsvfile, 1024);

			$agedcsv_array = explode(";", $agedcsv_data[$i]);
			$agedtrans = array();
			$agedtrans['account']        = $agedcsv_array[0];
			$agedtrans['docdate']        = $agedcsv_array[1];
			$agedtrans['docnumber']      = $agedcsv_array[2];
			$agedtrans['custref']        = $agedcsv_array[3];
			$agedtrans['otherref']       = $agedcsv_array[4];
			$agedtrans['docstatus']      = $agedcsv_array[5];
			$agedtrans['doctype']        = $agedcsv_array[6];
			$agedtrans['duedate']        = $agedcsv_array[7];
			$agedtrans['totalamount']    = $agedcsv_array[8];
			$agedtrans['paidamount']     = $agedcsv_array[9];
			$agedtrans['outstandamount'] = $agedcsv_array[10];
			$agedtrans['collectamount']  = $agedcsv_array[11];
			$agedtrans['overdueamount']  = $agedcsv_array[12];

			if (!$agedtrans['account'] == "")
			{
			  $query = "INSERT INTO agedtrans(account, docdate, docnumber, custref, otherref, docstatus, doctype, duedate, totalamount, paidamount, outstandamount, collectamount, overdueamount) VALUES('".$agedtrans['account']."','".$agedtrans['docdate']."','".$agedtrans['docnumber']."','".$agedtrans['custref']."','".$agedtrans['otherref']."','".$agedtrans['docstatus']."','".$agedtrans['doctype']."','".$agedtrans['duedate']."','".$agedtrans['totalamount']."','".$agedtrans['paidamount']."','".$agedtrans['outstandamount']."','".$agedtrans['collectamount']."','".$agedtrans['overdueamount']."')";
			  
			  $result = mysqli_query($link, $query);
			  
			  $i++;
			}
		}
		fclose($agedcsvfile);

		$newfilename="processed/".$agedcsv_file.date('m-d-Y_hia');
		rename ($agedcsv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);	
		
		echo "Aged Transactions Loaded ";
		
	}
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD SALES ORDERS & QUOTES - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
	if (file_exists("orders.csv")) 
	{
		$query = "DELETE FROM salesorders"; 
		$result = mysqli_query($link, $query) or die ("Error in query: $query. ".mysqli_error());

		echo "Sales orders deleted\n ";
		
		$orderscsv_file = "orders.csv";
		$orderscsvfile = fopen($orderscsv_file,"r");
//		$theordersdata = fgets($orderscsvfile);
		$i = 0;

		while (!feof($orderscsvfile))
		{
			$orderscsv_data[] = fgets($orderscsvfile, 1024);

			$orderscsv_array = explode(";", $orderscsv_data[$i]);
			$orders = array();
			$orders['orderno']       = $orderscsv_array[0];
			$orders['ordtype']       = $orderscsv_array[1];
			$orders['datein']        = $orderscsv_array[2];
			$orders['headerdatereq'] = $orderscsv_array[3];
			$orders['account']       = $orderscsv_array[4];
			$orders['headerstatus']  = $orderscsv_array[5];
			$orders['quotereason']   = $orderscsv_array[6];
			$orders['quotefolldate'] = $orderscsv_array[7];
			$orders['quoteexpidate'] = $orderscsv_array[8];
			$orders['quotevalue']    = $orderscsv_array[9];
			$orders['prodcode']      = $orderscsv_array[10];
			$orders['fulldesc']      = $orderscsv_array[11];
			$orders['quantity']      = $orderscsv_array[12];
			$orders['unitprice']     = $orderscsv_array[13];
			$orders['discount1']     = $orderscsv_array[14];
			$orders['discount2']     = $orderscsv_array[15];
			$orders['sales']         = $orderscsv_array[16];
			$orders['datereq']       = $orderscsv_array[17];
			$orders['cost']          = $orderscsv_array[18];
			$orders['status']        = $orderscsv_array[19];
			$orders['custorderno']   = $orderscsv_array[20];

			if (!$orders['orderno'] == "")
			{
			  $ordersquery = "INSERT INTO salesorders(orderno, ordtype, datein, headerdatereq, account, headerstatus, quotereason, quotefolldate, quoteexpidate, quotevalue, prodcode, fulldesc, quantity, unitprice, discount1, discount2, sales, datereq, cost, status, custorderno) VALUES('".$orders['orderno']."','".$orders['ordtype']."','".$orders['datein']."','".$orders['headerdatereq']."','".$orders['account']."','".$orders['headerstatus']."','".$orders['quotereason']."','".$orders['quotefolldate']."','".$orders['quoteexpidate']."','".$orders['quotevalue']."','".$orders['prodcode']."','".$orders['fulldesc']."','".$orders['quantity']."','".$orders['unitprice']."','".$orders['discount1']."','".$orders['discount2']."','".$orders['sales']."','".$orders['datereq']."','".$orders['cost']."','".$orders['status']."','".$orders['custorderno']."')";
			  
			  $result = mysqli_query($link, $ordersquery);
			  
			  $i++;
			}
		}
		fclose($orderscsvfile);

		$newfilename="processed/".$orderscsv_file.date('m-d-Y_hia');
		rename ($orderscsv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);	
		echo "Sales orders loaded";
	
	}

	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD TODAYS CUSTOMER INVOICES
	// ------------------------------------------------------------------------------------------------------------------------------
 
	if (file_exists("salesinvoice.csv")) 
	{
		
		$invoicescsv_file = "salesinvoice.csv";
		$invoicescsvfile = fopen($invoicescsv_file,"r");
//		$theinvoicessdata = fgets($invoicescsvfile);
		$i = 0;

		while (!feof($invoicescsvfile))
		{
			$invoicescsv_data[] = fgets($invoicescsvfile, 1024);

			$invoicescsv_array = explode(";", $invoicescsv_data[$i]);
			$invoices = array();
			$invoices['docnumber']     = $invoicescsv_array[0];
			$invoices['docdate']       = $invoicescsv_array[1];
			$invoices['orderno']       = $invoicescsv_array[2];
			$invoices['ordtype']       = $invoicescsv_array[3];
			$invoices['datein']        = $invoicescsv_array[4];
			$invoices['headerdatereq'] = $invoicescsv_array[5];
			$invoices['account']       = $invoicescsv_array[6];
			$invoices['headerstatus']  = $invoicescsv_array[7];
			$invoices['prodcode']      = $invoicescsv_array[8];
			$invoices['fulldesc']      = $invoicescsv_array[9];
			$invoices['quantity']      = $invoicescsv_array[10];
			$invoices['unitprice']     = $invoicescsv_array[11];
			$invoices['discount1']     = $invoicescsv_array[12];
			$invoices['discount2']     = $invoicescsv_array[13];
			$invoices['sales']         = $invoicescsv_array[14];
			$invoices['vat']           = $invoicescsv_array[15];
			$invoices['datereq']       = $invoicescsv_array[16];
			$invoices['cost']          = $invoicescsv_array[17];
			$invoices['status']        = $invoicescsv_array[18];
			$invoices['custorderno']   = $invoicescsv_array[19];

			if (!$invoices['orderno'] == "")
			{
			  $invoicesquery = "INSERT INTO salesinvoices(docnumber, docdate, orderno, ordtype, datein, headerdatereq, account, headerstatus, prodcode, fulldesc, quantity, unitprice, discount1, discount2, sales, vat, datereq, cost, status, custorderno) VALUES('".$invoices['docnumber']."','".$invoices['docdate']."','".$invoices['orderno']."','".$invoices['ordtype']."','".$invoices['datein']."','".$invoices['headerdatereq']."','".$invoices['account']."','".$invoices['headerstatus']."','".$invoices['prodcode']."','".$invoices['fulldesc']."','".$invoices['quantity']."','".$invoices['unitprice']."','".$invoices['discount1']."','".$invoices['discount2']."','".$invoices['sales']."','".$invoices['vat']."','".$invoices['datereq']."','".$invoices['cost']."','".$invoices['status']."','".$invoices['custorderno']."')";
			  
			  $result = mysqli_query($link, $invoicesquery);
			  
			  $i++;
			}
		}
		fclose($invoicescsvfile);

		$newfilename="processed/".$invoicescsv_file.date('m-d-Y_hia');
		rename ($invoicescsv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);	
		echo "Sales invoices loaded";
	
	}	

	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD TERMS HEADERS - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
 	if (file_exists("termsheader.csv")) 
	{
		// Delete all existing terms header rows
		
		$query = "TRUNCATE termsheader"; 
		$result = mysqli_query($link, $query);

		echo "Terms headers deleted \n";
		
		$termsheadercsv_file = "termsheader.csv";
		$termsheadercsvfile = fopen($termsheadercsv_file,"r");
//		$thetermsheaderdata = fgets($termsheadercsvfile);
		$i = 0;

		while (!feof($termsheadercsvfile))
		{
			$termsheadercsv_data[] = fgets($termsheadercsvfile, 1024);

			$termsheadercsv_array = explode(";", $termsheadercsv_data[$i]);		
			$unique        = $termsheadercsv_array[0];
			$termcode      = $termsheadercsv_array[1];
			$description   = $termsheadercsv_array[2];
			$effectivefrom = $termsheadercsv_array[3];
			$effectiveto   = $termsheadercsv_array[4];
			$termtype      = $termsheadercsv_array[5];

			if (!$termcode == "")
			{
				$termsheaderinsertquery = "INSERT INTO termsheader(termcode, description, effectivefrom, effectiveto, termtype) VALUES('".$termcode."','".$description."','".$effectivefrom."','".$effectiveto."','".$termtype."')";
			  
				$result = mysqli_query($link, $termsheaderinsertquery);

				$i++;
			}
		}
		
		$newfilename="processed/".$termsheadercsv_file.date('m-d-Y_hia');
		rename ($termsheadercsv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);  

		echo "Terms headers loaded\n ";	
	}

	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD TERMS GROUP - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
 	if (file_exists("termsgroup.csv")) 
	{
		// Delete all existing terms group rows
		
		$query = "TRUNCATE termsgroup"; 
		$result = mysqli_query($link, $query);

		echo "Terms groups deleted \n";
		
		$termsgroupcsv_file = "termsgroup.csv";
		$termsgroupcsvfile = fopen($termsgroupcsv_file,"r");
//		$thetermsgroupdata = fgets($termsgroupcsvfile);
		$i = 0;

		while (!feof($termsgroupcsvfile))
		{
			$termsgroupcsv_data[] = fgets($termsgroupcsvfile, 1024);

			$termsgroupcsv_array = explode(";", $termsgroupcsv_data[$i]);			
			$termcode      = $termsgroupcsv_array[0];
			$discgroupcode = $termsgroupcsv_array[1];
			$termtype      = $termsgroupcsv_array[2];
			$base          = $termsgroupcsv_array[3];
			$discount1     = $termsgroupcsv_array[4];
			$discount2     = $termsgroupcsv_array[5];

			if (!$termcode == "")
			{
				$termsgroupinsertquery = "INSERT INTO termsgroup(termcode, discgroupcode, termtype, base, discount1, discount2) VALUES('".$termcode."','".$discgroupcode."','".$termtype."','".$base."','".$discount1."','".$discount2."')";
			  
				$result = mysqli_query($link, $termsgroupinsertquery);

				$i++;
			}
		}
		
		$newfilename="processed/".$termsgroupcsv_file.date('m-d-Y_hia');
		rename ($termsgroupcsv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);  

		echo "Terms groups loaded\n ";
	}

	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD TERMS product - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
 	if (file_exists("termsproduct.csv")) 
	{
		// Delete all existing terms product rows
		
		$query = "TRUNCATE termsproduct"; 
		$result = mysqli_query($link, $query);

		echo "Terms products deleted \n";
		
		$termsproductcsv_file = "termsproduct.csv";
		$termsproductcsvfile = fopen($termsproductcsv_file,"r");
//		$thetermsproductdata = fgets($termsproductcsvfile);
		$i = 0;

		while (!feof($termsproductcsvfile))
		{
			$termsproductcsv_data[] = fgets($termsproductcsvfile, 1024);

			$termsproductcsv_array = explode(";", $termsproductcsv_data[$i]);			
			$termcode      = $termsproductcsv_array[0];
			$prodcode      = $termsproductcsv_array[1];
			$termtype      = $termsproductcsv_array[2];
			$baseprice     = $termsproductcsv_array[3];
			$discount1     = $termsproductcsv_array[4];
			$discount2     = $termsproductcsv_array[5];
			$nettprice     = $termsproductcsv_array[6];
			$pricetype     = $termsproductcsv_array[7];

			if (!$termcode == "")
			{
				$termsproductinsertquery = "INSERT INTO termsproduct(termcode, prodcode, termtype, baseprice, discount1, discount2, nettprice, pricetype) VALUES('".$termcode."','".$prodcode."','".$termtype."','".$baseprice."','".$discount1."','".$discount2."','".$nettprice."','".$pricetype."')";
			  
				$result = mysqli_query($link, $termsproductinsertquery);

				$i++;
			}
		}
		
		$newfilename="processed/".$termsproductcsv_file.date('m-d-Y_hia');
		rename ($termsproductcsv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);  

		echo "Terms products loaded\n ";

	}

	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD PAC1 - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
 	if (file_exists("PAC1.csv")) 
	{
		// Delete all existing PAC1 rows
		
		$query = "TRUNCATE pac1"; 
		$result = mysqli_query($link, $query);

		echo "PAC1 deleted \n";
		
		$PAC1csv_file = "PAC1.csv";
		$PAC1csvfile = fopen($PAC1csv_file,"r");
//		$thePAC1data = fgets($PAC1csvfile);
		$i = 0;

		while (!feof($PAC1csvfile))
		{
			$PAC1csv_data[] = fgets($PAC1csvfile, 1024);

			$PAC1csv_array = explode(";", $PAC1csv_data[$i]);			
			$code      = $PAC1csv_array[0];
			$desc      = $PAC1csv_array[1];

			if (!$code == "")
			{
				$PAC1insertquery = "INSERT INTO pac1(code, description) VALUES('".$code."','".$desc."')";
			  
				$result = mysqli_query($link, $PAC1insertquery);

				$i++;
			}
		}
		
		$newfilename="processed/".$PAC1csv_file.date('m-d-Y_hia');
		rename ($PAC1csv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);  

		echo "PAC1 loaded ".$i." records \n ";
	}


	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD PAC2 - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
 	if (file_exists("PAC2.csv")) 
	{
		// Delete all existing PAC2 rows
		
		$query = "TRUNCATE pac2"; 
		$result = mysqli_query($link, $query);

		echo "PAC2 deleted \n";
		
		$PAC2csv_file = "PAC2.csv";
		$PAC2csvfile = fopen($PAC2csv_file,"r");
//		$thePAC2data = fgets($PAC2csvfile);
		$i = 0;

		while (!feof($PAC2csvfile))
		{
			$PAC2csv_data[] = fgets($PAC2csvfile, 1024);

			$PAC2csv_array = explode(";", $PAC2csv_data[$i]);			
			$code      = $PAC2csv_array[0];
			$desc      = $PAC2csv_array[1];

			if (!$code == "")
			{
				$PAC2insertquery = "INSERT INTO pac2(code, description) VALUES('".$code."','".$desc."')";
			  
				$result = mysqli_query($link, $PAC2insertquery);

				$i++;
			}
		}
		
		$newfilename="processed/".$PAC2csv_file.date('m-d-Y_hia');
		rename ($PAC2csv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);  

		echo "PAC2 loaded ".$i." records \n ";
	
	}

	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD PAC3 - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
 	if (file_exists("PAC3.csv")) 
	{
		// Delete all existing PAC3 rows
		
		$query = "TRUNCATE pac3"; 
		$result = mysqli_query($link, $query);

		echo "PAC3 deleted \n";
		
		$PAC3csv_file = "PAC3.csv";
		$PAC3csvfile = fopen($PAC3csv_file,"r");
//		$thePAC3data = fgets($PAC3csvfile);
		$i = 0;

		while (!feof($PAC3csvfile))
		{
			$PAC3csv_data[] = fgets($PAC3csvfile, 1024);

			$PAC3csv_array = explode(";", $PAC3csv_data[$i]);			
			$code      = $PAC3csv_array[0];
			$desc      = $PAC3csv_array[1];

			if (!$code == "")
			{
				$PAC3insertquery = "INSERT INTO pac3(code, description) VALUES('".$code."','".$desc."')";
			  
				$result = mysqli_query($link, $PAC3insertquery);

				$i++;
			}
		}
		
		$newfilename="processed/".$PAC3csv_file.date('m-d-Y_hia');
		rename ($PAC3csv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);  

		echo "PAC3 loaded ".$i." records \n ";
		
	}

	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD PAC4 - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
 	if (file_exists("PAC4.csv")) 
	{
		// Delete all existing PAC4 rows
		
		$query = "TRUNCATE pac4"; 
		$result = mysqli_query($link, $query);

		echo "PAC4 deleted \n";
		
		$PAC4csv_file = "PAC4.csv";
		$PAC4csvfile = fopen($PAC4csv_file,"r");
//		$thePAC4data = fgets($PAC4csvfile);
		$i = 0;

		while (!feof($PAC4csvfile))
		{
			$PAC4csv_data[] = fgets($PAC4csvfile, 1024);

			$PAC4csv_array = explode(";", $PAC4csv_data[$i]);			
			$code      = $PAC4csv_array[0];
			$desc      = $PAC4csv_array[1];

			if (!$code == "")
			{
				$PAC4insertquery = "INSERT INTO pac4(code, description) VALUES('".$code."','".$desc."')";
			  
				$result = mysqli_query($link, $PAC4insertquery);

				$i++;
			}
		}
		
		$newfilename="processed/".$PAC4csv_file.date('m-d-Y_hia');
		rename ($PAC4csv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);  

		echo "PAC4 loaded ".$i." records \n ";	
	}

	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD DISCOUNT GROUPS - DELETE AND RELOAD
	// ------------------------------------------------------------------------------------------------------------------------------
 
 	if (file_exists("discgroup.csv")) 
	{
		// Delete all existing product discolunt group rows
		
		$query = "TRUNCATE proddiscgroup"; 
		$result = mysqli_query($link, $query);

		echo "Discount group deleted \n";
		
		$discgroupcsv_file = "discgroup.csv";
		$discgroupcsvfile = fopen($discgroupcsv_file,"r");
//		$thediscgroupdata = fgets($discgroupcsvfile);
		$i = 0;

		while (!feof($discgroupcsvfile))
		{
			$discgroupcsv_data[] = fgets($discgroupcsvfile, 1024);

			$discgroupcsv_array = explode(";", $discgroupcsv_data[$i]);			
			$code      = $discgroupcsv_array[0];
			$desc      = $discgroupcsv_array[1];

			if (!$code == "")
			{
				$discgroupinsertquery = "INSERT INTO proddiscgroup(code, description) VALUES('".$code."','".$desc."')";
			  
				$result = mysqli_query($link, $discgroupinsertquery);

				$i++;
			}
		}
		
		$newfilename="processed/".$discgroupcsv_file.date('m-d-Y_hia');
		rename ($discgroupcsv_file, $newfilename); 

		$logfile = "processed/logfile.txt";
		$fh = fopen($logfile, 'a') or die("Cant open logfile");
		$stringData = $newfilename . "\n";
		fwrite($fh, $stringData);
		fclose($fh);  

		echo "discgroup loaded ".$i." records \n ";
	
	}
	
	// ------------------------------------------------------------------------------------------------------------------------------
	// UPLOAD SALES ANALYSIS CURRENT REP CODE AND PAC CODES WITH THOSE FROM CUSTOMER AND PRODUCT FILES
	// ------------------------------------------------------------------------------------------------------------------------------
	
	$updrepcodequery = "UPDATE salesanalysis INNER JOIN customer ON salesanalysis.account = customer.account SET salesanalysis.currepcode = customer.repcode";
	$updrepcoderesult = mysqli_query($link, $updrepcodequery);
	
	$updpaccodequery = "UPDATE salesanalysis INNER JOIN product ON salesanalysis.prodcode = product.code SET salesanalysis.curpac1code = product.pac1code, salesanalysis.curpac2code = product.pac2code, salesanalysis.curpac3code = product.pac3code, salesanalysis.curpac4code = product.pac4code";
	$updpaccoderesult = mysqli_query($link, $updpaccodequery);

	mysqli_commit($link);
	
?>