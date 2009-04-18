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

/**
 * return an array of items to show in the your account panel
 *
 * @return array array of items, or false on failure
 */
function crpCalendar_accountapi_getall($args)
{
	if (!isset ($args['uname']))
	{
		if (!pnUserloggedIn())
		{
			$uname = null;
		}
		else
		{
			$uname = pnUserGetVar('uname');
		}
	}

	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_COMMENT))
		$uname = null;

	// Create an array of links to return
	if ($uname != null)
	{
		pnModLangLoad('crpCalendar');
		$items[] = array (
			'url' => pnModURL('crpCalendar', 'user', 'new'),
			'module' => 'crpCalendar',
			'set' => 'pnimages',
			'title' => _CRPCALENDAR_SUBMIT,
			'icon' => 'admin.gif'
		);
		if (pnModGetVar('crpCalendar', 'enable_partecipation'))
			$items[] = array (
				'url' => pnModURL('crpCalendar', 'user', 'get_partecipations'),
				'module' => 'crpCalendar',
				'set' => 'pnimages',
				'title' => _CRPCALENDAR_EVENTS_MYLIST,
				'icon' => 'userdate.gif'
			);
	}
	else
		$items = null;

	// Return the items
	return $items;
}