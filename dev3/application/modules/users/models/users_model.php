<?php

class Users_model extends Model {
	
	/* Fetch all the users in the system. */
    public function getUsersList($isAdmin=true){			
		$userid = $this->session->userdata('userid');		
		$this->db->select('userid, email, firstname, surname, thislogin, lastlogin, repcode, repcode_2, repcode_3, repcode_4, repcode_5, branch, usertype, k8userid, administrator, active, seemargins, editnotes, editterms');
		
        $this->db->from('users');
		
		if(!$isAdmin) {
			$this->db->where('userid', $userid);
		}
		
        $this->db->order_by('firstname', 'ASC');
        $this->db->order_by('surname', 'ASC');
        $query = $this->db->get();
		
	//	echo $this->db->last_query();
        return $query->result_array();
	}
	

// Get Active User //

    public function getActiveUsersList($isAdmin=true){			
				
		$this->db->select('*');
		
        $this->db->from('users');
         $this->db->where('active',1);
		
	
        $query = $this->db->get();
		
	//	echo $this->db->last_query();
        return $query->result_array();
	}
	
// End Get Active User //	

	/* Fetch all the users list for drop down. */
    public function getUsersListDropDown(){	
		$this->db->select('userid, firstname, surname');
        $this->db->from('users');
        $this->db->order_by('firstname', 'ASC');
        $this->db->order_by('surname', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
	}
    
	/* Fetch the target details with the target id */
    public function getTargetDetails($id) {		
		$this->db->select('id, userid, yearmonth, salestarget');
		$this->db->from('usersalestarget');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row_array();		
	}    
    
	/* Fetch the user details with the user id */
    public function getUserDetails($userid)
    {
		$this->db->select('*');
		$this->db->from('users');
		$this->db->where('userid', $userid);

		$query = $this->db->get();

		return $query->row_array();		
	}
	
	/* Fetch the users targets with the user id */
    public function getUserTargets($userid) {		
		$this->db->select('id, userid, yearmonth, salestarget');
		$this->db->from('usersalestarget');
		$this->db->where('userid', $userid);
		$query = $this->db->get();
		return $query->result_array();		
	}
	
	/* Fetch all the user types available in the system */	
	public function getUserTypes() {
		$userTypes = array(
			array(
				"option" => "-Select-",
				"value" => ""
			),
			array(
				"option" => "(B)ranch",
				"value" => "B"
			),
			array(
				"option" => "Sales (R)ep",
				"value" => "R"
			),
			array(
				"option" => "(A)ll",
				"value" => "A"
			),
		);
        return $userTypes;
	}
	
	/* Fetch all the available branches from the system */	
	public function getBranches() {
		$this->db->select('branch, name');
        $this->db->from('branch');
        $query = $this->db->get();
        return $query->result_array();
	}
	
	/* Add new user and update old users */
	public function userModify($data) {
		$uesrid = 0;
		$type = $data['type'];
		unset($data['type']);
		$unique = $this->checkUnique($data['email'], $data['userid']);
		if($unique) {
			if(isset($data['userid']) && 0!=intval($data['userid'])) {
				if(""!=$type) {
					$uesrid = $this->db->insert('users', $data); 
					$description = "New user #".$uesrid." - ".$data['firstname']." ".$data['surname']." created.";
					$this->savelog($description);
				} else {
					$userDetail = $this->getUserDetails($data['userid']);
					$this->db->where('userid', $data['userid']);
					if($this->db->update('users', $data)) {
						$uesrid = $data['userid'];
						$description = $this->descriptionUpdateUser($userid, $data);					
						$this->savelog($description);
					} 
				}				
			} else {
				$this->db->insert('users', $data); 
				$uesrid = $this->db->insert_id();
			}
			$userreps = array(
				$data['repcode'],
				$data['repcode_2'],
				$data['repcode_3'],
				$data['repcode_4'],
				$data['repcode_5'],
				$data['repcode_6'],
				$data['repcode_7'],
				$data['repcode_8'],
				$data['repcode_9'],
				$data['repcode_10']
			);
			$this->refreshUserRep($userreps, $uesrid);
		}
		return $uesrid;
	}
	
	/* Check if the email is unique. */
	public function checkUnique($email, $id) {
		$email = str_replace(array('--atrate--', '--dot--'), array('@', '.'), $email);
		$this->db->select('count(email) as cnt');
        $this->db->from('users');
        $this->db->where('email', $email);
		if(""!=$id) {
			$this->db->where('userid <>', $id);
		}        
        $query = $this->db->get();
        $result = $query->row_array();
		return intval($result['cnt'])==0;
	}
	
	/* Function to delete the user with it's id */
	public function deleteUser($userid) {
		return $this->db->delete('users', array('userid' => $userid)); 
	}
	
	/* Function to delete a target with it's id */
	public function deleteUserTarget($id) {
		return $this->db->delete('usersalestarget', array('id' => $id)); 
	}
	
	/* Function to create the description for update target */
	public function descriptionUpdateTarget($id, $data) {
		$targetDetail = $this->getTargetDetailsById($id);
		$description = "The target of Year/Month ".$targetDetail['yearmonth']." for the user #".$targetDetail['userid']." - ".$targetDetail['firstname']." ".$targetDetail['surname']." changed.";
		$changes = array();
		foreach($data as $key=>$value) {
			if($userDetail[$key]!=$value) {
				$changes[] = " ".ucfirst($key)." changed from [".$targetDetail[$key]."] to [".$value."]";
			}
		}
		
		$allChanges = implode("; ", $changes);
		$description .= $allChanges;
		return $description;
	}
	
	/* Function to create the description for update user */
	public function descriptionUpdateUser($userid, $data) {
		$userDetail = $this->getUserDetails($userid);
		$description = "User #".$userid." - ".$userDetail['firstname']." ".$userDetail['surname']." changed.";
		$changes = array();
		foreach($data as $key=>$value) {
			if($userDetail[$key]!=$value) {
				$changes[] = " ".ucfirst($key)." changed from [".$userDetail[$key]."] to [".$value."]";
			}
		}
		
		$allChanges = implode("; ", $changes);
		$description .= $allChanges;
		return $description;
	}
	
	/* Function to update surname by userid */
	public function updateSurnameByUserId($userid, $surname) {
		$data["surname"] = $surname; 
		$description = $this->descriptionUpdateUser($userid, $data);
		$this->db->where('userid', $userid);
		$return = $this->db->update('users', $data);
		if($return) {			
			$this->savelog($description);
		}
		
		return $return;
	}
	
	/* Function to update surname by userid */
	public function updateFirstnameByUserId($userid, $firstname) {
		$data["firstname"] = $firstname; 
		$description = $this->descriptionUpdateUser($userid, $data);
		$this->db->where('userid', $userid);
		$return = $this->db->update('users', $data);
		
		if($return) {
			$this->savelog($description);
		}
		
		return $return;
	}

		public function updatekpi($user,$kpithreshold1,$kpithreshold2) {
	
		if(isset($user) && 0!=intval($user)) {
			$value=array('kpithreshold1'=>$kpithreshold1,'kpithreshold2'=>$kpithreshold2);
			$this->db->where('userid', $user);
			if($this->db->update('users', $value)) {
			$result="success";
			} 	
			else{


				$result="fail";
			}


		} 
		return $result;
	}
	
	/* Function to update email by userid */
	public function updateEmailByUserId($userid, $email) {
		$data["email"] = $email; 
		$description = $this->descriptionUpdateUser($userid, $data);
		$this->db->where('userid', $userid);
		$return = $this->db->update('users', $data);
		
		if($return) {
			$this->savelog($description);
		}
		
		return $return;
	}
	
	/* Add unique target for a user */
	public function addUniqueTarget($userid, $year, $month, $target) {
		if(!$this->checkTargetUnique($userid, $year, $month)) {
			return "duplicate";
		} else {
			$data = array(
				"userid"		=> $userid,
				"yearmonth"		=> $year.$month,
				"salestarget"	=> $target
			);
			$userDetails = $this->getUserDetails($userid);
			if($this->db->insert('usersalestarget', $data)) {
				$description = "New target added for the user #".$userid." - ".$userDetails['firstname']." ".$userDetails['surname'];
				$this->savelog($description);
				return "success";
			} else {
				return "fail";
			}
		}
	}
	
	/* Function to check if the adding target is unique or not. This will return true, if the target is unique. */	
	public function checkTargetUnique($userid, $year, $month) {
		$yearmonth = $year.$month;
		$this->db->select('count(id) as cnt');
        $this->db->from('usersalestarget');
        $this->db->where('userid', $userid);
        $this->db->where('yearmonth', $yearmonth);      
        $query = $this->db->get();
        $result = $query->row_array();
		return intval($result['cnt'])==0;
	}
	
	/* Function to get the userid of a target by target id */
	public function getUserIdByTargetId($id) {
		$this->db->select('userid');
        $this->db->from('usersalestarget');
        $this->db->where('id', $id);   
        $query = $this->db->get();
        $result = $query->row_array();
		return $result['userid'];
	}
	
	/* Function to get the target details including user details of a target by target id */
	public function getTargetDetailsById($id) {
		$this->db->select('id, usersalestarget.userid, firstname, surname, yearmonth, salestarget');
		$this->db->from('usersalestarget');
		$this->db->join('users', 'users.userid=usersalestarget.userid', 'left');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row_array();	
	}
	
	/* Function to update Year/Month value of a target with target id as id */
	public function updateYearMonth($id, $yearmonth) {
		$data["yearmonth"] = $yearmonth; 
		$description = $this->descriptionUpdateTarget($id, $data);
		$this->db->where('id', $id);
		$return = $this->db->update('usersalestarget', $data);
		if($return) {
			$this->savelog($description);
		}
		
		return $return;
	}
	
	/* Function to update sales target value of a target with target id as id */
	public function updateSalesTarget($id, $salestarget) {
		$data["salestarget"] = $salestarget; 
		$description = $this->descriptionUpdateTarget($id, $data);
		$this->db->where('id', $id);
		$return = $this->db->update('usersalestarget', $data);
		if($return) {
			$this->savelog($description);			
		}
		
		return $return;
	}	
	
	/* Function to save log with description as $description and user type as $type. $type is either (U)ser or (S)ystem. By default it is (U)ser */
	public function savelog($description, $type="U") {
		$data['userid'] = $this->session->userdata('userid'); /* Logged in user id */
		$data['type'] = $type; /* U (for user)/S (for system) */
		$data['date'] = date('Y-m-d');
		$data['time'] = date('Y-m-d h:i:s');
		$data['description'] = $description;
		$this->db->insert('systemlog', $data);
	}
	
	/* Function to check if the user have any system log associated with it. */
	public function userHaveSystemLog($userid) {
		$this->db->select('count(id) as logs');
		$this->db->from('systemlog');
		$this->db->where('userid', $userid);
		$query = $this->db->get();
		$log_count = $query->row_array();	
		return 0!=intval($log_count['logs']);
	}
	
	/* Function to check if the limit of the license is crossed. */
	public function licenseLimitCrossed() {
		$this->db->select('count(userid) as licenses');
		$this->db->from('users');
		$this->db->where('active', '1');
		$query = $this->db->get();
		$count = $query->row_array();
		$licensesUsed = intval($count['licenses']);
		
		$this->db->select('activeusers');
		$this->db->from('system');
		$query = $this->db->get();
		$active = $query->row_array();
		$activeusers = intval($active['activeusers']);
		if($activeusers>0) {
			return $licensesUsed>=$activeusers;
		} else {
			return false;
		}
		
	}


	public function csv_export($isAdmin=true) {
$userid = $this->session->userdata('userid');		
		$this->db->select('userid as id,surname,firstname, email,  usertype');
		
        $this->db->from('users');
		
		if(!$isAdmin) {
			$this->db->where('userid', $userid);
		}
		
        $this->db->order_by('firstname', 'ASC');
        $this->db->order_by('surname', 'ASC');
        $query = $this->db->get();
        $this->load->dbutil();
    
        $opt= $this->dbutil->csv_from_result($query);


  $opt = str_replace($head_value1, $new_head1, $opt);

echo $opt;
	}
	
	public function refreshUserRep($userreps, $uesrid) {
		// Delete all the userreps for this user from userreps table
		// Insert new records if it exists
	}

	public function getUserRepDetails($userid) {
		return array();
	}

	public function userRepcodes($userid){
		$this->db->select("ur.userid as userid, ur.repcode as repcode, sr.name as name");
		$this->db->from("userreps ur");
		$this->db->join("salesrep sr", "ur.repcode = sr.repcode", "left");
		$this->db->where("userid", $userid);
		$query = $this->db->get();
		$result = array_map("userRepsDataTable",$query->result_array());
		return(array("data"=>$result));
	}
}

function userRepsDataTable($result_array) {
	$delete = "<span class='fa fa-fw fa-trash-o delete-repcode-customer pointer' data-account='".$result_array["account"]."' data-repcode='".$result_array["repcode"]."' onclick='deleteCustomerRep(event);'></span>";
	return array($result_array["repcode"],$result_array["name"],$delete);
}
