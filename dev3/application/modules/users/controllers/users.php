<?php
class Users extends Controller {
	public $isAdmin;
	function __construct() {
		parent::Controller();
		$this->load->model('users_model');		
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
		setcookie($this->session->userdata('userid').'_last_visited', current_url(), time() + (86400 * 365), "/"); // 86400 = 1 day
		$data['isAdmin'] = $this->isAdmin;
		$data['usersDetail'] = $this->users_model->getUsersList($this->isAdmin);
		$data['main_content']='users_list';
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

  function details($userid="", $type="") {
		$this->output->cache(0);
		$data['isAdmin'] = $this->isAdmin;
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		$max_size	= '500';
		$max_width  = '2048';
		$max_height  = '2048';
		$data['max_size'] = $max_size;			
		$data['max_width'] = $max_width;			
		$data['max_height'] = $max_height;	
		
		$parent_directory = APPPATH . 'modules' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;		
		
		$image = '';
		
	    if($this->site_model->is_logged_in()==false){
			redirect('/');
		}
		if(""!=$userid) {			
		} else {			
			$userid = $this->input->post('userid');
		}
		
		$this->load->helper('file');
		$sub_directory = $parent_directory. 'profile_images' . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR;
		$upload_path = $sub_directory . $userid;
		$filenames = get_filenames($upload_path);
		$image = $filenames[0];
			
		$data['image'] = $image;
		if(""!=$type) {
			$data['type'] = $type;
		} else {
			$data['type'] = $this->input->post('type');
		}
		
		/* A non system administration user should only be able to see/edit their own user account on the user list */
		$loggedinuser = $this->session->userdata("userid");
		$mainUserDetail = $this->users_model->getUserDetails($loggedinuser);
		$data['mainUserEdirAccess']=$mainUserDetail['edittargets'];
		if(!$this->isAdmin && $userid!=$loggedinuser) {
			$data['type'] = "view";
			$this->session->set_flashdata('user_operation', '<div class="alert alert-danger">You are not authorized to see the detail of other users.</div>');
			redirect('users/index');
		}
		/* ---- */
		
		$data['userDetail'] = array();
		$data['userTargets'] =  array();
		
		if(0!=intval($userid)) {
			$data['userDetail'] = $this->users_model->getUserDetails($userid);
			$data['userRepDetail'] = $this->users_model->getUserRepDetails($userid);
			$data['userTargets'] =  $this->users_model->getUserTargets($userid);	
		}
		
		$data['usertypes'] =  $this->users_model->getUserTypes();
		$data['branches'] =  $this->users_model->getBranches();

		$data['main_content']='users_edit';

		If ($userid == 0)
			$data['main_content']='users_create';
		
		/* Code to modify (copy/add/edit) data in users table */
		$canCopy = true;
		$canAdd = true;
		$canEdit = true;
		
		if(!$this->isAdmin) {
			$canCopy = false;
			$canAdd = false;
			$canEdit = true;
		}
		
		$submit = $this->input->post('submit');
		if(!empty($submit)) {
			$this->form_validation->set_rules('firstname', 'First Name', 'trim|required');
			$this->form_validation->set_rules('surname', 'Surname', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'is_unique[users.email]');
			if($this->isAdmin) {
				$this->form_validation->set_rules('usertype', 'User Type', 'trim|required');
			}
			
			if("B"==$this->input->post('usertype')) {
				$this->form_validation->set_rules('branch', 'Branch', 'trim|required');
			}
						
			if ($this->form_validation->run() == FALSE) {
				
			} else {
				
				$posted_data = array(
					'userid' => $this->input->post('userid'),
					'firstname' => $this->input->post('firstname'),
					'surname' => $this->input->post('surname'),
					'email' => $this->input->post('email'),
					'usertype' => $this->input->post('usertype'),
					'branch' => ""==$this->input->post('branch')?0:$this->input->post('branch'),
					'repcode' => $this->input->post('repcode'),
					'repcode_2' => $this->input->post('repcode_2'),
					'repcode_3' => $this->input->post('repcode_3'),
					'repcode_4' => $this->input->post('repcode_4'),
					'repcode_5' => $this->input->post('repcode_5'),
					'repcode_6' => $this->input->post('repcode_6'),
					'repcode_7' => $this->input->post('repcode_7'),
					'repcode_8' => $this->input->post('repcode_8'),
					'repcode_9' => $this->input->post('repcode_9'),
					'repcode_10' => $this->input->post('repcode_10'),
					'k8userid' => $this->input->post('k8userid'),
					'administrator' => intval($this->input->post('administrator')),
					'active' => intval($this->input->post('active')),
					'seemargins' => intval($this->input->post('seemargins')),
					'seeomr' => intval($this->input->post('seeomr')),
					'seeprojectedsales' => intval($this->input->post('seeprojectedsales')),
					'seeprojectedsalesyear' => intval($this->input->post('seeprojectedsalesyear')),
					'seeorderfulfillment' => intval($this->input->post('seeorderfulfillment')),
					'editnotes' => intval($this->input->post('editnotes')),
					'editterms' => intval($this->input->post('editterms')),
					'edittargets' => intval($this->input->post('edittargets')),
					'salesemail' => intval($this->input->post('salesemail')),
					'type' => $this->input->post('type')
				);				
				
				$message = '';
				
				if("copy"==$posted_data['type']) {
					if($canCopy) {
						$userid = $this->users_model->userModify($posted_data);
						$message = '<div class="alert alert-info">The new user record added successfully using the copy method!';
						$data['type'] = "view";
					} else {
						$message = '<div class="alert alert-danger">You are not authorized to add a new user!</div>';
						$this->session->set_flashdata('user_operation', $message.'</div>');
						redirect('users/index');
					}
				} else {
					if(!empty($posted_data['userid'])) {
						if($canEdit) {						
							$userid = $this->users_model->userModify($posted_data);
							$message = '<div class="alert alert-info">The user record updated successfully!';
							$data['type'] = "view";
						} else {
							$message = '<div class="alert alert-danger">You are not authorized to add a new user!</div>';
							$this->session->set_flashdata('user_operation', $message.'</div>');
							redirect('users/index');
						}					
					} else {
						if($canAdd) {
							$userid = $this->users_model->userModify($posted_data);
							$message = '<div class="alert alert-info">The new user record added successfully!';
							$data['type'] = "view";
						} else {
							$message = '<div class="alert alert-danger">You are not authorized to add a new user!</div>';
							$this->session->set_flashdata('user_operation', $message.'</div>');
							redirect('users/index');
						}
					}
				}	
				
				/* File upload functionality */	
				$updateimage = $this->input->post('updateimage');
				if(!empty($updateimage)) {
					$upload_path = $parent_directory. 'profile_images' . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR . $userid;
					
					if($this->deleteAllProfileImages($upload_path)) {
						$this->moveTempFile($parent_directory, $userid, $updateimage);
					}
				}
							
				/* File upload functionality finished */
				
				$this->session->set_flashdata('user_operation', $message.'</div>');
			//	redirect('users/details/'.$userid."/".$data['type']);
				redirect('users/index');
			}
		}
		$this->load->view('user/front_template', $data);
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
	
	/* Function to delete an user on the basis of the userid. User can not delete itself. */
	public function delete ($userid) {
		$logged_in_user_id = $this->session->userdata('userid');
		$user_details = $this->users_model->getUserDetails($userid);
		
		if(!$this->isAdmin) {
			$this->session->set_flashdata('user_operation', '<div class="alert alert-danger">You can not delete "'.$user_details['firstname'].' '.$user_details['surname'].'", as you are not administrator.</div>');
			redirect('users/index');
		}
		
		if($logged_in_user_id!=$userid) {
			if(!$this->users_model->userHaveSystemLog($userid)) {
				$this->users_model->deleteUser($userid);
				$this->session->set_flashdata('user_operation', '<div class="alert alert-success">The user record was successfully deleted!</div>');
			} else {
				$this->session->set_flashdata('user_operation', '<div class="alert alert-danger">The user cannot be deleted as it has an entry in the System Log.</div>');
				redirect('users/index');
			}
		} else {
			$this->session->set_flashdata('user_operation', '<div class="alert alert-danger">You can not delete "'.$user_details['firstname'].' '.$user_details['surname'].'", as you are logged in with that user.</div>');
		}
		redirect('users/index');
		
	}
	
	/* Function to delete a target of an user on the basis of the target id. */
	public function deletetarget($id, $userid) {	
		header('Content-type: application/json');
		$user_target_details = $this->users_model->getTargetDetails($id);
		$delete = $this->users_model->deleteUserTarget($id);
		echo json_encode(array("deleteresult"=>$delete));exit;
	}
	
	/* Function that returns true if the email is unique */
	public function isUnique($email, $userid="") {
		header('Content-type: application/json');
		$val = $this->users_model->checkUnique($email, $userid);
		echo json_encode(array("unique"=>$val));exit;
	}
	
	/* Function to save surname on userid */
	public function savesurname() {
		header('Content-type: application/json');
		$userid = $this->input->post("id");
		$surname = $this->input->post("surname");
		if($this->users_model->updateSurnameByUserId($userid, $surname)) {
			echo json_encode(array("value"=>$surname));
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


public function kpimodify() {
		$this->load->library('form_validation');
			$user= $this->input->post("user");
			$this->form_validation->set_rules('user', 'User', 'required');
		$this->form_validation->set_rules('kpithreshold1', 'Kpithreshold1', 'required');
		$this->form_validation->set_rules('kpithreshold2', 'kpithreshold2', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('kpi_operation', '<div class="alert alert-danger">The KPI could not be added!</div>');	
		} else {
				$user = $this->input->post("user");
			$kpithreshold1 = $this->input->post("kpithreshold1");
			$kpithreshold2 = $this->input->post("kpithreshold2");
		if($kpithreshold1 > $kpithreshold2)
		{
$result="error_greater";

		}
	else{

$result = $this->users_model->updatekpi($user,$kpithreshold1,$kpithreshold2);
			

	}	if($result=="error_greater") {
				$this->session->set_flashdata('kpi_operation', '<div class="alert alert-danger">kpi threshold2 must be greater then kpi threshold1</div>');	
			}
			
			
			if($result=="success") {
				$this->session->set_flashdata('kpi_operation', '<div class="alert alert-info">The user KPI record updated successfully!</div>');	
			}
			
			if($result=="duplicate") {
				$this->session->set_flashdata('kpi_operation', '<div class="alert alert-danger">Target for '.$year.$month.' already exists!</div>');	
			}
			
			if($result=="fail") {
				$this->session->set_flashdata('kpi_operation', '<div class="alert alert-danger">KPI could not be added!</div>');	
			}
			
		}
		redirect('users/details/'.$user.'/#success');	
	}
	/// End ADD KPI ///
	

	public function addtarget() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('userid', 'User Id', 'required');
		$this->form_validation->set_rules('year', 'Year', 'required');
		$this->form_validation->set_rules('month', 'Month', 'required');
		$this->form_validation->set_rules('target', 'target', 'required');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">The target could not be added!</div>');	
		} else {
			$userid = $this->input->post("userid");
			$year = $this->input->post("year");
			$month = str_pad($this->input->post("month"), 2, "0", STR_PAD_LEFT);
			$target = $this->input->post("target");
			$result = $this->users_model->addUniqueTarget($userid, $year, $month, $target);
			if($result=="success") {
				//$this->session->set_flashdata('target_operation', '<div class="alert alert-success">The target added successfully!</div>');	
			}
			
			if($result=="duplicate") {
				$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">Target for '.$year.$month.' already exists!</div>');	
			}
			
			if($result=="fail") {
				$this->session->set_flashdata('target_operation', '<div class="alert alert-danger">Target could not be added!</div>');	
			}
			
		}
		redirect('users/details/'.$userid.'/#target');	
	}
	
	
	
	
	
	
	public function uploadtarget()
	{
	    if($this->input->server('REQUEST_METHOD')=='POST')
	    {
	        $this->load->library('form_validation');
	       
	       
	         if(!empty($_FILES['file']['name'])){
                    $allowed =  array('csv');
                $filename = $_FILES['file']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if(!in_array($ext,$allowed) ) {
               $this->session->set_flashdata('target_operation', '<div class="alert alert-danger">Invalid Extension!</div>');	
                }else
                {
                    
                  
                    if(is_uploaded_file($_FILES['file']['tmp_name'])){
                    
                    
                    $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
                    
                 
                    fgetcsv($csvFile);
                    
                  
                    while(($line = fgetcsv($csvFile)) !== FALSE){
                    
                       $userid=$this->session->userdata('userid');
                 
                       $yearmonth=$line[0];
                  
                       $salestarget=$line[1];
                  
                      $year=substr($yearmonth,0,4);
                
                     $month=substr($yearmonth,4,2);
                  
                    
                    
                   	$result = $this->users_model->addUniqueTarget($userid, $year, $month, $salestarget);
                    
                    
                    }
                    
                    
                    $this->session->set_flashdata('target_operation', '<div class="alert alert-success">The target added successfully!</div>');	
                    
                    fclose($csvFile);
                    
                    $qstring = '?status=succ';
                    }else{
                    $this->session->set_flashdata('target_operation', '<div class="alert alert-danger">The target could not be added!</div>');	
                    } 
                }
                    
                    
                    
              
                }else{
                $this->session->set_flashdata('target_operation', '<div class="alert alert-danger">The target could not be added!</div>');	
                }
	        
	       	redirect('users/details/'.$userid.'/#target');	
	        
	    }
	    else
	    {
	        redirect('dashboard');
	    }
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
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
				$branchname = $this->site_model->getBranch($userDetail['branch']);
				$data = array(
					"selectedBranch" => array(
						"branchno" => $userDetail['branch'],
						"name" => $branchname
					),
					"selectedUser" => array(
						"userid" => $userDetail['userid'],
						"firstname" =>  $userDetail['firstname'],
						"surname" =>  $userDetail['surname'],
						"kpithreshold1" =>  $userDetail['kpithreshold1'],
						"kpithreshold2" =>  $userDetail['kpithreshold2']
					)
				);
			} else {
				$data = array(
					"selectedUser" => array(
						"userid" => $userDetail['userid'],
						"firstname" =>  $userDetail['firstname'],
						"surname" =>  $userDetail['surname'],
							"kpithreshold1" =>  $userDetail['kpithreshold1'],
						"kpithreshold2" =>  $userDetail['kpithreshold2']
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

	 function export_csv()
	 {

		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=user_export.csv");
		$data['isAdmin'] = $this->isAdmin;
		$data['usersDetail'] = $this->users_model->getUsersList($this->isAdmin);
		$csvOutput = $this->users_model->csv_export($this->isAdmin);
		echo $csvOutput;
		exit();

	 }

	public function repcodes($userid) {
		header('Content-Type: application/json');
		$result = $this->users_model->userRepcodes($userid);
		echo json_encode($result);
		exit;
	}
}
?>
