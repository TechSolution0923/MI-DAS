<?php
class company extends Controller {
	public $isAdmin;
	function __construct() {
		parent::Controller();
		$this->load->model('company_model');		
		$this->load->model('users/users_model');		
		$this->load->model('site/site_model');  
		$loggedInUserDetails = $this->users_model->getUserDetails($this->session->userdata('userid'));
		$this->isAdmin = $loggedInUserDetails['administrator'];
	}
	
     /**
     * Uesrs list
     *
     * @author		Virtual Employee PVT. LTD.
     * @Descrption	Return user list
     * @Created Date     16-03-2016
     * @Updated Date
     */
	
	function index() {		
	    if($this->site_model->is_logged_in()==false){
			redirect('/');
		}
		setcookie($this->config->item('site_name').'_'.$this->session->userdata('userid').'_last_visited', current_url(), time() + (86400 * 365), "/"); // 86400 = 1 day
		$data['activeUserList'] = $this->users_model->getActiveUsersList();
		
		$data['isAdmin'] = $this->isAdmin;
		$data['companyDetail'] = $this->company_model->getcompanyList($this->isAdmin);
		$data['main_content']='company_list';
		$this->load->view('company/front_template', $data);
	}
	
  /* Branch edit and Details */	
 /* function details($company="", $type="") {
//echo '<pre>'; print_r($this->session->all_userdata());exit;
 	$user_id=$this->session->userdata('userid'); 


 	 $company_details = $this->company_model->checkUser($user_id);
 	 $data=$company_details;

 	$data["user_id"]=$user_id;
		$this->output->cache(0);
		$data['isAdmin'] = $this->isAdmin;
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	

		$submit = $this->input->post('submit');
		
		if(!empty($submit)) {

				
				$posted_data = array(
					'userid'=>$this->input->post('userid'),
					'subtype'=>$this->input->post('subtype'),
					'name'=>$this->input->post('company_name'),
					'userid'=>$data["user_id"],
					'kpithreshold1' => $this->input->post('kpithreshhold1'),
					'kpithreshold2' => $this->input->post('kpithreshhold2'),
					'marginok' => $this->input->post('marginok'),
					'margingood' => $this->input->post('margingood')
				);				
				
				$message = '';
				$message = $this->company_model->companyModify($posted_data);

				 $company_data=$this->company_model->checkUser($user_id);

				 $data=$company_data;
				
				$data["message"]=$message;
			
		}

		$data['main_content']='company_detail';
		$this->load->view('company/front_template', $data);
	} */

	
	/* Function to update the inline editing values */

	function details($company="", $type="") {
//echo '<pre>'; print_r($this->session->all_userdata());exit;
 	$user_id=$this->session->userdata('userid'); 


 	 $company_details = $this->company_model->checkUser($user_id);
 	 $data=$company_details;

 	$data["user_id"]=$user_id;
		$this->output->cache(0);
		$data['isAdmin'] = $this->isAdmin;
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	

		$submit = $this->input->post('submit');
		
		if(!empty($submit)) {

				
				$posted_data = array(
					'userid'=>$this->input->post('userid'),
					'subtype'=>$this->input->post('subtype'),
					'name'=>$this->input->post('company_name'),
					'userid'=>$data["user_id"],
					'kpithreshold1' => $this->input->post('kpithreshhold1'),
					'kpithreshold2' => $this->input->post('kpithreshhold2'),
					'marginok' => $this->input->post('marginok'),
					'margingood' => $this->input->post('margingood')
				);				
				
				$message = '';
				$message = $this->company_model->companyModify($posted_data);

				 $company_data=$this->company_model->checkUser($user_id);

				 $data=$company_data;
				
				$data["message"]=$message;
			
		}

		$data['main_content']='company_detail';
		$this->load->view('company/front_template', $data);
	} 
	public function inlineupdate() {
		header('Content-type: application/json');
		$yearmonth = $this->input->post('yearmonth');
		$id = $this->input->post('id');
		if($yearmonth=='true') {
			$data['yearmonth'] = $this->input->post('year').str_pad($this->input->post("month"), 2, "0", STR_PAD_LEFT);
		} else {
			$fieldname = $this->input->post('fieldname');
			$data[$fieldname] = $this->input->post('value');
		}
		$saved = $this->company_model->saveUpdates($data, $id);
		echo (json_encode(array("success"=>$saved)));
		exit;
	}
	
	/* Function to move a file from one path to the other */
	public function moveTempFile($parent_directory, $userid, $updateimage) {
		$fileOriginalName = explode(".", $updateimage);
		$fileExtension = $fileOriginalName[count($fileOriginalName)-1];
		$oldname = $parent_directory . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $updateimage;
		
		$upload_path = $parent_directory . DIRECTORY_SEPARATOR . 'profile_images' . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR . $userid;
		
		$newname = $upload_path . DIRECTORY_SEPARATOR . $userid.".".$fileExtension;
		
		if (!is_dir($upload_path)) {
			if(!mkdir($upload_path, 0777, TRUE)) {
				die("can not create the directory");
			}
		}
		
		return rename($oldname, $newname);
	}
	
	/* Functionality to upload profile picture */
	public function uploadProfilePicture($files, $parent_directory, $max_size = '500', $max_width = '2048', $max_height = '2048', $userid=0) {
		$message = '';
		$success = 0;
		$timestamp = time();
		$fileOriginalName = explode(".", $files['name']);
		$fileExtension = $fileOriginalName[count($fileOriginalName)-1];
		$tempFileName = $timestamp.".".$fileExtension;
		if(""!=trim($files['tmp_name'])) {
		//	$config['upload_path'] = $parent_directory. 'profile_images' . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR . $userid;
			$config['upload_path'] = $parent_directory. 'temp';
		
			if (!is_dir($config['upload_path'])) {
				if(!mkdir($config['upload_path'], 0777, TRUE)) {
					return array("success"=>0, "message"=>"Can not create folders", "profilepath"=>generate_profile_image_url($userid), "filename"=>"");
				}
			}
			
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= $max_size;
			$config['max_width']  = $max_width;
			$config['max_height']  = $max_height;
			$config['overwrite']  = true;
			$config['file_name']  = $timestamp;

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload()) {
				$error = array('error' => $this->upload->display_errors());
				$message .= $error['error'].". Upload path : ".$config['upload_path'];
				$success = 0;
			} else {
				$data = array('upload_data' => $this->upload->data());
				$message .= ' Profile image also uploaded successfully.';
				$success = 1;				
			//	$this->deleteOtherProfileImages($config['upload_path'], $fileExtension, $userid);
			}
		}		
		
		return array("success"=>$success, "message"=>$message, "profilepath"=>generate_temp_image_url($tempFileName), "filename"=>$tempFileName);
	}
	
	/* Function to delete all the other files saving the one being just uploaded. */
	public function deleteOtherProfileImages($path, $ext, $filename) {
		$this->load->helper('file');
		$filenames = get_filenames($path);
		foreach($filenames as $key=>$name) {
			if($name!=$filename.".".$ext) {
				unlink($path . DIRECTORY_SEPARATOR . $name);
			}
		}
	} 
	
	/* Function to delete all the profile files before copying the new one from temp folder. */
	public function deleteAllProfileImages($path) {
		$this->load->helper('file');
		$filenames = get_filenames($path);
		$return  = true;
		foreach($filenames as $key=>$name) {
			if(!unlink($path . DIRECTORY_SEPARATOR . $name)) {
				$return = false;
			}
		}
		return $return;
	} 
	
	/* Function to delete a branch on the basis of the branch id. Branch can not be deleted if salesanalysis.branch or user.branch is associated with it. */
	
	public function delete ($company) {
		$logged_in_user_id = $this->session->userdata('userid');
		
		if(!$this->isAdmin) {
			$this->session->set_flashdata('company_operation', '<div class="alert alert-danger">You can not delete a branch, as you are not administrator.</div>');
			redirect('company/index');
		}
		
		if(!$this->company_model->BranchAssociated($company)) {
			$this->company_model->deleteBranch($company);
			$this->session->set_flashdata('company_operation', '<div class="alert alert-success">The branch was successfully deleted!</div>');
		} else {
			$this->session->set_flashdata('company_operation', '<div class="alert alert-danger">The branch cannot be deleted as it is associated with sales analysis or user.</div>');
			redirect('company/index');
		}
		
		redirect('company/index');		
	}
	
	/* Function to delete a target of an user on the basis of the target id. */
	public function deletetarget($id) {	
		header('Content-type: application/json');
		$company_target_details = $this->company_model->getTargetDetailsById($id);
		$delete = $this->company_model->deleteBranchTarget($id);
		echo json_encode(array("deleteresult"=>$delete));exit;
	}
	
	/* Function that returns true if the email is unique */
	public function isUnique($email, $userid="") {
		header('Content-type: application/json');
		$val = $this->users_model->checkUnique($email, $userid);
		echo json_encode(array("unique"=>$val));exit;
	}
	
	/* Function to save branch name on branch id */
	public function savebranchname() {
		header('Content-type: application/json');
		$id = $this->input->post("id");
		$name = $this->input->post("name");
		if($this->company_model->updateBranchnameByBranchId($id, $name)) {
			echo json_encode(array("value"=>$name));
		} else {
			echo json_encode(array("value"=>"notsaved"));
		}
	}
	
	/* Function to save firstname on userid */
	public function savefirstname() {
		header('Content-type: application/json');
		$userid = $this->input->post("id");
		$firstname = $this->input->post("firstname");
		if($this->users_model->updateFirstnameByUserId($userid, $firstname)) {
			echo json_encode(array("value"=>$firstname));
		} else {
			echo json_encode(array("value"=>"notsaved"));
		}
	}
	
	/* Function to save email on userid */
	public function saveemail() {
		header('Content-type: application/json');
		$userid = $this->input->post("id");
		$email = $this->input->post("email");
		$val = $this->users_model->checkUnique($email, $userid);
		if(!$val) {
			echo json_encode(array("value"=>"notunique"));
		} else {
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			if ($this->form_validation->run() == FALSE) {
				echo json_encode(array("value"=>"wrongemail"));
			} else {
				if($this->users_model->updateEmailByUserId($userid, $email)) {
					echo json_encode(array("value"=>$email));
				} else {
					echo json_encode(array("value"=>"notsaved"));
				}
			}			
		}
	}
	
	/* Function to add a new target for an user */
	public function addtarget() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('company', 'company', 'required');
		$this->form_validation->set_rules('year', 'Year', 'required');
		$this->form_validation->set_rules('month', 'Month', 'required');
		$this->form_validation->set_rules('salestarget', 'Sales target', 'required');
		$this->form_validation->set_rules('marginok', 'Margin OK', 'required');
		$this->form_validation->set_rules('margingood', 'Margin Good', 'required');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('company_operation', '<div class="alert alert-danger">The target could not be added!</div>');	
		} else {
			$company = $this->input->post("branch");
			$year = $this->input->post("year");
			$month = str_pad($this->input->post("month"), 2, "0", STR_PAD_LEFT);
			$salestarget = $this->input->post("salestarget");
			$marginok = $this->input->post("marginok");
			$margingood = $this->input->post("margingood");
			
			$result = $this->company_model->addUniqueTarget($company, $year, $month, $salestarget, $marginok, $margingood);
			
			if($result=="success") {
				$this->session->set_flashdata('company_operation', '<div class="alert alert-success">The target added successfully!</div>');	
			}
			
			if($result=="duplicate") {
				$this->session->set_flashdata('company_operation', '<div class="alert alert-danger">Target for '.$year.$month.' already exists!</div>');	
			}
			
			if($result=="fail") {
				$this->session->set_flashdata('company_operation', '<div class="alert alert-danger">Target could not be added!</div>');	
			}
			
		}
		redirect('company/details/'.$company.'/#target');	
	}





	// Add KPI  ///

	public function kpimodify() {
		$this->load->library('form_validation');
			$company = $this->input->post("branch");
			$this->form_validation->set_rules('company', 'company', 'required');
		$this->form_validation->set_rules('kpithreshold1', 'Kpithreshold1', 'required');
		$this->form_validation->set_rules('kpithreshold2', 'kpithreshold2', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('kpi_operation', '<div class="alert alert-danger">The KPI could not be added!</div>');	
		} else {
				$company = $this->input->post("branch");
			$kpithreshold1 = $this->input->post("kpithreshold1");
			$kpithreshold2 = $this->input->post("kpithreshold2");
		if($kpithreshold1 > $kpithreshold2)
		{
$result="error_greater";

		}
	else{

$result = $this->company_model->updatekpi($company,$kpithreshold1,$kpithreshold2);
			

	}	if($result=="error_greater") {
				$this->session->set_flashdata('kpi_operation', '<div class="alert alert-danger">kpi threshold2 must be greater then kpi threshold1</div>');	
			}
			
			
			if($result=="success") {
				$this->session->set_flashdata('kpi_operation', '<div class="alert alert-success">The KPI added successfully!</div>');	
			}
			
			if($result=="duplicate") {
				$this->session->set_flashdata('kpi_operation', '<div class="alert alert-danger">Target for '.$year.$month.' already exists!</div>');	
			}
			
			if($result=="fail") {
				$this->session->set_flashdata('kpi_operation', '<div class="alert alert-danger">KPI could not be added!</div>');	
			}
			
		}
		redirect('company/details/'.$company.'/#target');	
	}
	/// End ADD KPI ///
	
	/* Function to update the Year/Month value of a target with target id as id */
	function updateyearmonth() {
		header('Content-type: application/json');
		$yearmonth = $this->input->post("yearmonth");
		$year = substr($yearmonth, 0, 4);
		$month = substr($yearmonth, 4, 2);
		if($month<10) {
			$month = "0".$month;
		}
		$yearmonth = $year.$month;
		$id = $this->input->post("id");
		$userid = $this->users_model->getUserIdByTargetId($id);
		if(!$this->users_model->checkTargetUnique($userid, $year, $month)) {
			echo json_encode(array("value"=>"duplicate"));
		} else {
			if($this->users_model->updateYearMonth($id, $yearmonth)) {
				echo json_encode(array("value"=>"success"));
			} else {
				echo json_encode(array("value"=>"notsaved"));
			}
		}
		exit;
	}
	
	/* Function to update the sales target value of a target with target id as id */
	function updatesalestarget() {
		header('Content-type: application/json');
		$salestarget = $this->input->post("salestarget");
		$id = $this->input->post("id");
		if($this->users_model->updateSalesTarget($id, $salestarget)) {
			echo json_encode(array("value"=>"success"));
		} else {
			echo json_encode(array("value"=>"notsaved"));
		}
		exit;
	}	
	
	/* Function that checks if the limits for the license is crossed. */
	public function license() {
		header('Content-type: application/json');
		$limits_crossed = $this->users_model->licenseLimitCrossed();
		echo json_encode(array("limits_crossed"=>$limits_crossed));exit;
	}
	
	
	/* Function to set the session of the current selected user */
	public function set_selected_user_session($userid) {
		if(0!=$userid) {
			$userDetail = $this->users_model->getUserDetails($userid);
			if("B"==$userDetail['usertype']) { 
				$companyname = $this->site_model->getBranch($userDetail['company']);
				$data = array(
					"selectedBranch" => array(
						"branchno" => $userDetail['company'],
						"name" => $companyname
					),
					"selectedUser" => array(
						"userid" => $userDetail['userid'],
						"firstname" =>  $userDetail['firstname'],
						"surname" =>  $userDetail['surname']
					)
				);
			} else {
				$data = array(
					"selectedUser" => array(
						"userid" => $userDetail['userid'],
						"firstname" =>  $userDetail['firstname'],
						"surname" =>  $userDetail['surname']
					)
				);
				$this->session->unset_userdata('selectedBranch');
			}
		} else {
			$data = array(
				"selectedUser" => array(
					"userid" => 0,
					"firstname" =>  "All",
					"surname" =>  "Users"
				)
			);
			$this->session->unset_userdata('selectedBranch');
		}
		
		$this->session->set_userdata($data);
		
		redirect('dashboard');
	}
	
	/* Function to fetch all the users */
	public function getAllUsers() {
		header('Content-Type: application/json');
		$users = $this->users_model->getUsersListDropDown();
		$userid = $this->session->userdata("userid");
		$userDetail = $this->users_model->getUserDetails($userid);
		$usertype = $userDetail['usertype'];
		$selectedUser = $this->session->userdata("selectedUser");
		if(empty($selectedUser) || '0'==$selectedUser['userid']) {
			$selectedUser['userid'] = '0';
			$selectedUser['firstname'] = 'All';
			$selectedUser['surname'] = 'Users';
		}
		
		$lis[0] = "<li><a href='".site_url("users/set_selected_user_session/0")."'>All Users</a></li>";
		$listIndex = 1;
		foreach($users as $user) {
			$lis[$listIndex] = "<li><a href='".site_url("users/set_selected_user_session/".$user['userid'])."'>".$user['firstname']." ".$user['surname']."</a></li>";
			$listIndex++;
		}
		$li = implode("", $lis);
		echo json_encode(array('users'=>$li, 'selectedUser'=>$selectedUser, 'usertype'=>$usertype));exit;
	}
	
	/* Function to display the upload image as a profile image */
	public function uploadprofileimage($userid=0) {
		header('Content-Type: application/json');
		$parent_directory = APPPATH . 'modules' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;	
		$max_size	= '500';
		$max_width  = '2048';
		$max_height  = '2048';
		$uploadProfilePicture = $this->uploadProfilePicture($_FILES['userfile'], $parent_directory, $max_size, $max_width, $max_height, $userid);
		
		echo json_encode($uploadProfilePicture);exit;
	}
	
	/* Function to check if the branch id is unique or not.*/
	public function checkuniquebranchid() {
		header('Content-Type: application/json');
		$company_id = $this->input->post("branch");		
		if(intval($company_id)<1) {
			echo json_encode(array("unique"=>"0notpossible"));exit;
		} else {
			$result = $this->company_model->checkUniqueBranchId($company_id);
			echo json_encode(array("unique"=>$result));exit;
		}
		
	}
}
?>
