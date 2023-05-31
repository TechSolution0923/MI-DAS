<?php
	class Customer extends Controller
	{
		public $canSeeMargins, $canEditNotes, $canEditTerms, $urlFn, $isAdmin;

		function __construct()
		{
			parent::Controller();
			$this->load->model('customer_model');

			$this->load->model('site/site_model');
			$loggedInUserDetails = $this->users_model->getUserDetails($this->session->userdata('userid'));
			$this->isAdmin = $loggedInUserDetails['administrator'];

			$this->canSeeMargins = canSeeMargins();
			$this->canEditNotes = canEditNotes();
			$this->canEditTerms = canEditTerms();
			$this->urlFn = "";
		}

		public function index($alternateQuery = false)
		{
			if ($this->site_model->is_logged_in()==false){
				redirect('/');
			}
			setcookie($this->config->item('site_name').'_'.$this->session->userdata('userid').'_last_visited', current_url(), time() + (86400 * 365), "/"); // 86400 = 1 day
			// Get the start of this month two years ago, to get two years of data

			$data['daysinmonth'] = date("t",strtotime(date('Y-m-d')));
			$data['year0'] = date("Y");
			$data['year1'] = $data['year0'] - 1;
			$data['year2'] = $data['year0'] - 2;

			$data['thismonth'] = date("m");
			$data['thisyear']  = date("y");

			$data['graphlabel0'] = $data['year0'];
			$data['graphlabel1'] = $data['year1'];
			$data['graphlabel2'] = $data['year2'];

			$data['startdate'] = $data['year2'] . "01-01";

			$data['startyearmonth'] = ($data['year2'] * 100) + 1;
			$data['startthisyearrmonth'] = ($data['year0'] * 100) + 1;
			$data['curyearmonth'] = ($data['year0'] * 100) + $data['thismonth'];

			$data['userDetail']=$this->site_model->getUserDetails($this->session->userdata('userid'));
			$userType=$data['userType']=$data['userDetail']['usertype'];
			$specific_search = $this->findPostedSpecificSearch();

			$search_key = $this->input->post("search")?$this->input->post("search"):"";

			$data = $this->setDataForSpecificSearch($specific_search);
			$userId = 0;
			$branchNo = 0;

			if (count($this->session->userdata('selectedUser')) > 0){
				$UserSes= $this->session->userdata('selectedUser');
				$userId = $UserSes["userid"];
			}


			if (count($this->session->userdata('selectedBranch')) > 0){
				$branchSes= $this->session->userdata('selectedBranch');
				$branchNo = $branchSes["branchno"];
			}

			if ($userType=="B"){
				$G_level="branch";
			}

			if ($userType=="B"){
				$G_level="Branch";
			}elseif (($userType=="A") && ($branchNo == 0) && ($userId == 0)){
				$G_level="Company";
			}elseif (($userType=="A") && ($branchNo > 0) && ($userId == 0)){
				$G_level="Branch";
				$G_branchno = $branchNo;
			}elseif (($userType=="A") && ($branchNo == 0) && ($userId > 0)){
				$G_level="User";
				$G_userId = $userId;
				$data['userDetail']=$this->site_model->getUserDetails($userId);
			}else{
				$G_level="User";
			}

			$count = $this->customer_model->getCustomerSalesAnalysisCount($data['startyearmonth'], $data['curyearmonth'], $search_key, $specific_search);

			/* Number of rows to be displayed on one page */
			$limit = 10;
			$totalrows = $count->totalrows;

			$data['page'] = intval($this->input->post("page"))?intval($this->input->post("page")):1;

			$offset = ($data['page']-1)*$limit;

			$data['totalrows'] = $totalrows;
			$data['pagecount'] = intval($totalrows/$limit)+1;
			if (!$alternateQuery) {
				$data['result']=$this->customer_model->getCustomerSalesAnalysis($data['userDetail']['repwhere'], $data['startyearmonth'], $data['curyearmonth'], $offset, $limit, $search_key, $specific_search);
			} else {
				$userId = $this->session->userdata('userid');
				$data['userDetail'] = $this->site_model->getUserDetails($userId);
				$offset = ($alternateQuery-1)*$limit;
				$data['resultold']=$this->customer_model->getCustomerSalesAnalysis($data['userDetail']['repwhere'], $data['startyearmonth'], $data['curyearmonth'], $offset, $limit, $search_key, $specific_search);
				print_r($data['userDetail']);
				$data['result']=$this->customer_model->getCustomerSalesAnalysis2($data['userDetail']['repwhere'], $data['startyearmonth'], $data['curyearmonth'], $offset, $limit, $search_key, $specific_search);
				echo $this->load->view('common/table', $data, true);
				exit;
			}

			$data['search'] = $this->input->post("search");

			$data['lnk'] = base_url().'customer/customerDetails/';
			$data['main_content']='customer_list';

			$this->load->view('customer/front_template', $data);
		}

		public function contacts() {
			if ($this->site_model->is_logged_in()==false){
				redirect('/');
			}
			setcookie($this->config->item('site_name').'_'.$this->session->userdata('userid').'_last_visited', current_url(), time() + (86400 * 365), "/"); // 86400 = 1 day
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
			$userType=$data['userType']=$data['userDetail']['usertype'];
			$specific_search = $this->findPostedSpecificSearch();

			$search_key = $this->input->post("search")?$this->input->post("search"):"";

			$data = $this->setDataForSpecificSearch($specific_search);
		$userId = 0;
		$branchNo = 0;

			if (count($this->session->userdata('selectedUser')) > 0){
			$UserSes= $this->session->userdata('selectedUser');
			$userId = $UserSes["userid"];
		}


		if (count($this->session->userdata('selectedBranch')) > 0){
			$branchSes= $this->session->userdata('selectedBranch');
			$branchNo = $branchSes["branchno"];
		}

		if ($userType=="B"){
		$G_level="branch";
		}

		if ($userType=="B"){
		$G_level="Branch";
		}elseif (($userType=="A") && ($branchNo == 0) && ($userId == 0)){
		$G_level="Company";
		}elseif (($userType=="A") && ($branchNo > 0) && ($userId == 0)){
		$G_level="Branch";
		$G_branchno = $branchNo;
		}elseif (($userType=="A") && ($branchNo == 0) && ($userId > 0)){

		$G_level="User";
		$G_userId = $userId;
		$data['userDetail']=$this->site_model->getUserDetails($userId);
		}else{
		$G_level="User";
		}


			$count = $this->customer_model->getCustomerSalesAnalysisCount($data['startyearmonth'], $data['curyearmonth'], $search_key, $specific_search);

			/* Number of rows to be displayed on one page */
			$limit = 10;
			$totalrows = $count->totalrows;

			$data['page'] = intval($this->input->post("page"))?intval($this->input->post("page")):1;

			$offset = ($data['page']-1)*$limit;

			$data['totalrows'] = $totalrows;
			$data['pagecount'] = intval($totalrows/$limit)+1;

			$data['result']=$this->customer_model->getCustomerSalesAnalysis($data['userDetail']['repwhere'], $data['startyearmonth'], $data['curyearmonth'], $offset, $limit, $search_key, $specific_search);

			$data['search'] = $this->input->post("search");

			$data['lnk'] = base_url().'customer/customerDetails/';
			$data['main_content']='customer_contact_list';
			//print_r($data);

			$this->load->view('customer/front_template', $data);
		}

		/* Function to set the data values for the posted search so that those can be set on the front end */
		public function setDataForSpecificSearch($specific_search) {
			$data = array();
			foreach ($specific_search as $key=>$ss){
				$index = str_replace(".", "_", $key);
				$data[$index] = $ss;
			}
			return $data;
		}

		/* Find the specific search posted */
		public function findPostedSpecificSearch() {
			$specific_search = array();
			$specific_search['C.account'] = $this->input->post("account")?$this->input->post("account"):"";
			$specific_search['C.name'] = $this->input->post("name")?$this->input->post("name"):"";
			$specific_search['C.postcode'] = $this->input->post("postcode")?$this->input->post("postcode"):"";
			$specific_search['CS.mquantity0'] = $this->input->post("qtymtd")?$this->input->post("qtymtd"):"";
			$specific_search['CS.msales0'] = $this->input->post("salesmtd")?$this->input->post("salesmtd"):"";
			$specific_search['CS.mmarginpc0'] = $this->input->post("marginmtdpc")?$this->input->post("marginmtdpc"):"";
			$specific_search['CS.yquantity0'] = $this->input->post("qtyytd")?$this->input->post("qtyytd"):"";
			$specific_search['CS.ysales0'] = $this->input->post("salesytd")?$this->input->post("salesytd"):"";
			$specific_search['CS.ymarginpc0'] = $this->input->post("marginytdpc")?$this->input->post("marginytdpc"):"";

			return $specific_search;
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
			if ($this->site_model->is_logged_in() == false)
			{
				redirect('/');
			}

			$data['isAdmin']    = $this->isAdmin;
			$data['account']    = base64_decode($account);
			$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
			$data['year']       = date("Y");
			$data['thismonth']  = date("m");
			$data['soytemp']    = $data['year']."-"."01-01";
			$data['soy']        = date('Y-m-d', strtotime($data['soytemp']));
			$data['somtemp']    = $data['year']."-".$data['thismonth']."-01";
			$data['som']        = date ('Y-m-d', strtotime($data['somtemp']));
			$data['date']       = date ('Y-m-d');

			$this->load->helper('cookie');
			if (isset($_COOKIE['twoyearsalesanalysischart']))
			{
				$data['twoyearsalesanalysischart'] = get_cookie('twoyearsalesanalysischart', true);
			}
			else
			{
				$data['twoyearsalesanalysischart'] = 0;
			}

			// --------------------------------------------------------------------------------------------------
			// CUSTOMER DETAILS
			// --------------------------------------------------------------------------------------------------

			$result = $this->customer_model->getCustomerDetails($data['account']);

			$this->session->set_userdata("myaccount", $data['account']);

			$this->load->model('users_model');
			$description = "Accessed account  ".$data['account']."-".$result['customername'];
			$this->users_model->savelog($description, $this->session->userdata('usertype'));

			foreach ($result as $k => $val)
			{
				$data[$k] = $val;
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
			$data['year3'] = $data['year0'] - 3;
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

			$data['yearstartmonth'] = $this->customer_model->getYearStartMonth();
			$start_month_delta = $data['yearstartmonth'] <= date('m') ? 11 + $data['yearstartmonth'] : $data['yearstartmonth'] - 1;

			// Initialise sales array

			$data['yearmonth']  = array();
			$data['monthnames'] = array();
			$data['sales']      = array();

			// Preload the year and month into an array so that we can make sure we load the sales against the correct row. Pad the month with leading 0 if needed. Had an example where
			// a rep started more recently that three years ago, and therefore there was less than 36 months. It was loading all these into the start of the array, rather than against the
			// appropriate row.

			$data['tmpyear']  = $data['year3']; //CR0001 $year3;
			$data['tmpmonth'] = 1; // CR0001 $thismonth + 1;

			for ($x = 0; $x < 48; $x++)
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

			$rows = $this->customer_model->getSalesAccount($data['account'], $data['startyearmonth'], $data['curyearmonth']);
			$proRataCoefficient = $this->common_model->getWorkingDayProRataCoefficient(date("Y/m/d"));

			foreach ($rows as $row)
			{
				$data['salessummaryyearmonth'] = $row['yearmonth'];
				$data['salessummarysales']     = $row['sales'];

				// For each data row, loop through the array and put the sales value in the correct place

				for ($x = 0; $x < 48; $x++)
				{
					if ($data['yearmonth'][$x] == $data['salessummaryyearmonth'])
					{
						$data['sales'][$x] = $data['salessummarysales']; // If the year month of the data matches the array, put the value in
					}
				}
			}

			$userType=$data['userType'] = $data['userDetail']['usertype'];
			$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));

			if ($userTyp == "B")
			{
				$G_level = "Branch";
			}
			elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0))
			{
				$G_level = "Company";
			}
			elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0))
			{
				$G_level = "Branch";
				$G_branchno = $branchNo;
			}
			elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0))
			{
				$G_level = "User";
				$G_userId = $userId;
				$data['userDetail'] = $this->site_model->getUserDetails($userId);
			}
			else
			{
				$G_level = "User";
			}

			$G_userid = $this->session->userdata("userid");
			$targetUserId = $G_userid;

			if (count($this->session->userdata('selectedBranch')) > 0)
			{
				$branchSes = $this->session->userdata('selectedBranch');
				$branchNo = $branchSes["branchno"];
			}

			$G_branchno = $branchNo;

			$userKpi = $this->site_model->userKpi($G_level, $G_branchno, $G_userid);
	
			$data = GetKpiDataForTwoYearVsTargetChart($userKpi, $data, $G_level);

			$data['year0'] = date("Y");
			$data['year1'] = $data['year0'] - 1;
			$data['year2'] = $data['year0'] - 2;
			$data['year3'] = $data['year0'] - 3;

			$data['tmpyear'] = $data['year3']; //CR0001 $year3;
			$data['tmpmonth'] = 1; // CR0001 $thismonth + 1;

			for ($x = 0; $x < 48; $x++)
			{
				$data['yearmonth'][$x] = ($data['tmpyear'] * 100) + $data['tmpmonth'];
			}

			// --------------------------------------------------------------------------------------------------
		// AGED TRANSACTIONS
		// --------------------------------------------------------------------------------------------------
			$data['month0sales'] = $data['sales'][35 + $data['thismonth']];
			$data['month0bal'] = 0;
			$data['month0col'] = 0;
			$data['month0due'] = 0;
			$data['month1sales'] = $data['sales'][35 + ($data['thismonth'] - 1)];
			$data['month1bal'] = 0;
			$data['month1col'] = 0;
			$data['month1due'] = 0;
			$data['month2sales'] = $data['sales'][35 + ($data['thismonth'] - 2)];
			$data['month2bal'] = 0;
			$data['month2col'] = 0;
			$data['month2due'] = 0;
			$data['month3sales'] = $data['sales'][35 + ($data['thismonth'] - 3)];
			$data['month3bal'] = 0;
			$data['month3col'] = 0;
			$data['month3due'] = 0;
			$data['month4sales'] = $data['sales'][35 + ($data['thismonth'] - 4)];
			$data['month4bal'] = 0;
			$data['month4col'] = 0;
			$data['month4due'] = 0;
			$data['month5sales'] = $data['sales'][35 + ($data['thismonth'] - 5)];
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

			$data['agedtransresult'] = $this->customer_model->getAgeTransaction($data['account']);
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

			for ($i = 1; $i <= 4; $i++)
			{
				$data['r_pac'.$i] = $this->makePacList($this->customer_model->getCustomerPACDetailsWithGroupByModified($data['account'], 'pac'.$i, $data['startthisyearrmonth'], $data['curyearmonth'], $proRataCoefficient));
			}

			// $data['r_product'] = $this->customer_model->getCustomerSAProduct($data['account'], $data['startthisyearrmonth'], $data['curyearmonth']);

			$data['edittargets'] = $data['userDetail']['edittargets'];
			$data['main_content'] = 'customer_details';

			// Customer Sales Analysis///

			$year0 = date("Y");
			$year1 = $year0 - 1;
			$year2 = $year0 - 2;

			$thismonth = date("m");

			$graphlabel0 = $year0;
			$graphlabel1 = $year1;
			$graphlabel2 = $year2;

			// --------------------------------------------------------------------------------------------------------------------------------------------------
			// 3 YEAR SALES CHART
			// --------------------------------------------------------------------------------------------------------------------------------------------------

			// Build the query string ... selecting only the fields we need from the customer sales table

			$lastfieldno = 24 + ($thismonth-1);
			$px=$this->customer_model->customergarphsales($lastfieldno,$data['account']);
			for ($x = 0; $x <= 35; $x++)
			{
				$sales[$x] = 0;
			}
			for ($x = 0;$x < count($px[0]);$x++)
			{

				$p=count($px[0])-$x-1;
				$sales[$x] = $px[0]["msales".$p];
			}

			$data['year0data'] = $this->site_model->GetYearData($data['sales'], 24 + $start_month_delta, 35 + $start_month_delta);
			$data["year0total"] = $this->site_model->GetYearTotal($data['sales'], 24 + $start_month_delta, 35 + $start_month_delta);
			$data["year0table"] = $this->site_model->GetYearTable($data['sales'], $data["year0total"], 24 + $start_month_delta, 35 + $start_month_delta);
	
			$data['year1data'] = $this->site_model->GetYearData($data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
			$data["year1total"] = $this->site_model->GetYearTotal($data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
			$data["year1table"] = $this->site_model->GetYearTable($data['sales'], $data["year1total"], 12 + $start_month_delta, 23 + $start_month_delta);

			$data['year2data'] = $this->site_model->GetYearData($data['sales'], $start_month_delta, 11 + $start_month_delta);
			$data["year2total"] = $this->site_model->GetYearTotal($data['sales'], $start_month_delta, 11 + $start_month_delta);
			$data["year2table"] = $this->site_model->GetYearTable($data['sales'], $data["year2total"], $start_month_delta, 11 + $start_month_delta);

			$data['year0ChartValues'] = $data['year0data'];
			$data['year1ChartValues'] = $data['year1data'];
			$data['year2ChartValues'] = $data['year2data'];
			$data['cumulativeYear0ChartValues'] = $this->site_model->GetCumulativeYearData($data['sales'], 24 + $start_month_delta, 35 + $start_month_delta);
			$data['cumulativeYear1ChartValues'] = $this->site_model->GetCumulativeYearData($data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
			$data['cumulativeYear2ChartValues'] = $this->site_model->GetCumulativeYearData($data['sales'], $start_month_delta, 11 + $start_month_delta);

			$data['salesTargetForLastThreeYear'] = $this->site_model->getCustomerSalesTargetForThisYear($data['sales'], "customer", null, $data['account']);
			$data['targetDataForCurrentYear'] = $this->site_model->GetTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);
			$data['cumulativeTargetDataForCurrentYear'] = $this->site_model->GetCumulativeTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);

			$this->load->view('customer/front_template', $data);
		}

		/* Function to create the group by and sum of the quantity sales and cost manually on the basis of the condition that the salesanalysis date is of this year-month */

		public function makePacList($rawCustomerPACDetailsArray)
		{
			$numerical_result = array();
			$i = 0;

			foreach ($rawCustomerPACDetailsArray as $ra)
			{
				foreach ($ra as $key => $value)
				{
					if ($ra[$key] == '')
					{
						$ra[$key] = '0.00';
					}

					$numerical_result[$i] = $ra;
				}

				$i++;
			}

			return $numerical_result;
		}

		public function makePacList_csv($rawCustomerPACDetailsArray)
		{
			return $rawCustomerPACDetailsArray;
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
			$data['account'] = base64_decode($account);
			$this->load->view('customer_details_quotes', $data);
		}

		/* Function to fetch the Customer Quotes in JSON format for the use of the DataTable */
		public function fetchCustomerQuotes($account)
		{
			$account = base64_decode($account);
			header('Content-Type: application/json');

			$draw = 1;
			$count = 0;
			$data = array();
			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $count,
				"recordsFiltered" => $count,
				"data"            => $data,
			);

			if ($this->site_model->is_logged_in() == false)
			{
				echo json_encode($return_array);
				exit;
			}

			$limit = 25;
			$start = isset($_POST["start"]) ? $_POST["start"] : 0;
			$length = isset($_POST["length"]) ? $_POST["length"] : $limit;
			$search = isset($_POST["search"]["value"]) ? $_POST["search"]["value"] : array("value" => null);
			$draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;

			$data['page'] = 1;

			$offset = ($data['page'] - 1) * $limit;
			$data['result'] = array();
			$dbResult = $this->customer_model->getCustomerQuotes($account, $search, $start, $limit);
			$recordsTotal = $this->customer_model->getCustomerQuotesCount($account);
			$recordsFiltered = $this->customer_model->getCustomerQuotesFilteredCount($account, $search);

			foreach ($dbResult as $row)
			{
				$row['orderno'] = "<a href='".base_url()."quotation/detail/".$row['orderno']."'>".$row['orderno']."</a>";
				$data['result'][] = $row;
			}

			$data['search'] = "";
			$data['lnk'] = base_url().'customer/customerDetails/';
			$data['pagecount'] = intval(sizeof($data['result']) / $limit) + 1;

			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $recordsTotal,
				"recordsFiltered" => $recordsFiltered,
				"data"            => $data['result'],
			);

			echo json_encode($return_array);
			exit;
		}

		public function fetchCustomerQuotesCsvExport($account, $searchTerm = null)
		{
			$account = base64_decode($account);
			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment;filename=\"MI-DAS Management Information Dashboard.csv\"");
			header("Cache-Control: max-age=0");

			$csv = "";

			if ($this->site_model->is_logged_in() == false)
			{
				echo $csv;
				exit;
			}

			$limit = null; //No limit.
			$start = 0;
			$csv .= "Quotation No., Cust Order No., Reason, Value, Date In, Follow Up, Expiry Date";

			$dbResult = $this->customer_model->getCustomerQuotes($account, $searchTerm, $start, $limit);
			foreach ($dbResult as $row)
			{
				$csv .=
				"\n" . $row['orderno'] . "," . $row['custorderno'] . "," . $row['quotereason']
				. "," . $row['quotevalue'] . "," . $row['datein'] . "," . $row['quotefolldate']
				. "," . $row['quoteexpidate'];
			}

			echo $csv;
			exit;
		}

		/**
		* customerDetailsOrders method
		*
		* @author		Virtual Employee PVT. LTD.
		* @Descrption	Return customer quotes Data
		* @Created Date     22-01-2016
		* @Updated Date
		*/

		function customerDetailsOrders($account)
		{
			$data['account'] = base64_decode($account);
			$this->load->view('customer_details_orders', $data);
		}

		/* Function to fetch the Customer Orders in JSON format for the use of the DataTable */
		public function fetchCustomerOrders($account)
		{
			$account = base64_decode($account);
			header('Content-Type: application/json');

			$draw = 1;
			$count = 0;
			$data = array();
			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $count,
				"recordsFiltered" => $count,
				"data"            => $data,
			);

			if ($this->site_model->is_logged_in() == false)
			{
				echo json_encode($return_array);
				exit;
			}

			$limit = 25;
			$start = isset($_POST["start"]) ? $_POST["start"] : 0;
			$length = isset($_POST["length"]) ? $_POST["length"] : $limit;
			$search = isset($_POST["search"]["value"]) ? $_POST["search"]["value"] : array("value" => null);
			$draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;

			$data['page'] = 1;

			$offset = ($data['page'] - 1) * $limit;
			$recordsTotal = count($this->customer_model->getCustomerOrders($account));
			$recordsFiltered = count($this->customer_model->getCustomerOrders($account, $search));
			$data['result'] = array();
			$dbResult = $this->customer_model->getCustomerOrders($account, $search, $start, $limit);

			foreach ($dbResult as $row)
			{
				$thisRow = $row;

				$thisRow['datein']        = date('d/m/Y', strtotime($row['datein']));
				$thisRow['headerdatereq'] = date('d/m/Y', strtotime($row['headerdatereq']));
				$thisRow['datereq']       = date('d/m/Y', strtotime($row['datereq']));

				$thisRow['orderheading']  = "Order No: ".$row['orderno']." Entered: ".$thisRow['datein']." Required: ".$thisRow['headerdatereq']. " Customer Ord No: ".$row['custorderno'];

				$thisRow['nettprice']     = number_format($row['unitprice'] * (100 - $row['discount1']) / 100 * (100 - $row['discount2']) / 100, 2);

				$data['result'][] = $thisRow;
			}

			$data['search'] = "";
			$data['lnk'] = base_url().'customer/customerDetails/';
			$data['pagecount'] = intval(sizeof($data['result']) / $limit) + 1;

			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $recordsTotal,
				"recordsFiltered" => $recordsFiltered,
				"data"            => $data['result'],
			);

			echo json_encode($return_array);
			exit;
		}

		public function fetchCustomerOrdersCsvExport($account, $searchTerm = null)
		{
			$account = base64_decode($account);
			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment;filename=\"MI-DAS Management Information Dashboard.csv\"");
			header("Cache-Control: max-age=0");

			$csv = "";

			if ($this->site_model->is_logged_in() == false)
			{
				echo $csv;
				exit;
			}

			$limit = null; //No limit.
			$start = 0;
			$result = array();

			$dbResult = $this->customer_model->getCustomerOrders($account, $searchTerm, $start, $limit);
			foreach ($dbResult as $row)
			{
				$thisRow = $row;

				$thisRow['datein']        = date('d/m/Y', strtotime($row['datein']));
				$thisRow['headerdatereq'] = date('d/m/Y', strtotime($row['headerdatereq']));
				$thisRow['datereq']       = date('d/m/Y', strtotime($row['datereq']));

				$thisRow['orderheading']  = "Order No: ".$row['orderno']." Entered: ".$thisRow['datein']." Required: ".$thisRow['headerdatereq']. " Customer Ord No: ".$row['custorderno'];

				$thisRow['nettprice']     = number_format($row['unitprice'] * (100 - $row['discount1']) / 100 * (100 - $row['discount2']) / 100, 2);

				$result[] = $thisRow;
			}

			$csv .= "Order, Date In, Product, Description, Quantity, Unit Price, Discount 1%, Discount 2%, Nett Price, Value, Required, Status";

			foreach ($result as $row)
			{
				$csv .=
				"\n" . $row['orderheading'] . "," . $row['datein'] . "," . $row['prodcode']
				. "," . $row['fulldesc'] . "," . $row['quantity'] . "," . $row['unitprice']
				. "," . $row['discount1'] . "," . $row['discount2'] . "," . $row['nettprice']
				. "," . $row['sales'] . "," . $row['datereq'] . "," . $row['status'];
			}

			echo $csv;
			exit;
		}

		/* Function to fetch the Customer Sales Analysis Orders in JSON format for the use of the DataTable */
		public function fetchCustomerSalesAnalysisOrders($account)
		{
			$account = base64_decode($account);
			header('Content-Type: application/json');

			$draw = 1;
			$count = 0;
			$data = array();
			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $count,
				"recordsFiltered" => $count,
				"data"            => $data,
			);

			if ($this->site_model->is_logged_in() == false)
			{
				echo json_encode($return_array);
				exit;
			}

			$limit = 25;
			$start = isset($_POST["start"]) ? $_POST["start"] : 0;
			$length = isset($_POST["length"]) ? $_POST["length"] : $limit;
			$search = isset($_POST["search"]["value"]) ? $_POST["search"]["value"] : array("value" => null);
			$draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;

			$data['page'] = 1;

			$offset = ($data['page'] - 1) * $limit;
			$data['result'] = array();
			$data['result'] = $this->customer_model->getCustomerSalesAnalysisOrders($account, $search, $start, $limit);
			$recordsTotal = $this->customer_model->getCustomerSalesAnalysisOrdersCount($account);
			$recordsFiltered = $this->customer_model->getCustomerSalesAnalysisOrdersFilteredCount($account, $search);

			$data['search'] = "";
			$data['lnk'] = base_url().'customer/customerDetails/';
			$data['pagecount'] = intval(sizeof($data['result']) / $limit) + 1;

			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $recordsTotal,
				"recordsFiltered" => $recordsFiltered,
				"data"            => $data['result'],
			);

			echo json_encode($return_array);
			exit;
		}

		public function fetchCustomerSalesAnalysisOrdersCsvExport($account, $searchTerm = null)
		{
			$account = base64_decode($account);
			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment;filename=\"Sales Analysis Orders.csv\"");
			header("Cache-Control: max-age=0");

			$csv = "";

			if ($this->site_model->is_logged_in() == false)
			{
				echo $csv;
				exit;
			}

			$limit = null; //No limit.
			$start = 0;
			$csv .= "Order No, Date, Product, Description, Quantity, Value, Invoice";

			$dbResult = $this->customer_model->getCustomerSalesAnalysisOrders($account, $searchTerm, $start, $limit);
			foreach ($dbResult as $row)
			{
				$csv .=
				"\n" . $row['orderno'] . "," . $row['date'] . "," . $row['prodcode']
				. "," . $row['description'] . "," . $row['quantity'] . "," . $row['sales']
				. "," . $row['invoiceno'];
			}

			echo $csv;
			exit;
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
			$data['account'] = base64_decode($account);
			$userSales = $this->customer_model->getCustomerSales($data['account']);
			$data['t_product'] = $this->customer_model->getCustomerTermsProduct($data['account']);
			$terms['terms1code'] = $userSales['terms1code'];
			$terms['terms2code'] = $userSales['terms2code'];
			$terms['terms3code'] = $userSales['terms3code'];
			$terms['terms4code'] = $userSales['terms4code'];
			$terms['terms5code'] = $userSales['terms5code'];
			$data['t_group'] = $this->customer_model->getCustomerTermsGroup($data['account']);
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
			$data['account'] = base64_decode($account);
			$result = $this->customer_model->getCustomerDetails($data['account']);

			foreach ($result as $k => $val)
			{
				$data[$k] = $val;
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

		function customerPACTargets($account)
		{
			$result = array();
			$result['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
			$result['account'] = $account;
			$result['mainUserEditAccess'] = $result['userDetail']['edittargets'];

			$this->load->view('customer_details_targets', $result);
		}

		function customerContacts($account)
		{
			$result = array();
			$result['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
			$result['account'] = base64_decode($account);
			$result['mainUserEditAccess'] = $result['userDetail']['edittargets'];
			$this->load->view('customer_contact_list', $result);
		}

		function customerPACSalesVsTarget($yearmonth = null, $account)
		{
			$account = base64_decode($account);
			$yearmonthposted = $yearmonth;
			if ($this->site_model->is_logged_in()==false) {
				redirect('/');
			}

			$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));

			$userType = $data['userType'] = $data['userDetail']['usertype'];
			$userId = 0;
			$branchNo = 0;
			$headerUserId=0;

			if (count($this->session->userdata('selectedUser')) > 0){
				$UserSes = $this->session->userdata('selectedUser');
				$userId = $UserSes["userid"];
			}
			$headerUserId = $userId;

			if (count($this->session->userdata('selectedBranch')) > 0){
				$branchSes = $this->session->userdata('selectedBranch');
				$branchNo = $branchSes["branchno"];
			}

			if ($userType=="B"){
				$G_level = "branch";
			}

			if ($userType=="B"){
				$G_level = "Branch";
			} elseif (($userType=="A") && ($branchNo == 0) && ($userId == 0)){
				$G_level = "Company";
			} elseif (($userType=="A") && ($branchNo > 0) && ($userId == 0)){
				$G_level = "Branch";
				$G_branchno = $branchNo;
			} elseif (($userType=="A") && ($branchNo == 0) && ($userId > 0)){
				$G_level = "User";
				$G_userId = $userId;
				$data['userDetail'] = $this->site_model->getUserDetails($userId);
			} else {
				$G_level = "User";
			}

			$repclause = $data["userDetail"]["repclause"];
			$data['pac1salestarget'] = $this->customer_model->getPac1SalesTargetDashboard($G_level,$userId,$branchNo,$repclause,$account, $yearmonthposted);
			$data['getSalesTotalMonthWise'] = $this->site_model->getCustomerSalesTotalMonthWise($G_level,$targetUserId,$branchNo,$repclause, $account);
			$data["account"] = $account;
			$this->load->view('customersalestargetdata', $data);
		}

		/**
		* getCustomerDetailsBalance method
		*
		* @author		Virtual Employee PVT. LTD.
		* @Descrption	Return customer balance tab Data
		* @Created Date     22-01-2016
		* @Updated Date
		*/
		function customerDetailsBalance($account)
		{
			$data['account'] = base64_decode($account);
			$result = $this->customer_model->getCustomerDetails($data['account']);

			foreach ($result as $k => $val)
			{
				$data[$k] = $val;
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

			$data['daysinmonth'] = date("t", strtotime(date('Y-m-d')));
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

			$rows = $this->customer_model->getSalesAccount($data['account'], $data['startyearmonth'], $data['curyearmonth']);

			foreach ($rows as $row)
			{
				$data['salessummaryyearmonth'] = $row['yearmonth'];
				$data['salessummarysales']     = $row['sales'];

				// For each data row, loop through the array and put the sales value in the correct place

				for ($x = 0; $x <= 36; $x++)
				{
					if ($data['yearmonth'][$x] == $data['salessummaryyearmonth']) $data['sales'][$x] = $data['salessummarysales']; // If the year month of the data matches the array, put the value in
				}
			}

			// Build the $year2data string for the chart

			$data['year2data'] = "[";

			$y = 0;

			for ($x = 0; $x <= 11; $x++)
			{
				$data['year2data'].= "[$y,". $data['sales'][$x]."]";

				if ($x != 11) $data['year2data'].= ",";

				$y++;
			}

			$data['year2data'].= "]";

			// Build the $year1data string for the chart

			$data['year1data'] = "[";

			$y = 0;

			for ($x = 12; $x <= 23; $x++)
			{
				$data['year1data'].= "[$y,$sales[$x]]";

				if ($x != 23) $data['year1data'].= ",";

				$y++;
			}

			$data['year1data'].= "]";

			// Build the $year0data string for the chart

			$data['year0data'] = "[";

			$y = 0;

			for ($x = 24; $x <= 35; $x++)
			{
				$data['year0data'].= "[$y,$sales[$x]]";

				if ($x != 35) $data['year0data'].= ",";

				$y++;
			}

			$data['year0data'].= "]";

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
				$data['daysinmonth'] = date("t", strtotime($data['agedmonthstartdate'][$x]));
				$data['temp'] = $data['year']."-".$data['monthno']."-".$data['daysinmonth'];
				$data['agedmonthenddate'][$x] = date('Y-m-d', strtotime($data['temp']));

				$data['monthno'] = $data['monthno'] - 1;

				if ($data['monthno'] == 0)
				{
					$data['monthno'] = 12;
					$data['year'] = $data['year'] - 1;
				}
			}

			$data['monthlisting'][0] = $this->customer_model->getCustomMonthDataForCustomer($data['account'], $data['agedmonthstartdate'][0], $data['agedmonthenddate'][0]);
			$data['monthlisting'][1] = $this->customer_model->getCustomMonthDataForCustomer($data['account'], $data['agedmonthstartdate'][1], $data['agedmonthenddate'][1]);
			$data['monthlisting'][2] = $this->customer_model->getAgeMonthList($data['account'], $data['agedmonthstartdate'][2], $data['agedmonthenddate'][2]);
			$data['monthlisting'][3] = $this->customer_model->getAgeMonthList($data['account'], $data['agedmonthstartdate'][3], $data['agedmonthenddate'][3]);
			$data['monthlisting'][4] = $this->customer_model->getAgeMonthList($data['account'], $data['agedmonthstartdate'][4], $data['agedmonthenddate'][4]);
			$data['monthlisting'][5] = $this->customer_model->getAgeMonthList($data['account'], $data['agedmonthstartdate'][5], $data['agedmonthenddate'][5]);

			$this->load->view('customer_details_balances', $data);
		}

		/* Function to fetch the Customer Details Balance in JSON format for the use of the DataTable */
		public function fetchCustomerDetailsBalance($account)
		{
			$account = base64_decode($account);
			header('Content-Type: application/json');

			$draw = 1;
			$count = 0;
			$data = array();
			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $count,
				"recordsFiltered" => $count,
				"data"            => $data,
			);

			if ($this->site_model->is_logged_in() == false)
			{
				echo json_encode($return_array);
				exit;
			}

			$limit = 10;
			$start = isset($_POST["start"]) ? $_POST["start"] : 0;
			$length = isset($_POST["length"]) ? $_POST["length"] : $limit;
			$search = isset($_POST["search"]["value"]) ? $_POST["search"]["value"] : array("value" => null);
			$draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;

			$data['page'] = 1;

			$offset = ($data['page'] - 1) * $limit;
			$data['result'] = $this->customer_model->getAgeTransaction($account, $search, $start, $limit);
			$recordsTotal = $this->customer_model->getAgeTransactionCount($account);
			$recordsFiltered = $this->customer_model->getAgeTransactionFilteredCount($account, $search);

			$data['search'] = "";
			$data['lnk'] = base_url().'customer/customerDetails/';
			$data['pagecount'] = intval(sizeof($data['result']) / $limit) + 1;

			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $recordsTotal,
				"recordsFiltered" => $recordsFiltered,
				"data"            => $data['result'],
			);

			echo json_encode($return_array);
			exit;
		}

		public function fetchCustomerDetailsBalanceCsvExport($account, $searchTerm = null)
		{
			$account = base64_decode($account);
			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment;filename=\"Customer Balances Detailed.csv\"");
			header("Cache-Control: max-age=0");

			$csv = "";

			if ($this->site_model->is_logged_in() == false)
			{
				echo $csv;
				exit;
			}

			$limit = null; //No limit.
			$start = 0;
			$csv .= "Doc Date, Doc No, Customer Ref, Other Ref, Status, Type, Due Date, Total, Paid, Outstanding, Collectable, Overdue";

			$dbResult = $this->customer_model->getAgeTransaction($account, $searchTerm, $start, $limit);
			foreach ($dbResult as $row)
			{
				$csv .=
				"\n" . $row['docdate'] . "," . $row['docnumber'] . "," . $row['custref']
				. "," . $row['otherref'] . "," . $row['docstatus'] . "," . $row['doctype']
				. "," . $row['duedate'] . "," . $row['totalamount'] . "," . $row['paidamount']
				. "," . $row['outstandamount'] . "," . $row['collectamount'] . "," . $row['overdueamount'];
			}

			echo $csv;
			exit;
		}

		public function saveInternalText(){
			if (!$this->canSeeMargins) {
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

			if (!empty($table) && !empty($column) && !empty($keycolumn) && !empty($keydata) && $newVal>=0 && $this->canEditTerms)
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

			if (!empty($table) && !empty($column) && !empty($keycolumn) && !empty($keydata) && $newVal>=0 && $this->canEditTerms)
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

			foreach ($result as $row){
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
				$year2data.= "[$y,$sales[$x]]";
				if ($x != 11) $year2data.= ",";
				$y = $y + 1;
			}
			$year2data.= "]";

			// Build the $year1data string for the chart

			$year1data = "[";
			$y = 0;
			for ($x = 12; $x <= 23; $x++)
			{
				$year1data.= "[$y,$sales[$x]]";
				if ($x != 23) $year1data.= ",";
				$y = $y + 1;
			}
			$year1data.= "]";

			// Build the $year0data string for the chart

			$year0data = "[";
			$y = 0;
			for ($x = 24; $x <= 35; $x++)
			{
				$year0data.= "[$y,$sales[$x]]";
				if ($x != 35) $year0data.= ",";
				$y = $y + 1;
			}
			$year0data.= "]";

			$dataarray = array();
			$dataarray[0] = $year0data;
			$dataarray[1] = $year1data;
			$dataarray[2] = $year2data;

			echo json_encode($dataarray);
		}

		public function drawCustomerProductsDetails($account, $startthisyearrmonth, $curyearmonth) {
			$r_product=$this->customer_model->getCustomerSAProduct($account, $startthisyearrmonth, $curyearmonth);
			foreach ($r_product as $row) {
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
				echo "<td><a class='iframe123' href='http://wikipedia.com'>$description</a></td>";
				echo "<td>$qtymtd</td>";
				echo "<td>$salesmtd</td>";
				if ($this->canSeeMargins) {
					echo "<td>$marginmtdpc</td>";
				}
				echo "<td>$qtyytd</td>";
				echo "<td>$salesytd</td>";
				if ($this->canSeeMargins) {
					echo "<td>$marginytdpc</td>";
				}
				echo "</tr>";
			}
			exit;
		}


		/* Function to fetch the customer sales analysis in json format for the use of the DataTable */
		public function fetchCustomerSalesAnalysis()
		{
			$this->urlFn = "customer_list";
			header("Content-Type: application/json");
			$draw = 1;
			$count = 0;
			$data = array();

			$return_array = array
			(
				'draw'            => $draw,
				'recordsTotal'    => $count,
				'recordsFiltered' => $count,
				'data'            => $data,
				'with'            => array('columns' => $_POST['columns']),
			);

			if ($this->site_model->is_logged_in() == false)
			{
				echo json_encode($return_array);
				exit;
			}

			$data = $this->passDetails();

			/* Number of rows to be displayed on one page */
			$limit = 10;

			$start = isset($_POST['start']) ? $_POST['start'] : 0;
			$length = isset($_POST['length']) ? $_POST['length'] : $limit;
			$search_key = isset($_POST['search']['value']) ? $_POST['search']['value'] : "";
			$draw = isset($_POST['draw']) ? $_POST['draw'] : 1;

			$specific_search = $this->findPostedSpecificSearchAndMakec();
			$specific_order = $this->findPostedOrder();

			$totalCount = $this->customer_model->getCustomerSalesAnalysisCount($data['startyearmonth'], $data['curyearmonth']);
			$recordsTotal = $totalCount->totalrows;

			$filteredCount = $this->customer_model->getCustomerSalesAnalysisCount($data['startyearmonth'], $data['curyearmonth'], $search_key, $specific_search);
			$recordsFiltered = $filteredCount->totalrows;

			$result = $this->customer_model->getCustomerSalesAnalysisDataTable($data['startyearmonth'], $data['curyearmonth'], $start, $length, $search_key, $specific_search, $specific_order, $this->canSeeMargins);

			$return_array = array
			(
				'draw'            => $draw,
				'recordsTotal'    => $recordsTotal,
				'recordsFiltered' => $recordsFiltered,
				'data'            => $result,
				'with'            => array('columns' => $_POST['columns']),
			);

			echo json_encode($return_array);
			exit;
		}

		/* Function to fetch the customer contacts in json format for the use of the DataTable */
		public function fetchCustomerContacts($account)
		{
			$account = base64_decode($account);
			header('Content-Type: application/json');
			$draw = 1;
			$count = 0;
			$data = array();

			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $count,
				"recordsFiltered" => $count,
				"data"            => $data,
			);

			if ($this->site_model->is_logged_in() == false)
			{
				echo json_encode($return_array);
				exit;
			}

			if (!!$_POST["order"][0])
			{
				$order = $_POST["order"][0];
			}
			else
			{
				$order = array
				(
					"column" => 0,
					"dir"    => "asc",
				);
			}

			$limit = 10;
			$start = isset($_POST["start"]) ? $_POST["start"] : 0;
			$length = isset($_POST["length"]) ? $_POST["length"] : $limit;
			$search = isset($_POST["search"]["value"]) ? $_POST["search"]["value"] : array("value" => null);
			$draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;

			$data['page'] = 1;

			$offset = ($data['page']-1)*$limit;
			$recordsFiltered = count($this->customer_model->customerContacts($account, $search, 0, null, $order));
			$data['result'] = $this->customer_model->customerContacts($account, $search, $start, $limit, $order);
			$data['search'] = "";
			$data['lnk'] = base_url().'customer/customerDetails/';
			$data['pagecount'] = intval(sizeof($data['result'])/$limit)+1;

			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $limit,
				"recordsFiltered" => $recordsFiltered,
				"data"            => $data['result'],
			);

			echo json_encode($return_array);
			exit;
		}

		/* Display the customer contact detail page. */
		public function contactdetails($contactno) {
			if ($this->site_model->is_logged_in()==false){
				redirect('/');
			}
			setcookie($this->config->item('site_name').'_'.$this->session->userdata('userid').'_last_visited', current_url(), time() + (86400 * 365), "/"); // 86400 = 1 day

			$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
			$userType = $data['userType']=$data['userDetail']['usertype'];

			$data["contactDetail"] = $this->customer_model->fetchContactDetail($contactno);

			$this->load->view('customer_contact_detail', $data);
		}


		/* Function to fetch the customer product sales analysis in json format for the use of the DataTable */
		public function fetchCustomerProductSalesAnalysis()
		{
			header('Content-Type: application/json');
			$draw = 1;
			$count = 0;
			$data = array();
			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $count,
				"recordsFiltered" => $count,
				"data"            => $data,
				"with"            => array("columns" => $_POST["columns"]),
			);

			if ($this->site_model->is_logged_in() == false)
			{
				echo json_encode($return_array);
				exit;
			}

			$data = $this->passDetails();

			/* Number of rows to be displayed on one page */
			$limit = 10;

			$start = isset($_POST["start"]) ? $_POST["start"] : 0;
			$length = isset($_POST["length"]) ? $_POST["length"] : $limit;
			$search = isset($_POST["search"]) ? $_POST["search"] : array();
			$draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;

			$specific_order = $this->findPostedOrderProductSales();
			$search_key = $search['value'];

			$account = $this->session->userdata("myaccount");
			$specific_search = $this->findPostedSpecificSearchAndMake();

			$count = $this->customer_model->getCustomerProductSalesAnalysisCount($account, $search_key, $specific_search);

			$totalrows = $count;
			//$specific_search = $this->findPostedSpecificSearchAndMake();

			$data['result'] = $this->customer_model->getCustomerProductSalesAnalysis($account, $specific_order, $start, $length, $search_key, $specific_search);

			$return_array = array
			(
				"draw"            => $draw,
				"recordsTotal"    => $totalrows,
				"recordsFiltered" => $totalrows,
				"data"            => $data['result'],
				"with"            => array("columns" => $_POST["columns"]),
			);

			echo json_encode($return_array);
			exit;
		}

		/* function to create the details to be passed to the export search function. */

		public function passDetails() {
			$data = array();
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
			$userType=$data['userType']=$data['userDetail']['usertype'];
			$userId = 0;
				$branchNo = 0;

					if (count($this->session->userdata('selectedUser')) > 0){
					$UserSes= $this->session->userdata('selectedUser');
					$userId = $UserSes["userid"];
				}


				if (count($this->session->userdata('selectedBranch')) > 0){
					$branchSes= $this->session->userdata('selectedBranch');
					$branchNo = $branchSes["branchno"];
				}

			if ($userType=="B"){
				$G_level="branch";
			}

			if ($userType=="B"){
				$G_level="Branch";
			}elseif (($userType=="A") && ($branchNo == 0) && ($userId == 0)){
				$G_level="Company";
			}elseif (($userType=="A") && ($branchNo > 0) && ($userId == 0)){
				$G_level="Branch";
				$G_branchno = $branchNo;
			}elseif (($userType=="A") && ($branchNo == 0) && ($userId > 0)){

				$G_level="User";
				$G_userId = $userId;
				$data['userDetail']=$this->site_model->getUserDetails($userId);
			}else{
				$G_level="User";
			}
					return $data;
		}

		/* function to download CSV format */
		public function csv_export()
		{
			$search = array();

			for ($i = 3; $i <= 12; $i++)
			{
				$search[] = $this->uri->segment($i);
			}

			$specific_search_keys = $this->getSpecificSearchKeys();
			$data = $this->passDetails();
			$csvOutput = $this->customer_model->csv_export($data['userDetail']['repwhere'], $data['startyearmonth'], $data['curyearmonth'], $search[0], $search, $specific_search_keys);

			echo $csvOutput;
			exit();
		}

		/* function to download XLSX format */
		public function excel_export()
		{
			$search = array();

			for ($i = 3; $i <= 15; $i++)
			{
				$search[] = $this->uri->segment($i);
			}

			$specific_search_keys = $this->getSpecificSearchKeys();
			$data = $this->passDetails();

			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment;filename=\"MI-DAS-Customer.csv\"");
			header("Cache-Control: max-age=0");

			$xlsOutput = $this->customer_model->csv_export($data['userDetail']['repwhere'], $data['startyearmonth'], $data['curyearmonth'], $search[0], $search, $specific_search_keys, $this->canSeeMargins);

			echo $xlsOutput;
			exit();
		}

		public function prd_excel_export($account)
		{
			$data['account'] = base64_decode($account);
			$ind = strtolower($this->uri->segment(4));

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

			foreach ($result as $k=>$val){
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
			foreach ($rows as $row){
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
				$data['year2data'].= "[$y,". $data['sales'][$x]."]";
				if ($x != 11) $data['year2data'].= ",";
				$y = $y + 1;
			}
			$data['year2data'].= "]";

			// Build the $year1data string for the chart

			$data['year1data'] = "[";
			$y = 0;
			for ($x = 12; $x <= 23; $x++)
			{
				$data['year1data'].= "[$y,".$data['sales'][$x]."]";
				if ($x != 23) $data['year1data'].= ",";
				$y = $y + 1;
			}
			$data['year1data'].= "]";

			// Build the $year0data string for the chart

			$data['year0data'] = "[";
			$y = 0;
			for ($x = 24; $x <= 35; $x++)
			{
				$data['year0data'].= "[$y,".$data['sales'][$x]."]";
				if ($x != 35) $data['year0data'].= ",";
				$y = $y + 1;
			}
			$data['year0data'].= "]";

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

			$data['agedtransresult'] = $this->customer_model->getAgeTransaction($data['account']);
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
				header("Content-type: text/x-csv");
			header("Content-Disposition: attachment; filename=MI-DAS-CUSTOMER-".$this->uri->segment(4).".csv");
			header("Cache-Control: max-age=0");

			$xlsOutput =$this->customer_model->prd_csv_export_mycustom($data['account'], $ind, $data['startthisyearrmonth'], $data['curyearmonth']);


			//$xlsOutput = $this->customer_model->prd_csv_export($data['userDetail']['repwhere'], $data['startyearmonth'], $data['curyearmonth'], $search[0], $search, $specific_search_keys);
			echo $xlsOutput;
			exit();
		}
		/* function to download CSV format for customer products */
		public function csv_export_customer_products() {
			$account = $this->uri->segment(3);
			$search = $this->uri->segment(4);
			$data = $this->passDetails();

			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment; filename=customers.csv");
			$csvOutput = $this->customer_model->csv_export_customer_products($account, $search);
			echo $csvOutput;
			exit();
		}

		/* function to download XLSX format for customer products */
		public function excel_export_customer_products()
		{
			$account = base64_decode($this->uri->segment(3));
			$search = $this->uri->segment(4);
			$data = $this->passDetails();

			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment; filename=\"MI-DAS-Customer-Product.csv\"");
			header("Cache-Control: max-age=0");
			$xlsOutput = $this->customer_model->csv_export_customer_products($account, $search);
			echo $xlsOutput;
			exit();
		}

		/* function to download XLSX format for customer contacts */
		public function excel_export_contacts($account, $search)
		{
			$account = base64_decode($account);

			if (!!$_POST['order'][0])
			{
				$order = $_POST['order'][0];
			}
			else
			{
				$order = array('column' => 0, 'dir' => "asc");
			}

			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment; filename=\"MI-DAS-Customer-Product.csv\"");
			header("Cache-Control: max-age=0");
			$xlsOutput = $this->customer_model->csv_export_customer_contacts($account, $search, $order);
			echo $xlsOutput;
			exit();
		}

		/* function to download XLSX format for customer products */
		public function excel_export_sales_target()
		{
			$account = base64_decode($this->uri->segment(3));
			$search = $this->uri->segment(4);
			$data = $this->passDetails();

			header("Content-type: text/x-csv");
			header("Content-Disposition: attachment; filename=\"MI-DAS-Sales-Target.csv\"");
			header("Cache-Control: max-age=0");

			/* data for query  */
			$yearmonthposted = $yearmonth;
			if ($this->site_model->is_logged_in()==false) {
				redirect('/');
			}

			$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));

			$userType = $data['userType'] = $data['userDetail']['usertype'];
			$userId = 0;
			$branchNo = 0;
			$headerUserId=0;

			if (count($this->session->userdata('selectedUser')) > 0){
				$UserSes = $this->session->userdata('selectedUser');
				$userId = $UserSes["userid"];
			}
			$headerUserId = $userId;

			if (count($this->session->userdata('selectedBranch')) > 0){
				$branchSes = $this->session->userdata('selectedBranch');
				$branchNo = $branchSes["branchno"];
			}

			if ($userType=="B"){
				$G_level = "branch";
			}

			if ($userType=="B"){
				$G_level = "Branch";
			} elseif (($userType=="A") && ($branchNo == 0) && ($userId == 0)){
				$G_level = "Company";
			} elseif (($userType=="A") && ($branchNo > 0) && ($userId == 0)){
				$G_level = "Branch";
				$G_branchno = $branchNo;
			} elseif (($userType=="A") && ($branchNo == 0) && ($userId > 0)){
				$G_level = "User";
				$G_userId = $userId;
				$data['userDetail'] = $this->site_model->getUserDetails($userId);
			} else {
				$G_level = "User";
			}

			$repclause = $data["userDetail"]["repclause"];
			/* end data for query */

			$xlsOutput = $this->customer_model->csv_export_sales_target($search, $specific_search="", $G_level,$userid,$branchNo,$repclause, $account, $yearmonthposted);
			echo $xlsOutput;
			exit();
		}

		/* Function to get the posted order and it's direction. this function will return order by column name that can be used in query directly and the direction. */
		public function findPostedOrder() {
			$posted_order = $_POST['order'];
			$column_index = -1;
			$order = array(
				'by' =>	$search_keys[0],
				'dir' =>	'asc'
			);

			if (isset($posted_order[0]['column']) && isset($posted_order[0]['dir'])) {
				$column_index = $posted_order[0]['column'];

			}

			$search_keys = $this->getSpecificSearchKeys();
			if ($column_index>=0) {
				$order = array(
					'by' =>	$search_keys[$column_index],
					'dir' =>	$posted_order[0]['dir']
				);
			} else {
				$order = array(
					'by' =>	$search_keys[0],
					'dir' =>	'asc'
				);
			}

			return $order;
		}

		/* Function to get the posted order and it's direction for the customer product sales table. this function will return order by column name that can be used in query directly and the direction. */
		public function findPostedOrderProductSales() {
			$posted_order = $_POST['order'];
			$column_index = -1;
			$order = array(
				'by' =>	$search_keys[0],
				'dir' =>	'asc'
			);

			if (isset($posted_order[0]['column']) && isset($posted_order[0]['dir'])) {
				$column_index = $posted_order[0]['column'];
			}

			$search_keys = $this->getSpecificSearchKeysProductSales();


			if ($column_index>=0) {

				$order = array(
					'by' =>	$search_keys[$column_index],
					'dir' =>	$posted_order[0]['dir']
				);
			} else {
				$order = array(
					'by' =>	$search_keys[0],
					'dir' =>	'asc'
				);
			}

			return $order;
		}

		/* Function to get the specific search and make the searchable array */
		public function findPostedSpecificSearchAndMakec()
		{
			$posted_columns = $_POST['columns'];
			$search_keys = $this->getSpecificSearchKeys();
			$search = array();

			foreach ($posted_columns as $key => $col) 
			{
				$search[$search_keys[$key]] = $col['search']['value'];
			}

			return $search;
		}

		public function findPostedSpecificSearchAndMake() {
			$posted_columns = $_POST['columns'];
			$search_keys = $this->getSpecificSearchKeysProductSales();
			$search = array();
			foreach ($posted_columns as $key=>$col) {
				$search[$search_keys[$key]] = $col['search']['value'];
			}

			return $search;
		}

		/* Function to get the keys for specific search */
		public function getSpecificSearchKeys()
		{
			$seeMargins = $this->canSeeMargins;

			if (!$seeMargins)
			{
				$search_keys = array('C.account', 'C.name', 'CS.ysales0', 'YoY1Sales', 'diff_percent', 'YoY2Sales', 'CS.msales0', 'C.postcode', 'C.userdef1', 'C.repcode');
			}
			else
			{
				$search_keys = array('C.account', 'C.name', 'CS.ysales0', 'YoY1Sales', 'ysales1', 'diff_percent', 'YoY2Sales', 'ysales2', 'CS.msales0', 'CS.mmarginpc0', 'CS.ymarginpc0', 'C.postcode', 'C.userdef1', 'C.repcode');
			}

			return $search_keys;
		}

		public function getSpecificSearchKeysProductSales() {
			//'p.code,p.pac4code as cpac4, p.description, sum(s.msales0) as salemtd, sum(s.mquantity0) as quantitymtd, (sum(s.mmargin0)/sum(s.msales0))*100 as marginpcmtd, sum(s.ysales0) as ysalesytd, sum(s.yquantity0) as yquantityytd, (sum(s.ymargin0)/sum(s.ysales0))*100 as ymarginpcytd
			$search_keys = array('p.code', 'cpac4','p.description', 'sales_ytd', 'qty_ytd', 'sales_diff', 'qty_diff', 'sales_y1', 'qty_y1', 'sales_y2', 'qty_y2', 'sales_mtd', 'qty_mtd', 'sales_diff', 'qty_diff');
			return $search_keys;
		}

		// 04-04-2017 Customer Graph Virtual //
		public function customergraph()
		{
			$account  = $this->uri->segment(4);
			$level = $this->uri->segment(6);
			$code  = $this->uri->segment(8);

			$data["year0"]=$year0 = date("Y");
			$data["year1"]=$year1 = $year0 - 1;
			$data["year2"]=$year2 = $year0 - 2;

			$data['yearstartmonth'] = $this->customer_model->getYearStartMonth();
			$start_month_delta = $data['yearstartmonth'] <= date('m') ? 11 + $data['yearstartmonth'] : $data['yearstartmonth'] - 1;

			$this->load->helper('cookie');
			if (isset($_COOKIE['threeyearsalesanalysispacchart']))
			{
				$data['threeyearsalesanalysispacchart'] = get_cookie('threeyearsalesanalysispacchart', true);
			}
			else
			{
				$data['threeyearsalesanalysispacchart'] = 0;
			}

			$thismonth = date("m");

			$graphlabel0 = $year0;
			$graphlabel1 = $year1;
			$graphlabel2 = $year2;

			// --------------------------------------------------------------------------------------------------------------------------------------------------
			// 3 YEAR SALES CHART
			// --------------------------------------------------------------------------------------------------------------------------------------------------

			$lastfieldno = 24 + ($thismonth-1);

			$data['sales'] = $this->getCustomerGraphSales($lastfieldno, $account, $level, $code);
			$data['quantities'] = $this->getCustomerGraphQuantities($lastfieldno, $account, $level, $code);

			$data['year0data'] = $this->site_model->GetYearData($data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
			$data['year0total'] = $this->site_model->GetYearTotal($data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
			$data['year0table'] = $this->site_model->GetYearTable($data['sales'], $data['year0total'], 12 + $start_month_delta, 23 + $start_month_delta);
			$data['year0datagraph'] = $data['year0data'];

			$data['year1data'] = $this->site_model->GetYearData($data['sales'], $start_month_delta, 11 + $start_month_delta);
			$data['year1total'] = $this->site_model->GetYearTotal($data['sales'], $start_month_delta, 11 + $start_month_delta);
			$data['year1table'] = $this->site_model->GetYearTable($data['sales'], $data['year1total'], $start_month_delta, 11 + $start_month_delta);
			$data['year1datagraph'] = $data['year1data'];

			$data['year2data'] = $this->site_model->GetYearData($data['sales'], $start_month_delta - 12, $start_month_delta - 1);
			$data['year2total'] = $this->site_model->GetYearTotal($data['sales'], $start_month_delta - 12, $start_month_delta - 1);
			$data['year2table'] = $this->site_model->GetYearTable($data['sales'], $data['year2total'], $start_month_delta - 12, $start_month_delta - 1);
			$data['year2datagraph'] = $data['year2data'];

			$data['quantityyear0data'] = $this->site_model->GetYearData($data['quantities'], 12 + $start_month_delta, 23 + $start_month_delta);
			$data["quantityyear0total"] = $this->site_model->GetYearTotal($data['quantities'], 12 + $start_month_delta, 23 + $start_month_delta);
			$data["quantityyear0table"] = $this->site_model->GetYearTable($data['quantities'], $data["quantityyear0total"], 12 + $start_month_delta, 23 + $start_month_delta);

			$data['quantityyear1data'] = $this->site_model->GetYearData($data['quantities'], $start_month_delta, 11 + $start_month_delta);
			$data["quantityyear1total"] = $this->site_model->GetYearTotal($data['quantities'], $start_month_delta, 11 + $start_month_delta);
			$data["quantityyear1table"] = $this->site_model->GetYearTable($data['quantities'], $data["quantityyear1total"], $start_month_delta, 11 + $start_month_delta);

			$data['quantityyear2data'] = $this->site_model->GetYearData($data['quantities'], $start_month_delta - 12, $start_month_delta - 1);
			$data["quantityyear2total"] = $this->site_model->GetYearTotal($data['quantities'], $start_month_delta - 12, $start_month_delta - 1);
			$data["quantityyear2table"] = $this->site_model->GetYearTable($data['quantities'], $data["quantityyear2total"], $start_month_delta - 12, $start_month_delta - 1);

			$data['year0ChartValues'] = $data['year0data'];
			$data['year1ChartValues'] = $data['year1data'];
			$data['year2ChartValues'] = $data['year2data'];
			$data['cumulativeYear0ChartValues'] = $this->site_model->GetCumulativeYearData($data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
			$data['cumulativeYear1ChartValues'] = $this->site_model->GetCumulativeYearData($data['sales'], $start_month_delta, 11 + $start_month_delta);
			$data['cumulativeYear2ChartValues'] = $this->site_model->GetCumulativeYearData($data['sales'], $start_month_delta - 12, $start_month_delta - 1);
			$data['quantityYear0ChartValues'] = $data['quantityyear0data'];
			$data['quantityYear1ChartValues'] = $data['quantityyear1data'];
			$data['quantityYear2ChartValues'] = $data['quantityyear2data'];
			$data['cumulativeQuantityYear0ChartValues'] = $this->site_model->GetCumulativeYearData($data['quantities'], 12 + $start_month_delta, 23 + $start_month_delta);
			$data['cumulativeQuantityYear1ChartValues'] = $this->site_model->GetCumulativeYearData($data['quantities'], $start_month_delta, 11 + $start_month_delta);
			$data['cumulativeQuantityYear2ChartValues'] = $this->site_model->GetCumulativeYearData($data['quantities'], $start_month_delta - 12, $start_month_delta - 1);

			$userId = 0;
			$branchNo = 0;
	
			if (count($this->session->userdata('selectedUser')) > 0)
			{
				$UserSes = $this->session->userdata('selectedUser');
				$userId = $UserSes["userid"];
			}
	
			if (count($this->session->userdata('selectedBranch')) > 0)
			{
				$branchSes = $this->session->userdata('selectedBranch');
				$branchNo = $branchSes["branchno"];
			}
	
			$userType = $data['userType'] = $data['userDetail']['usertype'];
	
			if ($userType == "B")
			{
				$G_level = "Branch";
			}
			elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0))
			{
				$G_level = "Company";
			}
			elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0))
			{
				$G_level = "Branch";
				$G_branchno = $branchNo;
			}
			elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0))
			{
				$G_level = "User";
				$G_userId = $userId;
				$data['userDetail'] = $this->products_model->getUserDetails($userId);
			}
			else
			{
				$G_level = "User";
			}
			
			$G_userid = $this->session->userdata("userid");
			$userKpi = $this->site_model->userKpi($G_level, $G_branchno, $G_userid);

			$data = GetKpiDataForTwoYearVsTargetChart($userKpi, $data, $G_level);

			$data['salesTargetForLastThreeYear'] = $this->site_model->getCustomerSalesTargetForThisYear($data['sales'], $level, $code, $account);
			$data['targetDataForCurrentYear'] = $this->site_model->GetTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);
			$data['cumulativeTargetDataForCurrentYear'] = $this->site_model->GetCumulativeTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);

			$this->load->view('customer/customergraph', $data);
		}

		private function getCustomerGraphSales($lastfieldno, $account, $level, $code)
		{
			$graphData = $this->customer_model->customerGraph($lastfieldno, $account, $level, $code);

			for ($x = 0; $x <= 35; $x++)
			{
				$sales[$x] = 0;
			}

			for ($x = 0; $x < count($graphData[0]); $x++)
			{
				$p = count($graphData[0]) -$x -1;
				$sales[$x] = $graphData[0]["msales".$p];
			}

			return $sales;
		}

		private function getCustomerGraphQuantities($lastfieldno, $account, $level, $code)
		{
			$graphData = $this->customer_model->customerGraphQuantities($lastfieldno, $account, $level, $code);

			for ($x = 0; $x <= 35; $x++)
			{
				$quantities[$x] = 0;
			}

			for ($x = 0; $x < count($graphData[0]); $x++)
			{
				$p = count($graphData[0]) -$x -1;
				$quantities[$x] = $graphData[0]["mquantity".$p];
			}

			return $quantities;
		}

		private function getYearData($start, $finish, $data)
		{
			$yeardata = "[";

			for ($counter = $start; $counter <= $finish; $counter++)
			{
				$yeardata.= "$data[$counter]";

				if ($counter != $finish)
				{
					$yeardata.= ",";
				}
			}

			$yeardata.= "]";

			return $yeardata;
		}

		private function getYearTable($start, $finish, $data)
		{
			$yeartotal = 0;

			for ($counter = $start; $counter <= $finish; $counter++)
			{
				$yeartable.= "<td>".number_format($data[$counter])."</td>";
				$yeartotal += $data[$counter];
			}

			$yeartable.= "<td>".number_format($yeartotal)."</td>";

			return $yeartable;
		}

		// 04-04-2017 Customer Graph Virtual End //
		public function customergraphprod()
		{
			$account  = $this->uri->segment(4);
			$code  = base64_decode($this->uri->segment(6));

			$data["year0"]=$year0 = date("Y");
			$data["year1"]=$year1 = $year0 - 1;
			$data["year2"]=$year2 = $year0 - 2;

			$this->load->helper('cookie');
			if (isset($_COOKIE['threeyearsalesanalysisproductschart']))
			{
				$data['threeyearsalesanalysisproductschart'] = get_cookie('threeyearsalesanalysisproductschart', true);
			}
			else
			{
				$data['threeyearsalesanalysisproductschart'] = 0;
			}

			$thismonth = date("m");

			$graphlabel0 = $year0;
			$graphlabel1 = $year1;
			$graphlabel2 = $year2;

			// --------------------------------------------------------------------------------------------------------------------------------------------------
			// 3 YEAR SALES CHART
			// --------------------------------------------------------------------------------------------------------------------------------------------------

			$lastfieldno = 24 + ($thismonth-1);

			$data['sales'] = $this->getCustomerGraphProductSales($lastfieldno, $account, $code);
			$data['quantities'] = $this->getCustomerGraphProductQuantities($lastfieldno, $account, $code);

			$data['year0data'] = $this->site_model->GetYearData($data['sales'], 24, 35);
			$data['year0total'] = $this->site_model->GetYearTotal($data['sales'], 24, 35);
			$data['year0table'] = $this->site_model->GetYearTable($data['sales'], $data['year0total'], 24, 35);
			$data['year0datagraph'] = $data['year0data'];
	
			$data['year1data'] = $this->site_model->GetYearData($data['sales'], 12, 23);
			$data['year1total'] = $this->site_model->GetYearTotal($data['sales'], 12, 23);
			$data['year1table'] = $this->site_model->GetYearTable($data['sales'], $data['year1total'], 12, 23);
			$data['year1datagraph'] = $data['year1data'];
	
			$data['quantityyear0data'] = $this->site_model->GetYearData($data['quantities'], 24, 35);
			$data["quantityyear0total"] = $this->site_model->GetYearTotal($data['quantities'], 24, 35);
			$data["quantityyear0table"] = $this->site_model->GetYearTable($data['quantities'], $data["quantityyear0total"], 24, 35);
	
			$data['quantityyear1data'] = $this->site_model->GetYearData($data['quantities'], 12, 23);
			$data["quantityyear1total"] = $this->site_model->GetYearTotal($data['quantities'], 12, 23);
			$data["quantityyear1table"] = $this->site_model->GetYearTable($data['quantities'], $data["quantityyear1total"], 12, 23);

			$data['year0ChartValues'] = $data['year0data'];
			$data['year1ChartValues'] = $data['year1data'];
			$data['cumulativeYear0ChartValues'] = $this->site_model->GetCumulativeYearData($data['sales'], 24, 35);
			$data['cumulativeYear1ChartValues'] = $this->site_model->GetCumulativeYearData($data['sales'], 12, 23);
			$data['quantityYear0ChartValues'] = $data['quantityyear0data'];
			$data['quantityYear1ChartValues'] = $data['quantityyear1data'];
			$data['cumulativeQuantityYear0ChartValues'] = $this->site_model->GetCumulativeYearData($data['quantities'], 24, 35);
			$data['cumulativeQuantityYear1ChartValues'] = $this->site_model->GetCumulativeYearData($data['quantities'], 12, 23);

			$userId = 0;
			$branchNo = 0;
	
			if (count($this->session->userdata('selectedUser')) > 0)
			{
				$UserSes = $this->session->userdata('selectedUser');
				$userId = $UserSes["userid"];
			}
	
			if (count($this->session->userdata('selectedBranch')) > 0)
			{
				$branchSes = $this->session->userdata('selectedBranch');
				$branchNo = $branchSes["branchno"];
			}
	
			$userType = $data['userType'] = $data['userDetail']['usertype'];
	
			if ($userType == "B")
			{
				$G_level = "Branch";
			}
			elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0))
			{
				$G_level = "Company";
			}
			elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0))
			{
				$G_level = "Branch";
				$G_branchno = $branchNo;
			}
			elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0))
			{
				$G_level = "User";
				$G_userId = $userId;
				$data['userDetail'] = $this->products_model->getUserDetails($userId);
			}
			else
			{
				$G_level = "User";
			}
			
			$G_userid = $this->session->userdata("userid");
			$userKpi = $this->site_model->userKpi($G_level, $G_branchno, $G_userid);

			$data = GetKpiDataForTwoYearVsTargetChart($userKpi, $data, $G_level);

			$data['salesTargetForLastThreeYear'] = $this->site_model->getCustomerSalesTargetForThisYear($data['sales'], null, $code, $account);
			$data['targetDataForCurrentYear'] = $this->site_model->GetTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);
			$data['cumulativeTargetDataForCurrentYear'] = $this->site_model->GetCumulativeTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);

			$this->load->view('customer/customergraphprod', $data);
		}

		private function getCustomerGraphProductSales($lastfieldno, $account, $code)
		{
			$graphData = $this->customer_model->customerGraphProductSales($lastfieldno, $account, $code);

			for ($x = 0; $x <= 35; $x++)
			{
				$sales[$x] = 0;
			}

			for ($x = 0; $x < count($graphData[0]); $x++)
			{
				$p = count($graphData[0]) -$x -1;
				$sales[$x] = $graphData[0]["msales".$p];
			}

			return $sales;
		}

		private function getCustomerGraphProductQuantities($lastfieldno, $account, $code)
		{
			$graphData = $this->customer_model->customerGraphProductQuantities($lastfieldno, $account, $code);

			for ($x = 0; $x <= 35; $x++)
			{
				$quantities[$x] = 0;
			}

			for ($x = 0; $x < count($graphData[0]); $x++)
			{
				$p = count($graphData[0]) -$x -1;
				$quantities[$x] = $graphData[0]["mquantity".$p];
			}

			return $quantities;
		}

		public function searchcodes() {
			header('Content-Type: application/json');
			$data["searchresult"] = $this->customer_model->searchCodes();
			echo json_encode($data["searchresult"]); exit;
		}

		public function searchproductcodes($key) {
			$data["searchresult"] = $this->customer_model->searchProductCodes($key);
			$this->load->view('customer/searchresult', $data);
		}

		public function addCustomerTargetData()
		{
			header('Content-Type: application/json');
			$posted = $_POST;
			$modelResult = $this->customer_model->addCustomerUniqueTarget($posted);

			echo json_encode(array
			(
				'action' => "add",
				'status' => $modelResult
			));

			exit;
		}

		public function uploadCustomerTargetData() {
			header('Content-Type: application/json');
			$account = $_POST["account"];
			$returnJson = array("action"=>"upload", "status"=>"");
			/* Copied from products  */

			if ($this->input->server('REQUEST_METHOD')=='POST') {
				$this->load->library('form_validation');
				$this->load->library('user_agent');
				if (!empty($_FILES['targetcsv']['name'])) {
					$allowed =  array('csv');
					$filename = $_FILES['targetcsv']['name'];
					$ext = pathinfo($filename, PATHINFO_EXTENSION);
					if (!in_array($ext,$allowed) ) {
					$returnJson["status"] = "ExtensionNotAllowed";
					} else {
						if (is_uploaded_file($_FILES['targetcsv']['tmp_name'])) {
						//open uploaded csv file with read only mode
						$csvFile = fopen($_FILES['targetcsv']['tmp_name'], 'r');

						//skip first line
						fgetcsv($csvFile);

						//parse data from csv file line by line
						$tableInsert = array();
						$i["pac0"] = 0;
						$i["pac1"] = 0;
						$i["pac2"] = 0;
						$i["pac3"] = 0;
						$i["pac4"] = 0;
						$i["product"] = 0;
						$substringlength = array(
							"pac0"=>0,
							"pac1"=>1,
							"pac2"=>3,
							"pac3"=>5,
							"pac4"=>7,
							"product"=>24
						);
						while(($column = fgetcsv($csvFile)) !== FALSE) {
							$column[1] = strtoupper($column[1]);
							if ("P"!=$column[1]) {
								$column[1] = "pac".$column[1];
							} else {
								$column[1] = "product";
							}
							$tableInsert[$column[1]][$i[$column[1]]]["account"] = $column[0];
							$tableInsert[$column[1]][$i[$column[1]]][$column[1]."code"] = substr($column[2], 0, $substringlength[$column[1]]);
							$tableInsert[$column[1]][$i[$column[1]]]["yearmonth"] = $column[3];
							$tableInsert[$column[1]][$i[$column[1]]]["salestarget"] = $column[4];
							$i[$column[1]]++;
						}
						$returnedFromModel = $this->customer_model->addCustomerUniqueTargetcsv($tableInsert);
						$returnJson["modal"] = $returnedFromModel;
						$returnJson["status"] = "completed";
						fclose($csvFile);
						} else {
							$returnJson["status"] = "FileNotUploaded";
						}
					}
				} else {
					$returnJson["status"] = "EmptyFile";
				}

			} else {
				$returnJson["status"] = "NotPosted";
			}
			echo json_encode($returnJson);
			exit;
		}

		public function customerTargetPopulate($account)
		{
			$account = base64_decode($account);
			header('Content-Type: application/json');
			$userDetail = $this->site_model->getUserDetails($this->session->userdata('userid'));
			$customerTargetsData = $this->customer_model->customerTargetsData($userDetail['edittargets'], $account);
			$data = array('data' => $customerTargetsData);
			echo json_encode($data);
			exit;
		}

		public function deleteCustomerTargetData($codetype, $id) {
			header('Content-Type: application/json');
			$complete = $this->customer_model->deleteCustomerTargetsData($codetype, $id);
			echo json_encode(array(
				"complete"=>$complete
			));
			exit;
		}

		public function pacsalestargetdata() {
			if ($this->site_model->is_logged_in()==false) {
				redirect('/');
			}

			$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));

			$userType = $data['userType'] = $data['userDetail']['usertype'];
			$userId = 0;
			$branchNo = 0;
			$headerUserId=0;

			if (count($this->session->userdata('selectedUser')) > 0){
				$UserSes = $this->session->userdata('selectedUser');
				$userId = $UserSes["userid"];
			}
			$headerUserId = $userId;

			if (count($this->session->userdata('selectedBranch')) > 0){
				$branchSes = $this->session->userdata('selectedBranch');
				$branchNo = $branchSes["branchno"];
			}

			if ($userType=="B"){
				$G_level = "branch";
			}

			if ($userType=="B"){
				$G_level = "Branch";
			} elseif (($userType=="A") && ($branchNo == 0) && ($userId == 0)){
				$G_level = "Company";
			} elseif (($userType=="A") && ($branchNo > 0) && ($userId == 0)){
				$G_level = "Branch";
				$G_branchno = $branchNo;
			} elseif (($userType=="A") && ($branchNo == 0) && ($userId > 0)){
				$G_level = "User";
				$G_userId = $userId;
				$data['userDetail'] = $this->site_model->getUserDetails($userId);
			} else {
				$G_level = "User";
			}

			$repclause = $data["userDetail"]["repclause"];
			$data['pac1salestarget'] = $this->customer_model->getPac1SalesTargetDashboard($G_level,$userId,$branchNo,$repclause);
			$data['getSalesTotalMonthWise'] = $this->site_model->getSalesTotalMonthWise($G_level,$targetUserId,$branchNo,$repclause);
		//	$data['main_content'] = 'pacsalestargetdata';
		//	$this->load->view('product/front_template', $data);
		}

		public function saveTarget() {
			header('Content-Type: application/json');
			$post = $_POST;
			$table = $post["table"];
			unset($post["id"]);
			unset($post["table"]);
			$result = $this->customer_model->updateSalesTargetData($_POST["id"], $table, $post);
			echo json_encode($result);
			exit;
		}

		public function repcodes($account)
		{
			header('Content-Type: application/json');
			$result = $this->customer_model->customerRepcodes(base64_decode($account));
			echo json_encode($result);
			exit;
		}

		public function deleterep($repcode, $account)
		{
			header('Content-Type: application/json');
			$result = $this->customer_model->deleterep($repcode, base64_decode($account));
			echo json_encode($result);
			exit;
		}
		public function deleteurep($repcode, $userid) {
			header('Content-Type: application/json');
			$result = $this->customer_model->deleteurep($repcode, $userid);
			echo json_encode($result);
			exit;
		}

		public function productTotals()
		{
			header('Content-Type: application/json');
			$account = $this->session->userdata("myaccount");
			$specific_search = $this->findPostedSpecificSearchAndMake();
			$search_key = strtolower($_POST['search']['value']);
			$result = $this->customer_model->getCustomerProductSalesAnalysis($account, $specific_order = false, $offset = "0", $limit = "", $search_key, $specific_search, true, true);
			$result = $result[0];
			//$totals = $this->sumList($result);
			foreach ($result as $key => $item)
			{
				if (is_null($item))
				{
					$result[$key] = floatval(0.00);
				}
				else
				{
					$result[$key] = floatval($item);
				}
			}

			echo json_encode(array
			(
				'totals'  => $result,
				'success' => true,
			));

			exit;
		}

		public function totals()
		{
			header("Content-Type: application/json");
			$specific_search = $this->findPostedSpecificSearchAndMakec();
			$search_key = $_POST['search']['value'];
			$result = $this->customer_model->getCustomerSalesAnalysis("", "", "", $offset = "0", $limit = "0", $search_key, $specific_search);
			$totals = $this->sumList($result);

			echo json_encode(array
			(
				'totals'  => $totals,
				'success' => true,
			));
			exit;
		}

		/** This will  */
		public function sumList($result_array)
		{
			$addedArray = array();
			$percentageRequired = array();
			$marginRequired = array();

			foreach ($result_array as $rowNumber => $row)
			{
				$index = 0;

				if ($index == 0 && array_key_exists('diff_percent', $row))
				{
					$percentageRequired['diff_percent'] = array
					(
						'first'  => 'sales_ytd',
						'second' => 'sales_lastyear',
						'label'  => 'diff',
					);
				}

				if ($index == 0 && array_key_exists('sales_ytd', $row) && array_key_exists('sales_y1', $row))
				{
					$percentageRequired['sales_diff'] = array
					(
						'first'  => 'sales_ytd',
						'second' => 'sales_y1',
						'label'  => 'sales_diff',
					);
				}

				if ($index == 0 && array_key_exists('qty_ytd', $row) && array_key_exists('qty_y1', $row))
				{
					$percentageRequired['qty_diff'] = array
					(
						'first'  => 'qty_ytd',
						'second' => 'qty_y1',
						'label'  => 'qty_diff',
					);
				}

				if ($index == 0 && array_key_exists('sales_mtd', $row) && array_key_exists('costs_mtd', $row))
				{
					$marginRequired['gm_mtd'] = array
					(
						'first'  => 'sales_mtd',
						'second' => 'costs_mtd',
						'label'  => 'total_gm_mtd',
					);
				}

				if ($index == 0 && array_key_exists('sales_ytd', $row) && array_key_exists('costs_ytd', $row))
				{
					$marginRequired['gm_ytd'] = array
					(
						'first'  => 'sales_ytd',
						'second' => 'costs_ytd',
						'label'  => 'total_gm_ytd',
					);
				}

				foreach ($row as $key => $val)
				{
					switch ($key)
					{
						case "YoY1Sales":
						{
							$addedArray['sales_lastyear']+= floatval($val);
						}
						break;

						case "YoY2Sales":
						{
							$addedArray['sales_last_to_lastyear']+= floatval($val);
						}
						break;

						default:
						{
							$addedArray[$key]+= floatval($val);
						}
						break;
					}

					$index++;
				}
			}

			if (!empty($percentageRequired))
			{
				foreach ($percentageRequired as $key => $fields)
				{
					$addedArray[$fields['label']] = 0;

					if ($addedArray[$fields['second']] != 0)
					{
						$percentage = ($addedArray[$fields['first']] / $addedArray[$fields['second']]) * 100;
						$addedArray[$fields['label']] = number_format($percentage, 2);
					}
				}
			}

			if (!empty($marginRequired))
			{
				foreach ($marginRequired as $key => $fields)
				{
					$addedArray[$fields['label']] = 0;

					if ($addedArray[$fields['first']] != 0)
					{
						$percentage = ($addedArray[$fields['first']] - $addedArray[$fields['second']]) / $addedArray[$fields['first']] * 100;
						$addedArray[$fields['label']] = number_format($percentage, 2);
					}
				}
			}

			return $addedArray;
		}

		public function manage_cookie()
		{
			if ($this->input->server('REQUEST_METHOD') == 'POST')
			{
				$this->load->helper('cookie');
				$cookie_name = $this->input->post('cookie_name');
				$cookie_value = $this->input->post('cookie_value');
	
				$cookie = array(
					'name' => $cookie_name,
					'value' => $cookie_value,
					'expire' => '315360000', //cookie expires in 10 years!
					'secure' => TRUE);
				set_cookie($cookie);
			}
			else
			{
				redirect('dashboard');
			}
		}
	}
