<?php

class Site extends Controller
{
  public $canSeeMargins, $canSeeOMR;
  public $data;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('site_model');
    $this->load->model('users_model');
    $twoSee = canSeeMarginsAndOMR();
    $this->canSeeMargins = $twoSee['seemargins'];
    $this->canSeeOMR = $twoSee['seeomr'];
    $this->data = array();

  }

  public function index()
  {
    //echo base_url();
    if ($this->site_model->is_logged_in() !== false)
    {
      redirect('dashboard');
    }
    // if (strstr(current_url(), 'login') == false)
    // {
    //      redirect('login.php'); //@@@star
    // }
    $data['main_content'] = '';
    $this->load->view('site/login', $data);
  }

  public function password()
  {
    if ($this->site_model->is_logged_in() !== false)
    {
      redirect('dashboard');
    }

    $email = $this->input->post('email');
    $data["status"] = $status;
    $data['main_content'] = '';
    $this->load->view('site/password_template', $data);
  }

  public function loginForm()
  {
    $query = $this->site_model->get_user_login($this->input->post('user_name'), $this->input->post('userPass'));
    if (count($query) >= 1) // if the user's credentials validated...
    {
      if ("B" == $query->usertype)
      {
        $selectedUser = array();
        $branchname = $this->site_model->getBranch($query->branch);
        $branchSession = array(
          "branchno" => $query->branch,
          "name" => $branchname
        );
      }
      else
      {
        if ("R" == $query->usertype)
        {
          $branchSession = array();
          $selectedUser = array(
            "userid" => $query->userid,
            "firstname" => $query->firstname,
            "surname" => $query->surname
          );
        }
        else
        {
          $branchSession = array();
          $selectedUser = array();
        }
      }

      $data = array(
        'username' => $query->firstname,
        'userid' => $query->userid,
        'usertype' => $query->usertype, // Adding new key to identify the user type
        'is_logged_in' => true,
        'selectedBranch' => $branchSession,
        'selectedUser' => $selectedUser
      );
      $this->session->set_userdata($data);
      $this->load->model('users_model');
      $description = "User login";
      $this->users_model->savelog($description, $query->usertype);
      $data['status'] = "Success";
      $data['url'] = 'dashboard';
      /*if(isset($_COOKIE[$this->config->item('site_name').'_'.$this->session->userdata('userid').'_last_visited'])) {
  $data['url']=$_COOKIE[$this->config->item('site_name').'_'.$this->session->userdata('userid').'_last_visited'];
}*/
    }
    else // incorrect username or password
    {
      $result = $this->site_model->get_active($this->input->post('user_name'), $this->input->post('userPass'));
      if (count($result) > 0 && 0 == $result->active)
      {
        $data['msg'] = "This user is inactive and cannot login";
      }
      else
      {
        $data['msg'] = "Invalid user";
      }

      $data['status'] = "failed";
    }
    echo json_encode($data);
  }

  public function setPassword()
  {
    $query = $this->site_model->get_user($this->input->post('email'));
    //var_dump($query);
    if (count($query) >= 1)
    { // if the user's credentials validated...
      $this->load->library('email', array('mailtype' => 'html'));

      $encrypt = md5(90 * 13 + intval($query['userid']));
      $message = '<p>Hi ' . $query['firstname'] . ',</p><p>Click the link below to set your MI-DAS password</p><p>' . base_url() . 'site/resetpassword/' . $encrypt . ' </p>';

      $this->email->from('kieran@kk-cs.co.uk', 'Kieran Kelly');
      $this->email->to($this->input->post('email'));

      $this->email->subject('Set Your MI-DAS Password');
      $this->email->message($message);

      $this->email->send();

      $data['status'] = "success";
      $data['msg'] = 'Please check your mail';
      $message = "Please check your email" . $this->input->post('email') . " to set a login password.";
    }
    else
    { // incorrect username or password
      $data['status'] = "danger";
      $data['msg'] = "Invalid email";
      $message = "The email is not stored in the database.";
    }
    //echo"here".$data['status'];exit;
    $this->session->set_flashdata('password_message', "<div class='alert alert-" . $data['status'] . "'>" . $message . "</div>");
    redirect("set-password");
  }

  public function forgotPassword()
  {
    $query = $this->site_model->get_user($this->input->post('email'));
    //var_dump($query);
    if (count($query) >= 1) // if the user's credentials validated...
    {

      $this->load->library('email');

      $encrypt = md5(90 * 13 + intval($query['userid']));
      $message = 'Hi ' . $query['firstname'] . ',<br/> <br/>Click here to reset your MI-DAS password ' . base_url() . 'site/resetpassword/' . $encrypt;

      $this->email->from('kieran@kk-cs.co.uk', 'Kieran Kelly');
      $this->email->to($this->input->post('email'));

      $this->email->subject('Forget Your MI-DAS Password');
      $this->email->message($message);

      $this->email->send();

      $data['status'] = "Success";
      $data['msg'] = 'Please check your mail';
    }
    else // incorrect username or password
    {
      $data['status'] = "failed";
      $data['msg'] = "Invalid email";
    }
    echo json_encode($data);
  }

  public function resetpassword($encrypt = '')
  {
    if ($this->site_model->is_logged_in() !== false)
    {
      redirect('dashboard');
    }
    $data['user'] = $this->site_model->get_user_encrypt($encrypt);
    $data['encrypt'] = $encrypt;
    if ($data['user']['userid'] > 0)
    {
      $data['error'] = "";
    }
    else
    {
      $data['error'] = "Invalid encryption";
      $data['user']['userid'] = 0;
    }
    $data['main_content'] = '';
    $this->load->view('site/forgot_password', $data);
  }

  public function resetMyPass()
  {
    $status = $this->site_model->update_user_password($this->input->post('newPass'), $this->input->post('enc'));
    if ($status > 0)
    {
      $data['status'] = "Success";
      $data['url'] = base_url() . 'login.php';
    }
    else //
    {
      $data['status'] = "failed";
      $data['msg'] = "Invalid encryption";

    }
    //echo json_encode($data);
  }

  function logout()
  {
    $this->load->model('users_model');
    $description = "User logout";
    $this->users_model->savelog($description, $this->session->userdata('usertype'));
    $this->session->unset_userdata('username');
    $this->session->unset_userdata('is_logged_in');
    $this->session->sess_destroy();
    redirect('/');
  }

  public function dashboard()
  {
    $this->data['main_content'] = 'dashboard';
    $this->load->view('site/front_template', $this->data);
  }



  public function dashboard1()
  {
    $this->data = array();
    $this->data["canSeeMargins"] = $this->canSeeMargins;
    $this->data["canSeeOMR"] = $this->canSeeOMR;
    $this->load->helper('cookie');
    //echo get_cookie('salestodaydonutcharts',true);

    if (isset($_COOKIE['salestodaydonutcharts'])) {
      $this->data['salestodaydonutcharts'] = get_cookie('salestodaydonutcharts', true);
    } else {
      $this->data['salestodaydonutcharts'] = 0;
    }

    if (isset($_COOKIE['outstandingordersdonutchart'])) {
      $this->data['outstandingordersdonutchart'] = get_cookie('outstandingordersdonutchart', true);
    } else {
      $this->data['outstandingordersdonutchart'] = 0;
    }

    if (isset($_COOKIE['threeyearsaleschart'])) {
      $this->data['threeyearsaleschart'] = get_cookie('threeyearsaleschart', true);
    } else {
      $this->data['threeyearsaleschart'] = 0;
    }


    $currency_symbol = $this->config->item("currency_symbol");
    if ($this->site_model->is_logged_in() == false) {
      redirect('/');
    }
    $user_id = $this->session->userdata('userid');
    $G_todaysdate = date("Y/m/d");

    $som = date("Y/m/01", strtotime($G_todaysdate));
    $ukdate = date("d/m/Y", strtotime($G_todaysdate));


    $daysinmonth = date("t", strtotime($G_todaysdate));
    $year = date("Y", strtotime($G_todaysdate));
    $month = date("m", strtotime($G_todaysdate));
    $eomtemp = $year . '/' . $month . '/' . $daysinmonth;
    $eom = date('Y/m/d', strtotime($eomtemp));
    $ukeom = date('d/m/Y', strtotime($eomtemp));
    $this->data['userDetail'] = $this->site_model->getUserDetails($user_id);
    $userType = $this->data['userType'] = $this->data['userDetail']['usertype'];

    $userId = 0;
    $branchNo = 0;
    $headerUserId = 0;
    $selectedUserDetails = array('repwhere' => "");


    $selectedUser = $this->session->userdata('selectedUser');
    if (count($selectedUser) > 0) {
      $UserSes = $selectedUser;
      $userId = $UserSes["userid"];
      // $selectedUserDetails = $this->site_model->getUserDetails($userId);
      $selectedUserDetails = $this->data['userDetail'];
    }

    $headerUserId = $userId;

    $selectedBranch = $this->session->userdata('selectedBranch');
    if (count($selectedBranch) > 0) {
      //$branchSes = $this->session->userdata('selectedBranch');
      $branchNo = $selectedBranch["branchno"];
    }

    $G_branchno = null;

    if ($userType == "B") {
      $G_level = "Branch";
    } elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0)) {
      $G_level = "Company";
    } elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0)) {
      $G_level = "Branch";
      $G_branchno = $branchNo;
    } elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0)) {
      $G_level = "User";
      $G_userId = $userId;
      //  $this->data['userDetail'] = $this->site_model->getUserDetails($userId);
    } else {
      $G_level = "User";
    }

    $repclause = $this->data["userDetail"]["repclause"];

    /// KPI last Update///
    $kpiLastupdate_val = $this->site_model->kpiLastupdate();
    $this->data["G_KPIsLastUpdatedDateTime"] = $kpiLastupdate_val["kpislastupdated"];

    // END KPI last Update///

    // GET THE DAY NUMBER AND WORKING DAYS//
    $kworkingDays = $this->site_model->workingDays($G_todaysdate);
    $this->data["dayno"] = $kworkingDays['dayno'];  // Current working day number
    $this->data["totdays"] = $kworkingDays['totdays']; // Total number of working days in the month


    // END GET THE DAY NUMBER AND WORKING DAYS//


    // Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set

    // END Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set
    // Get TODAYS ORDERS BY TYPE PIE CHART ///

    if (is_null($G_branchno)) {
      $G_branchno = $this->data['userDetail']['branch'];
    }

    $todayOrders = $this->site_model->todayOrders($G_level, $G_todaysdate, $repclause, $G_branchno);

    $BIColour = "#3c8dbc";    // Book Ins  		Light Blue
    $BOColour = "#f39c12";    // Book Outs  		Yellow
    $BTColour = "#001f3f";    // Branch Transfers	Navy
    $CRColour = "#dd4b39";    // Credits  		Red
    $DNColour = "#39cccc";    // Debit Notes  	Teal
    $QTColour = "#00c0ef";    // Quotations  		Aqua
    $SLColour = "#00a65a";    // Orders  			Green
    $WOColour = "#d2d6de";    // Works Orders  	Gray
    $RWColour = "#f44295";
    $TCColour = "#7a1919";
    $THColour = "#4f5960";

    // Assign legend colours to order types

    $BITextColour = "text-light-blue";    // Book Ins  		Light Blue
    $BOTextColour = "text-yellow";        // Book Outs  		Yellow
    $BTTextColour = "text-navy";        // Branch Transfers	Navy
    $CRTextColour = "text-red";            // Credits  		Red
    $DNTextColour = "text-teal";        // Debit Notes  	Teal
    $QTTextColour = "text-aqua";        // Quotations  		Aqua
    $SLTextColour = "text-green";        // Orders  			Green
    $WOTextColour = "text-gray";        // Works Orders  	Gray
    $RWTextColour = "text-rwcolor";
    $TCTextColour = "text-tccolor";
    $THTextColour = "text-thcolor";
    $todaysordersbytypedata = "[";

    $i = 1;
    $tmp_total = 0;

    foreach ($todayOrders as $today) {
      $identifier = $today['identifier'];
      $value = $today['actualvalue1'];
      // The order type is the last two characters of the identifier
      $ordtype = substr($identifier, 10, 2);
      // Only interested in graphing order types that have a value
      if ($value <> 0) {
        $tmp_total += $value;

        // Set the colour, which is the order type followed by"Colour"
        switch ($ordtype) {
          case"BI":
            $colour = $BIColour;
            $textcolour = $BITextColour;
            $description = "Book Ins";
            break;
          case"BO":
            $colour = $BOColour;
            $textcolour = $BOTextColour;
            $description = "Book Outs";
            break;
          case"BT":
            $colour = $BTColour;
            $textcolour = $BTTextColour;
            $description = "Branch Transfers";
            break;
          case"CR":
            $colour = $CRColour;
            $textcolour = $CRTextColour;
            $description = "Credit Notes";
            break;
          case"DN":
            $colour = $DNColour;
            $textcolour = $DNTextColour;
            $description = "Debit Notes";
            break;
          case"QT":
            $colour = $QTColour;
            $textcolour = $QTTextColour;
            $description = "Quotations";
            break;
          case"SL":
            $colour = $SLColour;
            $textcolour = $SLTextColour;
            $description = "Sales Orders";
            break;
          case"WO":
            $colour = $WOColour;
            $textcolour = $WOTextColour;
            $description = "Works Orders";
            break;
          case"RW":
            $colour = $RWColour;
            $textcolour = $RWTextColour;
            $description = "Repairs & Warranty";
            break;
          case"TC":
            $colour = $TCColour;
            $textcolour = $TCTextColour;
            $description = "Plant Hire Credit Note";
            break;
          case"TH":
            $colour = $THColour;
            $textcolour = $THTextColour;
            $description = "Plant Hire Order";
            break;
        }

        // The comma only comes in after the first set

        if ($i <> 1) {
          $todaysordersbytypedata .= ",";
        }

        // Build the data string for the pie chart data
        $todaysordersbytypedata .= "{value:$value,color:'$colour',highlight:'$colour',label:'$ordtype'}";

        // Build the string for the legend
        $typeLink = site_url("site/todaysorder/" . $ordtype . "/type");
        $todaysordersbytypelegend .= "<li><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'><i class='fa fa-circle-o $textcolour'></i> $ordtype</a></li>";
        // Build the string for the table
        $todaysordersbytypetable .= "<tr><td><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>$ordtype</a></td><td><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>$description</a></td><td align='right'><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>" . $currency_symbol . number_format($value, 2) . "</a></td></tr>";

        $i++;
      }
    }
    $todaysordersbytypetable .= "<tr><th>&nbsp</th><th>Total</th><th style='text-align: right'>" . $currency_symbol . number_format($tmp_total, 2) . "</th></tr>";
    $todaysordersbytypedata .= "]";

    $this->data["todaysordersbytypedata"] = $todaysordersbytypedata;
    $this->data["todaysordersbytypelegend"] = $todaysordersbytypelegend;
    $this->data["todaysordersbytypetable"] = $todaysordersbytypetable;
    // End TODAYS ORDERS BY TYPE PIE CHART ///
    // TODAYS ORDERS BY STATUS PIE CHART ////
    $G_userid = $this->session->userdata("userid");


    // This is a potential bug fix, for some reason $userKpi is returning an empty array and therefore not working with the following function
    $userDetailAsKpi = array($this->data['userDetail']);
    $this->data = GetKpiDataForTwoYearVsTargetChart($userDetailAsKpi, $this->data, $G_level);

    // This is the code that was not working
    // $userKpi = $this->site_model->userKpi($G_level, $G_branchno, $G_userid);
    // $this->data = $this->site_model->GetKpiDataForTwoYearVsTargetChart($userKpi, $this->data, $G_level);

    $todayOrders = $this->site_model->todayOrdersStatus($G_level, $G_todaysdate, $repclause, $G_branchno);
    $ADVColour = "#f012be";    // Waiting advice note	Fuschia
    $COMColour = "#00a65a";    // Completed line		Green
    $CUSColour = "#39cccc";    // Call customer back	Teal
    $HLDColour = "#3d9970";    // Goods on hold		Olive
    $IBTColour = "#d2d6de";    // Inter-branch transfer	Gray
    $KITColour = "#01ff70";    // Process kit list		Lime
    $MEMColour = "#ff851b";    // Memo line			Orange
    $OFFColour = "#605ca8";    // Call off later		Purple
    $PIKColour = "#001f3f";    // Pick note printed	Navy
    $PROColour = "#3c8dbc";    // Process document		Light Blue
    $PURColour = "#dd4b39";    // Purchase order		Red
    $SBOColour = "#f39c12";    // Stock backorder		Yellow
    $WDLColour = "#00c0ef";    // Waiting delivery		Aqua
    $WRKColour = "#d81b60";    // Create works order	Maroon

    // Assign legend colours to order statuses

    $ADVTextColour = "text-fuschia";    // Waiting advice note	Fuschia
    $COMTextColour = "text-green";        // Completed line		Green
    $CUSTextColour = "text-teal";        // Call customer back	Teal
    $HLDTextColour = "text-olive";        // Goods on hold		Olive
    $IBTTextColour = "text-gray";        // Inter-branch transfer	Gray
    $KITTextColour = "text-lime";        // Process kit list		Lime
    $MEMTextColour = "text-orange";        // Memo line			Orange
    $OFFTextColour = "text-purple";        // Call off later		Purple
    $PIKTextColour = "text-navy";        // Pick note printed	Navy
    $PROTextColour = "text-light-blue";    // Process document		Light Blue
    $PURTextColour = "text-red";        // Purchase order		Red
    $SBOTextColour = "text-yellow";        // Stock backorder		Yellow
    $WDLTextColour = "text-aqua";        // Waiting delivery		Aqua
    $WRKTextColour = "text-maroon";        // Cr

    $todaysordersbystatusdata = "[";

    $i = 1;
    $tmp_total = 0;

    foreach ($todayOrders as $today) {

      $identifier = $today['identifier'];
      $value = $today['actualvalue1'];

      // The order type is the last three characters of the identifier
      $ordstatus = substr($identifier, 10, 3);
      // Only interested in graphing order statuses that have a value

      if ($value <> 0) {
        $tmp_total += $value;

        // Set the colour, which is the order status followed by"Colour"
        switch ($ordstatus) {
          case"ADV":
            $colour = $ADVColour;
            $textcolour = $ADVTextColour;
            $description = "Waiting Advice Note";
            break;
          case"COM":
            $colour = $COMColour;
            $textcolour = $COMTextColour;
            $description = "Completed Line";
            break;
          case"CUS":
            $colour = $CUSColour;
            $textcolour = $CUSTextColour;
            $description = "Call Customer Back";
            break;
          case"HLD":
            $colour = $HLDColour;
            $textcolour = $HLDTextColour;
            $description = "Goods On Hold";
            break;
          case"IBT":
            $colour = $IBTColour;
            $textcolour = $IBTTextColour;
            $description = "Inter-Branch Transfer";
            break;
          case"KIT":
            $colour = $KITColour;
            $textcolour = $KITTextColour;
            $description = "Process Kit List";
            break;
          case"MEM":
            $colour = $MEMColour;
            $textcolour = $MEMTextColour;
            $description = "Memo Line (Quotations)";
            break;
          case"OFF":
            $colour = $OFFColour;
            $textcolour = $OFFTextColour;
            $description = "Call Off Later";
            break;
          case"PIK":
            $colour = $PIKColour;
            $textcolour = $PIKTextColour;
            $description = "Pick Note Printed";
            break;
          case"PRO":
            $colour = $PROColour;
            $textcolour = $PROTextColour;
            $description = "Process Document";
            break;
          case"PUR":
            $colour = $PURColour;
            $textcolour = $PURTextColour;
            $description = "Purchase Order";
            break;
          case"SBO":
            $colour = $SBOColour;
            $textcolour = $SBOTextColour;
            $description = "Stock Backorder";
            break;
          case"WDL":
            $colour = $WDLColour;
            $textcolour = $WDLTextColour;
            $description = "Waiting Delivery";
            break;
          case"WRK":
            $colour = $WRKColour;
            $textcolour = $WRKTextColour;
            $description = "Create Works Order";
            break;
        }

        // The comma only comes in after the first set

        if ($i <> 1) {
          $todaysordersbystatusdata .= ",";
        }

        // Build the data string for the pie chart data
        $todaysordersbystatusdata .= "{value:$value,color:'$colour',highlight:'$colour',label:'$ordstatus'}";
        $typeLinkStatus = site_url("site/todaysorder/" . $ordstatus . "/status");
        // Build the string for the legend
        $todaysordersbystatuslegend .= "<li><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'><i class='fa fa-circle-o " . $textcolour . "'></i> $ordstatus</a></li>";

        // Build the string for the table
        $todaysordersbystatustable .= "<tr><td><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>$ordstatus</a></td><td><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>$description</a></td><td align='right'><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>" . $currency_symbol . number_format($value, 2) . "</a></td></tr>";

        $i++;
      }
    }
    $todaysordersbystatustable .= "<tr><th>&nbsp</th><th>Total</th><th  style='text-align: right'>" . $currency_symbol . number_format($tmp_total, 2) . "</th></tr>";
    $todaysordersbystatusdata .= "]";
    $this->data["todaysordersbystatustable"] = $todaysordersbystatustable;
    $this->data["todaysordersbystatusdata"] = $todaysordersbystatusdata;
    $this->data["todaysordersbystatuslegend"] = $todaysordersbystatuslegend;

    // End TODAYS ORDERS BY STATUS PIE CHART
    // -------------------------------------------------------------------------------------------------------------------------------------------------


    // Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set

    // Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set


    //-----------------Outstanding Ordrs---------------------//
    $outOrders = $this->site_model->outStandOrders($G_level, $G_todaysdate, $repclause, $G_branchno);
    $ADVColour = "#f012be";    // Waiting advice note	Fuschia
    $COMColour = "#00a65a";    // Completed line		Green
    $CUSColour = "#39cccc";    // Call customer back	Teal
    $HLDColour = "#3d9970";    // Goods on hold		Olive
    $IBTColour = "#d2d6de";    // Inter-branch transfer	Gray
    $KITColour = "#01ff70";    // Process kit list		Lime
    $MEMColour = "#ff851b";    // Memo line			Orange
    $OFFColour = "#605ca8";    // Call off later		Purple
    $PIKColour = "#001f3f";    // Pick note printed	Navy
    $PROColour = "#3c8dbc";    // Process document		Light Blue
    $PURColour = "#dd4b39";    // Purchase order		Red
    $SBOColour = "#f39c12";    // Stock backorder		Yellow
    $WDLColour = "#00c0ef";    // Waiting delivery		Aqua
    $WRKColour = "#d81b60";    // Create works order	Maroon

    // Assign legend colours to order statuses

    $ADVTextColour = "text-fuschia";        // Waiting advice note	Fuschia
    $COMTextColour = "text-green";        // Completed line		Green
    $CUSTextColour = "text-teal";        // Call customer back	Teal
    $HLDTextColour = "text-olive";        // Goods on hold		Olive
    $IBTTextColour = "text-gray";        // Inter-branch transfer	Gray
    $KITTextColour = "text-lime";        // Process kit list		Lime
    $MEMTextColour = "text-orange";        // Memo line			Orange
    $OFFTextColour = "text-purple";        // Call off later		Purple
    $PIKTextColour = "text-navy";        // Pick note printed	Navy
    $PROTextColour = "text-light-blue";    // Process document		Light Blue
    $PURTextColour = "text-red";            // Purchase order		Red
    $SBOTextColour = "text-yellow";        // Stock backorder		Yellow
    $WDLTextColour = "text-aqua";        // Waiting delivery		Aqua
    $WRKTextColour = "text-maroon";        // Create works order	Maroon

    // The pie chart data string looks something like this : [{value:179.80,color:'#dd4b39',highlight:'#dd4b39',label:'CR'},{value:1307.96,color:'#00a65a',highlight:'#00a65a',label:'SL'}]

    $outstandingordersbystatusdata = "[";

    $i = 1;
    $tmp_total = 0;

    foreach ($outOrders as $outor) {
      $identifier = $outor["identifier"];
      $value = $outor["actualvalue1"];

      // The order type is the last three characters of the identifier
      $ordstatus = substr($identifier, 10, 3);
      // Only interested in graphing order statuses that have a value

      if ($value <> 0) {
        $tmp_total += $value;

        // Set the colour, which is the order status followed by"Colour"
        switch ($ordstatus) {
          case"ADV":
            $colour = $ADVColour;
            $textcolour = $ADVTextColour;
            $description = "Waiting Advice Note";
            break;
          case"COM":
            $colour = $COMColour;
            $textcolour = $COMTextColour;
            $description = "Completed Line";
            break;
          case"CUS":
            $colour = $CUSColour;
            $textcolour = $CUSTextColour;
            $description = "Call Customer Back";
            break;
          case"HLD":
            $colour = $HLDColour;
            $textcolour = $HLDTextColour;
            $description = "Goods On Hold";
            break;
          case"IBT":
            $colour = $IBTColour;
            $textcolour = $IBTTextColour;
            $description = "Inter-Branch Transfer";
            break;
          case"KIT":
            $colour = $KITColour;
            $textcolour = $KITTextColour;
            $description = "Process Kit List";
            break;
          case"MEM":
            $colour = $MEMColour;
            $textcolour = $MEMTextColour;
            $description = "Memo Line (Quotations)";
            break;
          case"OFF":
            $colour = $OFFColour;
            $textcolour = $OFFTextColour;
            $description = "Call Off Later";
            break;
          case"PIK":
            $colour = $PIKColour;
            $textcolour = $PIKTextColour;
            $description = "Pick Note Printed";
            break;
          case"PRO":
            $colour = $PROColour;
            $textcolour = $PROTextColour;
            $description = "Process Document";
            break;
          case"PUR":
            $colour = $PURColour;
            $textcolour = $PURTextColour;
            $description = "Purchase Order";
            break;
          case"SBO":
            $colour = $SBOColour;
            $textcolour = $SBOTextColour;
            $description = "Stock Backorder";
            break;
          case"WDL":
            $colour = $WDLColour;
            $textcolour = $WDLTextColour;
            $description = "Waiting Delivery";
            break;
          case"WRK":
            $colour = $WRKColour;
            $textcolour = $WRKTextColour;
            $description = "Create Works Order";
            break;
        }

        // The comma only comes in after the first set

        if ($i <> 1) {
          $outstandingordersbystatusdata .= ",";
        }
        $outstandingLink = site_url("site/outstandingorder/" . $ordstatus . "/status");
        // Build the data string for the pie chart data
        $outstandingordersbystatusdata .= "{value:$value,color:'$colour',highlight:'$colour',label:'$ordstatus'}";

        // Build the string for the legend
        $outstandingordersbystatuslegend .= "<li><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'><i class='fa fa-circle-o $textcolour'></i> $ordstatus</a></li>";

        // Build the string for the table
        $outstandingordersbystatustable .= "<tr><td><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'>$ordstatus</a></td><td><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'>$description</a></td><td style='text-align: right'><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'>" . $currency_symbol . number_format($value, 2) . "</a></td></tr>";

        $i++;
      }
    }
    $outstandingordersbystatustable .= "<tr><th>&nbsp</th><th>Total</th><th  style='text-align: right'>" . $currency_symbol . number_format($tmp_total, 2) . "</th></tr>";
    $outstandingordersbystatusdata .= "]";

    $this->data["outstandingordersbystatustable"] = $outstandingordersbystatustable;
    $this->data["outstandingordersbystatusdata"] = $outstandingordersbystatusdata;
    $this->data["outstandingordersbystatuslegend"] = $outstandingordersbystatuslegend;
// Waiting Postin/////

    $waitingposting = $this->site_model->waitingposting($G_level, $G_todaysdate, $repclause, $G_branchno);

    $this->data["WaitingPostingCR"] = '0.00';
    $this->data["WaitingPostingSL"] = '0.00';

    foreach ($waitingposting as $wp) {
      if ($wp['identifier'] == "MIDASWAITSL") {
        $this->data["WaitingPostingSL"] = $wp['sum1'];
      }
      if ($wp['identifier'] == "MIDASWAITCR") {
        $this->data["WaitingPostingCR"] = $wp['sum1'];
      }
    }


    /*$tmp_identifier = $waitingposting["identifier"];
$tmp_value 		= number_format($waitingposting["sum1"],2);

if ($tmp_identifier =="MIDASWAITSL")
{
  $this->data["WaitingPostingCR"]="0.00";
  $this->data["WaitingPostingSL"] = $tmp_value;
}
else
{
  $this->data["WaitingPostingSL"] ="0.00";
  $this->data["WaitingPostingCR"] = $tmp_value;
}*/
//Waiting Posting End//


    //-----------------END Outstanding Ordrs---------------------//
    // HELD IN OMR


    $heldinomr = $this->site_model->heldInomr($G_level, $G_todaysdate, $repclause, $G_branchno);

    /*$tmp_identifier = $heldinomr["identifier"];
$tmp_value 		= number_format($heldinomr["actualvalue1"],2);*/
    $this->data["HeldInOMRCR"] = '0.00';
    $this->data["HeldInOMRSL"] = '0.00';

    foreach ($heldinomr as $hr) {
      if ($hr['identifier'] == "MIDASHELDOMRSL") {
        $this->data["HeldInOMRSL"] = $hr['actualvalue1'];
      }
      if ($hr['identifier'] == "MIDASHELDOMRCR") {
        $this->data["HeldInOMRCR"] = $hr['actualvalue1'];
      }
    }
// ------------------------------//End HELD IN OMR-----------------------------------------------------------------------------------------------------


    //-----------------END Outstanding Ordrs---------------------//

    // Posted

    $postedOmr = $this->site_model->postedOmr($G_level, $G_todaysdate, $repclause, $G_branchno);

    $this->data["PostedCR"] = '0.00';
    $this->data["PostedSL"] = '0.00';

    foreach ($postedOmr as $Pr) {
      if ($Pr['identifier'] == "MIDASPOSTEDSL") {
        $this->data["PostedSL"] = $Pr['actualvalue1'];
      }
      if ($Pr['identifier'] == "MIDASPOSTEDCR") {
        $this->data["PostedCR"] = $Pr['actualvalue1'];
      }
    }

    /* $tmp_identifier = $postedOmr["actualvalue1"];
$tmp_value 		= number_format($postedOmr["actualvalue1"],2);

if ($tmp_identifier =="MIDASPOSTEDSL")
{	$this->data["PostedCR"]="0.00";
  $this->data["PostedSL"] = $tmp_value;
}
else
{		$this->data["PostedSL"]="0.00";
  $this->data["PostedCR"] = $tmp_value;
}*/


// ------------------------------//End Posted-----------------------------------------------------------------------------------------------------


    // GET THE DAY NUMBER AND WORKING DAYS
    $date = $G_todaysdate;
    $this->data["$G_todaysdate"] = $G_todaysdate;
    // -------------------------------------------------------------------------------------------------------------------------------------------------
    $row = $this->site_model->workingDays($date);
    $this->data['dayno'] = $row['dayno'];  // Current working day number
    $this->data['totdays'] = $row['totdays']; // Total number of working days in the month

    // -------------------------------------------------------------------------------------------------------------------------------------------------
    // GET THE BRANCH NAME
    // -------------------------------------------------------------------------------------------------------------------------------------------------

    $this->data['branchname'] = $this->site_model->getBranch($this->data['branchno']);

    // Get the start of this month two years ago, to get two years of data
    // CR0001 - Changed from rolling 36 months to Jan-Dec for last 3 years

    $this->data['year0'] = date("Y");
    $this->data['year1'] = $this->data['year0'] - 1;
    $this->data['year2'] = $this->data['year0'] - 2;
    $this->data['year3'] = $this->data['year0'] - 3;

    $this->data['thismonth'] = date("m");

    $this->data['graphlabel0'] = $this->data['year0']; // CR0001 $year1 ."-" . $year0;
    $this->data['graphlabel1'] = $this->data['year1']; // CR0001 $year2 ."-" . $year1;
    $this->data['graphlabel2'] = $this->data['year2']; // CR0001 $year3 ."-" . $year2;

    $this->data['startdate'] = $this->data['year2'] . "01-01"; // CR0001"$year3 ."-" . $thismonth ."-" . $daysinmonth;

    $this->data['startyearmonth'] = ($this->data['year2'] * 100) + 1; // CR0001 ($year3 * 100) + $thismonth;
    $this->data['curyearmonth'] = ($this->data['year0'] * 100) + $this->data['thismonth']; // e.g. 201507

    $this->data['yearstartmonth'] = $this->site_model->getYearStartMonth();
    $start_month_delta = $this->data['yearstartmonth'] <= date('m') ? 11 + $this->data['yearstartmonth'] : $this->data['yearstartmonth'] - 1;

    // -------------------------------------------------------------------------------------------------------------------------------------------------
    // GET THE MONTHLY SALES REP TARGETS
    // -------------------------------------------------------------------------------------------------------------------------------------------------


    //	echo $G_userid;

    $this->data["year"] = $year;
    $soyyearmonth = ($this->data["year"] * 100) + 1;
    $row = $this->site_model->getmonthlySalesRepTarget($soyyearmonth, $G_userid, $G_branchno, $G_level);

    $row1 = $this->site_model->getSalesRepTarget($this->data['curyearmonth'], $G_userid, $G_branchno, $G_level);

    $targetyearmonth = $row['yearmonth'];
    $tmp_monthlysalestarget = $this->data['monthlysalestarget'] = $row['salesTarget'] = $row['saletarget'];
    $this->data["G_YearlySalesTarget"] += $G_YearlySalesTarget += $tmp_monthlysalestarget;
    $this->data['G_MonthlySalesTarget'] = $row1['salesTarget'] = $row1['saletarget'];
//print_r($this->data); exit;
    //7-9-2017 ash
    $targetUserId = $G_userid;
    if (!empty($headerUserId)) {
      $targetUserId = $headerUserId;
    }
    $targetDataMonth = $this->site_model->getMonthTargetData($this->data['curyearmonth'], $targetUserId, $G_branchno, $G_level);
    $targetDataYear = $this->site_model->getYearTargetData($targetUserId, $G_branchno, $G_level);
    $this->data['G_MonthlySalesTarget'] = $targetDataMonth['saletarget'];
    $this->data["G_YearlySalesTarget"] = $targetDataYear['saletarget'];
    //7-9-2017 ash

    //	print_r($this->data); exit;
    $this->data['marginok'] = $row['marginok'];
    $this->data['margingood'] = $row['margingood'];

    if ($this->data['totdays'] <> 0) {
      $this->data['dailysalestarget'] = $this->data['monthlysalestarget'] / $this->data['totdays'];
    }

    // Initialise sales array

    $this->data['yearmonth'] = array();
    $this->data['monthnames'] = array();
    $this->data['sales'] = array();
    $this->data['costs'] = array();

    // Preload the year and month into an array so that we can make sure we load the sales against the correct row. Pad the month with leading 0 if needed. Had an example where
    // a rep started more recently that three years ago, and therefore there was less than 36 months. It was loading all these into the start of the array, rather than against the
    // appropriate row.

    $this->data['tmpyear'] = $this->data['year3']; //CR0001 $year3;
    $this->data['tmpmonth'] = 1; // CR0001 $thismonth + 1;

    for ($x = 0; $x < 48; $x++) {
      $this->data['yearmonth'][$x] = ($this->data['tmpyear'] * 100) + $this->data['tmpmonth'];

      $this->data['sales'][$x] = 0;
      $this->data['costs'][$x] = 0;

      $this->data['tmpmonth'] = $this->data['tmpmonth'] + 1;
      if ($this->data['tmpmonth'] == 13) {
        $this->data['tmpmonth'] = 1;
        $this->data['tmpyear'] = $this->data['tmpyear'] + 1;
      }
    }
    // Get sales for the sales rep
    $result = $this->site_model->getSalesAnalisys($this->data['curyearmonth'], $this->data['userDetail']['repwhere'], $G_userid, $G_branchno, $G_level);

    $x = 0;

    foreach ($result as $row) {
      $this->data['salessummaryyearmonth'] = $row['yearmonth'];
      $this->data['salessummarysales'] = $row['sales'];
      $this->data['salessummarycost'] = $row['cost'];

      // For each data row, loop through the array and put the sales value in the correct place

      for ($x = 0; $x < 48; $x++) {
        if ($this->data['yearmonth'][$x] == $this->data['salessummaryyearmonth']) {
          $this->data['sales'][$x] = $this->data['salessummarysales']; // If the year month of the data matches the array, put the value in
          $this->data['costs'][$x] = $this->data['salessummarycost'];
        }
      }
    }

    $this->data['salesTargetForLastThreeYear'] = $this->site_model->getSalesTargetForLastThreeYear($G_level, $this->data['yearmonth'], $this->data['sales'], $targetUserId, $G_branchno);
    $this->data['targetDataForCurrentYear'] = $this->site_model->GetTargetDataForCurrentYear($this->data['salesTargetForLastThreeYear']);
    $this->data['cumulativeTargetDataForCurrentYear'] = $this->site_model->GetCumulativeTargetDataForCurrentYear($this->data['salesTargetForLastThreeYear']);

    $this->data['year0data'] = $this->site_model->GetYearData($this->data['sales'], 24 + $start_month_delta, 35 + $start_month_delta);
    $this->data["year0total"] = $this->site_model->GetYearTotal($this->data['sales'], 24 + $start_month_delta, 35 + $start_month_delta);
    $this->data["year0table"] = $this->site_model->GetYearTable($this->data['sales'], $this->data["year0total"], 24 + $start_month_delta, 35 + $start_month_delta);

    $this->data['year1data'] = $this->site_model->GetYearData($this->data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
    $this->data["year1total"] = $this->site_model->GetYearTotal($this->data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
    $this->data["year1table"] = $this->site_model->GetYearTable($this->data['sales'], $this->data["year1total"], 12 + $start_month_delta, 23 + $start_month_delta);

    $this->data['year2data'] = $this->site_model->GetYearData($this->data['sales'], $start_month_delta, 11 + $start_month_delta);
    $this->data["year2total"] = $this->site_model->GetYearTotal($this->data['sales'], $start_month_delta, 11 + $start_month_delta);
    $this->data["year2table"] = $this->site_model->GetYearTable($this->data['sales'], $this->data["year2total"], $start_month_delta, 11 + $start_month_delta);

    $this->data['year0ChartValues'] = $this->data['year0data'];
    $this->data['year1ChartValues'] = $this->data['year1data'];
    $this->data['year2ChartValues'] = $this->data['year2data'];
    $this->data['cumulativeYear0ChartValues'] = $this->site_model->GetCumulativeYearData($this->data['sales'], 24 + $start_month_delta, 35 + $start_month_delta);
    $this->data['cumulativeYear1ChartValues'] = $this->site_model->GetCumulativeYearData($this->data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
    $this->data['cumulativeYear2ChartValues'] = $this->site_model->GetCumulativeYearData($this->data['sales'], $start_month_delta, 11 + $start_month_delta);

    // getting targets of all years(14-03-2018)
    $threeYearTargetArray = $this->site_model->getThreeYearTargetArray($G_userid, $G_branchno, $G_level);
    // end getting targets of all years
    $this->data['monthlysales'] = $this->data['sales'][23 + $this->data['thismonth']];  // CR0001 $sales[35];
    $this->data['monthlycost'] = $this->data['costs'][23 + $this->data['thismonth']]; // CR0001 $costs[35];
    if ($this->data['G_MonthlySalesTarget'] != 0) {
      //echo $this->data['monthlysales'].'-'.$this->data['G_MonthlySalesTarget'] ;
      $this->data['monthlysalespc'] = ($this->data['monthlysales'] / $this->data['G_MonthlySalesTarget']) * 100;
    } else {
      $this->data['monthlysalespc'] = 0;
    }

    $this->data['monthlymargin'] = $this->data['monthlysales'] - $this->data['monthlycost'];
    if ($this->data['monthlysales'] <> 0) {
      $this->data['monthlymarginpc'] = ($this->data['monthlymargin'] / $this->data['monthlysales']) * 100;
    }

    // -------------------------------------------------------------------------------------------------------------------------------------------------
    // GET LAST SALES DATE FOR THE REP.
    // -------------------------------------------------------------------------------------------------------------------------------------------------
    $this->data['lastsalesdate'] = $this->site_model->getMaxDate($this->data['userDetail']['repwhere']);

    // -------------------------------------------------------------------------------------------------------------------------------------------------
    // GET LAST DAYS SALES FOR THE REP.
    // -------------------------------------------------------------------------------------------------------------------------------------------------

    $row = $this->site_model->getSalesRepLastSales($this->data['lastsalesdate'], $this->data['userDetail']['repwhere'], $G_userid, $G_branchno, $G_level);
    $this->data['dailysales'] = $row['sales'];
    $this->data['dailycost'] = $row['cost'];
    // Change the date to UK format for presentation

    $this->data['lastsalesdate'] = date('d/m/Y', strtotime($this->data['lastsalesdate'])); // UK format date

    if ($this->data['totdays'] <> 0) {
      $this->data['dailysalestarget'] = $this->data['monthlysalestarget'] / $this->data['totdays'];
    }


    $this->data['dailymargin'] = $this->data['dailysales'] - $this->data['dailycost'];
    if ($this->data['dailysales'] <> 0) {
      $this->data['dailymarginpc'] = ($this->data['dailymargin'] / $this->data['dailysales']) * 100;
    }


    if ($this->data['totdays'] <> 0) {
      $this->data['G_DailySalesTarget'] = $this->data['G_MonthlySalesTarget'] / $this->data['totdays'];
    }
    if ($this->data['G_DailySalesTarget'] <> 0) {
      $this->data['dailysalespc'] = ($this->data['dailysales'] / $this->data['G_DailySalesTarget']) * 100;
    }


    if ($this->data["dayno"] <> 0) {
      $this->data["projdaysales"] = ($this->data["monthlysales"] / $this->data["dayno"]);
      $this->data["projmonthsales"] = $this->data["projdaysales"] * $this->data["totdays"]; // Extrapolate projected sales
      if ($this->data["G_MonthlySalesTarget"] <> 0) {
        $this->data["projmonthsalespc"] = ($this->data["projmonthsales"] / $this->data["G_MonthlySalesTarget"]) * 100;
      }
    }

// Sales month to date, divided by the number of working days so far, multipled by the total number of working days in the month to plot where sales will be if they continue like this

    $cumday = $this->site_model->cumday($G_level, $G_todaysdate, $repclause, $G_branchno);


//print_r($cumday[0]["SUM(day01sales)"]);


    $cumday01sales = number_format($cumday[0]["SUM(day01sales)"], 0, '.', '');
    $cumday02sales = number_format($cumday[0]["SUM(day02sales)"] + $cumday01sales, 0, '.', '');
    $cumday03sales = number_format($cumday[0]["SUM(day03sales)"] + $cumday02sales, 0, '.', '');
    $cumday04sales = number_format($cumday[0]["SUM(day04sales)"] + $cumday03sales, 0, '.', '');
    $cumday05sales = number_format($cumday[0]["SUM(day05sales)"] + $cumday04sales, 0, '.', '');
    $cumday06sales = number_format($cumday[0]["SUM(day06sales)"] + $cumday05sales, 0, '.', '');
    $cumday07sales = number_format($cumday[0]["SUM(day07sales)"] + $cumday06sales, 0, '.', '');
    $cumday08sales = number_format($cumday[0]["SUM(day08sales)"] + $cumday07sales, 0, '.', '');
    $cumday09sales = number_format($cumday[0]["SUM(day09sales)"] + $cumday08sales, 0, '.', '');
    $cumday10sales = number_format($cumday[0]["SUM(day10sales)"] + $cumday09sales, 0, '.', '');
    $cumday11sales = number_format($cumday[0]["SUM(day11sales)"] + $cumday10sales, 0, '.', '');
    $cumday12sales = number_format($cumday[0]["SUM(day12sales)"] + $cumday11sales, 0, '.', '');
    $cumday13sales = number_format($cumday[0]["SUM(day13sales)"] + $cumday12sales, 0, '.', '');
    $cumday14sales = number_format($cumday[0]["SUM(day14sales)"] + $cumday13sales, 0, '.', '');
    $cumday15sales = number_format($cumday[0]["SUM(day15sales)"] + $cumday14sales, 0, '.', '');
    $cumday16sales = number_format($cumday[0]["SUM(day16sales)"] + $cumday15sales, 0, '.', '');
    $cumday17sales = number_format($cumday[0]["SUM(day17sales)"] + $cumday16sales, 0, '.', '');
    $cumday18sales = number_format($cumday[0]["SUM(day18sales)"] + $cumday17sales, 0, '.', '');
    $cumday19sales = number_format($cumday[0]["SUM(day19sales)"] + $cumday18sales, 0, '.', '');
    $cumday20sales = number_format($cumday[0]["SUM(day20sales)"] + $cumday19sales, 0, '.', '');
    $cumday21sales = number_format($cumday[0]["SUM(day21sales)"] + $cumday20sales, 0, '.', '');
    $cumday22sales = number_format($cumday[0]["SUM(day22sales)"] + $cumday21sales, 0, '.', '');
    $cumday23sales = number_format($cumday[0]["SUM(day23sales)"] + $cumday22sales, 0, '.', '');
    $cumday24sales = number_format($cumday[0]["SUM(day24sales)"] + $cumday23sales, 0, '.', '');
    $cumday25sales = number_format($cumday[0]["SUM(day25sales)"] + $cumday24sales, 0, '.', '');
    $cumday26sales = number_format($cumday[0]["SUM(day26sales)"] + $cumday25sales, 0, '.', '');
    $cumday27sales = number_format($cumday[0]["SUM(day27sales)"] + $cumday26sales, 0, '.', '');
    $cumday28sales = number_format($cumday[0]["SUM(day28sales)"] + $cumday27sales, 0, '.', '');
    $cumday29sales = number_format($cumday[0]["SUM(day29sales)"] + $cumday28sales, 0, '.', '');
    $cumday30sales = number_format($cumday[0]["SUM(day30sales)"] + $cumday29sales, 0, '.', '');
    $cumday31sales = number_format($cumday[0]["SUM(day31sales)"] + $cumday30sales, 0, '.', '');

    //------------------------//End Posted-----------------------------------------------------------------------------------------------------

    $this->data["ProjectedSalesMonthGraphActual"] = "[$cumday01sales,$cumday02sales,$cumday03sales,$cumday04sales,$cumday05sales,$cumday06sales,$cumday07sales,$cumday08sales,$cumday09sales,$cumday10sales,
							$cumday11sales,$cumday12sales,$cumday13sales,$cumday14sales,$cumday15sales,$cumday16sales,$cumday17sales,$cumday18sales,$cumday19sales,$cumday20sales,
							$cumday21sales,$cumday22sales,$cumday23sales,$cumday24sales,$cumday25sales,$cumday26sales,$cumday27sales,$cumday28sales,$cumday29sales,$cumday30sales,
							$cumday31sales]";


    // Build up the cumulative target and projected arrays

    $ProjectedSalesMonthGraphTarget = "[";
    $ProjectedSalesMonthGraphProjected = "[";
    $ProjectedSalesMonthGraphLabel = "[";
    $this->data["daysinmonth"] = $daysinmonth;
    for ($x = 1; $x <= $daysinmonth; $x++) {


      $cumulativetarget[$x] = ($this->data["G_MonthlySalesTarget"] / $this->data["daysinmonth"]) * $x;
      $cumulativeprojected[$x] = ($this->data["projmonthsales"] / $this->data["daysinmonth"]) * $x;

      $ProjectedSalesMonthGraphTarget .= number_format($cumulativetarget[$x], 0, '.', '');
      $ProjectedSalesMonthGraphProjected .= number_format($cumulativeprojected[$x], 0, '.', '');

      // Only putting the first and last day number in the label as its too busy with all the days

      if ($x == 1 or $x == $daysinmonth) {
        $ProjectedSalesMonthGraphLabel .= "'$x'";
      } else {
        $ProjectedSalesMonthGraphLabel .= "' '";
      }
      if ($x != $daysinmonth) {
        $ProjectedSalesMonthGraphTarget .= ",";
        $ProjectedSalesMonthGraphProjected .= ",";
        $ProjectedSalesMonthGraphLabel .= ",";
      }
    }

    $ProjectedSalesMonthGraphTarget .= "]";
    $ProjectedSalesMonthGraphProjected .= "]";
    $ProjectedSalesMonthGraphLabel .= ",' ']";
    //$ProjectedSalesMonthGraphLabel .= ",' ']";


    $this->data["ProjectedSalesMonthGraphTarget"] = $ProjectedSalesMonthGraphTarget;
    $this->data["ProjectedSalesMonthGraphProjected"] = $ProjectedSalesMonthGraphProjected;
    $this->data["ProjectedSalesMonthGraphLabel"] = $ProjectedSalesMonthGraphLabel;
    // --------------------------------------------------------------------------------------------------------------------------------------------------
    // PROJECTED SALES - YEAR CHART
    // --------------------------------------------------------------------------------------------------------------------------------------------------

    // Sales year to date, divided by the number of months so far, multipled by the total number of months in the year to plot where sales will be if they continue like this
    if ($year0total <> 0)    // From the 3 year chart
    {
      $this->data["projmonthsales"] = ($year0total / $month);
      $this->data["projyearsales"] = $this->data["projmonthsales"] * 12; // Extrapolate projected sales

      if (!is_null($this->data["G_YearlySalesTarget"]) && $this->data["G_YearlySalesTarget"] != 0) {
        $this->data["projyearsalespc"] = ($this->data["projyearsales"] / $this->data["G_YearlySalesTarget"]) * 100;
      }
    }
// Set the background colour. If the projection is greater than the target, then its green, otherwise its red
    if ($this->data["projyearsales"] > $this->data["G_YearlySalesTarget"]) {
      $this->data["ProjectedSalesYearGraphBg"] = "bg-green";
    } else {
      $this->data["ProjectedSalesYearGraphBg"] = "bg-red";
    }

    // Already have this years sales by month, from the 3 year chart, so build chart data from that

    $cummth01sales = number_format($this->data["sales"][24 + $start_month_delta], 0, '.', '');
    $cummth02sales = number_format($cummth01sales + $this->data["sales"][25 + $start_month_delta], 0, '.', '');
    $cummth03sales = number_format($cummth02sales + $this->data["sales"][26 + $start_month_delta], 0, '.', '');
    $cummth04sales = number_format($cummth03sales + $this->data["sales"][27 + $start_month_delta], 0, '.', '');
    $cummth05sales = number_format($cummth04sales + $this->data["sales"][28 + $start_month_delta], 0, '.', '');
    $cummth06sales = number_format($cummth05sales + $this->data["sales"][29 + $start_month_delta], 0, '.', '');
    $cummth07sales = number_format($cummth06sales + $this->data["sales"][30 + $start_month_delta], 0, '.', '');
    $cummth08sales = number_format($cummth07sales + $this->data["sales"][31 + $start_month_delta], 0, '.', '');
    $cummth09sales = number_format($cummth08sales + $this->data["sales"][32 + $start_month_delta], 0, '.', '');
    $cummth10sales = number_format($cummth09sales + $this->data["sales"][33 + $start_month_delta], 0, '.', '');
    $cummth11sales = number_format($cummth10sales + $this->data["sales"][34 + $start_month_delta], 0, '.', '');
    $cummth12sales = number_format($cummth11sales + $this->data["sales"][35 + $start_month_delta], 0, '.', '');

    // Something like this: sales [24070,36053,45000,53187,64540,64540,64540,75130,75130,75130, 75130,75130]
    $this->data["ProjectedSalesYearGraphActual"] = "[$cummth01sales,$cummth02sales,$cummth03sales,$cummth04sales,$cummth05sales,$cummth06sales,
                                                $cummth07sales,$cummth08sales,$cummth09sales,$cummth10sales,$cummth11sales,$cummth12sales]";

    // Build up the cumulative target and projected arrays

    $ProjectedSalesYearGraphTarget = "[";
    $ProjectedSalesYearGraphProjected = "[";

    for ($x = 1; $x <= 12; $x++) {
      $cumulativeprojected[$x] = ($this->data["projyearsales"] / 12) * $x;

      $ProjectedSalesYearGraphTarget .= number_format(($this->data["G_YearlySalesTarget"] / 12) * $x, 0, '.', '');
      $ProjectedSalesYearGraphProjected .= number_format(($this->data["projyearsales"] / 12) * $x, 0, '.', '');

      if ($x != 12) {
        $ProjectedSalesYearGraphTarget .= ",";
        $ProjectedSalesYearGraphProjected .= ",";
      }
    }

    $ProjectedSalesYearGraphTarget .= "]";
    $ProjectedSalesYearGraphProjected .= "]";


    $this->data["ProjectedSalesYearGraphTarget"] = $ProjectedSalesYearGraphTarget;
    $this->data["ProjectedSalesYearGraphProjected"] = $ProjectedSalesYearGraphProjected;

    // ORDERS FULFILLED CHART, USER, BRANCH OR COMPANT


    //END ORDERS FULFILLED CHART, USER, BRANCH OR COMPANT

    $lastthirty = $this->site_model->lastthirty($G_level, $repclause, $G_branchno);


    $OrdersFulfilledGraph = "[";
    $OrdersFulfilledGraphLabel = "[";
    $y = 0;

    foreach ($lastthirty as $lasth) {

      $fulfilleddate = $lasth["date"];
      $fulfilledlines = $lasth["sum1"];
      $totallines = $lasth["sum2"];

      if ($totallines <> 0) {
        $percentage = ($fulfilledlines / $totallines) * 100;
      }

      if ($y <> 0) {
        $OrdersFulfilledGraph .= ",";
        $OrdersFulfilledGraphLabel .= ",";
      }

      $OrdersFulfilledGraph .= number_format($percentage, 2);

      // Label will be day/month like 07/1204020

      $tmp_month = date("m", strtotime($fulfilleddate));
      $tmp_day = date("d", strtotime($fulfilleddate));
      $tmp_daymonth = $tmp_day . "/" . $tmp_month;

      $OrdersFulfilledGraphLabel .= "'$tmp_daymonth'";
      $y++;
    }
    $OrdersFulfilledGraph .= "]";
    $OrdersFulfilledGraphLabel .= "]";

    $this->data["OrdersFulfilledGraph"] = $OrdersFulfilledGraph;
    $this->data["OrdersFulfilledGraphLabel"] = $OrdersFulfilledGraphLabel;

    // pac dashboard code

    $this->data['pac1salestarget'] = $this->site_model->getPac1SalesTargetDashboard($G_level, $targetUserId, $branchNo, $repclause);
    $this->data['getSalesTotalMonthWise'] = $this->site_model->getSalesTotalMonthWise($G_level, $targetUserId, $branchNo, $repclause);

    // Quotations x PAC1
    $this->data['currentMonthPac1QuoteConversions'] = $this->site_model->getPac1QuoteConversionForCurrentMonth($selectedUserDetails['repwhere']);

    // Sales Pipeline
    $this->data['salesPipelineStages'] = $this->site_model->getSalesPipelineStages($selectedUserDetails['repwhere']);

    $this->load->view('origin_dashboard', $this->data);
  }


  public function dashboard_first() {
    $data = array();
    $data["canSeeMargins"] = $this->canSeeMargins;
    $this->load->helper('cookie');

    $currency_symbol = $this->config->item("currency_symbol");
    if ($this->site_model->is_logged_in() == false) {
      redirect('/');
    }
    $user_id = $this->session->userdata('userid');
    $G_todaysdate = date("Y/m/d");

    $year = date("Y", strtotime($G_todaysdate));

    $data['userDetail'] = $this->site_model->getUserDetails($user_id);
    $userType  = $data['userDetail']['usertype'];

    $userId = 0;
    $branchNo = 0;

    $selectedUser = $this->session->userdata('selectedUser');
    if (count($selectedUser) > 0) {
      $UserSes = $selectedUser;
      $userId = $UserSes["userid"];
    }

    $headerUserId = $userId;

    $selectedBranch = $this->session->userdata('selectedBranch');
    if (count($selectedBranch) > 0) {
      $branchNo = $selectedBranch["branchno"];
    }

    $G_branchno = null;

    if ($userType == "B") {
      $G_level = "Branch";
    } elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0)) {
      $G_level = "Company";
    } elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0)) {
      $G_level = "Branch";
      $G_branchno = $branchNo;
    } elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0)) {
      $G_level = "User";
      $G_userId = $userId;
      //  $data['userDetail'] = $this->site_model->getUserDetails($userId);
    } else {
      $G_level = "User";
    }
    $G_userid = $this->session->userdata("userid");
    // GET THE DAY NUMBER AND WORKING DAYS
    $date = $G_todaysdate;
    $data["$G_todaysdate"] = $G_todaysdate;
    // -------------------------------------------------------------------------------------------------------------------------------------------------
    $row = $this->site_model->workingDays($date);
    $data['totdays'] = $row['totdays']; // Total number of working days in the month

    $data['year0'] = date("Y");
    $data['year3'] = $data['year0'] - 3;

    $data['thismonth'] = date("m");
    $data['curyearmonth'] = ($data['year0'] * 100) + $data['thismonth']; // e.g. 201507

    $data["year"] = $year;
    $soyyearmonth = ($data["year"] * 100) + 1;
    $row = $this->site_model->getmonthlySalesRepTarget($soyyearmonth, $G_userid, $G_branchno, $G_level);

    $row1 = $this->site_model->getSalesRepTarget($data['curyearmonth'], $G_userid, $G_branchno, $G_level);

    $tmp_monthlysalestarget = $data['monthlysalestarget'] = $row['salesTarget'] = $row['saletarget'];
    $data["G_YearlySalesTarget"] += $G_YearlySalesTarget += $tmp_monthlysalestarget;
    $data['G_MonthlySalesTarget'] = $row1['salesTarget'] = $row1['saletarget'];
//print_r($data); exit;
    //7-9-2017 ash
    $targetUserId = $G_userid;
    if (!empty($headerUserId)) {
      $targetUserId = $headerUserId;
    }
    $targetDataMonth = $this->site_model->getMonthTargetData($data['curyearmonth'], $targetUserId, $G_branchno, $G_level);
    $targetDataYear = $this->site_model->getYearTargetData($targetUserId, $G_branchno, $G_level);
    $data['G_MonthlySalesTarget'] = $targetDataMonth['saletarget'];

    if ($data['totdays'] <> 0) {
      $data['dailysalestarget'] = $data['monthlysalestarget'] / $data['totdays'];
    }

    // Initialise sales array

    $data['yearmonth'] = array();
    $data['sales'] = array();
    $data['costs'] = array();

    // Preload the year and month into an array so that we can make sure we load the sales against the correct row. Pad the month with leading 0 if needed. Had an example where
    // a rep started more recently that three years ago, and therefore there was less than 36 months. It was loading all these into the start of the array, rather than against the
    // appropriate row.

    $data['tmpyear'] = $data['year3']; //CR0001 $year3;
    $data['tmpmonth'] = 1; // CR0001 $thismonth + 1;

    for ($x = 0; $x < 48; $x++) {
      $data['yearmonth'][$x] = ($data['tmpyear'] * 100) + $data['tmpmonth'];

      $data['sales'][$x] = 0;
      $data['costs'][$x] = 0;

      $data['tmpmonth'] = $data['tmpmonth'] + 1;
      if ($data['tmpmonth'] == 13) {
        $data['tmpmonth'] = 1;
        $data['tmpyear'] = $data['tmpyear'] + 1;
      }
    }
    // Get sales for the sales rep
    $result = $this->site_model->getSalesAnalisys($data['curyearmonth'], $data['userDetail']['repwhere'], $G_userid, $G_branchno, $G_level);

    $x = 0;

    foreach ($result as $row) {
      $data['salessummaryyearmonth'] = $row['yearmonth'];
      $data['salessummarysales'] = $row['sales'];
      $data['salessummarycost'] = $row['cost'];

      // For each data row, loop through the array and put the sales value in the correct place

      for ($x = 0; $x < 48; $x++) {
        if ($data['yearmonth'][$x] == $data['salessummaryyearmonth']) {
          $data['sales'][$x] = $data['salessummarysales']; // If the year month of the data matches the array, put the value in
          $data['costs'][$x] = $data['salessummarycost'];
        }
      }
    }

    $data['monthlysales'] = $data['sales'][23 + $data['thismonth']];  // CR0001 $sales[35];
    $data['monthlycost'] = $data['costs'][23 + $data['thismonth']]; // CR0001 $costs[35];
    if ($data['G_MonthlySalesTarget'] != 0) {
      //echo $data['monthlysales'].'-'.$data['G_MonthlySalesTarget'] ;
      $data['monthlysalespc'] = ($data['monthlysales'] / $data['G_MonthlySalesTarget']) * 100;
    } else {
      $data['monthlysalespc'] = 0;
    }

    $data['monthlymargin'] = $data['monthlysales'] - $data['monthlycost'];
    if ($data['monthlysales'] <> 0) {
      $data['monthlymarginpc'] = ($data['monthlymargin'] / $data['monthlysales']) * 100;
    }


    $data['lastsalesdate'] = $this->site_model->getMaxDate($data['userDetail']['repwhere']);
    $row = $this->site_model->getSalesRepLastSales($data['lastsalesdate'], $data['userDetail']['repwhere'], $G_userid, $G_branchno, $G_level);
    $data['dailysales'] = $row['sales'];
    $data['dailycost'] = $row['cost'];
    // Change the date to UK format for presentation

    $data['lastsalesdate'] = date('d/m/Y', strtotime($data['lastsalesdate'])); // UK format date

    $data['dailymargin'] = $data['dailysales'] - $data['dailycost'];
    if ($data['dailysales'] <> 0) {
      $data['dailymarginpc'] = ($data['dailymargin'] / $data['dailysales']) * 100;
    }

    if ($data['totdays'] <> 0) {
      $data['G_DailySalesTarget'] = $data['G_MonthlySalesTarget'] / $data['totdays'];
    }
    if ($data['G_DailySalesTarget'] <> 0) {
      $data['dailysalespc'] = ($data['dailysales'] / $data['G_DailySalesTarget']) * 100;
    }
    $this->load->view('dashboard_first', $data);
  }

  public function dashboard_second_left() {
    $data = array();
    $currency_symbol = $this->config->item("currency_symbol");
    $user_id = $this->session->userdata('userid');
    $G_todaysdate = date("Y/m/d");

    $daysinmonth = date("t", strtotime($G_todaysdate));
    $year = date("Y", strtotime($G_todaysdate));
    $month = date("m", strtotime($G_todaysdate));
    $eomtemp = $year . '/' . $month . '/' . $daysinmonth;
    $eom = date('Y/m/d', strtotime($eomtemp));
    $ukeom = date('d/m/Y', strtotime($eomtemp));
    $data['userDetail'] = $this->site_model->getUserDetails($user_id);
    $userType = $data['userType'] = $data['userDetail']['usertype'];

    $userId = 0;
    $branchNo = 0;
    $headerUserId = 0;
    $selectedUserDetails = array('repwhere' => "");


    $selectedUser = $this->session->userdata('selectedUser');
    if (count($selectedUser) > 0) {
      $UserSes = $selectedUser;
      $userId = $UserSes["userid"];
      // $selectedUserDetails = $this->site_model->getUserDetails($userId);
      $selectedUserDetails = $data['userDetail'];
    }

    $headerUserId = $userId;

    $selectedBranch = $this->session->userdata('selectedBranch');
    if (count($selectedBranch) > 0) {
      //$branchSes = $this->session->userdata('selectedBranch');
      $branchNo = $selectedBranch["branchno"];
    }

    $G_branchno = null;

    if ($userType == "B") {
      $G_level = "Branch";
    } elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0)) {
      $G_level = "Company";
    } elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0)) {
      $G_level = "Branch";
      $G_branchno = $branchNo;
    } elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0)) {
      $G_level = "User";
      $G_userId = $userId;
      //  $data['userDetail'] = $this->site_model->getUserDetails($userId);
    } else {
      $G_level = "User";
    }

    $repclause = $data["userDetail"]["repclause"];

/// KPI last Update///
    $kpiLastupdate_val = $this->site_model->kpiLastupdate();
    $data["G_KPIsLastUpdatedDateTime"] = $kpiLastupdate_val["kpislastupdated"];

// END KPI last Update///

// GET THE DAY NUMBER AND WORKING DAYS//
    $kworkingDays = $this->site_model->workingDays($G_todaysdate);
    $data["dayno"] = $kworkingDays['dayno'];  // Current working day number
    $data["totdays"] = $kworkingDays['totdays']; // Total number of working days in the month


// END GET THE DAY NUMBER AND WORKING DAYS//


// Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set

// END Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set
// Get TODAYS ORDERS BY TYPE PIE CHART ///

    if (is_null($G_branchno)) {
      $G_branchno = $data['userDetail']['branch'];
    }

    $todayOrders = $this->site_model->todayOrders($G_level, $G_todaysdate, $repclause, $G_branchno);

    $BIColour = "#3c8dbc";    // Book Ins         Light Blue
    $BOColour = "#f39c12";    // Book Outs        Yellow
    $BTColour = "#001f3f";    // Branch Transfers Navy
    $CRColour = "#dd4b39";    // Credits          Red
    $DNColour = "#39cccc";    // Debit Notes      Teal
    $QTColour = "#00c0ef";    // Quotations       Aqua
    $SLColour = "#00a65a";    // Orders           Green
    $WOColour = "#d2d6de";    // Works Orders     Gray
    $RWColour = "#f44295";
    $TCColour = "#7a1919";
    $THColour = "#4f5960";

// Assign legend colours to order types

    $BITextColour = "text-light-blue";    // Book Ins         Light Blue
    $BOTextColour = "text-yellow";        // Book Outs        Yellow
    $BTTextColour = "text-navy";        // Branch Transfers   Navy
    $CRTextColour = "text-red";            // Credits         Red
    $DNTextColour = "text-teal";        // Debit Notes    Teal
    $QTTextColour = "text-aqua";        // Quotations         Aqua
    $SLTextColour = "text-green";        // Orders            Green
    $WOTextColour = "text-gray";        // Works Orders   Gray
    $RWTextColour = "text-rwcolor";
    $TCTextColour = "text-tccolor";
    $THTextColour = "text-thcolor";
    $todaysordersbytypelegend = '';
    $todaysordersbystatuslegend = '';
    $todaysordersbytypetable = '';
    $todaysordersbystatustable = '';
    $i = 1;
    $tmp_total = 0;

    foreach ($todayOrders as $today) {
      $identifier = $today['identifier'];
      $value = $today['actualvalue1'];
      // The order type is the last two characters of the identifier
      $ordtype = substr($identifier, 10, 2);
      // Only interested in graphing order types that have a value
      if ($value <> 0) {
        $tmp_total += $value;

        // Set the colour, which is the order type followed by"Colour"
        switch ($ordtype) {
          case"BI":
            $colour = $BIColour;
            $textcolour = $BITextColour;
            $description = "Book Ins";
            break;
          case"BO":
            $colour = $BOColour;
            $textcolour = $BOTextColour;
            $description = "Book Outs";
            break;
          case"BT":
            $colour = $BTColour;
            $textcolour = $BTTextColour;
            $description = "Branch Transfers";
            break;
          case"CR":
            $colour = $CRColour;
            $textcolour = $CRTextColour;
            $description = "Credit Notes";
            break;
          case"DN":
            $colour = $DNColour;
            $textcolour = $DNTextColour;
            $description = "Debit Notes";
            break;
          case"QT":
            $colour = $QTColour;
            $textcolour = $QTTextColour;
            $description = "Quotations";
            break;
          case"SL":
            $colour = $SLColour;
            $textcolour = $SLTextColour;
            $description = "Sales Orders";
            break;
          case"WO":
            $colour = $WOColour;
            $textcolour = $WOTextColour;
            $description = "Works Orders";
            break;
          case"RW":
            $colour = $RWColour;
            $textcolour = $RWTextColour;
            $description = "Repairs & Warranty";
            break;
          case"TC":
            $colour = $TCColour;
            $textcolour = $TCTextColour;
            $description = "Plant Hire Credit Note";
            break;
          case"TH":
            $colour = $THColour;
            $textcolour = $THTextColour;
            $description = "Plant Hire Order";
            break;
        }

        // Build the string for the legend
        $typeLink = site_url("site/todaysorder/" . $ordtype . "/type");
        $todaysordersbytypelegend .= "<li><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'><i class='fa fa-circle-o $textcolour'></i> $ordtype</a></li>";
        // Build the string for the table
        $todaysordersbytypetable .= "<tr><td><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>$ordtype</a></td><td><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>$description</a></td><td align='right'><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>" . $currency_symbol . number_format($value, 2) . "</a></td></tr>";

        $i++;
      }
    }
    $todaysordersbytypetable .= "<tr><th>&nbsp</th><th>Total</th><th style='text-align: right'>" . $currency_symbol . number_format($tmp_total, 2) . "</th></tr>";

    $data["todaysordersbytypelegend"] = $todaysordersbytypelegend;
    $data["todaysordersbytypetable"] = $todaysordersbytypetable;


    $userDetailAsKpi = array($data['userDetail']);
    $data = GetKpiDataForTwoYearVsTargetChart($userDetailAsKpi, $data, $G_level);

    $todayOrders = $this->site_model->todayOrdersStatus($G_level, $G_todaysdate, $repclause, $G_branchno);
    $ADVColour = "#f012be";    // Waiting advice note Fuschia
    $COMColour = "#00a65a";    // Completed line      Green
    $CUSColour = "#39cccc";    // Call customer back  Teal
    $HLDColour = "#3d9970";    // Goods on hold       Olive
    $IBTColour = "#d2d6de";    // Inter-branch transfer   Gray
    $KITColour = "#01ff70";    // Process kit list        Lime
    $MEMColour = "#ff851b";    // Memo line           Orange
    $OFFColour = "#605ca8";    // Call off later      Purple
    $PIKColour = "#001f3f";    // Pick note printed   Navy
    $PROColour = "#3c8dbc";    // Process document        Light Blue
    $PURColour = "#dd4b39";    // Purchase order      Red
    $SBOColour = "#f39c12";    // Stock backorder     Yellow
    $WDLColour = "#00c0ef";    // Waiting delivery        Aqua
    $WRKColour = "#d81b60";    // Create works order  Maroon

    $ADVTextColour = "text-fuschia";    // Waiting advice note    Fuschia
    $COMTextColour = "text-green";        // Completed line       Green
    $CUSTextColour = "text-teal";        // Call customer back    Teal
    $HLDTextColour = "text-olive";        // Goods on hold        Olive
    $IBTTextColour = "text-gray";        // Inter-branch transfer Gray
    $KITTextColour = "text-lime";        // Process kit list      Lime
    $MEMTextColour = "text-orange";        // Memo line           Orange
    $OFFTextColour = "text-purple";        // Call off later      Purple
    $PIKTextColour = "text-navy";        // Pick note printed Navy
    $PROTextColour = "text-light-blue";    // Process document        Light Blue
    $PURTextColour = "text-red";        // Purchase order     Red
    $SBOTextColour = "text-yellow";        // Stock backorder     Yellow
    $WDLTextColour = "text-aqua";        // Waiting delivery      Aqua
    $WRKTextColour = "text-maroon";        // Cr


    $i = 1;
    $tmp_total = 0;

    foreach ($todayOrders as $today) {

      $identifier = $today['identifier'];
      $value = $today['actualvalue1'];

      $ordstatus = substr($identifier, 10, 3);
      if ($value <> 0) {
        $tmp_total += $value;

        switch ($ordstatus) {
          case"ADV":
            $colour = $ADVColour;
            $textcolour = $ADVTextColour;
            $description = "Waiting Advice Note";
            break;
          case"COM":
            $colour = $COMColour;
            $textcolour = $COMTextColour;
            $description = "Completed Line";
            break;
          case"CUS":
            $colour = $CUSColour;
            $textcolour = $CUSTextColour;
            $description = "Call Customer Back";
            break;
          case"HLD":
            $colour = $HLDColour;
            $textcolour = $HLDTextColour;
            $description = "Goods On Hold";
            break;
          case"IBT":
            $colour = $IBTColour;
            $textcolour = $IBTTextColour;
            $description = "Inter-Branch Transfer";
            break;
          case"KIT":
            $colour = $KITColour;
            $textcolour = $KITTextColour;
            $description = "Process Kit List";
            break;
          case"MEM":
            $colour = $MEMColour;
            $textcolour = $MEMTextColour;
            $description = "Memo Line (Quotations)";
            break;
          case"OFF":
            $colour = $OFFColour;
            $textcolour = $OFFTextColour;
            $description = "Call Off Later";
            break;
          case"PIK":
            $colour = $PIKColour;
            $textcolour = $PIKTextColour;
            $description = "Pick Note Printed";
            break;
          case"PRO":
            $colour = $PROColour;
            $textcolour = $PROTextColour;
            $description = "Process Document";
            break;
          case"PUR":
            $colour = $PURColour;
            $textcolour = $PURTextColour;
            $description = "Purchase Order";
            break;
          case"SBO":
            $colour = $SBOColour;
            $textcolour = $SBOTextColour;
            $description = "Stock Backorder";
            break;
          case"WDL":
            $colour = $WDLColour;
            $textcolour = $WDLTextColour;
            $description = "Waiting Delivery";
            break;
          case"WRK":
            $colour = $WRKColour;
            $textcolour = $WRKTextColour;
            $description = "Create Works Order";
            break;
        }
        $typeLinkStatus = site_url("site/todaysorder/" . $ordstatus . "/status");
        // Build the string for the legend
        $todaysordersbystatuslegend .= "<li><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'><i class='fa fa-circle-o " . $textcolour . "'></i> $ordstatus</a></li>";

        // Build the string for the table
        $todaysordersbystatustable .= "<tr><td><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>$ordstatus</a></td><td><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>$description</a></td><td align='right'><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>" . $currency_symbol . number_format($value, 2) . "</a></td></tr>";

        $i++;
      }
    }
    $todaysordersbystatustable .= "<tr><th>&nbsp</th><th>Total</th><th  style='text-align: right'>" . $currency_symbol . number_format($tmp_total, 2) . "</th></tr>";
    $data["todaysordersbystatustable"] = $todaysordersbystatustable;
    $data["todaysordersbystatuslegend"] = $todaysordersbystatuslegend;

    $this->load->view('dashboard_second_left', $data);
  }

  public function dashboard_second_right() {
    $data = array();

    $user_id = $this->session->userdata('userid');
    $G_todaysdate = date("Y/m/d");

    $data['userDetail'] = $this->site_model->getUserDetails($user_id);
    $userType = $data['userType'] = $data['userDetail']['usertype'];

    $userId = 0;
    $branchNo = 0;


    $selectedUser = $this->session->userdata('selectedUser');
    if (count($selectedUser) > 0) {
      $UserSes = $selectedUser;
      $userId = $UserSes["userid"];
      // $selectedUserDetails = $this->site_model->getUserDetails($userId);
      $selectedUserDetails = $data['userDetail'];
    }

    $headerUserId = $userId;

    $selectedBranch = $this->session->userdata('selectedBranch');
    if (count($selectedBranch) > 0) {
      //$branchSes = $this->session->userdata('selectedBranch');
      $branchNo = $selectedBranch["branchno"];
    }

    $G_branchno = null;

    if ($userType == "B") {
      $G_level = "Branch";
    } elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0)) {
      $G_level = "Company";
    } elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0)) {
      $G_level = "Branch";
      $G_branchno = $branchNo;
    } elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0)) {
      $G_level = "User";
      $G_userId = $userId;
      //  $data['userDetail'] = $this->site_model->getUserDetails($userId);
    } else {
      $G_level = "User";
    }

    $repclause = $data["userDetail"]["repclause"];


    if (is_null($G_branchno)) {
      $G_branchno = $data['userDetail']['branch'];
    }

    // This is a potential bug fix, for some reason $userKpi is returning an empty array and therefore not working with the following function
    $userDetailAsKpi = array($data['userDetail']);
    $data = GetKpiDataForTwoYearVsTargetChart($userDetailAsKpi, $data, $G_level);


    //-----------------Outstanding Ordrs---------------------//
    $outOrders = $this->site_model->outStandOrders($G_level, $G_todaysdate, $repclause, $G_branchno);

    $ADVColour = "#f012be";    // Waiting advice note	Fuschia
    $COMColour = "#00a65a";    // Completed line		Green
    $CUSColour = "#39cccc";    // Call customer back	Teal
    $HLDColour = "#3d9970";    // Goods on hold		Olive
    $IBTColour = "#d2d6de";    // Inter-branch transfer	Gray
    $KITColour = "#01ff70";    // Process kit list		Lime
    $MEMColour = "#ff851b";    // Memo line			Orange
    $OFFColour = "#605ca8";    // Call off later		Purple
    $PIKColour = "#001f3f";    // Pick note printed	Navy
    $PROColour = "#3c8dbc";    // Process document		Light Blue
    $PURColour = "#dd4b39";    // Purchase order		Red
    $SBOColour = "#f39c12";    // Stock backorder		Yellow
    $WDLColour = "#00c0ef";    // Waiting delivery		Aqua
    $WRKColour = "#d81b60";    // Create

    $ADVTextColour = "text-fuschia";        // Waiting advice note    Fuschia
    $COMTextColour = "text-green";        // Completed line       Green
    $CUSTextColour = "text-teal";        // Call customer back    Teal
    $HLDTextColour = "text-olive";        // Goods on hold        Olive
    $IBTTextColour = "text-gray";        // Inter-branch transfer Gray
    $KITTextColour = "text-lime";        // Process kit list      Lime
    $MEMTextColour = "text-orange";        // Memo line           Orange
    $OFFTextColour = "text-purple";        // Call off later      Purple
    $PIKTextColour = "text-navy";        // Pick note printed Navy
    $PROTextColour = "text-light-blue";    // Process document        Light Blue
    $PURTextColour = "text-red";            // Purchase order     Red
    $SBOTextColour = "text-yellow";        // Stock backorder     Yellow
    $WDLTextColour = "text-aqua";        // Waiting delivery      Aqua
    $WRKTextColour = "text-maroon";        // Create works order  Maroon

    // The pie chart data string looks something like this : [{value:179.80,color:'#dd4b39',highlight:'#dd4b39',label:'CR'},{value:1307.96,color:'#00a65a',highlight:'#00a65a',label:'SL'}]

    $outstandingordersbystatusdata = "[";

    $i = 1;
    $tmp_total = 0;

    foreach ($outOrders as $outor) {
      $identifier = $outor["identifier"];
      $value = $outor["actualvalue1"];

      // The order type is the last three characters of the identifier
      $ordstatus = substr($identifier, 10, 3);
      // Only interested in graphing order statuses that have a value

      if ($value <> 0) {
        $tmp_total += $value;

        // Set the colour, which is the order status followed by"Colour"
        switch ($ordstatus) {
          case"ADV":
            $colour = $ADVColour;
            $textcolour = $ADVTextColour;
            $description = "Waiting Advice Note";
            break;
          case"COM":
            $colour = $COMColour;
            $textcolour = $COMTextColour;
            $description = "Completed Line";
            break;
          case"CUS":
            $colour = $CUSColour;
            $textcolour = $CUSTextColour;
            $description = "Call Customer Back";
            break;
          case"HLD":
            $colour = $HLDColour;
            $textcolour = $HLDTextColour;
            $description = "Goods On Hold";
            break;
          case"IBT":
            $colour = $IBTColour;
            $textcolour = $IBTTextColour;
            $description = "Inter-Branch Transfer";
            break;
          case"KIT":
            $colour = $KITColour;
            $textcolour = $KITTextColour;
            $description = "Process Kit List";
            break;
          case"MEM":
            $colour = $MEMColour;
            $textcolour = $MEMTextColour;
            $description = "Memo Line (Quotations)";
            break;
          case"OFF":
            $colour = $OFFColour;
            $textcolour = $OFFTextColour;
            $description = "Call Off Later";
            break;
          case"PIK":
            $colour = $PIKColour;
            $textcolour = $PIKTextColour;
            $description = "Pick Note Printed";
            break;
          case"PRO":
            $colour = $PROColour;
            $textcolour = $PROTextColour;
            $description = "Process Document";
            break;
          case"PUR":
            $colour = $PURColour;
            $textcolour = $PURTextColour;
            $description = "Purchase Order";
            break;
          case"SBO":
            $colour = $SBOColour;
            $textcolour = $SBOTextColour;
            $description = "Stock Backorder";
            break;
          case"WDL":
            $colour = $WDLColour;
            $textcolour = $WDLTextColour;
            $description = "Waiting Delivery";
            break;
          case"WRK":
            $colour = $WRKColour;
            $textcolour = $WRKTextColour;
            $description = "Create Works Order";
            break;
        }

        // The comma only comes in after the first set

        if ($i <> 1) {
          $outstandingordersbystatusdata .= ",";
        }
        $outstandingLink = site_url("site/outstandingorder/" . $ordstatus . "/status");
        // Build the data string for the pie chart data
        $outstandingordersbystatusdata .= "{value:$value,color:'$colour',highlight:'$colour',label:'$ordstatus'}";

        // Build the string for the legend
        $outstandingordersbystatuslegend .= "<li><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'><i class='fa fa-circle-o $textcolour'></i> $ordstatus</a></li>";

        // Build the string for the table
        $outstandingordersbystatustable .= "<tr><td><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'>$ordstatus</a></td><td><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'>$description</a></td><td style='text-align: right'><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'>" . $currency_symbol . number_format($value, 2) . "</a></td></tr>";

        $i++;
      }
    }
    $outstandingordersbystatustable .= "<tr><th>&nbsp</th><th>Total</th><th  style='text-align: right'>" . $currency_symbol . number_format($tmp_total, 2) . "</th></tr>";
    $outstandingordersbystatusdata .= "]";

    $data["outstandingordersbystatustable"] = $outstandingordersbystatustable;
    $data["outstandingordersbystatusdata"] = $outstandingordersbystatusdata;
    $data["outstandingordersbystatuslegend"] = $outstandingordersbystatuslegend;
    $this->load->view('dashboard_second_right', $data);
  }

  public function dashboard_third() {

    $this->load->helper('cookie');
    //echo get_cookie('salestodaydonutcharts',true);

    if (isset($_COOKIE['salestodaydonutcharts'])) {
      $this->data['salestodaydonutcharts'] = get_cookie('salestodaydonutcharts', true);
    } else {
      $this->data['salestodaydonutcharts'] = 0;
    }

    if (isset($_COOKIE['outstandingordersdonutchart'])) {
      $this->data['outstandingordersdonutchart'] = get_cookie('outstandingordersdonutchart', true);
    } else {
      $this->data['outstandingordersdonutchart'] = 0;
    }

    if (isset($_COOKIE['threeyearsaleschart'])) {
      $this->data['threeyearsaleschart'] = get_cookie('threeyearsaleschart', true);
    } else {
      $this->data['threeyearsaleschart'] = 0;
    }

    $data = array();
    $currency_symbol = $this->config->item("currency_symbol");
    $user_id = $this->session->userdata('userid');
    $G_todaysdate = date("Y/m/d");

    $daysinmonth = date("t", strtotime($G_todaysdate));
    $year = date("Y", strtotime($G_todaysdate));
    $month = date("m", strtotime($G_todaysdate));
    $data['userDetail'] = $this->site_model->getUserDetails($user_id);
    $userType = $data['userType'] = $data['userDetail']['usertype'];

    $userId = 0;
    $branchNo = 0;


    $selectedUser = $this->session->userdata('selectedUser');
    if (count($selectedUser) > 0) {
      $UserSes = $selectedUser;
      $userId = $UserSes["userid"];
    }

    $headerUserId = $userId;

    $selectedBranch = $this->session->userdata('selectedBranch');
    if (count($selectedBranch) > 0) {
      $branchNo = $selectedBranch["branchno"];
    }

    $G_branchno = null;

    if ($userType == "B") {
      $G_level = "Branch";
    } elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0)) {
      $G_level = "Company";
    } elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0)) {
      $G_level = "Branch";
      $G_branchno = $branchNo;
    } elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0)) {
      $G_level = "User";
      $G_userId = $userId;
      //  $data['userDetail'] = $this->site_model->getUserDetails($userId);
    } else {
      $G_level = "User";
    }

    $repclause = $data["userDetail"]["repclause"];

    /// KPI last Update///
    $kpiLastupdate_val = $this->site_model->kpiLastupdate();
    $data["G_KPIsLastUpdatedDateTime"] = $kpiLastupdate_val["kpislastupdated"];

    // END KPI last Update///

    // GET THE DAY NUMBER AND WORKING DAYS//
    $kworkingDays = $this->site_model->workingDays($G_todaysdate);
    $data["dayno"] = $kworkingDays['dayno'];  // Current working day number
    $data["totdays"] = $kworkingDays['totdays']; // Total number of working days in the month

    if (is_null($G_branchno)) {
      $G_branchno = $data['userDetail']['branch'];
    }

    $G_userid = $this->session->userdata("userid");


    $data = GetKpiDataForTwoYearVsTargetChart($userDetailAsKpi, $data, $G_level);


    $date = $G_todaysdate;
    $data["$G_todaysdate"] = $G_todaysdate;

    $data['branchname'] = $this->site_model->getBranch($data['branchno']);

    $data['year0'] = date("Y");
    $data['year1'] = $data['year0'] - 1;
    $data['year2'] = $data['year0'] - 2;
    $data['year3'] = $data['year0'] - 3;

    $data['thismonth'] = date("m");

    $data['graphlabel0'] = $data['year0']; // CR0001 $year1 ."-" . $year0;
    $data['graphlabel1'] = $data['year1']; // CR0001 $year2 ."-" . $year1;
    $data['graphlabel2'] = $data['year2']; // CR0001 $year3 ."-" . $year2;

    $data['startdate'] = $data['year2'] . "01-01"; // CR0001"$year3 ."-" . $thismonth ."-" . $daysinmonth;

    $data['startyearmonth'] = ($data['year2'] * 100) + 1; // CR0001 ($year3 * 100) + $thismonth;
    $data['curyearmonth'] = ($data['year0'] * 100) + $data['thismonth']; // e.g. 201507

    $data['yearstartmonth'] = $this->site_model->getYearStartMonth();
    $start_month_delta = $data['yearstartmonth'] <= date('m') ? 11 + $data['yearstartmonth'] : $data['yearstartmonth'] - 1;


    $data["year"] = $year;
    $soyyearmonth = ($data["year"] * 100) + 1;
    $row = $this->site_model->getmonthlySalesRepTarget($soyyearmonth, $G_userid, $G_branchno, $G_level);

    $row1 = $this->site_model->getSalesRepTarget($data['curyearmonth'], $G_userid, $G_branchno, $G_level);

    $targetyearmonth = $row['yearmonth'];
    $tmp_monthlysalestarget = $data['monthlysalestarget'] = $row['salesTarget'] = $row['saletarget'];
    $data["G_YearlySalesTarget"] += $G_YearlySalesTarget += $tmp_monthlysalestarget;
    $data['G_MonthlySalesTarget'] = $row1['salesTarget'] = $row1['saletarget'];

    $targetUserId = $G_userid;
    if (!empty($headerUserId)) {
      $targetUserId = $headerUserId;
    }

    $targetDataMonth = $this->site_model->getMonthTargetData($data['curyearmonth'], $targetUserId, $G_branchno, $G_level);
    $targetDataYear = $this->site_model->getYearTargetData($targetUserId, $G_branchno, $G_level);
    $data['G_MonthlySalesTarget'] = $targetDataMonth['saletarget'];
    $data["G_YearlySalesTarget"] = $targetDataYear['saletarget'];
    // Initialise sales array

    $data['yearmonth'] = array();
    $data['monthnames'] = array();
    $data['sales'] = array();
    $data['costs'] = array();

    // Preload the year and month into an array so that we can make sure we load the sales against the correct row. Pad the month with leading 0 if needed. Had an example where
    // a rep started more recently that three years ago, and therefore there was less than 36 months. It was loading all these into the start of the array, rather than against the
    // appropriate row.

    $data['tmpyear'] = $data['year3']; //CR0001 $year3;
    $data['tmpmonth'] = 1; // CR0001 $thismonth + 1;

    for ($x = 0; $x < 48; $x++) {
      $data['yearmonth'][$x] = ($data['tmpyear'] * 100) + $data['tmpmonth'];

      $data['sales'][$x] = 0;
      $data['costs'][$x] = 0;

      $data['tmpmonth'] = $data['tmpmonth'] + 1;
      if ($data['tmpmonth'] == 13) {
        $data['tmpmonth'] = 1;
        $data['tmpyear'] = $data['tmpyear'] + 1;
      }
    }
    // Get sales for the sales rep
    $result = $this->site_model->getSalesAnalisys($data['curyearmonth'], $data['userDetail']['repwhere'], $G_userid, $G_branchno, $G_level);

    $x = 0;

    foreach ($result as $row) {
      $data['salessummaryyearmonth'] = $row['yearmonth'];
      $data['salessummarysales'] = $row['sales'];
      $data['salessummarycost'] = $row['cost'];

      // For each data row, loop through the array and put the sales value in the correct place

      for ($x = 0; $x < 48; $x++) {
        if ($data['yearmonth'][$x] == $data['salessummaryyearmonth']) {
          $data['sales'][$x] = $data['salessummarysales']; // If the year month of the data matches the array, put the value in
          $data['costs'][$x] = $data['salessummarycost'];
        }
      }
    }

    $data['salesTargetForLastThreeYear'] = $this->site_model->getSalesTargetForLastThreeYear($G_level, $data['yearmonth'], $data['sales'], $targetUserId, $G_branchno);
    $data['targetDataForCurrentYear'] = $this->site_model->GetTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);
    $data['cumulativeTargetDataForCurrentYear'] = $this->site_model->GetCumulativeTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);

    $data['year0data'] = $this->site_model->GetYearData($data['sales'], 24 + $start_month_delta, 35 + $start_month_delta);
    $data["year0total"] = $this->site_model->GetYearTotal($data['sales'], 24 + $start_month_delta, 35 + $start_month_delta);
    $data["year0table"] = $this->site_model->GetYearTable($data['sales'], $data["year0total"], 24 + $start_month_delta, 35 + $start_month_delta);

    $data['year1data'] = $this->site_model->GetYearData($data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
    $data["year1total"] = $this->site_model->GetYearTotal($data['sales'], 12 + $start_month_delta, 23 + $start_month_delta);
    $data["year1table"] = $this->site_model->GetYearTable($data['sales'], $data["year1total"], 12 + $start_month_delta, 23 + $start_month_delta);

    $data['year2data'] = $this->site_model->GetYearData($data['sales'], $start_month_delta, 11 + $start_month_delta);
    $data["year2total"] = $this->site_model->GetYearTotal($data['sales'], $start_month_delta, 11 + $start_month_delta);
    $data["year2table"] = $this->site_model->GetYearTable($data['sales'], $data["year2total"], $start_month_delta, 11 + $start_month_delta);

    $data['year0ChartValues'] = $data['year0data'];
    $data['year1ChartValues'] = $data['year1data'];
    $data['year2ChartValues'] = $data['year2data'];
    $data['cumulativeYear0ChartValues'] = $this->site_model->GetCumulativeYearData($data["sales"], 24 + $start_month_delta, 35 + $start_month_delta);
    $data['cumulativeYear1ChartValues'] = $this->site_model->GetCumulativeYearData($data["sales"], 12 + $start_month_delta, 23 + $start_month_delta);
    $data['cumulativeYear2ChartValues'] = $this->site_model->GetCumulativeYearData($data["sales"], $start_month_delta, 11 + $start_month_delta);


    $threeYearTargetArray = $this->site_model->getThreeYearTargetArray($G_userid, $G_branchno, $G_level);
    // end getting targets of all years
    $data['monthlysales'] = $data["sales"][23 + $data['thismonth']];  // CR0001 $sales[35];
    $data['monthlycost'] = $data['costs'][23 + $data['thismonth']]; // CR0001 $costs[35];
    if ($data['G_MonthlySalesTarget'] != 0)
    {
      //echo $data['monthlysales'].'-'.$data['G_MonthlySalesTarget'] ;
      $data['monthlysalespc'] = ($data['monthlysales'] / $data['G_MonthlySalesTarget']) * 100;
    }
    else
    {
      $data['monthlysalespc'] = 0;
    }

    $data['monthlymargin'] = $data['monthlysales'] - $data['monthlycost'];
    if ($data['monthlysales'] <> 0)
    {
      $data['monthlymarginpc'] = ($data['monthlymargin'] / $data['monthlysales']) * 100;
    }

    // -------------------------------------------------------------------------------------------------------------------------------------------------
    // GET LAST SALES DATE FOR THE REP.
    // -------------------------------------------------------------------------------------------------------------------------------------------------
    $data['lastsalesdate'] = $this->site_model->getMaxDate($data['userDetail']['repwhere']);

    // -------------------------------------------------------------------------------------------------------------------------------------------------
    // GET LAST DAYS SALES FOR THE REP.
    // -------------------------------------------------------------------------------------------------------------------------------------------------

    $row = $this->site_model->getSalesRepLastSales($data['lastsalesdate'], $data['userDetail']['repwhere'], $G_userid, $G_branchno, $G_level);
    $data['dailysales'] = $row['sales'];
    $data['dailycost'] = $row['cost'];
    // Change the date to UK format for presentation

    $data['lastsalesdate'] = date('d/m/Y', strtotime($data['lastsalesdate'])); // UK format date

    if ($data['totdays'] <> 0)
    {
      $data['dailysalestarget'] = $data['monthlysalestarget'] / $data['totdays'];
    }


    $data['dailymargin'] = $data['dailysales'] - $data['dailycost'];
    if ($data['dailysales'] <> 0)
    {
      $data['dailymarginpc'] = ($data['dailymargin'] / $data['dailysales']) * 100;
    }


    if ($data['totdays'] <> 0)
    {
      $data['G_DailySalesTarget'] = $data['G_MonthlySalesTarget'] / $data['totdays'];
    }
    if ($data['G_DailySalesTarget'] <> 0)
    {
      $data['dailysalespc'] = ($data['dailysales'] / $data['G_DailySalesTarget']) * 100;
    }


    if ($data["dayno"] <> 0)
    {
      $data["projdaysales"] = ($data["monthlysales"] / $data["dayno"]);
      $data["projmonthsales"] = $data["projdaysales"] * $data["totdays"]; // Extrapolate projected sales
      if ($data["G_MonthlySalesTarget"] <> 0)
      {
        $data["projmonthsalespc"] = ($data["projmonthsales"] / $data["G_MonthlySalesTarget"]) * 100;
      }
    }

    $cumday = $this->site_model->cumday($G_level, $G_todaysdate, $repclause, $G_branchno);

    $cumday01sales = number_format($cumday[0]["SUM(day01sales)"], 0, '.', '');
    $cumday02sales = number_format($cumday[0]["SUM(day02sales)"] + $cumday01sales, 0, '.', '');
    $cumday03sales = number_format($cumday[0]["SUM(day03sales)"] + $cumday02sales, 0, '.', '');
    $cumday04sales = number_format($cumday[0]["SUM(day04sales)"] + $cumday03sales, 0, '.', '');
    $cumday05sales = number_format($cumday[0]["SUM(day05sales)"] + $cumday04sales, 0, '.', '');
    $cumday06sales = number_format($cumday[0]["SUM(day06sales)"] + $cumday05sales, 0, '.', '');
    $cumday07sales = number_format($cumday[0]["SUM(day07sales)"] + $cumday06sales, 0, '.', '');
    $cumday08sales = number_format($cumday[0]["SUM(day08sales)"] + $cumday07sales, 0, '.', '');
    $cumday09sales = number_format($cumday[0]["SUM(day09sales)"] + $cumday08sales, 0, '.', '');
    $cumday10sales = number_format($cumday[0]["SUM(day10sales)"] + $cumday09sales, 0, '.', '');
    $cumday11sales = number_format($cumday[0]["SUM(day11sales)"] + $cumday10sales, 0, '.', '');
    $cumday12sales = number_format($cumday[0]["SUM(day12sales)"] + $cumday11sales, 0, '.', '');
    $cumday13sales = number_format($cumday[0]["SUM(day13sales)"] + $cumday12sales, 0, '.', '');
    $cumday14sales = number_format($cumday[0]["SUM(day14sales)"] + $cumday13sales, 0, '.', '');
    $cumday15sales = number_format($cumday[0]["SUM(day15sales)"] + $cumday14sales, 0, '.', '');
    $cumday16sales = number_format($cumday[0]["SUM(day16sales)"] + $cumday15sales, 0, '.', '');
    $cumday17sales = number_format($cumday[0]["SUM(day17sales)"] + $cumday16sales, 0, '.', '');
    $cumday18sales = number_format($cumday[0]["SUM(day18sales)"] + $cumday17sales, 0, '.', '');
    $cumday19sales = number_format($cumday[0]["SUM(day19sales)"] + $cumday18sales, 0, '.', '');
    $cumday20sales = number_format($cumday[0]["SUM(day20sales)"] + $cumday19sales, 0, '.', '');
    $cumday21sales = number_format($cumday[0]["SUM(day21sales)"] + $cumday20sales, 0, '.', '');
    $cumday22sales = number_format($cumday[0]["SUM(day22sales)"] + $cumday21sales, 0, '.', '');
    $cumday23sales = number_format($cumday[0]["SUM(day23sales)"] + $cumday22sales, 0, '.', '');
    $cumday24sales = number_format($cumday[0]["SUM(day24sales)"] + $cumday23sales, 0, '.', '');
    $cumday25sales = number_format($cumday[0]["SUM(day25sales)"] + $cumday24sales, 0, '.', '');
    $cumday26sales = number_format($cumday[0]["SUM(day26sales)"] + $cumday25sales, 0, '.', '');
    $cumday27sales = number_format($cumday[0]["SUM(day27sales)"] + $cumday26sales, 0, '.', '');
    $cumday28sales = number_format($cumday[0]["SUM(day28sales)"] + $cumday27sales, 0, '.', '');
    $cumday29sales = number_format($cumday[0]["SUM(day29sales)"] + $cumday28sales, 0, '.', '');
    $cumday30sales = number_format($cumday[0]["SUM(day30sales)"] + $cumday29sales, 0, '.', '');
    $cumday31sales = number_format($cumday[0]["SUM(day31sales)"] + $cumday30sales, 0, '.', '');

    //------------------------//End Posted-----------------------------------------------------------------------------------------------------

    $data["ProjectedSalesMonthGraphActual"] = "[$cumday01sales,$cumday02sales,$cumday03sales,$cumday04sales,$cumday05sales,$cumday06sales,$cumday07sales,$cumday08sales,$cumday09sales,$cumday10sales,
							$cumday11sales,$cumday12sales,$cumday13sales,$cumday14sales,$cumday15sales,$cumday16sales,$cumday17sales,$cumday18sales,$cumday19sales,$cumday20sales,
							$cumday21sales,$cumday22sales,$cumday23sales,$cumday24sales,$cumday25sales,$cumday26sales,$cumday27sales,$cumday28sales,$cumday29sales,$cumday30sales,
							$cumday31sales]";

    $ProjectedSalesMonthGraphTarget = "[";
    $ProjectedSalesMonthGraphProjected = "[";
    $ProjectedSalesMonthGraphLabel = "[";
    $data["daysinmonth"] = $daysinmonth;
    for ($x = 1; $x <= $daysinmonth; $x++)
    {


      $cumulativetarget[$x] = ($data["G_MonthlySalesTarget"] / $data["daysinmonth"]) * $x;
      $cumulativeprojected[$x] = ($data["projmonthsales"] / $data["daysinmonth"]) * $x;

      $ProjectedSalesMonthGraphTarget .= number_format($cumulativetarget[$x], 0, '.', '');
      $ProjectedSalesMonthGraphProjected .= number_format($cumulativeprojected[$x], 0, '.', '');

      // Only putting the first and last day number in the label as its too busy with all the days

      if ($x == 1 or $x == $daysinmonth)
      {
        $ProjectedSalesMonthGraphLabel .= "'$x'";
      }
      else
      {
        $ProjectedSalesMonthGraphLabel .= "' '";
      }
      if ($x != $daysinmonth)
      {
        $ProjectedSalesMonthGraphTarget .= ",";
        $ProjectedSalesMonthGraphProjected .= ",";
        $ProjectedSalesMonthGraphLabel .= ",";
      }
    }

    $ProjectedSalesMonthGraphTarget .= "]";
    $ProjectedSalesMonthGraphProjected .= "]";
    $ProjectedSalesMonthGraphLabel .= ",' ']";
    //$ProjectedSalesMonthGraphLabel .= ",' ']";

    $data["ProjectedSalesMonthGraphTarget"] = $ProjectedSalesMonthGraphTarget;
    $data["ProjectedSalesMonthGraphProjected"] = $ProjectedSalesMonthGraphProjected;
    $data["ProjectedSalesMonthGraphLabel"] = $ProjectedSalesMonthGraphLabel;
    $this->load->view('dashboard_third', $data);

  }


  public function dashboard_fourth() {
    $data = array();



    $userType = $data['userType'] = $data['userDetail']['usertype'];

    $userId = 0;
    $branchNo = 0;
    $headerUserId = 0;

    if (count($this->session->userdata('selectedUser')) > 0)
    {
      $UserSes = $this->session->userdata('selectedUser');
      $userId = $UserSes["userid"];

    }

    $headerUserId = $userId;

    if (count($this->session->userdata('selectedBranch')) > 0)
    {
      $branchSes = $this->session->userdata('selectedBranch');
      $branchNo = $branchSes["branchno"];
    }

    $G_branchno = null;

    if ($userType == "B")
    {
      $G_level = "Branch";
    }
    elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0))
    {
      $G_level = "Company";
    }
    elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0))
    {
      $G_level = "Branch";
      $G_branchno = $branchNo;
    }
    elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0))
    {
      $G_level = "User";
      $G_userId = $userId;
      $data['userDetail'] = $this->site_model->getUserDetails($userId);
    }
    else
    {
      $G_level = "User";
    }

    $repclause = $data["userDetail"]["repclause"];


    $targetUserId = $userId;
    if (!empty($headerUserId))
    {
      $targetUserId = $headerUserId;
    }


    $this->load->view('dashboard_fourth', $data);
  }

  public function dashboard_fifth() {
    $data = array();
    $selectedUserDetails = array('repwhere' => "");

    if (count($this->session->userdata('selectedUser')) > 0)
    {
      $UserSes = $this->session->userdata('selectedUser');
      $userId = $UserSes["userid"];

      $selectedUserDetails = $this->site_model->getUserDetails($userId);
    }
    $data['currentMonthPac1QuoteConversions'] = $this->site_model->getPac1QuoteConversionForCurrentMonth($selectedUserDetails['repwhere']);
    $this->load->view('dashboard_fifth', $data);
  }

  public function dashboard_sixth() {
    $data = array();
    $selectedUserDetails = array('repwhere' => "");

    if (count($this->session->userdata('selectedUser')) > 0)
    {
      $UserSes = $this->session->userdata('selectedUser');
      $userId = $UserSes["userid"];

      $selectedUserDetails = $this->site_model->getUserDetails($userId);
    }
    $data['salesPipelineStages'] = $this->site_model->getSalesPipelineStages($selectedUserDetails['repwhere']);
    $this->load->view('dashboard_sixth', $data);
  }

  public function dashboard_seventh() {


    if ($this->site_model->is_logged_in() == false)
    {
      redirect('/');
    }
    $G_todaysdate = date("Y/m/d");

    $som = date("Y/m/01", strtotime($G_todaysdate));
    $ukdate = date("d/m/Y", strtotime($G_todaysdate));

    $daysinmonth = date("t", strtotime($G_todaysdate));
    $data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));

    $userType = $data['userType'] = $data['userDetail']['usertype'];

    $userId = 0;
    $branchNo = 0;
    $headerUserId = 0;
    $selectedUserDetails = array('repwhere' => "");

    if (count($this->session->userdata('selectedUser')) > 0)
    {
      $UserSes = $this->session->userdata('selectedUser');
      $userId = $UserSes["userid"];

      $selectedUserDetails = $this->site_model->getUserDetails($userId);
    }

    $headerUserId = $userId;

    if (count($this->session->userdata('selectedBranch')) > 0)
    {
      $branchSes = $this->session->userdata('selectedBranch');
      $branchNo = $branchSes["branchno"];
    }

    $G_branchno = null;

    if ($userType == "B")
    {
      $G_level = "Branch";
    }
    elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0))
    {
      $G_level = "Company";
    }
    elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0))
    {
      $G_level = "Branch";
      $G_branchno = $branchNo;
    }
    elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0))
    {
      $G_level = "User";
      $G_userId = $userId;
      $data['userDetail'] = $this->site_model->getUserDetails($userId);
    }
    else
    {
      $G_level = "User";
    }

    $repclause = $data["userDetail"]["repclause"];

    $kpiLastupdate_val = $this->site_model->kpiLastupdate();
    $data["G_KPIsLastUpdatedDateTime"] = $kpiLastupdate_val["kpislastupdated"];

    $kworkingDays = $this->site_model->workingDays($G_todaysdate);
    $data["dayno"] = $kworkingDays['dayno'];  // Current working day number
    $data["totdays"] = $kworkingDays['totdays']; // Total number of working days in the month


    if (is_null($G_branchno))
    {
      $G_branchno = $data['userDetail']['branch'];
    }

//    *************************************

    // End TODAYS ORDERS BY TYPE PIE CHART ///
    // TODAYS ORDERS BY STATUS PIE CHART ////
    $G_userid = $this->session->userdata("userid");


    // This is a potential bug fix, for some reason $userKpi is returning an empty array and therefore not working with the following function
    $userDetailAsKpi = array($data['userDetail']);

    // The pie chart data string looks something like this : [{value:179.80,color:'#dd4b39',highlight:'#dd4b39',label:'CR'},{value:1307.96,color:'#00a65a',highlight:'#00a65a',label:'SL'}]




    // GET THE DAY NUMBER AND WORKING DAYS
    $date = $G_todaysdate;
    $data["$G_todaysdate"] = $G_todaysdate;

    // Get the start of this month two years ago, to get two years of data
    // CR0001 - Changed from rolling 36 months to Jan-Dec for last 3 years

    $data['year0'] = date("Y");
    $data['year1'] = $data['year0'] - 1;
    $data['year2'] = $data['year0'] - 2;
    $data['year3'] = $data['year0'] - 3;

    $data['thismonth'] = date("m");

    $data['graphlabel0'] = $data['year0']; // CR0001 $year1 ."-" . $year0;
    $data['graphlabel1'] = $data['year1']; // CR0001 $year2 ."-" . $year1;
    $data['graphlabel2'] = $data['year2']; // CR0001 $year3 ."-" . $year2;

    $data['startdate'] = $data['year2'] . "01-01"; // CR0001"$year3 ."-" . $thismonth ."-" . $daysinmonth;

    $data['startyearmonth'] = ($data['year2'] * 100) + 1; // CR0001 ($year3 * 100) + $thismonth;
    $data['curyearmonth'] = ($data['year0'] * 100) + $data['thismonth']; // e.g. 201507

    $data['yearstartmonth'] = $this->site_model->getYearStartMonth();
    $start_month_delta = $data['yearstartmonth'] <= date('m') ? 11 + $data['yearstartmonth'] : $data['yearstartmonth'] - 1;

    // -------------------------------------------------------------------------------------------------------------------------------------------------
    // GET THE MONTHLY SALES REP TARGETS
    // -------------------------------------------------------------------------------------------------------------------------------------------------


    $data["year"] = $year;
    $soyyearmonth = ($data["year"] * 100) + 1;
    $row = $this->site_model->getmonthlySalesRepTarget($soyyearmonth, $G_userid, $G_branchno, $G_level);

    $row1 = $this->site_model->getSalesRepTarget($data['curyearmonth'], $G_userid, $G_branchno, $G_level);

    $targetyearmonth = $row['yearmonth'];
    $tmp_monthlysalestarget = $data['monthlysalestarget'] = $row['salesTarget'] = $row['saletarget'];
    $data["G_YearlySalesTarget"] += $G_YearlySalesTarget += $tmp_monthlysalestarget;
    $data['G_MonthlySalesTarget'] = $row1['salesTarget'] = $row1['saletarget'];

    //7-9-2017 ash
    $targetUserId = $G_userid;
    if (!empty($headerUserId))
    {
      $targetUserId = $headerUserId;
    }
    $targetDataMonth = $this->site_model->getMonthTargetData($data['curyearmonth'], $targetUserId, $G_branchno, $G_level);
    $targetDataYear = $this->site_model->getYearTargetData($targetUserId, $G_branchno, $G_level);
    $data['G_MonthlySalesTarget'] = $targetDataMonth['saletarget'];
    $data["G_YearlySalesTarget"] = $targetDataYear['saletarget'];
    //7-9-2017 ash

    //	print_r($data); exit;
    $data['marginok'] = $row['marginok'];
    $data['margingood'] = $row['margingood'];

    if ($data['totdays'] <> 0)
    {
      $data['dailysalestarget'] = $data['monthlysalestarget'] / $data['totdays'];
    }

    // Initialise sales array

    $data['yearmonth'] = array();
    $data['monthnames'] = array();
    $data["sales"] = array();
    $data['costs'] = array();

    // Preload the year and month into an array so that we can make sure we load the sales against the correct row. Pad the month with leading 0 if needed. Had an example where
    // a rep started more recently that three years ago, and therefore there was less than 36 months. It was loading all these into the start of the array, rather than against the
    // appropriate row.

    $data['tmpyear'] = $data['year3']; //CR0001 $year3;
    $data['tmpmonth'] = 1; // CR0001 $thismonth + 1;

    for ($x = 0; $x < 48; $x++)
    {
      $data['yearmonth'][$x] = ($data['tmpyear'] * 100) + $data['tmpmonth'];

      $data["sales"][$x] = 0;
      $data['costs'][$x] = 0;

      $data['tmpmonth'] = $data['tmpmonth'] + 1;
      if ($data['tmpmonth'] == 13)
      {
        $data['tmpmonth'] = 1;
        $data['tmpyear'] = $data['tmpyear'] + 1;
      }
    }
    // Get sales for the sales rep
    $result = $this->site_model->getSalesAnalisys($data['curyearmonth'], $data['userDetail']['repwhere'], $G_userid, $G_branchno, $G_level);

    $x = 0;

    foreach ($result as $row)
    {
      $data['salessummaryyearmonth'] = $row['yearmonth'];
      $data['salessummarysales'] = $row['sales'];
      $data['salessummarycost'] = $row['cost'];

      // For each data row, loop through the array and put the sales value in the correct place

      for ($x = 0; $x < 48; $x++)
      {
        if ($data['yearmonth'][$x] == $data['salessummaryyearmonth'])
        {
          $data["sales"][$x] = $data['salessummarysales']; // If the year month of the data matches the array, put the value in
          $data['costs'][$x] = $data['salessummarycost'];
        }
      }
    }

    $data['salesTargetForLastThreeYear'] = $this->site_model->getSalesTargetForLastThreeYear($G_level, $data['yearmonth'], $data["sales"], $targetUserId, $G_branchno);
    $data['targetDataForCurrentYear'] = $this->site_model->GetTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);
    $data['cumulativeTargetDataForCurrentYear'] = $this->site_model->GetCumulativeTargetDataForCurrentYear($data['salesTargetForLastThreeYear']);

    $data['year0data'] = $this->site_model->GetYearData($data["sales"], 24 + $start_month_delta, 35 + $start_month_delta);
    $data["year0total"] = $this->site_model->GetYearTotal($data["sales"], 24 + $start_month_delta, 35 + $start_month_delta);
    $data["year0table"] = $this->site_model->GetYearTable($data["sales"], $data["year0total"], 24 + $start_month_delta, 35 + $start_month_delta);

    $data['year1data'] = $this->site_model->GetYearData($data["sales"], 12 + $start_month_delta, 23 + $start_month_delta);
    $data["year1total"] = $this->site_model->GetYearTotal($data["sales"], 12 + $start_month_delta, 23 + $start_month_delta);
    $data["year1table"] = $this->site_model->GetYearTable($data["sales"], $data["year1total"], 12 + $start_month_delta, 23 + $start_month_delta);

    $data['year2data'] = $this->site_model->GetYearData($data["sales"], $start_month_delta, 11 + $start_month_delta);
    $data["year2total"] = $this->site_model->GetYearTotal($data["sales"], $start_month_delta, 11 + $start_month_delta);
    $data["year2table"] = $this->site_model->GetYearTable($data["sales"], $data["year2total"], $start_month_delta, 11 + $start_month_delta);

    $data['year0ChartValues'] = $data['year0data'];
    $data['year1ChartValues'] = $data['year1data'];
    $data['year2ChartValues'] = $data['year2data'];
    $data['cumulativeYear0ChartValues'] = $this->site_model->GetCumulativeYearData($data["sales"], 24 + $start_month_delta, 35 + $start_month_delta);
    $data['cumulativeYear1ChartValues'] = $this->site_model->GetCumulativeYearData($data["sales"], 12 + $start_month_delta, 23 + $start_month_delta);
    $data['cumulativeYear2ChartValues'] = $this->site_model->GetCumulativeYearData($data["sales"], $start_month_delta, 11 + $start_month_delta);

    // getting targets of all years(14-03-2018)
    $threeYearTargetArray = $this->site_model->getThreeYearTargetArray($G_userid, $G_branchno, $G_level);

    //********************************************



    $data['monthlysales'] = $data["sales"][23 + $data['thismonth']];  // CR0001 $sales[35];
    $data['monthlycost'] = $data['costs'][23 + $data['thismonth']]; // CR0001 $costs[35];
    if ($data['G_MonthlySalesTarget'] != 0)
    {
      //echo $data['monthlysales'].'-'.$data['G_MonthlySalesTarget'] ;
      $data['monthlysalespc'] = ($data['monthlysales'] / $data['G_MonthlySalesTarget']) * 100;
    }
    else
    {
      $data['monthlysalespc'] = 0;
    }

    $data['monthlymargin'] = $data['monthlysales'] - $data['monthlycost'];
    if ($data['monthlysales'] <> 0)
    {
      $data['monthlymarginpc'] = ($data['monthlymargin'] / $data['monthlysales']) * 100;
    }

    $data['lastsalesdate'] = $this->site_model->getMaxDate($data['userDetail']['repwhere']);


    $row = $this->site_model->getSalesRepLastSales($data['lastsalesdate'], $data['userDetail']['repwhere'], $G_userid, $G_branchno, $G_level);
    $data['dailysales'] = $row['sales'];
    $data['dailycost'] = $row['cost'];

    $data['lastsalesdate'] = date('d/m/Y', strtotime($data['lastsalesdate'])); // UK format date

    if ($data['totdays'] <> 0)
    {
      $data['dailysalestarget'] = $data['monthlysalestarget'] / $data['totdays'];
    }


    $data['dailymargin'] = $data['dailysales'] - $data['dailycost'];
    if ($data['dailysales'] <> 0)
    {
      $data['dailymarginpc'] = ($data['dailymargin'] / $data['dailysales']) * 100;
    }


    if ($data['totdays'] <> 0)
    {
      $data['G_DailySalesTarget'] = $data['G_MonthlySalesTarget'] / $data['totdays'];
    }
    if ($data['G_DailySalesTarget'] <> 0)
    {
      $data['dailysalespc'] = ($data['dailysales'] / $data['G_DailySalesTarget']) * 100;
    }


    if ($data["dayno"] <> 0)
    {
      $data["projdaysales"] = ($data["monthlysales"] / $data["dayno"]);
      $data["projmonthsales"] = $data["projdaysales"] * $data["totdays"]; // Extrapolate projected sales
      if ($data["G_MonthlySalesTarget"] <> 0)
      {
        $data["projmonthsalespc"] = ($data["projmonthsales"] / $data["G_MonthlySalesTarget"]) * 100;
      }
    }

    $cumday = $this->site_model->cumday($G_level, $G_todaysdate, $repclause, $G_branchno);


    $cumday01sales = number_format($cumday[0]["SUM(day01sales)"], 0, '.', '');
    $cumday02sales = number_format($cumday[0]["SUM(day02sales)"] + $cumday01sales, 0, '.', '');
    $cumday03sales = number_format($cumday[0]["SUM(day03sales)"] + $cumday02sales, 0, '.', '');
    $cumday04sales = number_format($cumday[0]["SUM(day04sales)"] + $cumday03sales, 0, '.', '');
    $cumday05sales = number_format($cumday[0]["SUM(day05sales)"] + $cumday04sales, 0, '.', '');
    $cumday06sales = number_format($cumday[0]["SUM(day06sales)"] + $cumday05sales, 0, '.', '');
    $cumday07sales = number_format($cumday[0]["SUM(day07sales)"] + $cumday06sales, 0, '.', '');
    $cumday08sales = number_format($cumday[0]["SUM(day08sales)"] + $cumday07sales, 0, '.', '');
    $cumday09sales = number_format($cumday[0]["SUM(day09sales)"] + $cumday08sales, 0, '.', '');
    $cumday10sales = number_format($cumday[0]["SUM(day10sales)"] + $cumday09sales, 0, '.', '');
    $cumday11sales = number_format($cumday[0]["SUM(day11sales)"] + $cumday10sales, 0, '.', '');
    $cumday12sales = number_format($cumday[0]["SUM(day12sales)"] + $cumday11sales, 0, '.', '');
    $cumday13sales = number_format($cumday[0]["SUM(day13sales)"] + $cumday12sales, 0, '.', '');
    $cumday14sales = number_format($cumday[0]["SUM(day14sales)"] + $cumday13sales, 0, '.', '');
    $cumday15sales = number_format($cumday[0]["SUM(day15sales)"] + $cumday14sales, 0, '.', '');
    $cumday16sales = number_format($cumday[0]["SUM(day16sales)"] + $cumday15sales, 0, '.', '');
    $cumday17sales = number_format($cumday[0]["SUM(day17sales)"] + $cumday16sales, 0, '.', '');
    $cumday18sales = number_format($cumday[0]["SUM(day18sales)"] + $cumday17sales, 0, '.', '');
    $cumday19sales = number_format($cumday[0]["SUM(day19sales)"] + $cumday18sales, 0, '.', '');
    $cumday20sales = number_format($cumday[0]["SUM(day20sales)"] + $cumday19sales, 0, '.', '');
    $cumday21sales = number_format($cumday[0]["SUM(day21sales)"] + $cumday20sales, 0, '.', '');
    $cumday22sales = number_format($cumday[0]["SUM(day22sales)"] + $cumday21sales, 0, '.', '');
    $cumday23sales = number_format($cumday[0]["SUM(day23sales)"] + $cumday22sales, 0, '.', '');
    $cumday24sales = number_format($cumday[0]["SUM(day24sales)"] + $cumday23sales, 0, '.', '');
    $cumday25sales = number_format($cumday[0]["SUM(day25sales)"] + $cumday24sales, 0, '.', '');
    $cumday26sales = number_format($cumday[0]["SUM(day26sales)"] + $cumday25sales, 0, '.', '');
    $cumday27sales = number_format($cumday[0]["SUM(day27sales)"] + $cumday26sales, 0, '.', '');
    $cumday28sales = number_format($cumday[0]["SUM(day28sales)"] + $cumday27sales, 0, '.', '');
    $cumday29sales = number_format($cumday[0]["SUM(day29sales)"] + $cumday28sales, 0, '.', '');
    $cumday30sales = number_format($cumday[0]["SUM(day30sales)"] + $cumday29sales, 0, '.', '');
    $cumday31sales = number_format($cumday[0]["SUM(day31sales)"] + $cumday30sales, 0, '.', '');

    //------------------------//End Posted-----------------------------------------------------------------------------------------------------

    $data["ProjectedSalesMonthGraphActual"] = "[$cumday01sales,$cumday02sales,$cumday03sales,$cumday04sales,$cumday05sales,$cumday06sales,$cumday07sales,$cumday08sales,$cumday09sales,$cumday10sales,
							$cumday11sales,$cumday12sales,$cumday13sales,$cumday14sales,$cumday15sales,$cumday16sales,$cumday17sales,$cumday18sales,$cumday19sales,$cumday20sales,
							$cumday21sales,$cumday22sales,$cumday23sales,$cumday24sales,$cumday25sales,$cumday26sales,$cumday27sales,$cumday28sales,$cumday29sales,$cumday30sales,
							$cumday31sales]";


    // Build up the cumulative target and projected arrays

    $ProjectedSalesMonthGraphTarget = "[";
    $ProjectedSalesMonthGraphProjected = "[";
    $ProjectedSalesMonthGraphLabel = "[";
    $data["daysinmonth"] = $daysinmonth;
    for ($x = 1; $x <= $daysinmonth; $x++) {


      $cumulativetarget[$x] = ($data["G_MonthlySalesTarget"] / $data["daysinmonth"]) * $x;
      $cumulativeprojected[$x] = ($data["projmonthsales"] / $data["daysinmonth"]) * $x;

      $ProjectedSalesMonthGraphTarget .= number_format($cumulativetarget[$x], 0, '.', '');
      $ProjectedSalesMonthGraphProjected .= number_format($cumulativeprojected[$x], 0, '.', '');

      // Only putting the first and last day number in the label as its too busy with all the days

      if ($x == 1 or $x == $daysinmonth) {
        $ProjectedSalesMonthGraphLabel .= "'$x'";
      } else {
        $ProjectedSalesMonthGraphLabel .= "' '";
      }
      if ($x != $daysinmonth) {
        $ProjectedSalesMonthGraphTarget .= ",";
        $ProjectedSalesMonthGraphProjected .= ",";
        $ProjectedSalesMonthGraphLabel .= ",";
      }
    }

    $ProjectedSalesMonthGraphTarget .= "]";
    $ProjectedSalesMonthGraphProjected .= "]";
    $ProjectedSalesMonthGraphLabel .= ",' ']";
    //$ProjectedSalesMonthGraphLabel .= ",' ']";


    $data["ProjectedSalesMonthGraphTarget"] = $ProjectedSalesMonthGraphTarget;
    $data["ProjectedSalesMonthGraphProjected"] = $ProjectedSalesMonthGraphProjected;
    $data["ProjectedSalesMonthGraphLabel"] = $ProjectedSalesMonthGraphLabel;



    if ($year0total <> 0)    // From the 3 year chart
    {
      $data["projmonthsales"] = ($year0total / $month);
      $data["projyearsales"] = $data["projmonthsales"] * 12; // Extrapolate projected sales

      if (!is_null($data["G_YearlySalesTarget"]) && $data["G_YearlySalesTarget"] != 0)
      {
        $data["projyearsalespc"] = ($data["projyearsales"] / $data["G_YearlySalesTarget"]) * 100;
      }
    }
// Set the background colour. If the projection is greater than the target, then its green, otherwise its red
    if ($data["projyearsales"] > $data["G_YearlySalesTarget"])
    {
      $data["ProjectedSalesYearGraphBg"] = "bg-green";
    }
    else
    {
      $data["ProjectedSalesYearGraphBg"] = "bg-red";
    }

    // Already have this years sales by month, from the 3 year chart, so build chart data from that

    $cummth01sales = number_format($data["sales"][24 + $start_month_delta], 0, '.', '');
    $cummth02sales = number_format($cummth01sales + $data["sales"][25 + $start_month_delta], 0, '.', '');
    $cummth03sales = number_format($cummth02sales + $data["sales"][26 + $start_month_delta], 0, '.', '');
    $cummth04sales = number_format($cummth03sales + $data["sales"][27 + $start_month_delta], 0, '.', '');
    $cummth05sales = number_format($cummth04sales + $data["sales"][28 + $start_month_delta], 0, '.', '');
    $cummth06sales = number_format($cummth05sales + $data["sales"][29 + $start_month_delta], 0, '.', '');
    $cummth07sales = number_format($cummth06sales + $data["sales"][30 + $start_month_delta], 0, '.', '');
    $cummth08sales = number_format($cummth07sales + $data["sales"][31 + $start_month_delta], 0, '.', '');
    $cummth09sales = number_format($cummth08sales + $data["sales"][32 + $start_month_delta], 0, '.', '');
    $cummth10sales = number_format($cummth09sales + $data["sales"][33 + $start_month_delta], 0, '.', '');
    $cummth11sales = number_format($cummth10sales + $data["sales"][34 + $start_month_delta], 0, '.', '');
    $cummth12sales = number_format($cummth11sales + $data["sales"][35 + $start_month_delta], 0, '.', '');

    // Something like this: sales [24070,36053,45000,53187,64540,64540,64540,75130,75130,75130, 75130,75130]
    $data["ProjectedSalesYearGraphActual"] = "[$cummth01sales,$cummth02sales,$cummth03sales,$cummth04sales,$cummth05sales,$cummth06sales,
                                                $cummth07sales,$cummth08sales,$cummth09sales,$cummth10sales,$cummth11sales,$cummth12sales]";

    // Build up the cumulative target and projected arrays

    $ProjectedSalesYearGraphTarget = "[";
    $ProjectedSalesYearGraphProjected = "[";

    for ($x = 1; $x <= 12; $x++)
    {
      $cumulativeprojected[$x] = ($data["projyearsales"] / 12) * $x;

      $ProjectedSalesYearGraphTarget .= number_format(($data["G_YearlySalesTarget"] / 12) * $x, 0, '.', '');
      $ProjectedSalesYearGraphProjected .= number_format(($data["projyearsales"] / 12) * $x, 0, '.', '');

      if ($x != 12)
      {
        $ProjectedSalesYearGraphTarget .= ",";
        $ProjectedSalesYearGraphProjected .= ",";
      }
    }

    $ProjectedSalesYearGraphTarget .= "]";
    $ProjectedSalesYearGraphProjected .= "]";


    $data["ProjectedSalesYearGraphTarget"] = $ProjectedSalesYearGraphTarget;
    $data["ProjectedSalesYearGraphProjected"] = $ProjectedSalesYearGraphProjected;

    $this->load->view('dashboard_seventh', $data);
  }

  public function dashboard_eighth() {

    $this->load->helper('cookie');

    if (isset($_COOKIE['salestodaydonutcharts']))
    {
      $data['salestodaydonutcharts'] = get_cookie('salestodaydonutcharts', true);
    }
    else
    {
      $data['salestodaydonutcharts'] = 0;
    }

    if (isset($_COOKIE['outstandingordersdonutchart']))
    {
      $data['outstandingordersdonutchart'] = get_cookie('outstandingordersdonutchart', true);
    }
    else
    {
      $data['outstandingordersdonutchart'] = 0;
    }

    if (isset($_COOKIE['threeyearsaleschart']))
    {
      $data['threeyearsaleschart'] = get_cookie('threeyearsaleschart', true);
    }
    else
    {
      $data['threeyearsaleschart'] = 0;
    }

    $currency_symbol = $this->config->item("currency_symbol");

    $G_todaysdate = date("Y/m/d");

    $som = date("Y/m/01", strtotime($G_todaysdate));
    $ukdate = date("d/m/Y", strtotime($G_todaysdate));


    $daysinmonth = date("t", strtotime($G_todaysdate));
    $year = date("Y", strtotime($G_todaysdate));
    $month = date("m", strtotime($G_todaysdate));
    $eomtemp = $year . '/' . $month . '/' . $daysinmonth;
    $eom = date('Y/m/d', strtotime($eomtemp));
    $ukeom = date('d/m/Y', strtotime($eomtemp));

    $data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));

    $userType = $data['userType'] = $data['userDetail']['usertype'];


    $userId = 0;
    $branchNo = 0;
    $headerUserId = 0;
    $selectedUserDetails = array('repwhere' => "");

    if (count($this->session->userdata('selectedUser')) > 0)
    {
      $UserSes = $this->session->userdata('selectedUser');
      $userId = $UserSes["userid"];

      $selectedUserDetails = $this->site_model->getUserDetails($userId);
    }



    $headerUserId = $userId;

    if (count($this->session->userdata('selectedBranch')) > 0)
    {
      $branchSes = $this->session->userdata('selectedBranch');
      $branchNo = $branchSes["branchno"];
    }

    $G_branchno = null;

    if ($userType == "B")
    {
      $G_level = "Branch";
    }
    elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0))
    {
      $G_level = "Company";
    }
    elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0))
    {
      $G_level = "Branch";
      $G_branchno = $branchNo;
    }
    elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0))
    {
      $G_level = "User";
      $G_userId = $userId;
      $data['userDetail'] = $this->site_model->getUserDetails($userId);
    }
    else
    {
      $G_level = "User";
    }

    $repclause = $data["userDetail"]["repclause"];


/// KPI last Update///
    $kpiLastupdate_val = $this->site_model->kpiLastupdate();
    $data["G_KPIsLastUpdatedDateTime"] = $kpiLastupdate_val["kpislastupdated"];

// END KPI last Update///

// GET THE DAY NUMBER AND WORKING DAYS//
    $kworkingDays = $this->site_model->workingDays($G_todaysdate);
    $data["dayno"] = $kworkingDays['dayno'];  // Current working day number
    $data["totdays"] = $kworkingDays['totdays']; // Total number of working days in the month


// END GET THE DAY NUMBER AND WORKING DAYS//


// Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set

// END Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set
// Get TODAYS ORDERS BY TYPE PIE CHART ///



    if (is_null($G_branchno))
    {
      $G_branchno = $data['userDetail']['branch'];
    }

    $todayOrders = $this->site_model->todayOrders($G_level, $G_todaysdate, $repclause, $G_branchno);

    $BIColour = "#3c8dbc";    // Book Ins  		Light Blue
    $BOColour = "#f39c12";    // Book Outs  		Yellow
    $BTColour = "#001f3f";    // Branch Transfers	Navy
    $CRColour = "#dd4b39";    // Credits  		Red
    $DNColour = "#39cccc";    // Debit Notes  	Teal
    $QTColour = "#00c0ef";    // Quotations  		Aqua
    $SLColour = "#00a65a";    // Orders  			Green
    $WOColour = "#d2d6de";    // Works Orders  	Gray
    $RWColour = "#f44295";
    $TCColour = "#7a1919";
    $THColour = "#4f5960";

// Assign legend colours to order types

    $BITextColour = "text-light-blue";    // Book Ins  		Light Blue
    $BOTextColour = "text-yellow";        // Book Outs  		Yellow
    $BTTextColour = "text-navy";        // Branch Transfers	Navy
    $CRTextColour = "text-red";            // Credits  		Red
    $DNTextColour = "text-teal";        // Debit Notes  	Teal
    $QTTextColour = "text-aqua";        // Quotations  		Aqua
    $SLTextColour = "text-green";        // Orders  			Green
    $WOTextColour = "text-gray";        // Works Orders  	Gray
    $RWTextColour = "text-rwcolor";
    $TCTextColour = "text-tccolor";
    $THTextColour = "text-thcolor";
    $todaysordersbytypedata = "[";

    $i = 1;
    $tmp_total = 0;




    foreach ($todayOrders as $today)
    {
      $identifier = $today['identifier'];
      $value = $today['actualvalue1'];
      // The order type is the last two characters of the identifier
      $ordtype = substr($identifier, 10, 2);
      // Only interested in graphing order types that have a value
      if ($value <> 0)
      {
        $tmp_total += $value;

        // Set the colour, which is the order type followed by"Colour"
        switch ($ordtype)
        {
          case"BI":
            $colour = $BIColour;
            $textcolour = $BITextColour;
            $description = "Book Ins";
            break;
          case"BO":
            $colour = $BOColour;
            $textcolour = $BOTextColour;
            $description = "Book Outs";
            break;
          case"BT":
            $colour = $BTColour;
            $textcolour = $BTTextColour;
            $description = "Branch Transfers";
            break;
          case"CR":
            $colour = $CRColour;
            $textcolour = $CRTextColour;
            $description = "Credit Notes";
            break;
          case"DN":
            $colour = $DNColour;
            $textcolour = $DNTextColour;
            $description = "Debit Notes";
            break;
          case"QT":
            $colour = $QTColour;
            $textcolour = $QTTextColour;
            $description = "Quotations";
            break;
          case"SL":
            $colour = $SLColour;
            $textcolour = $SLTextColour;
            $description = "Sales Orders";
            break;
          case"WO":
            $colour = $WOColour;
            $textcolour = $WOTextColour;
            $description = "Works Orders";
            break;
          case"RW":
            $colour = $RWColour;
            $textcolour = $RWTextColour;
            $description = "Repairs & Warranty";
            break;
          case"TC":
            $colour = $TCColour;
            $textcolour = $TCTextColour;
            $description = "Plant Hire Credit Note";
            break;
          case"TH":
            $colour = $THColour;
            $textcolour = $THTextColour;
            $description = "Plant Hire Order";
            break;
        }

        // The comma only comes in after the first set

        if ($i <> 1)
        {
          $todaysordersbytypedata .= ",";
        }

        // Build the data string for the pie chart data
        $todaysordersbytypedata .= "{value:$value,color:'$colour',highlight:'$colour',label:'$ordtype'}";

        // Build the string for the legend
        $typeLink = site_url("site/todaysorder/" . $ordtype . "/type");
        $todaysordersbytypelegend .= "<li><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'><i class='fa fa-circle-o $textcolour'></i> $ordtype</a></li>";
        // Build the string for the table
        $todaysordersbytypetable .= "<tr><td><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>$ordtype</a></td><td><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>$description</a></td><td align='right'><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>" . $currency_symbol . number_format($value, 2) . "</a></td></tr>";

        $i++;
      }
    }


    $todaysordersbytypetable .= "<tr><th>&nbsp</th><th>Total</th><th style='text-align: right'>" . $currency_symbol . number_format($tmp_total, 2) . "</th></tr>";
    $todaysordersbytypedata .= "]";

    $data["todaysordersbytypedata"] = $todaysordersbytypedata;
    $data["todaysordersbytypelegend"] = $todaysordersbytypelegend;
    $data["todaysordersbytypetable"] = $todaysordersbytypetable;
    // End TODAYS ORDERS BY TYPE PIE CHART ///
    // TODAYS ORDERS BY STATUS PIE CHART ////
    $G_userid = $this->session->userdata("userid");


    // This is a potential bug fix, for some reason $userKpi is returning an empty array and therefore not working with the following function
    $userDetailAsKpi = array($data['userDetail']);

    // $data = $this->site_model->GetKpiDataForTwoYearVsTargetChart($userDetailAsKpi, $data, $G_level);

    // This is the code that was not working
    // $userKpi = $this->site_model->userKpi($G_level, $G_branchno, $G_userid);
    // $data = $this->site_model->GetKpiDataForTwoYearVsTargetChart($userKpi, $data, $G_level);

    $todayOrders = $this->site_model->todayOrdersStatus($G_level, $G_todaysdate, $repclause, $G_branchno);
    $ADVColour = "#f012be";    // Waiting advice note	Fuschia
    $COMColour = "#00a65a";    // Completed line		Green
    $CUSColour = "#39cccc";    // Call customer back	Teal
    $HLDColour = "#3d9970";    // Goods on hold		Olive
    $IBTColour = "#d2d6de";    // Inter-branch transfer	Gray
    $KITColour = "#01ff70";    // Process kit list		Lime
    $MEMColour = "#ff851b";    // Memo line			Orange
    $OFFColour = "#605ca8";    // Call off later		Purple
    $PIKColour = "#001f3f";    // Pick note printed	Navy
    $PROColour = "#3c8dbc";    // Process document		Light Blue
    $PURColour = "#dd4b39";    // Purchase order		Red
    $SBOColour = "#f39c12";    // Stock backorder		Yellow
    $WDLColour = "#00c0ef";    // Waiting delivery		Aqua
    $WRKColour = "#d81b60";    // Create works order	Maroon

    // Assign legend colours to order statuses

    $ADVTextColour = "text-fuschia";    // Waiting advice note	Fuschia
    $COMTextColour = "text-green";        // Completed line		Green
    $CUSTextColour = "text-teal";        // Call customer back	Teal
    $HLDTextColour = "text-olive";        // Goods on hold		Olive
    $IBTTextColour = "text-gray";        // Inter-branch transfer	Gray
    $KITTextColour = "text-lime";        // Process kit list		Lime
    $MEMTextColour = "text-orange";        // Memo line			Orange
    $OFFTextColour = "text-purple";        // Call off later		Purple
    $PIKTextColour = "text-navy";        // Pick note printed	Navy
    $PROTextColour = "text-light-blue";    // Process document		Light Blue
    $PURTextColour = "text-red";        // Purchase order		Red
    $SBOTextColour = "text-yellow";        // Stock backorder		Yellow
    $WDLTextColour = "text-aqua";        // Waiting delivery		Aqua
    $WRKTextColour = "text-maroon";        // Cr

    $todaysordersbystatusdata = "[";

    $i = 1;
    $tmp_total = 0;


    foreach ($todayOrders as $today)
    {

      $identifier = $today['identifier'];
      $value = $today['actualvalue1'];

      // The order type is the last three characters of the identifier
      $ordstatus = substr($identifier, 10, 3);
      // Only interested in graphing order statuses that have a value

      if ($value <> 0)
      {
        $tmp_total += $value;

        // Set the colour, which is the order status followed by"Colour"
        switch ($ordstatus)
        {
          case"ADV":
            $colour = $ADVColour;
            $textcolour = $ADVTextColour;
            $description = "Waiting Advice Note";
            break;
          case"COM":
            $colour = $COMColour;
            $textcolour = $COMTextColour;
            $description = "Completed Line";
            break;
          case"CUS":
            $colour = $CUSColour;
            $textcolour = $CUSTextColour;
            $description = "Call Customer Back";
            break;
          case"HLD":
            $colour = $HLDColour;
            $textcolour = $HLDTextColour;
            $description = "Goods On Hold";
            break;
          case"IBT":
            $colour = $IBTColour;
            $textcolour = $IBTTextColour;
            $description = "Inter-Branch Transfer";
            break;
          case"KIT":
            $colour = $KITColour;
            $textcolour = $KITTextColour;
            $description = "Process Kit List";
            break;
          case"MEM":
            $colour = $MEMColour;
            $textcolour = $MEMTextColour;
            $description = "Memo Line (Quotations)";
            break;
          case"OFF":
            $colour = $OFFColour;
            $textcolour = $OFFTextColour;
            $description = "Call Off Later";
            break;
          case"PIK":
            $colour = $PIKColour;
            $textcolour = $PIKTextColour;
            $description = "Pick Note Printed";
            break;
          case"PRO":
            $colour = $PROColour;
            $textcolour = $PROTextColour;
            $description = "Process Document";
            break;
          case"PUR":
            $colour = $PURColour;
            $textcolour = $PURTextColour;
            $description = "Purchase Order";
            break;
          case"SBO":
            $colour = $SBOColour;
            $textcolour = $SBOTextColour;
            $description = "Stock Backorder";
            break;
          case"WDL":
            $colour = $WDLColour;
            $textcolour = $WDLTextColour;
            $description = "Waiting Delivery";
            break;
          case"WRK":
            $colour = $WRKColour;
            $textcolour = $WRKTextColour;
            $description = "Create Works Order";
            break;
        }

        // The comma only comes in after the first set

        if ($i <> 1)
        {
          $todaysordersbystatusdata .= ",";
        }

        // Build the data string for the pie chart data
        $todaysordersbystatusdata .= "{value:$value,color:'$colour',highlight:'$colour',label:'$ordstatus'}";
        $typeLinkStatus = site_url("site/todaysorder/" . $ordstatus . "/status");
        // Build the string for the legend
        $todaysordersbystatuslegend .= "<li><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'><i class='fa fa-circle-o " . $textcolour . "'></i> $ordstatus</a></li>";

        // Build the string for the table
        $todaysordersbystatustable .= "<tr><td><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>$ordstatus</a></td><td><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>$description</a></td><td align='right'><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>" . $currency_symbol . number_format($value, 2) . "</a></td></tr>";

        $i++;
      }
    }
    $todaysordersbystatustable .= "<tr><th>&nbsp</th><th>Total</th><th  style='text-align: right'>" . $currency_symbol . number_format($tmp_total, 2) . "</th></tr>";
    $todaysordersbystatusdata .= "]";
    $data["todaysordersbystatustable"] = $todaysordersbystatustable;
    $data["todaysordersbystatusdata"] = $todaysordersbystatusdata;
    $data["todaysordersbystatuslegend"] = $todaysordersbystatuslegend;

// End TODAYS ORDERS BY STATUS PIE CHART
// -------------------------------------------------------------------------------------------------------------------------------------------------


// Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set

// Get the company kpi and margin thresholds, which are defaults if the user or branch ones arent set


//-----------------Outstanding Ordrs---------------------//
    $outOrders = $this->site_model->outStandOrders($G_level, $G_todaysdate, $repclause, $G_branchno);
    $ADVColour = "#f012be";    // Waiting advice note	Fuschia
    $COMColour = "#00a65a";    // Completed line		Green
    $CUSColour = "#39cccc";    // Call customer back	Teal
    $HLDColour = "#3d9970";    // Goods on hold		Olive
    $IBTColour = "#d2d6de";    // Inter-branch transfer	Gray
    $KITColour = "#01ff70";    // Process kit list		Lime
    $MEMColour = "#ff851b";    // Memo line			Orange
    $OFFColour = "#605ca8";    // Call off later		Purple
    $PIKColour = "#001f3f";    // Pick note printed	Navy
    $PROColour = "#3c8dbc";    // Process document		Light Blue
    $PURColour = "#dd4b39";    // Purchase order		Red
    $SBOColour = "#f39c12";    // Stock backorder		Yellow
    $WDLColour = "#00c0ef";    // Waiting delivery		Aqua
    $WRKColour = "#d81b60";    // Create works order	Maroon

// Assign legend colours to order statuses

    $ADVTextColour = "text-fuschia";        // Waiting advice note	Fuschia
    $COMTextColour = "text-green";        // Completed line		Green
    $CUSTextColour = "text-teal";        // Call customer back	Teal
    $HLDTextColour = "text-olive";        // Goods on hold		Olive
    $IBTTextColour = "text-gray";        // Inter-branch transfer	Gray
    $KITTextColour = "text-lime";        // Process kit list		Lime
    $MEMTextColour = "text-orange";        // Memo line			Orange
    $OFFTextColour = "text-purple";        // Call off later		Purple
    $PIKTextColour = "text-navy";        // Pick note printed	Navy
    $PROTextColour = "text-light-blue";    // Process document		Light Blue
    $PURTextColour = "text-red";            // Purchase order		Red
    $SBOTextColour = "text-yellow";        // Stock backorder		Yellow
    $WDLTextColour = "text-aqua";        // Waiting delivery		Aqua
    $WRKTextColour = "text-maroon";        // Create works order	Maroon

// The pie chart data string looks something like this : [{value:179.80,color:'#dd4b39',highlight:'#dd4b39',label:'CR'},{value:1307.96,color:'#00a65a',highlight:'#00a65a',label:'SL'}]

    $outstandingordersbystatusdata = "[";

    $i = 1;
    $tmp_total = 0;

    foreach ($outOrders as $outor)
    {
      $identifier = $outor["identifier"];
      $value = $outor["actualvalue1"];

      // The order type is the last three characters of the identifier
      $ordstatus = substr($identifier, 10, 3);
      // Only interested in graphing order statuses that have a value

      if ($value <> 0)
      {
        $tmp_total += $value;

        // Set the colour, which is the order status followed by"Colour"
        switch ($ordstatus)
        {
          case"ADV":
            $colour = $ADVColour;
            $textcolour = $ADVTextColour;
            $description = "Waiting Advice Note";
            break;
          case"COM":
            $colour = $COMColour;
            $textcolour = $COMTextColour;
            $description = "Completed Line";
            break;
          case"CUS":
            $colour = $CUSColour;
            $textcolour = $CUSTextColour;
            $description = "Call Customer Back";
            break;
          case"HLD":
            $colour = $HLDColour;
            $textcolour = $HLDTextColour;
            $description = "Goods On Hold";
            break;
          case"IBT":
            $colour = $IBTColour;
            $textcolour = $IBTTextColour;
            $description = "Inter-Branch Transfer";
            break;
          case"KIT":
            $colour = $KITColour;
            $textcolour = $KITTextColour;
            $description = "Process Kit List";
            break;
          case"MEM":
            $colour = $MEMColour;
            $textcolour = $MEMTextColour;
            $description = "Memo Line (Quotations)";
            break;
          case"OFF":
            $colour = $OFFColour;
            $textcolour = $OFFTextColour;
            $description = "Call Off Later";
            break;
          case"PIK":
            $colour = $PIKColour;
            $textcolour = $PIKTextColour;
            $description = "Pick Note Printed";
            break;
          case"PRO":
            $colour = $PROColour;
            $textcolour = $PROTextColour;
            $description = "Process Document";
            break;
          case"PUR":
            $colour = $PURColour;
            $textcolour = $PURTextColour;
            $description = "Purchase Order";
            break;
          case"SBO":
            $colour = $SBOColour;
            $textcolour = $SBOTextColour;
            $description = "Stock Backorder";
            break;
          case"WDL":
            $colour = $WDLColour;
            $textcolour = $WDLTextColour;
            $description = "Waiting Delivery";
            break;
          case"WRK":
            $colour = $WRKColour;
            $textcolour = $WRKTextColour;
            $description = "Create Works Order";
            break;
        }

        // The comma only comes in after the first set

        if ($i <> 1)
        {
          $outstandingordersbystatusdata .= ",";
        }
        $outstandingLink = site_url("site/outstandingorder/" . $ordstatus . "/status");
        // Build the data string for the pie chart data
        $outstandingordersbystatusdata .= "{value:$value,color:'$colour',highlight:'$colour',label:'$ordstatus'}";

        // Build the string for the legend
        $outstandingordersbystatuslegend .= "<li><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'><i class='fa fa-circle-o $textcolour'></i> $ordstatus</a></li>";

        // Build the string for the table
        $outstandingordersbystatustable .= "<tr><td><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'>$ordstatus</a></td><td><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'>$description</a></td><td style='text-align: right'><a style='text-decoration:none;color:#333;' href='" . $outstandingLink . "'>" . $currency_symbol . number_format($value, 2) . "</a></td></tr>";

        $i++;
      }
    }
    $outstandingordersbystatustable .= "<tr><th>&nbsp</th><th>Total</th><th  style='text-align: right'>" . $currency_symbol . number_format($tmp_total, 2) . "</th></tr>";
    $outstandingordersbystatusdata .= "]";

    $data["outstandingordersbystatustable"] = $outstandingordersbystatustable;
    $data["outstandingordersbystatusdata"] = $outstandingordersbystatusdata;
    $data["outstandingordersbystatuslegend"] = $outstandingordersbystatuslegend;
    // Waiting Postin/////
    $waitingposting = $this->site_model->waitingposting($G_level, $G_todaysdate, $repclause, $G_branchno);

    $data["WaitingPostingCR"] = '0.00';
    $data["WaitingPostingSL"] = '0.00';

    foreach ($waitingposting as $wp)
    {
      if ($wp['identifier'] == "MIDASWAITSL")
      {
        $data["WaitingPostingSL"] = $wp['sum1'];
      }
      if ($wp['identifier'] == "MIDASWAITCR")
      {
        $data["WaitingPostingCR"] = $wp['sum1'];
      }
    }

    $lastthirty = $this->site_model->lastthirty($G_level, $repclause, $G_branchno);


    $OrdersFulfilledGraph = "[";
    $OrdersFulfilledGraphLabel = "[";
    $y = 0;

    foreach ($lastthirty as $lasth)
    {

      $fulfilleddate = $lasth["date"];
      $fulfilledlines = $lasth["sum1"];
      $totallines = $lasth["sum2"];

      if ($totallines <> 0)

      {
        $percentage = ($fulfilledlines / $totallines) * 100;
      }

      if ($y <> 0)
      {
        $OrdersFulfilledGraph .= ",";
        $OrdersFulfilledGraphLabel .= ",";
      }

      $OrdersFulfilledGraph .= number_format($percentage, 2);

      // Label will be day/month like 07/1204020

      $tmp_month = date("m", strtotime($fulfilleddate));
      $tmp_day = date("d", strtotime($fulfilleddate));
      $tmp_daymonth = $tmp_day . "/" . $tmp_month;

      $OrdersFulfilledGraphLabel .= "'$tmp_daymonth'";
      $y++;
    }
    $OrdersFulfilledGraph .= "]";
    $OrdersFulfilledGraphLabel .= "]";

    $data["OrdersFulfilledGraph"] = $OrdersFulfilledGraph;
    $data["OrdersFulfilledGraphLabel"] = $OrdersFulfilledGraphLabel;

    $this->load->view('dashboard_eighth', $data);

  }

  public function get_chart_data() {
    $userId = 0;
    $branchNo = 0;
    $s_data = array();

    $s_data['yearstartmonth'] = $this->site_model->getYearStartMonth();
    $s_data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
    $userType = $s_data['userType'] = $s_data['userDetail']['usertype'];

    $UserSes = $this->session->userdata('selectedUser');
    if (count($UserSes) > 0) {
      $userId = $UserSes['userid'];
      $branchNo = $UserSes['branchno'];
    }
    $headerUserId = $userId;

    $G_branchno = null;
    if ($userType == "B") $G_level = "Branch";
    elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0))
      $G_level = "Company";
    elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0))
    {
      $G_level = "Branch";
      $G_branchno = $branchNo;
    }
    elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0))
    {
      $G_level = "User";
      $G_userId = $userId;
      $s_data['userDetail'] = $this->site_model->getUserDetails($userId);
    }
    else
    {
      $G_level = "User";
    }

    if (is_null($G_branchno)) $G_branchno = $s_data['userDetail']['branch'];
    $repclause = $s_data["userDetail"]["repclause"];

    $s_data['year0'] = date("Y");
    $s_data['year3'] = $s_data['year0'] - 1;
    $s_data['thismonth'] = date("m");
    $s_data['curyearmonth'] = ($s_data['year0'] * 100) + $s_data['thismonth'];
    $G_userid = $this->session->userdata('userid');
    $targetUserId = $G_userid;
    if (!empty($headerUserId))
    {
      $targetUserId = $headerUserId;
    }

    $s_data['lastsalesdate'] = $this->site_model->getMaxDate($s_data['userDetail']['repwhere']);

    // -------------------------------------------------------------------------------------------------------------------------------------------------
    // GET LAST DAYS SALES FOR THE REP.
    // -------------------------------------------------------------------------------------------------------------------------------------------------

    $row_1 = $this->site_model->getSalesRepLastSales($s_data['lastsalesdate'], $s_data['userDetail']['repwhere'], $G_userid, $G_branchno, $G_level);
    $s_data['dailysales'] = $row_1['sales'];
    $s_data['dailycost'] = $row_1['cost'];



    $G_todaysdate = date("Y/m/d");
    $year = date("Y", strtotime($G_todaysdate));
    $s_data["year"] = $year;
    $soyyearmonth = ($s_data["year"] * 100) + 1;
    $row = $this->site_model->getmonthlySalesRepTarget($soyyearmonth, $G_userid, $G_branchno, $G_level);
    $tmp_monthlysalestarget = $s_data['monthlysalestarget'] = $row['salesTarget'] = $row['saletarget'];

    $targetDataYear = $this->site_model->getYearTargetData($targetUserId, $G_branchno, $G_level);
    $s_data["G_YearlySalesTarget"] += $G_YearlySalesTarget += $tmp_monthlysalestarget;
    $s_data["G_YearlySalesTarget"] = $targetDataYear['saletarget'];

    ///////////////////////////////////
    $s_data['sales'] = array();

    $s_data['yearmonth'] = array();
    $s_data['tmpyear'] = $s_data['year3'];
    $s_data['tmpmonth'] = 1;
    for ($x = 0; $x < 48; $x++)
    {
      $s_data['yearmonth'][$x] = ($s_data['tmpyear'] * 100) + $s_data['tmpmonth'];

      $s_data["sales"][$x] = 0;
      $s_data['costs'][$x] = 0;

      $s_data['tmpmonth'] = $s_data['tmpmonth'] + 1;
      if ($s_data['tmpmonth'] == 13)
      {
        $s_data['tmpmonth'] = 1;
        $s_data['tmpyear'] = $s_data['tmpyear'] + 1;
      }
    }
    // Get sales for the sales rep
    $result = $this->site_model->getSalesAnalisys($s_data['curyearmonth'], $s_data['userDetail']['repwhere'], $G_userid, $G_branchno, $G_level);


    $x = 0;
    foreach ($result as $row)
    {
      $s_data['salessummaryyearmonth'] = $row['yearmonth'];
      $s_data['salessummarysales'] = $row['sales'];
      $s_data['salessummarycost'] = $row['cost'];

      // For each data row, loop through the array and put the sales value in the correct place

      for ($x = 0; $x < 48; $x++)
      {
        //var_dump($s_data['yearmonth'][$x].'-'.$s_data['salessummaryyearmonth']);
        if ($s_data['yearmonth'][$x] == $s_data['salessummaryyearmonth'])
        {
          $s_data["sales"][$x] = $s_data['salessummarysales']; // If the year month of the data matches the array, put the value in
          $s_data['costs'][$x] = $s_data['salessummarycost'];
        }
      }
    }

    /// ////

    $lastthirty = $this->site_model->lastthirty($G_level, $repclause, $G_branchno);

    $OrdersFulfilledGraph = "";
    $OrdersFulfilledGraphLabel = "";
    $y = 0;

    foreach ($lastthirty as $lasth)
    {

      $fulfilleddate = $lasth["date"];
      $fulfilledlines = $lasth["sum1"];
      $totallines = $lasth["sum2"];

      if ($totallines <> 0)

      {
        $percentage = ($fulfilledlines / $totallines) * 100;
      }

      if ($y <> 0)
      {
        $OrdersFulfilledGraph .= ",";
        $OrdersFulfilledGraphLabel .= ",";
      }

      $OrdersFulfilledGraph .= number_format($percentage, 2);

      // Label will be day/month like 07/1204020

      $tmp_month = date("m", strtotime($fulfilleddate));
      $tmp_day = date("d", strtotime($fulfilleddate));
      $tmp_daymonth = $tmp_day . "/" . $tmp_month;

      $OrdersFulfilledGraphLabel .= "'$tmp_daymonth'";
      $y++;
    }
    $OrdersFulfilledGraph .= "";
    $OrdersFulfilledGraphLabel .= "";

    $s_data["OrdersFulfilledGraph"] = $OrdersFulfilledGraph;
    $s_data["OrdersFulfilledGraphLabel"] = $OrdersFulfilledGraphLabel;
    /////////////////////////


    $targetDataMonth = $this->site_model->getMonthTargetData($s_data['curyearmonth'], $targetUserId, $G_branchno, $G_level);
    $s_data['G_MonthlySalesTarget'] = $targetDataMonth['saletarget'];
    $G_todaysdate = date('Y/m/d');
    $daysinmonth = date('t', strtotime($G_todaysdate));

    $ProjectedSalesMonthGraphTarget = "";
    $ProjectedSalesMonthGraphProjected = "";
    $ProjectedSalesMonthGraphLabel = "";
    $s_data["daysinmonth"] = $daysinmonth;
    for ($x = 1; $x <= $daysinmonth; $x++)
    {

      $cumulativetarget[$x] = ($s_data["G_MonthlySalesTarget"] / $s_data["daysinmonth"]) * $x;
      $cumulativeprojected[$x] = ($s_data["projmonthsales"] / $s_data["daysinmonth"]) * $x;
      $ProjectedSalesMonthGraphTarget .= number_format($cumulativetarget[$x], 0, '.', '');
      $ProjectedSalesMonthGraphProjected .= number_format($cumulativeprojected[$x], 0, '.', '');

      // Only putting the first and last day number in the label as its too busy with all the days

      $ProjectedSalesMonthGraphLabel .= "'$x'";
      if ($x != $daysinmonth)
      {
        $ProjectedSalesMonthGraphTarget .= ",";
        $ProjectedSalesMonthGraphProjected .= ",";
        $ProjectedSalesMonthGraphLabel .= ",";
      }
    }

    $ProjectedSalesMonthGraphTarget .= "";
    $ProjectedSalesMonthGraphProjected .= "";
    $ProjectedSalesMonthGraphLabel .= "";
    //$ProjectedSalesMonthGraphLabel .= ",' ']";


    $s_data["ProjectedSalesMonthGraphTarget"] = $ProjectedSalesMonthGraphTarget;
    $s_data["ProjectedSalesMonthGraphProjected"] = $ProjectedSalesMonthGraphProjected;
    $s_data["ProjectedSalesMonthGraphLabel"] = $ProjectedSalesMonthGraphLabel;

    $BIColour = "#3c8dbc";    // Book Ins  		Light Blue
    $BOColour = "#f39c12";    // Book Outs  		Yellow
    $BTColour = "#001f3f";    // Branch Transfers	Navy
    $CRColour = "#dd4b39";    // Credits  		Red
    $DNColour = "#39cccc";    // Debit Notes  	Teal
    $QTColour = "#00c0ef";    // Quotations  		Aqua
    $SLColour = "#00a65a";    // Orders  			Green
    $WOColour = "#d2d6de";    // Works Orders  	Gray
    $RWColour = "#f44295";
    $TCColour = "#7a1919";
    $THColour = "#4f5960";

    // Assign legend colours to order types

    $BITextColour = "text-light-blue";    // Book Ins  		Light Blue
    $BOTextColour = "text-yellow";        // Book Outs  		Yellow
    $BTTextColour = "text-navy";        // Branch Transfers	Navy
    $CRTextColour = "text-red";            // Credits  		Red
    $DNTextColour = "text-teal";        // Debit Notes  	Teal
    $QTTextColour = "text-aqua";        // Quotations  		Aqua
    $SLTextColour = "text-green";        // Orders  			Green
    $WOTextColour = "text-gray";        // Works Orders  	Gray
    $RWTextColour = "text-rwcolor";
    $TCTextColour = "text-tccolor";
    $THTextColour = "text-thcolor";

    $todaysordersbytypedata = "";

    $outstandingordersbystatusdata = "";
    $todayOrders = $this->site_model->todayOrders($G_level, $G_todaysdate, $repclause, $G_branchno);

    $i = 1;
    $tmp_total = 0;

    foreach ($todayOrders as $today)
    {
      $identifier = $today['identifier'];
      $value = $today['actualvalue1'];
      // The order type is the last two characters of the identifier
      $ordtype = substr($identifier, 10, 2);
      // Only interested in graphing order types that have a value
      if ($value <> 0)
      {
        $tmp_total += $value;

        // Set the colour, which is the order type followed by"Colour"
        switch ($ordtype)
        {
          case"BI":
            $colour = $BIColour;
            $textcolour = $BITextColour;
            $description = "Book Ins";
            break;
          case"BO":
            $colour = $BOColour;
            $textcolour = $BOTextColour;
            $description = "Book Outs";
            break;
          case"BT":
            $colour = $BTColour;
            $textcolour = $BTTextColour;
            $description = "Branch Transfers";
            break;
          case"CR":
            $colour = $CRColour;
            $textcolour = $CRTextColour;
            $description = "Credit Notes";
            break;
          case"DN":
            $colour = $DNColour;
            $textcolour = $DNTextColour;
            $description = "Debit Notes";
            break;
          case"QT":
            $colour = $QTColour;
            $textcolour = $QTTextColour;
            $description = "Quotations";
            break;
          case"SL":
            $colour = $SLColour;
            $textcolour = $SLTextColour;
            $description = "Sales Orders";
            break;
          case"WO":
            $colour = $WOColour;
            $textcolour = $WOTextColour;
            $description = "Works Orders";
            break;
          case"RW":
            $colour = $RWColour;
            $textcolour = $RWTextColour;
            $description = "Repairs & Warranty";
            break;
          case"TC":
            $colour = $TCColour;
            $textcolour = $TCTextColour;
            $description = "Plant Hire Credit Note";
            break;
          case"TH":
            $colour = $THColour;
            $textcolour = $THTextColour;
            $description = "Plant Hire Order";
            break;
        }

        // The comma only comes in after the first set

        if ($i <> 1)
        {
          $todaysordersbytypedata .= ",";
        }

        // Build the data string for the pie chart data
        $todaysordersbytypedata .= "{value:$value,color:'$colour',highlight:'$colour',label:'$ordtype'}";

        // Build the string for the legend
        $typeLink = site_url("site/todaysorder/" . $ordtype . "/type");
        $todaysordersbytypelegend .= "<li><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'><i class='fa fa-circle-o $textcolour'></i> $ordtype</a></li>";
        // Build the string for the table
        $todaysordersbytypetable .= "<tr><td><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>$ordtype</a></td><td><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>$description</a></td><td align='right'><a style='text-decoration:none;color:#333;' href='" . $typeLink . "'>" . $currency_symbol . number_format($value, 2) . "</a></td></tr>";

        $i++;
      }
    }
    $todaysordersbytypetable .= "<tr><th>&nbsp</th><th>Total</th><th style='text-align: right'>" . $currency_symbol . number_format($tmp_total, 2) . "</th></tr>";
    $todaysordersbytypedata .= "";

    $s_data["todaysordersbytypedata"] = $todaysordersbytypedata;


    $ADVColour = "#f012be";    // Waiting advice note	Fuschia
    $COMColour = "#00a65a";    // Completed line		Green
    $CUSColour = "#39cccc";    // Call customer back	Teal
    $HLDColour = "#3d9970";    // Goods on hold		Olive
    $IBTColour = "#d2d6de";    // Inter-branch transfer	Gray
    $KITColour = "#01ff70";    // Process kit list		Lime
    $MEMColour = "#ff851b";    // Memo line			Orange
    $OFFColour = "#605ca8";    // Call off later		Purple
    $PIKColour = "#001f3f";    // Pick note printed	Navy
    $PROColour = "#3c8dbc";    // Process document		Light Blue
    $PURColour = "#dd4b39";    // Purchase order		Red
    $SBOColour = "#f39c12";    // Stock backorder		Yellow
    $WDLColour = "#00c0ef";    // Waiting delivery		Aqua
    $WRKColour = "#d81b60";    // Create works order	Maroon


    $ADVTextColour = "text-fuschia";    // Waiting advice note	Fuschia
    $COMTextColour = "text-green";        // Completed line		Green
    $CUSTextColour = "text-teal";        // Call customer back	Teal
    $HLDTextColour = "text-olive";        // Goods on hold		Olive
    $IBTTextColour = "text-gray";        // Inter-branch transfer	Gray
    $KITTextColour = "text-lime";        // Process kit list		Lime
    $MEMTextColour = "text-orange";        // Memo line			Orange
    $OFFTextColour = "text-purple";        // Call off later		Purple
    $PIKTextColour = "text-navy";        // Pick note printed	Navy
    $PROTextColour = "text-light-blue";    // Process document		Light Blue
    $PURTextColour = "text-red";        // Purchase order		Red
    $SBOTextColour = "text-yellow";        // Stock backorder		Yellow
    $WDLTextColour = "text-aqua";        // Waiting delivery		Aqua
    $WRKTextColour = "text-maroon";        // Cr
    $currency_symbol = $this->config->item("currency_symbol");
    $todaysordersbystatusdata = "";
    foreach ($todayOrders as $today)
    {

      $identifier = $today['identifier'];
      $value = $today['actualvalue1'];

      // The order type is the last three characters of the identifier
      $ordstatus = substr($identifier, 10, 3);
      // Only interested in graphing order statuses that have a value

      if ($value <> 0)
      {
        $tmp_total += $value;

        // Set the colour, which is the order status followed by"Colour"
        switch ($ordstatus)
        {
          case"ADV":
            $colour = $ADVColour;
            $textcolour = $ADVTextColour;
            $description = "Waiting Advice Note";
            break;
          case"COM":
            $colour = $COMColour;
            $textcolour = $COMTextColour;
            $description = "Completed Line";
            break;
          case"CUS":
            $colour = $CUSColour;
            $textcolour = $CUSTextColour;
            $description = "Call Customer Back";
            break;
          case"HLD":
            $colour = $HLDColour;
            $textcolour = $HLDTextColour;
            $description = "Goods On Hold";
            break;
          case"IBT":
            $colour = $IBTColour;
            $textcolour = $IBTTextColour;
            $description = "Inter-Branch Transfer";
            break;
          case"KIT":
            $colour = $KITColour;
            $textcolour = $KITTextColour;
            $description = "Process Kit List";
            break;
          case"MEM":
            $colour = $MEMColour;
            $textcolour = $MEMTextColour;
            $description = "Memo Line (Quotations)";
            break;
          case"OFF":
            $colour = $OFFColour;
            $textcolour = $OFFTextColour;
            $description = "Call Off Later";
            break;
          case"PIK":
            $colour = $PIKColour;
            $textcolour = $PIKTextColour;
            $description = "Pick Note Printed";
            break;
          case"PRO":
            $colour = $PROColour;
            $textcolour = $PROTextColour;
            $description = "Process Document";
            break;
          case"PUR":
            $colour = $PURColour;
            $textcolour = $PURTextColour;
            $description = "Purchase Order";
            break;
          case"SBO":
            $colour = $SBOColour;
            $textcolour = $SBOTextColour;
            $description = "Stock Backorder";
            break;
          case"WDL":
            $colour = $WDLColour;
            $textcolour = $WDLTextColour;
            $description = "Waiting Delivery";
            break;
          case"WRK":
            $colour = $WRKColour;
            $textcolour = $WRKTextColour;
            $description = "Create Works Order";
            break;
        }

        // The comma only comes in after the first set

        if ($i <> 1)
        {
          $todaysordersbystatusdata .= ",";
          $todaysordersbytypedata .= ",";
          $outstandingordersbystatusdata .= ",";
        }


        // Build the data string for the pie chart data
        $todaysordersbystatusdata .= "{value:$value,color:'$colour',highlight:'$colour',label:'$ordstatus'}";
        $typeLinkStatus = site_url("site/todaysorder/" . $ordstatus . "/status");
        // Build the string for the legend
        $todaysordersbystatuslegend .= "<li><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'><i class='fa fa-circle-o " . $textcolour . "'></i> $ordstatus</a></li>";

        // Build the string for the table
        $todaysordersbystatustable .= "<tr><td><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>$ordstatus</a></td><td><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>$description</a></td><td align='right'><a style='text-decoration:none;color:#333;' href='" . $typeLinkStatus . "'>" . $currency_symbol . number_format($value, 2) . "</a></td></tr>";

        $outstandingordersbystatusdata .= "{value:$value,color:'$colour',highlight:'$colour',label:'$ordstatus'}";
        $i++;
      }
    }

    $todaysordersbystatustable .= "<tr><th>&nbsp</th><th>Total</th><th  style='text-align: right'>" . $currency_symbol . number_format($tmp_total, 2) . "</th></tr>";
    $todaysordersbystatusdata .= "";
    $todaysordersbytypedata .= "";
    $outstandingordersbystatusdata .= "";
    $s_data["todaysordersbystatustable"] = $todaysordersbystatustable;
    $s_data["todaysordersbystatusdata"] = $todaysordersbystatusdata;
    $s_data["outstandingordersbystatusdata"] = $outstandingordersbystatusdata;
    $s_data["todaysordersbytypedata"] = $todaysordersbytypedata;


    $cumday = $this->site_model->cumday($G_level, $G_todaysdate, $repclause, $G_branchno);
    $cumday01sales = number_format($cumday[0]["SUM(day01sales)"], 0, '.', '');
    $cumday02sales = number_format($cumday[0]["SUM(day02sales)"] + $cumday01sales, 0, '.', '');
    $cumday03sales = number_format($cumday[0]["SUM(day03sales)"] + $cumday02sales, 0, '.', '');
    $cumday04sales = number_format($cumday[0]["SUM(day04sales)"] + $cumday03sales, 0, '.', '');
    $cumday05sales = number_format($cumday[0]["SUM(day05sales)"] + $cumday04sales, 0, '.', '');
    $cumday06sales = number_format($cumday[0]["SUM(day06sales)"] + $cumday05sales, 0, '.', '');
    $cumday07sales = number_format($cumday[0]["SUM(day07sales)"] + $cumday06sales, 0, '.', '');
    $cumday08sales = number_format($cumday[0]["SUM(day08sales)"] + $cumday07sales, 0, '.', '');
    $cumday09sales = number_format($cumday[0]["SUM(day09sales)"] + $cumday08sales, 0, '.', '');
    $cumday10sales = number_format($cumday[0]["SUM(day10sales)"] + $cumday09sales, 0, '.', '');
    $cumday11sales = number_format($cumday[0]["SUM(day11sales)"] + $cumday10sales, 0, '.', '');
    $cumday12sales = number_format($cumday[0]["SUM(day12sales)"] + $cumday11sales, 0, '.', '');
    $cumday13sales = number_format($cumday[0]["SUM(day13sales)"] + $cumday12sales, 0, '.', '');
    $cumday14sales = number_format($cumday[0]["SUM(day14sales)"] + $cumday13sales, 0, '.', '');
    $cumday15sales = number_format($cumday[0]["SUM(day15sales)"] + $cumday14sales, 0, '.', '');
    $cumday16sales = number_format($cumday[0]["SUM(day16sales)"] + $cumday15sales, 0, '.', '');
    $cumday17sales = number_format($cumday[0]["SUM(day17sales)"] + $cumday16sales, 0, '.', '');
    $cumday18sales = number_format($cumday[0]["SUM(day18sales)"] + $cumday17sales, 0, '.', '');
    $cumday19sales = number_format($cumday[0]["SUM(day19sales)"] + $cumday18sales, 0, '.', '');
    $cumday20sales = number_format($cumday[0]["SUM(day20sales)"] + $cumday19sales, 0, '.', '');
    $cumday21sales = number_format($cumday[0]["SUM(day21sales)"] + $cumday20sales, 0, '.', '');
    $cumday22sales = number_format($cumday[0]["SUM(day22sales)"] + $cumday21sales, 0, '.', '');
    $cumday23sales = number_format($cumday[0]["SUM(day23sales)"] + $cumday22sales, 0, '.', '');
    $cumday24sales = number_format($cumday[0]["SUM(day24sales)"] + $cumday23sales, 0, '.', '');
    $cumday25sales = number_format($cumday[0]["SUM(day25sales)"] + $cumday24sales, 0, '.', '');
    $cumday26sales = number_format($cumday[0]["SUM(day26sales)"] + $cumday25sales, 0, '.', '');
    $cumday27sales = number_format($cumday[0]["SUM(day27sales)"] + $cumday26sales, 0, '.', '');
    $cumday28sales = number_format($cumday[0]["SUM(day28sales)"] + $cumday27sales, 0, '.', '');
    $cumday29sales = number_format($cumday[0]["SUM(day29sales)"] + $cumday28sales, 0, '.', '');
    $cumday30sales = number_format($cumday[0]["SUM(day30sales)"] + $cumday29sales, 0, '.', '');
    $cumday31sales = number_format($cumday[0]["SUM(day31sales)"] + $cumday30sales, 0, '.', '');

    //------------------------//End Posted-----------------------------------------------------------------------------------------------------

    $s_data["ProjectedSalesMonthGraphActual"] = "$cumday01sales,$cumday02sales,$cumday03sales,$cumday04sales,$cumday05sales,$cumday06sales,$cumday07sales,$cumday08sales,$cumday09sales,$cumday10sales,$cumday11sales,$cumday12sales,$cumday13sales,$cumday14sales,$cumday15sales,$cumday16sales,$cumday17sales,$cumday18sales,$cumday19sales,$cumday20sales,$cumday21sales,$cumday22sales,$cumday23sales,$cumday24sales,$cumday25sales,$cumday26sales,$cumday27sales,$cumday28sales,$cumday29sales,$cumday30sales,$cumday31sales";
    $s_data['yearstartmonth'] = $this->site_model->getYearStartMonth();
    $start_month_delta = $s_data['yearstartmonth'] <= date('m') ? 11 + $s_data['yearstartmonth'] : $s_data['yearstartmonth'] - 1;

    // Already have this years sales by month, from the 3 year chart, so build chart data from that



    $cummth01sales = number_format($s_data["sales"][24 + $start_month_delta], 0, '.', '');
    $cummth02sales = number_format($cummth01sales + $s_data["sales"][25 + $start_month_delta], 0, '.', '');
    $cummth03sales = number_format($cummth02sales + $s_data["sales"][26 + $start_month_delta], 0, '.', '');
    $cummth04sales = number_format($cummth03sales + $s_data["sales"][27 + $start_month_delta], 0, '.', '');
    $cummth05sales = number_format($cummth04sales + $s_data["sales"][28 + $start_month_delta], 0, '.', '');
    $cummth06sales = number_format($cummth05sales + $s_data["sales"][29 + $start_month_delta], 0, '.', '');
    $cummth07sales = number_format($cummth06sales + $s_data["sales"][30 + $start_month_delta], 0, '.', '');
    $cummth08sales = number_format($cummth07sales + $s_data["sales"][31 + $start_month_delta], 0, '.', '');
    $cummth09sales = number_format($cummth08sales + $s_data["sales"][32 + $start_month_delta], 0, '.', '');
    $cummth10sales = number_format($cummth09sales + $s_data["sales"][33 + $start_month_delta], 0, '.', '');
    $cummth11sales = number_format($cummth10sales + $s_data["sales"][34 + $start_month_delta], 0, '.', '');
    $cummth12sales = number_format($cummth11sales + $s_data["sales"][35 + $start_month_delta], 0, '.', '');
    $s_data["ProjectedSalesYearGraphActual"] = "$cummth01sales,$cummth02sales,$cummth03sales,$cummth04sales,$cummth05sales,$cummth06sales,$cummth07sales,$cummth08sales,$cummth09sales,$cummth10sales,$cummth11sales,$cummth12sales";

    $s_data["ProjectedSalesYearGraphActual"] = $s_data["sales"];
    $ProjectedSalesYearGraphTarget = "";
    $ProjectedSalesYearGraphProjected = "";

    for ($x = 1; $x <= 12; $x++) {
      $cumulativeprojected[$x] = ($this->data["projyearsales"] / 12) * $x;

      $ProjectedSalesYearGraphTarget .= number_format(($s_data["G_YearlySalesTarget"] / 12) * $x, 0, '.', '');
      $ProjectedSalesYearGraphProjected .= number_format(($s_data["projyearsales"] / 12) * $x, 0, '.', '');

      if ($x != 12) {
        $ProjectedSalesYearGraphTarget .= ",";
        $ProjectedSalesYearGraphProjected .= ",";
      }
    }

    $ProjectedSalesYearGraphTarget .= "";
    $ProjectedSalesYearGraphProjected .= "";


    $s_data["ProjectedSalesYearGraphTarget"] = $ProjectedSalesYearGraphTarget;
    $s_data["ProjectedSalesYearGraphProjected"] = $ProjectedSalesYearGraphProjected;
    echo json_encode($s_data);
  }


  /* Function to set the session of the current selected branch */

  public function set_selected_branch_session($branchno)
  {

    if (!empty($branchno) || $branchno > 0)
    {
      $branchname = $this->site_model->getBranch($branchno);
      $data = array(
        "selectedBranch" => array(
          "branchno" => $branchno,
          "name" => $branchname
        )
      );
    }
    else
    {
      $data = array(
        "selectedBranch" => array(
          "branchno" => 0,
          "name" => 'Company Level'
        )
      );
    }
    $this->session->set_userdata($data);
    $this->session->unset_userdata('selectedUser');
    $this->session->userdata('selectedBranch');
    redirect('dashboard');
  }

  /* Function to get all the branches */

  public function getAllBranches()
  {
    $this->load->model('users/users_model');
    header('Content-Type: application/json');

    $branches = $this->site_model->getAllBranches();
    $userid = $this->session->userdata("userid");
    $userDetail = $this->users_model->getUserDetails($userid);
    $usertype = $userDetail['usertype'];
    $selectedBranch = $this->session->userdata("selectedBranch");
    if (empty($selectedBranch))
    {
      $selectedBranch['branchno'] = '0';
      $selectedBranch['name'] = 'Company Level';
    }
    else
    {
      //$lis[0] = "<li><a href='".site_url("site/set_selected_branch_session/0")."'>Company Level</a></li>";
    }
    $listIndex = 1;
    $lis[0] = "<li><a href='" . site_url("site/set_selected_branch_session/0") . "'>Company Level</a></li>";
    foreach ($branches as $branch)
    {
      $lis[$listIndex] = "<li><a href='" . site_url("site/set_selected_branch_session/" . $branch['branch']) . "'>" . $branch['name'] . "</a></li>";
      $listIndex++;
    }
    $li = implode("", $lis);
    echo json_encode(array('branches' => $li, 'selectedBranch' => $selectedBranch, 'usertype' => $usertype));
    exit;
  }

  public function daydrillreport()
  {
    if ($this->site_model->is_logged_in() == false)
    {
      redirect('/');
    }
    setcookie($this->config->item('site_name') . '_' . $this->session->userdata('userid') . '_last_visited', current_url(), time() + (86400 * 365), "/"); // 86400 = 1 day
    $data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
    $data['lastsalesdate'] = $this->site_model->getMaxDate($data['userDetail']['repwhere']);
    $data['lastsalesdate'] = date('d/m/Y', strtotime($data['lastsalesdate']));
    $data['main_content'] = 'daydrillreport';
    $this->load->view('customer/front_template', $data);
  }

  public function salesmtdreport()
  {
    if ($this->site_model->is_logged_in() == false)
    {
      redirect('/');
    }
    setcookie($this->config->item('site_name') . '_' . $this->session->userdata('userid') . '_last_visited', current_url(), time() + (86400 * 365), "/"); // 86400 = 1 day
    $data['main_content'] = 'salesmtdreport';
    $this->load->view('customer/front_template', $data);
  }

  public function ajaxdaydrillreport()
  {
    header('Content-Type: application/json');
    $userId = 0;
    $branchNo = 0;

    $userDetail = $this->site_model->getUserDetails($this->session->userdata('userid'));
    $lastsalesdate = $this->site_model->getMaxDate($userDetail['repwhere']);
    $lastsalesdate = strtotime($lastsalesdate);
    if (count($this->session->userdata('selectedUser')) > 0)
    {
      $UserSes = $this->session->userdata('selectedUser');
      $userId = $UserSes["userid"];
    }

    if (count($this->session->userdata('selectedBranch')) > 0)
    {
      $branchSes = $this->session->userdata('selectedBranch');
      $branchNo = $branchSes["branchno"];
    }
    $limit = 10;
    $start = isset($_POST["start"]) ? $_POST["start"] : 0;
    $length = isset($_POST["length"]) ? $_POST["length"] : $limit;
    $search = isset($_POST["search"]) ? $_POST["search"] : array();
    $draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;
    $search_key = $search['value'];
    $specific_search = $this->findPostedSpecificSearchAndMakec();
    $specific_order = $this->findPostedOrder();
    $recodeArray = $this->site_model->getUsersRepcodeCustom($userId);
    $totalData = $this->site_model->dayDrillData($lastsalesdate, $specific_search, $specific_order, $search_key, '', '', 1, $recodeArray, $branchNo);
    $reportData = $this->site_model->dayDrillData($lastsalesdate, $specific_search, $specific_order, $search_key, $start, $length, '2', $recodeArray, $branchNo);
    $return_array = array(
      "draw" => $draw,
      "recordsTotal" => $totalData,
      "recordsFiltered" => $totalData,
      "data" => $reportData
    );
    echo json_encode($return_array);
    exit;
  }

  public function ajaxsalesmtdreport()
  {
    header('Content-Type: application/json');
    $userId = 0;
    $branchNo = 0;

    if (count($this->session->userdata('selectedUser')) > 0)
    {
      $UserSes = $this->session->userdata('selectedUser');
      $userId = $UserSes["userid"];
    }

    if (count($this->session->userdata('selectedBranch')) > 0)
    {
      $branchSes = $this->session->userdata('selectedBranch');
      $branchNo = $branchSes["branchno"];
    }
    $limit = 10;
    $start = isset($_POST["start"]) ? $_POST["start"] : 0;
    $length = isset($_POST["length"]) ? $_POST["length"] : $limit;
    $search = isset($_POST["search"]) ? $_POST["search"] : array();
    $draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;
    $search_key = $search['value'];
    $specific_search = $this->findPostedSpecificSearchAndMakec();
    $specific_order = $this->findPostedOrder();
    $recodeArray = $this->site_model->getUsersRepcodeCustom($userId);
    $totalData = $this->site_model->salesmtdData($specific_search, $specific_order, $search_key, '', '', 1, $recodeArray, $branchNo);
    $reportData = $this->site_model->salesmtdData($specific_search, $specific_order, $search_key, $start, $length, '2', $recodeArray, $branchNo);
    $return_array = array(
      "draw" => $draw,
      "recordsTotal" => $totalData,
      "recordsFiltered" => $totalData,
      "data" => $reportData
    );
    echo json_encode($return_array);
    exit;
  }

  public function findPostedSpecificSearchAndMakec()
  {
    $posted_columns = $_POST['columns'];
    $search_keys = $this->getSpecificSearchKeys();
    $search = array();
    foreach ($posted_columns as $key => $col)
    {
      $search[$search_keys[$key]] = $col['search']['value'];
    }

    return $search;
  }

  public function getSpecificSearchKeys()
  {

    $search_keys = array('salesanalysis.account', 'customer.name', 'salesanalysis.orderno', 'salesanalysis.ordtype', 'salesanalysis.prodcode', 'product.description', 'salesanalysis.repcode', 'salesanalysis.quantity', 'salesanalysis.sales', 'salesanalysis.date');
    return $search_keys;
  }

  public function findPostedOrder()
  {
    $posted_order = $_POST['order'];
    $column_index = -1;
    $order = array(
      'by' => $search_keys[0],
      'dir' => 'asc'
    );

    if (isset($posted_order[0]['column']) && isset($posted_order[0]['dir']))
    {
      $column_index = $posted_order[0]['column'];

    }

    $search_keys = $this->getSpecificSearchKeys();
    if ($column_index >= 0)
    {
      $order = array(
        'by' => $search_keys[$column_index],
        'dir' => $posted_order[0]['dir']
      );
    }
    else
    {
      $order = array(
        'by' => $search_keys[0],
        'dir' => 'asc'
      );
    }

    return $order;
  }

  public function daydrill_excel_export()
  {
    $search_key = $this->uri->segment(3);
    $specific_search_keys = $this->getSpecificSearchKeys();
    header("Content-type: text/x-csv");
    //$previousDate=date('Y_m_d',strtotime("-1 days"));
    $csvName = 'day-drill-report.csv';
    header("Content-Disposition: attachment;filename=" . $csvName . "");
    header("Cache-Control: max-age=0");
    $userId = 0;
    $branchNo = 0;
    $userDetail = $this->site_model->getUserDetails($this->session->userdata('userid'));
    $lastsalesdate = $this->site_model->getMaxDate($userDetail['repwhere']);
    $lastsalesdate = strtotime($lastsalesdate);
    if (count($this->session->userdata('selectedUser')) > 0)
    {
      $UserSes = $this->session->userdata('selectedUser');
      $userId = $UserSes["userid"];
    }
    if (count($this->session->userdata('selectedBranch')) > 0)
    {
      $branchSes = $this->session->userdata('selectedBranch');
      $branchNo = $branchSes["branchno"];
    }
    $specific_search = $this->findPostedSpecificSearchAndMakec();
    $recodeArray = $this->site_model->getUsersRepcodeCustom($userId);
    $xlsOutput = $this->site_model->csv_daydrill_export($lastsalesdate, $specific_search, $search_key, $recodeArray, $branchNo);

    echo $xlsOutput;
    exit();
  }

  public function salesmtd_excel_exportcustom()
  {
    $search_key = $this->uri->segment($i);
    $specific_search_keys = $this->getSpecificSearchKeys();
    header("Content-type: text/x-csv");
    $csvName = 'sales-mtd-report.csv';
    header("Content-Disposition: attachment;filename=" . $csvName . "");
    header("Cache-Control: max-age=0");
    $userId = 0;
    $branchNo = 0;
    if (count($this->session->userdata('selectedUser')) > 0)
    {
      $UserSes = $this->session->userdata('selectedUser');
      $userId = $UserSes["userid"];
    }
    if (count($this->session->userdata('selectedBranch')) > 0)
    {
      $branchSes = $this->session->userdata('selectedBranch');
      $branchNo = $branchSes["branchno"];
    }
    $specific_search = $this->findPostedSpecificSearchAndMakec();
    $recodeArray = $this->site_model->getUsersRepcodeCustom($userId);
    //print_r($recodeArray);die('hello');
    $xlsOutput = $this->site_model->csv_mtd_export($specific_search, $search_key, $recodeArray, $branchNo);
    //$xlsOutput = $this->site_model->csv_daydrill_export($specific_search,$search_key,$recodeArray,$branchNo);

    echo $xlsOutput;
    exit();
  }

  public function todaysorder($segment, $by)
  {
    if (!empty($segment) && !empty($by))
    {
      if ($this->site_model->is_logged_in() == false)
      {
        redirect('/');
      }
      setcookie($this->config->item('site_name') . '_' . $this->session->userdata('userid') . '_last_visited', current_url(), time() + (86400 * 365), "/"); // 86400 = 1 day
      $userId = 0;
      $branchNo = 0;
      if ($by == "type")
      {
        $segmentArray = array("BI" => "Book Ins", "BO" => "Book Outs", "BT" => "Branch Transfers", "CR" => "Credit Notes", "DN" => "Debit Notes", "QT" => "Quotations", "SL" => "Sales Orders", "WO" => "Works Orders", "RW" => "Repairs & Warranty");
      }
      else
      {
        $segmentArray = array("ADV" => "Waiting Advice Note", "COM" => "Completed Line", "CUS" => "Call Customer Back", "HLD" => "Goods On Hold", "IBT" => "Inter-Branch Transfer", "KIT" => "Process Kit List", "MEM" => "Memo Line (Quotations)", "OFF" => "Call Off Later", "PIK" => "Pick Note Printed", "PRO" => "Process Document", "PUR" => "Purchase Order", "SBO" => "Stock Backorder", "WDL" => "Waiting Delivery", "WRK" => "Create Works Order");
      }
      if (isset($segmentArray[$segment]))
      {
        $data['headTitle'] = $segmentArray[$segment];
      }
      else
      {
        redirect('/');
      }
      if (count($this->session->userdata('selectedUser')) > 0)
      {
        $UserSes = $this->session->userdata('selectedUser');
        $userId = $UserSes["userid"];
      }

      if (count($this->session->userdata('selectedBranch')) > 0)
      {
        $branchSes = $this->session->userdata('selectedBranch');
        $branchNo = $branchSes["branchno"];
      }
      $recodeArray = $this->site_model->getUsersRepcodeCustom($userId);
      $data['reportData'] = $this->site_model->todayOrdersBySegment($recodeArray, $branchNo, $segment, $by);
      $data['main_content'] = 'segmentreport';
      $this->load->view('customer/front_template', $data);
    }
    else
    {
      redirect('/');
    }
  }

  public function outstandingorder($segment, $by)
  {
    if (!empty($segment) && !empty($by))
    {
      if ($this->site_model->is_logged_in() == false)
      {
        redirect('/');
      }
      setcookie($this->config->item('site_name') . '_' . $this->session->userdata('userid') . '_last_visited', current_url(), time() + (86400 * 365), "/"); // 86400 = 1 day
      $userId = 0;
      $branchNo = 0;
      if ($by == "status")
      {
        $segmentArray = array("ADV" => "Waiting Advice Note", "COM" => "Completed Line", "CUS" => "Call Customer Back", "HLD" => "Goods On Hold", "IBT" => "Inter-Branch Transfer", "KIT" => "Process Kit List", "MEM" => "Memo Line (Quotations)", "OFF" => "Call Off Later", "PIK" => "Pick Note Printed", "PRO" => "Process Document", "PUR" => "Purchase Order", "SBO" => "Stock Backorder", "WDL" => "Waiting Delivery", "WRK" => "Create Works Order");
      }
      if (isset($segmentArray[$segment]))
      {
        $data['headTitle'] = $segmentArray[$segment];
      }
      else
      {
        redirect('/');
      }
      if (count($this->session->userdata('selectedUser')) > 0)
      {
        $UserSes = $this->session->userdata('selectedUser');
        $userId = $UserSes["userid"];
      }

      if (count($this->session->userdata('selectedBranch')) > 0)
      {
        $branchSes = $this->session->userdata('selectedBranch');
        $branchNo = $branchSes["branchno"];
      }
      $recodeArray = $this->site_model->getUsersRepcodeCustom($userId);
      $data['reportData'] = $this->site_model->OutstandingOrdersBySegment($recodeArray, $branchNo, $segment, $by);
      $data['main_content'] = 'outstandingsegmentreport';
      $this->load->view('customer/front_template', $data);
    }
    else
    {
      redirect('/');
    }
  }

  public function getprojectedmonthdata($stat, $currdatemonthindicator)
  {

    //  echo $currdatemonthindicator;
    if ($stat == 'prev')
    {
      $month = date('n', strtotime($currdatemonthindicator . ' -1 month'));
      $year = date('Y', strtotime($currdatemonthindicator . ' -1 month'));
    }
    else
    {
      $month = date('n', strtotime($currdatemonthindicator . ' +1 month'));
      $year = date('Y', strtotime($currdatemonthindicator . ' +1 month'));
    }
    $returnArray = array();
    $d = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    if ((date('m', strtotime($currdatemonthindicator)) == date('m', strtotime('-1 month'))) && ($stat == 'next'))
    {
      $dateofMonth = date('d', time());
    }
    else
    {
      $dateofMonth = $d;
    }
    $G_todaysdate = date("Y/m/d", strtotime($year . '-' . $month . '-' . $dateofMonth));
    //echo $dateofMonth.'  '.$G_todaysdate;die;
    $daysinmonth = date("t", strtotime($G_todaysdate));
    $data['year0'] = date("Y");
    $data['year1'] = $data['year0'] - 1;
    $data['year2'] = $data['year0'] - 2;
    $data['curyearmonth'] = (date('Y', time()) * 100) + $month;
    $date = $G_todaysdate;
    $row = $this->site_model->workingDays($date);
    $data['dayno'] = $row['dayno'];  // Current working day number
    $data['totdays'] = $row['totdays'];
    $G_userid = $this->session->userdata("userid");
    $data['userDetail'] = $this->site_model->getUserDetails($this->session->userdata('userid'));
    $userType = $data['userType'] = $data['userDetail']['usertype'];
    $userId = 0;
    $branchNo = 0;
    if (count($this->session->userdata('selectedUser')) > 0)
    {
      $UserSes = $this->session->userdata('selectedUser');
      $userId = $UserSes["userid"];
    }
    if (count($this->session->userdata('selectedBranch')) > 0)
    {
      $branchSes = $this->session->userdata('selectedBranch');
      $branchNo = $branchSes["branchno"];
    }
    if ($userType == "B")
    {
      $G_level = "branch";
    }

    if ($userType == "B")
    {
      $G_level = "Branch";
    }
    elseif (($userType == "A") && ($branchNo == 0) && ($userId == 0))
    {
      $G_level = "Company";
    }
    elseif (($userType == "A") && ($branchNo > 0) && ($userId == 0))
    {

      $G_level = "Branch";
      $G_branchno = $branchNo;
    }
    elseif (($userType == "A") && ($branchNo == 0) && ($userId > 0))
    {
      $G_level = "User";
      $G_userId = $userId;
      $data['userDetail'] = $this->site_model->getUserDetails($userId);
    }
    else
    {
      $G_level = "User";
    }
    if ($G_branchno == 0 && $G_level != 'Branch')
    {
      $G_branchno = $data['userDetail']['branch'];
    }
    $userKpi = $this->site_model->userKpi($G_level, $G_branchno, $G_userid);

    $data = GetKpiDataForTwoYearVsTargetChart($userKpi, $data, $G_level);

    $returnArray['G_kpithreshold1'] = $data["G_kpithreshold1"];
    $returnArray['G_kpithreshold2'] = $data["G_kpithreshold2"];
    $targetDataMonth = $this->site_model->getMonthTargetData($data['curyearmonth'], $userId, $branchNo, $G_level);

    $data['G_MonthlySalesTarget'] = $targetDataMonth['saletarget'];
    $data['yearmonth'] = array();
    $data['monthnames'] = array();
    $data['sales'] = array();
    $data['costs'] = array();
    $data['tmpyear'] = $data['year2']; //CR0001 $year3;
    $data['tmpmonth'] = 1;
    $data['startyearmonth'] = ($data['year2'] * 100) + 1;
    $data['curyearmonth'] = ($data['year0'] * 100) + $month;
    for ($x = 0; $x <= 36; $x++)
    {
      $data['yearmonth'][$x] = ($data['tmpyear'] * 100) + $data['tmpmonth'];

      $data['sales'][$x] = 0;
      $data['costs'][$x] = 0;

      $data['tmpmonth'] = $data['tmpmonth'] + 1;
      if ($data['tmpmonth'] == 13)
      {
        $data['tmpmonth'] = 1;
        $data['tmpyear'] = $data['tmpyear'] + 1;
      }
    }
    $result = $this->site_model->getSalesAnalisys($data['curyearmonth'], $data['userDetail']['repwhere'], $G_userid, $G_branchno, $G_level);

    $x = 0;

    foreach ($result as $row)
    {
      $data['salessummaryyearmonth'] = $row['yearmonth'];
      $data['salessummarysales'] = $row['sales'];
      $data['salessummarycost'] = $row['cost'];

      // For each data row, loop through the array and put the sales value in the correct place

      for ($x = 0; $x <= 36; $x++)
      {
        if ($data['yearmonth'][$x] == $data['salessummaryyearmonth'])
        {
          $data['sales'][$x] = $data['salessummarysales']; // If the year month of the data matches the array, put the value in
          $data['costs'][$x] = $data['salessummarycost'];
        }
      }
    }
    $custyrmn = date('Ym', strtotime($G_todaysdate));
    $currkey = array_search($custyrmn, $data['yearmonth']);
    if (!empty($currkey))
    {
      $data['monthlysales'] = $data['sales'][$currkey];
    }
    else
    {
      $data['monthlysales'] = 0;
    }

    $returnArray["projmonthsalespc"] = '';
    $data["projmonthsales"] = 0;
    if ($data["monthlysales"] <> 0)
    {
      $data["projdaysales"] = ($data["monthlysales"] / $data["dayno"]);
      $data["projmonthsales"] = $data["projdaysales"] * $data["totdays"]; // Extrapolate projected sales
      if ($data["G_MonthlySalesTarget"] <> 0)
      {
        $data["projmonthsalespc"] = ($data["projmonthsales"] / $data["G_MonthlySalesTarget"]) * 100;
      }
    }
    if (isset($data["projmonthsalespc"]))
    {
      $returnArray["projmonthsalespc"] = $data["projmonthsalespc"];
    }
    $repclause = $data["userDetail"]["repclause"];
    $cumday = $this->site_model->cumday($G_level, $G_todaysdate, $repclause, $G_branchno);
    //print_r($cumday);die;
    $cumday01sales = number_format($cumday[0]["SUM(day01sales)"], 0, '.', '');
    $cumday02sales = number_format($cumday[0]["SUM(day02sales)"] + $cumday01sales, 0, '.', '');
    $cumday03sales = number_format($cumday[0]["SUM(day03sales)"] + $cumday02sales, 0, '.', '');
    $cumday04sales = number_format($cumday[0]["SUM(day04sales)"] + $cumday03sales, 0, '.', '');
    $cumday05sales = number_format($cumday[0]["SUM(day05sales)"] + $cumday04sales, 0, '.', '');
    $cumday06sales = number_format($cumday[0]["SUM(day06sales)"] + $cumday05sales, 0, '.', '');
    $cumday07sales = number_format($cumday[0]["SUM(day07sales)"] + $cumday06sales, 0, '.', '');
    $cumday08sales = number_format($cumday[0]["SUM(day08sales)"] + $cumday07sales, 0, '.', '');
    $cumday09sales = number_format($cumday[0]["SUM(day09sales)"] + $cumday08sales, 0, '.', '');
    $cumday10sales = number_format($cumday[0]["SUM(day10sales)"] + $cumday09sales, 0, '.', '');
    $cumday11sales = number_format($cumday[0]["SUM(day11sales)"] + $cumday10sales, 0, '.', '');
    $cumday12sales = number_format($cumday[0]["SUM(day12sales)"] + $cumday11sales, 0, '.', '');
    $cumday13sales = number_format($cumday[0]["SUM(day13sales)"] + $cumday12sales, 0, '.', '');
    $cumday14sales = number_format($cumday[0]["SUM(day14sales)"] + $cumday13sales, 0, '.', '');
    $cumday15sales = number_format($cumday[0]["SUM(day15sales)"] + $cumday14sales, 0, '.', '');
    $cumday16sales = number_format($cumday[0]["SUM(day16sales)"] + $cumday15sales, 0, '.', '');
    $cumday17sales = number_format($cumday[0]["SUM(day17sales)"] + $cumday16sales, 0, '.', '');
    $cumday18sales = number_format($cumday[0]["SUM(day18sales)"] + $cumday17sales, 0, '.', '');
    $cumday19sales = number_format($cumday[0]["SUM(day19sales)"] + $cumday18sales, 0, '.', '');
    $cumday20sales = number_format($cumday[0]["SUM(day20sales)"] + $cumday19sales, 0, '.', '');
    $cumday21sales = number_format($cumday[0]["SUM(day21sales)"] + $cumday20sales, 0, '.', '');
    $cumday22sales = number_format($cumday[0]["SUM(day22sales)"] + $cumday21sales, 0, '.', '');
    $cumday23sales = number_format($cumday[0]["SUM(day23sales)"] + $cumday22sales, 0, '.', '');
    $cumday24sales = number_format($cumday[0]["SUM(day24sales)"] + $cumday23sales, 0, '.', '');
    $cumday25sales = number_format($cumday[0]["SUM(day25sales)"] + $cumday24sales, 0, '.', '');
    $cumday26sales = number_format($cumday[0]["SUM(day26sales)"] + $cumday25sales, 0, '.', '');
    $cumday27sales = number_format($cumday[0]["SUM(day27sales)"] + $cumday26sales, 0, '.', '');
    $cumday28sales = number_format($cumday[0]["SUM(day28sales)"] + $cumday27sales, 0, '.', '');
    $cumday29sales = number_format($cumday[0]["SUM(day29sales)"] + $cumday28sales, 0, '.', '');
    $cumday30sales = number_format($cumday[0]["SUM(day30sales)"] + $cumday29sales, 0, '.', '');
    $cumday31sales = number_format($cumday[0]["SUM(day31sales)"] + $cumday30sales, 0, '.', '');
    $data["ProjectedSalesMonthGraphActual"] = "$cumday01sales,$cumday02sales,$cumday03sales,$cumday04sales,$cumday05sales,$cumday06sales,$cumday07sales,$cumday08sales,$cumday09sales,$cumday10sales,$cumday11sales,$cumday12sales,$cumday13sales,$cumday14sales,$cumday15sales,$cumday16sales,$cumday17sales,$cumday18sales,$cumday19sales,$cumday20sales,$cumday21sales,$cumday22sales,$cumday23sales,$cumday24sales,$cumday25sales,$cumday26sales,$cumday27sales,$cumday28sales,$cumday29sales,$cumday30sales,$cumday31sales";
    $returnArray['ProjectedSalesMonthGraphActual'] = $data["ProjectedSalesMonthGraphActual"];
    $ProjectedSalesMonthGraphTarget = "";
    $ProjectedSalesMonthGraphProjected = "";
    $ProjectedSalesMonthGraphLabel = "";
    $data["daysinmonth"] = $daysinmonth;
    for ($x = 1; $x <= $daysinmonth; $x++)
    {
      $cumulativetarget[$x] = ($data["G_MonthlySalesTarget"] / $data["daysinmonth"]) * $x;
      $cumulativeprojected[$x] = ($data["projmonthsales"] / $data["daysinmonth"]) * $x;

      $ProjectedSalesMonthGraphTarget .= number_format($cumulativetarget[$x], 0, '.', '');
      $ProjectedSalesMonthGraphProjected .= number_format($cumulativeprojected[$x], 0, '.', '');

      // Only putting the first and last day number in the label as its too busy with all the days

      if ($x == 1 or $x == $daysinmonth)
      {
        $ProjectedSalesMonthGraphLabel .= "$x";
      }
      else
      {
        $ProjectedSalesMonthGraphLabel .= " ";
      }
      if ($x != $daysinmonth)
      {
        $ProjectedSalesMonthGraphTarget .= ",";
        $ProjectedSalesMonthGraphProjected .= ",";
        $ProjectedSalesMonthGraphLabel .= ",";
      }
    }
    $ProjectedSalesMonthGraphTarget .= "";
    $ProjectedSalesMonthGraphProjected .= "";
    $ProjectedSalesMonthGraphLabel .= ", ";
    $data["ProjectedSalesMonthGraphTarget"] = $ProjectedSalesMonthGraphTarget;
    $data["ProjectedSalesMonthGraphProjected"] = $ProjectedSalesMonthGraphProjected;
    $data["ProjectedSalesMonthGraphLabel"] = $ProjectedSalesMonthGraphLabel;
    $returnArray['ProjectedSalesMonthGraphTarget'] = $data["ProjectedSalesMonthGraphTarget"];
    $returnArray['ProjectedSalesMonthGraphProjected'] = $data["ProjectedSalesMonthGraphProjected"];
    $returnArray['ProjectedSalesMonthGraphLabel'] = $data["ProjectedSalesMonthGraphLabel"];
    $returnArray['fillColor'] = '';
    $returnArray['strokeColor'] = '';
    $returnArray['pointColor'] = '';
    $returnArray['pointStrokeColor'] = '';
    if (empty($returnArray['projmonthsalespc']))
    {
      $returnArray['fillColor'] = '#00a65a';
    }
    elseif (empty($returnArray['projmonthsalespc']))
    {
      $returnArray['fillColor'] = '#00a65a';
    }
    elseif ($returnArray['projmonthsalespc'] < $returnArray['G_kpithreshold1'])
    {
      $returnArray['fillColor'] = '#dd4b39';
    }
    elseif ($returnArray['projmonthsalespc'] >= $returnArray['G_kpithreshold1'] and $returnArray['projmonthsalespc'] < $returnArray['G_kpithreshold2'])
    {
      $returnArray['fillColor'] = '#f39c12';
    }
    elseif ($returnArray['projmonthsalespc'] > $returnArray['G_kpithreshold2'])
    {
      $returnArray['fillColor'] = '#00a65a';
    }
    else
    {
      $returnArray['fillColor'] = '#00000';
    }
    if (empty($returnArray['projmonthsalespc']))
    {
      $returnArray['strokeColor'] = '#00a65a';
    }
    elseif ($returnArray['projmonthsalespc'] < $returnArray['G_kpithreshold1'])
    {
      $returnArray['strokeColor'] = '#dd4b39';
    }
    elseif ($returnArray['projmonthsalespc'] >= $returnArray['G_kpithreshold1'] and $returnArray['projmonthsalespc'] < $returnArray['G_kpithreshold2'])
    {
      $returnArray['strokeColor'] = '#f39c12';
    }
    elseif ($returnArray['projmonthsalespc'] > $returnArray['G_kpithreshold2'])
    {
      $returnArray['strokeColor'] = '#00a65a';
    }
    else
    {
      $returnArray['strokeColor'] = '#00000';
    }
    if (empty($returnArray['projmonthsalespc']))
    {
      $returnArray['pointColor'] = '#00a65a';
    }
    elseif ($returnArray['projmonthsalespc'] < $returnArray['G_kpithreshold1'])
    {
      $returnArray['pointColor'] = '#dd4b39';
    }
    elseif ($returnArray['projmonthsalespc'] >= $returnArray['G_kpithreshold1'] and $returnArray['projmonthsalespc'] < $returnArray['G_kpithreshold2'])
    {
      $returnArray['pointColor'] = '#f39c12';
    }
    elseif ($returnArray['projmonthsalespc'] > $returnArray['G_kpithreshold2'])
    {
      $returnArray['pointColor'] = '#00a65a';
    }
    else
    {
      $returnArray['pointColor'] = '#00000';
    }
    if (empty($returnArray['projmonthsalespc']))
    {
      $returnArray['pointStrokeColor'] = '#00a65a';
    }
    elseif ($returnArray['projmonthsalespc'] < $returnArray['G_kpithreshold1'])
    {
      $returnArray['pointStrokeColor'] = '#dd4b39';
    }
    elseif ($returnArray['projmonthsalespc'] >= $returnArray['G_kpithreshold1'] and $returnArray['projmonthsalespc'] < $returnArray['G_kpithreshold2'])
    {
      $returnArray['pointStrokeColor'] = '#f39c12';
    }
    elseif ($returnArray['projmonthsalespc'] > $returnArray['G_kpithreshold2'])
    {
      $returnArray['pointStrokeColor'] = '#00a65a';
    }
    else
    {
      $returnArray['pointStrokeColor'] = '#00000';
    }
    $returnArray['projColor'] = '';
    if ($returnArray['projmonthsalespc'] < $returnArray['G_kpithreshold1'])
    {
      $returnArray['projColor'] = "text-red";
    }
    if ($returnArray['projmonthsalespc'] >= $returnArray['G_kpithreshold1'] and $returnArray['projmonthsalespc'] < $returnArray['G_kpithreshold2'])
    {
      $returnArray['projColor'] = "text-yellow";
    }
    if ($returnArray['projmonthsalespc'] >= $returnArray['G_kpithreshold2'])
    {
      $returnArray['projColor'] = "text-green";
    }
    if (empty($returnArray['projmonthsalespc']))
    {
      $returnArray['projColor'] = "text-green";
    }

    unset($returnArray['G_kpithreshold1']);
    unset($returnArray['G_kpithreshold2']);
    unset($returnArray['projmonthsalespc']);
    $returnArray['monthyearindicator'] = date('M Y', strtotime($G_todaysdate));
    $returnArray['currdatemonthindicatorCust'] = date('Y-m-01', strtotime($G_todaysdate));
    $returnArray['disablenext'] = 0;
    if (date('m', strtotime($G_todaysdate)) == date('m'))
    {
      $returnArray['disablenext'] = 1;
    }

    echo json_encode($returnArray);
    die;
  }


  public function manage_cookie()
  {
    if ($this->input->server('REQUEST_METHOD') == 'POST')
    {
      $this->load->helper('cookie');
      $cookie_name = $this->input->post('cookie_name');
      $cookie_value = $this->input->post('cookie_value');

      $cookie = array(
        'name' => $cookie_name,
        'value' => $cookie_value,
        'expire' => '315360000', //cookie expires in 10 years!
        'secure' => TRUE);
      set_cookie($cookie);
    }
    else
    {
      redirect('dashboard');
    }
  }

  ////// new controllers.
  function newGetUserDetails() {
    $userid = $this->session->userdata("userid");
    $userDetail = $this->users_model->getUserDetails($userid);
    echo json_encode($userDetail);
  }

}
