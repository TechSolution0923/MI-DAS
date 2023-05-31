<?php

class Common_model extends Model {

	/* Common function to produce the correct repcode array */
	public function repcode($repcode="") {
		$userid = $this->userSelection();
		$repcodes = $this->selectRepCodesFromUsereps($userid);
		if (!empty($repcodes)) {
			return $repcodes;
		} else {
			return explode(",",$repcode);
		}
	}

	/* Common function to produce the correct repcode string ready to be used in query */
	public function queryRepcode() {
		$userid = $this->userSelection();
		$repcodes = $this->selectRepCodesFromUsereps($userid);
	//	$repcodes = $this->selectRepCodes($userid); // old code

		$reparr = $repcodes;

		if (!empty($reparr)) {

			return "'".implode("','", $reparr)."'";

		} else {
			return "";
		}

	}

	/* Common function to produce the correct repcode string ready to be used in query */
	public function queryRepcodeSeperateTable() {
		$userid = $this->userSelection();

		$repcodes = $this->selectRepCodesFromUsereps($userid);

		$reparr = $repcodes;
		if (!empty($reparr)) {

			return "'".implode("','", $reparr)."'";

		} else {
			return "";
		}

	}

	/* Function for user selection */
	public function userSelection() {
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
		return $loggedInUserId;
	}

	/* Common function to get user type */
	public function getUserType($userid) {
		$this->db->select('usertype');
		$this->db->from('users');
		$this->db->where('userid', $userid);
		$query = $this->db->get();
		$ret = $query->row_array();
	//	echo $this->db->last_query();
		return $ret['usertype'];
	}

	// /* Function for users column selection */
	// public function selectRepCodes($userid) {
	// 	$nrepcodes = array();
	// 	if (0!=$userid){
	// 		$this->db->select('users.repcode, repcode_2, repcode_3, repcode_4, repcode_5');
	// 		$this->db->from('users');
	// 		$this->db->where('userid', $userid);
	// 		$query = $this->db->get();

	// 		$repcodes = $query->row_array();

	// 		foreach ($repcodes as $rc) {
	// 			if (""!=$rc) {
	// 				array_push($nrepcodes, $rc);
	// 			}
	// 		}
	// 	}
	// 	return $nrepcodes;
	// }

	public function selectRepCodesFromUsereps($userid) {
		$nrepcodes = array();
		if (0!=$userid){

			$this->db->select('repcode');
			$this->db->from('userreps');
			$this->db->where('userid', (int)$userid);
			$query = $this->db->get();

			$repcodes = $query->result('array');

			foreach ($repcodes as $rc) {
				if (""!=$rc) {
					array_push($nrepcodes, $rc['repcode']);
				}
			}
		}
		return $nrepcodes;
	}

	/* Function to make the repcode condition */
	public function makeRepcodeCondition($field='s.currepcode', $addCondition = FALSE, $returnString = TRUE, $checkLoggedInUser = TRUE) {
		$repWhereCondition = '';
		if ($addCondition) {
			$repwhere = $this->queryRepcode();
			$selectedBranch = $this->session->userdata("selectedBranch");
			$condition = false;
			if (!$checkLoggedInUser) {
				$condition = (""!=$repwhere) && empty($selectedBranch['branchno']);
			} else {
				$selectedUser = $this->session->userdata("selectedUser");
				$loggedInUserId = $selectedUser['userid'];
				$condition = (0!=$loggedInUserId) && (""!=$repwhere) && empty($selectedBranch['branchno']);
			}

			if ($condition) {
				if (!$returnString) {
				//	$this->db->where_in($field, $repwhere);
				} else {
					$repWhereCondition = ' AND '.$field.' IN ('.$repwhere.')';
				}
			}
		} else {

			$repWhereCondition = '';
		}

		return $repWhereCondition;
	}

	/* Function to make the repcode condition */
	public function makeRepcodeConditionNew($repwhere, $field = 's.currepcode', $addCondition = false, $returnString = true, $checkLoggedInUser = true)
	{
		$repWhereCondition = '';

		if ($addCondition)
		{
			$repwhere = $this->queryRepcodeSeperateTable($repwhere);
			$selectedBranch = $this->session->userdata("selectedBranch");
			$condition = false;

			if (!$checkLoggedInUser)
			{
				$condition = ("" != $repwhere) && empty($selectedBranch['branchno']);
			}
			else
			{
				$selectedUser = $this->session->userdata("selectedUser");
				$loggedInUserId = $selectedUser['userid'];
				$condition = (0 != $loggedInUserId) && ("" != $repwhere) && empty($selectedBranch['branchno']);
			}

			if ($condition)
			{
				if (!$returnString)
				{
					$this->db->where_in($field, $repwhere);
				}
				else
				{
					$repWhereCondition = ' AND '.$field.' IN ('.$repwhere.')';
				}
			}
		}
		else
		{
			$repWhereCondition = '';
		}

		return $repWhereCondition;
	}

	/* Function to make the branch condition */
	public function makeBranchCondition($field = 's.branch', $addCondition = FALSE) {
		if ($addCondition) {
			$selectedBranch = $this->session->userdata("selectedBranch");
			 if (!empty($selectedBranch['branchno'])) {
				$this->db->where($field, $selectedBranch['branchno']);
			}
		} else {
			// Don't want to add the branch condition.
		}
	}

	/* Function to print the last executed query */
	public function showLastQuery($functionName="", $display_queries = false) {
		if ($display_queries) {
			$rand = rand(1,9999999);
			echo "<div id='handel_".$rand."' onclick='$(\"#".$rand."\").toggle();' style='width: 100px;height: 30px;z-index: 999999;background: white;color: black;position: relative;'>Show/hide</div><div class='alert alert-info' id='".$rand."'' style='z-index:999999; position: relative;'> Function (".$functionName.") : <textarea class='form-control'>".$this->db->last_query()."</textarea></div>";
		}
	}

	public function loggedin_userdetail()
	{
		$userid = $this->session->userdata("userid");

		$queryStr = "SELECT * from `users` WHERE `userid`='".$userid."'";
		$query = $this->db->query($queryStr);

		return $query->row_array();
	}

	public function arr_mod($arrayToModify)
	{
		$marray = array();
		$currentMonth = date("m");
		$currentYear = date("Y");

		for ($i = 0; $i <= 35; $i++)
		{
			$present = intval($currentMonth);

			if ($currentMonth < 1)
			{
				$present = $currentMonth = 12;
				$currentYear = $currentYear-1;
			}

			$currentMonth--;
			$present = str_pad($present, 2, '0', STR_PAD_LEFT);
			$marray[$i]["yearmonth"] = $currentYear.$present;
			$marray[$i]["sales"] = $arrayToModify[0]["m".$i];
			$marray[$i]["cost"] = $arrayToModify[1]["c".$i];
		}

		return $marray;
	}

	/* This function takes the array of monthly sales for last two years and month till date */
	public function array_modification($arrayToModify)
	{
		$currentYear = intval(date('Y'));
		$prevYear = $currentYear-1;
		$pprevYear = $prevYear-1;

		$currentMonth = intval(date('m'));

		$allowedYearMonth = array();

		for ($cyear = $currentYear; $cyear >= $currentYear-2; $cyear--)
		{
			for ($ym = 1; $ym <= 12; $ym++)
			{
				$month = $ym > 9 ? $ym : '0'+$ym;

				if (intval($month) <= $currentMonth)
				{
					$allowedYearMonth[] = $cyear.$month;
				}
			}
		}

		$modified_array = array();
		$modified_array2 = array();
		$modified_array3 = array();
		$totalLength = sizeof($arrayToModify[0]);

		for ($i = 0; $i < $totalLength; $i++)
		{
			$yearmonth = date('Ym', strtotime(date('Ym')." -".$i." month"));

			$modified_array[] = array
			(
				'yearmonth' => $yearmonth,
				'sales'     => $arrayToModify[0]["m".$i],
				'cost'     	=> $arrayToModify[1]["c".$i]
			);

			$modified_array2[$yearmonth] = $arrayToModify[0]["m".$i];
		}

		for ($y = date("Y"); $y >= date("Y")-2; $y--)
		{
			for ($mon = 12; $mon > 0; $mon--)
			{
				if ($mon < 10)
				{
					$mon = "0".$mon;
				}

				$modified_array3[$y.$mon] = !!$modified_array2[$y.$mon] ? $modified_array2[$y.$mon] : "0.00";
			}
		}

		$modified_array = $this->arr_mod($arrayToModify);

		return array($modified_array, $modified_array2, $modified_array3);
	}

	/**Function to read the query file and return the built queries  */
	public function queryMaker($query_file, $className = "", $search=array(), $replace=array()) {
		$this->load->helper('file');

		if (""!=trim($className)) {
			$query_file_path = APPPATH . 'modules' . DIRECTORY_SEPARATOR  . $className. DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR .  $query_file;
		} else {
			$query_file_path = APPPATH . 'models' . DIRECTORY_SEPARATOR .  $query_file;
		}

		$query_str = read_file($query_file_path);
		$query_str = str_replace($search, $replace, $query_str);
		return $query_str;
	}

	public function selectPreviousYearsMonthlySales($tableAlias="CS") {
		$year0 = date("Y");
		$year1 = $year0 - 1;
		$year2 = $year0 - 2;
		$thismonth 	= date("m");
		$YoYEnd1 	= $thismonth + 11;
		$YoYStart1	= ($YoYEnd1 - $thismonth) + 1;
		$YoYEnd2 	= $thismonth + 23;
		$YoYStart2	= ($YoYEnd2 - $thismonth) + 1;
		$y = 0;

		for ($x = $YoYStart1; $x <= $YoYEnd1; $x++ ) {
			if (!$y == 0) {
				$query1 .= "+";
			}
			$query1 .= "`".$tableAlias."`.`msales".$x."`";
			$y++;
		}

		$y = 0;
		for ($x = $YoYStart2; $x <= $YoYEnd2; $x++ ) {
			if (!$y == 0) {
				$query2 .= "+";	// Add a + to the end if this isnt the first time in. Want to add up all the columns in the range
			}
			$query2 .= "`".$tableAlias."`.`msales".$x."`";	// Add the sales fields
			$y++;
		}

		return array(
			"YoY1Sales"=>$query1,
			"YoY2Sales"=>$query2
		);
	}

	public function getWorkingDaysRow($date)
	{
		$this->db->select('dayno, totdays');
		$this->db->from('workingdays');
		$this->db->where('date', $date);
		$query = $this->db->get();
		$str = $query->row_array();

		return $str;
	}

	public function getWorkingDayProRataCoefficient($date)
	{
		$proRata = date("d") / 30; // failsafe to current daynumber / 30 as an approximation if data in workingdays table is not found / invalid

		$workingDaysRow = $this->getWorkingDaysRow(date("Y/m/d"));

		if (!is_array($workingDaysRow))
		{
			return $proRata;
		}

		if (!isset($workingDaysRow['dayno']) || !is_numeric($workingDaysRow['dayno']))
		{
			return $proRata;
		}

		if (!isset($workingDaysRow['totdays']) || !is_numeric($workingDaysRow['totdays']))
		{
			return $proRata;
		}

		$dayno = intval($workingDaysRow['dayno']);
		$totdays = intval($workingDaysRow['totdays']);

		if ($totdays == 0)
		{
			return $proRata;
		}

		$proRata = $dayno / $totdays;

		return $proRata;
	}

	public function constructSingleLineAddress($data)
	{
		$addressString = "";

		for ($i = 1; $i < 6; $i++)
		{
			if (isset($data['address'.$i]) && !empty($data['address'.$i]))
			{
				$addressString.= $data['address'.$i].", ";
			}
		}

		if (isset($data['postcode']) && !empty($data['postcode']))
		{
			$addressString.= $data['postcode'];
		}

		$addressString = rtrim($addressString, ", ");

		return $addressString;
	}
}
