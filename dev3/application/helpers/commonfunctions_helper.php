<?php
	if (!defined('BASEPATH')) exit('No direct script access allowed');

	/* * *****************************************************************************
	 * Author	: VirtualEmployee Pvt. Ltd.
	 * Create Date	: 05/04/2016
	 * Update Date	: -
	 * Descrption	: The common functions
	 * ****************************************************************************** */

	/* This function is used to find if the user is admin or not. Returns false if not admin. */

	function isAdmin($userid=0) {
		$CI =& get_instance();
		$img = site_url('application/modules/users/images/dummy.png');
		if (0==intval($userid)) {
			$userid = $CI->session->userdata('userid'); /* Logged in user id */
		}
		$CI->load->model('users/users_model');
		$userDetail = $CI->users_model->getUserDetails($userid);
		return 1==intval($userDetail['administrator']);
	}


function GetKpiDataForTwoYearVsTargetChart($userKpi, $data, $G_level)
{
    if ($G_level == "Company")
    {
        $data["CompanyKPIThreshold1"] = $userKpi[0]["kpithreshold1"];
        $data["CompanyKPIThreshold2"] = $userKpi[0]["kpithreshold2"];
        $data["CompanyMarginOk"] = $userKpi[0]["marginok"];
        $data["CompanyMarginGood"] = $userKpi[0]["margingood"];
    }

    if ($G_level == "Branch")
    {
        $data["BranchKPIThreshold1"] = $userKpi[0]["kpithreshold1"];
        $data["BranchKPIThreshold2"] = $userKpi[0]["kpithreshold2"];
        $data["BranchMarginOk"] = $userKpi[0]["marginok"];
        $data["BranchMarginGood"] = $userKpi[0]["margingood"];
    }

    if ($G_level == "User")
    {
        $data["UserKPIThreshold1"] = $userKpi[0]["kpithreshold1"];
        $data["UserKPIThreshold2"] = $userKpi[0]["kpithreshold2"];
        $data["UserMarginOk"] = $userKpi[0]["marginok"];
        $data["UserMarginGood"] = $userKpi[0]["margingood"];
    }

    switch ($G_level)
    {
        case"Company":
            $data["G_kpithreshold1"] = $data["CompanyKPIThreshold1"];
            $data["G_kpithreshold2"] = $data["CompanyKPIThreshold2"];
            $data["G_MarginOk"] = $data["CompanyMarginOk"];
            $data["G_MarginGood"] = $data["CompanyMarginGood"];
            break;
        case"User":
            // If the user thresholds arent set, use the company ones, otherwise use the user ones
            if ($data["UserKPIThreshold1"] == 0 and $data["UserKPIThreshold2"] == 0)
            {
                $data["G_kpithreshold1"] = $data["CompanyKPIThreshold1"];
                $data["G_kpithreshold2"] = $data["CompanyKPIThreshold2"];
            }
            else
            {
                $data["G_kpithreshold1"] = $data["UserKPIThreshold1"];
                $data["G_kpithreshold2"] = $data["UserKPIThreshold2"];
            }
            if ($data["UserMarginOk"] == 0 and $data["UserMarginGood"] == 0)
            {
                $data["G_MarginOk"] = $data["CompanyMarginOk"];
                $data["G_MarginGood"] = $data["CompanyMarginGood"];
            }
            else
            {
                $data["G_MarginOk"] = $data["UserMarginOk"];
                $data["G_MarginGood"] = $data["UserMarginGood"];
            }
            break;
        case"Branch":
            // If the branch thresholds arent set, use the system ones, otherwise use the user ones
            if ($data["BranchKPIThreshold1"] != 0 and $data["BranchKPIThreshold2"] != 0)
            {
                $data["G_kpithreshold1"] = $data["BranchKPIThreshold1"];
                $data["G_kpithreshold2"] = $data["BranchKPIThreshold2"];
            }
            else
            {
                $data["G_kpithreshold1"] = $data["CompanyKPIThreshold1"];
                $data["G_kpithreshold2"] = $data["CompanyKPIThreshold2"];
            }
            if ($data["BranchMarginOk"] == 0 and $data["BranchMarginGood"] == 0)
            {
                $data["G_MarginOk"] = $data["CompanyMarginOk"];
                $data["G_MarginGood"] = $data["CompanyMarginGood"];
            }
            else
            {
                $data["G_MarginOk"] = $data["BranchMarginOk"];
                $data["G_MarginGood"] = $data["BranchMarginGood"];
            }
            break;
    }

    return $data;
}

	/* This function will return true if the logged in (if userid is 0, otherwise the user with the specified id) user is able to see margin and will return false otherwise. */

	/*function canSeeMargins($userid = 0)
	{
		$userDetail = getUserDetails($userid);
		$seemargins = $userDetail['seemargins'];

		return 1 == intval($seemargins);
	}

	function canSeeOMR($userid=0) {
		$userDetail = getUserDetails($userid);
		$seeomr = $userDetail['seeomr'];
		return 1==intval($seeomr);
	}*/

    function canSeeMarginsAndOMR($userid = 0) {
        $userDetail = getUserDetails($userid);
        $data = array();
        $data['seemargins'] = (1 == $userDetail['seemargins']);
        $data['seeomr'] = (1 == $userDetail['seeomr']);
        return $data;
    }

    function canSeeThreeInfo($userid = 0) {
        $userDetail = getUserDetails($userid);
        $data = array();
        $data['seeprojectedsales'] = (1 == intval($userDetail['seeprojectedsales']));
        $data['seeprojectedsalesyear'] = (1 == intval($userDetail['seeprojectedsalesyear']));
        $data['seeorderfulfillment'] = (1 == intval($userDetail['seeorderfulfillment']));
        return $data;
    }

	/* This function will return true if the logged in (if userid is 0, otherwise the user with the specified id) user is able to edit notes and will return false otherwise. */

	function canEditNotes($userid=0) {
		$userDetail = getUserDetails($userid);
		$editnotes = $userDetail['editnotes'];
		return 1==intval($editnotes);
	}

	/* This function will return true if the logged in (if userid is 0, otherwise the user with the specified id) user is able to edit the terms and will return false otherwise. */

	function canEditTerms($userid=0) {
		$userDetail = getUserDetails($userid);
		$editterms = $userDetail['editterms'];
		return 1==intval($editterms);
	}

	/* Function to get the user details */
	function getUserDetails($userid)
	{
		$CI =& get_instance();
		$CI->load->model('users/users_model');

		if (0 == intval($userid))
		{
			$userid = $CI->session->userdata('userid'); /* Logged in user id */
		}

		$userDetail = $CI->users_model->getUserDetails($userid);

		return $userDetail;
	}

	/* Function to fetch all the branches in the system */
	/*function getAllBranches() {
		$CI =& get_instance();
		$CI->load->model('sites/site_model');
		return $CI->site_model->getAllBranches();
	}*/

	/* A helper function to output the Customer details >> PAC reports. */
	/*
	function displayPAC($r_pac, $canSeeMargins, $data_level='1', $account) {
		foreach($r_pac as $row) {
			$qtymtd = 0;
			$salesmtd = 0;
			$costmtd = 0;
			extract($row);

			if (!isset($qtymtd)) {
				$qtymtd = 0;
			}

			if (!isset($salesmtd)) {
				$salesmtd = 0;
			}

			if (!isset($costmtd)) {
				$costmtd = 0;
			}

			$marginmtdpc = 0;
			$marginytdpc = 0;

			if (0!=$salesmtd) {
				$marginmtdpc = ($marginmtd * 100) / $salesmtd ;
				$marginmtdpc = number_format($marginmtdpc);
			}

			if (0!=$salesytd) {
				$marginytdpc = ($marginytd * 100) / $salesytd;
				$marginytdpc = number_format($marginytdpc);
			}

		?>
		  <tr>
			<td><?= $code;?></td>
			<td><a href='#GraphModal' data-toggle='modal' data-target='#GraphModal' data-account='<?= $account;?>' data-level='pac<?= $data_level;?>' data-code='<?= $code;?>' data-description='<?= $description;?>'>
			<?= $description;?></a></td>
			<td><?= $qtymtd;?></td>
			<td><?= $salesmtd;?></td>
			<?php if ($canSeeMargins) { ?>
				<td><?= $marginmtdpc;?></td>
			<?php }?>
			<td><?= $qtyytd;?></td>
			<td><?= $salesytd;?></td>
			<?php if ($canSeeMargins) { ?>
				<td><?= $marginytdpc;?></td>
			<?php } ?>
			</tr>
		<?php }
	}
	*/

	/* A helper function to output the Customer details >> PAC reports. */
	function displayPAC($r_pac, $canSeeMargins, $data_level = '1', $account)
	{
		$raccount = $account;
		$totals = array();

		foreach ($r_pac as $row)
		{
			$code = $row['code'];
			$account = $row['account'];
			$description = $row['description'];
			$qtymtd = $row['qtymtd'];
			$salesmtd = $row['salesmtd'];
			$marginmtdpc = $row['marginmtdpc'];
			$qtyytd = $row['qtyytd'];
			$salesytd = $row['salesytd'];
			$marginytdpc = $row['marginytdpc'];
			$YoY1Sales = $row['YoY1Sales'];
			$YoY1ProRataAdjustment = $row['YoY1ProRataAdjustment'];
			$YoY1Qty = $row['YoY1Qty'];
			$YoY2Sales = $row['YoY2Sales'];
			$YoY2Qty = $row['YoY2Qty'];

			$totals['salesmtd']+= $salesmtd;
			$totals['qtymtd']+= $qtymtd;
			$totals['costsmtd']+= $row['costsmtd'];
			$totals['salesytd']+= $salesytd;
			$totals['qtyytd']+= $qtyytd;
			$totals['costsytd']+= $row['costsytd'];
			$totals['YoY1Sales']+= $YoY1Sales;
			$totals['YoY1ProRataAdjustment']+= $YoY1ProRataAdjustment;
			$totals['YoY2Sales']+= $YoY2Sales;
			$totals['YoY1Qty']+= $YoY1Qty;
			$totals['YoY2Qty']+= $YoY2Qty;

			$salesDifferencePercentage = 0;
			$class = "";

			if ($YoY1Sales == 0)
			{
				if ($salesytd == 0)
				{
					$salesDifferencePercentage = "0.00";
					$class = "";
				}
				elseif ($salesytd < 0)
				{
					$salesDifferencePercentage = "-100.00";
					$class = "redrow";
				}
				else
				{
					$salesDifferencePercentage = "100.00";
					$class = "greenrow";
				}
			}
			else
			{
				$salesDifferencePercentage = getDiffPercentage($salesytd, $YoY1Sales + $YoY1ProRataAdjustment);
				$class = "";

				if ($salesDifferencePercentage < 0)
				{
					$class = "redrow";
				}
				else
				{
					$class = "greenrow";
				}
			}

			$html =
			'<tr class="'.$class.'">
				<td>'.$code.'</td>
				<td><a class="iframe cboxElement" href="'.base_url().'customer/customergraph/account/'.$raccount.'/level/pac'.$data_level.'/code/'.$code.'">'.$description.'</a></td>
				<td>'.$salesytd.'</td>
				<td>'.$qtyytd.'</td>
				<td>'.number_format($salesDifferencePercentage, 2).'</td>
				<td>'.getDiffPercentageFormatted($qtyytd, $YoY1Qty).'</td>
				<td>'.number_format($YoY1Sales + $YoY1ProRataAdjustment, 2).'</td>
				<td>'.$YoY1Qty.'</td>
				<td>'.$YoY2Sales.'</td>
				<td>'.$YoY2Qty.'</td>';

				if ($canSeeMargins)
				{
					$html.=
					'<td>'.number_format($marginmtdpc, 2).'</td>
					<td>'.number_format($marginytdpc, 2).'</td>';
				}

				$html.=
				'<td style="display: none;">'.$row['costsmtd'].'</td>
				<td style="display: none;">'.$row['costsytd'].'</td>
				<td style="display: none;">'.$row['salesmtd'].'</td>
			</tr>';

			echo $html;
		}

		return $totals;
	}

	function getDiffPercentage($first, $second)
	{
		if ($first > 0.01 && $second < 0.01)
		{
			return floatval(100.00);
		}

		if ($first < 0.01 && $second < 0.01)
		{
			return floatval(0.00);
		}

		$percentage = ($first - $second) / $second * 100;

		return $percentage;
	}

	function getDiffPercentageFormatted($first, $second)
	{
		$percentage = getDiffPercentage($first, $second);

		return number_format($percentage, 2);
	}

	function getDiffMargin($first, $second)
	{
		$percentage = ($first - $second) / $first * 100;

		return number_format($percentage, 2);
	}

	/*
	 * php delete function that deals with directories recursively
	 */
	function delete_files($target) {
		if (is_dir($target)){
			$files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

			foreach( $files as $file ){
				delete_files( $file );
			}

			return rmdir( $target ) >=0 ;

			/* Upon successful completion, the function rmdir() shall return 0. Otherwise, -1 shall be returned, and errno set to indicate the error. If -1 is returned, the named directory shall not be changed. */
		} elseif (is_file($target)) {
			return !!unlink( $target );

			/**The unlink() function returns Boolean False but many times it happens that it returns a non-Boolean value which evaluates to False. */
		}
	}
