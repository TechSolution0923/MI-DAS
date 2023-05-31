<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

/* * *****************************************************************************
 * Author	: VirtualEmployee Pvt. Ltd.
 * Create Date	: 05/04/2016
 * Update Date	: -
 * Descrption	: Display Profile image helper 
 * ****************************************************************************** */

/* This function is used to generate the profile image URL */

function generate_profile_image_url($userid=0, $addnew=false) {
	
	$CI =& get_instance();
	
	$img = site_url('application/modules/users/images/dummy.png');
	if($addnew) {
		return $img;	
	}
	
	if(0==intval($userid)) {
		$userid = $CI->session->userdata('userid'); /* Logged in user id */
	}
	
	$parent_directory = APPPATH . 'modules' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;	
	$sub_directory = $parent_directory. 'profile_images' . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR;
	$upload_path = $sub_directory . $userid;
	if(pathExists($upload_path)) {
		$filenames = array_filter(scandir($upload_path), function($item) {
			return !is_dir($upload_path . $item);
		});
		sort($filenames);
		$image = $filenames[0];
		
		if(!file_exists(APPPATH . 'modules' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'profile_images' . DIRECTORY_SEPARATOR . 'original' . DIRECTORY_SEPARATOR . $userid . DIRECTORY_SEPARATOR . $image) || ""==$image) {
			$img = site_url('application/modules/users/images/dummy.png');
		} else {
			$img = site_url('application/modules/users/images/profile_images/original/'.$userid.'/'.$image)."?".time();
		}
	}	
	return $img;	
}

/* Function to check if the path exists or not */

function pathExists($path) {
	return file_exists ($path);
}


/* This function is used to generate the temporary profile image URL */
function generate_temp_image_url($tempFileName) {	
	$parent_directory = APPPATH . 'modules' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;	
	$upload_path = $parent_directory. 'temp';
	if(pathExists($upload_path)) {
		$image = $tempFileName;
		
		if(!file_exists(APPPATH . 'modules' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $image) || ""==$image) {
			$img = site_url('application/modules/users/images/dummy.png');
		} else {
			$img = site_url('application/modules/users/images/temp/'.$image)."?".time();
		}
	}	
	return $img;	
}
?>