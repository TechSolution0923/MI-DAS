<?php

class Site_model extends Model {
	/**
	 * get_card_slides method
	 *
	 * @author		Virtual Emmployee PVT. LTD.
	 * @Descrption	Return slide Data
	 * @Created Date     20-01-2016
	 * @Updated Date
	 */
	public function get_user_login($uid,$pass) {
		$salt1="qm&h";
		$salt2="pg!@";
		$mypassword = stripslashes($pass);
		$newpass = $this->db->escape_str($mypassword);
		$mypassword = "$salt1$newpass$salt2";

		$usrId = stripslashes($uid);
		$uid = $this->db->escape_str($usrId);

		$this->db->where('email', $uid);
		$this->db->where('password', md5($mypassword));
		$this->db->where('active', '1'); /* Only login if the user is active */
		$query = $this->db->get('users');
		//echo $this->db->last_query();
		if ($query->num_rows == 1)
		{
			return $query->row();
		}
	}

	public function get_active($uid,$pass) {
		$salt1="qm&h";
		$salt2="pg!@";
		$mypassword = stripslashes($pass);
		$newpass = $this->db->escape_str($mypassword);
		$mypassword = "$salt1$newpass$salt2";

		$usrId = stripslashes($uid);
		$uid = $this->db->escape_str($usrId);

		$this->db->where('email', $uid);
		$this->db->where('password', md5($mypassword));
		$query = $this->db->get('users');
		return $query->row();
	}

	public function get_user($email) {

		$usrId = stripslashes($email);
		$uid = $this->db->escape_str($usrId);

		$this->db->where('email', $uid);
		$query = $this->db->get('users');
//		echo $this->db->last_query();
		if ($query->num_rows == 1)
		{
			return $query->row_array();
		}
	}

	public function get_user_encrypt($enc) {

		$this->db->where('md5(90 * 13 + userid)=', $enc);
		$query = $this->db->get('users');
//		echo $this->db->last_query();
		if ($query->num_rows == 1)
		{
			return $query->row_array();
		}
	}

	public function update_user_password($pass,$enc) {
		$user = $this->get_user_encrypt($enc);
		if ($user['userid']>0){
			$salt1="qm&h";
			$salt2="pg!@";
			$mypassword = stripslashes($pass);
			$newpass = $this->db->escape_str($mypassword);
			$mypassword = "$salt1$newpass$salt2";
			$data = array( 'password' => md5($mypassword) );
			$this->db->where('userid',$user['userid']);
			$this->db->update('users', $data);
//			echo $this->db->last_query();
			return $user['userid'];
		}else{
			return 0;
		}
	}
	/// KPI last Update///

	public function kpiLastupdate() {
		$this->db->select('kpislastupdated');
		$this->db->from('system');

		$query = $this->db->get();
		$str = $query->row_array();
		return $str;

	}
	/// End KPI last Update///
	/// KPI last Update///

	public function workingDays($G_todaysdate) {
		$this->db->select('dayno,totdays');
		$this->db->from('workingdays');
	 $this->db->where('date ', $G_todaysdate);
		$query = $this->db->get();
		$str = $query->row_array();
	   //echo $this->db->last_query(); die;
		return $str;

	}
	/// End KPI last Update///

	public function is_logged_in()
	{
		$is_logged_in = $this->session->userdata('is_logged_in');
		if (!isset($is_logged_in) || $is_logged_in != true)
		{
			return false;
		}else{
			return true;
		}
	}

	public function getUserDetails($userId)
	{
		$this->db->select("u.userid, u.email, u.password, u.firstname, u.surname, u.thislogin, u.lastlogin, u.branch, u.usertype, u.k8userid, u.administrator, u.manageruserid, u.active, u.seemargins, u.editnotes, u.editterms, u.edittargets, u.salesemail, u.kpithreshold1, u.kpithreshold2, u.marginok, u.margingood, u.seeomr, ur.repcode");
		$this->db->from("users u");
		$this->db->join("userreps ur", "ur.userid = u.userid", "LEFT");
		$this->db->where("u.userid", $userId);
		$query = $this->db->get();
		$rows = $query->result_array();

		$userDetails = array();
		$repcodes = array();

		$i = 0;

		foreach ($rows as $row)
		{
			if ($i == 0)
			{
				$userDetails['userid'] = $row['userid'];
				$userDetails['email'] = $row['email'];
				$userDetails['password'] = $row['password'];
				$userDetails['firstname'] = $row['firstname'];
				$userDetails['surname'] = $row['surname'];
				$userDetails['name'] = trim($row['firstname'])." ".$row['surname'];
				$userDetails['thislogin'] = $row['thislogin'];
				$userDetails['lastlogin'] = $row['lastlogin'];
				$userDetails['branch'] = $row['branch'];
				$userDetails['usertype'] = $row['usertype'];
				$userDetails['k8userid'] = $row['k8userid'];
				$userDetails['administrator'] = $row['administrator'];
				$userDetails['manageruserid'] = $row['manageruserid'];
				$userDetails['active'] = $row['active'];
				$userDetails['seemargins'] = $row['seemargins'];
				$userDetails['monthlyemail'] = $row['monthlyemail'];
				$userDetails['editnotes'] = $row['editnotes'];
				$userDetails['editterms'] = $row['editterms'];
				$userDetails['edittargets'] = $row['edittargets'];
				$userDetails['salesemail'] = $row['salesemail'];
				$userDetails['kpithreshold1'] = $row['kpithreshold1'];
				$userDetails['kpithreshold2'] = $row['kpithreshold2'];
				$userDetails['marginok'] = $row['marginok'];
				$userDetails['margingood'] = $row['margingood'];
				$userDetails['seeomr'] = $row['seeomr'];
			}

			if (!is_null($row['repcode']))
			{
				$repcodes[] = $row['repcode'];
			}

			$i++;
		}

		$userDetails['repwhere'] = implode(",", $repcodes);
		$userDetails['repclause'] = "IN ('".implode("','", $repcodes)."')";

		return $userDetails;
	}

	public function getBranch($branchno){
		$this->db->select('name');
		$this->db->from('branch');
		$this->db->where('branch', $branchno);
		$query = $this->db->get();
		$str = $query->row_array();
//		echo $this->db->last_query();
		return $str['name'];
	}

	public function getAllBranches(){
		$this->db->select('branch, name');
		$this->db->from('branch');
		$query = $this->db->get();
		$str = $query->result_array();
		return $str;
	}

	public function getMaxDate($repclause){
		$selectedBranch = $this->session->userdata("selectedBranch");
		$this->load->model('common_model');
		$repclause = $this->common_model->repcode($repclause);
		$selectedUser = $this->session->userdata("selectedUser");
		$loggedInUserId = $selectedUser['userid'];
		$this->db->select('MAX(date) as date');
		$this->db->from('salesanalysis');
		if (0!=$loggedInUserId && !empty($repclause) && empty($selectedBranch['branchno'])) {
			$this->db->where_in('repcode', $repclause);
		}
		$this->db->where('date < CURDATE()');
		$query = $this->db->get();
		$str = $query->row();

//		echo "<br />To select max date : ".$this->db->last_query()."<br />";
		return $str->date;
	}

	   public function getThresholdcomp(){
			$this->db->select('*');
		$this->db->from('company');

		$query = $this->db->get();
		$str = $query->row_array();


//		echo $this->db->last_query();
		return $str;
	}
	   public function getThresholdbranch($branchno){
			$this->db->select('*');
		$this->db->from('branch');
	   $this->db->where('branch',$branchno);
		$query = $this->db->get();
		$str = $query->row_array();


//		echo $this->db->last_query();
		return $str;
	}
	public function getThresholduser($userid){
			$this->db->select('*');
		$this->db->from('users');
	   $this->db->where('userid',$userid);
		$query = $this->db->get();
		$str = $query->row_array();


//		echo $this->db->last_query();
		return $str;
	}

	public function getSalesRepLastSales($lastsalesdate, $repclause, $G_userid, $G_branchno, $G_level) {
		if ($G_level=="Company") {
			$this->db->select('sum(sales) as sales, sum(cost) as cost');
			$this->db->from('replastsales');
			$this->db->where('date', $lastsalesdate);
			$query = $this->db->get();
			$str = $query->result_array();
		}
		else if ($G_level=="User") {
			$rep=explode(",",$repclause);
			$this->db->select('sum(sales) as sales, sum(cost) as cost');
			$this->db->from('replastsales');
			$i=0;

			foreach ($rep as $repc) {
				$ids[]=$repc;
			}

			$this->db->where_in('repcode', $ids);
			$this->db->where('date', $lastsalesdate);
			$query = $this->db->get();
			$str = $query->result_array();
		}
		else if ($G_level=="Branch") {
			$this->db->select('sum(sales) as sales, sum(cost) as cost');
			$this->db->from('replastsales');
			$this->db->where('date', $lastsalesdate);
			$this->db->where('branch =', $G_branchno);
			$query = $this->db->get();
			$str = $query->result_array();
		}

		return $str[0];

	}

	public function getSalesRepTarget($curyearmonth,$G_userid,$G_branchno,$G_level){

//debug
		//echo $curyearmonth.' uidididididi'.$G_userid;die;
	 if ( $G_level=="Company")
		{//debug
			/*$this->db->select('yearmonth,SUM(salestarget) as saletarget');

			$this->db->from('branchsalestarget');
			$this->db->where('yearmonth =', $curyearmonth);

			 $query = $this->db->get();
				$str = $query->result_array();
			*/
			//new code
			$this->db->select('yearmonth,SUM(salestarget) as saletarget');
			$this->db->from('usersalestarget');
			$this->db->where('yearmonth =', $curyearmonth);
			$this->db->where('userid =', $G_userid);
			$query = $this->db->get();
			$str = $query->result_array();
			//print_r($str);die;
		 }  //debug
		if ( $G_level=="User")
		{//d   //debug
			$this->db->select('yearmonth,SUM(salestarget) as saletarget');

			$this->db->from('usersalestarget');
			$this->db->where('yearmonth =', $curyearmonth);
				$this->db->where('userid =', $G_userid);

			 $query = $this->db->get();
				$str = $query->result_array();
		} //debug
		  if ( $G_level=="Branch")
		{//d
		$this->db->select('yearmonth,SUM(salestarget) as saletarget');

			$this->db->from('branchsalestarget');
			$this->db->where('yearmonth =', $curyearmonth);

			 $query = $this->db->get();
				 $this->db->where('branch =', $G_branchno);
				$str = $query->result_array();

		 //   $query = "SELECT SUM(salestarget), marginok, margingood FROM branchsalestarget WHERE branch = $G_branchno AND yearmonth = $curyearmonth";
		}

		   //echo $sql = $this->db->last_query();   exit;
		return $str[0];
	}


		public function getmonthlySalesRepTarget($soyyearmont,$G_userid,$G_branchno,$G_level){

//debug

	 if ( $G_level=="Company")
		{//debug
			$this->db->select('yearmonth,,SUM(salestarget) as saletarget');

			$this->db->from('branchsalestarget');
			$this->db->where('yearmonth  >=', $soyyearmont);
			 $this->db->order_by('yearmonth','ASC');
			 $query = $this->db->get();
				$str = $query->result_array();

		 }  //debug
		if ( $G_level=="User")
		{//d   //debug
			$this->db->select('yearmonth,,SUM(salestarget) as saletarget');

			$this->db->from('usersalestarget');
			$this->db->where('yearmonth  >=', $soyyearmont);
				$this->db->where('userid =', $G_userid);
			 $this->db->order_by('yearmonth','ASC');
			 $query = $this->db->get();
				$str = $query->result_array();
		   // $query = "SELECT SUM(salestarget) FROM usersalestarget WHERE userid = $G_userid AND yearmonth = $curyearmonth";
		} //debug
		  if ( $G_level=="Branch")
		{//d
		$this->db->select('yearmonth,,SUM(salestarget) as saletarget');

			$this->db->from('branchsalestarget');
			$this->db->where('yearmonth >=', $soyyearmont);
			 $this->db->order_by('yearmonth','ASC');
			 $query = $this->db->get();
				 $this->db->where('branch =', $G_branchno);
				$str = $query->result_array();

		 //   $query = "SELECT SUM(salestarget), marginok, margingood FROM branchsalestarget WHERE branch = $G_branchno AND yearmonth = $curyearmonth";
		}
	  //   echo $sql = $this->db->last_query();
	   //exit;
		return $str[0];
	}

public function getSalesAnalisys($curyearmonth,$repclause,$G_userid,$G_branchno,$G_level) {
	$this->load->model('common_model');

	$select = array();
	for($i=0; $i<36; $i++) {
		array_push($select, 'SUM(s.msales'.$i.') as m'.$i);
		array_push($select, 'SUM(s.mcost'.$i.') as c'.$i);
	}
	$this->db->select(join($select, ", "));
	$this->db->from('repsales s');

	if ($G_level=="User") {
		$rep=explode(",",$repclause);
		$i=0;
		foreach ($rep as $repclause) {
			if ($i==0) {
				$this->db->where('s.repcode', $repclause);
				$i++;
			} else {
				$this->db->or_where('s.repcode', $repclause);
			}
		}
	}
		  
	if ($G_level=="Branch") {
		$this->db->where('s.branch', $G_branchno);
	}

	$query = $this->db->get();
	$str = $query->result_array();

	$filterered_array = array();

	foreach($str[0] as $key => $value)
	{
		if (preg_match('/^m\d{1,2}$/',$key))
		{
			$filterered_array[0][$key] = $value;
		}
		else if (preg_match('/^c\d{1,2}$/',$key))
		{
			$filterered_array[1][$key] = $value;
		}
	}

	$modified_array = $this->common_model->array_modification($filterered_array);

	return $modified_array[0];
}

	/* Function to find the selected user or the logged in user. */
	public function loggedInUser() {
		$this->load->model('common_model');
		return $this->common_model->userSelection();
	}

	/*--- OUTSTANDING  Sum Sales Taerget-----*/

public function outStandOrders($G_level,$G_todaysdate,$repclause,$G_branchno){
	$str = array();
  if ( $G_level=="Company"){
    $this->db->select('identifier');
    $this->db->select_sum( 'actualvalue1');
    $this->db->from('kpidata');
    $this->db->where('period =', 1);
    $this->db->where('date =', date("Y-m-d", strtotime($G_todaysdate)));
    $this->db->like('identifier', 'MIDASOUTST');
    $this->db->group_by('identifier');
    $query = $this->db->get();
    $str = $query->result_array();
  }
  else if ( $G_level=="User") {
    $UserSes= $this->session->userdata('selectedUser');
    $userId = $UserSes["userid"];
    if ($userId!=0) {
      $sql=$this->db->query("SELECT * from users where userid=$userId");
      $str = $sql->row_array();
      $type_user=$str["usertype"];
    }
    if ($type_user=="A") {
      $this->db->select('identifier');
      $this->db->select_sum( 'actualvalue1');
      $this->db->from('kpidata');
      $this->db->where('period =', 1);
      $this->db->where('date =', date("Y-m-d", strtotime($G_todaysdate)));
      $this->db->like('identifier', 'MIDASOUTST');
      $this->db->group_by('identifier');
    } else if ($type_user=="R") {
      $repclause=$this->cleanRepClause($repclause);
      if ($repclause<10) {//	$repclause=sprintf("%02d", $repclause);
      }
      $this->db->select('identifier');
      $this->db->select_sum( 'actualvalue1');
      $this->db->from('kpidata');
      $this->db->where('period =', 1);
      $this->db->where('date =', date("Y-m-d", strtotime($G_todaysdate)));
      $this->db->like('identifier', 'MIDASOUTST');
      $this->db->where_in('analysis', explode(",", $repclause));
      $this->db->group_by('identifier');
    } else {
      $repclause=$this->cleanRepClause($repclause);
      if ($repclause<10) {//	$repclause=sprintf("%02d", $repclause);
      }
      $this->db->select('identifier');
      $this->db->select_sum( 'actualvalue1');
      $this->db->from('kpidata');
      $this->db->where('period =', 1);
      $this->db->where('date =', date("Y-m-d", strtotime($G_todaysdate)));
      $this->db->like('identifier', 'MIDASOUTST');
      $this->db->where_in('level', explode(",",$repclause));
      $this->db->group_by('identifier');
    }
    $query = $this->db->get();
    $str = $query->result_array();
  }
  else if ($G_level=="Branch") {
    $this->db->select('identifier');
    $this->db->select_sum( 'actualvalue1');
    $this->db->from('kpidata');
    $this->db->where('period =', 1);
    $this->db->where('date =', date("Y-m-d", strtotime($G_todaysdate)));
    if ($G_branchno != 0) {
      $this->db->where('level', $G_branchno);
    }
    $this->db->like('identifier', 'MIDASOUTST');
    $this->db->group_by('identifier');
    $query = $this->db->get();
    $str = $query->result_array();
  }
	/*	echo $sql = $this->db->last_query();
		print_r($str) */;
    return $str;
}
	/*-----OUTSTANDING Sales Target End---*/
	
	/** function that returns the comma seperated repsodes string */
	public function cleanRepClause($repclause) {
		$repclause=str_replace("IN ('", "", $repclause);
		$repclause=str_replace("')", "", $repclause);
		$repclause=str_replace("'", "", $repclause);
		$repclause=$repclause;
		return $repclause;
	}

	/*-----Daily Sales------------*/
public function dailySales($G_level,$repclause,$G_branchno){

		if ( $G_level=="Company")
		{//debug
			$this->db->select('SUM(day01sales), SUM(day02sales),SUM(day03sales),SUM(day04sales),SUM(day05sales),SUM(day06sales),SUM(day07sales),SUM(day08sales),SUM(day09sales),
							SUM(day10sales),SUM(day11sales),SUM(day12sales),SUM(day13sales),SUM(day14sales),SUM(day15sales),SUM(day16sales),SUM(day17sales),SUM(day18sales),
							SUM(day19sales),SUM(day20sales),SUM(day21sales),SUM(day22sales),SUM(day23sales),SUM(day24sales),SUM(day25sales),SUM(day26sales),SUM(day27sales),
							SUM(day28sales),SUM(day29sales),SUM(day30sales),SUM(day31sales)');


			$this->db->from('dailysales');
			$query = $this->db->get();
			 $str = $query->result_array();

		 }  //debug
		if ( $G_level=="User")
		{//d   //debug
			$this->db->select(' SUM(day01sales), SUM(day02sales),SUM(day03sales),SUM(day04sales),SUM(day05sales),SUM(day06sales),SUM(day07sales),SUM(day08sales),SUM(day09sales),
							SUM(day10sales),SUM(day11sales),SUM(day12sales),SUM(day13sales),SUM(day14sales),SUM(day15sales),SUM(day16sales),SUM(day17sales),SUM(day18sales),
							SUM(day19sales),SUM(day20sales),SUM(day21sales),SUM(day22sales),SUM(day23sales),SUM(day24sales),SUM(day25sales),SUM(day26sales),SUM(day27sales),
							SUM(day28sales),SUM(day29sales),SUM(day30sales),SUM(day31sales)');


			$this->db->from('dailysales');
			$this->db->where_in('repcode =', $repclause);
			$query = $this->db->get();
			 $str = $query->result_array();

		   // $query = "SELECT SUM(salestarget) FROM usersalestarget WHERE userid = $G_userid AND yearmonth = $curyearmonth";
		} //debug
		  if ( $G_level=="Branch")
		{//d
			$this->db->select(' SUM(day01sales), SUM(day02sales),SUM(day03sales),SUM(day04sales),SUM(day05sales),SUM(day06sales),SUM(day07sales),SUM(day08sales),SUM(day09sales),
							SUM(day10sales),SUM(day11sales),SUM(day12sales),SUM(day13sales),SUM(day14sales),SUM(day15sales),SUM(day16sales),SUM(day17sales),SUM(day18sales),
							SUM(day19sales),SUM(day20sales),SUM(day21sales),SUM(day22sales),SUM(day23sales),SUM(day24sales),SUM(day25sales),SUM(day26sales),SUM(day27sales),
							SUM(day28sales),SUM(day29sales),SUM(day30sales),SUM(day31sales)');
			 $this->db->select_sum( 'actualvalue1');

			$this->db->from('dailysales');

			$this->db->where('branch =',$G_branchno);

			 $query = $this->db->get();
				 $str = $query->result_array();

		}

  //print_r($str) ; die;
		return $str;
	}
	/*----End Daily Sales---------*/

	// If at user level, get the user kpi thresholds



// Today Order --Virtual-----------//


public function todayOrders($G_level,$G_todaysdate,$repclause,$G_branchno){



		if ( $G_level=="Company")
		{//debug

			$sql=$this->db->query("SELECT identifier, SUM(actualvalue1) as actualvalue1 FROM kpidata WHERE period = 1 AND date = '$G_todaysdate' AND identifier LIKE 'MIDASTODAY__' GROUP BY 1");
			 $str = $sql->result_array();

			/*$this->db->select('identifier');
			  $this->db->select_sum( 'actualvalue1');

			$this->db->from('kpidata');
			$this->db->where('period =', 1);
			$this->db->where('date =', $G_todaysdate);
			$this->db->like('identifier', 'MIDASTODAY__','none');
			$this->db->group_by('identifier');
			 $query = $this->db->get();
				$str = $query->result_array(); */

		 }  //debug
		if ( $G_level=="User")
		{//d   //debug

$UserSes= $this->session->userdata('selectedUser');
		 $userId = $UserSes["userid"];
		 if ($userId!=0)
		 {

	$sql=$this->db->query("SELECT * from users where userid=$userId");
			 $str = $sql->row_array();
			$type_user=$str["usertype"];

		 }

 if ($type_user=="A")
		 {
			$sql=$this->db->query("SELECT identifier, SUM(actualvalue1) as actualvalue1 FROM kpidata WHERE period = 1 AND date = '$G_todaysdate' AND identifier LIKE 'MIDASTODAY__'  GROUP BY 1");
			 $str = $sql->result_array();
			}
		 else if ($type_user=="R")
		 {
			$sql=$this->db->query("SELECT identifier, SUM(actualvalue1) as actualvalue1 FROM kpidata WHERE period = 1 AND date = '$G_todaysdate' AND identifier LIKE 'MIDASTODAY__' AND analysis $repclause GROUP BY 1");
			 $str = $sql->result_array();
			}
			else{

					$sql=$this->db->query("SELECT identifier, SUM(actualvalue1) as actualvalue1 FROM kpidata WHERE period = 1 AND date = '$G_todaysdate' AND identifier LIKE 'MIDASTODAY__' AND level $repclause GROUP BY 1");
			 $str = $sql->result_array();
			}

		} //debug
		  if ( $G_level=="Branch")
		{//d
				$sql=$this->db->query("SELECT identifier, SUM(actualvalue1) as actualvalue1 FROM kpidata WHERE period = 1 AND date = '$G_todaysdate' AND identifier LIKE 'MIDASTODAY__' AND level = $G_branchno GROUP BY 1");
			 $str = $sql->result_array();

		}

//echo $this->db->last_query();

		return $str;
	}



// End Today Order --Virtual -------//
//By Status//


public function todayOrdersStatus($G_level,$G_todaysdate,$repclause,$G_branchno){
  $str = array();
  if ( $G_level=="Company") {//debug
    $sql=$this->db->query("SELECT identifier, SUM(actualvalue1) as actualvalue1 FROM kpidata WHERE period = 1 AND date = '$G_todaysdate' AND identifier LIKE 'MIDASTODAY___' GROUP BY 1");
    $str = $sql->result_array();
			/*$this->db->select('identifier');
			  $this->db->select_sum( 'actualvalue1');

			$this->db->from('kpidata');
			$this->db->where('period =', 1);
			$this->db->where('date =', $G_todaysdate);
			$this->db->like('identifier', 'MIDASTODAY__','none');
			$this->db->group_by('identifier');
			 $query = $this->db->get();
				$str = $query->result_array(); */
	  }  //debug
  else if ( $G_level=="User") {//d   //debug
    $UserSes= $this->session->userdata('selectedUser');
    $userId = $UserSes["userid"];
    if ($userId!=0) {
      $sql=$this->db->query("SELECT * from users where userid=$userId");
      $str = $sql->row_array();
      $type_user=$str["usertype"];
    }
    if ($type_user=="A") {
      $sql=$this->db->query("SELECT identifier, SUM(actualvalue1) as actualvalue1 FROM kpidata WHERE period = 1 AND date = '$G_todaysdate' AND identifier LIKE 'MIDASTODAY___'  GROUP BY 1");
      $str = $sql->result_array();
    }
    else if ($type_user=="R") {
      $sql=$this->db->query("SELECT identifier, SUM(actualvalue1) as actualvalue1 FROM kpidata WHERE period = 1 AND date = '$G_todaysdate' AND identifier LIKE 'MIDASTODAY___' AND analysis $repclause GROUP BY 1");
      $str = $sql->result_array();
    } else {
      $sql=$this->db->query("SELECT identifier, SUM(actualvalue1) as actualvalue1 FROM kpidata WHERE period = 1 AND date = '$G_todaysdate' AND identifier LIKE 'MIDASTODAY___' AND level $repclause GROUP BY 1");
      $str = $sql->result_array();
    }
  }
  else if ( $G_level=="Branch") {//d
    $sql=$this->db->query("SELECT identifier, SUM(actualvalue1) as actualvalue1 FROM kpidata WHERE period = 1 AND date = '$G_todaysdate' AND identifier LIKE 'MIDASTODAY___' AND level = $G_branchno GROUP BY 1");
    $str = $sql->result_array();
  }

/*echo $this->db->last_query(); die;*/
		return $str;
}


	// end By status//


public function heldInomr($G_level,$G_todaysdate,$repclause,$G_branchno){
		$G_todaysdate=date('Y-m-d',strtotime($G_todaysdate));
		$repclause=str_replace("IN ('", "", $repclause);
		$repclause=str_replace("')", "", $repclause);
		$repclause=str_replace("','", "|", $repclause);
		$repclause=explode('|',$repclause);

		if ( $G_level=="Company"){

			$this->db->select('identifier');
			$this->db->select_sum( 'actualvalue1');
			$this->db->from('kpidata');
			$this->db->where('date', $G_todaysdate);
			$this->db->where_in('identifier',array('MIDASHELDOMRSL','MIDASHELDOMRCR'));
			$this->db->group_by('identifier');
			$query = $this->db->get();
			$str = $query->result_array();
		}
		if ( $G_level=="User"){

			$this->db->select('identifier');
			$this->db->select_sum( 'actualvalue1');
			$this->db->from('kpidata');
			$this->db->where('date', $G_todaysdate);
			$this->db->where_in('identifier',array('MIDASHELDOMRSL','MIDASHELDOMRCR'));
			$this->db->where_in('analysis', $repclause);
			$this->db->group_by('identifier');
			$query = $this->db->get();
			$str = $query->result_array();
		}
		  if ($G_level=="Branch"){

			$this->db->select('identifier');
			$this->db->select_sum( 'actualvalue1');
			$this->db->from('kpidata');
			$this->db->where('date', $G_todaysdate);
			$this->db->where('level', $G_branchno);
			$this->db->where_in('identifier',array('MIDASHELDOMRSL','MIDASHELDOMRCR'));
			$this->db->group_by('identifier');
			$query = $this->db->get();
			$str = $query->result_array();
		}
	//echo $this->db->last_query();
		return $str;
	}



public function postedOmr($G_level,$G_todaysdate,$repclause,$G_branchno){
  $str = array();
  $G_todaysdate=date('Y-m-d',strtotime($G_todaysdate));
  $repclause=str_replace("IN ('", "", $repclause);
  $repclause=str_replace("')", "", $repclause);
  $repclause=str_replace("','", "|", $repclause);
  $repclause=explode('|',$repclause);

  if ( $G_level=="Company"){
    $this->db->select('identifier');
    $this->db->select_sum( 'actualvalue1');
    $this->db->from('kpidata');
    $this->db->where('date', $G_todaysdate);
    $this->db->where_in('identifier',array('MIDASPOSTEDSL','MIDASPOSTEDCR'));
    $this->db->group_by('identifier');
    $query = $this->db->get();
    $str = $query->result_array();
  }
  if ( $G_level=="User"){
    $this->db->select('identifier');
    $this->db->select_sum( 'actualvalue1');
    $this->db->from('kpidata');
    $this->db->where('date =', $G_todaysdate);
    $this->db->where_in('identifier',array('MIDASPOSTEDSL','MIDASPOSTEDCR'));
    $this->db->where_in('analysis', $repclause);
    $this->db->group_by('identifier');
    $query = $this->db->get();
    $str = $query->result_array();
  }
  if ( $G_level=="Branch"){
    $this->db->select('identifier');
    $this->db->select_sum( 'actualvalue1');
    $this->db->from('kpidata');
    $this->db->where('date', $G_todaysdate);
    $this->db->where('level', $G_branchno);
    $this->db->where_in('identifier',array('MIDASPOSTEDSL','MIDASPOSTEDCR'));
    $this->db->group_by('identifier');
    $query = $this->db->get();
    $str = $query->result_array();
  }
		//echo $this->db->last_query();
		//print_r($str);
  return $str;
}
	// Sales month to date //
public function cumday($G_level,$G_todaysdate,$repclause,$G_branchno) {
   // echo $repclause;exit;
	$yearmonth=date('Ym',strtotime($G_todaysdate));
	if ( $G_level=="Company") {
		$this->db->select('SUM(day01sales), SUM(day02sales),SUM(day03sales),SUM(day04sales),SUM(day05sales),SUM(day06sales),SUM(day07sales),SUM(day08sales),SUM(day09sales),
							SUM(day10sales),SUM(day11sales),SUM(day12sales),SUM(day13sales),SUM(day14sales),SUM(day15sales),SUM(day16sales),SUM(day17sales),SUM(day18sales),
							SUM(day19sales),SUM(day20sales),SUM(day21sales),SUM(day22sales),SUM(day23sales),SUM(day24sales),SUM(day25sales),SUM(day26sales),SUM(day27sales),
							SUM(day28sales),SUM(day29sales),SUM(day30sales),SUM(day31sales)');
		$this->db->from('dailysales');
		$this->db->where('yearmonth', $yearmonth);
		$query = $this->db->get();
	   // echo $this->db->last_query();
		$str = $query->result_array();
	}
	if ( $G_level=="User") {
		$str1=str_replace("IN", "", $repclause);
		$str1=str_replace("'", "", $str1);
		$str1=str_replace("(", "", $str1);
		$str1=str_replace(")", "", $str1);
		$str1=explode(",",trim($str1));

//	print_r($str1);	exit;
		$this->db->select('SUM(day01sales), SUM(day02sales),SUM(day03sales),SUM(day04sales),SUM(day05sales),SUM(day06sales),SUM(day07sales),SUM(day08sales),SUM(day09sales),
							SUM(day10sales),SUM(day11sales),SUM(day12sales),SUM(day13sales),SUM(day14sales),SUM(day15sales),SUM(day16sales),SUM(day17sales),SUM(day18sales),
							SUM(day19sales),SUM(day20sales),SUM(day21sales),SUM(day22sales),SUM(day23sales),SUM(day24sales),SUM(day25sales),SUM(day26sales),SUM(day27sales),
							SUM(day28sales),SUM(day29sales),SUM(day30sales),SUM(day31sales)');
		$this->db->from('dailysales');
		$this->db->where('yearmonth', $yearmonth);
		$this->db->where_in('repcode', $str1);
		$query = $this->db->get();
	   // echo $this->db->last_query();
		$str = $query->result_array();
	}
	if ( $G_level=="Branch") {
		$this->db->select('SUM(day01sales), SUM(day02sales),SUM(day03sales),SUM(day04sales),SUM(day05sales),SUM(day06sales),SUM(day07sales),SUM(day08sales),SUM(day09sales),
							SUM(day10sales),SUM(day11sales),SUM(day12sales),SUM(day13sales),SUM(day14sales),SUM(day15sales),SUM(day16sales),SUM(day17sales),SUM(day18sales),
							SUM(day19sales),SUM(day20sales),SUM(day21sales),SUM(day22sales),SUM(day23sales),SUM(day24sales),SUM(day25sales),SUM(day26sales),SUM(day27sales),
							SUM(day28sales),SUM(day29sales),SUM(day30sales),SUM(day31sales)');

		$this->db->from('dailysales');
		$this->db->where('yearmonth', $yearmonth);
		$this->db->where('branch =', $G_branchno);
		$query = $this->db->get();
		//  echo $this->db->last_query();
		$str = $query->result_array();
	}
	return $str;
}

	// End  Sales month to date//

		// Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set
	  public function userKpi($G_level,$G_branchno,$G_userid){




		 if ( $G_level=="Company")
		{//debug
			$this->db->select('kpithreshold1, kpithreshold2, marginok, margingood');
			$this->db->from('company');
			 $query = $this->db->get();
				$str = $query->result_array();

		 }  //debug
		if ( $G_level=="User")
		{//d   //debug
				$this->db->select('kpithreshold1, kpithreshold2, marginok, margingood');
			$this->db->from('company');
			   $this->db->where('userid =',$G_userid);
				$query = $this->db->get();
				$str = $query->result_array();
		} //debug
		  if ( $G_level=="Branch")
		{//d
			$this->db->select('kpithreshold1, kpithreshold2, marginok, margingood');
			$this->db->from('branch');
			  $this->db->where('branch =',$G_branchno);

			 $query = $this->db->get();
				$str = $query->result_array();


		}
	return $str;
	}

	// End Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set


	// I want to get the last 30 rows, but want them sorted in ascending date order

  public function lastthirty($G_level,$repclause,$G_branchno){


		 if ( $G_level=="Company")
		{//debug
			$subquery="SELECT date, SUM(actualvalue1) as sum1, SUM(actualvalue2) as sum2 FROM kpidata WHERE identifier = 'MIDASFULFILLED' GROUP BY date ORDER BY date DESC LIMIT 30";
			$this->db->select('*');
			$this->db->from('('.$subquery.') temp');
			$this->db->order_by('date');
			$query = $this->db->get();
			$str = $query->result_array();

		 }  //debug
		if ( $G_level=="User")
		{//d   //debug
			$sql=$this->db->query("SELECT * FROM ((SELECT date, SUM(actualvalue1) as sum1, SUM(actualvalue2) as sum2 FROM kpidata WHERE identifier = 'MIDASFULFILLED' AND analysis $repclause GROUP BY date ORDER BY date DESC LIMIT 30) temp) ORDER BY date");
		   // $query = $this->db->get();
			$str = $sql->result_array();
		} //debug
		  if ( $G_level=="Branch")
		{//d
			$subquery="SELECT * FROM (SELECT date, SUM(actualvalue1) as sum1, SUM(actualvalue2) as sum2  FROM kpidata WHERE identifier = 'MIDASFULFILLED' AND level = $G_branchno GROUP BY date ORDER BY date DESC LIMIT 30) temp";
			$this->db->select('*');
			$this->db->from('('.$subquery.') temp');
			$this->db->order_by('date');
			$query = $this->db->get();
			$str = $query->result_array();
		}
		// echo $this->db->last_query(); die;


	return $str;
	}
// 3 YEAR SALES CHART

  public function waitingposting($G_level,$G_todaysdate,$repclause,$G_branchno){
		$G_todaysdate=date('Y-m-d',strtotime($G_todaysdate));
		$repclause=str_replace("IN ('", "", $repclause);
		$repclause=str_replace("')", "", $repclause);
		$repclause=str_replace("','", "|", $repclause);
		$repclause=explode('|',$repclause);
    $str = array();
		if ( $G_level=="Company"){

			$this->db->select('identifier, SUM(actualvalue1) as sum1');
			$this->db->from('kpidata');
			$this->db->where('date =', $G_todaysdate);
			$this->db->where_in('identifier',array('MIDASWAITSL','MIDASWAITCR'));
			$this->db->group_by('identifier');
			$query = $this->db->get();
			$str = $query->result_array();
		}
		else if ( $G_level=="User"){
			$this->db->select('identifier,SUM(actualvalue1) as sum1');
			$this->db->from('kpidata');
			$this->db->where('date =', $G_todaysdate);
			$this->db->where_in('identifier',array('MIDASWAITSL','MIDASWAITCR'));
			$this->db->where_in('analysis', $repclause);
			$this->db->group_by('identifier');
			$query = $this->db->get();
			$str = $query->result_array();

		}
		else if ( $G_level=="Branch"){
			$this->db->select('identifier,SUM(actualvalue1) as sum1');
			$this->db->from('kpidata');
			$this->db->where('date =', $G_todaysdate);
			$this->db->where('level =', $G_branchno);
			$this->db->where_in('identifier',array('MIDASWAITSL','MIDASWAITCR'));
			$this->db->group_by('identifier');
			$query = $this->db->get();
			$str = $query->result_array();
		}
	//echo $this->db->last_query();
	return $str;
	}
	// END I want to get the last 30 rows, but want them sorted in ascending date order

	public function dayDrillData($lastsalesdate = 0, $specific_search = array(), $specific_order = array(), $search_key = "", $offset = 0, $limit = 10, $count = 2, $recodeArray = array(), $branchNo = 0)
	{
		if ($count == '1')
		{
			$this->db->select('COUNT(salesanalysis.account) as totalrows');
		}
		else
		{
			$this->db->select('salesanalysis.repcode, salesanalysis.ordtype, salesanalysis.account, salesanalysis.orderno, salesanalysis.date, salesanalysis.prodcode, salesanalysis.quantity, salesanalysis.sales, customer.name, product.description');
		}

		$this->db->from('customer');
		$this->db->join('salesanalysis', "salesanalysis.account = customer.account", "left");
		$this->db->join('product', "product.code = salesanalysis.prodcode", "left");

		$previousDate = date('Y-m-d', $lastsalesdate);
		$search_key = trim($search_key);
		$whereData = 'salesanalysis.date = "'.$previousDate.'"';

		if (!empty($search_key))
		{
			$likeData = array();

			foreach ($specific_search as $key => $val)
			{
				if (!empty($key))
				{
					$likeData[] = ' '.$key.' LIKE "%'.$search_key.'%" ';
				}
			}

			if (!empty($likeData))
			{
				$likeData = implode('OR', $likeData);
				$whereData.= ' AND ('.$likeData.')';
			}
		}

		$this->db->where($whereData);

		if (!empty($branchNo))
		{
			$this->db->where('salesanalysis.branch =', $branchNo);
		}

		if (!empty($recodeArray))
		{
			$this->db->where_in('salesanalysis.currepcode', $recodeArray);
		}

		if (!empty($specific_order['by']) && !empty($specific_order['dir']))
		{
			$this->db->order_by($specific_order['by'], $specific_order['dir']);
		}
		else
		{
			$this->db->order_by('salesanalysis.orderno', "DESC");
		}

		if ($count != '1')
		{
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();
		$result = $query->result_array();

		if ($count != '1')
		{
			$dataArray = array();

			foreach ($result as $rdata)
			{
				$class="nofill";

				if ("CR" == strtoupper($rdata['ordtype']))
				{
					$class="redrow";
				}

				$custLink = '<a class="customerlink" data-class="'.$class.'" href="'.base_url().'customer/customerDetails/'.base64_encode($rdata["account"]).'">'.$rdata['name'].'</a>';
				$productLink = '<a href="'.base_url().'products/details/'.base64_encode($rdata["prodcode"]).'">'.$rdata['description'].'</a>';
				$repDate = date('d/m/Y', strtotime($rdata['date']));

				$dataArray[] = array($rdata['account'], $custLink, $rdata['orderno'], $rdata['ordtype'], $rdata['prodcode'], $productLink, $rdata['repcode'], $rdata['quantity'], $rdata['sales'], $repDate);
			}

			return $dataArray;
		}
		else
		{
			return $result[0]['totalrows'];
		}
	}

	public function salesmtdData($specific_search = array(), $specific_order = array(), $search_key = "", $offset = 0, $limit = 10, $count = 2, $recodeArray = array(), $branchNo = 0)
	{
		if ($count == '1')
		{
			$this->db->select('COUNT(salesanalysis.account) as totalrows');
		}
		else
		{
			$this->db->select('salesanalysis.repcode, salesanalysis.ordtype, salesanalysis.account, salesanalysis.orderno, salesanalysis.date, salesanalysis.prodcode, salesanalysis.quantity, salesanalysis.sales, customer.name, product.description');
		}

		$this->db->from('customer');
		$this->db->join('salesanalysis', "salesanalysis.account = customer.account", "left");
		$this->db->join('product', "product.code = salesanalysis.prodcode", "left");

		$currntDay = date('Y-m', time());
		$previousDate = date('Y-m-01', time());
		$search_key = trim($search_key);
		$whereData = 'salesanalysis.date >= "'.$previousDate.'"';

		if (!empty($search_key))
		{
			$likeData = array();

			foreach ($specific_search as $key => $val)
			{
				if (!empty($key))
				{
					$likeData[] = ' '.$key.' LIKE "%'.$search_key.'%" ';
				}
			}

			if (!empty($likeData))
			{
				$likeData = implode('OR', $likeData);
				$whereData.= ' AND ('.$likeData.')';
			}
		}

		$this->db->where($whereData);

		if (!empty($branchNo))
		{
			$this->db->where('salesanalysis.branch = ', $branchNo);
		}

		if (!empty($recodeArray))
		{
			$this->db->where_in('salesanalysis.currepcode', $recodeArray);
		}

		if (!empty($specific_order['by']) && !empty($specific_order['dir']))
		{
			$this->db->order_by($specific_order['by'], $specific_order['dir']);
		}
		else
		{
			$this->db->order_by('salesanalysis.date', "ASC");
		}

		if ($count != '1')
		{
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();
		$result = $query->result_array();

		if ($count != '1')
		{
			$dataArray = array();

			foreach ($result as $rdata)
			{
				$class = "nofill";

				if ("CR" == strtoupper($rdata['ordtype']))
				{
					$class = "redrow";
				}

				$custLink = '<a class="customerlink" data-class="'.$class.'" href="'.base_url().'customer/customerDetails/'.base64_encode($rdata["account"]).'">'.$rdata['name'].'</a>';
				$productLink = '<a href="'.base_url().'products/details/'.base64_encode($rdata["prodcode"]).'">'.$rdata['description'].'</a>';
				$repDate = date('d/m/Y', strtotime($rdata['date']));
				$dataArray[] = array($rdata['account'], $custLink, $rdata['orderno'], $rdata['ordtype'], $rdata['prodcode'], $productLink, $rdata['repcode'], $rdata['quantity'], $rdata['sales'], $repDate);
			}

			return $dataArray;
		}
		else
		{
			return $result[0]['totalrows'];
		}
	}

	public function csv_daydrill_export($lastsalesdate=0,$specific_search=array(),$search_key='',$recodeArray=array(),$branchNo=0) {
		$this->db->select('salesanalysis.account,salesanalysis.orderno,salesanalysis.ordtype, salesanalysis.date,salesanalysis.prodcode,salesanalysis.repcode,salesanalysis.quantity,salesanalysis.sales,customer.name,product.description');
		$this->db->from('customer');
		$this->db->join('salesanalysis', "salesanalysis.account = customer.account", "left");
		$this->db->join('product', "product.code = salesanalysis.prodcode", "left");
		//$currntDay=date('w',time());
		$previousDate=date('Y-m-d',$lastsalesdate);
		$search_key=trim($search_key);
		$whereData='salesanalysis.date = "'. $previousDate.'"';
		if (!empty($search_key)) {
			$likeData=array();
			foreach ($specific_search as $key=>$val) {
				if (!empty($key)) {
					$likeData[]=' '.$key.' LIKE "%'.$search_key.'%" ';
				}
			}
			if (!empty($likeData)) {
				$likeData=implode('OR', $likeData);
				$whereData.=' AND ('.$likeData.')';
			}

		}
		$this->db->where($whereData);
		if (!empty($branchNo)) {
			$this->db->where('salesanalysis.branch =', $branchNo);
		}
		if (!empty($recodeArray)) {
			$this->db->where_in('salesanalysis.currepcode', $recodeArray);
		}
		$this->db->order_by('salesanalysis.prodcode', "DESC");
		$this->db->order_by('salesanalysis.orderno', "DESC");
		$query = $this->db->get();

		$this->load->dbutil();
		$opt=$this->dbutil->csv_from_result($query);
		$head_value = array("account","name", "orderno", "ordtype", "prodcode","repcode","description","quantity", "sales", "date");
		$new_head  =array("Account", "Cust. Name", "Order No.","Order Type", "Product Code","Rep","Product", "Quantity", "Sales","Date");
		$head_value1 = array('""');
		$new_head1  =array('"0.00"');
		$opt = str_replace($head_value, $new_head, $opt);
		echo $opt = str_replace($head_value1, $new_head1, $opt);
	}

	public function csv_mtd_export($specific_search=array(),$search_key='',$recodeArray=array(),$branchNo=0) {
		$this->db->select('salesanalysis.account,salesanalysis.orderno,salesanalysis.ordtype, salesanalysis.date,salesanalysis.prodcode,salesanalysis.repcode,salesanalysis.quantity,salesanalysis.sales,customer.name,product.description');
		$this->db->from('customer');
		$this->db->join('salesanalysis', "salesanalysis.account = customer.account", "left");
		$this->db->join('product', "product.code = salesanalysis.prodcode", "left");
		$currntDay=date('Y-m',time());
		$previousDate=date('Y-m-01',time());
		$search_key=trim($search_key);
		$whereData='salesanalysis.date >= "'. $previousDate.'"';
		if (!empty($search_key)) {
			$likeData=array();
			foreach ($specific_search as $key=>$val) {
				if (!empty($key)) {
					$likeData[]=' '.$key.' LIKE "%'.$search_key.'%" ';
				}
			}
			if (!empty($likeData)) {
				$likeData=implode('OR', $likeData);
				$whereData.=' AND ('.$likeData.')';
			}

		}
		$this->db->where($whereData);
		if (!empty($branchNo)) {
			$this->db->where('salesanalysis.branch =', $branchNo);
		}
		if (!empty($recodeArray)) {
			$this->db->where_in('salesanalysis.currepcode', $recodeArray);
		}
		$this->db->order_by('salesanalysis.prodcode', "DESC");
		$query = $this->db->get();

		$this->load->dbutil();
		$opt=$this->dbutil->csv_from_result($query);
		$head_value = array("account","name", "orderno","ordtype", "prodcode","repcode","description","quantity", "sales", "date");
		$new_head  =array("Account", "Cust. Name", "Order No.","Order Type","Product Code","Rep","Product", "Quantity", "Sales","Date");
		$head_value1 = array('""');
		$new_head1  =array('"0.00"');
		$opt = str_replace($head_value, $new_head, $opt);
		echo $opt = str_replace($head_value1, $new_head1, $opt);
	}

	public function getUsersRepcodeCustom($userId = 0)
	{
		$returnArray = array();

		if (!empty($userId))
		{
			$this->db->select("repcode");
			$this->db->from("userreps");
			$this->db->where("userid", $userId);

			$query = $this->db->get();
			$rows = $query->result_array();

			foreach ($rows as $row)
			{
				$returnArray[] = $row['repcode'];
			}
		}

		return $returnArray;
	}

	public function todayOrdersBySegment($recodeArray, $branchNo, $segment, $by)
	{
		$this->db->select('salesorders.account, salesorders.orderno, salesorders.ordtype, salesorders.datein, salesorders.prodcode, salesorders.quantity, salesorders.sales, salesorders.repcode, salesorders.status, customer.name, product.description');
		$this->db->from('salesorders');
		$this->db->join('customer', "salesorders.account = customer.account", "left");
		$this->db->join('product', "product.code = salesorders.prodcode", "left");

		$previousDate = date('Y-m-d', time());
		$whereData = 'salesorders.datein = "'.$previousDate.'"';
		$this->db->where($whereData);

		if ($by == "type")
		{
			$this->db->where('salesorders.ordtype =', $segment);
		}
		else
		{
			$this->db->where('salesorders.status =', $segment);
		}

		if (!empty($branchNo))
		{
			$this->db->where('salesorders.branch =', $branchNo);
		}

		if (!empty($recodeArray))
		{
			$this->db->where_in('salesorders.repcode', $recodeArray);
		}

		$this->db->order_by('salesorders.orderno', "DESC");

		$query = $this->db->get();
		$result = $query->result_array();

		$dataArray = array();

		foreach ($result as $rdata)
		{
			$class="nofill";

			if ("CR" == strtoupper($rdata['ordtype']))
			{
				$class="redrow";
			}

			$custLink = '<a class="customerlink" data-class="'.$class.'" href="'.base_url().'customer/customerDetails/'.base64_encode($rdata["account"]).'">'.$rdata['name'].'</a>';
			$productLink = '<a href="'.base_url().'products/details/'.base64_encode($rdata["prodcode"]).'">'.$rdata['description'].'</a>';
			$repDate = date('d/m/Y', strtotime($rdata['datein']));
			$dataArray[] = array($rdata['account'], $custLink, $rdata['orderno'], $rdata['ordtype'], $rdata['prodcode'], $productLink, $rdata['quantity'], $rdata['sales'], $rdata['repcode'], $rdata['status'], $repDate);
		}

		return $dataArray;
	}

	public function OutstandingOrdersBySegment($recodeArray, $branchNo, $segment, $by)
	{
		$this->db->select('salesorders.account, salesorders.orderno, salesorders.ordtype, salesorders.datein, salesorders.prodcode, salesorders.quantity, salesorders.sales, salesorders.repcode, salesorders.status, customer.name, product.description');
		$this->db->from('salesorders');
		$this->db->join('customer', "salesorders.account = customer.account", "left");
		$this->db->join('product', "product.code = salesorders.prodcode", "left");

		$ordTypeArray = array("SL","CR");

		$this->db->where_in('salesorders.ordtype', $ordTypeArray);
		$this->db->where('salesorders.status =', $segment);

		if (!empty($branchNo))
		{
			$this->db->where('salesorders.branch =', $branchNo);
		}

		if (!empty($recodeArray))
		{
			$this->db->where_in('salesorders.repcode', $recodeArray);
		}

		$this->db->order_by('salesorders.orderno', "DESC");

		$query = $this->db->get();
		$result = $query->result_array();

		$dataArray = array();

		foreach ($result as $rdata)
		{
			$class = "nofill";

			if ("CR" == strtoupper($rdata['ordtype']))
			{
				$class="redrow";
			}

			$custLink = '<a class="customerlink" data-class="'.$class.'" href="'.base_url().'customer/customerDetails/'.base64_encode($rdata["account"]).'">'.$rdata['name'].'</a>';
			$productLink = '<a href="'.base_url().'products/details/'.base64_encode($rdata["prodcode"]).'">'.$rdata['description'].'</a>';
			$repDate = date('d/m/Y', strtotime($rdata['datein']));
			$dataArray[] = array($rdata['account'], $custLink, $rdata['orderno'], $rdata['ordtype'], $rdata['prodcode'], $productLink, $rdata['quantity'], $rdata['sales'], $rdata['repcode'], $rdata['status'], $repDate);
		}

		return $dataArray;
	}

	public function getCustomersAccount($recodeArray) {
		$this->db->select('customer.account');
	$this->db->from('customer');
	if (!empty($recodeArray)) {
			$this->db->where_in('customer.repcode', $recodeArray);
		}
		$query = $this->db->get();
		$result = $query->result_array();
		$returnArray=array();
		foreach ($result as $cusData) {
			$returnArray[]=$cusData['account'];
		}
		return $returnArray;
	}

	public function getMonthTargetData($curyearmonth,$G_userid,$G_branchno,$G_level) {

		switch($G_level){
			case 'Company':
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
		$this->db->from('usersalestarget');
		$this->db->where('yearmonth =', $curyearmonth);
		//$this->db->where('userid =', $G_userid);
		$query = $this->db->get();
		$str = $query->result_array();
		if (empty($str[0])) {
			$this->db->select('yearmonth,SUM(salestarget) as saletarget');
			$this->db->from('branchsalestarget');
			$this->db->where('yearmonth =', $curyearmonth);
			$query = $this->db->get();
			//$this->db->where('branch =', $G_branchno);
			$str = $query->result_array();
		}
				break;
			case 'User':
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
		$this->db->from('usersalestarget');
		$this->db->where('yearmonth =', $curyearmonth);
		$this->db->where('userid =', $G_userid);
		$query = $this->db->get();
		$str = $query->result_array();
				break;
			case 'Branch':
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
		$this->db->from('branchsalestarget');
		$this->db->where('yearmonth =', $curyearmonth);
		$query = $this->db->get();
		$this->db->where('branch =', $G_branchno);
		$str = $query->result_array();
				break;
			default:
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
		$this->db->from('usersalestarget');
		$this->db->where('yearmonth =', $curyearmonth);
		//$this->db->where('userid =', $G_userid);
		$query = $this->db->get();
		$str = $query->result_array();



		if (empty($str[0])) {
			$this->db->select('yearmonth,SUM(salestarget) as saletarget');
			$this->db->from('branchsalestarget');
			$this->db->where('yearmonth =', $curyearmonth);
			$query = $this->db->get();
			//$this->db->where('branch =', $G_branchno);
			$str = $query->result_array();
		}
				break;
		}
	//	echo $G_userid;
		//echo $this->db->last_query();
		return $str[0];
	}

	public function getYearTargetData($G_userid,$G_branchno,$G_level) {
		$year=date('Y',time());
		$whereData='yearmonth LIKE "'.$year.'%" ';
		switch($G_level){
			case 'Company':
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
		$this->db->from('usersalestarget');
		$this->db->where($whereData);
		//$this->db->where('userid =', $G_userid);
		$query = $this->db->get();
		$str = $query->result_array();
		if (empty($str[0])) {
			$this->db->select('yearmonth,SUM(salestarget) as saletarget');
			$this->db->from('branchsalestarget');
			$this->db->where($whereData);
			$query = $this->db->get();
			//$this->db->where('branch =', $G_branchno);
			$str = $query->result_array();
		}
				break;
			case 'User':
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
		$this->db->from('usersalestarget');
		$this->db->where($whereData);
		$this->db->where('userid =', $G_userid);
		$query = $this->db->get();
		$str = $query->result_array();
				break;
			case 'Branch':
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
		$this->db->from('branchsalestarget');
		$this->db->where($whereData);
		$query = $this->db->get();
		$this->db->where('branch =', $G_branchno);
		$str = $query->result_array();
				break;
			default:
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
		$this->db->from('usersalestarget');
		$this->db->where($whereData);
		//$this->db->where('userid =', $G_userid);
		$query = $this->db->get();
		$str = $query->result_array();
		if (empty($str[0])) {
			$this->db->select('yearmonth,SUM(salestarget) as saletarget');
			$this->db->from('branchsalestarget');
			$this->db->where($whereData);
			$query = $this->db->get();
			//$this->db->where('branch =', $G_branchno);
			$str = $query->result_array();
		}
				break;
		}
		return $str[0];
	}
//14-3-2018
	public function getThreeYearTargetArray($G_userid,$G_branchno,$G_level) {
		$curyearmonth='201701';
		switch($G_level) {
			case 'Company':
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
				$this->db->from('usersalestarget');
				$this->db->where('yearmonth =', $curyearmonth);
				//$this->db->where('userid =', $G_userid);
				$query = $this->db->get();
				$str = $query->result_array();
				if (empty($str[0])) {
					$this->db->select('yearmonth,SUM(salestarget) as saletarget');
					$this->db->from('branchsalestarget');
					$this->db->where('yearmonth =', $curyearmonth);
					$query = $this->db->get();
					//$this->db->where('branch =', $G_branchno);
					$str = $query->result_array();
				}
				break;
			case 'User':
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
				$this->db->from('usersalestarget');
				$this->db->where('yearmonth =', $curyearmonth);
				$this->db->where('userid =', $G_userid);
				$query = $this->db->get();
				$str = $query->result_array();
				break;
			case 'Branch':
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
				$this->db->from('branchsalestarget');
				$this->db->where('yearmonth =', $curyearmonth);
				$query = $this->db->get();
				$this->db->where('branch =', $G_branchno);
				$str = $query->result_array();
				break;
			default:
				$this->db->select('yearmonth,SUM(salestarget) as saletarget');
				$this->db->from('usersalestarget');
				$this->db->where('yearmonth =', $curyearmonth);
				//$this->db->where('userid =', $G_userid);
				$query = $this->db->get();
				$str = $query->result_array();
				if (empty($str[0])) {
					$this->db->select('yearmonth,SUM(salestarget) as saletarget');
					$this->db->from('branchsalestarget');
					$this->db->where('yearmonth =', $curyearmonth);
					$query = $this->db->get();
					//$this->db->where('branch =', $G_branchno);
					$str = $query->result_array();
				}
				break;
		}
	}


public	function newMerge(array $arr1,array $arr2)
	{
		for($i=0;$i<count($arr1); $i++)
		{
			$arr1[$i]['salesmtd']=$arr2[$i]['salesmtd'];
		}
		return $arr1;
	}


	// pac sales target dashboard

	public function getPac1SalesTargetDashboard($G_level,$userid=0,$branchNo=0,$repclause)
	{
		$yearmonth=date("Y").date("m");
		switch($G_level){
			case 'Company':
							$query = "select '1' as 'tabl', pac1.description as description, pac1salestarget.pac1code as paccode,sum(pac1salestarget.salestarget) as salestarget from pac1salestarget LEFT JOIN pac1 ON pac1.code=pac1salestarget.pac1code where pac1salestarget.yearmonth='$yearmonth' GROUP BY pac1.code UNION select '2' as 'tabl',pac2.description as description, pac2salestarget.pac2code as paccode,sum(pac2salestarget.salestarget) as salestarget from pac2salestarget LEFT JOIN pac2 ON pac2.code=pac2salestarget.pac2code where pac2salestarget.yearmonth='$yearmonth' GROUP BY pac2.code UNION select '3' as 'tabl',pac3.description as description,pac3salestarget.pac3code as paccode,sum(pac3salestarget.salestarget) as salestarget from pac3salestarget LEFT JOIN pac3 ON pac3.code=pac3salestarget.pac3code where pac3salestarget.yearmonth='$yearmonth' GROUP BY pac3.code UNION select '4' as 'tabl', pac4.description as description,pac4salestarget.pac4code as paccode,sum(pac4salestarget.salestarget) as salestarget from pac4salestarget LEFT JOIN pac4 ON pac4.code=pac4salestarget.pac4code where pac4salestarget.yearmonth='$yearmonth' GROUP BY pac4.code UNION select '5' as 'tabl', product.description as description,productsalestarget.productcode as paccode,sum(productsalestarget.salestarget) as salestarget from productsalestarget LEFT JOIN product ON product.code=productsalestarget.productcode where productsalestarget.yearmonth='$yearmonth' GROUP BY product.code LIMIT 5";

				break;
			case 'User':
								$query = "select '1' as 'tabl',pac1.description as description, pac1salestarget.pac1code as paccode,sum(pac1salestarget.salestarget) as salestarget from pac1 left join pac1salestarget on pac1.code=pac1salestarget.pac1code  where pac1salestarget.userid='$userid' and pac1salestarget.yearmonth='$yearmonth' GROUP BY pac1.code UNION select '2' as 'tabl',pac2.description as description, pac2salestarget.pac2code as paccode,sum(pac2salestarget.salestarget) as salestarget from pac2 left join pac2salestarget on pac2.code=pac2salestarget.pac2code  where pac2salestarget.userid='$userid' and pac2salestarget.yearmonth='$yearmonth' GROUP BY pac2.code  UNION  select '3' as 'tabl',pac3.description as description,pac3salestarget.pac3code as paccode,sum(pac3salestarget.salestarget) as salestarget from pac3 left join pac3salestarget on pac3.code=pac3salestarget.pac3code  where pac3salestarget.userid='$userid' and pac3salestarget.yearmonth='$yearmonth' GROUP BY pac3.code UNION select '4' as 'tabl',pac4.description as description,pac4salestarget.pac4code as paccode,sum(pac4salestarget.salestarget) as salestarget from pac4 left join pac4salestarget on pac4.code=pac4salestarget.pac4code  where pac4salestarget.userid='$userid' and pac4salestarget.yearmonth='$yearmonth' GROUP BY pac4.code UNION select '5' as 'tabl',product.description as description,productsalestarget.productcode as paccode,sum(productsalestarget.salestarget) as salestarget from product left join productsalestarget on product.code=productsalestarget.productcode  where productsalestarget.userid='$userid' and productsalestarget.yearmonth='$yearmonth' GROUP BY product.code  LIMIT 5 ";

				break;

				 case 'Branch':
								$query = "select '1' as 'tabl',pac1.description as description, pac1salestarget.pac1code as paccode,sum(pac1salestarget.salestarget) as salestarget  from pac1 left join pac1salestarget on pac1.code=pac1salestarget.pac1code  left join users on pac1salestarget.userid=users.userid where users.branch='$branchNo' and pac1salestarget.yearmonth='$yearmonth' GROUP BY pac1.code  UNION select '2' as 'tabl',pac2.description as description, pac2salestarget.pac2code as paccode,sum(pac2salestarget.salestarget) as salestarget  from pac2 left join pac2salestarget on pac2.code=pac2salestarget.pac2code  left join users on pac2salestarget.userid=users.userid where users.branch='$branchNo' and pac2salestarget.yearmonth='$yearmonth' GROUP BY pac2.code UNION select '3' as 'tabl',pac3.description as description, pac3salestarget.pac3code as paccode,sum(pac3salestarget.salestarget) as salestarget  from pac3 left join pac3salestarget on pac3.code=pac3salestarget.pac3code  left join users on pac3salestarget.userid=users.userid where users.branch='$branchNo' and pac3salestarget.yearmonth='$yearmonth' GROUP BY pac3.code UNION select '4' as 'tabl',pac4.description as description, pac4salestarget.pac4code as paccode,sum(pac4salestarget.salestarget) as salestarget  from pac4 left join pac4salestarget on pac4.code=pac4salestarget.pac4code  left join users on pac4salestarget.userid=users.userid where users.branch='$branchNo' and pac4salestarget.yearmonth='$yearmonth' GROUP BY pac4.code UNION select '5' as 'tabl',product.description as description, productsalestarget.productcode as paccode,sum(productsalestarget.salestarget) as salestarget  from product left join productsalestarget on product.code=productsalestarget.productcode  left join users on productsalestarget.userid=users.userid where users.branch='$branchNo' and productsalestarget.yearmonth='$yearmonth' GROUP BY product.code
								limit 5  ";
				break;
			}
			$result= $this->db->query($query)->result();
		return $result;
	}

	public function  getSalesTotalMonthWise($G_level,$userid=0,$branchNo=0,$repclause) {
		$yearmonth=date("Y").date("m");
		switch($G_level) {
			case 'Company':
				$result2= $this->db->query("select sum(pac1sales.msales0) as salesmtd, pac1sales.pac1code as paccode from pac1sales group by pac1code UNION select sum(pac2sales.msales0) as salesmtd, pac2sales.pac2code as paccode from pac2sales group by pac2code UNION select sum(pac3sales.msales0) as salesmtd, pac3sales.pac3code as paccode from pac3sales group by pac3code UNION select sum(pac4sales.msales0) as salesmtd, pac4sales.pac4code as paccode from pac4sales group by pac4code UNION select sum(productsales.msales0) as salesmtd, productsales.prodcode as paccode from productsales group by prodcode")->result();
	
			 $returnArray = array();
			 
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
				foreach ($rep as $repclause) {
					if ($i==0) {
						$sql1.=" where repcode='".$repclause."' ";   $i++;
					} else {
						$sql1.="  or  repcode='".$repclause."' ";
					}
				}
				$sql.= "group by pac1code";
	
				$final_sql = "select pac1code as paccode ,sum(pac1sales.msales0) as salesmtd from pac1sales ".$sql1." group by pac1code UNION select pac2code as paccode, sum(pac2sales.msales0) as salesmtd from pac2sales ".$sql1."  group by pac2code UNION select pac3code as paccode, sum(pac3sales.msales0) as salesmtd from pac3sales ".$sql1." group by pac3code UNION select pac4code as paccode,sum(pac4sales.msales0) as salesmtd from pac4sales ".$sql1." group by pac4code  UNION select prodcode as paccode,sum(productsales.msales0) as salesmtd from productsales ".$sql1." group by prodcode ";
	
				$result2= $this->db->query($final_sql)->result();
	
				$returnArray=array();
				foreach ($result2 as $data) {
					$returnArray[$data->paccode]=$data->salesmtd;
				}
			break;
	
		  case 'Branch':
		  $result2= $this->db->query("select sum(pac1sales.msales0) as salesmtd from pac1sales where pac1sales.branch='$branchNo' group by pac1code UNION select sum(pac2sales.msales0) as salesmtd from pac2sales where pac2sales.branch='$branchNo' group by pac2code UNION  select sum(pac3sales.msales0) as salesmtd from pac3sales where pac3sales.branch='$branchNo' group by pac3code UNION select sum(pac4sales.msales0) as salesmtd from pac4sales where pac4sales.branch='$branchNo' group by pac4code UNION select sum(productsales.msales0) as salesmtd from productsales where productsales.branch='$branchNo' group by prodcode")->result();
				
				$returnArray = array();
				foreach ($result2 as $data) {
					$returnArray[$data->paccode]=$data->salesmtd;
				}
		break;
		}
		return $returnArray;
	}



	public function  getCustomerSalesTotalMonthWise($G_level,$userid=0,$branchNo=0,$repclause, $account) {
		$yearmonth=date("Y").date("m");
		$WHERE = array(
			"pac0" => "where customersales.account = '".$account."'",
			"pac1" => "where customerpac1sales.account = '".$account."'",
			"pac2" => "where customerpac2sales.account = '".$account."'",
			"pac3" => "where customerpac3sales.account = '".$account."'",
			"pac4" => "where customerpac4sales.account = '".$account."'",
			"product" => "where customerprodsales.account = '".$account."'",
		);
		$query_execute = "";
		switch($G_level) {
			case 'Company':
				$query_execute = "select sum(customersales.msales0) as salesmtd, '0' as paccode from customersales ".$WHERE["pac0"]." UNION select sum(customerpac1sales.msales0) as salesmtd, customerpac1sales.pac1code as paccode from customerpac1sales ".$WHERE["pac1"]." group by pac1code UNION select sum(customerpac2sales.msales0) as salesmtd, customerpac2sales.pac2code as paccode from customerpac2sales  ".$WHERE["pac2"]." group by pac2code UNION select sum(customerpac3sales.msales0) as salesmtd, customerpac3sales.pac3code as paccode from customerpac3sales  ".$WHERE["pac3"]." group by pac3code UNION select sum(customerpac4sales.msales0) as salesmtd, customerpac4sales.pac4code as paccode from customerpac4sales  ".$WHERE["pac4"]." group by pac4code UNION select sum(customerprodsales.msales0) as salesmtd, customerprodsales.prodcode as paccode from customerprodsales  ".$WHERE["product"]." group by prodcode";
				$result2= $this->db->query($query_execute)->result();
			//	echo $query_execute;
			 $returnArray = array();
			 
				foreach ($result2 as $data) {
			  $returnArray[$data->paccode]=$data->salesmtd;
			 }
		break;
						
			case 'User':
				$sql="select sum(customerpac1sales.msales0) as salesmtd from customerpac1sales ";
				$sql1="";
				$repclause=str_replace("IN ('", "", $repclause);
				$repclause=str_replace("')", "", $repclause);
				$repclause=str_replace("','", "|", $repclause);
				$rep=explode('|',$repclause);
				$i=0;
				foreach ($rep as $repclause) {
					if ($i==0) {
						$sql1.=" repcode='".$repclause."' ";   $i++;
					} else {
						$sql1.="  or  repcode='".$repclause."' ";
					}
				}
				$sql.= "group by pac1code";
				$sql1 = "1=1";
				$final_sql = "select '0' as paccode ,sum(customersales.msales0) as salesmtd from customersales ".$WHERE["pac0"]." and (".$sql1.")  UNION select pac1code as paccode ,sum(customerpac1sales.msales0) as salesmtd from customerpac1sales ".$WHERE["pac1"]." and (".$sql1.")  group by pac1code UNION select pac2code as paccode, sum(customerpac2sales.msales0) as salesmtd from customerpac2sales ".$WHERE["pac2"]." and (".$sql1." ) group by pac2code UNION select pac3code as paccode, sum(customerpac3sales.msales0) as salesmtd from customerpac3sales ".$WHERE["pac3"]." and (".$sql1.") group by pac3code UNION select pac4code as paccode,sum(customerpac4sales.msales0) as salesmtd from customerpac4sales ".$WHERE["pac4"]." and (".$sql1.") group by pac4code  UNION select prodcode as paccode,sum(customerprodsales.msales0) as salesmtd from customerprodsales ".$WHERE["product"]." and (".$sql1.") group by prodcode ";
			//	echo $final_sql;
				$result2= $this->db->query($final_sql)->result();
	
				$returnArray=array();
				foreach ($result2 as $data) {
					$returnArray[$data->paccode]=$data->salesmtd;
				}
			break;
	
		  case 'Branch':
		  $result2= $this->db->query("select sum(customersales.msales0) as salesmtd from customersales where customersales.branch='$branchNo' ".$WHERE["pac0"]." UNION select sum(customerpac1sales.msales0) as salesmtd from customerpac1sales where customerpac1sales.branch='$branchNo' ".$WHERE["pac1"]."  group by pac1code UNION select sum(customerpac2sales.msales0) as salesmtd from customerpac2sales where customerpac2sales.branch='$branchNo' ".$WHERE["pac2"]."  group by pac2code UNION  select sum(customerpac3sales.msales0) as salesmtd from customerpac3sales where customerpac3sales.branch='$branchNo'  ".$WHERE["pac3"]." group by pac3code UNION select sum(customerpac4sales.msales0) as salesmtd from customerpac4sales where customerpac4sales.branch='$branchNo' ".$WHERE["pac4"]."  group by pac4code UNION select sum(customerprodsales.msales0) as salesmtd from customerprodsales where customerprodsales.branch='$branchNo' ".$WHERE["product"]."  group by prodcode")->result();
				
				$returnArray = array();
				foreach ($result2 as $data) {
					$returnArray[$data->paccode]=$data->salesmtd;
				}
		break;
		}
		return $returnArray;
	}


	public function getSalesTargetForLastThreeYear($G_level, $yearmonthArray = array(), $salesArray = array(), $userid = 0, $branchNo = 0)
	{
		$currYear = date("Y");
		$prev1 = $currYear - 1;
		$prev2 = $currYear - 2;

		$year = array
		(
			'0' => $currYear,
			'1' => $prev1,
			'2' => $prev2,
		);

		$yearMonthArray = array();

		foreach ($year as $yr)
		{
			for ($i = 1; $i <= 12 ; $i++)
			{
				$pre = '';

				if ($i < 10)
				{
					$pre = '0';
				}

				$yearMonthArray[] = $yr.$pre.$i;
			}
		}

		if ($G_level == "Company")
		{
			$this->db->select('yearmonth, SUM(salestarget) AS saletarget');
			$this->db->from('usersalestarget');
		}

		if ($G_level == "User")
		{
			$this->db->select('yearmonth, SUM(salestarget) AS saletarget');
			$this->db->from('usersalestarget');
			$this->db->where('userid = ', $userid);
		}

		if ($G_level == "Branch")
		{
			$this->db->select('yearmonth, SUM(salestarget) AS saletarget');
			$this->db->from('branchsalestarget');
			$this->db->where('branch = ', $branchNo);
		}

		$this->db->where_in('yearmonth', $yearMonthArray);
		$this->db->group_by('yearmonth');

		$query = $this->db->get();
		$result = $query->result_array();

		$resultArray = array();

		foreach ($result as $res)
		{
			$nYear = substr($res['yearmonth'], 0, 4);
			$resultArray[$nYear][] = $res['saletarget'];
			$resultArray[$res['yearmonth']] = $res['saletarget'];
		}

		foreach ($yearmonthArray as $key => $yearmonth)
		{
			if (isset($resultArray[$yearmonth]))
			{
				$resultArray['monthlysalespc'][$yearmonth] = ($salesArray[$key] / $resultArray[$yearmonth]) * 100;
			}
			else
			{
				$resultArray['monthlysalespc'][$yearmonth] = 0;
			}
		}

		return $resultArray;
	}

	public function getCustomerSalesTargetForThisYear($salesArray = array(), $page_code, $pac_code, $account_code)
	{
		$table_name = "customerproductsalestarget";
		$column_name = "productcode";

		if ($page_code == "pac1" || $page_code == "pac2" || $page_code == "pac3" || $page_code == "pac4")
		{
			$table_name = "customer".$page_code."salestarget";
			$column_name = $page_code."code";
		}
		else if ($page_code == "customer")
		{
			$table_name = "customersalestarget";
		}

		$currYear = date("Y");

		$year = array
		(
			"0" => $currYear
		);

		$yearMonthArray = array();

		foreach ($year as $yr)
		{
			for ($i = 1; $i <= 12 ; $i++)
			{
				$pre = "";

				if ($i < 10)
				{
					$pre = "0";
				}

				$yearMonthArray[] = $yr.$pre.$i;
			}
		}

		$this->db->select("yearmonth, SUM(salestarget) AS saletarget");
		$this->db->from($table_name);
		$this->db->where("account = ", $account_code);

		if ($pac_code != null)
		{
			$this->db->where($column_name . " = ", $pac_code);
		}

		$this->db->where_in("yearmonth", $yearMonthArray);
		$this->db->group_by("yearmonth");

		$query = $this->db->get();
		$result = $query->result_array();

		$resultArray = array();

		foreach ($result as $res)
		{
			$nYear = substr($res["yearmonth"], 0, 4);
			$resultArray[$nYear][] = $res["saletarget"];
			$resultArray[$res["yearmonth"]] = $res["saletarget"];
		}

		return $resultArray;
	}

	public function getProductSalesTargetForLastThreeYear($G_level, $yearmonthArray = array(), $salesArray = array(), $userId = 0, $branchNo = 0, $page_code, $product_code)
	{
		if ($page_code == 1 || $page_code == 2 || $page_code == 3 || $page_code == 4) {
			$table_name="pac".$page_code."salestarget";
		} else {
			$table_name="productsalestarget";
		}

		$currYear = date("Y");
		$prev1 = $currYear - 1;
		$prev2 = $currYear - 2;

		$year = array
		(
			'0' => $currYear,
			'1' => $prev1,
			'2' => $prev2,
		);

		$yearMonthArray = array();

		foreach ($year as $yr)
		{
			for ($i = 1; $i <= 12 ; $i++)
			{
				$pre = '';

				if ($i < 10)
				{
					$pre = '0';
				}

				$yearMonthArray[] = $yr.$pre.$i;
			}
		}

		switch($G_level)
		{
			case 'Company':
				if ($page_code == 1 || $page_code == 2 || $page_code == 3 || $page_code == 4) {
					$result=$this->db->query("select ".$table_name.".yearmonth, sum(".$table_name.".salestarget) AS saletarget from ".$table_name." left join users on ".$table_name.".userid=users.userid where ".$table_name.".pac".$page_code."code='$product_code' group by ".$table_name.".yearmonth order by ".$table_name.".yearmonth desc ")->result_array();
				} else {
					$result=$this->db->query("select ".$table_name.".yearmonth, sum(".$table_name.".salestarget) AS saletarget from ".$table_name." left join users on ".$table_name.".userid=users.userid where ".$table_name.".productcode='$product_code' group by ".$table_name.".yearmonth order by ".$table_name.".yearmonth desc ")->result_array();
				}

				break;
			case 'User':
				if ($page_code == 1 || $page_code == 2 || $page_code == 3 || $page_code == 4) {
					$result=$this->db->query("select ".$table_name.".yearmonth, sum(".$table_name.".salestarget) AS saletarget from ".$table_name." left join users on ".$table_name.".userid=users.userid where users.userid='$userId' and ".$table_name.".pac".$page_code."code='$product_code' group by ".$table_name.".yearmonth order by ".$table_name.".yearmonth desc  ")->result_array();
				} else {
					$result=$this->db->query("select ".$table_name.".yearmonth, sum(".$table_name.".salestarget) AS saletarget from ".$table_name." left join users on ".$table_name.".userid=users.userid where users.userid='$userId' and ".$table_name.".productcode='$product_code' group by ".$table_name.".yearmonth order by ".$table_name.".yearmonth desc  ")->result_array();
				}
				break;
			case 'Branch':
				if ($page_code == 1 || $page_code == 2 || $page_code == 3 || $page_code == 4) {
					$result=$this->db->query("select ".$table_name.".yearmonth, sum(".$table_name.".salestarget) AS saletarget from ".$table_name." left join users on ".$table_name.".userid=users.userid where  ".$table_name.".pac".$page_code."code='$product_code' and users.branch='$branchNo' group by ".$table_name.".yearmonth order by ".$table_name.".yearmonth desc  ")->result_array();
				} else {
					$result=$this->db->query("select ".$table_name.".yearmonth, sum(".$table_name.".salestarget) AS saletarget from ".$table_name." left join users on ".$table_name.".userid=users.userid where ".$table_name.".productcode='$product_code' and users.branch='$branchNo' group by ".$table_name.".yearmonth order by ".$table_name.".yearmonth desc  ")->result_array();
				}
				break;
		}

		$resultArray = array();

		foreach ($result as $res)
		{
			$nYear = substr($res['yearmonth'], 0, 4);
			$resultArray[$nYear][] = $res['saletarget'];
			$resultArray[$res['yearmonth']] = $res['saletarget'];
		}

		foreach ($yearMonthArray as $key => $yearmonth)
		{
			if (isset($resultArray[$yearmonth]))
			{
				$resultArray['monthlysalespc'][$yearmonth] = ($salesArray[$key] / $resultArray[$yearmonth]) * 100;
			}
			else
			{
				$resultArray['monthlysalespc'][$yearmonth] = 0;
			}
		}

		return $resultArray;
	}

	public function getPac1QuoteConversionForCurrentMonth($repwhere = "")
	{
		$this->db->select("pac1.code, pac1.description, SUM(kpidata.actualvalue1) AS value_this_month, SUM(kpidata.actualvalue2) AS quantity_this_month", false);
		$this->db->from("pac1");
		$this->db->join("kpidata", "kpidata.identifier = CONCAT('MIDASQUOTES', pac1.code)", "LEFT");
		$this->db->where("kpidata.date = curdate()");

		if (!empty($repwhere))
		{
			$repCodeArray = explode(",", $repwhere);

			if (!empty($repCodeArray))
			{
				$repclause = "kpidata.analysis IN ('".implode("','", $repCodeArray)."')";
				$this->db->where($repclause);
			}
		}

		$this->db->group_by("kpidata.identifier");
		$query = $this->db->get();
		$resultArray = $query->result_array();

		$totalValueThisMonth = 0;
		$totalQuantityThisMonth = 0;
		foreach ($resultArray as $resultArrayItem)
		{
			$totalValueThisMonth += $resultArrayItem['value_this_month'];
			$totalQuantityThisMonth += $resultArrayItem['quantity_this_month'];
		}

		$totalRow = array(
			'code'  => null,
			'description' => null,
			'value_this_month' => number_format($totalValueThisMonth, 2, '.', ''),
			'quantity_this_month' => $totalQuantityThisMonth
		);

		array_push($resultArray, $totalRow);

		return $resultArray;
	}

	public function getSalesPipelineStages($repwhere = "")
	{
		$this->db->select('pls.code, pls.description, SUM(kpidata.actualvalue1) as value', false);
		$this->db->from('pipelinestages pls');
		$this->db->join("kpidata", "kpidata.identifier = CONCAT('MIDASPIPELINE', pls.code)", "LEFT");
		$this->db->where("kpidata.date = curdate()");

		if (!empty($repwhere))
		{
			$repCodeArray = explode(",", $repwhere);

			if (!empty($repCodeArray))
			{
				$repclause = "kpidata.analysis IN ('".implode("','", $repCodeArray)."')";
				$this->db->where($repclause);
			}
		}

		$this->db->group_by('kpidata.identifier');
		$query = $this->db->get();
		$resultArray = $query->result_array();

		$totalValue = 0;
		foreach ($resultArray as $resultArrayItem)
		{
			$totalValue+= $resultArrayItem['value'];
		}

		if ($totalValue != 0)
		{
			$i = 0;

			foreach ($resultArray as $resultArrayItem)
			{
				$resultArray[$i]['percentage'] = $resultArrayItem['value'] / $totalValue * 100;

				$i++;
			}
		}

		$totalRow = array(
			'code'  => null,
			'description' => null,
			'value' => number_format($totalValue, 2, '.', ''),
			'percentage' => null
		);

		array_push($resultArray, $totalRow);

		return $resultArray;
	}

	public function GetYearTotal($data, $start, $finish)
	{
        $year0total = 0;

        for ($counter = $start; $counter <= $finish; $counter++)
        {
            $year0total += $data[$counter];
        }

		return $year0total;
	}

	public function GetYearData($data, $start, $finish)
	{
		$yeardata = "[";

        for ($counter = $start; $counter <= $finish; $counter++)
        {
            $yeardata .= $data[$counter];

            if ($counter != $finish)
            {
                $yeardata .= ",";
            }
        }

        $yeardata .= "]";

		return $yeardata;
	}

	public function GetCumulativeYearData($data, $start, $finish)
	{
		$yearData = "[";
		$cumulativeYearData = 0;

        for ($counter = $start; $counter <= $finish; $counter++)
        {
			$cumulativeYearData += $data[$counter];
            $yearData .= $cumulativeYearData;

            if ($counter != $finish)
            {
                $yearData .= ",";
            }
        }

        $yearData .= "]";

		return $yearData;
	}

	public function GetYearTable($data, $yeartotal, $start, $finish)
	{
		$yeartable = "";

        for ($counter = $start; $counter <= $finish; $counter++)
        {
            $yeartable .= "<td><b>" . number_format($data[$counter]) . "</b></td>";
        }

        $yeartable .= "<td><b>" . number_format($yeartotal) . "</b></td>";

		return $yeartable;
	}

	public function GetTargetDataForCurrentYear($salesTargetForLastThreeYear)
	{
		$currentYear = date("Y");
		$targetData = "[";

		for ($counter = 1; $counter <= 12; $counter++)
		{
			$prefix = "";

			if ($counter < 10)
				$prefix = "0";

			if (isset($salesTargetForLastThreeYear[$currentYear.$prefix.$counter]))
				$targetData .= $salesTargetForLastThreeYear[$currentYear.$prefix.$counter];
			else
				$targetData .= "0";

			if ($counter != 12)
                $targetData .= ",";
		}

		$targetData .= "]";

		return $targetData;
	}

	public function GetCumulativeTargetDataForCurrentYear($salesTargetForLastThreeYear)
	{
		$currentYear = date("Y");
		$targetData = "[";
		$cumulativeTarget = 0;

		for ($counter = 1; $counter <= 12; $counter++)
		{
			$prefix = "";

			if ($counter < 10)
				$prefix = "0";

			if (isset($salesTargetForLastThreeYear[$currentYear.$prefix.$counter]))
				$cumulativeTarget += $salesTargetForLastThreeYear[$currentYear.$prefix.$counter];

			$targetData .= $cumulativeTarget;

			if ($counter != 12)
				$targetData .= ",";
		}

		$targetData .= "]";

		return $targetData;
	}



	public function getYearStartMonth() {
		$this->db->select('yearstartmonth');
		$this->db->from('system');
		$query = $this->db->get();
		$res = $query->row_array();

		return isset($res) ? $res['yearstartmonth'] : 1;
	}
}
