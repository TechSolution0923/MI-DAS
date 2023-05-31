<?php
class Customer extends Controller 
{
	public $canSeeMargins, $canEditNotes, $canEditTerms;
	function __construct()
	{
		parent::Controller();
		$this->load->model('customer_model');
		
		$this->load->model('site/site_model');
		
		$this->canSeeMargins = canSeeMargins();
		$this->canEditNotes = canEditNotes();
		$this->canEditTerms = canEditTerms();
	}
	
	
      /**
     * customer_list list
     *
     * @author		Virtual Employee PVT. LTD.
     * @Descrption	Return customer list
     * @Created Date     22-01-2016
     * @Updated Date
     */
	
    function index() {
	    if($this->site_model->is_logged_in()==false){
			redirect('/');
		}
		echo "here2 ";exit;
		// Get the start of this month two years ago, to get two years of data

		$data['daysinmonth'] = date("t",strtotime(date('Y-m-d')));	
		$data['year0'] = date("Y");
		$data['year1'] = $data['year0'] - 1;
		$data['year2'] = $data['year0'] - 2;
		// CR0001 $year3 = $year0 - 3;	
		
		$data['thismonth'] = date("m");
		$data['thisyear']  = date("y");
		
		$data['graphlabel0'] = $data['year0']; // CR0001 $year1 . "-" . $year0;
		$data['graphlabel1'] = $data['year1']; // CR0001 $year2 . "-" . $year1;
		$data['graphlabel2'] = $data['year2']; // CR0001 $year3 . "-" . $year2;
		
		$data['startdate'] = $data['year2'] . "01-01"; // CR0001 "$year3 . "-" . $thismonth . "-" . $daysinmonth;
		
		$data['startyearmonth'] = ($data['year2'] * 100) + 1; // CR0001 ($year3 * 100) + $thismonth;
		$data['startthisyearrmonth'] = ($data['year0'] * 100) + 1; // The start of the current year
		$data['curyearmonth'] = ($data['year0'] * 100) + $data['thismonth']; // e.g. 201507
		
		$data['userDetail']=$this->site_model->getUserDetails($this->session->userdata('userid'));
	//	$data['result']=$this->customer_model->getCustomerList($data['userDetail']['repwhere'], $data['startyearmonth'], $data['curyearmonth']);
	
	$data['result']=$this->customer_model->getCustomerSalesAnalysis($data['userDetail']['repwhere'], $data['startyearmonth'], $data['curyearmonth']);
	
	
		$data['lnk'] = base_url().'customer/customerDetails/';
		$data['main_content']='customer_list';
		$this->load->view('customer/front_template', $data);
	}
     
     
      /**
     * customer_detail method
     *
     * @author		Virtual Employee PVT. LTD.
     * @Descrption	Return customer Data
     * @Created Date     22-01-2016
     * @Updated Date
     */  
     
     function customerDetails($account)
     {
		if($this->site_model->is_logged_in()==false){
			redirect('/');
		}
		//$this->output->cache(1);
		$data['account']=$account;
		$data['userDetail']=$this->site_model->getUserDetails($this->session->userdata('userid'));
		$data['year'] = date("Y");
		$data['thismonth'] = date("m");
		$data['soytemp'] = $data['year']."-"."01-01";
		$data['soy'] = date('Y-m-d', strtotime($data['soytemp']));
		$data['somtemp'] = $data['year']."-".$data['thismonth']."-01";
		$data['som'] = date ('Y-m-d', strtotime($data['somtemp']));
		$data['date'] = date ('Y-m-d');
		
	// --------------------------------------------------------------------------------------------------
	// CUSTOMER DETAILS
	// --------------------------------------------------------------------------------------------------
		
		$result = $this->customer_model->getCustomerDetails($data['account']);
		
		foreach($result as $k=>$val){
			$data[$k]=$val;
		}
		
		
		$data['dellocn'] = $data['dellocncode']." - ".$data['dellocndesc'];
		$data['salesrep'] = $data['repcode']." - ".$data['repname'];

		$data['nameaddress'] = trim($data['customername']);
		if ($data['address1'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address1'];
		if ($data['address2'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address2'];
		if ($data['address3'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address3'];
		if ($data['address4'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address4'];
		if ($data['address5'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address5'];
		if ($data['postcode'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['postcode'];
		if ($data['phone'] <> "") $data['nameaddress'] = trim($data['nameaddress'])." p:".$data['phone'];
		if ($data['email1'] <> "") $data['nameaddress'] = trim($data['nameaddress'])." e:<a href=\"mailto:".$data['email1']."\">".$data['email1']."</a>";

	// --------------------------------------------------------------------------------------------------
	// CUSTOMER SALES GRAPH
	// --------------------------------------------------------------------------------------------------
		
		// Get the start of this month two years ago, to get two years of data

		$data['daysinmonth'] = date("t",strtotime(date('Y-m-d')));	
		$data['year0'] = date("Y");
		$data['year1'] = $data['year0'] - 1;
		$data['year2'] = $data['year0'] - 2;
		// CR0001 $year3 = $year0 - 3;	
		
		$data['thismonth'] = date("m");
		$data['thisyear']  = date("y");
		
		$data['graphlabel0'] = $data['year0']; // CR0001 $year1 . "-" . $year0;
		$data['graphlabel1'] = $data['year1']; // CR0001 $year2 . "-" . $year1;
		$data['graphlabel2'] = $data['year2']; // CR0001 $year3 . "-" . $year2;
		
		$data['startdate'] = $data['year2'] . "01-01"; // CR0001 "$year3 . "-" . $thismonth . "-" . $daysinmonth;
		
		$data['startyearmonth'] = ($data['year2'] * 100) + 1; // CR0001 ($year3 * 100) + $thismonth;
		$data['startthisyearrmonth'] = ($data['year0'] * 100) + 1; // The start of the current year
		$data['curyearmonth'] = ($data['year0'] * 100) + $data['thismonth']; // e.g. 201507
		
		// Initialise sales array
		
		$data['yearmonth']  = array();
		$data['monthnames'] = array();
		$data['sales']      = array();

		// Preload the year and month into an array so that we can make sure we load the sales against the correct row. Pad the month with leading 0 if needed. Had an example where
		// a rep started more recently that three years ago, and therefore there was less than 36 months. It was loading all these into the start of the array, rather than against the
		// appropriate row.
		
		$data['tmpyear']  = $data['year2']; //CR0001 $year3;
		$data['tmpmonth'] = 1; // CR0001 $thismonth + 1;
		
		for ($x = 0; $x <= 36; $x++)
		{
			$data['yearmonth'][$x] = ($data['tmpyear'] * 100) + $data['tmpmonth'];
			$data['sales'][$x] = 0;

			$data['tmpmonth'] = $data['tmpmonth'] + 1;
			if ($data['tmpmonth'] == 13)
			{
				$data['tmpmonth'] = 1;
				$data['tmpyear']  = $data['tmpyear'] + 1;
			}
		}
		
		// Get sales for this account

		$x = 0;
		$rows = $this->customer_model->getSalesAccount($data['account'], $data['startyearmonth'], $data['curyearmonth']);
		foreach($rows as $row){
			$data['salessummaryyearmonth'] = $row['yearmonth'];
			$data['salessummarysales']     = $row['sales'];
		
			// For each data row, loop through the array and put the sales value in the correct place
			
			for ($x = 0; $x <= 36; $x++)
			{		
				if ($data['yearmonth'][$x] == $data['salessummaryyearmonth'] ) $data['sales'][$x] = $data['salessummarysales']; // If the year month of the data matches the array, put the value in
			}
		}

		// Build the $year2data string for the chart
		
		$data['year2data'] = "[";
		$y = 0;
		for ($x = 0; $x <= 11; $x++)
		{ 
			$data['year2data'] .= "[$y,". $data['sales'][$x]."]";
			if ($x != 11) $data['year2data'] .= ",";
			$y = $y + 1;
		} 
		$data['year2data'] .= "]";

		// Build the $year1data string for the chart
		
		$data['year1data'] = "[";
		$y = 0;
		for ($x = 12; $x <= 23; $x++)
		{ 
			$data['year1data'] .= "[$y,".$data['sales'][$x]."]";
			if ($x != 23) $data['year1data'] .= ",";
			$y = $y + 1;
		} 
		$data['year1data'] .= "]";

		// Build the $year0data string for the chart
		
		$data['year0data'] = "[";
		$y = 0;
		for ($x = 24; $x <= 35; $x++)
		{ 
			$data['year0data'] .= "[$y,".$data['sales'][$x]."]";
			if ($x != 35) $data['year0data'] .= ",";
			$y = $y + 1;
		} 
		$data['year0data'] .= "]";
		
		// --------------------------------------------------------------------------------------------------
	// AGED TRANSACTIONS
	// --------------------------------------------------------------------------------------------------
		$data['month0sales'] = $data['sales'][23 + $data['thismonth']];
		$data['month0bal'] = 0;
		$data['month0col'] = 0;
		$data['month0due'] = 0;
		$data['month1sales'] = $data['sales'][23 + ($data['thismonth'] - 1)];
		$data['month1bal'] = 0;
		$data['month1col'] = 0;
		$data['month1due'] = 0;
		$data['month2sales'] = $data['sales'][23 + ($data['thismonth'] - 2)];
		$data['month2bal'] = 0;
		$data['month2col'] = 0;
		$data['month2due'] = 0;
		$data['month3sales'] = $data['sales'][23 + ($data['thismonth'] - 3)];
		$data['month3bal'] = 0;
		$data['month3col'] = 0;
		$data['month3due'] = 0;
		$data['month4sales'] = $data['sales'][23 + ($data['thismonth'] - 4)];
		$data['month4bal'] = 0;
		$data['month4col'] = 0;
		$data['month4due'] = 0;
		$data['month5sales'] = $data['sales'][23 + ($data['thismonth'] - 5)];
		$data['month5bal'] = 0;
		$data['month5col'] = 0;
		$data['month5due'] = 0;
		$data['totalbal']  = 0;
		$data['totalcol']  = 0;
		$data['totaldue']  = 0;
		
		// Set the aged month names, abd start and end dates
		
		$data['agedmonth'] = array();
		$data['agedmonthstartdate'] = array();
		$data['agedmonthenddate']   = array();
		
		$data['monthno'] = $data['thismonth'];
		$data['year']    = $data['thisyear'];
		
		for ($x = 0; $x <= 5; $x++)
		{
			// this is the name
			$data['agedmonth'][$x] = date('F', mktime(0, 0, 0, $data['monthno'], 10));
			
			// this is the start date
			$data['temp'] = $data['year']."-".$data['monthno']."-01";
			$data['agedmonthstartdate'][$x] = date('Y-m-d', strtotime($data['temp']));

			// this is the end date
			$data['daysinmonth'] = date("t",strtotime($data['agedmonthstartdate'][$x]));	
			$data['temp'] = $data['year']."-".$data['monthno']."-".$data['daysinmonth'];
			$data['agedmonthenddate'][$x] = date('Y-m-d', strtotime($data['temp']));
			
			$data['monthno'] = $data['monthno'] - 1;
			if ($data['monthno'] == 0) 
			{
				$data['monthno'] = 12;
				$data['year'] = $data['year'] - 1;
			}
		}
			
		$data['agedtransresult'] = $this->customer_model->getAgeTransaction($account);
		foreach ($data['agedtransresult'] as $row) 
		{
			$data['docdate']   = $row['docdate'];
			$data['docnumber'] = $row['docnumber'];
			$data['custref']   = $row['custref'];
			$data['otherref']  = $row['otherref'];
			$data['docstatus'] = $row['docstatus'];
			$data['doctype']   = $row['doctype'];
			$data['duedate']   = $row['duedate'];
			$data['totalamount']    = $row['totalamount'];
			$data['paidamount']     = $row['paidamount'];
			$data['outstandamount'] = $row['outstandamount'];
			$data['collectamount']  = $row['collectamount'];
			$data['overdueamount']  = $row['overdueamount'];
			
			$data['balance']        = $data['balance'] + $data['outstandamount'];
			$data['collectbalance'] = $data['collectbalance'] + $data['collectamount'];
			$data['overduebalance'] = $data['overduebalance'] + $data['overdueamount'];
			
			$data['docmonth'] = date("m",strtotime($data['docdate']));
			$data['age'] = $data['thismonth'] - $data['docmonth'];
			
			if ($data['age'] == 0) 
			{
				$data['month0bal'] = $data['month0bal'] + $data['outstandamount'];
				$data['month0col'] = $data['month0col'] + $data['collectamount'];
				$data['month0due'] = $data['month0due'] + $data['overdueamount'];
			}
			if ($data['age'] == 1) 
			{
				$data['month1bal'] = $data['month1bal'] + $data['outstandamount'];
				$data['month1col'] = $data['month1col'] + $data['collectamount'];
				$data['month1due'] = $data['month1due'] + $data['overdueamount'];
			}		
			if ($data['age'] == 2) 
			{
				$data['month2bal'] = $data['month2bal'] + $data['outstandamount'];
				$data['month2col'] = $data['month2col'] + $data['collectamount'];
				$data['month2due'] = $data['month2due'] + $data['overdueamount'];
			}
			if ($data['age'] == 3) 
			{
				$data['month3bal'] = $data['month3bal'] + $data['outstandamount'];
				$data['month3col'] = $data['month3col'] + $data['collectamount'];
				$data['month3due'] = $data['month3due'] + $data['overdueamount'];
			}
			if ($data['age'] == 4) 
			{
				$data['month4bal'] = $data['month4bal'] + $data['outstandamount'];
				$data['month4col'] = $data['month4col'] + $data['collectamount'];
				$data['month4due'] = $data['month4due'] + $data['overdueamount'];
			}	
			if ($data['age'] >= 5) 
			{
				$data['month5bal'] = $data['month5bal'] + $data['outstandamount'];
				$data['month5col'] = $data['month5col'] + $data['collectamount'];
				$data['month5due'] = $data['month5due'] + $data['overdueamount'];
			}
			
			$data['totalbal'] = $data['totalbal'] + $data['outstandamount'];
			$data['totalcol'] = $data['totalcol'] + $data['collectamount'];
			$data['totaldue'] = $data['totaldue'] + $data['overdueamount'];		
		}
		
		$data['r_pac1'] = $this->makePacList($this->customer_model->getCustomerPACDetailsWithoutGroupBy($data['account'], 'pac1', $data['startthisyearrmonth'], $data['curyearmonth']));
		
		$data['r_pac2'] =  $this->makePacList($this->customer_model->getCustomerPACDetailsWithoutGroupBy($data['account'], 'pac2', $data['startthisyearrmonth'], $data['curyearmonth']));
		
		$data['r_pac3'] =  $this->makePacList($this->customer_model->getCustomerPACDetailsWithoutGroupBy($data['account'], 'pac3', $data['startthisyearrmonth'], $data['curyearmonth']));
		
		$data['r_pac4'] =  $this->makePacList($this->customer_model->getCustomerPACDetailsWithoutGroupBy($data['account'], 'pac4', $data['startthisyearrmonth'], $data['curyearmonth']));
		
		$data['r_product']=$this->customer_model->getCustomerSAProduct($data['account'], $data['startthisyearrmonth'], $data['curyearmonth']);
		
		$data['r_orders']=$this->customer_model->getCustomerSAOrder($data['account']);
		$data['main_content']='customer_details';
		
		$this->load->view('customer/front_template', $data);
	 }
	 
	/* Function to create the group by and sum of the quantity sales and cost manually on the basis of the condition that the salesanalysis date is of this year-month */
	
	public function makePacList($rawCustomerPACDetailsArray) {
		$newArray = array();
		foreach($rawCustomerPACDetailsArray as $row) {			
			if(strtotime($row['date']) >= strtotime(date('Y-m-01'))) {
				$newArray[$row['code']]['qtymtd'] += $row['quantity'];
				$newArray[$row['code']]['salesmtd'] += $row['sales'];
				$newArray[$row['code']]['costmtd'] += $row['cost'];
			}
			$newArray[$row['code']]['code'] = $row['code'];
			$newArray[$row['code']]['description'] = $row['description'];
			$newArray[$row['code']]['qtyytd'] += $row['quantity'];
			$newArray[$row['code']]['salesytd'] += $row['sales'];
			$newArray[$row['code']]['costytd'] += $row['cost'];
		}
		 
		return $newArray;
	}
	
	  /**
     * customerDetailsQuotes method
     *
     * @author		Virtual Employee PVT. LTD.
     * @Descrption	Return customer quotes Data
     * @Created Date     22-01-2016
     * @Updated Date
     */  
     
     function customerDetailsQuotes($account)
     {
		$data['account']=$account;
		$data['result']=$this->customer_model->getCustomerQuotes($data['account']);
		$this->load->view('customer_details_quotes', $data);
	 }
	 
	 /**
     * customerDetailsQuotes method
     *
     * @author		Virtual Employee PVT. LTD.
     * @Descrption	Return customer quotes Data
     * @Created Date     22-01-2016
     * @Updated Date
     */  
     
     function customerDetailsOrders($account)
     {
		$data['account']=$account;
		$data['result']=$this->customer_model->getCustomerOrders($data['account']);
		$this->load->view('customer_details_orders', $data);
	 }
	 
	 /**
     * customerDetailsTerms method
     *
     * @author		Virtual Employee PVT. LTD.
     * @Descrption	Return customer quotes Data
     * @Created Date     22-01-2016
     * @Updated Date
     */  
     
     function customerDetailsTerms($account)
     {
		$data['account']=$account;
		$userSales=$this->customer_model->getCustomerSales($data['account']);
		//var_dump($userSales); exit;
		$data['t_product']=$this->customer_model->getCustomerTermsProduct($data['account']);
		$terms['terms1code'] = $userSales['terms1code'];
		$terms['terms2code'] = $userSales['terms2code'];
		$terms['terms3code'] = $userSales['terms3code'];
		$terms['terms4code'] = $userSales['terms4code'];
		$terms['terms5code'] = $userSales['terms5code'];
		$data['t_group']=$this->customer_model->getCustomerTermsGroup($terms);
		$this->load->view('customer_details_terms', $data);
	 }
	 
	 /**
     * customerDetailsDetail method
     *
     * @author		Virtual Employee PVT. LTD.
     * @Descrption	Return details tab
     * @Created Date     22-01-2016
     * @Updated Date
     */  
     
     function customerDetailsDetail($account)
     {
		$data['account']=$account;
		$result = $this->customer_model->getCustomerDetails($data['account']);
		
		foreach($result as $k=>$val){
			$data[$k]=$val;
		}
		
		
		$data['dellocn'] = $data['dellocn']." - ".$data['dellocndesc'];
		$data['salesrep'] = $data['repcode']." - ".$data['repname'];

		$data['nameaddress'] = trim($data['customername']);
		if ($data['address1'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address1'];
		if ($data['address2'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address2'];
		if ($data['address3'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address3'];
		if ($data['address4'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address4'];
		if ($data['address5'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address5'];
		if ($data['postcode'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['postcode'];
		if ($data['phone'] <> "") $data['nameaddress'] = trim($data['nameaddress'])." p:".$data['phone'];
		
		$this->load->view('customer_details_details', $data);
	 }
	 
	 /**
     * customerDetailsBalance method
     *
     * @author		Virtual Employee PVT. LTD.
     * @Descrption	Return customer balance tab Data
     * @Created Date     22-01-2016
     * @Updated Date
     */  
     
     function customerDetailsBalance($account)
     {
		$data['account']=$account;
		$result = $this->customer_model->getCustomerDetails($data['account']);
		
		foreach($result as $k=>$val){
			$data[$k]=$val;
		}
		
		
		$data['dellocn'] = $data['dellocncode']." - ".$data['dellocndesc'];
		$data['salesrep'] = $data['repcode']." - ".$data['repname'];

		$data['nameaddress'] = trim($data['customername']);
		if ($data['address1'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address1'];
		if ($data['address2'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address2'];
		if ($data['address3'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address3'];
		if ($data['address4'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address4'];
		if ($data['address5'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['address5'];
		if ($data['postcode'] <> "") $data['nameaddress'] = trim($data['nameaddress']).", ".$data['postcode'];
		if ($data['phone'] <> "") $data['nameaddress'] = trim($data['nameaddress'])." p:".$data['phone'];
	// --------------------------------------------------------------------------------------------------
	// CUSTOMER SALES GRAPH
	// --------------------------------------------------------------------------------------------------
		
		// Get the start of this month two years ago, to get two years of data

		$data['daysinmonth'] = date("t",strtotime(date('Y-m-d')));	
		$data['year0'] = date("Y");
		$data['year1'] = $data['year0'] - 1;
		$data['year2'] = $data['year0'] - 2;
		// CR0001 $year3 = $year0 - 3;	
		
		$data['thismonth'] = date("m");
		$data['thisyear']  = date("y");
		
		$data['graphlabel0'] = $data['year0']; // CR0001 $year1 . "-" . $year0;
		$data['graphlabel1'] = $data['year1']; // CR0001 $year2 . "-" . $year1;
		$data['graphlabel2'] = $data['year2']; // CR0001 $year3 . "-" . $year2;
		
		$data['startdate'] = $data['year2'] . "01-01"; // CR0001 "$year3 . "-" . $thismonth . "-" . $daysinmonth;
		
		$data['startyearmonth'] = ($data['year2'] * 100) + 1; // CR0001 ($year3 * 100) + $thismonth;
		$data['startthisyearrmonth'] = ($data['year0'] * 100) + 1; // The start of the current year
		$data['curyearmonth'] = ($data['year0'] * 100) + $data['thismonth']; // e.g. 201507
		
		// Initialise sales array
		
		$data['yearmonth']  = array();
		$data['monthnames'] = array();
		$data['sales']      = array();

		// Preload the year and month into an array so that we can make sure we load the sales against the correct row. Pad the month with leading 0 if needed. Had an example where
		// a rep started more recently that three years ago, and therefore there was less than 36 months. It was loading all these into the start of the array, rather than against the
		// appropriate row.
		
		$data['tmpyear']  = $data['year2']; //CR0001 $year3;
		$data['tmpmonth'] = 1; // CR0001 $thismonth + 1;
		
		for ($x = 0; $x <= 36; $x++)
		{
			$data['yearmonth'][$x] = ($data['tmpyear'] * 100) + $data['tmpmonth'];
			$data['sales'][$x] = 0;

			$data['tmpmonth'] = $data['tmpmonth'] + 1;
			if ($data['tmpmonth'] == 13)
			{
				$data['tmpmonth'] = 1;
				$data['tmpyear']  = $data['tmpyear'] + 1;
			}
		}
		
		// Get sales for this account

		$x = 0;
		$rows = $this->customer_model->getSalesAccount($data['account'], $data['startyearmonth'], $data['curyearmonth']);
		foreach($rows as $row){
			$data['salessummaryyearmonth'] = $row['yearmonth'];
			$data['salessummarysales']     = $row['sales'];
		
			// For each data row, loop through the array and put the sales value in the correct place
			
			for ($x = 0; $x <= 36; $x++)
			{		
				if ($data['yearmonth'][$x] == $data['salessummaryyearmonth'] ) $data['sales'][$x] = $data['salessummarysales']; // If the year month of the data matches the array, put the value in
			}
		}

		// Build the $year2data string for the chart
		
		$data['year2data'] = "[";
		$y = 0;
		for ($x = 0; $x <= 11; $x++)
		{ 
			$data['year2data'] .= "[$y,". $data['sales'][$x]."]";
			if ($x != 11) $data['year2data'] .= ",";
			$y = $y + 1;
		} 
		$data['year2data'] .= "]";

		// Build the $year1data string for the chart
		
		$data['year1data'] = "[";
		$y = 0;
		for ($x = 12; $x <= 23; $x++)
		{ 
			$data['year1data'] .= "[$y,$sales[$x]]";
			if ($x != 23) $data['year1data'] .= ",";
			$y = $y + 1;
		} 
		$data['year1data'] .= "]";

		// Build the $year0data string for the chart
		
		$data['year0data'] = "[";
		$y = 0;
		for ($x = 24; $x <= 35; $x++)
		{ 
			$data['year0data'] .= "[$y,$sales[$x]]";
			if ($x != 35) $data['year0data'] .= ",";
			$y = $y + 1;
		} 
		$data['year0data'] .= "]";


	// --------------------------------------------------------------------------------------------------
	// AGED TRANSACTIONS
	// --------------------------------------------------------------------------------------------------
		$data['month0sales'] = $data['sales'][23 + $data['thismonth']];
		$data['month0bal'] = 0;
		$data['month0col'] = 0;
		$data['month0due'] = 0;
		$data['month1sales'] = $data['sales'][23 + ($data['thismonth'] - 1)];
		$data['month1bal'] = 0;
		$data['month1col'] = 0;
		$data['month1due'] = 0;
		$data['month2sales'] = $data['sales'][23 + ($data['thismonth'] - 2)];
		$data['month2bal'] = 0;
		$data['month2col'] = 0;
		$data['month2due'] = 0;
		$data['month3sales'] = $data['sales'][23 + ($data['thismonth'] - 3)];
		$data['month3bal'] = 0;
		$data['month3col'] = 0;
		$data['month3due'] = 0;
		$data['month4sales'] = $data['sales'][23 + ($data['thismonth'] - 4)];
		$data['month4bal'] = 0;
		$data['month4col'] = 0;
		$data['month4due'] = 0;
		$data['month5sales'] = $data['sales'][23 + ($data['thismonth'] - 5)];
		$data['month5bal'] = 0;
		$data['month5col'] = 0;
		$data['month5due'] = 0;
		$data['totalbal']  = 0;
		$data['totalcol']  = 0;
		$data['totaldue']  = 0;
		
		// Set the aged month names, abd start and end dates
		
		$data['agedmonth'] = array();
		$data['agedmonthstartdate'] = array();
		$data['agedmonthenddate']   = array();
		
		$data['monthno'] = $data['thismonth'];
		$data['year']    = $data['thisyear'];
		
		for ($x = 0; $x <= 5; $x++)
		{
			// this is the name
			$data['agedmonth'][$x] = date('F', mktime(0, 0, 0, $data['monthno'], 10));
			
			// this is the start date
			$data['temp'] = $data['year']."-".$data['monthno']."-01";
			$data['agedmonthstartdate'][$x] = date('Y-m-d', strtotime($data['temp']));

			// this is the end date
			$data['daysinmonth'] = date("t",strtotime($data['agedmonthstartdate'][$x]));	
			$data['temp'] = $data['year']."-".$data['monthno']."-".$data['daysinmonth'];
			$data['agedmonthenddate'][$x] = date('Y-m-d', strtotime($data['temp']));
			
			$data['monthno'] = $data['monthno'] - 1;
			if ($data['monthno'] == 0) 
			{
				$data['monthno'] = 12;
				$data['year'] = $data['year'] - 1;
			}
		}
			
		$data['agedtransresult'] = $this->customer_model->getAgeTransaction($account);
		foreach ($data['agedtransresult'] as $row) 
		{
			$data['docdate']   = $row['docdate'];
			$data['docnumber'] = $row['docnumber'];
			$data['custref']   = $row['custref'];
			$data['otherref']  = $row['otherref'];
			$data['docstatus'] = $row['docstatus'];
			$data['doctype']   = $row['doctype'];
			$data['duedate']   = $row['duedate'];
			$data['totalamount']    = $row['totalamount'];
			$data['paidamount']     = $row['paidamount'];
			$data['outstandamount'] = $row['outstandamount'];
			$data['collectamount']  = $row['collectamount'];
			$data['overdueamount']  = $row['overdueamount'];
			
			$data['balance']        = $data['balance'] + $data['outstandamount'];
			$data['collectbalance'] = $data['collectbalance'] + $data['collectamount'];
			$data['overduebalance'] = $data['overduebalance'] + $data['overdueamount'];
			
			$data['docmonth'] = date("m",strtotime($data['docdate']));
			$data['age'] = $data['thismonth'] - $data['docmonth'];
			
			if ($data['age'] == 0) 
			{
				$data['month0bal'] = $data['month0bal'] + $data['outstandamount'];
				$data['month0col'] = $data['month0col'] + $data['collectamount'];
				$data['month0due'] = $data['month0due'] + $data['overdueamount'];
			}
			if ($data['age'] == 1) 
			{
				$data['month1bal'] = $data['month1bal'] + $data['outstandamount'];
				$data['month1col'] = $data['month1col'] + $data['collectamount'];
				$data['month1due'] = $data['month1due'] + $data['overdueamount'];
			}		
			if ($data['age'] == 2) 
			{
				$data['month2bal'] = $data['month2bal'] + $data['outstandamount'];
				$data['month2col'] = $data['month2col'] + $data['collectamount'];
				$data['month2due'] = $data['month2due'] + $data['overdueamount'];
			}
			if ($data['age'] == 3) 
			{
				$data['month3bal'] = $data['month3bal'] + $data['outstandamount'];
				$data['month3col'] = $data['month3col'] + $data['collectamount'];
				$data['month3due'] = $data['month3due'] + $data['overdueamount'];
			}
			if ($data['age'] == 4) 
			{
				$data['month4bal'] = $data['month4bal'] + $data['outstandamount'];
				$data['month4col'] = $data['month4col'] + $data['collectamount'];
				$data['month4due'] = $data['month4due'] + $data['overdueamount'];
			}	
			if ($data['age'] >= 5) 
			{
				$data['month5bal'] = $data['month5bal'] + $data['outstandamount'];
				$data['month5col'] = $data['month5col'] + $data['collectamount'];
				$data['month5due'] = $data['month5due'] + $data['overdueamount'];
			}
			
			$data['totalbal'] = $data['totalbal'] + $data['outstandamount'];
			$data['totalcol'] = $data['totalcol'] + $data['collectamount'];
			$data['totaldue'] = $data['totaldue'] + $data['overdueamount'];		
		}
		
		$data['monthlisting'][0]=$this->customer_model->getAgeMonthList($data['account'],$data['agedmonthstartdate'][0],$data['agedmonthenddate'][0]);
		$data['monthlisting'][1]=$this->customer_model->getAgeMonthList($data['account'],$data['agedmonthstartdate'][1],$data['agedmonthenddate'][1]);
		$data['monthlisting'][2]=$this->customer_model->getAgeMonthList($data['account'],$data['agedmonthstartdate'][2],$data['agedmonthenddate'][2]);
		$data['monthlisting'][3]=$this->customer_model->getAgeMonthList($data['account'],$data['agedmonthstartdate'][3],$data['agedmonthenddate'][3]);
		$data['monthlisting'][4]=$this->customer_model->getAgeMonthList($data['account'],$data['agedmonthstartdate'][4],$data['agedmonthenddate'][4]);
		$data['monthlisting'][5]=$this->customer_model->getAgeMonthList($data['account'],$data['agedmonthstartdate'][5],$data['agedmonthenddate'][5]);
		
		$this->load->view('customer_details_balances', $data);
	 }
	 
	 public function saveInternalText(){
		 if(!$this->canSeeMargins) {
			 echo "Invalid data";
			 exit;
		 }
		$this->customer_model->updateInternalText($this->input->post('custId'),$this->input->post('val'));
		// updating csv
		$account = $this->input->post('custId');
		$internaltext = $this->input->post('val');
		
		if (file_exists("public/csv/midas_internaltext.csv")) 
		{
			// If the file exists, open it in append mode
			$fp = fopen('public/csv/midas_internaltext.csv', 'a');
			
			// If the file exists, it will already have headings
			$list = array (
				array($account, $internaltext)
			);
		}
		else
		{
			// If the file doesnt exist, open it in write mode
			$fp = fopen('public/csv/midas_internaltext.csv', 'w');
			
			// If the file doesnt exist, need to write headers first
			$list = array (
				array('ACCOUNT', 'INTERTXT'),
				array($account, $internaltext)
			);		
		}

		// Write the data to the file
		foreach ($list as $fields) {
			fputcsv($fp, $fields);
		}
		
		fclose($fp);
	 }
	 
	 public function saveGroupDiscount(){
		//echo $this->input->post('p') ."=/=". $this->input->post('acID') ."=/=".  $this->input->post('newVal');
		$split_data = explode(':', $this->input->post('p'));
			
		$table      = $split_data[0];
		$column     = $split_data[1];
		$keycolumn  = $split_data[2];
		$keydata    = $split_data[3];
		$newVal		= $this->input->post('newVal');

		if(!empty($table) && !empty($column) && !empty($keycolumn) && !empty($keydata) && $newVal>=0 && $this->canEditTerms)
		{
			$str_csv = $this->customer_model->updateGroupDiscount($table, $column, $keycolumn, $keydata, $newVal);
			$CSV_file = "public/csv/MI-DAS_".$table."_".$column.".csv";
	
			if (file_exists($CSV_file)) 
			{
				// If the file exists, open it in append mode
				$fp = fopen($CSV_file, 'a');
				
				// If the file exists, it will already have headings
				$list = array (
					array($str_csv['termcode'], $str_csv['discgroupcode'], $str_csv['termtype'], $newVal)
				);
			}
			else
			{
				// If the file doesnt exist, open it in write mode
				$fp = fopen($CSV_file, 'w');
				
				// If the file doesnt exist, need to write headers first
				$list = array (
					array('CLASS','CODE','TYPE', $column),
					array($str_csv['termcode'], $str_csv['discgroupcode'], $str_csv['termtype'], $newVal)
				);		
			}
			
			// Write the data to the file
			foreach ($list as $fields) {
				fputcsv($fp, $fields);
			}
			
			fclose($fp);
		} else {
			echo "Invalid Requests";
		}
		
	 }
	 
	 public function saveProductDiscount(){
		$split_data = explode(':', $this->input->post('p'));
			
		$table      = $split_data[0];
		$column     = $split_data[1];
		$keycolumn  = $split_data[2];
		$keydata    = $split_data[3];
		$newVal		= $this->input->post('newVal');
		$netPrice		= $this->input->post('netPrice');

		if(!empty($table) && !empty($column) && !empty($keycolumn) && !empty($keydata) && $newVal>=0 && $this->canEditTerms)
		{
			$str_csv = $this->customer_model->updateProductDiscount($table, $column, $keycolumn, $keydata, $newVal, $netPrice);
			$CSV_file = "public/csv/MI-DAS_".$table."_".$column.".csv";
	
			if (file_exists($CSV_file)) 
			{
				// If the file exists, open it in append mode
				$fp = fopen($CSV_file, 'a');
				
				// If the file exists, it will already have headings
				$list = array (
					array($str_csv['termcode'], $str_csv['prodcode'], '', $str_csv['termtype'], 'GBP', $newVal)
				);
			}
			else
			{
				// If the file doesnt exist, open it in write mode
				$fp = fopen($CSV_file, 'w');
				
				// If the file doesnt exist, need to write headers first
				$list = array (
					array('CLASS','PARTNO','MANUFACT','TYPE', 'CURRENCY', $column),
					array($str_csv['termcode'], $str_csv['prodcode'], '', $str_csv['termtype'], 'GBP', $newVal)
				);		
			}
			//var_dump($list);
			// Write the data to the file
			foreach ($list as $fields) {
				fputcsv($fp, $fields);
			}
			
			fclose($fp);
		} else {
			echo "Invalid Requests";
		}
	 }
	 
	 /**
     * customerDetailsTerms method
     *
     * @author		Virtual Employee PVT. LTD.
     * @Descrption	Return customer quotes Data
     * @Created Date     22-01-2016
     * @Updated Date
     */  
     
     function customerModelGraph()
     {
		// Get the passed in parameters
		$account  = $this->input->post("account");

		$level    = $this->input->post("level");
		$code     = $this->input->post("code");

		// Get the current month and year for sales analysis
		
		$date = date('Y-m-d');
		$year = date("Y");
		$thismonth = date("m");
		$soytemp = $year."-"."01-01";
		$soy = date('Y-m-d', strtotime($soytemp));
		$somtemp = $year."-".$thismonth."-01";
		$som = date ('Y-m-d', strtotime($somtemp));

	// --------------------------------------------------------------------------------------------------
	// PRODUCT SALES GRAPH
	// --------------------------------------------------------------------------------------------------
		
		// Get the start of this month three years ago, to get three years of data
		
		$daysinmonth = date("t",strtotime($date));	
		$year0 = date("Y");
		$year1 = $year0 - 1;
		$year2 = $year0 - 2;
		// CR0001 $year3 = $year0 - 3;	
		
		$thismonth = date("m");
		
		$graphlabel0 = $year0; // CR0001 $year1 . "-" . $year0;
		$graphlabel1 = $year1; // CR0001 $year2 . "-" . $year1;
		$graphlabel2 = $year2; // CR0001 $year3 . "-" . $year2;
		
		$startdate = $year2 . "01-01"; // CR0001 "$year3 . "-" . $thismonth . "-" . $daysinmonth;
		
		$startyearmonth = ($year2 * 100) + 1; // CR0001 ($year3 * 100) + $thismonth;
		$startthisyearrmonth = ($year0 * 100) + 1; // The start of the current year
		$curyearmonth = ($year0 * 100) + $thismonth; // e.g. 201507
		
		// Initialise sales array
		
		$yearmonth  = array();
		$monthnames = array();
		$sales      = array();

		// Preload the year and month into an array so that we can make sure we load the sales against the correct row. Pad the month with leading 0 if needed. Had an example where
		// a rep started more recently that three years ago, and therefore there was less than 36 months. It was loading all these into the start of the array, rather than against the
		// appropriate row.
		
		$tmpyear  = $year2; //CR0001 $year3;
		$tmpmonth = 1; // CR0001 $thismonth + 1;
		
		for ($x = 0; $x <= 36; $x++)
		{
			$yearmonth[$x] = ($tmpyear * 100) + $tmpmonth;
			$sales[$x] = 0;

			$tmpmonth = $tmpmonth + 1;
			if ($tmpmonth == 13)
			{
				$tmpmonth = 1;
				$tmpyear  = $tmpyear + 1;
			}
		}
		
		// Get sales for this product & sales rep. THIS IS FOR GRAPH - LIST SQLS FURTHER DOWN
		
		if ($level == "pac1") $levelclause = "product.pac1code = '$code'";
		if ($level == "pac2") $levelclause = "product.pac2code = '$code'";
		if ($level == "pac3") $levelclause = "product.pac3code = '$code'";
		if ($level == "pac4") $levelclause = "product.pac4code = '$code'";
		if ($level == "product") $levelclause = "product.code = '$code'";
	//	echo $account .'/'. $levelclause .'/'. $startyearmonth .'/'. $curyearmonth; exit;
		$result = $this->customer_model->getSAChart($account, $levelclause, $startyearmonth, $curyearmonth);
		
		foreach($result as $row){
			$salessummaryyearmonth = $row['yearmonth'];
			$salessummarysales     = $row['sales'];
		
			// For each data row, loop through the array and put the sales value in the correct place
			
			for ($x = 0; $x <= 36; $x++)
			{		
				if ($yearmonth[$x] == $salessummaryyearmonth ) $sales[$x] = $salessummarysales; // If the year month of the data matches the array, put the value in
			}
		}

		// Build the $year2data string for the chart
		
		$year2data = "[";
		$y = 0;
		for ($x = 0; $x <= 11; $x++)
		{ 
			$year2data .= "[$y,$sales[$x]]";
			if ($x != 11) $year2data .= ",";
			$y = $y + 1;
		} 
		$year2data .= "]";
		
		// Build the $year1data string for the chart
		
		$year1data = "[";
		$y = 0;
		for ($x = 12; $x <= 23; $x++)
		{ 
			$year1data .= "[$y,$sales[$x]]";
			if ($x != 23) $year1data .= ",";
			$y = $y + 1;
		} 
		$year1data .= "]";

		// Build the $year0data string for the chart
		
		$year0data = "[";
		$y = 0;
		for ($x = 24; $x <= 35; $x++)
		{ 
			$year0data .= "[$y,$sales[$x]]";
			if ($x != 35) $year0data .= ",";
			$y = $y + 1;
		} 
		$year0data .= "]";
		
		$dataarray = array();
		$dataarray[0] = $year0data;
		$dataarray[1] = $year1data;
		$dataarray[2] = $year2data;
		
		echo json_encode($dataarray);
	 }
	 
	 public function drawCustomerProductsDetails($account, $startthisyearrmonth, $curyearmonth) {	
		$r_product=$this->customer_model->getCustomerSAProduct($account, $startthisyearrmonth, $curyearmonth);
		foreach($r_product as $row) {
			$qtymtd = 0;
			$salesmtd = 0;
			$costmtd  = 0;
			$marginmtdpc = 0;
			$qtyytd = 0;
			$salesytd = 0;
			$costytd = 0;
			$marginytdpc = 0;
			$code = "";
			extract($row);
																		
			if ($date >= $som)  
			{	
				$qtymtd   = $qtymtd + $quantity;
				$salesmtd = $salesmtd + $sales;
				$costmtd  = $costmtd + $cost;
			}

			// No sales found, set to 0
											
			if (IS_NULL($qtymtd)) $qtymtd = 0;
			if (IS_NULL($salesmtd)) $salesmtd = 0;
			if (IS_NULL($costmtd)) $costmtd = 0;
											
			$marginmtd = $salesmtd - $costmtd;
											
			if ($salesmtd != 0)
			{
				$marginmtdpc = ($marginmtd / $salesmtd) * 100;
				$marginmtdpc = number_format($marginmtdpc);
			}
			else
			{
				$marginmtdpc = 0;
			}
										
			$qtyytd   = $qtyytd + $quantity;
			$salesytd = $salesytd + $sales;
			$costytd = $costytd + $cost;

			// No sales found, set to 0
											
			if (IS_NULL($qtyytd)) $qtyytd = 0;
			if (IS_NULL($salesytd)) $salesytd = 0;
			if (IS_NULL($costytd)) $costytd = 0;
											
			$marginytd = $salesytd - $costytd;
											
			if ($salesytd != 0)
			{
				$marginytdpc = ($marginytd / $salesytd) * 100;
				$marginytdpc = number_format($marginytdpc);														
			}
			else
			{
				$marginytdpc = 0;
			}
			echo "<tr>";
			echo "<td>$code</td>";
			echo "<td><a href='#GraphModal' data-toggle='modal' data-target='#GraphModal' data-account=$account data-level='product' data-code=$code data-description='$description'>$description</a></td>";
			echo "<td>$qtymtd</td>";
			echo "<td>$salesmtd</td>";
			if($this->canSeeMargins) {
				echo "<td>$marginmtdpc</td>";
			}
			echo "<td>$qtyytd</td>";
			echo "<td>$salesytd</td>";
			if($this->canSeeMargins) {
				echo "<td>$marginytdpc</td>";
			}
			echo "</tr>";
		}
		exit;
	 }
	 
	 /* Function to fetch the customer list */
	 
	 public function fetchCustomerList($page=1, $max=10) {
		 // Get the start of this month two years ago, to get two years of data
		$data['daysinmonth'] = date("t",strtotime(date('Y-m-d')));	
		$data['year0'] = date("Y");
		$data['year1'] = $data['year0'] - 1;
		$data['year2'] = $data['year0'] - 2;
		// CR0001 $year3 = $year0 - 3;	
		
		$data['thismonth'] = date("m");
		$data['thisyear']  = date("y");
		
		$data['graphlabel0'] = $data['year0']; // CR0001 $year1 . "-" . $year0;
		$data['graphlabel1'] = $data['year1']; // CR0001 $year2 . "-" . $year1;
		$data['graphlabel2'] = $data['year2']; // CR0001 $year3 . "-" . $year2;
		
		$data['startdate'] = $data['year2'] . "01-01"; // CR0001 "$year3 . "-" . $thismonth . "-" . $daysinmonth;
		
		$data['startyearmonth'] = ($data['year2'] * 100) + 1; // CR0001 ($year3 * 100) + $thismonth;
		$data['startthisyearrmonth'] = ($data['year0'] * 100) + 1; // The start of the current year
		$data['curyearmonth'] = ($data['year0'] * 100) + $data['thismonth']; // e.g. 201507
		
		$data['userDetail']=$this->site_model->getUserDetails($this->session->userdata('userid'));
		
		$data['result'] = $this->customer_model->getCustomerList($data['userDetail']['repwhere'], $data['startyearmonth'], $data['curyearmonth']);
		
		$data['lnk'] = base_url().'customer/customerDetails/';

		$data['result'] = $this->limitManually($data['result'], $page, $max);
		
		 echo json_encode(array("data"=>$data['result']));
	//	 echo json_encode($this->load->view('customer/rows_array',$data,true));
		 exit;
		// echo $this->load->view('customer/rows',$data,true);
	 }
	 
	/* Function to limit the output manually */	
	public function limitManually($result, $page=0, $maxRows=0) {
		
		$offset = ($page-1)*$maxRows;	
		
		$customerData = array();
		$new = array();
		foreach($result as $row) {
			$customerData[$row['account']]['account'] = $row['account'];
			$customerData[$row['account']]['name'] = $row['name'];
			$customerData[$row['account']]['postcode'] = $row['postcode'];
			$new[$row['account']][] = $row['account'];
			$new[$row['account']][] = $row['name'];
			$new[$row['account']][] = $row['postcode'];
			
			$customerData[$row['account']]['quantity'] = $row['quantity'];
			$customerData[$row['account']]['sales'] = $row['sales'];
			$customerData[$row['account']]['cost'] = $row['cost'];
			
			if ($row['yearmonth'] == date('Ym')) {	
				$customerData[$row['account']]['qtymtd'] += $customerData[$row['account']]['quantity'];
				$customerData[$row['account']]['salesmtd'] += $customerData[$row['account']]['sales'];
				$customerData[$row['account']]['costmtd'] += $customerData[$row['account']]['cost'];
			}
			
			// No sales found, set to 0
			
			if (IS_NULL($customerData[$row['account']]['qtymtd'])) $customerData[$row['account']]['qtymtd'] = 0;
			if (IS_NULL($customerData[$row['account']]['salesmtd'])) $customerData[$row['account']]['salesmtd'] = 0;
			if (IS_NULL($customerData[$row['account']]['costmtd'])) $customerData[$row['account']]['costmtd'] = 0;
			
			
			$customerData[$row['account']]['qtyytd'] = $customerData[$row['account']]['qtyytd'] + $customerData[$row['account']]['quantity'];
			$customerData[$row['account']]['salesytd'] = $customerData[$row['account']]['salesytd'] + $customerData[$row['account']]['sales'];
			$customerData[$row['account']]['costytd']  = $customerData[$row['account']]['costytd'] + $customerData[$row['account']]['cost'];

			// No sales found, set to 0
			
			if (IS_NULL($customerData[$row['account']]['qtyytd'])) $customerData[$row['account']]['qtyytd'] = 0;
			if (IS_NULL($customerData[$row['account']]['salesytd'])) $customerData[$row['account']]['salesytd'] = 0;
			if (IS_NULL($customerData[$row['account']]['costytd'])) $customerData[$row['account']]['costytd'] = 0;
			
			$new[$row['account']][] = $customerData[$row['account']]['qtymtd'];
			$new[$row['account']][] = $customerData[$row['account']]['salesmtd'];
			
			$new[$row['account']][] = $customerData[$row['account']]['qtyytd'];
			$new[$row['account']][] = $customerData[$row['account']]['salesytd'];
			
			$customerData[$row['account']]['marginytd'] = $customerData[$row['account']]['salesytd'] - $customerData[$row['account']]['costytd'];
			
			if($customerData[$row['account']]['salesytd'] != 0) {
				$customerData[$row['account']]['marginytdpc'] = ($customerData[$row['account']]['marginytd'] / $customerData[$row['account']]['salesytd']) * 100;
				$customerData[$row['account']]['marginytdpc'] = number_format($customerData[$row['account']]['marginytdpc']);	
			} else {
				$customerData[$row['account']]['marginytdpc'] = 0;
			}
			
			$new[$row['account']][] = $customerData[$row['account']]['marginmtdpc'];
			$new[$row['account']][] = $customerData[$row['account']]['marginytdpc'];
		}
		
//		$customerData = $this->Filtered_Array($customerData);
		$maxRows = 0;
		if($maxRows>0) {
			$customerData = array_slice($customerData, $offset, $maxRows);
		}
		sort($new);
		return $new;
	}
	
	/* Filter the array on the basis of the posted queries. */
	public function Filtered_Array($customerData) {		
		$keyword = $this->input->post("key");
		$account = $this->input->post("account");
		$name = $this->input->post("name");
		$postcode = $this->input->post("postcode");
		$qtymtd = $this->input->post("qtymtd");
		$salesmtd = $this->input->post("salesmtd");
		$marginmtdpc = $this->input->post("marginmtdpc");
		$qtyytd = $this->input->post("qtyytd");
		$salesytd = $this->input->post("salesytd");
		$marginytdpc = $this->input->post("marginytdpc");
		
		$filteredArray = array();
		foreach($customerData as $cdata) {
			if(""!=trim($keyword) && ($cdata['account']==trim($keyword) || $cdata['name']==trim($keyword) || $cdata['postcode']==trim($keyword) || $cdata['qtymtd']==trim($keyword) || $cdata['salesmtd']==trim($keyword) || $cdata['marginmtdpc']==trim($keyword) || $cdata['qtyytd']==trim($keyword) || $cdata['salesytd']==trim($keyword) || $cdata['marginytdpc']==trim($keyword))) {
				$filteredArray[$cdata['account']] = $cdata;
			}
		}
		if(!empty($filteredArray)) {
			return $filteredArray;
		} else {
			return $customerData;
		}
	}
	 
}
?>
