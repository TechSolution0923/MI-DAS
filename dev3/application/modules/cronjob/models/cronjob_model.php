<?php

class Cronjob_model extends Model {
    
    public function all_salesemail_users()
    {
         return $this->db->query("select userid,email,firstname,surname from users where salesemail='1' ")->result();
    }
    
    
    public function pac1($userid,$yearmonth,$repclause,$column)
    {

            $result= $this->db->query("select pac1.*,sum(pac1salestarget.salestarget) as salestarget from pac1 left join pac1salestarget on pac1.code=pac1salestarget.pac1code  where pac1salestarget.userid='$userid' and pac1salestarget.yearmonth='$yearmonth' GROUP BY pac1.code  ")->result();
               // $result2= $this->db->query("select sum(pac1sales.msales0) as salesmtd from pac1sales group by pac1code")->result();



                    $sql="select sum(pac1sales.".$column.") as salesmtd from pac1sales  ";
                    $repclause=str_replace("IN ('", "", $repclause);
                    $repclause=str_replace("')", "", $repclause);
                    $repclause=str_replace("','", "|", $repclause);
                    $rep=explode('|',$repclause);
                    $i=0;
                    foreach($rep as $repclause)
                    {
                    if($i==0)
                    {
                    $sql.=" where repcode=".$repclause." ";    $i++;
                    }
                    else{
                    $sql.=" or  repcode=".$repclause." "; 
                    }
                    }

                    $sql.="group by pac1code";

                    $result2= $this->db->query($sql)->result();
                    for($i=0;$i<count($result); $i++)
                    {
                    $result[$i]->salesmtd=$result2[$i]->salesmtd;
                    }
                    return $result;


    }
     public function pac2($userid,$yearmonth,$repclause,$column)
    {
         $result= $this->db->query("select pac2.*,sum(pac2salestarget.salestarget) as salestarget from pac2 left join pac2salestarget on pac2.code=pac2salestarget.pac2code  where pac2salestarget.userid='$userid' and pac2salestarget.yearmonth='$yearmonth' GROUP BY pac2.code   ")->result();
                //$result2= $this->db->query("select sum(pac2sales.msales0) as salesmtd from pac2sales group by pac2code ")->result();
                
                
                $sql="select sum(pac2sales.".$column.") as salesmtd from pac2sales  ";
                    $repclause=str_replace("IN ('", "", $repclause);
                    $repclause=str_replace("')", "", $repclause);
                    $repclause=str_replace("','", "|", $repclause);
                    $rep=explode('|',$repclause);
                    $i=0;
                    foreach($rep as $repclause)
                    {
                    if($i==0)
                    {
                    $sql.=" where repcode=".$repclause." ";    $i++;
                    }
                    else{
                    $sql.=" or  repcode=".$repclause." "; 
                    }
                    }

                    $sql.="group by pac2code";
//echo $sql;
                    $result2= $this->db->query($sql)->result();
                    for($i=0;$i<count($result); $i++)
                    {
                    $result[$i]->salesmtd=$result2[$i]->salesmtd;
                    }

                    return $result;


    }
     public function pac3($userid,$yearmonth,$repclause,$column)
    {
           
                 $result= $this->db->query("select pac3.*,sum(pac3salestarget.salestarget) as salestarget from pac3 left join pac3salestarget on pac3.code=pac3salestarget.pac3code  where pac3salestarget.userid='$userid' and pac3salestarget.yearmonth='$yearmonth'  GROUP BY pac3.code  ")->result();
                //$result2= $this->db->query("select sum(pac3sales.msales0) as salesmtd from pac3sales group by pac3code")->result();
                
                $sql="select sum(pac3sales.".$column.") as salesmtd from pac3sales  ";
                    $repclause=str_replace("IN ('", "", $repclause);
                    $repclause=str_replace("')", "", $repclause);
                    $repclause=str_replace("','", "|", $repclause);
                    $rep=explode('|',$repclause);
                    $i=0;
                    foreach($rep as $repclause)
                    {
                    if($i==0)
                    {
                    $sql.=" where repcode=".$repclause." ";    $i++;
                    }
                    else{
                    $sql.=" or  repcode=".$repclause." "; 
                    }
                    }

                    $sql.="group by pac3code";

                    $result2= $this->db->query($sql)->result();
                    for($i=0;$i<count($result); $i++)
                    {
                    $result[$i]->salesmtd=$result2[$i]->salesmtd;
                    }


              return $result;

    }
     public function pac4($userid,$yearmonth,$repclause,$column)
    {
          $result= $this->db->query("select pac4.*,sum(pac4salestarget.salestarget) as salestarget from pac4 left join pac4salestarget on pac4.code=pac4salestarget.pac4code  where pac4salestarget.userid='$userid' and pac4salestarget.yearmonth='$yearmonth' GROUP BY pac4.code  ")->result();
                //$result2= $this->db->query("select sum(pac4sales.msales0) as salesmtd from pac4sales group by pac4code")->result();
                
               $sql="select sum(pac4sales.".$column.") as salesmtd from pac4sales  ";
                    $repclause=str_replace("IN ('", "", $repclause);
                    $repclause=str_replace("')", "", $repclause);
                    $repclause=str_replace("','", "|", $repclause);
                    $rep=explode('|',$repclause);
                    $i=0;
                    foreach($rep as $repclause)
                    {
                    if($i==0)
                    {
                    $sql.=" where repcode=".$repclause." ";    $i++;
                    }
                    else{
                    $sql.=" or  repcode=".$repclause." "; 
                    }
                    }

                    $sql.="group by pac4code";

                    $result2= $this->db->query($sql)->result();
                    for($i=0;$i<count($result); $i++)
                    {
                    $result[$i]->salesmtd=$result2[$i]->salesmtd;
                    }
                    return $result;
    }
    
    
    
   public function getUserDetails($userid) {    
        // $this->db->select('*');
        // $this->db->from('users');
        // $this->db->where('userid', $userid);
        // $query = $this->db->get();
        // return $query->row_array();      
$this->db->select('*');
        $this->db->from('users');
        $this->db->where('userid ', $userid);
        $query = $this->db->get();
        $str = $query->row_array();
        //print_r($str);
        $repwhere = $str['repcode'];    
        $repwhere = $repwhere . (EMPTY($str['repcode_2']) ? "" :  ",".$str['repcode_2']) . (EMPTY($str['repcode_3']) ? "" :  ",".$str['repcode_3']) . (EMPTY($str['repcode_4']) ? "" :  ",".$str['repcode_4']) . (EMPTY($str['repcode_5']) ? "" :  ",".$str['repcode_5']) . (EMPTY($str['repcode_6']) ? "" :  ",".$str['repcode_6']) . (EMPTY($str['repcode_7']) ? "" :  ",".$str['repcode_7']) . (EMPTY($str['repcode_8']) ? "" :  ",".$str['repcode_8']) . (EMPTY($str['repcode_9']) ? "" :  ",".$str['repcode_9']) . (EMPTY($str['repcode_10']) ? "" :  ",".$str['repcode_10']);
        $str['repwhere'] = $repwhere."";

        $str['name'] = trim($str['firstname']) . " " . $str['surname'];


            $repclause = "IN ('$str[repcode]'"; 
    $repclause = $repclause . (EMPTY($str['repcode_2']) ? "" :  ",'$str[repcode_2]'") . (EMPTY($str['repcode_3']) ? "" :  ",'$str[repcode_3]'") . (EMPTY($str['repcode_4']) ? "" :  ",'$str[repcode_4]'") . (EMPTY($str['repcode_5']) ? "" :  ",'$str[repcode_5]'") . (EMPTY($str['repcode_6']) ? "" :  ",'$str[repcode_6]'") . (EMPTY($str['repcode_7']) ? "" :  ",'$str[repcode_7]'") . (EMPTY($str['repcode_8']) ? "" :  ",'$str[repcode_8]'") . (EMPTY($str['repcode_9']) ? "" :  ",'$str[repcode_9]'") . (EMPTY($str['repcode_10']) ? "" :  ",'$str[repcode_10]'");
    $repclause = $repclause.")";
$str['repclause']=$repclause;
//echo $repclause; die;
//echo $this->db->last_query();
        return $str;




    }






















// public function pac1($userid,$yearmonth,$repclause,$column)
//     {

//             $sql="select sum(pac1sales.".$column.") as salesmtd from pac1sales  ";
//             $repclause=str_replace("IN ('", "", $repclause);
//             $repclause=str_replace("')", "", $repclause);
//             $repclause=str_replace("','", "|", $repclause);
//             $rep=explode('|',$repclause);
//             $i=0;
//             foreach($rep as $repclause)
//             {
//             if($i==0)
//             {
//             $sql.=" where repcode=".$repclause." ";    $i++;
//             }
//             else{
//             $sql.=" or  repcode=".$repclause." "; 
//             }
//             }

//          //   echo $sql;



// $salesmtd= $this->db->query($sql)->row()->salesmtd;
//      $salestarget=$this->db->query("select sum(salestarget) as salestarget from pac1salestarget where userid='$userid' and yearmonth='$yearmonth' ")->row()->salestarget;
     
//      return array('salesmtd'=>$salesmtd,'salestarget'=>$salestarget);


//     }
//      public function pac2($userid,$yearmonth,$repclause,$column)
//     {
//          $sql="select sum(pac2sales.".$column.") as salesmtd from pac2sales  ";
//             $repclause=str_replace("IN ('", "", $repclause);
//             $repclause=str_replace("')", "", $repclause);
//             $repclause=str_replace("','", "|", $repclause);
//             $rep=explode('|',$repclause);
//             $i=0;
//             foreach($rep as $repclause)
//             {
//             if($i==0)
//             {
//             $sql.=" where repcode=".$repclause." ";    $i++;
//             }
//             else{
//             $sql.=" or  repcode=".$repclause." "; 
//             }
//             }
//  //echo $sql;
//          $salesmtd= $this->db->query($sql)->row()->salesmtd;
     
//      $salestarget=$this->db->query("select sum(salestarget) as salestarget from pac2salestarget where userid='$userid' and yearmonth='$yearmonth' ")->row()->salestarget;
     
//      return array('salesmtd'=>$salesmtd,'salestarget'=>$salestarget);


//     }
//      public function pac3($userid,$yearmonth,$repclause,$column)
//     {
//          $sql="select sum(pac3sales.".$column.") as salesmtd from pac3sales  ";
//             $repclause=str_replace("IN ('", "", $repclause);
//             $repclause=str_replace("')", "", $repclause);
//             $repclause=str_replace("','", "|", $repclause);
//             $rep=explode('|',$repclause);
//             $i=0;
//             foreach($rep as $repclause)
//             {
//             if($i==0)
//             {
//             $sql.=" where repcode=".$repclause." ";    $i++;
//             }
//             else{
//             $sql.=" or  repcode=".$repclause." "; 
//             }
//             }
//       $salesmtd= $this->db->query($sql)->row()->salesmtd;
    
//      $salestarget=$this->db->query("select sum(salestarget) as salestarget from pac3salestarget where userid='$userid' and yearmonth='$yearmonth' ")->row()->salestarget;
     
//      return array('salesmtd'=>$salesmtd,'salestarget'=>$salestarget);
//     }
//      public function pac4($userid,$yearmonth,$repclause,$column)
//     {
//          $sql="select sum(pac4sales.".$column.") as salesmtd from pac4sales  ";
//             $repclause=str_replace("IN ('", "", $repclause);
//             $repclause=str_replace("')", "", $repclause);
//             $repclause=str_replace("','", "|", $repclause);
//             $rep=explode('|',$repclause);
//             $i=0;
//             foreach($rep as $repclause)
//             {
//             if($i==0)
//             {
//             $sql.=" where repcode=".$repclause." ";    $i++;
//             }
//             else{
//             $sql.=" or  repcode=".$repclause." "; 
//             }
//             }
            
//         $salesmtd= $this->db->query($sql)->row()->salesmtd;
     
//      $salestarget=$this->db->query("select sum(salestarget) as salestarget from pac4salestarget where userid='$userid' and yearmonth='$yearmonth' ")->row()->salestarget;
     
//      return array('salesmtd'=>$salesmtd,'salestarget'=>$salestarget);
//     }
    
    





















    
}


?>