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
 * get form list of categorized events
 *
 * @return array events
 */
function crpCalendar_ajax_getCategorizedEvent()
{
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		AjaxUtil :: error(pnVarPrepHTMLDisplay(_MODULENOAUTH));
	}

	pnModLangLoad('crpCalendar', 'admin');

	// get all module vars
	$modvars = pnModGetVar('crpCalendar');

	// load the category registry util
	if (!($class = Loader :: loadClass('CategoryRegistryUtil')))
		pn_exit('Unable to load class [CategoryRegistryUtil] ...');
	if (!($class = Loader :: loadClass('CategoryUtil')))
		pn_exit('Unable to load class [CategoryUtil] ...');

	$category = DataUtil :: convertFromUTF8(FormUtil :: getPassedValue('category', null, 'GET'));
	$startnum = '1';
	$mainCat = CategoryRegistryUtil :: getRegisteredModuleCategory('crpCalendar', 'crpcalendar', 'Main', '/__SYSTEM__/Modules/crpCalendar');
	$ignoreml = true;
	$sortOrder = 'DESC';
	$data = compact('startnum', 'category', 'clear', 'ignoreml', 'mainCat', 'cats', 'modvars', 'sortOrder');

	$events = pnModAPIFunc('crpCalendar', 'user', 'getall_formlist', $data);

	$resultlist = DataUtil :: convertFromUTF8($events);
	return $resultlist;
}

/**
 * swap obj_status	value
 *
 * @return array event status
 */
function crpCalendar_ajax_toggleStatus()
{
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_ADD))
	{
		AjaxUtil :: error(pnVarPrepHTMLDisplay(_MODULENOAUTH));
	}

	$eventid = FormUtil :: getPassedValue('eventid', null, 'GET');
	$status = FormUtil :: getPassedValue('status', null, 'GET');

	pnModAPIFunc('crpCalendar', 'admin', 'change_status', array (
		'eventid' => $eventid,
		'status' => $status
	));
	//($status=='A')?$status='P':$status='A';

	return array (
		'eventid' => $eventid,
		'status' => $status
	);
}