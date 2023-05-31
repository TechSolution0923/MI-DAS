<?php

class Tasks_model extends Model {

	/*########################################################### 
	** Functions used before the new tables are introduces. 
	** These functions mostly uses the salesanalysis table.
	** There were other old functions as well those we deleted 
	** as they were not being used anywhere.
	*#############################################################*/
	
	public function taskDetail($taskid) {
		$this->taskListQuery($account=0, $start=0, $length=10, $search_key="", array("t.taskid"=>$taskid), $specific_order=array());
		$query = $this->db->get();
		if(!$count) {
			return $query->result_array();
		} else {
			return count($query->result_array());
		}
		
	}	

	public function taskList($count=false, $account, $start=0, $length=0, $search_key, $specific_search, $specific_order) {
		$this->taskListQuery($account, $start, $length, $search_key, $specific_search, $specific_order);
		$query = $this->db->get();
		if(!$count) {
			return $query->result_array();
		} else {
			return count($query->result_array());
		}
		
	}
	
	public function taskListQuery($account, $start, $length, $search_key, $specific_search, $specific_order) {
		$this->db->select('t.taskid as t_taskid, t.userid as t_userid, t.account as t_account, t.contactno as t_contactno, t.date as t_date, t.complete as t_complete, t.description as t_description, t.notes as t_notes, c.name as c_name, contact.title as cont_title, contact.firstname as cont_firstname, contact.surname as cont_surname');
		$this->db->from('tasks t');
		$this->db->join('customer c', 't.account = c.account', 'left');
		$this->db->join('customercontact contact', 'contact.contactno = t.contactno', 'left');
		if(sizeof($specific_search) >0) {
			$where = array();
			foreach($specific_search as $field=>$val) {
				$value = trim($val);
				$operator = "=";
				if(!empty($value)) {
					array_push($where, $field." ".$operator." ".$value);
				}

				if(sizeof($where)>0) {
					$this->db->where("(".join(" AND ", $where).")");
				}
			}
		}

		$search_key = trim($search_key);
		if(!empty($search_key)) {
			$search_key = strtolower($search_key);
			$where = array();
			array_push($where, 'LOWER(t.taskid) like "%'.$search_key.'%"');
			array_push($where, 'LOWER(contact.title) like "%'.$search_key.'%"');
			array_push($where, 'LOWER(contact.firstname) like "%'.$search_key.'%"');
			array_push($where, 'LOWER(contact.surname) like "%'.$search_key.'%"');
			array_push($where, 'LOWER(t.userid) like "%'.$search_key.'%"');
			array_push($where, 'LOWER(t.account) like "%'.$search_key.'%"');
			array_push($where, 'LOWER(t.contactno) like "%'.$search_key.'%"');
			array_push($where, 'LOWER(t.date) like "%'.$search_key.'%"');
			array_push($where, 'LOWER(t.complete) like "%'.$search_key.'%"');
			array_push($where, 'LOWER(t.description) like "%'.$search_key.'%"');
			array_push($where, 'LOWER(t.notes) like "%'.$search_key.'%"');
			array_push($where, 'LOWER(c.name) like "%'.$search_key.'%"');
			$this->db->where("(".join(" OR ", $where).")");
		}

		if(!empty($account) && "null"!=$account) {
			$this->db->where("t.account", $account);
		}

		$main_where_string = "(t.userid = '".$this->fetchUserId()."'";
		$urlSegments = explode("/",base_url());
		$domain = $urlSegments[sizeof($urlSegments)-2];
		/** This is a temporary arrangement for ATC domain as the ATC do not have the customerreps and userreps tables */
		$in = array();
		if("ATC"!=$domain) {
			$in = $this->customer_accounts_selection($this->fetchUserId());
		} else {
			$in = $this->atc_customer_select($this->fetchUserId());
		}
		if(sizeof($in)>0) {
			$main_where_string .= " OR t.account IN ";
			$main_where_string .= " (".implode(",", $in).")";
		}

		$main_where_string .= ")";

		if(count($this->session->userdata('selectedUser'))>0) {
			$userDetails = $this->session->userdata('selectedUser');
			if(intval($userDetails["userid"])>0) {
				$this->db->where($main_where_string);
			}
		}
		
		if(sizeof($specific_order) > 0) {
			$this->db->order_by($specific_order["by"], $specific_order["dir"]);
		} else {
			$this->db->order_by('t.taskid', 'ASC');
		}

		if(0!=$length) {
			$this->db->limit($length, $start);
		}
	}

	/** This is a temporary function particularly for ATC */
	public function atc_customer_select($userid) {
		$query_str = "SELECT repcode, repcode_2, repcode_3, repcode_4, repcode_5, repcode_6, repcode_7, repcode_8, repcode_9, repcode_10 FROM users WHERE userid=".$userid;

		$query = $this->db->query($query_str);

		$result = $query->result_array();
		$repcodes = array();
		foreach($result[0] as $key=>$res) {
			if(""!=trim($res)) {
				array_push($repcodes, "'".$res."'");
			}
		}

		if(!empty($repcodes)) {
			$query_str2 = "SELECT account FROM `customer` WHERE repcode IN (".implode(",", $repcodes).")";

			$query2 = $this->db->query($query_str2);

			$result2 = $query2->result_array();
			$return = array();
			foreach($result2 as $res) {
				array_push($return, "'".$res["account"]."'");
			}
		} else {
			$return = array();
		}
		
		return $return;
	}

	public function customer_accounts_selection($userid) {
		$main_where_string = " (SELECT cr.account as cr_account FROM `userreps` as ur LEFT JOIN `customerreps` as cr ON ur.repcode = cr.repcode WHERE ur.userid='".$userid."')";
		$query = $this->db->query($main_where_string);
		$result = $query->result_array();
		$return = array();
		foreach($result as $res) {
			array_push($return, "'".$res["cr_account"]."'");
		}
		return $return;
	}

	/* Function to get the user id of the selected user, if any, otherwise get the logged in user user id */
	public function fetchUserId() {
		$userId = -1;
		if(count($this->session->userdata('selectedUser')) > 0){		
			$UserSes = $this->session->userdata('selectedUser');		
			$userId = $UserSes["userid"];		
		}

		if(intval($userId) > 0) {		
		} else {
			$userId = $this->session->userdata('userid');
		}
		return intval($userId);
	}
	
	/* Function to create a file to be exported in CSV or EXCEL */
	public function csv_export($search, $search_key, $specific_search_keys, $account=NULL) {
		$specific_search = $this->makeSpecificSearch($search, $specific_search_keys);
		if("nosearchedvalue"==$search_key) {
			$search_key = "";
		}
		$start = 0;
		$length = 0;
		$specific_order = array();
		$this->taskListQuery($account, $start, $length, $search_key, $specific_search, $specific_order);
		
		$query = $this->db->get();
		$this->load->dbutil();
		$opt=$this->dbutil->csv_from_result($query);
		
		echo str_replace(
			array("t_taskid","t_userid","t_account","t_contactno","t_date","t_complete","t_description","t_notes","c_name"),
			array("Task ID","User ID","Account","Contact Number","Date","Completed","Task Description","Notes","Customer Name"),
			$opt);
	}

	/* Function to make the specific search array as required */
	public function makeSpecificSearch($search, $specific_search_keys) {
		
		$specific_search = array(); 
		foreach($search as $k=>$s) {
			if($k) {
				if("nosearchedvalue"==$s) {
					$specific_search[$specific_search_keys[($k-1)]] = "";
				} else {
					$specific_search[$specific_search_keys[($k-1)]] = $s;
				}
			}
		}
		return $specific_search;
	}

	public function addTask() {
		$taskid = null;
		if(0==intval($_POST["taskid"])) {
			$taskid = null;
		} else {
			$taskid = intval($_POST["taskid"]);
		}
		if(null==$taskid) {
			$array = array(
				'taskid' => null,
				'userid' => $this->session->userdata('userid'),
				'account' => $_POST["account"],
				'contactno' => $_POST["contactno"],
				'date' => $_POST["date"],
				'complete' => ("true"==$_POST["completed"]?1:0),
				'description' => stripcslashes($_POST["description"]),
				'notes' => stripcslashes($_POST["notes"])
			);
			$this->db->insert('tasks', $array);
			return $this->db->insert_id();
		} else {
			$array = array(
				'account' => $_POST["account"],
				'contactno' => $_POST["contactno"],
				'date' => $_POST["date"],
				'complete' => ("true"==$_POST["completed"]?1:0),
				'description' => stripcslashes($_POST["description"]),
				'notes' => $_POST["notes"]
			);
			$this->db->where('taskid', $taskid);
			$this->db->update('tasks', $array);
			return $taskid;
		}
		
		echo json_encode($array);
		
	}

	public function deleteRecord($taskid) {
		if($this->db->delete("tasks", array("taskid"=>$taskid))) {
			return true;
		} else {
			return false;
		}
	}

	public function overdueTask($account="") {
		$this->taskListQuery($account, $start=0, $length=0, $search_key="", $specific_search=array(), $specific_order=array());
		$date = date("Y-m-d");
		$this->db->where("t.date < ", $date);
		$this->db->where("t.complete", 0);
		$query = $this->db->get();
		return $query->result_array();
	}


	public function overdueTaskList($count=false, $account, $start=0, $length=0, $search_key, $specific_search, $specific_order) {
		$date = date("Y-m-d");
		$this->taskListQuery($account, $start, $length, $search_key, $specific_search, $specific_order);
		$this->db->where("t.date < ", $date);
		$this->db->where("t.complete", 0);
		$query = $this->db->get();
		if(!$count) {
			return $query->result_array();
		} else {
			return count($query->result_array());
		}
	}

	public function updateCompletedState() {
		$array = array('complete' => "false"!=$this->input->post("complete")?1:0);
		$this->db->where('taskid', $this->input->post("taskid"));
		$this->db->update('tasks', $array);
		return array(
			"taskid"=>$this->input->post("taskid"), 
			"complete"=>$this->input->post("complete")
		);
	}

	public function allAccounts() {
		$this->db->select("*");
		$this->db->from('customer');
		$query = $this->db->get();
		$faetched_array = $query->result_array();
		$return_array = array();
		$i = 0;
		foreach($faetched_array as $key=>$value){
			$return_array[$i]["value"] = $value["account"];
			$return_array[$i]["label"] = "(".$value["account"].") - ".$value["name"];
			$return_array[$i]["desc"] = "email:".$value["email1"]."|phone:".$value["phone"]."|address1:".$value["address1"]."|address2:".$value["address2"]."|address3:".$value["address3"]."|address4:".$value["address4"]."|address5:".$value["address5"]."|postcode:".$value["postcode"]."|fax:".$value["fax"];
			$i++;
		}
		return $return_array;
	}

	public function fetchContacts($account) {
		$ZeroOption = array(
            "value" => "",
            "title" => "",
            "firstname" => "",
            "surname" => ""
		);
		$return_array_with_vlank = array();
		$this->db->select("contactno as value, title, firstname, surname");
		$this->db->from('customercontact');
		if(!empty($account)) {
			$this->db->where("account", $account);
		}
		$query = $this->db->get();
		$return_array = $query->result_array();
		array_push($return_array_with_vlank, $ZeroOption);
		foreach($return_array as $ra) {
			array_push($return_array_with_vlank, $ra);
		}
		return sizeof($return_array_with_vlank)>1?$return_array_with_vlank:array();
	}

	public function addCustomerRepcode()
	{
		$account = base64_decode($this->input->post("account"));
		$repcode = $this->input->post("repcode");

		$this->db->select("COUNT(account) as acc");
		$this->db->from("customerreps");
		$this->db->where("account", $account);
		$this->db->where("repcode", $repcode);

		$query = $this->db->get();
		$search_result = $query->result_array();

		if (intval($search_result[0]['acc']) != 0)
		{
			return array
			(
				'success' => false,
				'message' => "Repcode ".$repcode." already exists with the customer account.",
			);
		}
		else
		{
			$data = array
			(
				'account' => $account,
				'repcode' => $repcode,
			);

			$success = $this->db->insert("customerreps", $data);

			return array
			(
				'success' => $success,
				'message' => "Repcode ".$repcode." added successfully.",
			);
		}
	}

	public function addUserRepcode() {
		$userid = $this->input->post("userid");
		$repcode = $this->input->post("repcode");
		$this->db->select("COUNT(userid) as acc");
		$this->db->from("userreps");
		$this->db->where("userid", $userid);
		$this->db->where("repcode", $repcode);
		$query = $this->db->get();
		$search_result = $query->result_array();
		if(intval($search_result[0]["acc"])!=0) {
			return array("success"=>false, "message"=>"Repcode ".$repcode." already exists with the user account.");
		} else {
			$data = array(
				"userid"=>$userid,
				"repcode"=>$repcode
			);
			$success = $this->db->insert("userreps", $data);
			return array("success"=>$success, "message"=>"Repcode ".$repcode." added successfully.");
		}
	}

	public function fetchSalesrep() {
		$this->db->select("repcode, name");
		$this->db->from("salesrep");
		$query = $this->db->get();
		return $search_result = $query->result_array();
	}
		
}
