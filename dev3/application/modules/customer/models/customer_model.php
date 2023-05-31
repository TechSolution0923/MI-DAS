<?php
class Customer_model extends Model {
	private $in;
	function __construct() {
		parent::__construct();
		$this->load->model('common_model');
		$userid = $this->session->userdata("userid");
		$usertype = $this->session->userdata("usertype");

		$loggedInUserId = "";
		if ("A"!=$usertype) {
			if ("B"!=$usertype) {
				$loggedInUserId = $userid;
			} else {
				$loggedInUserId = 0;
			}
		} else {
			$selectedUser = $this->session->userdata("selectedUser");
			$loggedInUserId = $selectedUser['userid'];
			if (0!=$loggedInUserId){
			} else {
				$loggedInUserId = 0;
			}
		}
		$this->in = $this->repcodeFactor($loggedInUserId);
	}

	/* The base of the Customersales table query */

	public function baseCustomerSalesQuery($startyearmonth, $curyearmonth, $search_key, $specific_search, $count = true)
	{
		$this->makeSelectForCustomerSalesAnalysis($count);
		$this->db->from('customer C');
		$this->db->join('customersales CS', 'C.account = CS.account', 'left');

		$UserSes = $this->session->userdata('selectedUser');
		$userId = $UserSes["userid"];

		if ($userId != 0)
		{
			$sql = $this->db->query("SELECT * FROM users WHERE userid = $userId");
			$str = $sql->row_array();
			$type_user = $str["usertype"];
		}

		if ($type_user == "A")
		{
			$this->db->where('C.account IS NOT NULL ');
		}
		else
		{
			$this->db->where('C.account IS NOT NULL ');
		}

		$check_sees = $this->session->userdata('selectedBranch');
		$chk_ses = $check_sees["branchno"];

		if ($chk_ses != 0)
		{
			$this->db->where('C.branch', $chk_ses);
		}

		$skip_columns_in_search_key = array();

		$specific_like = array();

		foreach ($specific_search as $key => $specific)
		{
			if (isset($specific) && "" != trim($specific))
			{
				$specific_like[] = "LOWER(".$key.") LIKE LOWER('%".$specific."%')";
				$skip_columns_in_search_key[] = $key;
			}
		}

		if (!empty($specific_like))
		{
			$specificLikePart = "(".implode(" AND ", $specific_like).")";
			$this->db->where($specificLikePart);
		}

		$like = array();

		if (isset($search_key) && "" != trim($search_key))
		{
			if (!in_array('C.account', $skip_columns_in_search_key))
			{
				$like[] = "LOWER(C.account) LIKE LOWER('%".$search_key."%')";
			}

			if (!in_array('C.name', $skip_columns_in_search_key))
			{
				$like[] = "LOWER(C.name) LIKE LOWER('%".$search_key."%')";
			}

			if (!in_array('C.postcode', $skip_columns_in_search_key))
			{
				$like[] = "LOWER(C.postcode) LIKE LOWER('%".$search_key."%')";
			}

			if (!in_array('C.userdef1', $skip_columns_in_search_key))
			{
				$like[] = "LOWER(C.userdef1) LIKE LOWER('%".$search_key."%')";
			}

			if (!in_array('C.repcode', $skip_columns_in_search_key))
			{
				$like[] = "LOWER(C.repcode) LIKE LOWER('%".$search_key."%')";
			}

			$like[] = "LOWER(C.address1) LIKE LOWER('%".$search_key."%')";
			$like[] = "LOWER(C.address2) LIKE LOWER('%".$search_key."%')";
			$like[] = "LOWER(C.address3) LIKE LOWER('%".$search_key."%')";
			$like[] = "LOWER(C.address4) LIKE LOWER('%".$search_key."%')";
			$like[] = "LOWER(C.address5) LIKE LOWER('%".$search_key."%')";

			$where = $this->whereMonthWise();

			foreach ($where['month'] as $month)
			{
				if (!in_array($month, $skip_columns_in_search_key))
				{
					$like[] = $month." LIKE '%".$search_key."%'";
				}
			}

			foreach ($where['year'] as $year)
			{
				if (!in_array($year, $skip_columns_in_search_key))
				{
					$like[] = $year." LIKE '%".$search_key."%'";
				}
			}

			$likePart = "(".implode(" OR ", $like).")";

			$this->db->where($likePart);
		}

		if (!empty($this->in))
		{
			$where_user_selected = "C.account IN (".implode(", ", $this->in).")";
			$this->db->where($where_user_selected);
		}
	}

	/** Function to return the array of account to be used in the IN brackets */
	public function repcodeFactor($userId) {
		if (0!= $userId) {
			$this->db->select("c.account as cr_account");
			$this->db->from("customerreps as c");
			$this->db->join("userreps as u", "c.repcode = u.repcode", "left");
			$this->db->where("u.userid", $userId);
			$query = $this->db->get();
			$result_array = $query->result_array();
			if (!empty($result_array)) {
				return array_map("mapRepcodeFactor", $result_array);
			} else {
				return array("'nocustomeraccountassociated'");
			}
		} else {
			return false;
		}

	}

	public function baseCustomerSalesQuery_csv($repwhere, $startyearmonth, $curyearmonth, $search_key, $specific_search, $count = true, $canSeeMargins = 0)
	{
		$this->makeSelectForCustomerSalesAnalysis($count);
		$this->db->from('customer C');
		$this->db->join('customersales CS', 'C.account = CS.account', 'left');
		$this->db->group_by('C.account');

		/* Generating repcode condition */

		$UserSes = $this->session->userdata('selectedUser');
		$userId = $UserSes["userid"];

		if ($userId != 0)
		{
			$sql = $this->db->query("SELECT * from users where userid = $userId");
			$str = $sql->row_array();
			$type_user = $str["usertype"];
		}

		if ($type_user == "A")
		{
			$this->db->where('C.account IS NOT NULL ');
		}
		else
		{
			$this->db->where('C.account IS NOT NULL '.$repwhere);
		}

		$check_sees = $this->session->userdata('selectedBranch');
		$chk_ses = $check_sees["branchno"];

		if ($chk_ses != 0)
		{
			$this->db->where('C.branch',$chk_ses);
		}

		$SpecificSearchExists = $this->checkIfSpecificSearchExists($specific_search);

		$skip_columns_in_search_key = array();

		$like = array();

		if (isset($search_key) && "" != trim($search_key))
		{
			if (!in_array('C.account', $skip_columns_in_search_key))
			{
				$like[] = "LOWER(C.account) LIKE LOWER('%".$search_key."%')";
			}

			if (!in_array('C.name', $skip_columns_in_search_key))
			{
				$like[] = "LOWER(C.name) LIKE LOWER('%".$search_key."%')";
			}

			if (!in_array('C.postcode', $skip_columns_in_search_key))
			{
				$like[] = "LOWER(C.postcode) LIKE LOWER('%".$search_key."%')";
			}

			$like[] = "LOWER(C.address1) LIKE LOWER('%".$search_key."%')";
			$like[] = "LOWER(C.address2) LIKE LOWER('%".$search_key."%')";
			$like[] = "LOWER(C.address3) LIKE LOWER('%".$search_key."%')";
			$like[] = "LOWER(C.address4) LIKE LOWER('%".$search_key."%')";
			$like[] = "LOWER(C.address5) LIKE LOWER('%".$search_key."%')";

			$where = $this->whereMonthWise();

			foreach ($where['month'] as $month)
			{
				if (!in_array($month, $skip_columns_in_search_key))
				{
					$like[] = $month." LIKE LOWER('%".$search_key."%')";
				}
			}

			foreach ($where['year'] as $year)
			{
				if (!in_array($year, $skip_columns_in_search_key))
				{
					$like[] = $year." LIKE LOWER('%".$search_key."%')";
				}
			}

			$likePart = "(".implode(" OR ", $like).")";
			$this->db->where($likePart);
		}
	}

	public function updateSalesTargetData($id, $table, $data) {
		$isUnique = $this->uniqueRow($id, $table, $data);
		if ($isUnique) {
			$this->db->where('id',$id);
			return $this->db->update($table, $data);
		} else {
			return false;
		}
	}

	public function uniqueRow($id, $table, $data) {
		$changng_yearmonth = false;
		$this->db->select("*");
		$this->db->from($table);
		$this->db->where("id", $id);
		$query = $this->db->get();
		$oldData = $query->result_array();
		$oldData = $oldData[0];
		unset($oldData["id"]);
		unset($oldData["salestarget"]);

		if (isset($data["yearmonth"])) {
			$oldData["yearmonth"] = $data["yearmonth"];
			$changng_yearmonth = true;
		} else {
			$changng_yearmonth = false;
		}

		$this->db->select("id");
		$this->db->from($table);
		$this->db->where($oldData);
		$query1 = $this->db->get();
		$dataCount = $query1->num_rows();
		return (!$changng_yearmonth || $dataCount==0);
	}

	/* Function to fetch the total rows of result present in the customer sales analysis using the new table customersales. */

	public function getCustomerSalesAnalysisCount($startyearmonth, $curyearmonth, $search_key = "", $specific_search = array())
	{
		/* Generating branch condition */

		$this->common_model->makeBranchCondition('customersales.branch', false);

		$this->baseCustomerSalesQuery($startyearmonth, $curyearmonth, $search_key, $specific_search, true);

		$query = $this->db->get();

		return $query->row();
	}

	/* Function to fetch the customer sales analysis using the new table customersales. This table reduces the effort of calculation. */

	public function getCustomerSalesAnalysis($repwhere, $startyearmonth, $curyearmonth,$offset, $limit, $search_key="", $specific_search=array()) {
//		$repwhereCondition = $this->common_model->queryRepcode();
//		if (""!=trim($repwhereCondition)) {
//			$repcode_section = "AND CR.cr_repcode IN (".$repwhereCondition.")";
//		} else {
//			$repcode_section = "";
//		}
//
//		if (!empty($limit)) {
//			if (!empty($offset)) {
//				$limit_offset = "LIMIT ".$limit." OFFSET ".$offset;
//			} else {
//				$limit_offset = "LIMIT ".$limit;
//			}
//		} else {
//			$limit_offset = "";
//		}
//
//
//		$query_file = 'index.sql';
//		$className = 'customer';
//		$search = array("{repcode_section}", "{limit_offset}");
//		$replace = array($repcode_section, $limit_offset);
//
//		$query_str = $this->common_model->queryMaker($query_file, $className, $search, $replace);
//	//	echo $query_str;
//		$query = $this->db->query($query_str);
//		$result = $query->result_array();
		$result = $this->getCustomerSalesAnalysisDataTable(null, null, 0, 10000000, $search_key, $specific_search, false, 0, true);
		return $result;
	}

	/* The base of the Customer productsales table query */

	public function baseCustomerProductSalesQuery($account, $search_key, $specific_search, $count = false, $totals = false)
	{
		if ($count)
		{
			$specific_search_set = false;

			foreach ($specific_search as $search)
			{
				if ($search != "")
				{
					$specific_search_set = true;
					break;
				}
			}

			$this->db->flush_cache();
			$this->db->select('p.code');
			$this->db->from('product p');

			if ($search_key != "" || $specific_search_set)
			{
				$this->db->join('customerprodsales s', "p.code = s.prodcode AND s.account = '".$account."'", "left");
				$this->db->group_by('p.code');
			}
		}
		else
		{
			$proRataCoefficient = $this->common_model->getWorkingDayProRataCoefficient(date("Y/m/d"));
			$proRataAdjustment = "";

			$seemarginsAr = $this->common_model->loggedin_userdetail();
			$year0 = date("Y");
			$year1 = $year0 - 1;
			$year2 = $year0 - 2;
			$thismonth = date("m");
			$YoYEnd1 = $thismonth + 11;
			$YoYStart1 = ($YoYEnd1 - $thismonth) + 1;
			$YoYEnd2 = $thismonth + 23;
			$YoYStart2 = ($YoYEnd2 - $thismonth) + 1;
			$y = 0;

			for ($x = $YoYStart1; $x <= $YoYEnd1; $x++)
			{
				if (!$y == 0)
				{
					$query1.= "+";
					$queryq1.= "+";
				}
				else
				{
					$proRataAdjustment = "-s.msales".$x."*(1-".$proRataCoefficient.")";
				}

				$query1.= "s.msales".$x;
				$queryq1.= "s.mquantity".$x;

				$y++;
			}

			$y = 0;

			for ($x = $YoYStart2; $x <= $YoYEnd2; $x++)
			{
				if (!$y == 0)
				{
					$query2.= "+";
					$queryq2.= "+";
				}

				$query2.= "s.msales".$x;
				$queryq2.= "s.mquantity".$x;

				$y++;
			}

			$yearOnYearData = ", ROUND(SUM(".$query1.$proRataAdjustment."), 2) as sales_y1, SUM(".$query2.") AS sales_y2, SUM(".$queryq1.") as qty_y1, SUM(".$queryq2.") as qty_y2";

			if (!!$seemarginsAr["seemargins"])
			{
				$selMarginM = '(sum(s.mmargin0)/sum(s.msales0))*100 as total_gm_mtd,';
				$selMarginY = ', (sum(s.ymargin0)/sum(s.ysales0))*100 as total_gm_ytd';
			}
			else
			{
				$selMarginM = '';
				$selMarginY = '';
			}

			$this->db->flush_cache();

			if ($totals)
			{
				$this->db->select('sum(s.msales0) as sales_mtd, sum(s.mquantity0) as qty_mtd, '.$selMarginM.' sum(s.ysales0) as sales_ytd, sum(s.yquantity0) as qty_ytd, SUM(s.mcost0) as costs_mtd, SUM(s.ycost0) as costs_ytd'.$selMarginY.$yearOnYearData.', IFNULL(IF((SUM(s.ysales0)-SUM('.$query1.$proRataAdjustment.'))/SUM('.$query1.$proRataAdjustment.')*100 > 100, ((SUM(s.ysales0)-SUM('.$query1.$proRataAdjustment.'))/SUM('.$query1.$proRataAdjustment.')*100), (SUM(s.ysales0)-SUM('.$query1.$proRataAdjustment.'))/SUM('.$query1.$proRataAdjustment.')*100), IF(SUM(s.ysales0) > 0.00, CAST(100.00 AS DECIMAL), CAST(0.00 AS DECIMAL))) as sales_diff, IFNULL(IF((SUM(s.yquantity0)-SUM('.$queryq1.'))/SUM('.$queryq1.')*100 > 100, ((SUM(s.yquantity0)-SUM('.$queryq1.'))/SUM('.$queryq1.')*100)-100, (SUM(s.yquantity0)-SUM('.$queryq1.'))/SUM('.$queryq1.')*100), IF(SUM(s.yquantity0) > 0.00, CAST(100.00 AS DECIMAL), CAST(0.00 AS DECIMAL))) as qty_diff', false);
				$this->db->from('product p');
				$this->db->join('customerprodsales s', "p.code = s.prodcode AND s.account = '".$account."'");
			}
			else
			{
				$this->db->select('p.code, p.pac4code as cpac4, p.description, sum(s.msales0) as sales_mtd, sum(s.mquantity0) as qty_mtd, '.$selMarginM.' sum(s.ysales0) as sales_ytd, sum(s.yquantity0) as qty_ytd, SUM(s.mcost0) as costs_mtd, SUM(s.ycost0) as costs_ytd'.$selMarginY.$yearOnYearData.', IFNULL(IF((SUM(s.ysales0)-SUM('.$query1.$proRataAdjustment.'))/SUM('.$query1.$proRataAdjustment.')*100 > 100, ((SUM(s.ysales0)-SUM('.$query1.'))/SUM('.$query1.$proRataAdjustment.')*100), (SUM(s.ysales0)-SUM('.$query1.$proRataAdjustment.'))/SUM('.$query1.$proRataAdjustment.')*100), IF(SUM(s.ysales0) > 0.00, CAST(100.00 AS DECIMAL), CAST(0.00 AS DECIMAL))) as sales_diff, IFNULL(IF((SUM(s.yquantity0)-SUM('.$queryq1.'))/SUM('.$queryq1.')*100 > 100, ((SUM(s.yquantity0)-SUM('.$queryq1.'))/SUM('.$queryq1.')*100)-100,  (SUM(s.yquantity0)-SUM('.$queryq1.'))/SUM('.$queryq1.')*100), IF(SUM(s.yquantity0) > 0.00, CAST(100.00 AS DECIMAL), CAST(0.00 AS DECIMAL))) as qty_diff', false);
				$this->db->from('product p');
				$this->db->join('customerprodsales s', "p.code = s.prodcode AND s.account = '".$account."'", "left");
				$this->db->group_by('p.code');
			}
		}

		/* Generating branch condition */

		$this->common_model->makeBranchCondition('s.branch', false);

		if ("" != trim($search_key))
		{

			$this->db->like("p.code COLLATE UTF16_GENERAL_CI", $search_key);

			$this->db->or_like("p.description COLLATE UTF16_GENERAL_CI", $search_key);
			$this->db->or_like("p.pac4code COLLATE UTF16_GENERAL_CI", $search_key);

		}

		$skip_columns_in_search_key = array();
		$count_arr = count($specific_search);

		if ($count_arr != 0)
		{
			$specific_like = array();

			foreach ($specific_search as $key => $specific)
			{
				if (isset($specific) && "" != trim($specific))
				{
					if ("cpac4" == $key)
					{
						$key = "p.pac4code";
					}

					$specific_like[] = "LOWER(".$key.") LIKE LOWER('%".$specific."%')";
					$skip_columns_in_search_key[] = $key;
				}
			}

			if (!empty($specific_like))
			{
				$specificLikePart = "(".implode(" AND ", $specific_like).")";
				$this->db->where($specificLikePart);
			}
		}
	}

	/* Function to fetch the total rows of result present in the customer product sales analysis using the new table customersales. */

	public function getCustomerProductSalesAnalysisCount($account, $search_key="",$specific_search=array()) {

		$this->baseCustomerProductSalesQuery($account, $search_key,$specific_search, true);

		$query = $this->db->get();

		$this->common_model->showLastQuery("getCustomerProductSalesAnalysisCount");

		return $query->num_rows();

	}

	/* Function to fetch the customer product sales analysis using the new table customersales. This table reduces the effort of calculation. */

	public function getCustomerProductSalesAnalysis($account, $specific_order, $offset, $limit, $search_key = "", $specific_search = array(), $with_keys = false, $totals = false)
	{
		$this->baseCustomerProductSalesQuery($account, $search_key, $specific_search, false, $totals);

		$this->db->limit($limit, $offset);

		if ($specific_order)
		{
			$this->db->order_by($specific_order['by'], $specific_order['dir']);
		}

		//$query_string =  $this->db->_compile_select();
		//$query_string = str_replace('(product p)', 'product p', $query_string);
		//echo $query_string;
		//exit;
		//$query = $this->db->query($query_string);
		$query = $this->db->get();

		$result_array = $query->result_array();

		$this->common_model->showLastQuery("getCustomerProductSalesAnalysis");

		if ($_SERVER['REMOTE_ADDR'] == "182.65.62.217")
		{
			echo $this->db->last_query();
		}

		$numerical_result = array();
		$canSeeMargins = canSeeMargins();

		foreach ($result_array as $ra)
		{
			$description = '<a  style="cursor: pointer;" onclick="hide_pop(\''.base_url().'customer/customergraphprod/account/'.$account.'/code/'.base64_encode($ra["code"]).'\')" >'.$ra['description'].'</a>';

			if (!$with_keys)
			{
				$resultItem = array
				(
					$ra['code'],
					$ra['cpac4'],
					$description,
					number_format($ra['sales_ytd'], 2),
					number_format($ra['qty_ytd'], 2),
					number_format($ra['sales_diff'], 2),
					number_format($ra['qty_diff'], 2),
					number_format($ra['sales_y1'], 2),
					number_format($ra['qty_y1'], 2),
					number_format($ra['sales_y2'], 2),
					number_format($ra['qty_y2'], 2),
					number_format($ra['sales_mtd'], 2),
					number_format($ra['qty_mtd'], 2),
				);

				if ($canSeeMargins)
				{
					$resultItem[] = number_format($ra['total_gm_mtd'], 2);
					$resultItem[] = number_format($ra['total_gm_ytd'], 2);
				}

				$numerical_result[] = $resultItem;
			}
			else
			{
				$numerical_result = $result_array;
				// $numerical_result[] = array
				// (
				// 	"code"        => $ra['code'],
				// 	"pac4"        => $ra['cpac4'],
				// 	"description" => $description,
				// 	"sales_ytd"   => number_format($ra['ysalesytd'],2),
				// 	"qty_ytd"     => number_format($ra['yquantityytd'],2),
				// 	"sales_diff"  => number_format($ra['salesdiff'],2),
				// 	"qty_diff"    => number_format($ra['qtydiff'],2),
				// 	"sales_y1"    => number_format($ra['YoY1Sales'],2),
				// 	"qty_y1"      => number_format($ra['YoY1Qty'],2),
				// 	"sales_y2"    => number_format($ra['YoY2Sales'],2),
				// 	"qty_y2"      => number_format($ra['YoY2Qty'],2),
				// 	"sales_mtd"   => number_format($ra['salemtd'],2),
				// 	"qty_mtd"     => number_format($ra['quantitymtd'],2),
				// 	"gm_mtd"      => number_format($ra['marginpcmtd'],2),
				// 	"gm_ytd"      => number_format($ra['ymarginpcytd'],2),
				// 	"costs_mtd"   => number_format($ra['costsmtd'], 2),
				// 	"costs_ytd"   => number_format($ra['costsytd'], 2),
				// );
			}
		}

		return $numerical_result;
	}

	/* Function to fetch the customer sales analysis using the new table customersales. This function return array will be used in the DataTable.*/

	public function getCustomerSalesAnalysisDataTable($startyearmonth, $curyearmonth, $offset, $limit, $search_key = "", $specific_search = array(), $specific_order = false, $canSeeMargins = 0, $with_keys = false)
	{
		/* Generating branch condition */

		$this->common_model->makeBranchCondition('customersales.branch', false);

		$this->baseCustomerSalesQuery($startyearmonth, $curyearmonth, $search_key, $specific_search, false);

		$this->db->limit($limit, $offset);

		if ($specific_order)
		{
			$this->db->order_by($specific_order['by'], $specific_order['dir']);
		}

		$query = $this->db->get();

		// echo $this->db->last_query();

		if (!$with_keys)
		{
			return $this->numericalResult($query->result_array(), $canSeeMargins);
		}

		return $query->result_array();
	}

	/** Converting the result array from the customer sales analysis data to numerical array to be used in data tables */
	public function numericalResult($result_array, $canSeeMargins)
	{
		$numerical_result = array();
		$index = 0;

		foreach ($result_array as $ra)
		{
			$sales_ytd = ($ra['sales_ytd'] ?: "0.00");
			$YoY1Sales = ($ra['YoY1Sales'] ?: "0.00");
            $ysales1 = ($ra['ysales1'] ?: "0.00");
			$YoY2Sales = ($ra['YoY2Sales'] ?: "0.00");
            $ysales2 = ($ra['ysales2'] ?: "0.00");
			/* Intriducing a new column diff_percent */

			$diff_percent = $ra['diff_percent'];
			/* End diff_percent calculation */

			if (floatval($sales_ytd) > floatval($YoY1Sales))
			{
				$rowcolor = "greenrow";
			}
			elseif (floatval($sales_ytd) < floatval($YoY1Sales))
			{
				$rowcolor = "redrow";
			}
			else
			{
				$rowcolor = "";
			}

			$ra_name = "<a title='".$this->common_model->constructSingleLineAddress($ra)."' data-class='".$rowcolor."' href='".site_url("customer/customerDetails/".base64_encode($ra['account']))."'>".$ra['name']."</a>";

			/*
			Account, Name, Sales YTD, Sales Last Year (2018), Diff % (from step 2), Sales 2017, Sales MTD, GM% MTD (if visible), GM% YTD (if visible), Post Code and User Def. 1? I’m taking the Qty MTD and Qty YTD columns off the list
			*/
			$numerical_result[$index][] = $ra['account'];
			$numerical_result[$index][] = $ra_name;
			$numerical_result[$index][] = $sales_ytd;
			$numerical_result[$index][] = $YoY1Sales;
            $numerical_result[$index][] = $ysales1;
			$numerical_result[$index][] = $diff_percent;
			$numerical_result[$index][] = $YoY2Sales;
            $numerical_result[$index][] = $ysales2;
			$numerical_result[$index][] = ($ra['sales_mtd'] ? $ra['sales_mtd'] : "0.00");

			if ($canSeeMargins)
			{
				$numerical_result[$index][] = ($ra['gm_per_mtd'] ? $ra['gm_per_mtd'] : "0.00");
				$numerical_result[$index][] = ($ra['gm_per_ytd'] ? $ra['gm_per_ytd'] : "0.00");
			}

			$numerical_result[$index][] = $ra['postcode'];
			$numerical_result[$index][] = $ra['userdef1'];
			$numerical_result[$index][] = $ra['repcode'];

			$index++;
		}

		return $numerical_result;
	}

	/* function to check if the specific search is performed or not. returns true if the search is performed else false. */

	public function checkIfSpecificSearchExists($specific_search) {
		$flag = false;
		foreach ($specific_search as $ss) {
			if (""!=trim($ss)) {
				$flag = true;
			}
		}
		return $flag;
	}

	/* function to make the select part of the customer sales analysis query */
	public function makeSelectForCustomerSalesAnalysis($count)
	{
		if (!$count)
		{
			$proRataCoefficient = $this->common_model->getWorkingDayProRataCoefficient(date("Y/m/d"));
			$proRataAdjustment = "";

			$year0 = date("Y");
			$year1 = $year0 - 1;
			$year2 = $year0 - 2;
			$thismonth = date("m");
			$YoYEnd1 = $thismonth + 11;
			$YoYStart1	= ($YoYEnd1 - $thismonth) + 1;
			$YoYEnd2 = $thismonth + 23;
			$YoYStart2	= ($YoYEnd2 - $thismonth) + 1;
			$y = 0;

			for ($x = $YoYStart1; $x <= $YoYEnd1; $x++ )
			{
				if (!$y == 0)
				{
					$query1.= "+";
				}
				else
				{
					$proRataAdjustment = "-CS.msales".$x."*(1-".$proRataCoefficient.")";
				}

				$query1.= "CS.msales".$x;
				$y++;
			}

			$y = 0;

			for ($x = $YoYStart2; $x <= $YoYEnd2; $x++ )
			{
				if (!$y == 0)
				{
					$query2.= "+"; // Add a + to the end if this isnt the first time in. Want to add up all the columns in the range
				}

				$query2.= "CS.msales".$x; // Add the sales fields
				$y++;
			}

			$selectMonthWise = '';
			$yearOnYearData=", (".$query1.") as YoY1Sales, (".$query2.") AS YoY2Sales ";

			$seemarginsAr = $this->common_model->loggedin_userdetail();

			if (!!$seemarginsAr["seemargins"])
			{
				$selMarginM = 'CS.mmarginpc0 as gm_per_mtd,';
				$selMarginY = 'CS.ymarginpc0 as gm_per_ytd';
			}
			else
			{
				$selMarginM = '';
				$selMarginY = '';
			}

			$this->db->select('C.account as account, C.name as name, C.address1, C.address2, C.address3, C.address4, C.address5, CS.ysales0 as sales_ytd, ROUND('.$query1.$proRataAdjustment.', 2) as YoY1Sales, CS.ysales1, IFNULL(ROUND((CS.ysales0-('.$query1.$proRataAdjustment.'))*100/('.$query1.$proRataAdjustment.'), 2), IF(SUM(CS.ysales0) > 0.00, CAST(100.00 AS DECIMAL), CAST(0.00 AS DECIMAL))) as diff_percent, ('.$query2.') as YoY2Sales, CS.ysales2, CS.msales0 as sales_mtd, '.$selMarginM.', '.$selMarginY.', C.postcode as postcode, C.userdef1 as userdef1, C.repcode as repcode, CS.mquantity0 as qty_mtd, CS.yquantity0 as qty_ytd'.$selectMonthWise. ', CS.mcost0 as costs_mtd, CS.ycost0 as costs_ytd', false);
			$this->db->group_by('C.account');
		}
		else
		{
			$this->db->select('COUNT(C.account) as totalrows');
		}
	}

	/* Function to make the where clause */

	public function whereMonthWise() {

		$selectionYear = array();

		$selectionMonth = array();

		for ($month=0; $month<36; $month++) {

			$current_month = $month;

			$selectionMonth[] = 'LOWER(CS.mquantity'.$current_month.")";

			$selectionMonth[] = 'LOWER(CS.msales'.$current_month.")";

			$selectionMonth[] = 'LOWER(CS.mmarginpc'.$current_month.')';
		}

		for ($year=0; $year<3; $year++) {
			if ($year!=0) {

				$append = "_prev_".$year;

			} else {

				$append = "";

			}

			$selectionYear[] = 'LOWER(CS.yquantity'.$year.')';

			$selectionYear[] = 'LOWER(CS.ysales'.$year.')';

			$selectionYear[] = 'LOWER(CS.ymarginpc'.$year.')';

		}

		return array('month'=>$selectionMonth, 'year'=>$selectionYear);

	}

	public function getCustomerDetails($custId){
		$this->db->select('customer.name as customername, address1, address2, address3, address4, address5, postcode, phone, fax, email1, terms1code, terms2code, terms3code, terms4code, terms5code, creditlimit, committeddebt, potentialdebt1, potentialdebt2, potentialdebt3, creditstatus, lastpaymentdate, lastpaymentamount, dellocn, dellocndesc, salesrep.name as repname, customer.internaltext, customer.repcode, customer.userdef1, customer.userdef2, customer.userdef3, customer.currency');

		$this->db->from('customer');

		$this->db->join('salesrep', 'salesrep.repcode = customer.repcode', 'left');

		$this->db->where_in('customer.account ', $custId);

		$query = $this->db->get();

		$this->common_model->showLastQuery("getCustomerDetails");

		return $query->row_array();
	}

	public function getCustomerSAProduct($account,$startthisyearrmonth,$curyearmonth){

		$this->db->select('p.code, p.description, sum(s.mquantity0) as quantity, sum(s.msales0) as sales, sum(s.mcost0) as cost, CURDATE() as date');

		$this->db->from('product p');

		$this->db->join('customerprodsales s', "p.code = s.prodcode AND s.account = '". $account ."'", "left");

		$this->db->group_by('p.code');

		$this->db->order_by('p.code', "ASC");


		$limit = $this->config->item('limit');

		if ($limit>0) {

			$this->db->limit($limit);

		}

		$query = $this->db->get();

		$this->common_model->showLastQuery("getCustomerSAProduct");

		return $query->result_array();

	}

	public function getCustomerSalesAnalysisOrdersCount($account)
	{
		$escaped_account_string = $this->db->escape_str($account);

		$sql="select count(*) from salesanalysis where account = '".$escaped_account_string."'";
		$query = $this->db->query($sql);

		return reset($query->row_array());
	}

	public function getCustomerSalesAnalysisOrdersFilteredCount($account, $search = array())
	{
		$escaped_account_string = $this->db->escape_str($account);
		$escaped_search_string = $this->db->escape_str($search);
		
		$where = " where s.account = '".$escaped_account_string."'";

		if (!!$search && !is_array($search))
		{
			$where.= " AND (LOWER(s.orderno) LIKE LOWER('%".$escaped_search_string."%') OR LOWER(p.description) LIKE LOWER('%".$escaped_search_string."%'))";
		}

		$sql="select count(*) from salesanalysis s left join product p on p.code = s.prodcode " . $where;
		$query = $this->db->query($sql);

		return reset($query->row_array());
	}

	public function getCustomerSalesAnalysisOrders($account, $search = array(), $start = 0, $limit = null)
	{
		$escaped_account_string = $this->db->escape_str($account);
		$escaped_search_string = $this->db->escape_str($search);

		$this->db->select('s.orderno, s.date, s.prodcode, p.description, s.quantity, s.sales, s.invoiceno');
		$this->db->from('salesanalysis s');
		$this->db->join('product p', "s.prodcode = p.code", "left");

		$where = "account = '".$escaped_account_string."'";

		if (!!$search && !is_array($search))
		{
			$where.= " AND (LOWER(s.orderno) LIKE LOWER('%".$escaped_search_string."%') OR LOWER(p.description) LIKE LOWER('%".$escaped_search_string."%'))";
		}

		$this->db->where($where);
		$this->db->order_by('s.orderno', "DESC");

		if (!is_null($limit))
		{
			$this->db->limit($limit, $start);
		}

		$query = $this->db->get();
		$this->common_model->showLastQuery("getCustomerSalesAnalysisOrders");

		return $query->result_array();

	}

	public function getCustomerQuotesCount($account)
	{
		$escaped_account_string = $this->db->escape_str($account);

		$sql="select count(distinct orderno) from salesorders where account = '".$escaped_account_string."' AND ordtype = 'QT'";
		$query = $this->db->query($sql);

		return reset($query->row_array());
	}

	public function getCustomerQuotesFilteredCount($account, $search = array())
	{
		$escaped_account_string = $this->db->escape_str($account);
		$escaped_search_string = $this->db->escape_str($search);
		
		$where = " where (account = '".$escaped_account_string."' AND ordtype = 'QT')";
		
		if (!!$search && !is_array($search))
		{
			$where.= " AND (LOWER(custorderno) LIKE LOWER('%".$escaped_search_string."%') OR LOWER(quotereason) LIKE LOWER('%".$escaped_search_string."%'))";
		}

		$sql="select count(distinct orderno) from salesorders" . $where;
		$query = $this->db->query($sql);

		return reset($query->row_array());
	}

	public function getCustomerQuotes($account, $search = null, $start = 0, $limit = null)
	{
		$escaped_account_string = $this->db->escape_str($account);
		$escaped_search_string = $this->db->escape_str($search);

		$this->db->select('orderno, custorderno, quotereason, quotevalue, datein, quotefolldate, quoteexpidate');
		$this->db->from('salesorders s');

		$where = "(account = '".$escaped_account_string."' AND ordtype = 'QT')";

		if ($search)
		{
			$where.= " AND (LOWER(custorderno) LIKE LOWER('%".$escaped_search_string."%') OR LOWER(quotereason) LIKE LOWER('%".$escaped_search_string."%'))";
		}

		$this->db->where($where);
		$this->db->group_by('orderno');
		$this->db->order_by('datein', "DESC");
		
		if (!is_null($limit))
		{
			$this->db->limit($limit, $start);
		}

		$query = $this->db->get();
		$this->common_model->showLastQuery("getCustomerQuotes");

		return $query->result_array();
	}

	public function getCustomerOrders($account, $search = null, $start = 0, $limit = null)
	{
		$escaped_account_string = $this->db->escape_str($account);
		$escaped_search_string = $this->db->escape_str($search);

		$this->db->select('orderno, datein, headerdatereq, prodcode, fulldesc, quantity, unitprice, discount1, discount2, sales, datereq, status, custorderno', false);
		$this->db->from('salesorders');

		$where = "(account = '".$escaped_account_string."' AND ordtype != 'QT')";

		if ($search)
		{
			$where.= " AND (LOWER(prodcode) LIKE LOWER('%".$escaped_search_string."%') OR LOWER(fulldesc) LIKE LOWER('%".$escaped_search_string."%'))";
		}

		$this->db->where($where);
		$this->db->order_by('datein', "DESC");
		
		if (!is_null($limit))
		{
			$this->db->limit($limit, $start);
		}

		$query = $this->db->get();
		$this->common_model->showLastQuery("getCustomerOrders");
		
		return $query->result_array();
	}

	public function getCustomerTermsProduct($account)
	{
		$customer_details = $this->getCustomerDetails($account);
		$escaped_terms1code = $this->db->escape_str($customer_details["terms1code"]);
		$escaped_terms2code = $this->db->escape_str($customer_details["terms2code"]);
		$escaped_terms3code = $this->db->escape_str($customer_details["terms3code"]);
		$escaped_terms4code = $this->db->escape_str($customer_details["terms4code"]);
		$escaped_terms5code = $this->db->escape_str($customer_details["terms5code"]);
		$escaped_currency = $this->db->escape_str($customer_details["currency"]);

		$sql = 
			"select th.termtype, th.termcode, th.description as termdescription, th.effectivefrom, th.effectiveto, tp.prodcode, tp.baseprice, tp.discount1, tp.discount2, tp.nettprice, p.description
			from (termsheader th)
			join termsproduct tp on tp.termcode = th.termcode
			join product p on p.code = tp.prodcode
			where (th.termcode = '$escaped_terms1code'
			or th.termcode = '$escaped_terms2code'
			or th.termcode = '$escaped_terms3code'
			or th.termcode = '$escaped_terms4code'
			or th.termcode = '$escaped_terms5code')
			and (th.currency = '$escaped_currency' and tp.currency = '$escaped_currency')
			order by tp.prodcode asc";

		$query = $this->db->query($sql);
		$this->common_model->showLastQuery("getCustomerTermsProduct");
		return $query->result_array();
	}

	public function getCustomerTermsGroup($account)
	{
		$customer_details = $this->getCustomerDetails($account);
		$escaped_terms1code = $this->db->escape_str($customer_details["terms1code"]);
		$escaped_terms2code = $this->db->escape_str($customer_details["terms2code"]);
		$escaped_terms3code = $this->db->escape_str($customer_details["terms3code"]);
		$escaped_terms4code = $this->db->escape_str($customer_details["terms4code"]);
		$escaped_terms5code = $this->db->escape_str($customer_details["terms5code"]);
		$escaped_currency = $this->db->escape_str($customer_details["currency"]);

		$sql =
			"select th.termtype, th.termcode, th.description as termdescription, th.effectivefrom, th.effectiveto, tp.discgroupcode, tp.discount1, tp.discount2, p.description
			from termsheader th
			join termsgroup tp on tp.termcode = th.termcode
			join proddiscgroup p on p.code = tp.discgroupcode
			where (th.termcode = '$escaped_terms1code'
			or th.termcode = '$escaped_terms2code'
			or th.termcode = '$escaped_terms3code'
			or th.termcode = '$escaped_terms4code'
			or th.termcode = '$escaped_terms5code')
			and (th.currency = '$escaped_currency' and tp.currency = '$escaped_currency')
			order by tp.termtype desc";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCustomerSales($account){

		$this->db->select('customer.name, address1, address2, address3, address4, address5, postcode, phone, fax, email1, terms1code, terms2code, terms3code, terms4code, terms5code, creditlimit, committeddebt, potentialdebt1, potentialdebt2, potentialdebt3, creditstatus, lastpaymentdate, lastpaymentamount, dellocn, dellocndesc, salesrep.name, customer.internaltext');

		$this->db->from('customer');

		$this->db->join('salesrep','salesrep.repcode = customer.repcode');

		$this->db->where('customer.account',$account);

		$query = $this->db->get();

		$this->common_model->showLastQuery("getCustomerSales");

		return $query->row_array();

	}

	public function getSalesAccount($account, $startyearmonth, $curyearmonth)
	{
		$this->load->model('common_model');
		$select = array();

		for ($i = 0; $i < 36; $i++)
		{
			$select[] = 'SUM(msales'.$i.') as m'.$i;
		}

		$this->db->select(join($select, ", "));
		$this->db->from('customersales');
		$this->db->where('account', $account);
		$query = $this->db->get();
		$str = $query->result_array();
		$modified_array = $this->common_model->array_modification($str);

		return $modified_array[0];
	}

	public function getAgeTransactionCount($account)
	{
		$escaped_account_string = $this->db->escape_str($account);

		$sql="select count(*) from agedtrans where account = '".$escaped_account_string."'";
		$query = $this->db->query($sql);

		return reset($query->row_array());
	}

	public function getAgeTransactionFilteredCount($account, $search = array())
	{
		$escaped_account_string = $this->db->escape_str($account);
		$escaped_search_string = $this->db->escape_str($search);
		
		$where = " where (account = '".$escaped_account_string."')";
		
		if (!!$search && !is_array($search))
		{
			$where.= " AND (LOWER(docnumber) LIKE LOWER('%".$escaped_search_string."%') OR LOWER(custref) LIKE LOWER('%".$escaped_search_string."%'))";
		}

		$sql="select count(*) from agedtrans" . $where;
		$query = $this->db->query($sql);

		return reset($query->row_array());
	}

	public function getAgeTransaction($account, $search = array(), $start = 0, $limit = null)
	{
		$escaped_account_string = $this->db->escape_str($account);
		$escaped_search_string = $this->db->escape_str($search);

		$this->db->select('docdate, docnumber, custref, otherref, docstatus, doctype, duedate, totalamount, paidamount, outstandamount, collectamount, overdueamount');
		$this->db->from('agedtrans');

		$where = "(account = '".$escaped_account_string."')";

		if (!!$search && !is_array($search))
		{
			$where.= " AND (LOWER(docnumber) LIKE LOWER('%".$escaped_search_string."%') OR LOWER(custref) LIKE LOWER('%".$escaped_search_string."%'))";
		}

		$this->db->where($where);
		$this->db->order_by('docdate', 'ASC');
		
		if (!is_null($limit))
		{
			$this->db->limit($limit, $start);
		}

		$query = $this->db->get();
		$this->common_model->showLastQuery("getAgeTransaction");

		return $query->result_array();
	}

	public function getAgeMonthList($account, $agedmonthstartdate, $agedmonthenddate){
		$this->db->select('ag.docdate, ag.docnumber, ag.custref, ag.otherref, ag.docstatus, ag.doctype, ag.duedate, ag.totalamount, ag.paidamount, ag.outstandamount, ag.collectamount, ag.overdueamount, sai.orderno, sai.datein, sai.prodcode, sai.fulldesc, sai.quantity, sai.unitprice, sai.discount1, sai.discount2, sai.sales, sai.vat, sai.custorderno');

		$this->db->from('agedtrans as ag');

		$this->db->join('salesinvoices as sai','sai.account = ag.account AND sai.docnumber = ag.docnumber', 'left');

		$this->db->where('ag.account',$account);

		$this->db->where('ag.docdate >=',$agedmonthstartdate);

		$this->db->where('ag.docdate <=',$agedmonthenddate);

		$this->db->where('ag.outstandamount >','0');

		$this->db->order_by('ag.docdate', 'ASC');

		$query = $this->db->get();

		$this->common_model->showLastQuery("getAgeMonthList");
		return $query->result_array;
	}

	public function getCustomMonthDataForCustomer($account, $agedmonthstartdate, $agedmonthenddate){
		$this->db->select('agedtrans.docdate, agedtrans.docnumber, agedtrans.custref, agedtrans.otherref, agedtrans.docstatus, agedtrans.doctype, agedtrans.duedate, agedtrans.totalamount, agedtrans.paidamount, agedtrans.outstandamount, agedtrans.collectamount, agedtrans.overdueamount, salesinvoices.orderno, salesinvoices.datein, salesinvoices.prodcode, salesinvoices.fulldesc, salesinvoices.quantity, salesinvoices.unitprice, salesinvoices.discount1, salesinvoices.discount2, salesinvoices.sales, salesinvoices.vat, salesinvoices.custorderno');
		$this->db->from('agedtrans');
		$this->db->join('salesinvoices', 'salesinvoices.account = agedtrans.account AND salesinvoices.docnumber = agedtrans.docnumber', 'left');
		$this->db->where('agedtrans.account', $account);
		$this->db->where('agedtrans.docdate >=', $agedmonthstartdate);
		$this->db->where('agedtrans.docdate <=', $agedmonthenddate);
		$this->db->where('agedtrans.outstandamount >', '0');
		$this->db->order_by('agedtrans.docdate', 'ASC');

		$query = $this->db->get();
		$this->common_model->showLastQuery("getAgeMonthList");
		return $query->result_array();
	}

	public function getSAChart($account, $levelclause, $startyearmonth, $curyearmonth){

		$this->db->select('yearmonth, SUM(sales) as sales');

		$this->db->from('salesanalysis');

		$this->db->join("customer","customer.account = salesanalysis.account");

		$this->db->join('product','product.code = salesanalysis.prodcode AND '.$levelclause);

		$this->db->where('customer.account', $account);

		$this->db->where('salesanalysis.yearmonth >=',$startyearmonth);

		$this->db->where('salesanalysis.yearmonth <=',$curyearmonth);

		$this->db->or_where('yearmonth ', null);

		$this->db->group_by('yearmonth');

		$this->db->order_by('yearmonth', 'ASC');

		$query = $this->db->get();

		$this->common_model->showLastQuery("getSAChart");

		return $query->result_array();
	}

	public function updateInternalText($account, $newVal){
		//Getting old value

		$query = $this->db->select('account,name,internaltext')->from('customer')->where('account',$account)->get()->row();

		$odVal = $query->internaltext;

		//update internal text

		$data = array( 'internaltext' => $newVal );

		$this->db->where('account',$account);

		$this->db->update('customer', $data);

		$str = "Customer ".$query->account." - ".$query->name." text changed from $odVal to $newVal";

		$data = array(
		   'userid' => $this->session->userdata('userid'),
		   'description' => $str ,
		   'date' => date('Y-m-d'),
		   'type' => 'U'
		);
		$this->db->insert('systemlog', $data);
	}

	public function updateGroupDiscount($table, $column, $keycolumn, $keydata, $newVal){

		//Getting old value

		$query = array_values( $this->db->select($column)->from($table)->where($keycolumn,$keydata)->get()->row_array() );

		$odVal = $query[0];

		//update the values

		$data = array( $column => $newVal );

		$this->db->where($keycolumn,$keydata);

		$this->db->update($table, $data);

		// Get the key columns needed for the CSV file and systemlog needed to ascii import into K8

		$this->db->select('a.termcode, a.discgroupcode, a.termtype, p.description, c.name');

		$this->db->from('termsgroup a');

		$this->db->join('proddiscgroup p','a.discgroupcode=p.code','left');

		$this->db->join('customer c','a.termcode=c.account','left');

		$this->db->where('a.unique',$keydata);

		$row = $this->db->get()->row_array();

		$termcode  = $row['termcode'];

		$discgroupcode  = $row['discgroupcode'];

		$termtype  = $row['termtype'];

		$pdescription = $row['description'];

		$cname = $row['name'];

		$discnt = ($column=='discount1')?"Discount 1":"Discount 2";

		$str = "Customer $termcode - $cname, Group Term $termtype/$termcode, Group $discgroupcode - $pdescription $discnt changed from $odVal to $newVal";

		$data = array(
		   'userid' => $this->session->userdata('userid'),
		   'description' => $str ,
		   'date' => date('Y-m-d'),
		   'type' => 'U'
		);

		$this->db->insert('systemlog', $data);

		//content for saving in csv

		$str2['termcode']=$termcode;

		$str2['discgroupcode']=$discgroupcode;

		$str2['termtype']=$termtype;

		return $str2;
	}

	public function updateProductDiscount($table, $column, $keycolumn, $keydata, $newVal, $netPrice){
		//Getting old value

		$query = array_values( $this->db->select($column)->from($table)->where($keycolumn,$keydata)->get()->row_array() );

		$odVal = $query[0];;

		//update the values

		$data = ($netPrice!='')?array( $column => $newVal,'nettprice' => $netPrice ):array( $column => $newVal );

		$this->db->where($keycolumn,$keydata);

		$this->db->update($table, $data);

		// Get the key columns needed for the CSV file and systemlog needed to ascii import into K8

		$this->db->select('a.termcode, a.prodcode, a.termtype, a.baseprice, p.description, c.name');

		$this->db->from('termsproduct a');

		$this->db->join('product p','a.prodcode=p.code','left');

		$this->db->join('customer c','a.termcode=c.account','left');

		$this->db->where('a.unique',$keydata);

		$row = $this->db->get()->row_array();

		extract($row);

		$discnt = ($column=='discount1')?"Discount 1":"Discount 2";

		$str = "Customer ".$termcode." – ".$name.", Product Term ".$termtype."/".$termcode.", Product ".$prodcode." - ".$description." $discnt changed from $odVal to $newVal";

		$data = array(
		   'userid' => $this->session->userdata('userid'),
		   'description' => $str ,
		   'date' => date('Y-m-d'),
		   'type' => 'U'
		);

		$this->db->insert('systemlog', $data);

		//content for saving in csv

		$str2['termcode']=$termcode;

		$str2['prodcode']=$prodcode;

		$str2['termtype']=$termtype;
		return $str2;
	}

	/* Function to create a file to be exported in CSV or EXCEL */

	public function csv_export($repwhere, $startyearmonth, $curyearmonth, $search_key, $search, $specific_search_keys, $canSeeMargins = 0)
	{
		$specific_search = $this->makeSpecificSearch($search, $specific_search_keys);

		if ("nosearchedvalue" == $search_key)
		{
			$search_key = "";
		}

		$repWhereCondition = $this->common_model->makeRepcodeConditionNew($repwhere, 'C.repcode', true, true, true);

		$this->baseCustomerSalesQuery_csv($repWhereCondition, $startyearmonth, $curyearmonth, $search_key, $specific_search, false, $canSeeMargins);

		$this->db->order_by("CS.ysales0", "desc");
		$query = $this->db->get();
		$this->load->dbutil();
		$opt = $this->dbutil->csv_from_result($query);

		$curryear = date('Y');
		$saleyear1 = "Sales ".($curryear - 1);
		$saleyear2 = "Sales ".($curryear - 2);
		$head_value1 = array('""');
		$new_head1 = array('"0.00"');

		/*
			Account, Name, Sales YTD, Sales Last Year (2018), Diff % (from step 2), Sales 2017, Sales MTD, GM% MTD (if visible), GM% YTD (if visible), Post Code and User Def. 1? I’m taking the Qty MTD and Qty YTD columns off the list
		*/
		$head_value1 = array('account', 'name', 'sales_ytd', 'YoY1Sales', 'ysales1', 'diff_percent', 'YoY2Sales', 'ysales2', 'sales_mtd', 'gm_per_mtd', 'gm_per_ytd', 'postcode', 'userdef1', 'repcode', 'qty_mtd', 'qty_ytd');
		$new_head1 = array("Account", "Name", "Sales YTD", $saleyear1 . " YTD", $saleyear1, "Diff %", $saleyear2 . " YTD", $saleyear2, "Sales MTD", "GM% MTD", "GM% YTD", "Post Code","User Def 1", "Rep Code", "QTY MTD", "QTY YTD");
		
		echo $opt = str_replace($head_value1, $new_head1, $opt);
	}

	/* Function to create a file to be exported in CSV or EXCEL for customer products */

	public function csv_export_customer_products($account, $search_key,$specific_search) {
		$this->baseCustomerProductSalesQuery($account, $search_key,$specific_search);

		$query = $this->db->get();

		$this->load->dbutil();

		$opt=$this->dbutil->csv_from_result($query);
		$head_value = array("code","cpac4", "description", "salemtd","quantitymtd","marginpcmtd", "ysalesytd", "yquantityytd","ymarginpcytd","YoY1Sales","YoY2Sales");
		$curryear=date('Y');
		$saleyear1="Sales ".($curryear-1);
		$saleyear2="Sales ".($curryear-2);
		$new_head   =array("Code","PAC4", "Description","Sales MTD","Qty MTD","GM% MTD","Sales YTD","Qty YTD","GM% YTD",$saleyear1,$saleyear2);
		$head_value1 = array('""');

		$new_head1   =array('"0.00"');

		$opt = str_replace($head_value, $new_head, $opt);

		echo $opt = str_replace($head_value1, $new_head1, $opt);
	}

	/* Function to create a file to be exported in CSV or EXCEL for customer products */

	public function csv_export_sales_target($search_key,$specific_search, $G_level,$userid=0,$branchNo=0,$repclause, $account, $yearmonthposted) {
		$query = $this->salesTrgetQuery($G_level,$userid=0,$branchNo=0,$repclause, $account, $yearmonthposted, $ShowProgress = true);
		$query = $this->db->query($query);
		$this->load->dbutil();
		$opt=explode("||", $this->dbutil->csv_from_result($query, "|", "|"));
		$reStructuredRow = array();
		$reStructured = array();
		$restrStr = array();
		foreach ($opt as $op) {
			$o = explode("|", $op);
			if (!empty($o[0])) {
				$progress = $o[0];
				$tabl = $o[1];
				$ytd = $o[2];
				$ytp = $o[3];
				$ytpp = $o[4];
				$description = $o[5];
				$paccode = $o[6];
				$salestarget = $o[7];
				$reStructuredRow[0] = $tabl;
				$reStructuredRow[1] = $paccode;
				$reStructuredRow[2] = $description;
				$reStructuredRow[3] = $ytp;
				$reStructuredRow[4] = $ytpp;
				$reStructuredRow[5] = $ytd;
				$reStructuredRow[6] = $salestarget;
				$reStructuredRow[7] = $progress;

				array_push($restrStr, join($reStructuredRow, ", "));
			}
			$opt = str_replace('"', '',(join($restrStr, "\n")));
		}
		$head_value = array("tabl","ytd","ytpp","ytp","description","paccode","salestarget", "progress");
		$curryear=date('Y');
		$saleyear1="Sales ".($curryear-1);
		$saleyear2="Sales ".($curryear-2);
		$new_head   =array("Type","Sales YTD",$saleyear2,$saleyear1,"Description","Code", "Target", "Progress");
		$head_value1 = array('""');

		$new_head1   =array('"0.00"');

		$opt = str_replace($head_value, $new_head, $opt);

		echo $opt = str_replace($head_value1, $new_head1, $opt);
	}

	/* Function to make the specific search array as required */

	public function makeSpecificSearch($search, $specific_search_keys) {
		$specific_search = array();
		foreach ($search as $k=>$s) {
			if ($k) {
				if ("nosearchedvalue"==$s) {
					$specific_search[$specific_search_keys[($k-1)]] = "";
				} else {
					$specific_search[$specific_search_keys[($k-1)]] = $s;
				}
			}
		}
		return $specific_search;
	}

	public function customerGraph($num, $account, $pac1, $code)
	{
		if ($pac1=="pac1") {
			$table="customerpac1sales";
			$pcode="pac1code";
		}
		if ($pac1=="pac2") {
			$table="customerpac2sales";
			$pcode="pac2code";
		}
		if ($pac1=="pac3") {
			$table="customerpac3sales";
			$pcode="pac3code";
		}
		if ($pac1=="pac4") {
			$table="customerpac4sales";
			$pcode="pac4code";
		}

		$query = "SELECT ";

		$y = 0;

		for ($x = $num; $x >= 0; $x-- ) {
			if (!$y == 0) {
				$query.= ", ";	// Add a comma to the end if this isnt the first time in
			}
			$query.= "msales$x";	// Add the sales field
			$y++;
		}

		$query.= " FROM ".$table." WHERE account LIKE '$account' AND ".$pcode." LIKE '$code'";
		$query = $this->db->query($query);
		$result = $query->result_array();

		return $result;
	}

	public function customerGraphQuantities($num, $account, $pac1, $code)
	{
		if ($pac1=="pac1") {
			$table="customerpac1sales";
			$pcode="pac1code";
		}
		if ($pac1=="pac2") {
			$table="customerpac2sales";
			$pcode="pac2code";
		}
		if ($pac1=="pac3") {
			$table="customerpac3sales";
			$pcode="pac3code";
		}
		if ($pac1=="pac4") {
			$table="customerpac4sales";
			$pcode="pac4code";
		}

		$query = "SELECT ";

		$y = 0;

		for ($x = $num; $x >= 0; $x-- ) {
			if (!$y == 0) {
				$query.= ", ";	// Add a comma to the end if this isnt the first time in
			}
			$query.= "mquantity$x";	// Add the quantity field
			$y++;
		}

		$query.= " FROM ".$table." WHERE account LIKE '$account' AND ".$pcode." LIKE '$code'";
		$query = $this->db->query($query);
		$result = $query->result_array();

		return $result;
	}

	// Customer Graph Sales analyis //

	public function customergarphsales($num, $account, $code = null)
	{
		$table="customersales";
		$pcode="prodcode";
		$query = "SELECT ";
		$y = 0;
		for ($x = $num; $x >= 0; $x-- ) {
			if (!$y == 0) {
				$query.= ", ";	// Add a comma to the end if this isnt the first time in
			}
			$query.= "msales$x";	// Add the sales field
			$y++;
		}

		$query.= " FROM ".$table." WHERE account LIKE '$account'";
		$query = $this->db->query($query);
		$result = $query->result_array();
		return $result;
	}

	public function customerGraphProductSales($num, $account, $code)
	{
		$table="customerprodsales";
		$pcode="prodcode";
		$query = "SELECT ";
		$y = 0;

		for ($x = $num; $x >= 0; $x-- )
		{
			if (!$y == 0)
			{
				$query.= ", ";	// Add a comma to the end if this isnt the first time in
			}

			$query.= "msales$x";	// Add the sales field
			$y++;
		}

		$query.= " FROM ".$table." WHERE account LIKE '$account' AND prodcode LIKE '".$code."'";
		$query = $this->db->query($query);
		$result=$query->result_array();

		return $result;
	}

	public function customerGraphProductQuantities($num, $account, $code)
	{
		$table="customerprodsales";
		$pcode="prodcode";
		$query = "SELECT ";
		$y = 0;

		for ($x = $num; $x >= 0; $x-- )
		{
			if (!$y == 0)
			{
				$query.= ", ";	// Add a comma to the end if this isnt the first time in
			}
			
			$query.= "mquantity$x";	// Add the quantity field
			$y++;
		}

		$query.= " FROM ".$table." WHERE account LIKE '$account' AND prodcode LIKE '".$code."'";
		$query = $this->db->query($query);
		$result=$query->result_array();

		return $result;
	}

	public function getCustomerPACDetailsWithGroupByModified($account, $PAC, $startthisyearrmonth, $curyearmonth, $proRataCoefficient = 1)
	{
		$proRataAdjustment = "";

		$year0 = date("Y");
		$year1 = $year0 - 1;
		$year2 = $year0 - 2;
		$thismonth = date("m");
		$YoYEnd1 = $thismonth + 11;
		$YoYStart1	= ($YoYEnd1 - $thismonth) + 1;
		$YoYEnd2 = $thismonth + 23;
		$YoYStart2 = ($YoYEnd2 - $thismonth) + 1;
		$y = 0;

		for ($x = $YoYStart1; $x <= $YoYEnd1; $x++)
		{
			if (!$y == 0)
			{
				$query1.= "+";
				$queryq1.= "+";
			}
			else
			{
				$proRataAdjustment = ", -s.msales".$x."*(1-".$proRataCoefficient.") AS YoY1ProRataAdjustment";
			}

			$query1.= "s.msales".$x;
			$queryq1.= "s.mquantity".$x;

			$y++;
		}

		$y = 0;

		for ($x = $YoYStart2; $x <= $YoYEnd2; $x++)
		{
			if (!$y == 0)
			{
				$query2.= "+";
				$queryq2.= "+";
			}

			$query2.= "s.msales".$x;
			$queryq2.= "s.mquantity".$x;

			$y++;
		}

		$yearOnYearData=", (".$query1.") as YoY1Sales, (".$query2.") AS YoY2Sales, (".$queryq1.") as YoY1Qty, (".$queryq2.") as YoY2Qty".$proRataAdjustment;
		$this->db->select('p.code, p.description, s.msales0 as salesmtd, s.mquantity0 as qtymtd, ((s.mmargin0*100)/s.msales0) as marginmtdpc, s.ysales0 as salesytd, s.yquantity0 as qtyytd,  ((s.ymargin0*100)/s.ysales0) as marginytdpc, s.mcost0 as costsmtd, s.ycost0 as costsytd'.$yearOnYearData, false);

		$this->db->from($PAC.' p');

		$this->db->join("customer".$PAC."sales s", "p.code = s.".$PAC."code AND s.account = '". $account ."'", "left");
		$this->db->group_by('p.code');
		$this->db->order_by('p.code', "ASC");

		$query = $this->db->get();

		$result_array = $query->result_array();

		$this->common_model->showLastQuery("getCustomerPACDetailsWithoutGroupBy");

		return $result_array;
	}

	public function prd_csv_export_mycustom($account,$PAC,$startthisyearrmonth,$curyearmonth) {
		$this->load->model('common_model');
		$seemarginsAr = $this->common_model->loggedin_userdetail();
		$year0 = date("Y");
		$year1 = $year0 - 1;
		$year2 = $year0 - 2;
		$thismonth = date("m");
		$YoYEnd1 = $thismonth + 11;
		$YoYStart1	= ($YoYEnd1 - $thismonth) + 1;
		$YoYEnd2 = $thismonth + 23;
		$YoYStart2	= ($YoYEnd2 - $thismonth) + 1;
		$y = 0;
		for ($x = $YoYStart1; $x <= $YoYEnd1; $x++ )
		{
			if (!$y == 0)
			{
				$query1.= "+";	// Add a + to the end if this isnt the first time in. Want to add up all the columns in the range
			}
			$query1.= "`s`.`msales".$x."`";	// Add the sales field
			$y++;
		}

		$y = 0;
		for ($x = $YoYStart2; $x <= $YoYEnd2; $x++ )
		{
			if (!$y == 0)
			{
				$query2.= "+";	// Add a + to the end if this isnt the first time in. Want to add up all the columns in the range
			}
			$query2.= "`s`.`msales".$x."`";	// Add the sales fields
			$y++;
		}
		$yearOnYearData=", (".$query1.") as YoY1Sales, (".$query2.") AS YoY2Sales ";

		if (!!$seemarginsAr["seemargins"]) {
			$selMarginM = '((s.mmargin0*100)/s.msales0) as marginmtdpc,';
			$selMarginY = ',  ((s.ymargin0*100)/s.ysales0) as marginytdpc';
		} else {
			$selMarginM = '';
			$selMarginY = '';
		}

		$this->db->select('p.code, p.description, s.msales0 as salesmtd, s.mquantity0 as qtymtd, '.$selMarginM.' s.ysales0 as salesytd, s.yquantity0 as qtyytd'.$selMarginY.$yearOnYearData);

		$this->db->from($PAC.' p');
		$this->db->join("customer".$PAC."sales s", "p.code = s.".$PAC."code AND s.account = '". $account ."'", "left");
		$this->db->group_by('p.code');
		$this->db->order_by('p.code', "ASC");
		$query = $this->db->get();
		$this->load->dbutil();
		$opt=$this->dbutil->csv_from_result($query);
		$curryear=date('Y');
		$saleyear1="Sales ".($curryear-1);
		$saleyear2="Sales ".($curryear-2);
		$head_value = array("code","description", "salesmtd", "qtymtd","marginmtdpc","salesytd", "qtyytd", "marginytdpc","YoY1Sales","YoY2Sales");
		$new_head   =array("Code", "Description", "Sales MTD","Qty MTD","GM% MTD","Sales YTD","Qty YTD","GM% YTD",$saleyear1,$saleyear2);
		$head_value1 = array('""');
		$new_head1   =array('"0.00"');
		$opt = str_replace($head_value, $new_head, $opt);
		echo $opt = str_replace($head_value1, $new_head1, $opt);
		die;
	}

	public function searchCodes(){
		$codes = array();
		$this->db->select('p.code, p.description');
		$this->db->from('pac1 p');
		$this->db->order_by('p.code', "ASC");
		$query1 = $this->db->get();
		$result1 = $query1->result_array();

		$this->db->select('p.code, p.description');
		$this->db->from('pac2 p');
		$this->db->order_by('p.code', "ASC");
		$query2 = $this->db->get();
		$result2 = $query2->result_array();

		$this->db->select('p.code, p.description');
		$this->db->from('pac3 p');
		$this->db->order_by('p.code', "ASC");
		$query3 = $this->db->get();
		$result3 = $query3->result_array();

		$this->db->select('p.code, p.description');
		$this->db->from('pac4 p');
		$this->db->order_by('p.code', "ASC");
		$query4 = $this->db->get();
		$result4 = $query4->result_array();

		$codes["1"] = array_map("productCode", $result1);
		$codes["2"] = array_map("productCode", $result2);
		$codes["3"] = array_map("productCode", $result3);
		$codes["4"] = array_map("productCode", $result4);
		return $codes;

	}

	public function searchProductCodes($searchKey){
		$codes = array();

		$this->db->select('p.code, p.description');
		$this->db->from('product p');
		$this->db->where('p.code like "%'.strtoupper($searchKey).'%"');
		$this->db->order_by('p.code', "ASC");
		$query5 = $this->db->get();
		$result5 = $query5->result_array();
		$codes = array_map("productCode", $result5);
		return $codes;

	}

	public function addCustomerUniqueTargetcsv($insertData){
		$pac0Data = $insertData["pac0"];
		$pac1Data = $insertData["pac1"];
		$pac2Data = $insertData["pac2"];
		$pac3Data = $insertData["pac3"];
		$pac4Data = $insertData["pac4"];
		$productData = $insertData["product"];
		$insertPac0 = $this->makeInsertString("0", $pac0Data);
		$insertPac1 = $this->makeInsertString("1", $pac1Data);
		$insertPac2 = $this->makeInsertString("2", $pac2Data);
		$insertPac3 = $this->makeInsertString("3", $pac3Data);
		$insertPac4 = $this->makeInsertString("4", $pac4Data);
		$insertProduct = $this->makeInsertString("P", $productData);
		try {
			$inserted["pac1"] = !!$insertPac1?$this->db->query($insertPac1):null;
		} catch(Exception $e) {
			echo $e->getMessage();
		}

		try {
			$inserted["pac2"] = !!$insertPac2?$this->db->query($insertPac2):null;
		} catch(Exception $e) {
			echo $e->getMessage();
		}

		try {
			$inserted["pac3"] = !!$insertPac3?$this->db->query($insertPac3):null;
		} catch(Exception $e) {
			echo $e->getMessage();
		}

		try {
			$inserted["pac4"] = !!$insertPac4?$this->db->query($insertPac4):null;
		} catch(Exception $e) {
			echo $e->getMessage();
		}

		try {
			$inserted["product"] = !!$insertProduct?$this->db->query($insertProduct):null;
		} catch(Exception $e) {
			echo $e->getMessage();
		}

		try {
			$inserted["pac0"] = !!$insertPac0?$this->db->query($insertPac0):null;
		} catch(Exception $e) {
			echo $e->getMessage();
		}

		return $inserted;
	}

	public function makeInsertString($type, $data) {
		if ("P"!=$type) {
			if (0!=$type) {
				$tableMid = "pac".$type;
			} else {
				$tableMid = "";
			}

		} else {
			$tableMid = "product";
		}
		if (!!$tableMid) {
			$insert = "INSERT INTO `customer".$tableMid."salestarget` (`id`, `account`, `".$tableMid."code`, `yearmonth`, `salestarget`) VALUES ";
		} else {
			$insert = "INSERT INTO `customer".$tableMid."salestarget` (`id`, `account`, `yearmonth`, `salestarget`) VALUES ";
		}

		$values = array();
		for ($index=0; $index<sizeof($data); $index++) {
			$duplicate = $this->isDuplicate($type, $data[$index]);
			if (!$duplicate) {
				if (!!$tableMid) {
					$values[$index] = "(NULL, '".$data[$index]["account"]."', '".$data[$index][$tableMid."code"]."', '".$data[$index]["yearmonth"]."', '".$data[$index]["salestarget"]."')";
				} else {
					$values[$index] = "(NULL, '".$data[$index]["account"]."', '".$data[$index]["yearmonth"]."', '".$data[$index]["salestarget"]."')";
				}
			}
		}
		if (sizeof($values)>0) {
			return $insert.implode(",", $values);
		} else {
			return null;
		}

	}

	public function isDuplicate($type, $data) {
		if ("P"!=$type) {
			if (0!=$type) {
				$type = "pac".$type;
			} else {
				$type = "";
			}

		} else {
			$type = "product";
		}
		$this->db->select('id');
		$this->db->from("customer".$type."salestarget");
		$this->db->where('account', $data["account"]);
		if (""!=$type) {
			$this->db->where($type."code", $data[$type."code"]);
		}

		$this->db->where('yearmonth', $data["yearmonth"]);
		$query = $this->db->get();
		$num = $query->num_rows();
		return intval($num)>0;
	}

	public function addCustomerUniqueTarget($postedData)
	{
		$OriginalCodetype = $postedData['codetype'];

		if ("P" != $postedData['codetype'])
		{
			if (0 == $postedData['codetype'])
			{
				$postedData['codetype'] = "";
			}
			else
			{
				$postedData['codetype'] = "pac".$postedData['codetype'];
			}
		}
		else
		{
			$postedData['codetype'] = "product";
		}

		$table = "customer".$postedData['codetype']."salestarget";
		$data['account'] = base64_decode($postedData['account']);

		if ("" != $postedData['codetype'])
		{
			$data[$postedData['codetype']."code"] = $postedData['code'];
		}

		$data['yearmonth'] = $postedData['year'].$postedData['month'];
		$data['salestarget'] = $postedData['salestarget'];

		$duplicate = $this->isDuplicate($OriginalCodetype, $data);

		if (!$duplicate)
		{
			$inserted = $this->db->insert($table, $data);

			return $inserted ? "completed" : "error";
		}
		else
		{
			return "duplicate";
		}
	}

	public function customerTargetsData($mainUserEditAccess, $account)
	{
		$sql = 'SELECT "1" AS codetype, `id`, `pac1code` AS code, `yearmonth`, `salestarget` FROM `customerpac1salestarget` WHERE `account` =  "'.$account.'" UNION SELECT "2" AS codetype, `id`, `pac2code` AS code, `yearmonth`, `salestarget` FROM `customerpac2salestarget` WHERE `account` =  "'.$account.'" UNION SELECT "3" AS codetype, `id`, `pac3code` AS code, `yearmonth`, `salestarget` FROM `customerpac3salestarget` WHERE `account` =  "'.$account.'" UNION SELECT "4" AS codetype, `id`, `pac4code` AS code, `yearmonth`, `salestarget` FROM `customerpac4salestarget` WHERE `account` =  "'.$account.'" UNION SELECT "P" AS codetype,  `id`,`productcode` AS code, `yearmonth`, `salestarget` FROM `customerproductsalestarget` WHERE `account` =  "'.$account.'" UNION SELECT "0" AS codetype,  `id`, "All" AS code, `yearmonth`, `salestarget` FROM `customersalestarget` WHERE `account` =  "'.$account.'"';

		$result = $this->db->query($sql)->result_array();

		if ($mainUserEditAccess)
		{
			$result = array_map("addDeleteActionableResource", $result);
		}
		else
		{
			$result = array_map("removeIdActionableResource", $result);
		}

		return $result;
	}

	public function deleteCustomerTargetsData($type, $id) {
		if ("P"!=$type) {
			if (0!=$type) {
				$type = "pac".$type;
			} else {
				$type = "";
			}

		} else {
			$type = "product";
		}
		return $this->db->delete("customer".$type."salestarget",  array('id' => $id));
	}

	public function getAccountTargetDataForYears($account, $yearsSalesData)
	{
		$yearMonthSearchArray = array();
		$result = array();

		if (!is_array($yearsSalesData))
		{
			return $result;
		}

		foreach ($yearsSalesData as $year => $salesData)
		{
			$salesDataArray = explode(",", rtrim(ltrim($salesData, "["), "]"));
			$salesCum = 0;

			for ($i = 1; $i <= 12; $i++)
			{ 
				$pre = $i < 10 ? "0" : "";

				$yearMonthSearchArray[] = $year.$pre.$i;
				$result[$year][$pre.$i]['sales'] = floatval($salesDataArray[$i - 1]);
				$result[$year][$pre.$i]['target'] = 0;
				$result[$year][$pre.$i]['salesVsTargetPercent'] = 0;
				$result[$year][$pre.$i]['salesCum'] = $salesCum+= floatval($salesDataArray[$i - 1]);
				$result[$year][$pre.$i]['targetCum'] = 0;
				$result[$year][$pre.$i]['salesCumVsTargetPercent'] = 0;
			}

			$result[$year]['total']['sales'] = $salesCum;
			$result[$year]['total']['target'] = 0;
			$result[$year]['total']['salesVsTargetPercent'] = 0;
		}

		$this->db->select("yearmonth, salestarget");
		$this->db->from("customersalestarget");
		$this->db->where("account", $account);
		$this->db->where_in("yearmonth", $yearMonthSearchArray);
		$this->db->order_by("yearmonth");

		$query = $this->db->get();
		$rows = $query->result_array();

		foreach ($rows as $row)
		{
			$rowYear = substr($row['yearmonth'], 0, 4);
			$rowMonth = substr($row['yearmonth'], 4, 2);

			$result[$rowYear][$rowMonth]['target'] = floatval($row['salestarget']);
			$result[$rowYear]['total']['target']+= floatval($row['salestarget']);
		}

		foreach ($result as $year => $yearData)
		{
			$targetCum = 0;

			foreach ($yearData as $month => $monthData)
			{
				if ($monthData['target'] == 0)
				{
					$result[$year][$month]['salesVsTargetPercent'] = null;
				}
				else
				{
					$result[$year][$month]['salesVsTargetPercent'] = $result[$year][$month]['sales'] / $result[$year][$month]['target'] * 100;
				}

				$result[$year][$month]['targetCum'] = $targetCum+= $result[$year][$month]['target'];

				if ($targetCum == 0)
				{
					$result[$year][$month]['salesCumVsTargetPercent'] = null;
				}
				else
				{
					$result[$year][$month]['salesCumVsTargetPercent'] = $result[$year][$month]['salesCum'] / $result[$year][$month]['targetCum'] * 100;
				}
			}

			$result[$year]['total']['targetCum'] = $result[$year]['total']['target'];
			$result[$year]['total']['salesCumVsTargetPercent'] = $result[$year]['total']['salesVsTargetPercent'];
		}

		return $result;
	}

	public function salesTrgetQuery($G_level,$userid=0,$branchNo=0,$repclause, $account, $yearmonthposted, $ShowProgress=false) {
		$salesColumnAdd = $this->sa("customersales");
		$colSalesYTD = $salesColumnAdd["SalesYTD"];
		$colPSalesYTD = $salesColumnAdd["SalesPYTD"];
		$colPPSalesYTD = $salesColumnAdd["SalesPPYTD"];

		$colSalesYTDQryPart =  " ( ".join(" + ",$colSalesYTD)." ) ";
		$colPSalesYTDQryPart =  " ( ".join(" + ",$colPSalesYTD)." ) ";
		$colPPSalesYTDQryPart =  " ( ".join(" + ",$colPPSalesYTD)." ) ";

		if (!!$yearmonthposted) {
			$yearmonth=$yearmonthposted;
		} else {
			$yearmonth=date("Y").date("m");
		}
		$query = "";
		$customerPrefix = "customer";
		$customerAndCondition = " AND ";
		$leftJoins["Company"] = array();

		$leftJoins["Company"][0] = array(
			"select" => $colSalesYTDQryPart." as ytd, ".$colPSalesYTDQryPart." as ytp,  ".$colPPSalesYTDQryPart." as ytpp",
			"join" => " LEFT JOIN customersales ON (customersales.account=customersalestarget.account) "
		);
		if (!$ShowProgress) {
			$progress_col[0] = "";
		} else  {
			$progress_col[0] = "((".$colSalesYTDQryPart.")*100)/(sum(".$customerPrefix."salestarget.salestarget)) as progress,";
		}

		for ($i=1; $i<=4; $i++) {
			$salesColumnAdd = $this->sa("customerpac".$i."sales");
			$colSalesYTD = $salesColumnAdd["SalesYTD"];
			$colPSalesYTD = $salesColumnAdd["SalesPYTD"];
			$colPPSalesYTD = $salesColumnAdd["SalesPPYTD"];

			$colSalesYTDQryPart =  " ( ".join(" + ",$colSalesYTD)." ) ";
			$colPSalesYTDQryPart =  " ( ".join(" + ",$colPSalesYTD)." ) ";
			$colPPSalesYTDQryPart =  " ( ".join(" + ",$colPPSalesYTD)." ) ";

			$leftJoins["Company"][$i] = array("select" => $colSalesYTDQryPart." as ytd, ".$colPSalesYTDQryPart." as ytp, ".$colPPSalesYTDQryPart." as ytpp",
				"join" => " LEFT JOIN customerpac".$i."sales ON (customerpac".$i."sales.account='".$account."' AND customerpac".$i."sales.pac".$i."code=pac".$i.".code) "
			);

			if (!$ShowProgress) {
				$progress_col[$i] = "";
			} else  {
				$progress_col[$i] = "((".$colSalesYTDQryPart.")*100)/(sum(".$customerPrefix."pac".$i."salestarget.salestarget)) as progress,";
			}
		}

		$salesColumnAdd1 = $this->sa("customerprodsales");
		$colSalesYTD1 = $salesColumnAdd1["SalesYTD"];
		$colPSalesYTD1 = $salesColumnAdd1["SalesPYTD"];
		$colPPSalesYTD1 = $salesColumnAdd1["SalesPPYTD"];

		$colSalesYTDQryPart1 =  " ( ".join(" + ",$colSalesYTD1)." ) ";
		$colPSalesYTDQryPart1 =  " ( ".join(" + ",$colPSalesYTD1)." ) ";
		$colPPSalesYTDQryPart1 =  " ( ".join(" + ",$colPPSalesYTD1)." ) ";
		$leftJoins["Company"][5] = array(
			"select" => $colSalesYTDQryPart1." as ytd, ".$colPSalesYTDQryPart1." as ytp, ".$colPPSalesYTDQryPart1." as ytpp",
			"join" => " LEFT JOIN customerprodsales ON (customerprodsales.account='".$account."' AND customerprodsales.prodcode=product.code) "
		);

		if (!$ShowProgress) {
			$progress_col[5] = "";
		} else  {
			$progress_col[5] = "((".$colSalesYTDQryPart1.")*100)/(sum(".$customerPrefix."productsalestarget.salestarget)) as progress,";
		}

		switch($G_level){
			case 'Company':
				$query = "select '1' as 'tabl', ".$leftJoins["Company"][1]["select"].", pac1.description as description, ".$customerPrefix."pac1salestarget.pac1code as paccode,sum(".$customerPrefix."pac1salestarget.salestarget) as salestarget from ".$customerPrefix."pac1salestarget LEFT JOIN pac1 ON pac1.code=".$customerPrefix."pac1salestarget.pac1code  ".$leftJoins["Company"][1]["join"]." where ".$customerPrefix."pac1salestarget.yearmonth='$yearmonth'AND ".$customerPrefix."pac1salestarget.account='$account' GROUP BY pac1.code UNION select '2' as 'tabl', ".$leftJoins["Company"][2]["select"].", pac2.description as description, ".$customerPrefix."pac2salestarget.pac2code as paccode,sum(".$customerPrefix."pac2salestarget.salestarget) as salestarget from ".$customerPrefix."pac2salestarget LEFT JOIN pac2 ON pac2.code=".$customerPrefix."pac2salestarget.pac2code  ".$leftJoins["Company"][2]["join"]." where ".$customerPrefix."pac2salestarget.yearmonth='$yearmonth'AND ".$customerPrefix."pac2salestarget.account='$account'  GROUP BY pac2.code UNION select '3' as 'tabl', ".$leftJoins["Company"][3]["select"].", pac3.description as description,".$customerPrefix."pac3salestarget.pac3code as paccode,sum(".$customerPrefix."pac3salestarget.salestarget) as salestarget from ".$customerPrefix."pac3salestarget LEFT JOIN pac3 ON pac3.code=".$customerPrefix."pac3salestarget.pac3code  ".$leftJoins["Company"][3]["join"]." where ".$customerPrefix."pac3salestarget.yearmonth='$yearmonth'AND ".$customerPrefix."pac3salestarget.account='$account'  GROUP BY pac3.code UNION select '4' as 'tabl', ".$leftJoins["Company"][4]["select"].", pac4.description as description,".$customerPrefix."pac4salestarget.pac4code as paccode,sum(".$customerPrefix."pac4salestarget.salestarget) as salestarget from ".$customerPrefix."pac4salestarget LEFT JOIN pac4 ON pac4.code=".$customerPrefix."pac4salestarget.pac4code  ".$leftJoins["Company"][4]["join"]." where ".$customerPrefix."pac4salestarget.yearmonth='$yearmonth'AND ".$customerPrefix."pac4salestarget.account='$account'  GROUP BY pac4.code UNION select '5' as 'tabl', ".$leftJoins["Company"][5]["select"].", product.description as description,".$customerPrefix."productsalestarget.productcode as paccode,sum(".$customerPrefix."productsalestarget.salestarget) as salestarget from ".$customerPrefix."productsalestarget LEFT JOIN product ON product.code=".$customerPrefix."productsalestarget.productcode  ".$leftJoins["Company"][5]["join"]." where ".$customerPrefix."productsalestarget.yearmonth='$yearmonth' AND ".$customerPrefix."productsalestarget.account='$account'  GROUP BY product.code UNION select '0' as 'tabl',  ".$leftJoins["Company"][0]["select"].", 'Customer Target' as description, '0' as paccode,sum(".$customerPrefix."salestarget.salestarget) as salestarget from ".$customerPrefix."salestarget  ".$leftJoins["Company"][0]["join"]."  where ".$customerPrefix."salestarget.yearmonth='$yearmonth'AND ".$customerPrefix."salestarget.account='$account' GROUP BY ".$customerPrefix."salestarget.account";
			break;
			case 'User':
			$extraCondition[0] = "1=1";
			$extraCondition[1] = "".$customerPrefix."pac1salestarget.userid='$userid'";
			$extraCondition[2] = "".$customerPrefix."pac2salestarget.userid='$userid'";
			$extraCondition[3] = "".$customerPrefix."pac3salestarget.userid='$userid'";
			$extraCondition[4] = "".$customerPrefix."pac4salestarget.userid='$userid'";
			$extraCondition[5] = "".$customerPrefix."pac5salestarget.userid='$userid'";


			$extraCondition[0] = "1=1";
			$extraCondition[1] = "1=1";
			$extraCondition[2] = "1=1";
			$extraCondition[3] = "1=1";
			$extraCondition[4] = "1=1";
			$extraCondition[5] = "1=1";

				$query = "select '1' as 'tabl', ".$leftJoins["Company"][1]["select"].",pac1.description as description, ".$customerPrefix."pac1salestarget.pac1code as paccode,sum(".$customerPrefix."pac1salestarget.salestarget) as salestarget from pac1 left join ".$customerPrefix."pac1salestarget on pac1.code=".$customerPrefix."pac1salestarget.pac1code ".$leftJoins["Company"][1]["join"]." where ".$extraCondition[1]." and ".$customerPrefix."pac1salestarget.yearmonth='$yearmonth' AND ".$customerPrefix."pac1salestarget.account='$account'  GROUP BY pac1.code UNION select '2' as 'tabl', ".$leftJoins["Company"][2]["select"].",pac2.description as description, ".$customerPrefix."pac2salestarget.pac2code as paccode,sum(".$customerPrefix."pac2salestarget.salestarget) as salestarget from pac2 left join ".$customerPrefix."pac2salestarget on pac2.code=".$customerPrefix."pac2salestarget.pac2code ".$leftJoins["Company"][2]["join"]." where ".$extraCondition[2]." and ".$customerPrefix."pac2salestarget.yearmonth='$yearmonth' AND ".$customerPrefix."pac2salestarget.account='$account'   GROUP BY pac2.code  UNION  select '3' as 'tabl', ".$leftJoins["Company"][3]["select"].",pac3.description as description,".$customerPrefix."pac3salestarget.pac3code as paccode,sum(".$customerPrefix."pac3salestarget.salestarget) as salestarget from pac3 left join ".$customerPrefix."pac3salestarget on pac3.code=".$customerPrefix."pac3salestarget.pac3code ".$leftJoins["Company"][3]["join"]." where ".$extraCondition[3]." and ".$customerPrefix."pac3salestarget.yearmonth='$yearmonth' AND ".$customerPrefix."pac3salestarget.account='$account'   GROUP BY pac3.code  UNION select '4' as 'tabl', ".$leftJoins["Company"][4]["select"].",pac4.description as description,".$customerPrefix."pac4salestarget.pac4code as paccode,sum(".$customerPrefix."pac4salestarget.salestarget) as salestarget from pac4 left join ".$customerPrefix."pac4salestarget on pac4.code=".$customerPrefix."pac4salestarget.pac4code ".$leftJoins["Company"][4]["join"]." where ".$extraCondition[4]." and ".$customerPrefix."pac4salestarget.yearmonth='$yearmonth' AND ".$customerPrefix."pac4salestarget.account='$account'   GROUP BY pac4.code UNION select '5' as 'tabl', ".$leftJoins["Company"][5]["select"].",product.description as description,".$customerPrefix."productsalestarget.productcode as paccode,sum(".$customerPrefix."productsalestarget.salestarget) as salestarget from product left join ".$customerPrefix."productsalestarget on product.code=".$customerPrefix."productsalestarget.productcode ".$leftJoins["Company"][5]["join"]." where ".$extraCondition[5]." and ".$customerPrefix."productsalestarget.yearmonth='$yearmonth' AND ".$customerPrefix."productsalestarget.account='$account'   GROUP BY product.code UNION select '0' as 'tabl',  ".$leftJoins["Company"][0]["select"].", 'Customer Target' as description, '0' as paccode,sum(".$customerPrefix."salestarget.salestarget) as salestarget from ".$customerPrefix."salestarget  ".$leftJoins["Company"][0]["join"]."  where ".$customerPrefix."salestarget.yearmonth='$yearmonth'AND ".$customerPrefix."salestarget.account='$account' GROUP BY ".$customerPrefix."salestarget.account";
				break;
			case 'Branch':
				$query = "select '1' as 'tabl',pac1.description as description, ".$customerPrefix."pac1salestarget.pac1code as paccode,sum(".$customerPrefix."pac1salestarget.salestarget) as salestarget  from pac1 left join ".$customerPrefix."pac1salestarget on pac1.code=".$customerPrefix."pac1salestarget.pac1code  left join users on ".$customerPrefix."pac1salestarget.userid=users.userid where users.branch='$branchNo' and ".$customerPrefix."pac1salestarget.yearmonth='$yearmonth' AND ".$customerPrefix."pac1salestarget.account='$account'   GROUP BY pac1.code  UNION select '2' as 'tabl',pac2.description as description, ".$customerPrefix."pac2salestarget.pac2code as paccode,sum(".$customerPrefix."pac2salestarget.salestarget) as salestarget  from pac2 left join ".$customerPrefix."pac2salestarget on pac2.code=".$customerPrefix."pac2salestarget.pac2code  left join users on ".$customerPrefix."pac2salestarget.userid=users.userid where users.branch='$branchNo' and ".$customerPrefix."pac2salestarget.yearmonth='$yearmonth' AND ".$customerPrefix."pac2salestarget.account='$account'   GROUP BY pac2.code UNION select '3' as 'tabl',pac3.description as description, ".$customerPrefix."pac3salestarget.pac3code as paccode,sum(".$customerPrefix."pac3salestarget.salestarget) as salestarget  from pac3 left join ".$customerPrefix."pac3salestarget on pac3.code=".$customerPrefix."pac3salestarget.pac3code  left join users on ".$customerPrefix."pac3salestarget.userid=users.userid where users.branch='$branchNo' and ".$customerPrefix."pac3salestarget.yearmonth='$yearmonth' AND ".$customerPrefix."pac3salestarget.account='$account'   GROUP BY pac3.code UNION select '4' as 'tabl',pac4.description as description, ".$customerPrefix."pac4salestarget.pac4code as paccode,sum(".$customerPrefix."pac4salestarget.salestarget) as salestarget  from pac4 left join ".$customerPrefix."pac4salestarget on pac4.code=".$customerPrefix."pac4salestarget.pac4code  left join users on ".$customerPrefix."pac4salestarget.userid=users.userid where users.branch='$branchNo' and ".$customerPrefix."pac4salestarget.yearmonth='$yearmonth' AND ".$customerPrefix."pac4salestarget.account='$account'   GROUP BY pac4.code UNION select '5' as 'tabl',product.description as description, ".$customerPrefix."productsalestarget.productcode as paccode,sum(".$customerPrefix."productsalestarget.salestarget) as salestarget  from product left join ".$customerPrefix."productsalestarget on product.code=".$customerPrefix."productsalestarget.productcode  left join users on ".$customerPrefix."productsalestarget.userid=users.userid where users.branch='$branchNo' and ".$customerPrefix."productsalestarget.yearmonth='$yearmonth' AND ".$customerPrefix."productsalestarget.account='$account'   GROUP BY product.code";
				break;
		}
		return $query;
	}

	public function getPac1SalesTargetDashboard($G_level,$userid=0,$branchNo=0,$repclause, $account, $yearmonthposted) {
		$query = $this->salesTrgetQuery($G_level,$userid=0,$branchNo=0,$repclause, $account, $yearmonthposted);
		$result = $this->db->query($query)->result();
		return $result;
	}

	public function sa($tablename) {
		$CurrYearMonthArr = array();
		$PrevYearMonthArr = array();
		$PPrevYearMonthArr = array();

		$CurrMonth = date("m");

		$CurrYear = date("Y");
		$PrevYear = $CurrYear-1;
		$PPrevYear = $PrevYear-1;

		for ($currInd=0; $currInd<$CurrMonth; $currInd++) {
			array_push($CurrYearMonthArr, $tablename.".msales".$currInd);
		}

		for ($currInd=12; $currInd<($CurrMonth+12); $currInd++) {
			array_push($PrevYearMonthArr, $tablename.".msales".$currInd);
		}

		for ($currInd=24; $currInd<($CurrMonth+24); $currInd++) {
			array_push($PPrevYearMonthArr, $tablename.".msales".$currInd);
		}

		$salesColumnsToAdd = array(
			"SalesYTD"	=> $CurrYearMonthArr,
			"SalesPYTD"	=> $PrevYearMonthArr,
			"SalesPPYTD"=> $PPrevYearMonthArr
		);

		return $salesColumnsToAdd;
	}

	public function customerContactQuery($account, $search, $order)
	{
		$orderCol = array
		(
			"contactno ".$order["dir"],
			"title ".$order["dir"].", firstname ".$order["dir"].", surname ".$order["dir"],
			"contacttype ".$order["dir"],
			"jobtitle ".$order["dir"],
			"sensitivecontact ".$order["dir"],
			"donotcommunicate ".$order["dir"],
			"phone1desc ".$order["dir"],
			"phone1no ".$order["dir"],
			"phone2desc ".$order["dir"],
			"phone2no ".$order["dir"],
			"email1desc ".$order["dir"],
			"emailaddress1 ".$order["dir"],
			"email2desc ".$order["dir"],
			"emailaddress2 ".$order["dir"],
		);

		$this->db->select('contactno, concat(title, " ", firstname, " ", surname) as name, contacttype, jobtitle, address1, address2, address3, address4, address5, postcode, sensitivecontact, donotcommunicate, phone1desc, phone1no, phone2desc, phone2no, phone3desc, phone3no, phone4desc, phone4no, email1desc, emailaddress1, email2desc, emailaddress2', false);
		$this->db->from("customercontact");
		$where = "account = '".$account."'";

		if (!!$search && !is_array($search))
		{
			$where.= " AND (";
			$where.= " LOWER(contactno) LIKE LOWER('%".$search."%')  ";
			$where.= " OR LOWER(title) LIKE LOWER('%".$search."%')  ";
			$where.= " OR LOWER(firstname) LIKE LOWER('%".$search."%')  ";
			$where.= " OR LOWER(surname) LIKE LOWER('%".$search."%')  ";
			$where.= " OR LOWER(postcode) LIKE LOWER('%".$search."%')  ";
			$where.= " OR LOWER(phone1no) LIKE LOWER('%".$search."%')  ";
			$where.= ")";
		}

		$this->db->where($where);
		$this->db->order_by($orderCol[$order["column"]]);
	}

	public function customerContacts($account, $search = array(), $start = 0, $limit = null, $order)
	{
		$this->customerContactQuery($account, $search, $order);

		if ($limit)
		{
			$this->db->limit($limit, $start);
		}

		$query = $this->db->get();
		$return = $query->result_array();
		$return_nocol = array();

		foreach ($return as $row)
		{
			$onclick = "onClick='openPopupDetail(".$row["contactno"].");'";
			$name = "<a href='javascript:void(0);' data-toggle='modal' data-target='#CustomerDetail' onclick='openPopupDetail(".$row["contactno"].");'>".$row["name"]."</a>";
			$return_nocol[] = array
			(
				$row["contactno"],
				$name,
				$row["contacttype"],
				$row["jobtitle"],
				$row["sensitivecontact"],
				$row["donotcommunicate"],
				$row["phone1desc"],
				"<a href='tel:".$row["phone1no"]."'>".$row["phone1no"]."</a>",
				$row["phone2desc"],
				"<a href='tel:".$row["phone2no"]."'>".$row["phone2no"]."</a>",
				$row["email1desc"],
				"<a href='mailto:".$row["emailaddress1"]."'>".$row["emailaddress1"]."</a>",
				$row["email2desc"],
				"<a href='mailto:".$row["emailaddress2"]."'>".$row["emailaddress2"]."</a>",
				"<button type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#CustomerDetail' ".$onclick."><i class='fa fa-eye' aria-hidden='true'></i> Show Details</button>",
			);
		}

		return $return_nocol;
	}

	public function fetchContactDetail($contactno) {
		$this->db->_protect_identifiers=false;
		$this->db->select('*');
		$this->db->from("customercontact");
		$this->db->where("contactno", $contactno);
		$query = $this->db->get();
		$return = $query->result_array();
		return $return[0];
	}

	/* Function to create a file to be exported in CSV or EXCEL for customer contacts */

	public function csv_export_customer_contacts($account, $search, $order) {
		$this->customerContactQuery($account, $search, $order);
		$query = $this->db->get();
		$this->load->dbutil();
		$opt=$this->dbutil->csv_from_result($query);
		echo $opt;
	}

	public function customerRepcodes($account)
	{
		$this->db->select("cr.account as account, cr.repcode as repcode, sr.name as name");
		$this->db->from("customerreps cr");
		$this->db->join("salesrep sr", "cr.repcode = sr.repcode", "left");
		$this->db->where("account", $account);

		$query = $this->db->get();

		$result = array_map("custRepsDataTable", $query->result_array());
		return array('data' => $result);
	}

	public function deleterep($repcode, $account) {
		$this->db->where("repcode", $repcode);
		$this->db->where("account", $account);
		$this->db->delete("customerreps");
		$affected_rows = $this->db->affected_rows();
		$success = $affected_rows>0;
		return array("success"=>$success, "affected_rows"=>$affected_rows);
	}

	public function deleteurep($repcode, $userid) {
		$this->db->where("repcode", $repcode);
		$this->db->where("userid", $userid);
		$this->db->delete("userreps");
		$affected_rows = $this->db->affected_rows();
		$success = $affected_rows>0;
		return array("success"=>$success, "affected_rows"=>$affected_rows);
	}

	public function getYearStartMonth() {
		$this->db->select('yearstartmonth');
		$this->db->from('system');
		$query = $this->db->get();
		$res = $query->row_array();

		return isset($res) ? $res['yearstartmonth'] : 1;
	}
}

function productCode($codearr) {
	return $codearr["code"].": ".$codearr["description"];
}

function addDeleteActionableResource($targetArr) {
	$targetArr["delete"] = '<span id="dlink_'.$targetArr["id"].'" data-codetype="'.$targetArr["codetype"].'" data-id="'.$targetArr["id"].'"><a onclick="return deletetarget(event);" href="javascript:void(0);" class="transform-link"><i class="fa fa-fw fa-trash-o"></i></a></span>';

	$selectYear = "<option>-Select Year-</option>";
	$selectYearArray = array("<option value='1970'>-Select Year-</option>");
	for ($yearNumber=intval(date("Y")); $yearNumber<=intval(date("Y"))+10;$yearNumber++) {
		$selected = $yearNumber==intval(substr($targetArr["yearmonth"],0,4))?"selected":"";
		array_push($selectYearArray, "<option value='".$yearNumber."' ".$selected.">".$yearNumber."</option>");
	}
	$selectYear = join("", $selectYearArray);

	$selectMonth = "<option>-Select Month-</option>";
	$selectMonthArray = array("<option value='0'>-Select Month-</option>");
	for ($monthNumber=1; $monthNumber<=12;$monthNumber++) {
		$selected = $monthNumber==intval(substr($targetArr["yearmonth"],4,2))?"selected":"";
		array_push($selectMonthArray, "<option value='".($monthNumber<10?"0".$monthNumber:$monthNumber)."' ".$selected.">".($monthNumber<10?"0".$monthNumber:$monthNumber)."</option>");
	}
	$selectMonth = join("", $selectMonthArray);

	$targetArr["yearmonth"] = '<div class="linktype" id="yearmonth_display_'.$targetArr["id"].'" title="Click to edit" onclick="OpenEditForm(\''.$targetArr["id"].'\', \'yearmonth\');">'.$targetArr["yearmonth"].'</div><div class="edittype hidden" id="yearmonth_'.$targetArr["id"].'"><select name="year" id="year_'.$targetArr["id"].'" value="'.substr($targetArr["yearmonth"],0,4).'" class="form-control input-sm">'.$selectYear.'</select><select name="month" id="month_'.$targetArr["id"].'" value="'.substr($targetArr["yearmonth"],4,6).'" class="form-control input-sm">'.$selectMonth.'</select><button type="button" class="btn btn-primary btn-sm editable-submit" onclick="changeYearMonth(\''.$targetArr["id"].'\', \''.$targetArr["codetype"].'\');"><i class="glyphicon glyphicon-ok"></i></button><button type="button" class="btn btn-default btn-sm editable-cancel"
	onclick="OpenEditForm(\''.$targetArr["id"].'\', \'yearmonth\');"><i class="glyphicon glyphicon-remove"></i></button></div>';

	$targetArr["salestarget"] = '<div class="linktype" id="salestarget_edit_display_'.$targetArr["id"].'" title="Click to edit" onclick="OpenEditForm(\''.$targetArr["id"].'\', \'salestarget_edit\');">'.$targetArr["salestarget"].'</div><div class="edittype hidden" id="salestarget_edit_'.$targetArr["id"].'"><input type="text" name="salestarget" id="salestarget_'.$targetArr["id"].'" value="'.$targetArr["salestarget"].'" style="width: 30%;" class="form-control input-sm"><button type="button" class="btn btn-primary btn-sm editable-submit" onclick="changeSalestarget(\''.$targetArr["id"].'\', \''.$targetArr["codetype"].'\');"><i class="glyphicon glyphicon-ok"></i></button><button type="button" class="btn btn-default btn-sm editable-cancel" onclick="OpenEditForm(\''.$targetArr["id"].'\', \'salestarget_edit\');"><i class="glyphicon glyphicon-remove"></i></button></div>';

	unset($targetArr["id"]);
	return $targetArr;
}

function removeIdActionableResource($targetArr) {
	unset($targetArr["id"]);
	return $targetArr;
}

function mapRepcodeFactor($result_array) {
	return "'".$result_array["cr_account"]."'";
}

function custRepsDataTable($result_array)
{
	$delete = "<span class='fa fa-fw fa-trash-o delete-repcode-customer pointer' data-account='".base64_encode($result_array["account"])."' data-repcode='".$result_array["repcode"]."' onclick='deleteCustomerRep(event);'></span>";

	return array($result_array["repcode"], $result_array["name"], $delete);
}
