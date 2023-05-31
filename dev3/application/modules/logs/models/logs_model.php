<?php

class Logs_model extends Model {
	
	/* Fetch all the logs in the system. */
    public function getLogList(){
		$this->db->select('id, systemlog.userid, users.firstname as firstname, users.surname as surname, type, date, time, description');		
        $this->db->from('systemlog');
        $this->db->join('users', 'users.userid=systemlog.userid', 'left');
        $this->db->order_by('time', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
	}
	
	/* Fetch the detail for the log with id as $id. */
    public function getLogDetail($id){
		$this->db->select('id, systemlog.userid, users.firstname as firstname, users.surname as surname, type, date, time, description');		
        $this->db->from('systemlog');
        $this->db->join('users', 'users.userid=systemlog.userid', 'left');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
	}

    public function csv_export() {
       $this->db->select("CONCAT((systemlog.userid),('-'),(users.firstname),(' '),(users.surname) ) as User, type as Type,time as Time,description as Description");        
        $this->db->from("systemlog");
        $this->db->join("users", "users.userid=systemlog.userid", "left");
        $this->db->order_by("date", "DESC");
        $this->db->order_by("time", "DESC");
        $query = $this->db->get();
        $this->load->dbutil();
    
        $opt= $this->dbutil->csv_from_result($query);


echo $opt;
    }
}
