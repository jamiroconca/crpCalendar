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
 * retrieve list of events, filtered if specified
 *
 * @return array events
 */
function crpCalendar_adminapi_getall($navigationValues)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_EDIT))
	{
		return LogUtil :: registerPermissionError();
	}

	$calendar = new crpCalendar();

	return $calendar->dao->adminList($navigationValues['startnum'], $navigationValues['category'], $navigationValues['clear'], $navigationValues['ignoreml'], $navigationValues['modvars'], $navigationValues['mainCat'], $navigationValues['active'], null, $navigationValues['sortOrder'], null, null, $navigationValues['typeList'], null, $navigationValues['sortColumn']);
}

/**
 * get available admin panel links
 *
 * @return array admin links
 */
function crpCalendar_adminapi_getlinks()
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_EDIT))
	{
		return LogUtil :: registerPermissionError();
	}

	$dom = ZLanguage::getModuleDomain('crpCalendar');

	$links = array ();

	$itemname = __('Event', $dom);
	$itemsname = __('Calendar', $dom);

	if (SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_EDIT))
	{
		$links[] = array (
			'url' => pnModURL('crpCalendar', 'admin', 'view'),
			'text' => __f('%s List', $itemsname)
		);
	}
	if (SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_EDIT))
	{
		$links[] = array (
			'url' => pnModURL('crpCalendar', 'admin', 'new'),
			'text' => __f('Create %s', $itemname)
		);
	}
	if (SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_EDIT))
	{
		$links[] = array (
			'url' => pnModURL('crpCalendar', 'admin', 'import_ical'),
			'text' => __('iCal Import', $dom)
		);
	}
	if (SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_DELETE))
	{
		$links[] = array (
			'url' => pnModURL('crpCalendar', 'admin', 'purge_events'),
			'text' => __('Purge events', $dom)
		);
	}
	if (SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_ADMIN))
	{
		$links[] = array (
			'url' => pnModURL('crpCalendar', 'admin', 'modifyconfig'),
			'text' => __('Settings', $dom)
		);
	}

	return $links;
}

/**
 * modify item status
 *
 * @return void
 */
function crpCalendar_adminapi_change_status($args = array ())
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_ADD))
	{
		return LogUtil :: registerPermissionError();
	}

	$calendar = new crpCalendar();

	if ($args['status'] == 'P' || $args['status'] == 'A')
	{
		($args['status'] == 'A') ? $args['status'] = 'P' : $args['status'] = 'A';
		$calendar->dao->updateStatus($args['eventid'], $args['status']);
	}

	return;
}