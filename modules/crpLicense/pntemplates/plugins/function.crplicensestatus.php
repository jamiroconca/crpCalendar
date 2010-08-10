<?php

/**
 * crpLicense
 *
 * @copyright (c) 2009, Daniele Conca
 * @link http://code.zikula.org/crplicense Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpLicense
 */

/**
 * Smarty function to display status of a video
 *
 * Example
 * <!--[crplicensestatus status="$status_flag" ]-->
 *
 * @param array $params All attributes passed to this function from the template
 * @param object &$smarty Reference to the Smarty object
 * @param int $status status flag
 * @param int id item_identifier
 *
 * @return string the results of the module function
 */
function smarty_function_crplicensestatus($params, & $smarty)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_EDIT))
	{
		return LogUtil :: registerPermissionError();
	}

	$statusimage= '';

	if (!$params['fake'])
	{
		if (SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_ADD) && ($params['status'] == 'A' || $params['status'] == 'P'))
			$statusimage .= '<a href="' . pnModUrl('crpLicense', 'admin', 'change_status', array (
				'id' => $params['id'],
				'obj_status' => $params['status']
			)) . '" title="' . _CRPLICENSE_CHANGE_STATUS . '">' . "\n";
		else
			$statusimage .= '';

		if ($params['status'] == 'A')
			$statusimage .= '<img id="licensestatus_' . $params['id'] . '" src="modules/crpLicense/pnimages/green_dot.gif" alt="' . _ACTIVE . '" title="' . _CRPLICENSE_CHANGE_STATUS . '"/>' . "\n</a>\n";
		elseif ($params['status'] == 'P') $statusimage .= '<img id="licensestatus_' . $params['id'] . '" src="modules/crpLicense/pnimages/yellow_dot.gif" alt="' . _CRPLICENSE_PENDING . '" title="' . _CRPLICENSE_CHANGE_STATUS . '"/>' . "\n</a>\n";
		else
			$statusimage .= '<img id="licensestatus_' . $params['id'] . '" src="modules/crpLicense/pnimages/red_dot.gif" alt="' . _CRPLICENSE_REJECTED . '" title="' . _CRPLICENSE_CHANGE_STATUS_MODIFYING . '" />' . "\n";
	}
	else
	{
		if (SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_ADD) && ($params['status'] == 'A' || $params['status'] == 'P'))
		{
			$statusimage .= "<a href='javascript:void(0);'>";
			$statusimage .= '<img id="licensestatus_fake_A_' . $params['id'] . '" ';
			$statusimage .= ($params['status'] == 'P') ? ' style="display:none" ' : '';
			$statusimage .= '" onclick="togglestatus(\'' . $params['id'] . '\',\'A\')" src="modules/crpLicense/pnimages/green_dot.gif" alt="' . _ACTIVE . '" title="' . _CRPLICENSE_CHANGE_STATUS . '"/>' . "\n";
			$statusimage .= '<img id="licensestatus_fake_P_' . $params['id'] . '"';
			$statusimage .= ($params['status'] == 'A') ? ' style="display:none" ' : '';
			$statusimage .= 'onclick="togglestatus(\'' . $params['id'] . '\',\'P\')" src="modules/crpLicense/pnimages/yellow_dot.gif" alt="' . _CRPLICENSE_PENDING . '" title="' . _CRPLICENSE_CHANGE_STATUS . '"/>' . "\n";
			$statusimage .= "</a>";
		}
		elseif ($params['status'] == 'A') $statusimage .= '<img id="licensestatus_' . $params['id'] . '" src="modules/crpLicense/pnimages/green_dot.gif" alt="' . _ACTIVE . '" title="' . _CRPLICENSE_CHANGE_STATUS . '"/>' . "\n";
		elseif ($params['status'] == 'P') $statusimage .= '<img id="licensestatus_' . $params['id'] . '" src="modules/crpLicense/pnimages/yellow_dot.gif" alt="' . _CRPLICENSE_PENDING . '" title="' . _CRPLICENSE_CHANGE_STATUS . '"/>' . "\n";
		else
			$statusimage .= '<img id="licensestatus_' . $params['id'] . '" src="modules/crpLicense/pnimages/red_dot.gif" alt="' . _CRPLICENSE_REJECTED . '" title="' . _CRPLICENSE_CHANGE_STATUS_MODIFYING . '" />' . "\n";
	}

	return $statusimage;
}
?>
