<?php

/**
 * crpCalendar
 *
 * @copyright (c) 2007,2009 Daniele Conca
 * @link http://code.zikula.org/crpcalendar Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */

Loader :: includeOnce('modules/crpCalendar/pnclass/crpCalendar.php');

/**
 * retrieve list of events, filtered as specified
 *
 * @return array events
 */
function crpCalendar_userapi_getall($navigationValues)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	$calendar = new crpCalendar();

	return $calendar->dao->adminList($navigationValues['startnum'], $navigationValues['category'], $navigationValues['clear'], $navigationValues['ignoreml'], $navigationValues['modvars'], $navigationValues['mainCat'], 'A', $navigationValues['interval'], $navigationValues['sortOrder'], $navigationValues['startDate'], $navigationValues['endDate'], $navigationValues['typeList'], $navigationValues['bylocation']);
}

/**
 * retrieve list of partecipations, filtered by uid
 *
 * @return array partecipations
 */
function crpCalendar_userapi_getall_partecipations($navigationValues)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	$calendar = new crpCalendar();

	return $calendar->dao->getPartecipations($navigationValues['uid'], $navigationValues['startnum'], $navigationValues['modvars'], $navigationValues['mainCat'], 'A', $navigationValues['sortOrder'], null, null);
}

/**
 * retrieve list of events, filtered as specified, for form use
 *
 * @return array events
 */
function crpCalendar_userapi_getall_formlist($navigationValues)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	$calendar = new crpCalendar();

	return $calendar->dao->formList($navigationValues['startnum'], $navigationValues['category'], $navigationValues['clear'], $navigationValues['ignoreml'], $navigationValues['modvars'], 'A', $navigationValues['interval'], $navigationValues['sortOrder']);
}

/**
 * get a specific item
 *
 * @return array event, or false on failure
 */
function crpCalendar_userapi_get($args)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	// optional arguments
	if (isset ($args['objectid']))
	{
		$args['eventid'] = $args['objectid'];
	}

	// Argument check
	if ((!isset ($args['eventid']) || !is_numeric($args['eventid'])) && !isset ($args['title']))
	{
		return LogUtil :: registerError(_MODARGSERROR);
	}

	$calendar = new crpCalendar();
	return $calendar->dao->getAdminData($args['eventid'], true, $args['title']);
}

/**
 * utility function to count the number of items held by this module
 *
 * @return integer number of items held by this module
 */
function crpCalendar_userapi_countitems($args)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	$calendar = new crpCalendar();

	return $calendar->dao->countItems($args['category'], $args['active'], $args['uid'], $args['eventid'], $args['typeList'], $calendar->modvars, $args['startDate'], $args['endDate']);
}

/**
 * utility function to count the number of attendances
 *
 * @return integer number of items held by this module
 */
function crpCalendar_userapi_countitems_attendance($args)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	$calendar = new crpCalendar();

	return $calendar->dao->countAttendeeItems($args['category'], $args['active'], $args['uid'], $args['eventid'], $calendar->modvars);
}

/**
 * get meta data for the module
 *
 * @return array metadata
 */
function crpCalendar_userapi_getmodulemeta()
{
	return array (
		'viewfunc' => 'view',
		'displayfunc' => 'display',
		'newfunc' => 'new',
		'createfunc' => 'create',
		'modifyfunc' => 'modify',
		'updatefunc' => 'update',
		'deletefunc' => 'delete',
		'titlefield' => 'title',
		'itemid' => 'eventid'
	);
}