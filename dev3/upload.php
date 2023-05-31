<?php
session_start();

$message = ''; 
if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Upload')
{
  if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK)
  {
    // get details of the uploaded file
    $fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
    $fileName = $_FILES['uploadedFile']['name'];
    $fileSize = $_FILES['uploadedFile']['size'];
    $fileType = $_FILES['uploadedFile']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // sanitize file-name
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

    // check if file has one of the following extensions
    $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc','csv');

    if (in_array($fileExtension, $allowedfileExtensions))
    {
      // directory in which the uploaded file will be moved
      $uploadFileDir = './uploaded_files/';
//      $dest_path = $uploadFileDir . $newFileName;
      $dest_path = $uploadFileDir . $fileName;

      if(move_uploaded_file($fileTmpPath, $dest_path)) 
      {
        $message ='File is successfully uploaded.';
      }
      else 
      {
        $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
      }
    }
    else
    {
      $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
    }
  }
  else
  {
    $message = 'There is some error in the file upload. Please check the following error.<br>';
    $message .= 'Error:' . $_FILES['uploadedFile']['error'];
  }
}

	date_default_timezone_set('Europe/London');

	require_once 'dblogin.php';

	ini_set('log_errors',1);
	ini_set('error_log', 'error_log');
	
	error_reporting(E_ALL);	

	// open connection 
	$link = mysqli_connect($host, $user, $pass, $db) or logerror($query,mysqli_error($link));


	if (($handle = @fopen($dest_path, "r")) !== FALSE) 
	{
		$rowno = 0;
		
		while (($data = fgetcsv($handle, 750, ",")) !== FALSE)	
		{
			if ($rowno == 0)
			{
				// Get the yearmonths from the header and put them into an array so they are easier to access in the loop below
				$yearmonth = array($data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11], $data[12], $data[13]);
			}
			else
			{
				// Go through the year month columns and insert/update the sales targets
				
				$account = $data[0];
				$pac2code = $data[1];
				
				$y = 0;
				
				for($x = 2; $x <= 13; $x++)
				{
					// Insert/update the customer pac2 sales targets
				
					$salestarget = $data[$x];
					
					$query = "INSERT INTO customerpac2salestarget(id,account,pac2code,yearmonth,salestarget) VALUES(0,'$account','$pac2code',$yearmonth[$y],$salestarget) ON DUPLICATE KEY UPDATE salestarget = $salestarget";
					
					$result = mysqli_query($link, $query) or logerror($query,mysqli_error($link));
					
					$y++;
				}
			}
			$rowno++;
		}
	}
	
	$_SESSION['message'] = $message;
	header("Location: fileopentest.php");

	function logerror($query,$error)
	{
		$email = "kieran" . "@" . "kk-cs" . ".co.uk";
		
		$errormsg = "Hi Kieran, an error just occurred in ".__FILE__.". The query was '".$query."' and the error was '".$error."'<BR>";
		
		error_log($errormsg);
		error_log($errormsg,1,$email);
		
		return true;
	}	
?>