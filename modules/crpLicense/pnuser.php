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

function crpLicense_user_newlicense()
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_COMMENT))
	{
		return LogUtil :: registerPermissionError();
	}

	$modvars= pnModGetVar('crpLicense');
	$licenseArray= array ();

	$licenseArray= pnModAPIFunc('crpLicense', 'user', 'getall', array (
		'startnum' => '1',
		'modvars' => $modvars,
		'active' => 'A',
		'sortOrder' => 'ASC'
	));

	$license= new crpLicense();
	return $license->ui->newItemLicense($modvars, $licenseArray);
}

function crpLicense_user_modifylicense($args= array ())
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::Hook', '::', ACCESS_COMMENT))
	{
		return LogUtil :: registerPermissionError();
	}
	if (!$args['objectid'] || !$args['extrainfo']['module'])
	{
		LogUtil :: registerError(_MODARGSERROR);
	}

	$licenseChoosed= pnModAPIFunc('crpLicense', 'user', 'get', array (
		'id_module' => $args['objectid'],
		'modname' => $args['extrainfo']['module']
	));

	$modvars= pnModGetVar('crpLicense');

	$licenseArray= pnModAPIFunc('crpLicense', 'user', 'getall', array (
		'startnum' => '1',
		'modvars' => $modvars,
		'active' => 'A',
		'sortOrder' => 'ASC'
	));

	$license= new crpLicense();
	return $license->ui->modifyItemLicense($licenseChoosed, $modvars, $licenseArray);
}

function crpLicense_user_embedlicense($args= array ())
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::Hook', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	$modvars= pnModGetVar('crpLicense');
	$licenseChoosed= pnModAPIFunc('crpLicense', 'user', 'get', array (
		'id_module' => $args['objectid'],
		'modname' => $args['extrainfo']['module']
	));

	$license= new crpLicense();

	if (empty ($licenseChoosed))
	{
		return;
	}
	else
	{
		return $license->ui->displayItemLicense($licenseChoosed, $modvars, $args['extrainfo']['returnurl']);
	}
}

/**
 * get license's image
 *
 * @return blob image
 */
function crpLicense_user_get_image()
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	$license= new crpLicense();
	return $license->dao->getImage();
}

/**
 * get license's thumbnail thru gd library
 *
 * @return blob image
 */
function crpLicense_user_get_thumbnail()
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	$license= new crpLicense();
	return $license->getThumbnail();
}
?>
