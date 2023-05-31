<!-- This file opens the FTP folder and extracts the files into the folder ready for the data to be loaded

<?php
	date_default_timezone_set('Europe/London');

	ini_set('log_errors',0);
	ini_set('display_errors',1);
	
	// ini_set('error_log', 'error_log');
	
	error_reporting(E_ALL);	
	ini_set('display_startup_errors', 1); 

	set_include_path('./phpseclib');
	include('Net/SFTP.php');
	
	require_once 'ftplogin.php';

	$sftp = new Net_SFTP($ftpserver);
    
	if (!$sftp->login($ftpuser, $ftppass)) {
        exit('Acceso incorrecto..');
    }
	
	$remote_folder = $sftp->pwd()."Outgoing";
	$local_folder = getcwd();
	
	$GOFile = $remote_folder."/GO.csv";
	
	if ($sftp->file_exists($GOFile)) // Only retrieve the files if there is a GO File - This is so that we're not retrieving partially complete files
	{
		$files = $sftp->nlist($remote_folder); // Get the list of files in the folder

		foreach ($files as $file)
		{
			//echo "File $file\r\n";
			if (substr($file,0,6) === "MI-DAS") // We're only interested in the files beginning with MI-DAS
			{
				$remote_file = $remote_folder."/".$file;
				$local_file = $local_folder."/".$file;
				
				$sftp->get($remote_file,$local_file); // Bring the file back
				$sftp->delete($remote_file,false); // Delete the remote file
			}
		}
		$sftp->delete($GOFile,false); // Delete the GO file
	}

?>
