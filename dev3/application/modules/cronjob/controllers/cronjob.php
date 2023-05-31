<?php
class Cronjob extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this
            ->load
            ->model('cronjob_model');
        $this
            ->load
            ->library('session');

    }

    public function monthly()
    {

        $users = $this
            ->cronjob_model
            ->all_salesemail_users();
        $year = date("Y");
        $month = date("m");
        $yearmonth = $year . $month;

        foreach ($users as $user)
        {
            $user->userid = 1;
            $data['userDetail'] = $this
                ->cronjob_model
                ->getUserDetails($user->userid);
            $userType = $data['userType'] = $data['userDetail']['usertype'];

            $repclause = $data["userDetail"]["repclause"];
            $column = "msales0";

            $pac['pac1'] = $this
                ->cronjob_model
                ->pac1($user->userid, $yearmonth, $repclause, $column);
            $pac['pac2'] = $this
                ->cronjob_model
                ->pac2($user->userid, $yearmonth, $repclause, $column);
            $pac['pac3'] = $this
                ->cronjob_model
                ->pac3($user->userid, $yearmonth, $repclause, $column);
            $pac['pac4'] = $this
                ->cronjob_model
                ->pac4($user->userid, $yearmonth, $repclause, $column);

            $table .= '<table id="customers" style="font-family: Trebuchet MS, Arial, Helvetica, sans-serif;border-collapse: collapse;width: 60%;"> 
  <tr>
    <th style=" border: 1px solid #ddd;padding: 8px;background-color: #367fa9; color:#fff; ">PAC </th>
    <th style=" border: 1px solid #ddd;padding: 8px;background-color: #367fa9; color:#fff; ">Description </th>
  
    <th style=" border: 1px solid #ddd;padding: 8px;background-color: #367fa9; color:#fff;">Sales MTD</th>
    <th style=" border: 1px solid #ddd;padding: 8px;background-color: #367fa9; color:#fff;">Target</th>
    <th style=" border: 1px solid #ddd;padding: 8px;background-color: #367fa9; color:#fff;">Progress</th>
  </tr>';

            foreach ($pac as $pc)
            {
                foreach ($pc as $p)
                {

                    $progress=round($p->salesmtd * 100 / $p->salestarget, 2);
                    
                    if($progress >=0 && $progress<30)
                    {
                         $background="none";
                         $color="#000";
                    }
                    elseif($progress >=31 && $progress<70)
                    {
                         $background="none";
                         $color="#000";
                    }
                    elseif($progress>70)
                    {
                      $background="none";
                         $color="#000";
                    }
                    else
                    {
                        $background="none";
                         $color="#000";
                    }



                    $table .= "<tr>
              
              <td style=' border: 1px solid #ddd;padding: 8px;'>" . $p->code . "</td>
              
             <td style=' border: 1px solid #ddd;padding: 8px;'>";

                    if ($p->description != "")
                    {
                        $table .= $p->description;
                    }
                    else
                    {
                        $table .= '0';
                    }
                    $table .= "</td><td style=' border: 1px solid #ddd;padding: 8px;'>";
                    if ($p->salesmtd <> 0)
                    {
                        $table .= $p->salesmtd;
                    }
                    else
                    {
                        $table .= '0';
                    }
                    $table .= "</td><td style=' border: 1px solid #ddd;padding: 8px;'>";
                    if ($p->salestarget <> 0)
                    {
                        $table .= $p->salestarget;
                    }
                    else
                    {
                        $table .= '0';
                    }

                    $table .= "</td><td style=' border: 1px solid #ddd;padding: 8px; color:".$color.";background:".$background.";' ><center><b>" .$progress. "%</b></center></td></tr>";

                }
            }

            $table .= '</table>';

            $htmlContent = '<html style="font-family: Trebuchet MS, Arial, Helvetica, sans-serif;"><head><title>Sales Report</title>
</head>';



            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => 'smtp.123-reg.co.uk',
                'smtp_port' => 465,
                'smtp_user' => 'system@mi-das.co.uk',
                'smtp_pass' => '$s\+\>/Spu@3fnT,',
                'mailtype' => 'html',
                'charset' => 'utf-8'
            );

            $config['protocol'] = 'sendmail';
            $config['mailpath'] = '/usr/sbin/sendmail';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = true;

            $this
                ->load
                ->library('email');

            $this
                ->email
                ->initialize($config);
            $this
                ->email
                ->set_mailtype("html");
            $this
                ->email
                ->set_newline("\r\n");
            $htmlContent .= '<h2>Hi ' . $user->firstname . '' . $user->surname . '</h2>';
            $htmlContent .= '<h4>Your Sales Report Of ' . date("M") . ' ' . date("Y") . ' is below: </h4>';

            $htmlContent .= $table;

            $htmlContent .= '</html>';
            echo $htmlContent; exit;
            //$this->email->to($user->email);
            $this
                ->email
                ->clear();
            $this
                ->email
                ->to('kieran@kk-cs.co.uk');
            $this
                ->email
                ->from('system@mi-das.co.uk', 'mi-das');
            $this
                ->email
                ->subject('Sales Report : Monthly');
            $this
                ->email
                ->message($htmlContent);

            if ($this
                ->email
                ->send())
            {
                echo 'sent<br>';
            }
            else
            {
                echo 'fail<br>';
            }

            break;

        }

        //print_r($users);
        
    }

    public function yearly()
    {

        $users = $this
            ->cronjob_model
            ->all_salesemail_users();
        $year = date("Y");
        $month = date("m");
        $yearmonth = $year . $month;

        foreach ($users as $user)
        {

            $user->userid = 1;
            $data['userDetail'] = $this
                ->cronjob_model
                ->getUserDetails($user->userid);
            $userType = $data['userType'] = $data['userDetail']['usertype'];

            $repclause = $data["userDetail"]["repclause"];

            $monthArray = range(1, 12);

            $table .= '<table id="customers" style="font-family: Trebuchet MS, Arial, Helvetica, sans-serif;border-collapse: collapse;width: 60%;"> 
  <tr>
    <th style=" border: 1px solid #ddd;padding: 8px;background-color: #367fa9; color:#fff; ">PAC </th>
    <th style=" border: 1px solid #ddd;padding: 8px;background-color: #367fa9; color:#fff; ">Description </th>
  
    <th style=" border: 1px solid #ddd;padding: 8px;background-color: #367fa9; color:#fff;">Sales MTD</th>
    <th style=" border: 1px solid #ddd;padding: 8px;background-color: #367fa9; color:#fff;">Target</th>
    <th style=" border: 1px solid #ddd;padding: 8px;background-color: #367fa9; color:#fff;">Progress</th>
  </tr>';
            // foreach ($monthArray as $month) {
            //        $monthPadding = str_pad($month, 2, "0", STR_PAD_LEFT);
            //        $fdate = date("F", strtotime("2015-$monthPadding-01"));
            //          $fdate = date("F", strtotime("2015-$monthPadding-01"));
            //           $yearmonth=date("Y").$monthPadding;
            

            $currentmonth = date("m");
            $temp = (int)$currentmonth;
            for ($i = 0;$i < $currentmonth;$i++)
            {

                $monthPadding = str_pad($i + 1, 2, "0", STR_PAD_LEFT);
                $fdate = date("F", mktime(0, 0, 0, $i + 1, 10));
                $yearmonth = date("Y") . $monthPadding;
                $column = "msales" . ($temp - 1);

                $temp--;

                $pac['pac1'] = $this
                    ->cronjob_model
                    ->pac1($user->userid, $yearmonth, $repclause, $column);
                $pac['pac2'] = $this
                    ->cronjob_model
                    ->pac2($user->userid, $yearmonth, $repclause, $column);
                $pac['pac3'] = $this
                    ->cronjob_model
                    ->pac3($user->userid, $yearmonth, $repclause, $column);
                $pac['pac4'] = $this
                    ->cronjob_model
                    ->pac4($user->userid, $yearmonth, $repclause, $column);
                //print_r($pac);
                $table .= "<tr ><th colspan='5' style='background:#00a65a; border: 1px solid #ddd;padding: 8px; color:#fff; '><center>" . $fdate . "</center></th><tr>";

                foreach ($pac as $pc)
                {

                    if (empty($pc))
                    {
                        if ($i != $currentmonth - 1)
                        {

                            $table .= "<tr>
              
              <td style=' border: 1px solid #ddd;padding: 8px;' colspan='5'><center>No Record Found</center></td></tr>";
                            break;
                        }
                    }
                    else
                    {
                        foreach ($pc as $p)
                        {





$progress=round($p->salesmtd * 100 / $p->salestarget, 2);
                    
                    if($progress >=0 && $progress<30)
                    {
                         $background="none";
                         $color="#000";
                    }
                    elseif($progress >=31 && $progress<70)
                    {
                         $background="none";
                         $color="#000";
                    }
                    elseif($progress>70)
                    {
                        $background="none";
                         $color="#000";
                    }
                    else
                    {
                        $background="none";
                         $color="#000";
                    }










                            $table .= "<tr>
              
              <td style=' border: 1px solid #ddd;padding: 8px;'>" . $p->code . "</td>
              
             <td style=' border: 1px solid #ddd;padding: 8px;'>";

                            if ($p->description != "")
                            {
                                $table .= $p->description;
                            }
                            else
                            {
                                $table .= '0';
                            }
                            $table .= "</td><td style=' border: 1px solid #ddd;padding: 8px;'>";
                            if ($p->salesmtd <> 0)
                            {
                                $table .= $p->salesmtd;
                            }
                            else
                            {
                                $table .= '0';
                            }
                            $table .= "</td><td style=' border: 1px solid #ddd;padding: 8px;'>";
                            if ($p->salestarget <> 0)
                            {
                                $table .= $p->salestarget;
                            }
                            else
                            {
                                $table .= '0';
                            }

                            $table .= "</td><td style=' border: 1px solid #ddd;padding: 8px; color:".$color.";background:".$background.";'><center><b>" .$progress. "%</b></center></td></tr>";

                        }

                    }
                }
            }

            $table .= '</table>';

            $htmlContent = '<html  style="font-family: Trebuchet MS, Arial, Helvetica, sans-serif;"><head><title>Sales Report</title>
</head>';

            $this
                ->load
                ->library('email');

            $config = array(
                'protocol' => 'smtp',
                'smtp_host' => 'smtp.123-reg.co.uk',
                'smtp_port' => 465,
                'smtp_user' => 'system@mi-das.co.uk',
                'smtp_pass' => '$s\+\>/Spu@3fnT,',
                'mailtype' => 'html',
                'charset' => 'utf-8'
            );
            $config['protocol'] = 'sendmail';
            $config['mailpath'] = '/usr/sbin/sendmail';
            $config['charset'] = 'iso-8859-1';
            $config['wordwrap'] = true;

            $this
                ->email
                ->initialize($config);
            $this
                ->email
                ->set_mailtype("html");
            $this
                ->email
                ->set_newline("\r\n");
            $htmlContent .= '<h2>Hi ' . $user->firstname . '' . $user->surname . '</h2>';
            $htmlContent .= '<h4>Your Sales Report Of Year ' . date("Y") . ' is below: </h4>';

            $htmlContent .= $table;

            $htmlContent .= '</html>';
echo $htmlContent; exit;
            $this
                ->email
                ->clear();
            $this
                ->email
                ->to('kieran@kk-cs.co.uk');
            //$this->email->to($user->email);
            $this
                ->email
                ->from('system@mi-das.co.uk', 'mi-das');
            $this
                ->email
                ->subject('Sales Report : Yearly');
            $this
                ->email
                ->message($htmlContent);
            if ($this
                ->email
                ->send())
            {
                echo 'sent<br>';
            }
            else
            {
                echo 'fail<br>';
            }

        }
        break;
        //print_r($users);
        
    }

}
?>
