<?php
class Products extends Controller {
	public $getSAListTotals = array();
	function __construct()
	{
		parent::Controller();
		$this->load->model('products_model');

		$this->load->model('site/site_model');
		$this->getSAListTotals = array();
	}


		/**
	* product list
	*
	* @author		Virtual Employee PVT. LTD.
	* @Descrption	Return product list
	* @Created Date     22-01-2016
	* @Updated Date
	*/

	function index()
	{
		if ($this->site_model->is_logged_in() == false)
		{
			redirect('/');
		}

		$data['main_content'] = 'product_list';
		$this->load->view('product/front_template', $data);
	}

	function productPAC($data)
	{
		$this->load->view('product_pac', $data);
	}

	/* Function to fetch the Product PAC in JSON format for DataTable */
	public function fetchProductPAC($pacNo)
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
		$data['prodsanalpac'] = array();

		$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
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
			$branchNo = $branchSes['branchno'];
		}

		$recodeArray = $this->site_model->getUsersRepcodeCustom($userId);
		$recordsTotal = $this->products_model->getProductPACCount($pacNo);
		$recordsFiltered = $this->products_model->getProductPACFilteredCount($pacNo, $branchNo, $search);

		$specificOrder = array
		(
			'by'  => $_POST['columns'][$_POST['order'][0]['column']]['data'],
			'dir' => $_POST['order'][0]['dir'],
		);

		$dbResult = $this->products_model->getProductPAC($data['userDetail']['repwhere'], $pacNo, $specificOrder, $recodeArray, $branchNo, $search, $start, $limit);

		$numerical_result = array();
		$data['prodsanalpac'] = array();

		foreach ($dbResult as $row)
		{
			$row['description'] = "<a href='".base_url()."products/details2/".$pacNo."/".$row['code']."'>".$row['description']."</a>";
			$data['prodsanalpac'][] = $row;
		}

		$index = 0;

		foreach ($data['prodsanalpac'] as $row)
		{
			foreach ($row as $key=>$value)
			{
				if ($row[$key]=="")
				{
					$data['prodsanalpac'][$index][$key] = "0.00";
				}
			}

			$index++;
		}

		$dbResultAll = $this->products_model->getProductPAC($data['userDetail']['repwhere'], $pacNo, $specificOrder, $recodeArray, $branchNo, $search, 0, null);
		$index = 0;

		foreach ($dbResultAll as $row)
		{
			foreach ($row as $key=>$value)
			{
				if ($row[$key]=="")
				{
					$dbResultAll[$index][$key] = "0.00";
				}
			}

			$index++;
		}

		$data["prodsanalpacsum"] = $this->products_model->sumList($dbResultAll);

		$data['search'] = "";
		$data['curyearmonth'] = date('Ym');
		$data['lnk'] = base_url().'products/details2/'.$indx;
		$data['pagecount'] = intval(sizeof($data['prodsanalpac']) / $limit) + 1;

		$return_array = array
		(
			"draw"            => $draw,
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $recordsFiltered,
			"data"            => $data['prodsanalpac'],
			"columnTotals"	  => $data["prodsanalpacsum"]
		);

		echo json_encode($return_array);
		exit;
	}

	public function fetchProductPACCsvExport($pacNo, $searchTerm = null)
	{
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
		$lastYear = date("Y") - 1;
		$yearBeforeLastYear = date("Y") - 2;
		$csv .= "Code, Description, Sales YTD, Qty YTD, Sales Diff %, Qty Diff %, Sales " . $lastYear
			. ", Qty " . $lastYear . " , Sales " . $yearBeforeLastYear . ", Qty " . $yearBeforeLastYear
			. " , Sales MTD, Qty MTD, GM% MTD, GM% YTD";

		$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
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
			$branchNo = $branchSes['branchno'];
		}

		$recodeArray = $this->site_model->getUsersRepcodeCustom($userId);

		$specificOrder = array
		(
			'by'  => 1,
			'dir' => "asc",
		);

		$dbResult = $this->products_model->getProductPAC($data['userDetail']['repwhere'], $pacNo, $specificOrder, $recodeArray, $branchNo, $searchTerm, $start, $limit);

		$index = 0;

		foreach ($dbResult as $row)
		{
			foreach ($row as $key => $value)
			{
				if ($value == "")
				{
					$dbResult[$index][$key] = "0.00";
				}
			}

			$index++;
		}

		//$columnTotals = $this->products_model->sumList($dbResult);

		foreach ($dbResult as $row)
		{
			$csv .=
			"\n" . $row['code'] . "," . $row['description'] . "," . $row['salesytd']
			. "," . $row['qtyytd'] . "," . $row['salesdiff'] . "," . $row['qtydiff']
			. "," . $row['YoY1Sales'] . "," . $row['YoY1Qty'] . "," . $row['YoY2Sales']
			. "," . $row['YoY2Qty'] . "," . $row['salesmtd'] . "," . $row['qtymtd']
			. "," . $row['marginmtdpc'] . "," . $row['marginytdpc'];
		}

		// $csv .=
		// 	"\n" . $columnTotals['code'] . "," . $columnTotals['description'] . ",". $columnTotals['salesytd']
		// 	. "," . $columnTotals['qtyytd'] . "," . $columnTotals['sales_diff'] . "," . $columnTotals['qty_diff']
		// 	. "," . $columnTotals['YoY1Sales'] . "," . $columnTotals['YoY1Qty'] . "," . $columnTotals['YoY2Sales']
		// 	. "," . $columnTotals['YoY2Qty'] . "," . $columnTotals['salesmtd'] . "," . $columnTotals['qtymtd']
		// 	. "," . $columnTotals['marginmtdpc'] . "," . $columnTotals['marginytdpc'];

		echo $csv;
		exit;
	}

	/**
	* product prodSAProd list
	*
	* @author		Virtual Employee PVT. LTD.
	* @Descrption	Return product prodSAProd list
	* @Created Date     22-01-2016
	* @Updated Date
	*/

	function getSAList($elementId = "")
	{
		header('Content-Type: application/json');
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

		$draw = isset($_POST['draw']) ? $_POST['draw'] : 1;

		$specific_search = $this->findPostedSpecificSearchAndMake();
		$search_key = $_POST['search']['value'];

		$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));

		$userId = 0;
		$branchNo = 0;

		if (count($this->session->userdata('selectedUser')) > 0)
		{
			$UserSes = $this->session->userdata('selectedUser');
			$userId = $UserSes['userid'];
		}

		if (count($this->session->userdata('selectedBranch')) > 0)
		{
			$branchSes = $this->session->userdata('selectedBranch');
			$branchNo = $branchSes['branchno'];
		}

		$recodeArray = $this->site_model->getUsersRepcodeCustom($userId);

		$totalCount = $this->products_model->prodSAProd1DataTableCount($data['userDetail']['repwhere'], "", array(), $recodeArray, $branchNo);
		$filteredCount = $this->products_model->prodSAProd1DataTableCount($data['userDetail']['repwhere'], $search_key, $specific_search, $recodeArray, $branchNo);

		$search_keys = array("p.code", "p.pac4code", "p.description", "freeqty", "purchaseqty", "salesytd", "qtyytd", "salesdiff", "qtydiff", "YoY1Sales", "YoY1Qty", "YoY2Sales", "YoY2Qty", "salesmtd", "qtymtd", "marginmtdpc", "marginytdpc", "costsmtd", "costsytd");

		$specific_order = array
		(
			'by'  => $search_keys[0],
			'dir' => 'asc',
		);

		$posted_order = $_POST['order'];

		if (isset($posted_order[0]['column']) && $posted_order[0]['column'] >= 0 && isset($posted_order[0]['dir']))
		{
			$specific_order['by'] = $search_keys[$posted_order[0]['column']];
			$specific_order['dir'] = $posted_order[0]['dir'];
		}

		$results = $this->products_model->prodSAProd1DataTable($data['userDetail']['repwhere'], $specific_order, $search_key, $specific_search, $recodeArray, $branchNo);
		$data['result'] = $results['numerical_array'];

		$return_array = array
		(
			'draw'            => $draw,
			'recordsTotal'    => $totalCount,
			'recordsFiltered' => $filteredCount,
			'data'            => $data['result'],
			'with'            => array('columns' => $_POST['columns']),
		);

		echo json_encode($return_array);
		exit;
	}

	public function getSAListTotalValues()
	{
		header('Content-Type: application/json');
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

		$specific_search = $this->findPostedSpecificSearchAndMake();
		$search_key = $_POST['search']['value'];

		$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
		$specific_order = $this->findPostedOrder();
		$recodeArray = $this->site_model->getUsersRepcodeCustom($userId);
		$results = $this->products_model->prodSAProd1DataTableNoLimit($data['userDetail']['repwhere'], $specific_order, $search_key, $specific_search, $recodeArray, $branchNo, true, true);

		$this->getSAListTotals = $results['numerical_array'][0]; //$this->products_model->sumList($results["numerical_array"]);

		unset($this->getSAListTotals['costsmtd']);
		unset($this->getSAListTotals['costsytd']);

		echo json_encode(array
		(
			'totals'  => $this->getSAListTotals,
			'success' => true,
		));

		exit;
	}

	/**
	* Product detail method
	*
	* @author		Virtual Employee PVT. LTD.
	* @Descrption	Return Product Data
	* @Created Date     02-02-2016
	* @Updated Date
	*/ public function findPostedSpecificSearchAndMake() {
		$posted_columns = $_POST['columns'];
		$search_keys = $this->getSpecificSearchKeys();
		$search = array();
		foreach ($posted_columns as $key=>$col) {
			$search[$search_keys[$key]] = $col['search']['value'];
		}

		return $search;
	}

	function details($prodcode)
	{
		if ($this->site_model->is_logged_in() == false)
		{
			redirect('/');
		}

		$data['year'] = date("Y");
		$data['thismonth'] = date("m");
		$data['soytemp'] = $data['year']."-"."01-01";
		$data['soy'] = date('Y-m-d', strtotime($data['soytemp']));
		$data['somtemp'] = $data['year']."-".$data['thismonth']."-01";
		$data['som'] = date ('Y-m-d', strtotime($data['somtemp']));
		$data['prodcode'] = base64_decode($prodcode);
		$data['users'] = $this->products_model->get_users();
		$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
		$row = $this->products_model->prodDesc2($data['prodcode']);
		$data['description'] = $row['description'];
		$data['pac4'] = $row['pac4code'];

		$data['daysinmonth'] = date("t",strtotime(date('Y-m-d')));
		$data['year0'] = date("Y");
		$data['year1'] = $data['year0'] - 1;
		$data['year2'] = $data['year0'] - 2;
		$data['year3'] = $data['year0'] - 3;
		$data['thismonth'] = date("m");
		$data['mainUserEdirAccess'] = $data['userDetail']['edittargets'];

		$data['graphlabel0'] = $data['year1']."-".$data['year0'];
		$data['graphlabel1'] = $data['year2']."-".$data['year1'];
		$data['graphlabel2'] = $data['year3']."-".$data['year2'];

		$data['startdate'] = $data['year3']."-".$data['thismonth']."-".$data['daysinmonth'];

		$data['startyearmonth'] = ($data['year2'] * 100) + '01';
		$data['startthisyearrmonth'] = ($data['year0'] * 100) + 1; // The start of the current year
		$data['curyearmonth'] = ($data['year0'] * 100) + $data['thismonth']; // e.g. 201507

		// Initialise sales array

		$data['yearmonth']  = array();
		$data['monthnames'] = array();
		$data['sales']      = array();

		// Preload the year and month into an array so that we can make sure we load the sales against the correct row. Pad the month with leading 0 if needed. Had an example where
		// a rep started more recently that three years ago, and therefore there was less than 36 months. It was loading all these into the start of the array, rather than against the
		// appropriate row.

		$data['tmpyear']  = $data['year2'];
		$data['tmpmonth'] = 1;

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

		/*$result = $this->products_model->getSaleAnalysis($data['userDetail']['repwhere'],$data['prodcode'], $data['startyearmonth'], $data['curyearmonth']);*/
		$page = 5;
		$collectiveResult = $this->products_model->getPACSaleAnalysis($data['prodcode'], $data['startyearmonth'], $data['curyearmonth'], $page);
		$result = $collectiveResult[0];
		$data["result"] = $collectiveResult[2];

		$collectiveResultQuantities = $this->products_model->getPACSaleAnalysisQuantities($data['userDetail']['repcode'], $data['prodcode'], $data['startyearmonth'], $data['curyearmonth'], $page);
		$data["quantity_result"] = $collectiveResultQuantities[2];

		foreach ($result as $row)
		{
			$data['salessummaryyearmonth'] = $row['yearmonth'];
			$data['salessummarysales']     = $row['sales'];

			// For each data row, loop through the array and put the sales value in the correct place
			for ($x = 0; $x <= 36; $x++)
			{
				if ($data['yearmonth'][$x] == $data['salessummaryyearmonth'])
				{
					$data['sales'][$x] = $data['salessummarysales'];
				}

				// If the year month of the data matches the array, put the value in
			}
		}
		$data['year0data'] = $this->site_model->GetYearData($data['sales'], 24, 35);
        $data['year0total'] = $this->site_model->GetYearTotal($data['sales'], 24, 35);
        $data['year0table'] = $this->site_model->GetYearTable($data['sales'], $data['year0total'], 24, 35);

        $data['year1data'] = $this->site_model->GetYearData($data['sales'], 12, 23);
        $data['year1total'] = $this->site_model->GetYearTotal($data['sales'], 12, 23);
        $data['year1table'] = $this->site_model->GetYearTable($data['sales'], $data['year1total'], 12, 23);

		$counter = 0;

		foreach (array_reverse($data["quantity_result"]) as $row)
		{
			$data['quantities'][$counter] = $row;
			$counter++;
		}

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

		// Set the month names ... the first 12 are last year, the last 1 are this year

		$data['ticks'] = "[";

		for ($x = 0; $x <= 12; $x++)
		{
			$data['mthno'] = $data['thismonth'] + ($x + 1);

			if ($data['mthno'] > 12)
			{
				$data['mthno'] = $data['mthno'] - 12;
			}

			$data['month'] = date('F', mktime(0, 0, 0, $data['mthno'], 10));
			$ticks.= "[$x,'". $data['month'] ."']";

			if ($x != 12)
			{
				$data['ticks'].= ",";
			}
		}

		$data['ticks'].= "]";

		$this->load->helper('cookie');
		if (isset($_COOKIE['threeyearproductschart']))
		{
			$data['threeyearproductschart'] = get_cookie('threeyearproductschart', true);
		}
		else
		{
			$data['threeyearproductschart'] = 0;
		}

		// $data['custList'] = $this->products_model->prodSADetails($data['userDetail']['repwhere'],$data['prodcode'],$data['startthisyearrmonth'], $data['curyearmonth']);
		$data['custList'] = $this->products_model->prodSADetailsUsingCustomerProdSales($data['userDetail']['repwhere'], $data['prodcode']);
		$data['productStockList'] = $this->products_model->getProductStockList($data['prodcode']);

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

		$data['salesTargetForLastThreeYear'] = $this->site_model->getProductSalesTargetForLastThreeYear($G_level, $data['yearmonth'], $data['sales'], $userId, $branchNo, $page, $prodcode);
		$data['targetDataForCurrentYear'] = $this->site_model->GetTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);
		$data['cumulativeTargetDataForCurrentYear'] = $this->site_model->GetCumulativeTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);

		$data['lnk'] = base_url().'customer/customerDetails/';
		$data['salestarget'] = $this->products_model->get_productSalestarget($data['userDetail'], $userId, $branchNo, $page, $prodcode);
		$data['main_content'] = 'product_detail';
		$this->load->view('product/front_template', $data);
	}

	/**
	* Product detail2 method
	*
	* @author		Virtual Employee PVT. LTD.
	* @Descrption	Return Product Data
	* @Created Date     02-02-2016
	* @Updated Date
	*/

	function details2($page, $prodcode)
	{
		if ($this->site_model->is_logged_in() == false)
		{
			redirect('/');
		}

		$data['prodcode'] = $prodcode;
		$data['page'] = $page;
		$data['date'] = date('Y-m-d');
		$data['daysinmonth'] = date("t", strtotime($data['date']));
		$data['year0'] = date("Y");
		$data['year1'] = $data['year0'] - 1;
		$data['year2'] = $data['year0'] - 2;
		$data['year3'] = $data['year0'] - 3;
		$data['thismonth'] = date("m");
		$data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
		$data['mainUserEdirAccess'] = $data['userDetail']['edittargets'];

		$data['graphlabel0'] = $data['year1']."-".$data['year0'];
		$data['graphlabel1'] = $data['year2']."-".$data['year1'];
		$data['graphlabel2'] = $data['year3']."-".$data['year2'];
		$data['startdate'] = $data['year3']."-".$data['thismonth']."-".$data['daysinmonth'];
		$data['startyearmonth'] = ($data['year2'] * 100) + 1; // Start of three years ago (rolling)
		$data['startthisyearrmonth'] = ($data['year0'] * 100) + 1; // The start of the current year
		$data['curyearmonth'] = ($data['year0'] * 100) + $data['thismonth']; // e.g. 201507

		$data['yearstartmonth'] = $this->products_model->getYearStartMonth();
        $start_month_delta = $data['yearstartmonth'] <= date('m') ? 11 + $data['yearstartmonth'] : $data['yearstartmonth'] - 1;

		// Initialise sales array

		$data['yearmonth']  = array();
		$data['monthnames'] = array();
		$data['sales']      = array();

		// Preload the year and month into an array so that we can make sure we load the sales against the correct row. Pad the month with leading 0 if needed. Had an example where
		// a rep started more recently that three years ago, and therefore there was less than 36 months. It was loading all these into the start of the array, rather than against the
		// appropriate row.

		$data['tmpyear']  = $data['year3'];
		$data['tmpmonth'] = 1;

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

		$this->load->helper('cookie');
		if (isset($_COOKIE['threeyearproductspacchart']))
		{
			$data['threeyearproductspacchart'] = get_cookie('threeyearproductspacchart', true);
		}
		else
		{
			$data['threeyearproductspacchart'] = 0;
		}

		// Get sales for this product & sales rep. THIS IS FOR GRAPH - LIST SQLS FURTHER DOWN
		$collectiveResult = $this->products_model->getPACSaleAnalysis($data['prodcode'], $data['startyearmonth'], $data['curyearmonth'], $page);
		$result = $collectiveResult[0];
		$data["result"] = $collectiveResult[2];

		$collectiveResultQuantities = $this->products_model->getPACSaleAnalysisQuantities($data['userDetail']['repcode'], $data['prodcode'], $data['startyearmonth'], $data['curyearmonth'], $page);
		$data["quantity_result"] = $collectiveResultQuantities[2];

		foreach ($result as $row)
		{
			$data['salessummaryyearmonth'] = $row['yearmonth'];
			$data['salessummarysales']     = $row['sales'];

			for ($x = 0; $x < 48; $x++)
			{
				if ($data['yearmonth'][$x] == $data['salessummaryyearmonth'])
				{
					$data['sales'][$x] = $data['salessummarysales'];
				} // If the year month of the data matches the array, put the value in
			}
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

		$counter = 0;

		foreach (array_reverse($data["quantity_result"]) as $row)
		{
			$data['quantities'][$counter] = $row;
			$counter++;
		}

		$data['quantityyear0data'] = $this->site_model->GetYearData($data['quantities'], 24 + $start_month_delta, 35 + $start_month_delta);
        $data["quantityyear0total"] = $this->site_model->GetYearTotal($data['quantities'], 24 + $start_month_delta, 35 + $start_month_delta);
        $data["quantityyear0table"] = $this->site_model->GetYearTable($data['quantities'], $data["quantityyear0total"], 24 + $start_month_delta, 35 + $start_month_delta);

		$data['quantityyear1data'] = $this->site_model->GetYearData($data['quantities'], 12 + $start_month_delta, 23 + $start_month_delta);
        $data["quantityyear1total"] = $this->site_model->GetYearTotal($data['quantities'], 12 + $start_month_delta, 23 + $start_month_delta);
        $data["quantityyear1table"] = $this->site_model->GetYearTable($data['quantities'], $data["quantityyear1total"], 12 + $start_month_delta, 23 + $start_month_delta);

		$data['quantityyear2data'] = $this->site_model->GetYearData($data['quantities'], $start_month_delta, 11 + $start_month_delta);
        $data["quantityyear2total"] = $this->site_model->GetYearTotal($data['quantities'], $start_month_delta, 11 + $start_month_delta);
        $data["quantityyear2table"] = $this->site_model->GetYearTable($data['quantities'], $data["quantityyear2total"], $start_month_delta, 11 + $start_month_delta);

		$data['description'] = $this->products_model->prodDesc($data['prodcode'], $page);

		$data['year0ChartValues'] = $data['year0data'];
		$data['year1ChartValues'] = $data['year1data'];
		$data['year2ChartValues'] = $data['year2data'];
		$data['cumulativeYear0ChartValues'] = $this->site_model->GetCumulativeYearData($data['sales'], 24 + $start_month_delta, 35 + $start_month_delta);
		$data['cumulativeYear1ChartValues'] = $this->site_model->GetCumulativeYearData($data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
		$data['cumulativeYear2ChartValues'] = $this->site_model->GetCumulativeYearData($data['sales'], $start_month_delta, 11 + $start_month_delta);
		$data['quantityYear0ChartValues'] = $data['quantityyear0data'];
		$data['quantityYear1ChartValues'] = $data['quantityyear1data'];
		$data['quantityYear2ChartValues'] = $data['quantityyear2data'];
		$data['cumulativeQuantityYear0ChartValues'] = $this->site_model->GetCumulativeYearData($data['quantities'], 24 + $start_month_delta, 35 + $start_month_delta);
		$data['cumulativeQuantityYear1ChartValues'] = $this->site_model->GetCumulativeYearData($data['quantities'], 12 + $start_month_delta, 23 + $start_month_delta);
		$data['cumulativeQuantityYear2ChartValues'] = $this->site_model->GetCumulativeYearData($data['quantities'], $start_month_delta, 11 + $start_month_delta);

		$userId = 0;
		$branchNo = 0;

		if (count($this->session->userdata('selectedUser')) > 0)
		{
			$UserSes= $this->session->userdata('selectedUser');
			$userId = $UserSes["userid"];
		}

		if (count($this->session->userdata('selectedBranch')) > 0)
		{
			$branchSes= $this->session->userdata('selectedBranch');
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

		$data['salesTargetForLastThreeYear'] = $this->site_model->getProductSalesTargetForLastThreeYear($G_level, $data['yearmonth'], $data['sales'], $userId, $branchNo, $page, $prodcode);
		$data['targetDataForCurrentYear'] = $this->site_model->GetTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);
		$data['cumulativeTargetDataForCurrentYear'] = $this->site_model->GetCumulativeTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);

		$recodeArray = $this->site_model->getUsersRepcodeCustom($userId);
		$data['custList'] = $this->products_model->prodDetails2Customerprodsales($data['userDetail']['repwhere'], $page, $prodcode, $data['startthisyearrmonth'], $data['curyearmonth'], $recodeArray, $branchNo);
		$data['lnk'] = base_url().'customer/customerDetails/';
		$data['salestarget'] = $this->products_model->get_salestarget($G_level, $userId, $branchNo, $page, $prodcode);
		$data['main_content'] = 'product_detail2';
		$data['users'] = $this->products_model->get_users();
		$this->load->view('product/front_template', $data);
	}

	/* function to download CSV format */
	public function csv_export() {
		$search_key = $this->uri->segment(3);
		$specific_search_keys = $this->getSpecificSearchKeys();
		$data = $this->passDetails();

		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=customers.csv");
		$csvOutput = $this->products_model->csv_export($data['userDetail']['repwhere'], $search_key);
		echo $csvOutput;
		exit();
	}

	/* function to download XLSX format */
	public function excel_export() {
		$search_key = $this->uri->segment(3);
		$specific_search_keys = $this->getSpecificSearchKeys();
		$data = $this->passDetails();
		// code written on 24-09-2018
		$specific_search = $this->findPostedSpecificSearchAndMake();
		$data['userDetail']=$this->site_model->getUserDetails($this->session->userdata('userid'));
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
		$recodeArray=$this->site_model->getUsersRepcodeCustom($userId);
		//code on 24-09-2018
		header("Content-type: text/x-csv");

		header("Content-Disposition: attachment;filename=\"MI-DAS-Product.csv\"");

		header("Cache-Control: max-age=0");

		//$xlsOutput = $this->products_model->csv_export($data['userDetail']['repwhere'], $search_key);
		$xlsOutput = $this->products_model->csv_exportCustom($data['userDetail']['repwhere'], $search_key, $specific_search, $recodeArray, $branchNo);
		echo $xlsOutput;
		exit();
	}
	public function prd1_excel_export() {
		$ind = $this->uri->segment(3);

		$specific_search_keys = $this->getSpecificSearchKeys();
		$data = $this->passDetails();
		$indx=str_replace("PAC", "", $ind);
		// code written on 25-09-2018
		$data['userDetail']=$this->site_model->getUserDetails($this->session->userdata('userid'));
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
		$recodeArray=$this->site_model->getUsersRepcodeCustom($userId);
		//code on 25-09-2018
		header("Content-type: text/x-csv");

		header("Content-Disposition: attachment;filename=MI-DAS-Product-".$ind."-report.csv");

		header("Cache-Control: max-age=0");
		//$xlsOutput = $this->products_model->prd1_csv_export($data['userDetail']['repwhere'], $search_key,$ind);
		$xlsOutput = $this->products_model->prd1_csv_exportCustom($data['userDetail']['repwhere'], $search_key,$ind, $recodeArray, $branchNo);

		echo $xlsOutput;
		exit();
	}


	/* Function to get the keys for specific search */
	public function getSpecificSearchKeys() {
		$search_keys = array('p.code','p.pac4code' ,'p.description', 'p.freeqty', 'salesmtd', 'qtymtd', 'marginmtdpc', 'salesytd', 'qtyytd', 'marginytdpc','YoY1Sales','YoY2Sales');
		return $search_keys;
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

		$data['graphlabel0'] = $data['year0']; // CR0001 $year1."-".$year0;
		$data['graphlabel1'] = $data['year1']; // CR0001 $year2."-".$year1;
		$data['graphlabel2'] = $data['year2']; // CR0001 $year3."-".$year2;

		$data['startdate'] = $data['year2']."01-01"; // CR0001 "$year3."-".$thismonth."-".$daysinmonth;

		$data['startyearmonth'] = ($data['year2'] * 100) + 1; // CR0001 ($year3 * 100) + $thismonth;
		$data['startthisyearrmonth'] = ($data['year0'] * 100) + 1; // The start of the current year
		$data['curyearmonth'] = ($data['year0'] * 100) + $data['thismonth']; // e.g. 201507

		$data['userDetail']=$this->site_model->getUserDetails($this->session->userdata('userid'));
		return $data;
	}

	/* Function to get the posted order and it's direction. this function will return order by column name that can be used in query directly and the direction. */
	public function findPostedOrder() {

		$posted_order = $_POST['order'];
		$column_index = -1;
		$order = array(
			'by'	=>	$search_keys[0],
			'dir'	=>	'asc'
		);

		if (isset($posted_order[0]['column']) && isset($posted_order[0]['dir'])) {
			$column_index = $posted_order[0]['column'];
		}

		$search_keys = $this->getSpecificSearchKeys();

		if ($column_index>=0) {
			$order = array(
				'by'	=>	$search_keys[$column_index],
				'dir'	=>	$posted_order[0]['dir']
			);
		} else {
			$order = array(
				'by'	=>	$search_keys[0],
				'dir'	=>	'asc'
			);
		}

		return $order;
	}



	public function addtarget() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('userid', 'User Id', 'required');
		$this->form_validation->set_rules('year', 'Year', 'required');
		$this->form_validation->set_rules('month', 'Month', 'required');
		$this->form_validation->set_rules('salestarget', 'salestarget', 'required');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">The target could not be added!</div>');
		} else {
			$userid = $this->input->post("userid");
			$page_code=$this->input->post("page_code");
			$product_code=$this->input->post("product_code");
			$year = $this->input->post("year");
			$month = str_pad($this->input->post("month"), 2, "0", STR_PAD_LEFT);
			$salestarget = $this->input->post("salestarget");
			$result = $this->products_model->addUniqueTarget($userid, $year, $month, $salestarget,$page_code,$product_code);
			if ($result=="success") {
				$this->session->set_flashdata('target_operation', '<div class="alert alert-success">The target added successfully!</div>');
			}

			if ($result=="duplicate") {
				$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">Target for '.$year.$month.' already exists!</div>');
			}

			if ($result=="fail") {
				$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">Target could not be added!</div>');
			}

		}

		redirect('products/details2/'.$page_code.'/'.$product_code.'#target');
	}

	public function addtargettoproductsalestarget() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('userid', 'User Id', 'required');
		$this->form_validation->set_rules('year', 'Year', 'required');
		$this->form_validation->set_rules('month', 'Month', 'required');
		$this->form_validation->set_rules('salestarget', 'salestarget', 'required');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">The target could not be added!</div>');
		} else {
			$userid = $this->input->post("userid");
			$product_code=$this->input->post("product_code");
			$year = $this->input->post("year");
			$month = str_pad($this->input->post("month"), 2, "0", STR_PAD_LEFT);
			$salestarget = $this->input->post("salestarget");
			$result = $this->products_model->addUniqueTargetToproductsalestarget($userid, $year, $month, $salestarget,$product_code);
			if ($result=="success") {
				$this->session->set_flashdata('target_operation', '<div class="alert alert-success">The target added successfully!</div>');
			}

			if ($result=="duplicate") {
				$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">Target for '.$year.$month.' already exists!</div>');
			}

			if ($result=="fail") {
				$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">Target could not be added!</div>');
			}

		}

		redirect('products/details/'.base64_encode($product_code).'#target');
	}




	public function uploadtarget()
	{
		if ($this->input->server('REQUEST_METHOD')=='POST')
		{


			$this->load->library('form_validation');
$this->load->library('user_agent');


		//		$page_code=$this->input->post("page_code");
			// $product_code=$this->input->post("product_code");

			if (!empty($_FILES['file']['name'])){
					$allowed =  array('csv');
				$filename = $_FILES['file']['name'];
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				if (!in_array($ext,$allowed) ) {
				$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">Invalid Extension!</div>');
				}else
				{

					// echo "allowed"; exit;
					if (is_uploaded_file($_FILES['file']['tmp_name'])){

					//open uploaded csv file with read only mode
					$csvFile = fopen($_FILES['file']['tmp_name'], 'r');

					//skip first line
					fgetcsv($csvFile);

					//parse data from csv file line by line
					//print_r(fgetcsv($csvFile));
					// echo "entered"; exit;
					while(($line = fgetcsv($csvFile)) !== FALSE){

						$userid=$line[0];

						$yearmonth=$line[3];

						$salestarget=$line[4];
						$page_code=$line[2];
						$product_code=$line[1];

						$year=substr($yearmonth,0,4);

					$month=substr($yearmonth,4,2);



					$result = $this->products_model->addUniqueTargetcsv($userid, $year, $month, $salestarget,$page_code,$product_code);


					}


					$this->session->set_flashdata('target_operation', '<div class="alert alert-success">The target added successfully!</div>');

					fclose($csvFile);

					$qstring = '?status=succ';
					}else{
					$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">The target could not be added!</div>');
					}
				}




				}else{
				$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">The target could not be added!</div>');
				}

			redirect($this->agent->referrer());

		}
		else
		{
			redirect('dashboard');
		}
	}


public function uploadtargettoproductsalestarget() {
	if ($this->input->server('REQUEST_METHOD')=='POST') {
		$this->load->library('form_validation');
		$this->load->library('user_agent');
		if (!empty($_FILES['file']['name'])) {
			$allowed =  array('csv');
			$filename = $_FILES['file']['name'];
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			if (!in_array($ext,$allowed) ) {
				$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">Invalid Extension!</div>');
			} else {
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {
					//open uploaded csv file with read only mode
					$csvFile = fopen($_FILES['file']['tmp_name'], 'r');

					//skip first line
					fgetcsv($csvFile);

					//parse data from csv file line by line

					while(($line = fgetcsv($csvFile)) !== FALSE) {
						$userid = $line[0];
						$yearmonth = $line[3];
						$salestarget = $line[4];
						$page_code = $line[2];
						$product_code = $line[1];
						$year = substr($yearmonth,0,4);
						$month = substr($yearmonth,4,2);
						$result = $this->products_model->addProductsUniqueTargetcsv($userid, $year, $month, $salestarget,$page_code,$product_code);
					}
					$this->session->set_flashdata('target_operation', '<div class="alert alert-success">Targets for the products added successfully!</div>');

					fclose($csvFile);

					$qstring = '?status=succ';
				} else {
					$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">The target could not be added!</div>');
				}
			}
		} else {
			$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">The target could not be added!</div>');
		}

		redirect($this->agent->referrer());

	} else {
		redirect('dashboard');
	}
}




function updateyearmonth() {
		header('Content-type: application/json');
		$yearmonth = $this->input->post("yearmonth");
		$year = substr($yearmonth, 0, 4);
		$month = substr($yearmonth, 4, 2);
		if ($month<10) {
			$month = "0".$month;
		}
		$yearmonth = $year.$month;
		$id = $this->input->post("id");
		$userid = $this->session->userdata('userid');
		$page=$this->input->post('page');
		$prodcode=$this->input->post('prodcode');
		if (!$this->products_model->checkTargetUnique($userid, $year, $month,$page,$prodcode)) {
			echo json_encode(array("value"=>"duplicate"));
		} else {
			if ($this->products_model->updateYearMonth($id, $yearmonth,$page)) {
				echo json_encode(array("value"=>"success"));
			} else {
				echo json_encode(array("value"=>"notsaved"));
			}
		}
		exit;
	}

function updatesalestarget() {
		header('Content-type: application/json');
		$salestarget = $this->input->post("salestarget");
		$id = $this->input->post("id");
			$page = $this->input->post("page");
		if ($this->products_model->updateSalesTarget($id, $salestarget,$page)) {
			echo json_encode(array("value"=>"success"));
		} else {
			echo json_encode(array("value"=>"notsaved"));
		}
		exit;
	}
	public function deletetarget($id, $userid,$page) {
		header('Content-type: application/json');
		$user_target_details = $this->products_model->getTargetDetails($id,$page);
		$delete = $this->products_model->deleteUserTarget($id,$page);
		echo json_encode(array("deleteresult"=>$delete));exit;
	}

	/*public function queryTester() {
		ini_set('display_errors', 1);
		$columnSelectorArray=array(
			"account"=>"account",
			"name"=>"customer_name",
			"product_code"=>"",
			"cpac4"=>"",
			"product_description"=>"",
			"salesmtd"=>"salemtd",
			"qtymtd"=>"quantitymtd",
			"marginmtdpc"=>"marginmtdpc",
			"salesytd"=>"salesytd",
			"qtyytd"=>"quantityytd",
			"marginytdpc"=>"marginytdpc"
		);
		$this->load->model('common_model');
		$this->common_model->customerProduct($columnSelectorArray);
		$query = $this->common_model->db->get();
		$resultarray = $query->row_array();
	//	print_r($resultarray);
		echo "Last query >>> ".$this->common_model->db->last_query();
		exit;
	}*/

	public function pacsalestargetdata($segment,$by) {
		if ($this->site_model->is_logged_in()==false) {
			redirect('/');
		}

		$data['userDetail'] = $this->products_model->getUserDetails($this->session->userdata('userid'));

		$userType = $data['userType']=$data['userDetail']['usertype'];
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
			$data['userDetail']=$this->products_model->getUserDetails($userId);
		} else {
			$G_level = "User";
		}

		$repclause = $data["userDetail"]["repclause"];
		$data['pac1salestarget'] = $this->products_model->getPac1SalesTargetDashboard($G_level,$userId,$branchNo,$repclause);
		$data['getSalesTotalMonthWise'] = $this->site_model->getSalesTotalMonthWise($G_level,$targetUserId,$branchNo,$repclause);
		$data['main_content'] = 'pacsalestargetdata';
		$this->load->view('product/front_template', $data);
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
?>
