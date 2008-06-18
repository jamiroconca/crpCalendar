<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007, Daniele Conca
 * @link http://noc.postnuke.com/projects/crpcalendar Support and documentation
 * @author Daniele Conca <jami at cremonapalloza dot org>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */

Loader :: includeOnce('modules/crpCalendar/pnclass/crpCalendar.php');

/**
 * Retrieve list of events, filtered if specified
 */
function crpCalendar_userapi_getall($navigationValues)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	$calendar = new crpCalendar();

	return $calendar->dao->adminList($navigationValues['startnum'], $navigationValues['category'],
																		$navigationValues['clear'], $navigationValues['ignoreml'],
																		$navigationValues['modvars'], $navigationValues['mainCat'], 'A',
																		$navigationValues['interval'], $navigationValues['sortOrder'],
																		$navigationValues['startDate'], $navigationValues['endDate']);
}

/**
 * Retrieve list of events, filtered as specified, for form use
 */
function crpCalendar_userapi_getall_formlist($navigationValues)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	$calendar = new crpCalendar();

	return $calendar->dao->formList($navigationValues['startnum'], $navigationValues['category'],
																		$navigationValues['clear'], $navigationValues['ignoreml'],
																		$navigationValues['modvars'], $navigationValues['mainCat'], 'A',
																		$navigationValues['interval'], $navigationValues['sortOrder']);
}

/**
 * get a specific item
 * @param int $args['eventid'] id item to get
 * 
 * @return mixed item array, or false on failure
 */
function crpCalendar_userapi_get($args)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	$calendar = new crpCalendar();
	
	// Argument check
  if ((!isset($args['eventid']) || !is_numeric($args['eventid'])) &&
       !isset($args['title'])) {
      return LogUtil::registerError (_MODARGSERROR);
  }
    
	return $calendar->dao->getAdminData($args['eventid']);
}

/**
 * utility function to count the number of items held by this module
 * @return integer number of items held by this module
 */
function crpCalendar_userapi_countitems($args)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	$calendar = new crpCalendar();
	
	return $calendar->dao->countItems($args['category'], $args['active']);
}

?>