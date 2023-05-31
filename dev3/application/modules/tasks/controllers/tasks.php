<?php
error_reporting(1);
class Tasks extends Controller {
	public $isAdmin;
	function __construct() {
		parent::Controller();
		$this->load->model('customer/customer_model');
		$this->load->model('tasks_model');
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
		if ($this->site_model->is_logged_in()==false){
			redirect('/');
		}
		/* Old code  */
		setcookie($this->session->userdata('userid').'_last_visited', current_url(), time() + (86400 * 365), "/"); // 86400 = 1 day
		$data['isAdmin'] = $this->isAdmin;
		$data['usersDetail'] = $this->users_model->getUsersList($this->isAdmin);
		$data["taskList"] = $this->tasks_model->taskList();
		$data['main_content']='tasks';
		$this->load->view('tasks/front_template', $data);
	}

	/* Creating fetchTasks function to load the task data on ajax call */
	public function fetchTasks($account = null)
	{
		if (!is_null($account) && $account != "null")
		{
			$account = base64_decode($account);
		}

		header('Content-Type: application/json');
		$draw = 1;
		$count = 0;
		$data = array();

		$return_array = array
		(
			'draw'            => $draw,
			'recordsTotal'    => $count,
			'recordsFiltered' => $count,
			'data'            => $data,
		);

		if ($this->site_model->is_logged_in() == false)
		{
			echo json_encode($return_array);
			exit;
		}

		/* Number of rows to be displayed on one page */
		$limit = 10;

		$start = isset($_POST["start"]) ? $_POST["start"] : 0;
		$length = isset($_POST["length"]) ? $_POST["length"] : $limit;
		$search = isset($_POST["search"]) ? $_POST["search"] : array();
		$draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;

		$specific_search = $this->findPostedSpecificSearchAndMakec($account);
		$specific_order = $this->findPostedOrder($account);
		$search_key = $search['value'];

		$count = $this->tasks_model->taskList($count = true, $account, $start, 0, $search_key, $specific_search, $specific_order);
		$totalrows = $count;

		$data['page'] = intval($this->input->post("page")) ? intval($this->input->post("page")) : 1;
		$offset = ($data['page'] - 1) * $limit;

		$data['totalrows'] = $totalrows;
		$data['pagecount'] = intval($totalrows / $limit) + 1;
		$data['result'] = $this->tasks_model->taskList($count = false, $account, $start, $length, $search_key, $specific_search, $specific_order);

		$data['search'] = $this->input->post("search");
		$fullList = true;

		$return_array = array
		(
			'draw'            => $draw,
			'recordsTotal'    => $totalrows,
			'recordsFiltered' => $totalrows,
			'data'            => $this->filterDataFOrDataTable($data['result'], $account, $fullList),
		);

		echo json_encode($return_array);
		exit;
	}

	/** Function to filter the data from the database to fit in the datatable. */
	public function filterDataFOrDataTable($resultArray, $account, $fullList = true)
	{
		$dataArray = array();

		foreach ($resultArray as $resultRow)
		{
			$attachmentInfo = sizeof($this->attachmentsInfo($resultRow['t_taskid']));
			$attachmentActionLink = '';

			if ($attachmentInfo > 0)
			{
				$attachmentActionLink = '<button onclick="return openAttachments('.$resultRow['t_taskid'].', event);" aria-label="update task attachments" title="Update task attachments" class="btn btn-link" style="padding:0"><i class="glyphicon glyphicon-paperclip" aria-hidden="true"></i></button>';
			}

			$ariaLabel = "";
			$completeClass = "";

			if ($resultRow['t_complete'])
			{
				$ariaLabel = "Task Completed";
				$completeClass = "glyphicon-check";
				$iscomplete = "true";
			}
			else
			{
				$ariaLabel = "Task Incomplete";
				$completeClass = "glyphicon-unchecked";
				$iscomplete = "false";
			}

			$completed = '<button id="chkbx_'.$resultRow['t_taskid'].'" onclick="return stateChange('.$resultRow['t_taskid'].', '.$account.');" aria-label="'.$ariaLabel.'" title="'.$ariaLabel.'" class="btn btn-link completeStatus" style="padding: 0px; margin-right: 4px;" data-iscomplete="'.$iscomplete.'"><i class="glyphicon '.$completeClass.'" aria-hidden="true"></i></button>';

			$actions = '<button onclick="return readRecord('.$resultRow['t_taskid'].', event);" aria-label="view details" title="View details" class="btn btn-link" style="padding: 0px;"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></button>
			<button onclick="return updateRecord('.$resultRow['t_taskid'].', event);" aria-label="update task details" title="Update task details" class="btn btn-link" style="padding: 0px"><i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>'.$attachmentActionLink.'
			<button onclick="return deleteRecord('.$resultRow['t_taskid'].', event);" aria-label="Delete task" title="Delete task" class="btn btn-link" style="padding: 0px"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>';
			$checked = $resultRow['t_complete'] == "0" ? "" : "checked";
			$num = $resultRow['t_complete'] == "0" ? 0 : 1;
			$complete = '<input type="checkbox" value="chkbx_'.$resultRow['t_taskid'].'" '.$checked.' onclick="stateChange(event, '.$resultRow['t_taskid'].', '.($num).', \''.$account.'\')" data-toggle="toggle" />';

			/* Description  */

			$tsk_description = strval("<span id='desc-".$resultRow['t_taskid']."'>");
			$tsk_description+= strval("<span class='short-desc'>".substr($resultRow['t_description'], 0, 20)."</span>");
			$desc_more_string = trim(substr($resultRow['t_description'], 20));
			$more_desc_empty = empty($desc_more_string);
			$more_description = "";
			$description_link = "";

			if (!$more_desc_empty)
			{
				$more_description = "<span class='full-desc hidden'>".substr($resultRow['t_description'], 20)." </span>";
			}

			$tsk_description+= strval("</span>");

			if (!$more_desc_empty)
			{
				$description_link = " <a href='javascript:showMore(".$resultRow['t_taskid'].");' title='show full description' ><span class='show-full'>[show more]</span></a>";
			}

			/* // Description  */
			$tsk_description = "<span id='desc-".$resultRow['t_taskid']."'><span class='short-desc'>".substr($resultRow['t_description'], 0, 20)."</span>".$more_description."</span>".$description_link;
			$overdue = "";

			if (strtotime($resultRow["t_date"]) < strtotime("now"))
			{
				if ("checked" != $checked)
				{
					$overdue = "redrow";
				}
			}

			$date = "<span class='formatted-date' data-class='".$overdue."'>".date("d/m/Y", strtotime($resultRow['t_date']))."</span>";
			$lnk = base_url().'customer/customerDetails/'.base64_encode($resultRow['t_account']);
			$t_account = "<a href='".$lnk ."'>".$resultRow['t_account']."</a>";
			$c_name = "<a href='".$lnk ."'>".$resultRow['c_name']."</a>";
			$fullList = true;

			if (!is_null($account) && "null" != $account && 0 != intval($account) && "" != trim($account) && !empty($account))
			{
				$dataArray[] = array(intVal($resultRow['t_taskid']), $resultRow['cont_title']." ".$resultRow['cont_firstname']." ".$resultRow['cont_surname'], $date, $tsk_description, $completed, $actions);
			}
			else
			{
				$dataArray[] = array(intVal($resultRow['t_taskid']), $resultRow['cont_title']." ".$resultRow['cont_firstname']." ".$resultRow['cont_surname'], $t_account, $c_name, $date, $tsk_description, $completed, $actions);
			}
		}

		return $dataArray;
	}

	/* Function to get the specific search and make the searchable array */
	public function findPostedSpecificSearchAndMakec($account) {
		$posted_columns = $_POST['columns'];
		$search_keys = $this->getSpecificSearchKeys($account);
		$search = array();
		foreach ($posted_columns as $key=>$col) {
			$search[$search_keys[$key]] = $col['search']['value'];
		}
		return $search;
	}

	/* Function to get the keys for specific search */
	public function getSpecificSearchKeys($account) {
		if (!$account) {
			$search_keys = array('t.taskid', 'contact.firstname', 't.date', 't.description', 't.complete');
		} else {
			$search_keys = array('t.taskid', 'contact.firstname', 't.account', 'c.name', 't.date', 't.description', 't.complete');
		}

		/*
		t.taskid as t_taskid, t.userid as t_userid, t.account as t_account, t.contactno as t_contactno, t.date as t_date, t.complete as t_complete, t.description as t_description, t.notes as t_notes, c.name as c_name, contact.title as cont_title, contact.firstname as cont_firstname, contact.surname as cont_surname*/
		return $search_keys;
	}

	/* Function to get the posted order and it's direction. this function will return order by column name that can be used in query directly and the direction. */
	public function findPostedOrder($account) {
		$search_keys = $this->getSpecificSearchKeys($account);
		$posted_order = $_POST['order'];
		$column_index = -1;
		$order = array(
			'by'	=>	$search_keys[0],
			'dir'	=>	'asc'
		);

		if (isset($posted_order[0]['column']) && isset($posted_order[0]['dir'])) {
			$column_index = $posted_order[0]['column'];

		}

		if ($column_index>=0) {
			$order = array(
				'by'	=>	$search_keys[$column_index],
				'dir'	=>	$posted_order[0]['dir']
			);
		} else {
			$order = array(
				'by'	=>	$search_keys[0],
				'dir'	=>	'asc'
			);
		}

		return $order;
	}

	public function excel_export_tasks($account, $ValuesOfSearch) {
		$search = array();
		for ($i=3; $i<=15; $i++) {
			$search[] = $this->uri->segment($i);
		}
		$specific_search_keys = $this->getSpecificSearchKeys($account);
		header("Content-type: text/x-csv");

		header("Content-Disposition: attachment;filename=\"MI-DAS-Task.csv\"");

		header("Cache-Control: max-age=0");
		$xlsOutput = $this->tasks_model->csv_export($search[0], $ValuesOfSearch, $specific_search_keys);

		echo $xlsOutput;
		exit();
	}

	public function newTask($account=NULL) {
		header('Content-Type: application/json');
		$data = array("userId"=>$this->session->userdata('userid'));
		$data["view"] = 0;
		$data["edit"] = 1;
		$data["new"] = 1;
		$data["contactno_input"] = $this->getContactNoInput();
		if (NULL!=$account) {
			$data["t_account"] = $account;
		}
		$return_array = array(
			"title" => "Add New Task",
			"body"	=> $this->load->view('new_task_form', $data, true)
		);
		echo json_encode($return_array);exit;
	}

	public function viewTask($taskid, $edit="onlyview") {
		header('Content-Type: application/json');
		$dataArr = $this->tasks_model->taskDetail($taskid);
		$data = $dataArr[0];
		$data["t_description"] = stripslashes(trim($data["t_description"]));
		$data["t_notes"] = stripslashes(trim($data["t_notes"]));
		$data["userId"] = $this->session->userdata('userid');
		$data["view"] = 1;
		$data["contactno_input"] = $this->getContactNoInput($data['t_contactno'], $data["t_account"]);
		if ("edit"!=$edit) {
			$data["edit"] = 0;
		} else {
			$data["edit"] = 1;
		}

		$data["uploads"] = $this->readUploadedFiles($data["t_taskid"]);

		$return_array = array(
			"title" => "Task Details",
			"body"	=> $this->load->view('new_task_form', $data, true)
		);
		echo json_encode($return_array);exit;
	}

	public function getContactNoInput($t_contactno="", $t_account="") {
		$selected_value = !empty($t_contactno)?$t_contactno:"";
		$account = !empty($t_account)?$t_account:"none";
		$return_default = '<input type="text" class="form-control" id="contactno" name="contactno" value="'.$selected_value.'">';
		$data["options"] = $this->tasks_model->fetchContacts($account);
		$data["selected"] = $selected_value;
		$options = $this->load->view('contact_options', $data, true);
		if (!empty($data["options"])) {
			$return_default = '<select class="form-control" id="contactno" name="contactno" data-value="'.$selected_value.'">'.$options.'</select>';
		}
		return $return_default;
	}
	public function readUploadedFiles($taskid) {
		$uploads = array();
		$dir = $this->getUploadDir($taskid);
		if (is_dir($dir)){
			if ($dh = opendir($dir)){
				while (($file = readdir($dh)) !== false){
					$file = trim($file);
					if ("."!=$file && ".."!=$file && !empty($file)) {
						$uploads[] = $file;
					}
				}
				closedir($dh);
			}
		}
		return $uploads;
	}

	public function addTask() {
		header('Content-Type: application/json');
		$addedTaskId = $this->tasks_model->addTask();
		$fileUpload = array("success"=>"undefined", "data"=>"undefined" ,"messages"=>"undefined");
		if (0!=$addedTaskId) {
			$fileUpload = $this->do_upload($addedTaskId);
		} else {
			$fileUpload = array("success"=>false, "data"=>"No data updated/added", "messages"=>"There is no task updated or added!");
		}
		echo json_encode($fileUpload);exit;
	}

	public function addFile() {
		header('Content-Type: application/json');
		$fileUpload = array("success"=>"undefined", "data"=>"undefined" ,"messages"=>"undefined");
		$fileUpload = $this->do_upload($_POST["taskid"]);
		echo json_encode($fileUpload);exit;
	}

	public function do_upload($addedTaskId){
		$upload_path = $this->getUploadDir($addedTaskId);
		mkdir($upload_path);
		chmod($upload_path, 0777);
		$config['upload_path']          = $upload_path;
		$config['allowed_types']        = 'gif|jpg|png|pdf|doc|docx|xls|xlsx';
		$config['max_size']             = 0;
		$config['max_width']            = 0;
		$config['max_height']           = 0;
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload('uploadeddoc')) {
				$error = array('error' => $this->upload->display_errors());
				return array("success"=>false, "data"=>"data updated to database. However, file not uploaded" , "messages"=>$error);
		} else {
			$data = array('upload_data' => $this->upload->data());
			return array("success"=>true, "data"=>"data updated to database." , "messages"=>$data);
		}
	}

	public function getUploadDir($addedTaskId) {
		$parent_directory = APPPATH . 'modules' . DIRECTORY_SEPARATOR . 'tasks' . DIRECTORY_SEPARATOR . 'documents' . DIRECTORY_SEPARATOR;
		$sub_directory = $parent_directory. $addedTaskId . DIRECTORY_SEPARATOR;
		$upload_path = $sub_directory;
		return $upload_path;
	}

	public function documents($taskid, $file) {
		$this->load->helper('download');
	//	$file = str_replace("/", "", $file);
		$filepath = $this->getUploadDir($taskid);
		$data = file_get_contents($filepath.$file);
		force_download($file, $data);
		echo $filepath.$file;exit;
	}

	public function deleteRecord($taskid) {
		header('Content-Type: application/json');
		$filepath = $this->getUploadDir($taskid);
		if ($this->tasks_model->deleteRecord($taskid)) {
			$this->load->helper("file");
			$isDeleted = delete_files($filepath);
			if (!$isDeleted) {
				if (!file_exists($filepath)) {
					echo json_encode(array("status"=>"success", "deleted"=>"Selected record is deleted successfully"));
				} else {
					echo json_encode(array("status"=>"warning", "deleted"=>"Selected record is deleted. However, the files could not be deleted"));
				}
			} else {
				echo json_encode(array("status"=>"success", "deleted"=>"Selected record is deleted successfully"));
			}
		} else {
			echo json_encode(array("status"=>"danger", "deleted"=>"Nither record nor files related to record could be deleted!"));
		}
	}

	public function attachmentsInfo($taskid) {
		$filepath = $this->getUploadDir($taskid);
		$this->load->helper("file");
		if (!file_exists($filepath)){
			$return_array = array();
		} else {
			$return_array = get_dir_file_info($filepath, true);
		}

		return $return_array;
	}

	public function overdue() {
		header('Content-Type: application/json');
		$account = "";
		$overdueTask = $this->tasks_model->overdueTask($account);
		$return = array(
			"count" => intval(count($overdueTask)),
			"taskIds"=> $overdueTask
		);
		echo json_encode($return); exit;
	}


	/* Creating fetchOverdueTasks function to load the overdue task data on ajax call */
	public function fetchOverdueTasks($account = null)
	{
		if (!is_null($account) && $account != "null")
		{
			$account = base64_decode($account);
		}

		header('Content-Type: application/json');
		$draw = 1;
		$count = 0;
		$data = array();

		$return_array = array
		(
			'draw'            => $draw,
			'recordsTotal'    => $count,
			'recordsFiltered' => $count,
			'data'            => $data,
		);

		if ($this->site_model->is_logged_in() == false)
		{
			echo json_encode($return_array);
			exit;
		}

		/* Number of rows to be displayed on one page */
		$limit = 10;

		$start = isset($_POST["start"]) ? $_POST["start"] : 0;
		$length = isset($_POST["length"]) ? $_POST["length"] : $limit;
		$search = isset($_POST["search"]) ? $_POST["search"] : array();
		$draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;

		$specific_search = $this->findPostedSpecificSearchAndMakec($account);
		$specific_order = $this->findPostedOrder($account);
		$search_key = $search['value'];

		$data['userDetail']['repwhere'];
		$count = $this->tasks_model->overdueTaskList($count = true, $account, $start, 0, $search_key, $specific_search, $specific_order);
		$totalrows = $count;

		$data['page'] = intval($this->input->post("page")) ? intval($this->input->post("page")) : 1;

		$offset = ($data['page'] - 1) * $limit;

		$data['totalrows'] = $totalrows;
		$data['pagecount'] = intval($totalrows/$limit) + 1;
		$data['result'] = $this->tasks_model->overdueTaskList($count = false, $account, $start, $length, $search_key, $specific_search, $specific_order);

		$data['search'] = $this->input->post("search");
		$fullList = false;

		$return_array = array
		(
			'draw'            => $draw,
			'recordsTotal'    => $totalrows,
			'recordsFiltered' => $totalrows,
			'data'            => $this->filterDataFOrDataTable($data['result'], $account, $fullList),
		);

		echo json_encode($return_array);
		exit;
	}

	public function deletefile($taskid, $filename) {
		header('Content-Type: application/json');
		$filepath = $this->getUploadDir($taskid);

		$this->load->helper("file");

		$isDeleted = delete_files($filepath."/".stripslashes(html_entity_decode($filename)));
		if (!$isDeleted) {
			if (!file_exists($filepath."/".$filename)) {
				echo json_encode(array("status"=>"success", "deleted"=>"Selected file was not available to delete!"));
			} else {
				echo json_encode(array("status"=>"warning", "deleted"=>"The file could not be deleted"));
			}
		} else {
			echo json_encode(array("status"=>"success", "deleted"=>"Selected file is deleted successfully"));
		}
	}

	public function uploadedDocumentsList($taskid=0, $isEditing=0) {
		header('Content-Type: application/json');
		$data = array("userId"=>$this->session->userdata('userid'));
		$data["uploads"] = $this->readUploadedFiles($taskid);
		$data["taskid"] = $taskid;
		if ("0"==$isEditing) {
			$data["view"] = 1;
			$data["edit"] = 0;
		} else {
			$data["view"] = 0;
			$data["edit"] = 1;
		}
		$return_array = array(
			"body"	=> $this->load->view('uploaded_document_form', $data, true)
		);
		echo json_encode($return_array);exit;
	}

	public function completed() {
		header('Content-Type: application/json');
		$updated = $this->tasks_model->updateCompletedState();
		echo json_encode($updated);exit;
	}

	public function allAccounts() {
		header('Content-Type: application/json');
		echo json_encode($this->tasks_model->allAccounts());
	}

	public function fetchContacts($account, $selected_value) {
		$data["options"] = $this->tasks_model->fetchContacts($account);
		$data["selected"] = $selected_value;
		echo $this->load->view('contact_options', $data, true)."|".count($data["options"]);
		exit;
	}

	public function newRep($accountEncoded)
	{
		$account = base64_decode($accountEncoded);
		header('Content-Type: application/json');
		$data = array();
		$data['account'] = $accountEncoded;
		$data['salesrep'] = $this->tasks_model->fetchSalesrep();
		$title = "" != trim($account) ? "Add customer repcode for ".$account : "Add user repcode";

		$return_array = array
		(
			'title' => $title,
			'body'	=> $this->load->view("new_customer_rep_form", $data, true),
		);

		echo json_encode($return_array);
		exit;
	}

	public function addCustomerRepcode()
	{
		header('Content-Type: application/json');
		$return_array = $this->tasks_model->addCustomerRepcode();
		echo json_encode($return_array);
		exit;
	}

	public function addUserRepcode() {
		header('Content-Type: application/json');
		$return_array = $this->tasks_model->addUserRepcode();
		echo json_encode($return_array);exit;
	}
}
?>
