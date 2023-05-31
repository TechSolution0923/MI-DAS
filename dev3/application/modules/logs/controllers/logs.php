<?php
class Logs extends Controller {
	
	function __construct() {
		parent::Controller();
		$this->load->model('logs_model'); 
		$this->load->model('site/site_model'); 
	}
	
    /* Function to list the logs */
	
    function index() {
	    if($this->site_model->is_logged_in()==false){
			redirect('/');
		}
		setcookie($this->config->item('site_name').'_'.$this->session->userdata('userid').'_last_visited', current_url(), time() + (86400 * 365), "/"); // 86400 = 1 day
		$data['logs'] = $this->logs_model->getLogList();
		$data['main_content']='logs_list';
		$this->load->view('user/front_template', $data);
	}
	
	 /**
     * Uesr edit form
     *
     * @author		Virtual Employee PVT. LTD.
     * @Descrption	Display user edit form on the basis of user id
     * @Created Date     16-03-2016
     * @Updated Date
     */
	
    function detail($id) {
		$this->load->helper(array('form', 'url'));
		
	    if($this->site_model->is_logged_in()==false){
			redirect('/');
		}
		
		$data['main_content']='log_detail';
		$data['log'] = $this->logs_model->getLogDetail($id);
		$this->load->view('user/front_template', $data);
	}
	 function export_csv()
	 {

header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=logs_export.csv");
		$csvOutput = $this->logs_model->csv_export($data['userDetail']['repwhere'], $search_key);
		echo $csvOutput;
		exit();

	 }
}
?>
