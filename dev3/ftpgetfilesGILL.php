<!-- This file opens the FTP folder and extracts the files into the folder ready for the data to be loaded. The files are only retrieved if there is a GO file i.e. K8 has finished exporting reports

<?php
	date_default_timezone_set('Europe/London');

	ini_set('log_errors',0);
	ini_set('display_errors',1);
	
	error_reporting(E_ALL);	
	ini_set('display_startup_errors', 1); 

	$remote_folder = "/home/midasco/public_ftp/incoming/GILL";
	$local_folder = getcwd();
	$RemoteGOFile = $remote_folder."/MI-DAS_GO02.csv";
	$LocalGOFile = $local_folder."/MI-DAS_GO02.csv";
	
	if (file_exists($RemoteGOFile)) // Only retrieve the files if there is a GO File - This is so that we're not retrieving partially complete files
	{
		foreach (scandir($remote_folder) as $file)
		{
			if (substr($file,0,6) === "MI-DAS") // We're only interested in the files beginning with MI-DAS
			{
				//echo "File $file\r\n";

				$remote_file = $remote_folder."/".$file;
				$local_file = $local_folder."/".$file;
					
				rename($remote_file,$local_file); // Move (rename) the file
			}
		}
		unlink($LocalGOFile); // Delete the GO file
	}
?>
