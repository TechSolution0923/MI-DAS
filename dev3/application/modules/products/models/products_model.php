<?php

class Products_model extends Model {
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
	/*###########################################################
	** Functions used before the new tables are introduces.
	** These functions mostly uses the salesanalysis table.
	** There were other old functions as well those we deleted
	** as they were not being used anywhere.
	*#############################################################*/

	public function prodPAC($repwhere,$n) {
		// Get the start of this month three years ago, to get three years of data
		$this->load->model('common_model');

		$pac = 'pac'.$n;
		$daysinmonth = date("t",strtotime($date));
		$year0 = date("Y");
		$year1 = $year0 - 1;
		$year2 = $year0 - 2;
		$year3 = $year0 - 3;
		$thismonth = date("m");

		$graphlabel0 = $year1."-".$year0;
		$graphlabel1 = $year2."-".$year1;
		$graphlabel2 = $year3."-".$year2;

		$startdate = $year3."-".$thismonth."-".$daysinmonth;

		$startyearmonth = ($year3 * 100) + $thismonth; // Start of three years ago
		$thisyearstartyearmonth = ($year0 * 100) + 1; // Start of this year
		$curyearmonth = ($year0 * 100) + $thismonth; // e.g. 201507

		$this->db->select('p.code, p.description, SUM(s.quantity) as quantity_sum, SUM(s.sales) as sales_sum, SUM(s.cost) as cost_sum, s.yearmonth');
		$this->db->from($pac.' p');

		/* Generating repcode condition */
		$repWhereCondition = $this->common_model->makeRepcodeCondition($repwhere, 's.currepcode', true, true);

		/* Generating branch condition */
		$this->common_model->makeBranchCondition('s.branch', true);

		$this->db->join('salesanalysis s', 's.curpac'.$n.'code = p.code AND s.yearmonth BETWEEN '. $thisyearstartyearmonth .' AND '. $curyearmonth .$repWhereCondition, 'left');

		$this->db->group_by('p.code, s.yearmonth');
		$this->db->order_by('p.code', 'ASC');

		$query = $this->db->get();
		return $query->result_array();
	}

	public function prodSAProd($repwhere) {
		// Get the start of this month three years ago, to get three years of data
		$this->load->model('common_model');

		$daysinmonth = date("t",strtotime($date));
		$year0 = date("Y");
		$year1 = $year0 - 1;
		$year2 = $year0 - 2;
		$year3 = $year0 - 3;
		$thismonth = date("m");

		$graphlabel0 = $year1."-".$year0;
		$graphlabel1 = $year2."-".$year1;
		$graphlabel2 = $year3."-".$year2;

		$startdate = $year3."-".$thismonth."-".$daysinmonth;

		$startyearmonth = ($year3 * 100) + $thismonth; // Start of three years ago
		$thisyearstartyearmonth = ($year0 * 100) + 1; // Start of this year
		$curyearmonth = ($year0 * 100) + $thismonth; // e.g. 201507

		$this->db->select('p.code, p.pac4code,p.description, SUM(s.quantity) as quantity_sum, SUM(s.sales) as sales_sum, SUM(s.cost) as cost_sum, s.yearmonth');
		$this->db->from('product p');

		/* Generating repcode condition */
		$repWhereCondition = $this->common_model->makeRepcodeCondition($repwhere, 's.currepcode', true, true);

		/* Generating branch condition */
		$this->common_model->makeBranchCondition('s.branch', true);

			$limit = $this->config->item('limit');
		if ($limit>0) {
			$this->db->limit($limit);
		}
		$this->db->join('salesanalysis s', 's.prodcode = p.code AND s.yearmonth BETWEEN '. $thisyearstartyearmonth .' AND '. $curyearmonth .$repWhereCondition, 'left');
		$this->db->group_by('p.code, s.yearmonth');
		$this->db->order_by('p.code', 'ASC');

		$query = $this->db->get();
		return $query->result_array();
	}

	/* An old function in use with the controller function details. */
	public function getSaleAnalysis($repwhere, $prodcode, $startyearmonth, $curyearmonth){

		$this->load->model('common_model');

		/* Generating repcode condition */
		$repWhereCondition = $this->common_model->makeRepcodeCondition($repwhere, 's.currepcode', true, true);
		//echo $repWhereCondition;die;
		/* Generating branch condition */
		$this->common_model->makeBranchCondition('s.branch', true);
		$this->db->select('s.yearmonth, SUM(s.sales) as sales');
		$this->db->from('salesanalysis s');
		$this->db->join("customer c", "c.account = s.account AND s.prodcode = '".$prodcode."' ".$repWhereCondition." AND s.yearmonth BETWEEN ".$startyearmonth." AND ".$curyearmonth);

		$this->db->group_by('s.yearmonth');
		$this->db->order_by('s.yearmonth', 'ASC');

		$query = $this->db->get();

		return $query->result_array();
	}

	/* An old function in use with the controller function details2. */
	public function getPACSaleAnalysis($prodcode, $startyearmonth, $curyearmonth, $page)
	{
		/* Generating repcode */
		$this->load->model('common_model');
		/* Generating repcode condition */
		$repWhereCondition = $this->common_model->makeRepcodeCondition('c.repcode', true, true);

		$select = array();

		for ($i = 0; $i < 36; $i++)
		{
			$select[] = "SUM(s.msales".$i.") as m".$i;
		}

		$this->db->select(join($select, ", "));

		if ($page == 5)
		{
			$this->db->from("productsales s");
			$this->db->where("s.prodcode", $prodcode);
		}
		else
		{
			$this->db->from("pac".$page."sales s");
			$this->db->where("s.pac".$page."code", $prodcode);
		}
		
		$query = $this->db->get();

		$modified_array = $this->common_model->array_modification($query->result_array());

		return $modified_array;
	}

	public function getPACSaleAnalysisQuantities($repwhere, $prodcode, $startyearmonth, $curyearmonth, $page)
	{
		/* Generating repcode */
		$this->load->model('common_model');
		/* Generating repcode condition */
		$repWhereCondition = $this->common_model->makeRepcodeCondition('c.repcode', true, true);

		$select = array();

		for ($i = 0; $i < 36; $i++)
		{
			$select[] = "SUM(s.mquantity".$i.") as m".$i;
		}

		$this->db->select(join($select, ", "));

		if ($page == 5)
		{
			$this->db->from("productsales s");
			$this->db->where("s.prodcode", $prodcode);
		}
		else
		{
			$this->db->from("pac".$page."sales s");
			$this->db->where("s.pac".$page."code", $prodcode);
		}
		
		$query = $this->db->get();

		$modified_array = $this->common_model->array_modification($query->result_array());

		return $modified_array;
	}

	public function prodDesc($code,$n){
		if (0!=$n) {
			$this->db->select('description');
			if (5!=$n) {
				$this->db->from('pac'.$n);
			} else {
				$this->db->from('product');
			}

			$this->db->where('code', $code);
			$query = $this->db->get();
			$qry = $query->row_array();
			return $qry['description'];
		} else {
			return "Customer Target";
		}
	}

	public function prodDesc2($code){
		$this->db->select('description, pac4code');
		$this->db->from('product');
		$this->db->where('code', $code);
		$query = $this->db->get();
		return $query->row_array();
	}

	public function getProductPACCount($pacNo)
	{
		$sql="select count(distinct code) from pac".$pacNo."";
		$query = $this->db->query($sql);

		return reset($query->row_array());
	}

	public function getProductPACFilteredCount($pacNo, $branchNo = 0, $search = array())
	{
		$escaped_search_string = $this->db->escape_str($search);

		$where = "";
		
		if (!empty($branchNo))
		{
			$where.= "where (s.branch = '".$branchNo."')";
		}

		if (!!$search && !is_array($search))
		{
			if (!empty($branchNo))
			{
				$where.= "and (lower(p.code) like lower('%".$escaped_search_string."%') or lower(p.description) like lower('%".$escaped_search_string."%'))";
			}
			else
			{
				$where.= "where (lower(p.code) like lower('%".$escaped_search_string."%') or lower(p.description) like lower('%".$escaped_search_string."%'))";
			}
		}

		$sql="select count(distinct p.code) from pac".$pacNo." p left join pac1sales s on s.pac1code = p.code ".$where;
		$query = $this->db->query($sql);

		return reset($query->row_array());
	}

	public function getProductPAC($repwhere, $n, $specificOrder, $recodeArray = array(), $branchNo = 0, $search = array(), $start = 0, $limit = null)
	{
		$escaped_search_string = $this->db->escape_str($search);

		$this->load->model('common_model');
		$repWhereCondition = $this->common_model->makeRepcodeCondition('s.repcode', true);
		$proRataCoefficient = $this->common_model->getWorkingDayProRataCoefficient(date("Y/m/d"));
		$proRataAdjustmentYoY1Sales = "";
		$proRataAdjustmentYoY2Sales = "";
		$proRataAdjustmentYoY1Qty = "";
		$proRataAdjustmentYoY2Qty = "";

		$seemarginsAr = $this->common_model->loggedin_userdetail();

		$pacNSales = 'pac'.$n.'sales';
		$pacN = 'pac'.$n;
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
				$proRataAdjustmentYoY1Sales = "-s.msales".$x."*(1-".$proRataCoefficient.")";
				$proRataAdjustmentYoY1Qty = "-s.mquantity".$x."*(1-".$proRataCoefficient.")";
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
			else
			{
				$proRataAdjustmentYoY2Sales = "-s.msales".$x."*(1-".$proRataCoefficient.")";
				$proRataAdjustmentYoY2Qty = "-s.mquantity".$x."*(1-".$proRataCoefficient.")";
			}

			$query2.= "s.msales".$x;
			$queryq2.= "s.mquantity".$x;

			$y++;
		}

		$yearOnYearData=", ROUND(SUM(".$query1.$proRataAdjustmentYoY1Sales."), 2) as YoY1Sales, ROUND(SUM(".$query2.$proRataAdjustmentYoY2Sales."), 2) AS YoY2Sales, ROUND(SUM(".$queryq1.$proRataAdjustmentYoY1Qty."), 2) AS YoY1Qty, ROUND(SUM(".$queryq2.$proRataAdjustmentYoY2Qty."), 2) AS YoY2Qty";

		if (!!$seemarginsAr['seemargins'])
		{
			$selMarginM = '(SUM(s.mmargin0)/SUM(s.msales0))*100 as marginmtdpc,';
			$selMarginY = ', (SUM(s.ymargin0)/SUM(s.ysales0))*100 as marginytdpc';
		}
		else
		{
			$selMarginM = '';
			$selMarginY = '';
		}

		$select_string = "p.code, p.description, SUM(s.mquantity0) as qtymtd, SUM(s.msales0) as salesmtd, ".$selMarginM." SUM(s.yquantity0) as qtyytd, SUM(s.ysales0) as salesytd".$selMarginY.$yearOnYearData.", SUM(mcost0) as costsmtd, SUM(ycost0) as costsytd, IFNULL(IF((SUM(s.ysales0)-SUM(".$query1.$proRataAdjustmentYoY1Sales."))/SUM(".$query1.$proRataAdjustmentYoY1Sales.")*100 > 100, ((SUM(s.ysales0)-SUM(".$query1.$proRataAdjustmentYoY1Sales."))/SUM(".$query1.$proRataAdjustmentYoY1Sales.")*100), (SUM(s.ysales0)-SUM(".$query1.$proRataAdjustmentYoY1Sales."))/SUM(".$query1.$proRataAdjustmentYoY1Sales.")*100), IF(SUM(s.ysales0) > 0.00, CAST(100.00 AS DECIMAL), CAST(0.00 AS DECIMAL))) as salesdiff, IFNULL(IF((SUM(s.yquantity0)-SUM(".$queryq1.$proRataAdjustmentYoY1Qty."))/SUM(".$queryq1.$proRataAdjustmentYoY1Qty.")*100 > 100, ((SUM(s.yquantity0)-SUM(".$queryq1.$proRataAdjustmentYoY1Qty."))/SUM(".$queryq1.$proRataAdjustmentYoY1Qty.")*100)-100,  (SUM(s.yquantity0)-SUM(".$queryq1.$proRataAdjustmentYoY1Qty."))/SUM(".$queryq1.$proRataAdjustmentYoY1Qty.")*100), IF(SUM(s.yquantity0) > 0.00, CAST(100.00 AS DECIMAL), CAST(0.00 AS DECIMAL))) as qtydiff";

		$this->db->select($select_string, false);
		$this->db->from($pacN.' p');
		/* Generating branch condition */
		$this->common_model->makeBranchCondition('s.branch', false);

		$this->db->join($pacNSales.' s', 's.pac'.$n.'code = p.code '.$repWhereCondition, 'left');
		// code for user branch filter on 3rd nov

		if (!empty($branchNo))
		{
			$where = "(s.branch = '".$branchNo."')";
			$this->db->where($where);
		}

		if (!!$search && !is_array($search))
		{
			$where = "(LOWER(p.code) LIKE LOWER('%".$escaped_search_string."%') OR LOWER(p.description) LIKE LOWER('%".$escaped_search_string."%'))";
			$this->db->where($where);
		}
		
		$this->db->group_by('p.code');
		$this->db->order_by($specificOrder['by'], $specificOrder['dir']);

		if (!is_null($limit))
		{
			$this->db->limit($limit, $start);
		}

		$query = $this->db->get();

		$return_array = $query->result_array();

		return $return_array;
	}

	/** This will  */
	public function sumList($result_array) {
		$addedArray = array();
		$percentageRequired = false;
		$marginRequired = false;
		foreach ($result_array as $rowNumber=>$row)
		{
			$index = 0;
			if ($index == 0 && array_key_exists('salesytd', $row) && array_key_exists('YoY1Sales', $row))
			{
				$percentageRequired['sales_diff'] = array(
					'first'     => 'salesytd',
					'second'    => 'YoY1Sales',
					'label'     => 'sales_diff'
				);
			}
			if ($index == 0 && array_key_exists('qtyytd', $row) && array_key_exists('YoY1Qty', $row))
			{
				$percentageRequired['qty_diff'] = array(
					'first'     => 'qtyytd',
					'second'    => 'YoY1Qty',
					'label'     => 'qty_diff'
				);
			}
			if ($index == 0 && array_key_exists('salesmtd', $row) && array_key_exists('costsmtd', $row))
			{
				$marginRequired['gm_mtd'] = array(
					'first' => 'salesmtd',
					'second' => 'costsmtd',
					'label'	 => 'marginmtdpc'
				);
			}

			if ($index == 0 && array_key_exists('salesytd', $row) && array_key_exists('costsytd', $row))
			{
				$marginRequired['gm_ytd'] = array(
					'first' => 'salesytd',
					'second' => 'costsytd',
					'label'	 => 'marginytdpc'
				);
			}
			foreach ($row as $key=>$val)
			{
				if ("code"==$key) {
					$addedArray[$key] = "Total";
				} else {
					if ("qtymtd"==$key) {
						$addedArray[$key] += floatval($val);
					} else if ("salesmtd"==$key) {
						$addedArray[$key] += floatval($val);
					} else if ("qtyytd"==$key) {
						$addedArray[$key] += floatval($val);
					} else if ("salesytd"==$key) {
						$addedArray[$key] += floatval($val);
					} else if ("YoY1Sales"==$key) {
						$addedArray[$key] += floatval($val);
					} else if ("YoY2Sales"==$key) {
						$addedArray[$key] += floatval($val);
					} else if ("costsmtd"==$key) {
						$addedArray[$key] += floatval($val);
					} else if ("costsytd"==$key) {
						$addedArray[$key] += floatval($val);
					} elseif ("freeqty"==$key) {
						$addedArray[$key] += floatval($val);
					} elseif ("purchaseqty"==$key) {
						$addedArray[$key] += floatval($val);
					} elseif ("YoY1Qty"==$key) {
						$addedArray[$key] += floatval($val);
					} else if ("YoY2Qty"==$key) {
						$addedArray[$key] += floatval($val);
					} else {
						$addedArray[$key] = "";
					}

				}
				$index++;
			}

		}

		if ($percentageRequired !== false)
		{
			foreach ($percentageRequired as $key => $fields)
			{
				if ($addedArray[$fields['second']] == 0)
				{
					$percentage = 0;
				}
				else
				{
					$percentage = ($addedArray[$fields['first']] - $addedArray[$fields['second']]) / $addedArray[$fields['second']] * 100;
				}

				$addedArray[$fields['label']] = number_format($percentage, 2);
			}
		}

		if ($marginRequired !== false)
		{
			foreach ($marginRequired as $key => $fields)
			{
				if ($addedArray[$fields['first']] == 0)
				{
					$percentage = 0;
				}
				else
				{
					$percentage = ($addedArray[$fields['first']] - $addedArray[$fields['second']]) / $addedArray[$fields['first']] * 100;
				}

				$addedArray[$fields['label']] = number_format($percentage, 2);
			}
		}

		return $addedArray;
	}

	/* 2. NEW Function to fetch all the products using the table productsales */
	public function prodSAProd1DataTable($repwhere, $specific_order, $search_key, $specific_search = array(), $recodeArray = array(), $branchNo = 0)
	{
		/* Number of rows to be displayed on one page */
		$limit = 10;

		$start = isset($_POST['start']) ? $_POST['start'] : 0;
		$length = isset($_POST['length']) ? $_POST['length'] : $limit;

		$search = isset($_POST['search']) ? $_POST['search'] : array();
		$search_key = $search['value'];

		$this->builtQueryForProductSales($search_key, $repwhere, $recodeArray, $branchNo, false);
		$skip_columns_in_search_key = array();

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

		$this->db->order_by($specific_order['by'], $specific_order['dir']);
		$this->db->limit($length, $start);

		$query = $this->db->get();

		$result_array = $query->result_array();
		$numerical_result = array();
		$canSeeMargins = canSeeMargins();

		foreach ($result_array as $ra)
		{
			$YoY1Sales = (!empty($ra['YoY1Sales']) ? $ra['YoY1Sales'] : '0.00');
			$salesytd = (!empty($ra['salesytd']) ? $ra['salesytd'] : '0.00');

			if (floatval($salesytd) > floatval($YoY1Sales))
			{
				$class = "greenrow";
			}
			elseif (floatval($salesytd) < floatval($YoY1Sales))
			{
				$class = "redrow";
			}
			else
			{
				$class = "";
			}

			$ra['freeqty'] = (!empty($ra['freeqty']) ? $ra['freeqty'] : '0.00');
			$radescription = "<a href='".site_url("products/details/".base64_encode($ra['code']))."' data-class='".$class."' class='color-identifier'>".$ra['description']."</a>";
			$salesmtd = (!empty($ra['salesmtd']) ? $ra['salesmtd'] : '0.00');
			$qtymtd = (!empty($ra['qtymtd']) ? $ra['qtymtd'] : '0.00');
			$marginmtdpc = (!empty($ra['marginmtdpc']) ? $ra['marginmtdpc'] : '0.00');
			$qtyytd = (!empty($ra['qtyytd']) ? $ra['qtyytd'] : '0.00');
			$marginytdpc = (!empty($ra['marginytdpc']) ? $ra['marginytdpc'] : '0.00');
			$YoY2Sales = (!empty($ra['YoY2Sales']) ? $ra['YoY2Sales'] : '0.00');
			$purchaseqty = (!empty($ra['purchaseqty']) ? $ra['purchaseqty'] : '0.00');

			$resultItem = array
			(
				$ra['code'],
				$ra['pac4code'],
				$radescription,
				number_format($ra['freeqty'], 2),
				number_format($purchaseqty, 2),
				number_format($salesytd, 2),
				number_format($qtyytd, 2),
				number_format($ra['salesdiff'], 2),
				number_format($ra['qtydiff'], 2),
				number_format($YoY1Sales, 2),
				number_format($ra['YoY1Qty'], 2),
				number_format($YoY2Sales, 2),
				number_format($ra['YoY2Qty'], 2),
				number_format($salesmtd, 2),
				number_format($qtymtd, 2),
			);

			if ($canSeeMargins)
			{
				$resultItem[] = number_format($marginmtdpc, 2);
				$resultItem[] = number_format($marginytdpc, 2);
			}

			$numerical_result[] = $resultItem;
		}

		return array('numerical_array' => $numerical_result, 'original_array' => $result_array);
	}

	/* 2. NEW Function to fetch all the products without the limit using the table prod */
	public function prodSAProd1DataTableNoLimit($repwhere, $specific_order, $search_key, $specific_search = array(), $recodeArray = array(), $branchNo = 0, $with_keys = false, $totals = false)
	{
		/* Number of rows to be displayed on one page */
		$this->builtQueryForProductSales($search_key, $repwhere,$recodeArray,$branchNo, $totals, $specific_search);
		$query = $this->db->get();

		$result_array = $query->result_array();
		$numerical_result = array();
		$canSeeMargins = canSeeMargins();

		foreach ($result_array as $ra)
		{
			$YoY1Sales = (!empty($ra['YoY1Sales']) ? $ra['YoY1Sales'] : '0.00');
			$salesmtd = (!empty($ra['salesmtd']) ? $ra['salesmtd'] : '0.00');

			if (floatval($salesmtd) > floatval($YoY1Sales))
			{
				$class = "greenrow";
			}
			elseif (floatval($salesmtd) < floatval($YoY1Sales))
			{
				$class = "redrow";
			}
			else
			{
				$class = "";
			}

			$ra['freeqty'] = (!empty($ra['freeqty']) ? $ra['freeqty'] : '0.00');
			$radescription = "<a href='".site_url("products/details/".$ra['code'])."' data-class='".$class."' class='color-identifier'>".$ra['description']."</a>";
			$freeQty = $ra['freeqty'];
			$qtymtd = (!empty($ra['qtymtd']) ? $ra['qtymtd'] : '0.00');
			$marginmtdpc = (!empty($ra['marginmtdpc']) ? $ra['marginmtdpc'] : '0.00');
			$salesytd = (!empty($ra['salesytd']) ? $ra['salesytd'] : '0.00');
			$qtyytd = (!empty($ra['qtyytd']) ? $ra['qtyytd'] : '0.00');
			$marginytdpc = (!empty($ra['marginytdpc']) ? $ra['marginytdpc'] : '0.00');
			$YoY2Sales = (!empty($ra['YoY2Sales']) ? $ra['YoY2Sales'] : '0.00');
			$purchaseqty = (!empty($ra['purchaseqty']) ? $ra['purchaseqty'] : '0.00');

			if (!$with_keys)
			{
				$resultItem = array
				(
					$ra['code'],
					$ra['pac4code'],
					$radescription,
					$freeQty,
					$purchaseqty,
					$salesytd,
					$qtyytd,
					number_format($ra['salesdiff'], 2),
					number_format($ra['qtydiff'], 2),
					$YoY1Sales,
					$ra['YoY1Qty'],
					$YoY2Sales,
					$ra['YoY2Qty'],
					$salesmtd,
					$qtymtd,
				);

				if ($canSeeMargins)
				{
					$resultItem[] = number_format($marginmtdpc, 2);
					$resultItem[] = number_format($marginytdpc, 2);
				}
					
				$numerical_result[] = $resultItem;
			}
			else
			{
				if ($totals)
				{
					$resultItem = array
					(
						'freeqty'     => intval($freeQty),
						'purchaseqty' => intval($purchaseqty),
						'salesytd'    => floatval($salesytd),
						'qtyytd'      => floatval($qtyytd),
						'sales_diff'  => floatval(number_format($ra['salesdiff'], 2)),
						'qty_diff'    => floatval(number_format($ra['qtydiff'], 2)),
						'YoY1Sales'   => floatval($YoY1Sales),
						'YoY1Qty'     => floatval($ra['YoY1Qty']),
						'YoY2Sales'   => floatval($YoY2Sales),
						'YoY2Qty'     => floatval($ra['YoY2Qty']),
						'salesmtd'    => floatval($salesmtd),
						'qtymtd'      => floatval($qtymtd),
					);

					if ($canSeeMargins)
					{
						$resultItem['marginmtdpc'] = floatval(number_format($marginmtdpc, 2));
						$resultItem['marginytdpc'] = floatval(number_format($marginytdpc, 2));
					}

					$resultItem['costsytd'] = floatval($ra['costsytd']);
					$resultItem['costsmtd'] = floatval($ra['costsmtd']);

					$numerical_result[] = $resultItem;
				}
				else
				{
					$resultItem = array
					(
						'code'        => $ra['code'],
						'pac4code'    => $ra['pac4code'],
						'description' => $radescription,
						'freeqty'     => $freeQty,
						'purchaseqty' => $purchaseqty,
						'salesytd'    => $salesytd,
						'qtyytd'      => $qtyytd,
						'sales_diff'  => number_format($ra['salesdiff'], 2),
						'qty_diff'    => number_format($ra['qtydiff'], 2),
						'YoY1Sales'   => $YoY1Sales,
						'YoY1Qty'     => $ra['YoY1Qty'],
						'YoY2Sales'   => $YoY2Sales,
						'YoY2Qty'     => $ra['YoY2Qty'],
						'salesmtd'    => $salesmtd,
						'qtymtd'      => $qtymtd,
					);

					if ($canSeeMargins)
					{
						$resultItem['marginmtdpc'] = number_format($marginmtdpc, 2);
						$resultItem['marginytdpc'] = number_format($marginytdpc, 2);
					}

					$resultItem['costsytd'] = $ra['costsytd'];
					$resultItem['costsmtd'] = $ra['costsmtd'];

					$numerical_result[] = $resultItem;
				}
			}
		}

		return array('numerical_array' => $numerical_result, 'original_array' => $result_array);
	}

	public function getFreeQtyValueFromStock() {
		$this->db->select("prodcode, SUM(freeqty) as totalfreeqty");

		$this->db->from("stock");
		$this->db->group_by('stock.prodcode');
		$query = $this->db->get();
		$result_array = $query->result_array();
		$freeQtyArray=array();
		foreach ($result_array as $stockData) {
			$freeQtyArray[$stockData['prodcode']]=$stockData['totalfreeqty'];
		}
		return $freeQtyArray;
	}

	/* 3. NEW Function to make the product sales query part. */
	public function builtQueryForProductSales($search_key, $repwhere, $recodeArray = array(), $branchNo = 0, $totals, $specific_search = array())
	{
		$this->load->model('common_model');
		$seemarginsAr = $this->common_model->loggedin_userdetail();

		$proRataCoefficient = $this->common_model->getWorkingDayProRataCoefficient(date("Y/m/d"));
		$proRataAdjustmentYoY1Sales = "";
		$proRataAdjustmentYoY2Sales = "";
		$proRataAdjustmentYoY1Qty = "";
		$proRataAdjustmentYoY2Qty = "";

		/* Generating repcode */
		$repWhereCondition = $this->common_model->makeRepcodeCondition($repwhere, 'ps.currepcode', false, true);
		$year0 = date("Y");
		$year1 = $year0 - 1;
		$year2 = $year0 - 2;
		$thismonth = date("m");
		$YoYEnd1 = $thismonth + 11;
		$YoYStart1 = ($YoYEnd1 - $thismonth) + 1;
		$YoYEnd2 = $thismonth + 23;
		$YoYStart2 = ($YoYEnd2 - $thismonth) + 1;

		for ($x = $YoYStart1; $x <= $YoYEnd1; $x++ )
		{
			if ($x != $YoYStart1)
			{
				$query1.= "+";
				$queryq1.= "+";
			}
			else
			{
				$proRataAdjustmentYoY1Sales = "-ps.msales".$x."*(1-".$proRataCoefficient.")";
				$proRataAdjustmentYoY1Qty = "-ps.mquantity".$x."*(1-".$proRataCoefficient.")";
			}

			$query1.= "ps.msales".$x;
			$queryq1.= "ps.mquantity".$x;
		}

		for ($x = $YoYStart2; $x <= $YoYEnd2; $x++ )
		{
			if ($x != $YoYStart2)
			{
				$query2.= "+";
				$queryq2.= "+";
			}
			else
			{
				$proRataAdjustmentYoY2Sales = "-ps.msales".$x."*(1-".$proRataCoefficient.")";
				$proRataAdjustmentYoY2Qty = "-ps.mquantity".$x."*(1-".$proRataCoefficient.")";
			}

			$query2.= "ps.msales".$x;
			$queryq2.= "ps.mquantity".$x;
		}

		$yearOnYearData = ", ROUND(SUM(".$query1.$proRataAdjustmentYoY1Sales."), 2) as YoY1Sales, ROUND(SUM(".$query2.$proRataAdjustmentYoY2Sales."), 2) AS YoY2Sales, ROUND(SUM(".$queryq1.$proRataAdjustmentYoY1Qty."), 2) AS YoY1Qty, ROUND(SUM(".$queryq2.$proRataAdjustmentYoY2Qty."), 2) AS YoY2Qty";

		// echo $yearOnYearData;
		// exit;

		if (!!$seemarginsAr["seemargins"])
		{
			$selMarginM = "(SUM(ps.mmargin0)*100/SUM(ps.msales0)) as marginmtdpc,";
			$selMarginY = ", (SUM(ps.ymargin0)*100/SUM(ps.ysales0)) as marginytdpc";
		}
		else
		{
			$selMarginM = "";
			$selMarginY = "";
		}

		if ($totals)
		{
			$this->db->select("SUM(p.freeqty) as freeqty, SUM(p.purchaseqty) as purchaseqty, SUM(ps.msales0) as salesmtd, SUM(ps.mquantity0) AS qtymtd,".$selMarginM." SUM(ps.ysales0) as salesytd, SUM(ps.yquantity0) as qtyytd".$selMarginY." ".$yearOnYearData.", SUM(ps.mcost0) as costsmtd, SUM(ps.ycost0) as costsytd, IFNULL(IF((SUM(ps.ysales0)-SUM(".$query1.$proRataAdjustment."))/SUM(".$query1.$proRataAdjustment.")*100 > 100, ((SUM(ps.ysales0)-SUM(".$query1.$proRataAdjustment."))/SUM(".$query1.$proRataAdjustment.")*100), (SUM(ps.ysales0)-SUM(".$query1.$proRataAdjustment."))/SUM(".$query1.$proRataAdjustment.")*100), IF(SUM(ps.ysales0) > 0.00, CAST(100.00 AS DECIMAL), CAST(0.00 AS DECIMAL))) as salesdiff, IFNULL(IF((SUM(ps.yquantity0)-(".$queryq1."))/(".$queryq1.")*100 > 100, ((SUM(ps.yquantity0)-(".$queryq1."))/(".$queryq1.")*100)-100, (SUM(ps.yquantity0)-(".$queryq1."))/(".$queryq1.")*100), IF(SUM(ps.yquantity0) > 0.00, CAST(100.00 AS DECIMAL), CAST(0.00 AS DECIMAL))) as qtydiff", false);
		}
		else
		{
			$this->db->select("p.code,p.pac4code, p.description, p.freeqty as freeqty, p.purchaseqty as purchaseqty, SUM(ps.msales0) as salesmtd, SUM(ps.mquantity0) AS qtymtd,".$selMarginM." SUM(ps.ysales0) as salesytd, SUM(ps.yquantity0) as qtyytd".$selMarginY." ".$yearOnYearData.", SUM(ps.mcost0) as costsmtd, SUM(ps.ycost0) as costsytd, IFNULL(IF((SUM(ps.ysales0)-SUM(".$query1.$proRataAdjustment."))/SUM(".$query1.$proRataAdjustment.")*100 > 100, ((SUM(ps.ysales0)-SUM(".$query1.$proRataAdjustment."))/SUM(".$query1.$proRataAdjustment.")*100), (SUM(ps.ysales0)-SUM(".$query1.$proRataAdjustment."))/SUM(".$query1.$proRataAdjustment.")*100), IF(SUM(ps.ysales0) > 0.00, CAST(100.00 AS DECIMAL), CAST(0.00 AS DECIMAL))) as salesdiff, IFNULL(IF((SUM(ps.yquantity0)-(".$queryq1."))/(".$queryq1.")*100 > 100, ((SUM(ps.yquantity0)-(".$queryq1."))/(".$queryq1.")*100)-100, (SUM(ps.yquantity0)-(".$queryq1."))/(".$queryq1.")*100), IF(SUM(ps.yquantity0) > 0.00, CAST(100.00 AS DECIMAL), CAST(0.00 AS DECIMAL))) as qtydiff", false);
		}

		$this->db->from("product p");
		$this->db->join("productsales ps", "ps.prodcode = p.code ".$repWhereCondition, "left");
			// code for user branch filter on 3rd nov
		if (!empty($branchNo))
		{
			$this->db->where('ps.branch =', $branchNo);
		}

		if (!empty($recodeArray))
		{
			$this->db->where_in('ps.repcode', $recodeArray);
		}

		// code for user branch filter on 3rd nov
		/* Generating branch condition */
		$this->common_model->makeBranchCondition('ps.branch', false);

		if (!$totals)
		{
			$this->db->group_by('p.code');
		}

		$this->productSalesWhereCondition($search_key, $specific_search);
	}

	/* 4. NEW Function to make product sales where condition */
	public function productSalesWhereCondition($search_key, $specific_search)
	{
		$like = array();

		if (isset($search_key) && "" != trim($search_key))
		{
			$like[] = "LOWER(p.code) LIKE LOWER('%".$search_key."%')";
			$like[] = "LOWER(p.pac4code) LIKE LOWER('%".$search_key."%')";
			$like[] = "LOWER(p.description) LIKE LOWER('%".$search_key."%')";
			// $this->db->like('p.code', $search_key);
			// $this->db->or_like('p.pac4code', $search_key);
			// $this->db->or_like('p.description', $search_key);
		}

		if (!empty($like))
		{
			$this->db->where("(".implode(" OR ", $like).")");
		}

		if (!empty($specific_search))
		{
			foreach ($specific_search as $column => $search)
			{
				if ($search != '')
				{
					$this->db->where("$column COLLATE UTF16_GENERAL_CI LIKE '%".strtolower($search)."%'");
				}
			}
		}
	}

	/* 5. New function to fetch the count of products using the table productsales */
	public function prodSAProd1DataTableCount($repwhere, $search_key, $specific_search = array(), $recodeArray = array(), $branchNo = 0)
	{
		$this->builtQueryForProductSales($search_key, $repwhere, $recodeArray, $branchNo, false);

		$skip_columns_in_search_key = array();

		foreach ($specific_search as $key => $specific)
		{
			if (isset($specific) && "" != trim($specific))
			{
				$this->db->like($key, $specific);
				$skip_columns_in_search_key[] = $key;
			}
		}

		$query = $this->db->get();
		$count = $query->num_rows();

		return $count;
	}

	/* 6. New function to fetch the product details using the new table customerprodsales. This function have its repwhere and branch conditions in-built. */
	public function prodDetails2Customerprodsales($repwhere, $page, $code, $startthisyearrmonth, $curyearmonth, $recodeArray = array(), $branchNo = null)
	{
		$this->load->model('common_model');

		$proRataCoefficient = $this->common_model->getWorkingDayProRataCoefficient(date("Y/m/d"));
		$proRataAdjustment = "";

		/* Generating branch condition */
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
			if ($y != 0)
			{
				$query1.= "+";
			}
			else
			{
				$proRataAdjustment = "-s.msales".$x."*(1-".$proRataCoefficient.")";
			}

			$query1.= "s.msales".$x;

			$y++;
		}

		$y = 0;

		for ($x = $YoYStart2; $x <= $YoYEnd2; $x++)
		{
			if ($y != 0)
			{
				$query2.= "+";
			}

			$query2.= "s.msales".$x;

			$y++;
		}

		$yearOnYearData = ", ROUND(SUM(".$query1.$proRataAdjustment."), 2) as YoY1Sales, SUM(".$query2.") AS YoY2Sales ";
		//end yoy 5-12-2017
		$this->db->select('c.account, c.name, IF('.$query1.', ROUND((s.ysales0-SUM('.$query1.$proRataAdjustment.'))*100/SUM('.$query1.$proRataAdjustment.'), 2), 100) as diff_percent, SUM(s.mquantity0) AS qtymtd, SUM(s.msales0) AS salesmtd, (SUM(s.mmargin0)*100/SUM(s.msales0)) AS marginmtdpc, SUM(s.yquantity0) AS qtyytd, SUM(s.ysales0) as salesytd, (SUM(s.ymargin0)*100/SUM(s.ysales0)) AS marginytdpc'.$yearOnYearData.', SUM(mcost0) as costsmtd, SUM(ycost0) as costsytd', false);
		$this->db->from('customer c');
		$this->db->join("customerprodsales s", "c.account = s.account", "left");
		$this->db->join("product p", "s.prodcode = p.code", "left");

		if (5 != $page)
		{
			$this->db->where('p.pac'.$page.'code', $code);
		}
		else
		{
			$this->db->where('p.code', $code);
		}

		/* Generating repcode condition */
		// code for user branch filter on 21st nov
		if (!empty($branchNo))
		{
			$this->db->where('c.branch =', $branchNo);
		}

		if (!empty($recodeArray))
		{
			$this->db->where_in('c.repcode', $recodeArray);
		}

		if (!empty($this->in))
		{
			$where_user_selected = "c.account IN (".implode(", ", $this->in).")";
			$this->db->where($where_user_selected);
		}

		// code for user branch filter on 21st nov
		$this->db->group_by('c.account');
		$this->db->order_by('c.account', 'ASC');

		$query = $this->db->get();

		return $query->result_array();
	}

	/* 7. New function to fetch the product sales details using the customerprodsales table. This function have its repwhere and branch conditions in-built. */
	public function prodSADetailsUsingCustomerProdSales($repwhere, $prodcode)
	{
		$this->load->model('common_model');

		/* Generating repcode condition */
		$this->common_model->makeRepcodeCondition($repwhere, 'c.repcode', true, false);

		/* Generating branch condition */
		$this->common_model->makeBranchCondition('s.branch', false);

		$proRataCoefficient = $this->common_model->getWorkingDayProRataCoefficient(date("Y/m/d"));
		$proRataAdjustment = "";

		//yoy 5-12-2017
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
			if ($y != 0)
			{
				$query1.= "+";
			}
			else
			{
				$proRataAdjustment = "-s.msales".$x."*(1-".$proRataCoefficient.")";
			}

			$query1.= "s.msales".$x;

			$y++;
		}

		$y = 0;

		for ($x = $YoYStart2; $x <= $YoYEnd2; $x++)
		{
			if ($y != 0)
			{
				$query2.= "+";
			}

			$query2.= "s.msales".$x;

			$y++;
		}

		$yearOnYearData = ", ROUND(SUM(".$query1.$proRataAdjustment."), 2) as YoY1Sales, SUM(".$query2.") AS YoY2Sales ";

		$this->db->select('c.account, c.name, ROUND((SUM(s.ysales0)-SUM('.$query1.$proRataAdjustment.'))*100/SUM('.$query1.$proRataAdjustment.'), 2) as diff_percent, SUM(s.mquantity0) AS qtymtd, SUM(s.msales0) AS salesmtd, (SUM(s.mmargin0*100)/SUM(s.msales0)) AS marginmtdpc, SUM(s.yquantity0) as qtyytd, SUM(s.ysales0) as salesytd, (SUM(s.ymargin0*100)/SUM(s.ysales0)) as marginytdpc'.$yearOnYearData, false);
		$this->db->from('customerprodsales s');
		$this->db->join("customer c", "c.account = s.account ".$repWhereCondition, "inner");
		$this->db->join("product p", "p.code = s.prodcode", "left");
		$this->db->where( "p.code = '".$prodcode."'");
		$this->db->group_by('c.account');
		$this->db->order_by('c.account', 'ASC');
		$query = $this->db->get();

		if ($_SERVER['REMOTE_ADDR'] == "182.65.49.4")
		{
		//	echo $this->db->last_query();
		}

		return $query->result_array();
	}

	/* 8. New function to create a file to be exported in CSV or EXCEL */
	public function csv_export($repwhere, $search_key) {
		if ("nosearchedvalue"==$search_key) {
			$search_key = "";
		}

		$this->builtQueryForProductSales($search_key, $repwhere);
		$query = $this->db->get();
		$this->load->dbutil();
		$opt= $this->dbutil->csv_from_result($query);


	$head_value = array("pac4code","qtymtd", "salesmtd", "marginmtdpc","qtyytd","salesytd", "marginytdpc","YoY1Sales","YoY2Sales");
	$curryear=date('Y');
	$saleyear1="Sales ".($curryear-1);
	$saleyear2="Sales ".($curryear-2);
	$new_head   =array("PAC4", "Qty MTD", "Sales MTD","GM% MTD","QTY YTD", "Sales YTD", "GM% YTD",$saleyear1,$saleyear2);


	$head_value1 = array('""');

	$new_head1   =array('"0.00"');

  $opt = str_replace($head_value, $new_head, $opt);

echo $opt = str_replace($head_value1, $new_head1, $opt);
	}
	public function prd1_csv_export($repwhere, $search_key,$ind) {
		if ("nosearchedvalue"==$search_key) {
			$search_key = "";
		}
		$ind=str_replace("PAC", "", $ind);

		$this->load->model('common_model');
		$pacNSales = 'pac'.$ind.'sales';
		$pacN = 'pac'.$ind;
		$year0 = date("Y");
		$year1 = $year0 - 1;
		$year2 = $year0 - 2;
		$thismonth = date("m");
	$YoYEnd1 = $thismonth + 11;
	$YoYStart1 = ($YoYEnd1 - $thismonth) + 1;
	$YoYEnd2 = $thismonth + 23;
	$YoYStart2 = ($YoYEnd2 - $thismonth) + 1;
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
		$yearOnYearData=", SUM(".$query1.") as YoY1Sales, SUM(".$query2.") AS YoY2Sales ";
		$this->db->select('p.code, p.description, SUM(s.msales0) as salesmtd, SUM(s.mquantity0) as qtymtd, (SUM(s.mmargin0)/SUM(s.msales0))*100 as marginmtdpc, SUM(s.ysales0) as salesytd, SUM(s.yquantity0) as qtyytd, (SUM(s.ymargin0)/SUM(s.ysales0))*100 as marginytdpc'.$yearOnYearData);
		$this->db->from($pacN.' p');

		/* Generating repcode condition */
		$repWhereCondition = $this->common_model->makeRepcodeCondition($repwhere, 's.currepcode', false, true);

		/* Generating branch condition */
		$this->common_model->makeBranchCondition('s.branch', false);

		$this->db->join($pacNSales.' s', 's.pac'.$ind.'code = p.code '.$repWhereCondition, 'left');

		$this->db->group_by('p.code');
		$this->db->order_by('p.code', 'ASC');

		$query = $this->db->get();


		$this->load->dbutil();
		$opt=$this->dbutil->csv_from_result($query);

		$curryear=date('Y');
		$saleyear1="Sales ".($curryear-1);
		$saleyear2="Sales ".($curryear-2);
$head_value = array("code","description","qtymtd", "salesmtd", "marginmtdpc","qtyytd","salesytd", "marginytdpc","YoY1Sales","YoY2Sales");

$new_head   =array("Code","Description", "Qty MTD", "Sales MTD","GM% MTD","QTY YTD", "Sales YTD", "GM% YTD",$saleyear1,$saleyear2);


$head_value1 = array('""');

$new_head1   =array('"0.00"');

  $opt = str_replace($head_value, $new_head, $opt);

echo $opt = str_replace($head_value1, $new_head1, $opt);
	}

	public function getProductStockList($prodCode)
	{
		$this->db->select('stock.branch, branch.name, stock.totalqty, stock.backorderqty, stock.allocatedqty, stock.reservedqty, stock.forwardsoqty, stock.freeqty, stock.unitofstock, stock.purchaseqty , stock.backtobackqty, stock.dateexpected, stock.purchaseqty');
		$this->db->from('stock');
		$this->db->join("branch", "stock.branch = branch.branch", "left");
		$this->db->where('stock.prodcode', $prodCode);
		$this->db->order_by('stock.branch', "ASC");

		$query = $this->db->get();
		$result_array = $query->result_array();

		return $result_array;
	}

	  public function getUserDetails($userid) {
		// $this->db->select('*');
		// $this->db->from('users');
		// $this->db->where('userid', $userid);
		// $query = $this->db->get();
		// return $query->row_array();
$this->db->select('*');
		$this->db->from('users');
		$this->db->where('userid ', $userid);
		$query = $this->db->get();
		$str = $query->row_array();
		//print_r($str);
		$repwhere = $str['repcode'];
		$repwhere = $repwhere.(EMPTY($str['repcode_2']) ? "" :  ",".$str['repcode_2']).(EMPTY($str['repcode_3']) ? "" :  ",".$str['repcode_3']).(EMPTY($str['repcode_4']) ? "" :  ",".$str['repcode_4']).(EMPTY($str['repcode_5']) ? "" :  ",".$str['repcode_5']).(EMPTY($str['repcode_6']) ? "" :  ",".$str['repcode_6']).(EMPTY($str['repcode_7']) ? "" :  ",".$str['repcode_7']).(EMPTY($str['repcode_8']) ? "" :  ",".$str['repcode_8']).(EMPTY($str['repcode_9']) ? "" :  ",".$str['repcode_9']).(EMPTY($str['repcode_10']) ? "" :  ",".$str['repcode_10']);
		$str['repwhere'] = $repwhere."";

		$str['name'] = trim($str['firstname'])." ".$str['surname'];


			$repclause = "IN ('$str[repcode]'";
	$repclause = $repclause.(EMPTY($str['repcode_2']) ? "" :  ",'$str[repcode_2]'").(EMPTY($str['repcode_3']) ? "" :  ",'$str[repcode_3]'").(EMPTY($str['repcode_4']) ? "" :  ",'$str[repcode_4]'").(EMPTY($str['repcode_5']) ? "" :  ",'$str[repcode_5]'").(EMPTY($str['repcode_6']) ? "" :  ",'$str[repcode_6]'").(EMPTY($str['repcode_7']) ? "" :  ",'$str[repcode_7]'").(EMPTY($str['repcode_8']) ? "" :  ",'$str[repcode_8]'").(EMPTY($str['repcode_9']) ? "" :  ",'$str[repcode_9]'").(EMPTY($str['repcode_10']) ? "" :  ",'$str[repcode_10]'");
	$repclause = $repclause.")";
$str['repclause']=$repclause;
//echo $repclause; die;
//echo $this->db->last_query();
		return $str;




	}
		public function savelog($description, $type="U") {
		$data['userid'] = $this->session->userdata('userid'); /* Logged in user id */
		$data['type'] = $type; /* U (for user)/S (for system) */
		$data['date'] = date('Y-m-d');
		$data['time'] = date('Y-m-d h:i:s');
		$data['description'] = $description;
		$this->db->insert('systemlog', $data);
	}

	public function addUniqueTargetToproductsalestarget($userid, $year, $month, $salestarget, $product_code) {
		if (!$this->checkTargetUniqueProducts($userid, $year, $month, $product_code)) {
			return "duplicate";
		} else {
			$data = array(
				"userid"	 => $userid,
				"yearmonth"	 => $year.$month,
				"salestarget" => $salestarget,
				"productcode"=>$product_code
			);

			$userDetails = $this->getUserDetails($userid);

			$table_name='productsalestarget';

			if ($this->db->insert($table_name, $data)) {
				return "success";
			} else {
				return "fail";
			}
		}
	}

	public function addUniqueTarget($userid, $year, $month, $salestarget,$page_code,$product_code) {
		if (!$this->checkTargetUnique($userid, $year, $month,$page_code,$product_code)) {
			return "duplicate";
		} else {
			$data = array(
				"userid"	 => $userid,
				"yearmonth"	 => $year.$month,
				"salestarget" => $salestarget,
				"pac".$page_code."code"=>$product_code
			);


			$userDetails = $this->getUserDetails($userid);

			$table_name='pac'.$page_code.'salestarget';

			if ($this->db->insert($table_name, $data)) {

			   // echo "Yes"; exit;
			//	$description = "New Sales Target added for the user #".$userid." - ".$userDetails['firstname']." ".$userDetails['surname'];
				//$this->savelog($description);
				return "success";
			} else {
				// echo "No"; exit;
				return "fail";
			}
		}
	}

	public function addUniqueTargetcsv($userid, $year, $month, $salestarget,$page_code,$product_code) {
		if (!$this->checkTargetUnique($userid, $year, $month,$page_code,$product_code)) {

$yearmonth = $year.$month;
$table_name='pac'.$page_code.'salestarget';
$pac_column='pac'.$page_code.'code';
$data = array(
				"salestarget" => $salestarget
			);

$this->db->query("update $table_name set salestarget=$salestarget where userid=$userid and yearmonth=$yearmonth and $pac_column='$product_code' ");


return "duplicate";

		} else {
			$data = array(
				"userid"	 => $userid,
				"yearmonth"	 => $year.$month,
				"salestarget" => $salestarget,
				"pac".$page_code."code"=>$product_code
			);


			$userDetails = $this->getUserDetails($userid);

			$table_name='pac'.$page_code.'salestarget';

			if ($this->db->insert($table_name, $data)) {

			   // echo "Yes"; exit;
			//	$description = "New Sales Target added for the user #".$userid." - ".$userDetails['firstname']." ".$userDetails['surname'];
				//$this->savelog($description);
				return "success";
			} else {
				// echo "No"; exit;
				return "fail";
			}
		}
	}


public function addProductsUniqueTargetcsv($userid, $year, $month, $salestarget,$page_code,$product_code) {
	if (!$this->checkTargetUniqueProducts($userid, $year, $month,$page_code,$product_code)) {
		$yearmonth = $year.$month;
		$table_name='productsalestarget';
		$pac_column='productcode';
		$data = array("salestarget" => $salestarget);
		$this->db->query("update $table_name set salestarget=$salestarget where userid=$userid and yearmonth=$yearmonth and $pac_column='$product_code' ");
		return "duplicate";
	} else {
		$data = array(
			"userid"	 => $userid,
			"yearmonth"	 => $year.$month,
			"salestarget" => $salestarget,
			"productcode"=>$product_code
		);
		$userDetails = $this->getUserDetails($userid);
		$table_name='productsalestarget';

		if ($this->db->insert($table_name, $data)) {
			return "success";
		} else {
			return "fail";
		}
	}
}

	public function checkTargetUnique($userid, $year, $month,$page_code,$product_code) {
		$yearmonth = $year.$month;
		$this->db->select('count(id) as cnt');
		$this->db->from('pac'.$page_code.'salestarget');
		$this->db->where('userid', $userid);
		$this->db->where('yearmonth', $yearmonth);
		 $this->db->where('pac'.$page_code.'code', $product_code);
		$query = $this->db->get();
		$result = $query->row_array();
		return intval($result['cnt'])==0;
	}

	public function checkTargetUniqueProducts($userid, $year, $month, $product_code) {
	$yearmonth = $year.$month;
	$this->db->select('count(id) as cnt');
			$this->db->from('productsalestarget');
			$this->db->where('userid', $userid);
			$this->db->where('yearmonth', $yearmonth);
			 $this->db->where('productcode', $product_code);
			$query = $this->db->get();
			$result = $query->row_array();
	return intval($result['cnt'])==0;
}




	public function get_salestarget($G_level,$userId=0,$branchNo=0,$page_code,$product_code)
	{
		if (5!=$page_code) {
			$table_name="pac".$page_code."salestarget";
		} else {
			$table_name="productsalestarget";
		}


		switch($G_level){
			case 'Company':
				if (5!=$page_code) {
					$result=$this->db->query("select ".$table_name.".*, concat(users.firstname,' ',users.surname) as username from ".$table_name." left join users on ".$table_name.".userid=users.userid where pac".$page_code."code='$product_code' order by ".$table_name.".yearmonth desc ")->result();
				} else {
					$result=$this->db->query("select ".$table_name.".*, concat(users.firstname,' ',users.surname) as username from ".$table_name." left join users on ".$table_name.".userid=users.userid where productcode='$product_code' order by ".$table_name.".yearmonth desc ")->result();
				}

				break;
			case 'User':
				if (5!=$page_code) {
					$result=$this->db->query("select ".$table_name.".*, concat(users.firstname,' ',users.surname) as username from ".$table_name." left join users on ".$table_name.".userid=users.userid where users.userid='$userId' and pac".$page_code."code='$product_code' order by ".$table_name.".yearmonth desc  ")->result();
				} else {
					$result=$this->db->query("select ".$table_name.".*, concat(users.firstname,' ',users.surname) as username from ".$table_name." left join users on ".$table_name.".userid=users.userid where users.userid='$userId' and productcode='$product_code' order by ".$table_name.".yearmonth desc  ")->result();
				}
				break;
			case 'Branch':
				if (5!=$page_code) {
					$result=$this->db->query("select ".$table_name.".*, concat(users.firstname,' ',users.surname) as username from ".$table_name." left join users on ".$table_name.".userid=users.userid where  pac".$page_code."code='$product_code' and users.branch='$branchNo' order by ".$table_name.".yearmonth desc  ")->result();
				} else {
					$result=$this->db->query("select ".$table_name.".*, concat(users.firstname,' ',users.surname) as username from ".$table_name." left join users on ".$table_name.".userid=users.userid where productcode='$product_code' and users.branch='$branchNo' order by ".$table_name.".yearmonth desc  ")->result();
				}
				break;
		}
		return $result;
	}

	public function get_productSalestarget($userDetails,$userId=0,$branchNo=0,$page_code,$product_code)
	{
		if ($userDetails["usertype"]=="B"){
			$G_level="Branch";
		} elseif (($userDetails["usertype"]=="A") && ($branchNo == 0) && ($userId == 0)){
			$G_level="Company";
		} elseif (($userDetails["usertype"]=="A") && ($branchNo > 0) && ($userId == 0)){
			$G_level="Branch";
			$G_branchno = $branchNo;
		} elseif (($userDetails["usertype"]=="A") && ($branchNo == 0) && ($userId > 0)){
			$G_level="User";
			$G_userId = $userId;
		} else {
			$G_level="User";
		}

		$table_name="productsalestarget";
		$product_code_decoded = base64_decode($product_code);
		$query = "";
		switch($G_level){
			case 'Company':
				$query = "select ".$table_name.".*, concat(users.firstname,' ',users.surname) as username from ".$table_name." left join users on ".$table_name.".userid=users.userid where productcode='$product_code_decoded' order by ".$table_name.".yearmonth desc ";
				break;
			case 'User':
				$query = "select ".$table_name.".*, concat(users.firstname,' ',users.surname) as username from ".$table_name." left join users on ".$table_name.".userid=users.userid where users.userid='$userId' and productcode='$product_code_decoded' order by ".$table_name.".yearmonth desc  ";
				break;
			case 'Branch':
				$query = "select ".$table_name.".*, concat(users.firstname,' ',users.surname) as username from ".$table_name." left join users on ".$table_name.".userid=users.userid where productcode='$product_code_decoded' and users.branch='$branchNo' order by ".$table_name.".yearmonth desc  ";
				break;
		}
		$result=$this->db->query($query)->result();
		return $result;
	}

public function updateYearMonth($id, $yearmonth,$page) {
		$data["yearmonth"] = $yearmonth;
	//	$description = $this->descriptionUpdateTarget($id, $data);
		$this->db->where('id', $id);
		$return = $this->db->update('pac'.$page.'salestarget', $data);
		if ($return) {
			//$this->savelog($description);
		}

		return $return;
	}

public function updateSalesTarget($id, $salestarget,$page) {
		$data["salestarget"] = $salestarget;
	//	$description = $this->descriptionUpdateTarget($id, $data);
		$this->db->where('id', $id);
		$return = $this->db->update('pac'.$page.'salestarget', $data);
		if ($return) {
		//	$this->savelog($description);
		}

		return $return;
	}

  public function getTargetDetails($id,$page) {
		$this->db->select('id, userid, yearmonth, salestarget');
		$this->db->from('pac'.$page.'salestarget');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row_array();
	}


public function deleteUserTarget($id,$page) {
		return $this->db->delete('pac'.$page.'salestarget', array('id' => $id));
	}





	// pac sales target dashboard




// public function getPac1SalesTargetDashboard($G_level,$userid=0,$branchNo=0,$repclause)
// {
//    $yearmonth=date("Y").date("m");


// //print_r($repclause); exit;

//     switch($G_level){
//         case 'Company':

//               $result= $this->db->query("select pac1.*,sum(pac1salestarget.salestarget) as salestarget from pac1 left join pac1salestarget on pac1.code=pac1salestarget.pac1code where pac1salestarget.yearmonth='$yearmonth'  GROUP BY pac1.code ")->result();
//                 $result2= $this->db->query("select sum(pac1sales.msales0) as salesmtd from pac1sales group by pac1code")->result();
//                 for ($i=0;$i<count($result); $i++)
//                 {
//                 $result[$i]->salesmtd=$result2[$i]->salesmtd;
//                 }

//                 break;

//            case 'User':

//              $result= $this->db->query("select pac1.*,sum(pac1salestarget.salestarget) as salestarget from pac1 left join pac1salestarget on pac1.code=pac1salestarget.pac1code  where pac1salestarget.userid='$userid' and pac1salestarget.yearmonth='$yearmonth' GROUP BY pac1.code  ")->result();
//                // $result2= $this->db->query("select sum(pac1sales.msales0) as salesmtd from pac1sales group by pac1code")->result();



// $sql="select sum(pac1sales.msales0) as salesmtd from pac1sales  ";
// $repclause=str_replace("IN ('", "", $repclause);
// $repclause=str_replace("')", "", $repclause);
// $repclause=str_replace("','", "|", $repclause);
// $rep=explode('|',$repclause);
// $i=0;
// foreach ($rep as $repclause)
// {
// if ($i==0)
// {
// $sql.=" where repcode='".$repclause."' ";    $i++;
// }
// else{
// $sql.=" or  repcode='".$repclause."' ";
// }
// }

// $sql.="group by pac1code";
// //echo $sql;
// $result2= $this->db->query($sql)->result();
// for ($i=0;$i<count($result); $i++)
// {
// $result[$i]->salesmtd=$result2[$i]->salesmtd;
// }








//                 break;

//                 case 'Branch':

//                     $result= $this->db->query("select pac1.*,sum(pac1salestarget.salestarget) as salestarget from pac1 left join pac1salestarget on pac1.code=pac1salestarget.pac1code  left join users on pac1salestarget.userid=users.userid where users.branch='$branchNo' and pac1salestarget.yearmonth='$yearmonth' GROUP BY pac1.code  ")->result();
//                     $result2= $this->db->query("select sum(pac1sales.msales0) as salesmtd from pac1sales where pac1sales.branch='$branchNo' group by pac1code")->result();


//                     for ($i=0;$i<count($result); $i++)
//                     {
//                     $result[$i]->salesmtd=$result2[$i]->salesmtd;
//                     }



//                     break;
//     }

//     return $result;


// }



	public function getPac1SalesTargetDashboard($G_level,$userid=0,$branchNo=0,$repclause)
	{
		$yearmonth=date("Y").date("m");
		switch($G_level){
			case 'Company':



				$result= $this->db->query("select '1' as 'tabl',pac1.description as description, pac1salestarget.pac1code as paccode,sum(pac1salestarget.salestarget) as salestarget from pac1salestarget LEFT JOIN pac1 ON pac1.code=pac1salestarget.pac1code where pac1salestarget.yearmonth='$yearmonth' GROUP BY pac1.code UNION select '2' as 'tabl',pac2.description as description, pac2salestarget.pac2code as paccode,sum(pac2salestarget.salestarget) as salestarget from pac2salestarget LEFT JOIN pac2 ON pac2.code=pac2salestarget.pac2code where pac2salestarget.yearmonth='$yearmonth' GROUP BY pac2.code UNION select '3' as 'tabl',pac3.description as description,pac3salestarget.pac3code as paccode,sum(pac3salestarget.salestarget) as salestarget from pac3salestarget LEFT JOIN pac3 ON pac3.code=pac3salestarget.pac3code where pac3salestarget.yearmonth='$yearmonth' GROUP BY pac3.code UNION select '4' as 'tabl',pac4.description as description,pac4salestarget.pac4code as paccode,sum(pac4salestarget.salestarget) as salestarget from pac4salestarget LEFT JOIN pac4 ON pac4.code=pac4salestarget.pac4code where pac4salestarget.yearmonth='$yearmonth' GROUP BY pac4.code UNION select '5' as 'tabl',product.description as description,productsalestarget.productcode as paccode,sum(productsalestarget.salestarget) as salestarget from productsalestarget LEFT JOIN product ON product.code=productsalestarget.productcode where productsalestarget.yearmonth='$yearmonth' GROUP BY product.code")->result();

				break;
			case 'User':

				$result= $this->db->query("select '1' as 'tabl',pac1.description as description, pac1salestarget.pac1code as paccode,sum(pac1salestarget.salestarget) as salestarget from pac1 left join pac1salestarget on pac1.code=pac1salestarget.pac1code  where pac1salestarget.userid='$userid' and pac1salestarget.yearmonth='$yearmonth' GROUP BY pac1.code UNION select '2' as 'tabl',pac2.description as description, pac2salestarget.pac2code as paccode,sum(pac2salestarget.salestarget) as salestarget from pac2 left join pac2salestarget on pac2.code=pac2salestarget.pac2code  where pac2salestarget.userid='$userid' and pac2salestarget.yearmonth='$yearmonth' GROUP BY pac2.code  UNION  select '3' as 'tabl',pac3.description as description,pac3salestarget.pac3code as paccode,sum(pac3salestarget.salestarget) as salestarget from pac3 left join pac3salestarget on pac3.code=pac3salestarget.pac3code  where pac3salestarget.userid='$userid' and pac3salestarget.yearmonth='$yearmonth' GROUP BY pac3.code  UNION select '4' as 'tabl',pac4.description as description,pac4salestarget.pac4code as paccode,sum(pac4salestarget.salestarget) as salestarget from pac4 left join pac4salestarget on pac4.code=pac4salestarget.pac4code  where pac4salestarget.userid='$userid' and pac4salestarget.yearmonth='$yearmonth' GROUP BY pac4.code UNION select '5' as 'tabl',product.description as description,productsalestarget.productcode as paccode,sum(productsalestarget.salestarget) as salestarget from product left join productsalestarget on product.code=productsalestarget.productcode  where productsalestarget.userid='$userid' and productsalestarget.yearmonth='$yearmonth' GROUP BY product.code")->result();
				break;

		   case 'Branch':
				$result= $this->db->query("select '1' as 'tabl',pac1.description as description, pac1salestarget.pac1code as paccode,sum(pac1salestarget.salestarget) as salestarget  from pac1 left join pac1salestarget on pac1.code=pac1salestarget.pac1code  left join users on pac1salestarget.userid=users.userid where users.branch='$branchNo' and pac1salestarget.yearmonth='$yearmonth' GROUP BY pac1.code  UNION select '2' as 'tabl',pac2.description as description, pac2salestarget.pac2code as paccode,sum(pac2salestarget.salestarget) as salestarget  from pac2 left join pac2salestarget on pac2.code=pac2salestarget.pac2code  left join users on pac2salestarget.userid=users.userid where users.branch='$branchNo' and pac2salestarget.yearmonth='$yearmonth' GROUP BY pac2.code UNION select '3' as 'tabl',pac3.description as description, pac3salestarget.pac3code as paccode,sum(pac3salestarget.salestarget) as salestarget  from pac3 left join pac3salestarget on pac3.code=pac3salestarget.pac3code  left join users on pac3salestarget.userid=users.userid where users.branch='$branchNo' and pac3salestarget.yearmonth='$yearmonth' GROUP BY pac3.code UNION select '4' as 'tabl',pac4.description as description, pac4salestarget.pac4code as paccode,sum(pac4salestarget.salestarget) as salestarget  from pac4 left join pac4salestarget on pac4.code=pac4salestarget.pac4code  left join users on pac4salestarget.userid=users.userid where users.branch='$branchNo' and pac4salestarget.yearmonth='$yearmonth' GROUP BY pac4.code

								UNION select '5' as 'tabl',product.description as description, productsalestarget.productcode as paccode,sum(productsalestarget.salestarget) as salestarget  from product left join productsalestarget on product.code=productsalestarget.productcode  left join users on productsalestarget.userid=users.userid where users.branch='$branchNo' and productsalestarget.yearmonth='$yearmonth' GROUP BY product.code")->result();

				break;
		}
		return $result;
	}

public function  getSalesTotalMonthWise($G_level,$userid=0,$branchNo=0,$repclause) {

$yearmonth=date("Y").date("m");
		switch($G_level){
			case 'Company':





$result2= $this->db->query("select sum(pac1sales.msales0) as salesmtd, pac1sales.pac1code as paccode from pac1sales group by pac1code UNION select sum(pac2sales.msales0) as salesmtd, pac2sales.pac2code as paccode from pac2sales group by pac2code UNION select sum(pac3sales.msales0) as salesmtd, pac3sales.pac3code as paccode from pac3sales group by pac3code  UNION select sum(pac4sales.msales0) as salesmtd, pac4sales.pac4code as paccode from pac4sales group by pac4code")->result();
	 $returnArray=array();
	 foreach ($result2 as $data) {
	  $returnArray[$data->paccode]=$data->salesmtd;
	 }

				break;
			case 'User':
			   $sql="select sum(pac1sales.msales0) as salesmtd from pac1sales ";
$sql1="";

						$repclause=str_replace("IN ('", "", $repclause);
						$repclause=str_replace("')", "", $repclause);
						$repclause=str_replace("','", "|", $repclause);
						$rep=explode('|',$repclause);
						$i=0;
						foreach ($rep as $repclause){
						if ($i==0){
						$sql1.=" where repcode='".$repclause."' ";   $i++;
						}
						else{
						$sql1.="  or  repcode='".$repclause."' ";
						}
						}
$sql.="group by pac1code";



$final_sql="select sum(pac1sales.msales0) as salesmtd from pac1sales ".$sql1." group by pac1code UNION select sum(pac2sales.msales0) as salesmtd from pac2sales ".$sql1."  group by pac2code UNION select sum(pac3sales.msales0) as salesmtd from pac3sales ".$sql1." group by pac3code UNION select sum(pac4sales.msales0) as salesmtd from pac4sales ".$sql1." group by pac4code ";

						$result2= $this->db->query($final_sql)->result();
						$returnArray=array();
					foreach ($result2 as $data) {
					$returnArray[$data->paccode]=$data->salesmtd;
					}

				break;

		   case 'Branch':
			 $result2= $this->db->query("select sum(pac1sales.msales0) as salesmtd from pac1sales where pac1sales.branch='$branchNo' group by pac1code UNION select sum(pac2sales.msales0) as salesmtd from pac2sales where pac2sales.branch='$branchNo' group by pac2code UNION  select sum(pac3sales.msales0) as salesmtd from pac3sales where pac3sales.branch='$branchNo' group by pac3code UNION select sum(pac4sales.msales0) as salesmtd from pac4sales where pac4sales.branch='$branchNo' group by pac4code")->result();
					$returnArray=array();
					foreach ($result2 as $data) {
					$returnArray[$data->paccode]=$data->salesmtd;
					}
				break;
		}
		return $returnArray;


// $result2= $this->db->query("select sum(pac1sales.msales0) as salesmtd, pac1sales.pac1code as paccode from pac1sales group by pac1code UNION select sum(pac2sales.msales0) as salesmtd, pac2sales.pac2code as paccode from pac2sales group by pac2code UNION select sum(pac3sales.msales0) as salesmtd, pac3sales.pac3code as paccode from pac3sales group by pac3code  UNION select sum(pac4sales.msales0) as salesmtd, pac4sales.pac4code as paccode from pac4sales group by pac4code")->result();
//      $returnArray=array();
//      foreach ($result2 as $data) {
//       $returnArray[$data->paccode]=$data->salesmtd;
//      }
//      return $returnArray;




	}


// public function getPac2SalesTargetDashboard_limited($G_level,$userid=0,$branchNo=0)
// {
//      return $this->db->query("select pac2.*, sum(pac2sales.msales0) as salesmtd ,pac2salestarget.salestarget from pac2 left join pac2sales on pac2.code=pac2sales.pac2code left join pac2salestarget on pac2.code=pac2salestarget.pac2code group by pac2sales.pac2code limit 8")->result();
// }
	public function getPac2SalesTargetDashboard($G_level,$userid=0,$branchNo=0,$repclause)
	{

		 $yearmonth=date("Y").date("m");

		 switch($G_level){
			case 'Company':


				  $result= $this->db->query("select pac2.*,sum(pac2salestarget.salestarget) as salestarget from pac2 left join pac2salestarget on pac2.code=pac2salestarget.pac2code where pac2salestarget.yearmonth='$yearmonth'  GROUP BY pac2.code  ")->result();
				$result2= $this->db->query("select sum(pac2sales.msales0) as salesmtd from pac2sales group by pac2code ")->result();
				for ($i=0;$i<count($result); $i++)
				{
				$result[$i]->salesmtd=$result2[$i]->salesmtd;
				}

				break;

			   case 'User':

				 $result= $this->db->query("select pac2.*,sum(pac2salestarget.salestarget) as salestarget from pac2 left join pac2salestarget on pac2.code=pac2salestarget.pac2code  where pac2salestarget.userid='$userid' and pac2salestarget.yearmonth='$yearmonth' GROUP BY pac2.code   ")->result();
				//$result2= $this->db->query("select sum(pac2sales.msales0) as salesmtd from pac2sales group by pac2code ")->result();


				$sql="select sum(pac2sales.msales0) as salesmtd from pac2sales  ";
					$repclause=str_replace("IN ('", "", $repclause);
					$repclause=str_replace("')", "", $repclause);
					$repclause=str_replace("','", "|", $repclause);
					$rep=explode('|',$repclause);
					$i=0;
					foreach ($rep as $repclause)
					{
					if ($i==0)
					{
					$sql.=" where repcode='".$repclause."' ";    $i++;
					}
					else{
					$sql.=" or  repcode='".$repclause."' ";
					}
					}

					$sql.="group by pac2code";
//echo $sql;
					$result2= $this->db->query($sql)->result();
					for ($i=0;$i<count($result); $i++)
					{
					$result[$i]->salesmtd=$result2[$i]->salesmtd;
					}




					break;

					case 'Branch':

					$result= $this->db->query("select pac2.*,sum(pac2salestarget.salestarget) as salestarget from pac2 left join pac2salestarget on pac2.code=pac2salestarget.pac2code  left join users on pac2salestarget.userid=users.userid where users.branch='$branchNo' and pac2salestarget.yearmonth='$yearmonth' GROUP BY pac2.code   ")->result();
					$result2= $this->db->query("select sum(pac2sales.msales0) as salesmtd from pac2sales where pac2sales.branch='$branchNo' group by pac2code ")->result();


					for ($i=0;$i<count($result); $i++)
					{
					$result[$i]->salesmtd=$result2[$i]->salesmtd;
					}



						break;
		}

	   // print_r($result);  exit;
		return $result;









	}
	public function getPac3SalesTargetDashboard($G_level,$userid=0,$branchNo=0,$repclause)
	{

		 $yearmonth=date("Y").date("m");


		switch($G_level){
			case 'Company':

				  $result= $this->db->query("select pac3.*,sum(pac3salestarget.salestarget) as salestarget from pac3 left join pac3salestarget on pac3.code=pac3salestarget.pac3code  where pac3salestarget.yearmonth='$yearmonth' GROUP BY pac3.code ")->result();
				$result2= $this->db->query("select sum(pac3sales.msales0) as salesmtd from pac3sales group by pac3code")->result();
				for ($i=0;$i<count($result); $i++)
				{
				$result[$i]->salesmtd=$result2[$i]->salesmtd;
				}

				break;

			   case 'User':

				 $result= $this->db->query("select pac3.*,sum(pac3salestarget.salestarget) as salestarget from pac3 left join pac3salestarget on pac3.code=pac3salestarget.pac3code  where pac3salestarget.userid='$userid' and pac3salestarget.yearmonth='$yearmonth'  GROUP BY pac3.code  ")->result();
				//$result2= $this->db->query("select sum(pac3sales.msales0) as salesmtd from pac3sales group by pac3code")->result();

				$sql="select sum(pac3sales.msales0) as salesmtd from pac3sales  ";
					$repclause=str_replace("IN ('", "", $repclause);
					$repclause=str_replace("')", "", $repclause);
					$repclause=str_replace("','", "|", $repclause);
					$rep=explode('|',$repclause);
					$i=0;
					foreach ($rep as $repclause)
					{
					if ($i==0)
					{
					$sql.=" where repcode='".$repclause."' ";    $i++;
					}
					else{
					$sql.=" or  repcode='".$repclause."' ";
					}
					}

					$sql.="group by pac3code";

					$result2= $this->db->query($sql)->result();
					for ($i=0;$i<count($result); $i++)
					{
					$result[$i]->salesmtd=$result2[$i]->salesmtd;
					}




					break;

					case 'Branch':

					$result= $this->db->query("select pac3.*,sum(pac3salestarget.salestarget) as salestarget from pac3 left join pac3salestarget on pac3.code=pac3salestarget.pac3code  left join users on pac3salestarget.userid=users.userid where users.branch='$branchNo' and pac3salestarget.yearmonth='$yearmonth'  GROUP BY pac3.code  ")->result();
					$result2= $this->db->query("select sum(pac3sales.msales0) as salesmtd from pac3sales where pac3sales.branch='$branchNo' group by pac3code")->result();


					for ($i=0;$i<count($result); $i++)
					{
					$result[$i]->salesmtd=$result2[$i]->salesmtd;
					}



						break;
		}

		return $result;










	}
	public function getPac4SalesTargetDashboard($G_level,$userid=0,$branchNo=0,$repclause)
	{


		 $yearmonth=date("Y").date("m");

		switch($G_level){
			case 'Company':

				  $result= $this->db->query("select pac4.*,sum(pac4salestarget.salestarget) as salestarget from pac4 left join pac4salestarget on pac4.code=pac4salestarget.pac4code where pac4salestarget.yearmonth='$yearmonth'   GROUP BY pac4.code ")->result();
				$result2= $this->db->query("select sum(pac4sales.msales0) as salesmtd from pac4sales group by pac4code")->result();
				for ($i=0;$i<count($result); $i++)
				{
				$result[$i]->salesmtd=$result2[$i]->salesmtd;
				}

				break;

			   case 'User':

				 $result= $this->db->query("select pac4.*,sum(pac4salestarget.salestarget) as salestarget from pac4 left join pac4salestarget on pac4.code=pac4salestarget.pac4code  where pac4salestarget.userid='$userid' and pac4salestarget.yearmonth='$yearmonth' GROUP BY pac4.code  ")->result();
				//$result2= $this->db->query("select sum(pac4sales.msales0) as salesmtd from pac4sales group by pac4code")->result();

			   $sql="select sum(pac4sales.msales0) as salesmtd from pac4sales  ";
					$repclause=str_replace("IN ('", "", $repclause);
					$repclause=str_replace("')", "", $repclause);
					$repclause=str_replace("','", "|", $repclause);
					$rep=explode('|',$repclause);
					$i=0;
					foreach ($rep as $repclause)
					{
					if ($i==0)
					{
					$sql.=" where repcode='".$repclause."' ";    $i++;
					}
					else{
					$sql.=" or  repcode='".$repclause."' ";
					}
					}

					$sql.="group by pac4code";

					$result2= $this->db->query($sql)->result();
					for ($i=0;$i<count($result); $i++)
					{
					$result[$i]->salesmtd=$result2[$i]->salesmtd;
					}




					break;

					case 'Branch':

					$result= $this->db->query("select pac4.*,sum(pac4salestarget.salestarget) as salestarget from pac4 left join pac4salestarget on pac4.code=pac4salestarget.pac4code  left join users on pac4salestarget.userid=users.userid where users.branch='$branchNo' and pac4salestarget.yearmonth='$yearmonth' GROUP BY pac4.code  ")->result();
					$result2= $this->db->query("select sum(pac4sales.msales0) as salesmtd from pac4sales where pac4sales.branch='$branchNo' group by pac4code")->result();


					for ($i=0;$i<count($result); $i++)
					{
					$result[$i]->salesmtd=$result2[$i]->salesmtd;
					}



						break;
		}

		return $result;









	}





	public function get_users()
	{
		return $this->db->query("select userid,concat(firstname,' ',surname) as username from users ")->result();
	}

	public function csv_exportCustom($repwhere, $search_key, $specific_search=array(),$recodeArray=array(),$branchNo=0) {
		if ("nosearchedvalue"==$search_key) {
			$search_key = "";
		}
		$this->builtQueryForProductSales($search_key, $repwhere,$recodeArray,$branchNo);

		//$this->builtQueryForProductSales($search_key, $repwhere);
		$query = $this->db->get();
		$this->load->dbutil();
		$opt= $this->dbutil->csv_from_result($query);


		$head_value = array("pac4code","qtymtd", "salesmtd", "marginmtdpc","qtyytd","salesytd", "marginytdpc","YoY1Sales","YoY2Sales");
		$curryear=date('Y');
		$saleyear1="Sales ".($curryear-1);
		$saleyear2="Sales ".($curryear-2);
		$new_head   =array("PAC4", "Qty MTD", "Sales MTD","GM% MTD","QTY YTD", "Sales YTD", "GM% YTD",$saleyear1,$saleyear2);
		$head_value1 = array('""');
		$new_head1   =array('"0.00"');
		$opt = str_replace($head_value, $new_head, $opt);
		echo $opt = str_replace($head_value1, $new_head1, $opt);
	}

	public function prd1_csv_exportCustom($repwhere, $search_key,$ind,$recodeArray=array(),$branchNo=0) {
		$this->load->model('common_model');
		$seemarginsAr = $this->common_model->loggedin_userdetail();
		if ("nosearchedvalue"==$search_key) {
			$search_key = "";
		}
		$ind=str_replace("PAC", "", $ind);

		$pacNSales = 'pac'.$ind.'sales';
		$pacN = 'pac'.$ind;
		$year0 = date("Y");
		$year1 = $year0 - 1;
		$year2 = $year0 - 2;
		$thismonth = date("m");
	$YoYEnd1 = $thismonth + 11;
	$YoYStart1 = ($YoYEnd1 - $thismonth) + 1;
	$YoYEnd2 = $thismonth + 23;
	$YoYStart2 = ($YoYEnd2 - $thismonth) + 1;
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
		$yearOnYearData=", SUM(".$query1.") as YoY1Sales, SUM(".$query2.") AS YoY2Sales ";
		if (!!$seemarginsAr["seemargins"]) {
			$selMarginM = '(SUM(s.mmargin0)/SUM(s.msales0))*100 as marginmtdpc,';
			$selMarginY = ', (SUM(s.ymargin0)/SUM(s.ysales0))*100 as marginytdpc';
		} else {
			$selMarginM = '';
			$selMarginY = '';
		}
		$this->db->select('p.code, p.description, SUM(s.msales0) as salesmtd, SUM(s.mquantity0) as qtymtd, '. $selMarginM .' SUM(s.ysales0) as salesytd, SUM(s.yquantity0) as qtyytd'. $selMarginY.$yearOnYearData);
		$this->db->from($pacN.' p');

		/* Generating repcode condition */
		$repWhereCondition = $this->common_model->makeRepcodeCondition($repwhere, 's.currepcode', false, true);

		/* Generating branch condition */
		$this->common_model->makeBranchCondition('s.branch', false);

		$this->db->join($pacNSales.' s', 's.pac'.$ind.'code = p.code '.$repWhereCondition, 'left');
		// code for user branch filter on 3rd nov
		if (!empty($branchNo)) {
			$this->db->where('s.branch =', $branchNo);
		}
		if (!empty($recodeArray)) {
			$this->db->where_in('s.repcode', $recodeArray);
		}
		// code for user branch filter on 3rd nov
		$this->db->group_by('p.code');
		$this->db->order_by('p.code', 'ASC');

		$query = $this->db->get();


		$this->load->dbutil();
		$opt=$this->dbutil->csv_from_result($query);

		$curryear=date('Y');
		$saleyear1="Sales ".($curryear-1);
		$saleyear2="Sales ".($curryear-2);
$head_value = array("code","description","qtymtd", "salesmtd", "marginmtdpc","qtyytd","salesytd", "marginytdpc","YoY1Sales","YoY2Sales");

$new_head   =array("Code","Description", "Qty MTD", "Sales MTD","GM% MTD","QTY YTD", "Sales YTD", "GM% YTD",$saleyear1,$saleyear2);


$head_value1 = array('""');

$new_head1   =array('"0.00"');

  $opt = str_replace($head_value, $new_head, $opt);

echo $opt = str_replace($head_value1, $new_head1, $opt);
	}

	public function getYearStartMonth() {
		$this->db->select('yearstartmonth');
		$this->db->from('system');
		$query = $this->db->get();
		$res = $query->row_array();

		return isset($res) ? $res['yearstartmonth'] : 1;
	}
}


function mapRepcodeFactor($result_array) {
	return "'".$result_array["cr_account"]."'";
}
