<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007-2008, Daniele Conca
 * @link http://code.zikula.org/projects/crpcalendar Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
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
																		$navigationValues['startDate'], $navigationValues['endDate'], 
																		$navigationValues['typeList']);
}

/**
 * Retrieve list of events, filtered by uid
 */
function crpCalendar_userapi_getall_partecipations($navigationValues)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	$calendar = new crpCalendar();

	return $calendar->dao->getPartecipations($navigationValues['uid'], $navigationValues['startnum'],
																		$navigationValues['modvars'], $navigationValues['mainCat'], 'A',
																		$navigationValues['sortOrder'], null, null);
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
	
	// optional arguments
  if (isset($args['objectid'])) {
     $args['eventid'] = $args['objectid'];
  }
		
	// Argument check
  if ((!isset($args['eventid']) || !is_numeric($args['eventid'])) &&
       !isset($args['title'])) {
      return LogUtil::registerError (_MODARGSERROR);
  }
  
  $calendar = new crpCalendar(); 
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
	
	return $calendar->dao->countItems($args['category'], $args['active'], $args['uid'], $args['eventid'], $args['typeList']);
}

/**
 * utility function to count the number of items held by this module
 * @return integer number of items held by this module
 */
function crpCalendar_userapi_countitems_attendance($args)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	$calendar = new crpCalendar();
	
	return $calendar->dao->countAttendeeItems($args['category'], $args['active'], $args['uid'], $args['eventid']);
}

/**
 * get meta data for the module
 *
 */
function crpCalendar_userapi_getmodulemeta()
{
   return array('viewfunc'    => 'view',
                'displayfunc' => 'display',
                'newfunc'     => 'new',
                'createfunc'  => 'create',
                'modifyfunc'  => 'modify',
                'updatefunc'  => 'update',
                'deletefunc'  => 'delete',
                'titlefield'  => 'title',
                'itemid'      => 'eventid');
}

?>
