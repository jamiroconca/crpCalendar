<?php

/**
 * crpTalk
 *
 * @copyright (c) 2010 Daniele Conca
 * @link http://code.zikula.org/crptalk Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpTalk
 */

/**
 * return an array of items to show in the your account panel
 *
 * @return array array of items, or false on failure
 */
function crpTalk_accountapi_getall($args)
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
	if (!SecurityUtil :: checkPermission('crpTalk::', '::', ACCESS_COMMENT))
		$uname = null;

	// Create an array of links to return
	if ($uname != null)
	{
		$items[] = array (
			'url' => pnModURL('crpTalk', 'user'),
			'module' => 'crpTalk',
			'set' => 'pnimages',
			'title' => _CRPTALK_CHAT,
			'icon' => 'admin.gif'
		);
	}
	else
		$items = null;

	// Return the items
	return $items;
}