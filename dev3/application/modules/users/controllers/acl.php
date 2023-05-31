<?php
class Acls extends Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('users/users_model');
	}
	
     /* Function for Access control list. This function restricts normal user to see the system logs
	 */
	
    function index() {
		$userid = $this->session->userdata('userid');
		if(!empty($userid)) {		
			$module = $this->router->fetch_module();
			$class = $this->router->fetch_class();
			$method = $this->router->fetch_method();
			$userDetails = $this->users_model->getUserDetails($this->session->userdata('userid'));
			$userIsNotAdmin = 0==intval($userDetails['administrator']);
			$admin_module_array = array("logs");
			$admin_class_array = array("logs");
			$admin_function_array = array();		
			if($userIsNotAdmin) {
				if(!in_array($module, $admin_module_array) && !in_array($class, $admin_class_array)){	
				} else {
					echo "<div style='background-color:red; color:white;padding:10px;'>This module is restricted to Administrators only. You will now be redirected to the dashboard!.</div><script>setTimeout(function(){ window.location='".base_url()."' }, 3000);</script>";exit;
				}
			} else {
			}
		}
	}
}	