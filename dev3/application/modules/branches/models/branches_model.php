<?php

class Branches_model extends Model {
	
	/* Fetch all the branches in the system. */
  public function getBranchesList($isAdmin=true){		
		$this->db->select('branch, name');
		$this->db->from('branch');
		$this->db->order_by('name', 'ASC');
    $query = $this->db->get();
    return $query->result_array();
	}
    
	/* Fetch the branch details with the branch id */
    public function getBranchDetails($branch) {	
		$this->db->select('*');
		$this->db->from('branch');
		$this->db->where('branch', $branch);
		$query = $this->db->get();
		return $query->row_array();		
	}
	
	/* Fetch the branch targets with the branch id */
    public function getBranchTargets($branch) {		
		$this->db->select('id, branch, yearmonth, salestarget, marginok, margingood');
		$this->db->from('branchsalestarget');
		$this->db->where('branch', $branch);
		$query = $this->db->get();
		return $query->result_array();		
	}
	
	/* Fetch the branch target by target id */
    public function getBranchTargetDetails($targetId) {		
		$this->db->select('id, branch, yearmonth, salestarget, marginok, margingood');
		$this->db->from('branchsalestarget');
		$this->db->where('id', $targetId);
		$query = $this->db->get();
		return $query->row_array();		
	}
	
	/* Add new branch and update old branch */
	public function branchModify($data) {
		$type = $data['type'];
		unset($data['type']);
		
		$uniqueId = $this->checkUniqueID($data['branch']);
		$uniqueName = $this->checkUniqueName($data['name']);
		
		$unique = $uniqueId && $uniqueName;
		$branchDetail = $this->getBranchDetails($data['branch']);
		
		if(isset($branchDetail['branch']) && 0!=intval($branchDetail['branch']) && "add"!=$data["operation"]) {
			$updateData['name'] = $data['name'];
			$this->db->where('branch', $data['branch']);
			if($this->db->update('branch', $updateData)) {
				$branch = $data['branch'];
				$description = $this->descriptionUpdateBranch($branchDetail, $data);					
				$this->savelog($description, 'S');
			} 	
		} else {
			if($unique) {
				unset($data["operation"]);
				if($this->db->insert('branch', $data)) {
					$description = $this->descriptionAddBranch($data);
					$this->savelog($description, 'S');
				}
				$branch = $data['branch'];
			} else {
				$branch = 0;
			}
		}
		return $branch;
	}
	

// KPI start

	public function updatekpi($branch,$kpithreshold1,$kpithreshold2) {
	
		if(isset($branch) && 0!=intval($branch)) {
			$value=array('kpithreshold1'=>$kpithreshold1,'kpithreshold2'=>$kpithreshold2);
			$this->db->where('branch', $branch);
			if($this->db->update('branch', $value)) {
			$result="success";
			} 	
			else{


				$result="fail";
			}


		} 
		return $result;
	}
// End KPI start

	/* Check if the branch id is unique. */
	public function checkUniqueID($branch) {		
		$this->db->select('count(branch) as cnt');
    $this->db->from('branch');
    $this->db->where('branch', $branch);
    $query = $this->db->get();
    $result = $query->row_array();
		return intval($result['cnt'])==0;
	}
	
	/* Check if the branch name is unique. */
	public function checkUniqueName($name) {		
		$this->db->select('count(branch) as cnt');
    $this->db->from('branch');
    $this->db->where('name', $name);
    $query = $this->db->get();
    $result = $query->row_array();
		return intval($result['cnt'])==0;
	}
	
	/* Function to delete the branch with it's id */
	public function deleteBranch($branch) {
		return $this->db->delete('branch', array('branch' => $branch)); 
	}
	
	/* Function to delete a target with it's id */
	public function deleteBranchTarget($id) {
		return $this->db->delete('branchsalestarget', array('id' => $id)); 
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
	
	/* Function to create the description for update branch target */
	public function descriptionUpdateBranchTarget($targetId, $data) {
		$branchTargetDetail = $this->getBranchTargetDetails($targetId);
		$description = "Branch target #".$branchTargetDetail['id']." for ".$branchTargetDetail['yearmonth']." is changed.";
		$changes = array();
		foreach($data as $key=>$value) {
			if($branchTargetDetail[$key]!=$value) {
				$changes[] = " ".ucfirst($key)." changed from [".$branchTargetDetail[$key]."] to [".$value."]";
			}
		}
		$allChanges = implode("; ", $changes);
		$description .= $allChanges;
		return $description;
	}
	
	/* Function to create the description for update branch */
	public function descriptionUpdateBranch($branchDetail, $data) {
		$description = "Branch #".$branchDetail['branch']." - ".$branchDetail['name']." changed.";
		$changes = array();
		unset($data["operation"]);
		foreach($data as $key=>$value) {
			if($branchDetail[$key]!=$value) {
				$changes[] = " ".ucfirst($key)." changed from [".$branchDetail[$key]."] to [".$value."]";
			}
		}
		$allChanges = implode("; ", $changes);
		$description .= $allChanges;
		return $description;
	}
	
	
	/* Function to create the description for add branch */
	public function descriptionAddBranch($data) {
		$description = "New branch #".$data['branch']." - ".$data['name']." created.";
		return $description;
	}
	
	/* Function to update branch name by branch id */
	public function updateBranchnameByBranchId($id, $name) {
		$data["name"] = $name;
		$branchDetail = $this->getBranchDetails($id);
		$this->db->where('branch', $id);
		$return = $this->db->update('branch', $data);	
		if($return) {
			$description = $this->descriptionUpdateBranch($branchDetail, array("name"=>$name, "operation"=>"edit"));					
			$this->savelog($description, 'S');
		}
		return $return;
	}
		
	/* Add unique target for a branch */
	public function addUniqueTarget($branch, $year, $month, $salestarget, $marginok, $margingood) {
		if(!$this->checkTargetUnique($branch, $year, $month)) {
			return "duplicate";
		} else {
			$data = array(
				"branch"		=> $branch,
				"yearmonth"		=> $year.$month,
				"salestarget"	=> $salestarget,
				"marginok"	=> $marginok,
				"margingood"	=> $margingood
			);
			$branchDetails = $this->getBranchDetails($branch);
			if($this->db->insert('branchsalestarget', $data)) {
				$description = "New target added for the Branch #".$branch." - ".$branchDetails['name'];
				$this->savelog($description);
				return "success";
			} else {
				return "fail";
			}
		}
	}
	
	/* Function to check if the adding target is unique or not. This will return true, if the target is unique. */	
	public function checkTargetUnique($branch, $year, $month) {
		$yearmonth = $year.$month;
		$this->db->select('count(branch) as cnt');
        $this->db->from('branchsalestarget');
        $this->db->where('branch', $branch);
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
		$this->db->select('id, branch, yearmonth, salestarget, marginok, margingood');
		$this->db->from('branchsalestarget');
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
	
	
	/* Function to check if the branch is associated with any sales analysis or user. */
	public function BranchAssociated($branch) {
		$this->db->select('count(branch) as branches');
		$this->db->from('salesanalysis');
		$this->db->where('branch', $branch);
		$query = $this->db->get();
		$log_count = $query->row_array();	
		if(0!=intval($log_count['branches'])) {
			return true;
		} else {
			$this->db->select('count(branch) as branches');
			$this->db->from('users');
			$this->db->where('branch', $branch);
			$query = $this->db->get();
			$log_count = $query->row_array();	
			return 0!=intval($log_count['branches']);
		}
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
	
	/* Function to save the updated values of the branch target */
	public function saveUpdates($data, $id) {
		$saved = false;
		$description = $this->descriptionUpdateBranchTarget($id, $data);	
		$this->db->where('id', $id);
		if($this->db->update('branchsalestarget', $data)) {
			$saved = true;				
			$this->savelog($description);
		}
		return $saved;
	}
	
	/* Function to check if the branch id is unique */
	public function checkUniqueBranchId($branch) {
		$this->db->select('count(branch) as branches');
		$this->db->from('branch');
		$this->db->where('branch', $branch);
    $query = $this->db->get();
    return $query->row()->branches<1;		
	}
}
