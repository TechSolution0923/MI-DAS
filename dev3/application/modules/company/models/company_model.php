<?php

class company_model extends Model {
	
	/* Fetch all the company in the system. */
  public function getcompanyList($isAdmin=true){		
		$this->db->select('id, name');
		$this->db->from('company');
		$this->db->order_by('name', 'ASC');
    $query = $this->db->get();
    return $query->result_array();
	}
    
	/* Fetch the company details with the company id */
    public function getcompanyDetails($company) {	
		$this->db->select('id, name');
		$this->db->from('company');
		$this->db->where('id', $company);
		$query = $this->db->get();
		return $query->row_array();		
	}
	
	/* Fetch the company targets with the company id */
    public function getcompanyTargets($company) {		
		$this->db->select('id, company, yearmonth, salestarget, marginok, margingood');
		$this->db->from('companysalestarget');
		$this->db->where('company', $company);
		$query = $this->db->get();
		return $query->result_array();		
	}
	
	/* Fetch the company target by target id */
    public function getcompanyTargetDetails($targetId) {		
		$this->db->select('id, company, yearmonth, salestarget, marginok, margingood');
		$this->db->from('companysalestarget');
		$this->db->where('id', $targetId);
		$query = $this->db->get();
		return $query->row_array();		
	}
	
	/* Add new company and update old company */

public function checkUser($id)
{



		$this->db->select('*');
        $this->db->from('company');
        $this->db->where('userid ',$id);
        $query = $this->db->get();
        //$str = $query->row_array();
        $count= $query->num_rows();
            if($count!=0)
        {

        	$return =$query->row_array();
        	$return["subtype"]="update";
        }
        else{

        	$return["subtype"] ="add";
        }



        return $return;

}

	public function companyModify($data) {

	
		 $type = $data['subtype'];

if($type=="add")
{
$data_sub = array(
   'name' => $data["name"] ,
   'kpithreshold1' => $data["kpithreshold1"] ,
   'kpithreshold2' => $data["kpithreshold2"],
   'marginok' => $data["marginok"] ,
   'margingood' => $data["margingood"],
    'userid' => $data["userid"]
);
$this->db->insert('company', $data_sub); 
$return="Data Added Successfully";

}
else{

$data_sub = array(
   'name' => $data["name"] ,
   'kpithreshold1' => $data["kpithreshold1"] ,
   'kpithreshold2' => $data["kpithreshold2"],
   'marginok' => $data["marginok"] ,
   'margingood' => $data["margingood"],
    'userid' => $data["userid"]
);

$this->db->where('id', 1);
$this->db->update('company', $data_sub); 
 $sql = $this->db->last_query();
 
$return="Data updated Successfully";

}	
		return $return;
	}
	

// KPI start

	public function updatekpi($company,$kpithreshold1,$kpithreshold2) {
	
		if(isset($company) && 0!=intval($company)) {
			$value=array('kpithreshold1'=>$kpithreshold1,'kpithreshold2'=>$kpithreshold2);
			$this->db->where('company', $company);
			if($this->db->update('company', $value)) {
			$result="success";
			} 	
			else{


				$result="fail";
			}


		} 
		return $result;
	}
// End KPI start

	/* Check if the company id is unique. */
	public function checkUniqueID($company) {		
		$this->db->select('count(id) as cnt');
    $this->db->from('company');
    $this->db->where('id', $company);
    $query = $this->db->get();
    $result = $query->row_array();
		return intval($result['cnt'])==0;
	}
	
	/* Check if the company name is unique. */
	public function checkUniqueName($name) {		
		$this->db->select('count(id) as cnt');
    $this->db->from('company');
    $this->db->where('name', $name);
    $query = $this->db->get();
    $result = $query->row_array();
		return intval($result['cnt'])==0;
	}
	
	/* Function to delete the company with it's id */
	public function deletecompany($company) {
		return $this->db->delete('company', array('id' => $company)); 
	}
	
	/* Function to delete a target with it's id */
	public function deletecompanyTarget($id) {
		return $this->db->delete('companysalestarget', array('id' => $id)); 
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
	
	/* Function to create the description for update company target */
	public function descriptionUpdatecompanyTarget($targetId, $data) {
		$companyTargetDetail = $this->getcompanyTargetDetails($targetId);
		$description = "company target #".$companyTargetDetail['id']." for ".$companyTargetDetail['yearmonth']." is changed.";
		$changes = array();
		foreach($data as $key=>$value) {
			if($companyTargetDetail[$key]!=$value) {
				$changes[] = " ".ucfirst($key)." changed from [".$companyTargetDetail[$key]."] to [".$value."]";
			}
		}
		$allChanges = implode("; ", $changes);
		$description .= $allChanges;
		return $description;
	}
	
	/* Function to create the description for update company */
	public function descriptionUpdatecompany($companyDetail, $data) {
		$description = "company #".$companyDetail['company']." - ".$companyDetail['name']." changed.";
		$changes = array();
		unset($data["operation"]);
		foreach($data as $key=>$value) {
			if($companyDetail[$key]!=$value) {
				$changes[] = " ".ucfirst($key)." changed from [".$companyDetail[$key]."] to [".$value."]";
			}
		}
		$allChanges = implode("; ", $changes);
		$description .= $allChanges;
		return $description;
	}
	
	
	/* Function to create the description for add company */
	public function descriptionAddcompany($data) {
		$description = "New company #".$data['company']." - ".$data['name']." created.";
		return $description;
	}
	
	/* Function to update company name by company id */
	public function updatecompanynameBycompanyId($id, $name) {
		$data["name"] = $name;
		$companyDetail = $this->getcompanyDetails($id);
		$this->db->where('company', $id);
		$return = $this->db->update('company', $data);	
		if($return) {
			$description = $this->descriptionUpdatecompany($companyDetail, array("name"=>$name, "operation"=>"edit"));					
			$this->savelog($description, 'S');
		}
		return $return;
	}
		
	/* Add unique target for a company */
	public function addUniqueTarget($company, $year, $month, $salestarget, $marginok, $margingood) {
		if(!$this->checkTargetUnique($company, $year, $month)) {
			return "duplicate";
		} else {
			$data = array(
				"company"		=> $company,
				"yearmonth"		=> $year.$month,
				"salestarget"	=> $salestarget,
				"marginok"	=> $marginok,
				"margingood"	=> $margingood
			);
			$companyDetails = $this->getcompanyDetails($company);
			if($this->db->insert('companysalestarget', $data)) {
				$description = "New target added for the company #".$company." - ".$companyDetails['name'];
				$this->savelog($description);
				return "success";
			} else {
				return "fail";
			}
		}
	}
	
	/* Function to check if the adding target is unique or not. This will return true, if the target is unique. */	
	public function checkTargetUnique($company, $year, $month) {
		$yearmonth = $year.$month;
		$this->db->select('count(company) as cnt');
        $this->db->from('companysalestarget');
        $this->db->where('company', $company);
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
		$this->db->select('id, company, yearmonth, salestarget, marginok, margingood');
		$this->db->from('companysalestarget');
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
	
	
	/* Function to check if the company is associated with any sales analysis or user. */
	public function companyAssociated($company) {
		$this->db->select('count(company) as company');
		$this->db->from('salesanalysis');
		$this->db->where('company', $company);
		$query = $this->db->get();
		$log_count = $query->row_array();	
		if(0!=intval($log_count['company'])) {
			return true;
		} else {
			$this->db->select('count(company) as company');
			$this->db->from('users');
			$this->db->where('company', $company);
			$query = $this->db->get();
			$log_count = $query->row_array();	
			return 0!=intval($log_count['company']);
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
	
	/* Function to save the updated values of the company target */
	public function saveUpdates($data, $id) {
		$saved = false;
		$description = $this->descriptionUpdatecompanyTarget($id, $data);	
		$this->db->where('id', $id);
		if($this->db->update('companysalestarget', $data)) {
			$saved = true;				
			$this->savelog($description);
		}
		return $saved;
	}
	
	/* Function to check if the company id is unique */
	public function checkUniquecompanyId($company) {
		$this->db->select('count(id) as company');
		$this->db->from('company');
		$this->db->where('id', $company);
    $query = $this->db->get();
    return $query->row()->company<1;		
	}
}
