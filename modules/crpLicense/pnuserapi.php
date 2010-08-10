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

function crpLicense_userapi_createlicense($args= array ())
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

	$licenselist= FormUtil :: getPassedValue('licenselist', null, 'POST');
	$license= new crpLicense();

	return $license->insertLicense($args['objectid'], $args['extrainfo'], $licenselist);
}

function crpLicense_userapi_updatelicense($args= array ())
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

	$licenselist= FormUtil :: getPassedValue('licenselist', null, 'POST');
	$license= new crpLicense();

	return $license->editLicense($args['objectid'], $args['extrainfo'], $licenselist);
}

function crpLicense_userapi_get($args= array ())
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::Hook', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	$license= new crpLicense();
	return $license->dao->getLicenses($args['id_license'], $args['id_module'], $args['modname'], $args['extended']);
}

/**
 * Retrieve list of licenses, filtered if specified
 */
function crpLicense_userapi_getall($navigationValues)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::Hook', '::', ACCESS_COMMENT))
	{
		return LogUtil :: registerPermissionError();
	}

	$license= new crpLicense();

	return $license->dao->adminList($navigationValues['startnum'], $navigationValues['clear'], $navigationValues['modvars'], $navigationValues['active'], $navigationValues['sortOrder']);
}

/**
 * utility function to count the number of items held by this module
 * @return integer number of items held by this module
 */
function crpLicense_userapi_countitems($args)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpLicense::License', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	$license= new crpLicense();

	return $license->dao->countItems($args['active']);
}

/**
 * get meta data for the module
 *
 */
function crpLicense_userapi_getmodulemeta()
{
	return array (
		'viewfunc' => 'view',
		'displayfunc' => 'display',
		'newfunc' => 'new',
		'createfunc' => 'create',
		'modifyfunc' => 'modify',
		'updatefunc' => 'update',
		'deletefunc' => 'delete',
		'titlefield' => 'name',
		'itemid' => 'id'
	);
}