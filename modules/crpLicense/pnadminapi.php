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

Loader :: includeOnce('modules/crpLicense/pnclass/crpLicense.php');

function crpLicense_adminapi_deletelicense($args= array ())
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::Hook', '::', ACCESS_COMMENT))
	{
		return LogUtil :: registerPermissionError();
	}

	if (!$args['objectid'])
	{
		LogUtil :: registerError(_MODARGSERROR);
	}

	$license= new crpLicense();

	return $license->deleteLicense($args['objectid'], $args['extrainfo']);
}

function crpLicense_adminapi_removelicense($args= array ())
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::Hook', '::', ACCESS_COMMENT))
	{
		return LogUtil :: registerPermissionError();
	}

	if (!$args['extrainfo']['module'])
	{
		LogUtil :: registerError(_MODARGSERROR);
	}

	$license= new crpLicense();

	return $license->removeLicense($args['extrainfo']);
}

/**
 * Retrieve list of licenses, filtered if specified
 */
function crpLicense_adminapi_getall($navigationValues)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_EDIT))
	{
		return LogUtil :: registerPermissionError();
	}

	$license= new crpLicense();

	return $license->dao->adminList($navigationValues['startnum'], $navigationValues['clear'], $navigationValues['modvars'], $navigationValues['active'], $navigationValues['sortOrder']);
}

/**
 * delete a license
 * @param $args['id_license'] ID of the video
 * @return bool true on success, false on failure
 */
function crpLicense_adminapi_delete($args)
{
	// Argument check
	if (!isset ($args['id']))
	{
		return LogUtil :: registerError(_MODARGSERROR);
	}

	// Check item exists before attempting deletion
	$licenseObj= new crpLicense();
	$oldData= $licenseObj->dao->getAdminData($args['id'], false);

	if ($oldData == false)
	{
		return LogUtil :: registerError(_NOSUCHITEM);
	}

	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::License', "::", ACCESS_DELETE))
	{
		return LogUtil :: registerError(_MODULENOAUTH);
	}

	if (!DBUtil :: deleteObjectByID('crplicense', $args['id'], 'id'))
	{
		return LogUtil :: registerError(_DELETEFAILED);
	}

	if (!DBUtil :: deleteObjectByID('crplicense_archive', $args['id'], 'id_license'))
	{
		return LogUtil :: registerError(_DELETEFAILED);
	}

	// remove cover
	$item= $licenseObj->dao->getFile($args['id'], 'image');
	if ($item)
	{
		if (!DBUtil :: deleteObjectByID('crplicense_images', $item['id'], 'id_license'))
			return LogUtil :: registerError(_DELETEFAILED);
	}

	// Let any hooks know that we have deleted an item.
	pnModCallHooks('item', 'delete', $args['id'], array (
		'module' => 'crpLicense'
	));

	return true;
}

/**
 * get available admin panel links
 *
 * @return array array of admin links
 */
function crpLicense_adminapi_getlinks()
{
	$links= array ();

	pnModLangLoad('crpLicense', 'admin');

	$itemname= _CRPLICENSE_LICENSE;
	$itemsname= _CRPLICENSE_LICENSES;

	if (SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_READ))
	{
		$links[]= array (
			'url' => pnModURL('crpLicense', 'admin', 'view'),
			'text' => pnML('_VIEWITEMS', array (
				'i' => $itemsname
			))
		);
	}
	if (SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_ADD))
	{
		$links[]= array (
			'url' => pnModURL('crpLicense', 'admin', 'new'),
			'text' => pnML('_CREATEITEM', array (
				'i' => $itemname
			))
		);
	}
	if (SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_ADMIN))
	{
		$links[]= array (
			'url' => pnModURL('crpLicense', 'admin', 'modifyconfig'),
			'text' => _MODIFYCONFIG
		);
	}

	return $links;
}

/**
 * modify item status
 *
 * @return string HTML output
 */
function crpLicense_adminapi_change_status($args= array ())
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_ADD))
	{
		return LogUtil :: registerPermissionError();
	}

	$video= new crpLicense();

	if ($args['status'] == 'P' || $args['status'] == 'A')
	{
		($args['status'] == 'A') ? $args['status']= 'P' : $args['status']= 'A';
		$video->dao->updateStatus($args['id'], $args['status']);
	}

	return;
}
?>