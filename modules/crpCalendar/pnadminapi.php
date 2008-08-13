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
 * retrieve list of events, filtered if specified
 * 
 * @return array events
 */
function crpCalendar_adminapi_getall($navigationValues)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_EDIT))
	{
		return LogUtil::registerPermissionError();
	}
	
	$calendar = new crpCalendar();

	return $calendar->dao->adminList($navigationValues['startnum'], $navigationValues['category'],
																		$navigationValues['clear'], $navigationValues['ignoreml'],
																		$navigationValues['modvars'], $navigationValues['mainCat'],
																		$navigationValues['active']);
}

/**
 * get available admin panel links
 * 
 * @return array admin links
 */
function crpCalendar_adminapi_getlinks()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_EDIT))
	{
		return LogUtil::registerPermissionError();
	}
  
  $links = array();

  pnModLangLoad('crpCalendar', 'admin');

  $itemname = _CRPCALENDAR_EVENT;
  $itemsname = _CRPCALENDAR_EVENTS;

  if (SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_EDIT)) {
      $links[] = array('url' => pnModURL('crpCalendar', 'admin', 'view'), 'text' => pnML('_VIEWITEMS', array('i' => $itemsname)));
  }
  if (SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_EDIT)) {
      $links[] = array('url' => pnModURL('crpCalendar', 'admin', 'new'), 'text' => pnML('_CREATEITEM', array('i' => $itemname)));
  }
  if (SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_EDIT)) {
      $links[] = array('url' => pnModURL('crpCalendar', 'admin', 'import_ical'), 'text' => _CRPCALENDAR_IMPORT_ICAL);
  }
  if (SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_DELETE)) {
      $links[] = array('url' => pnModURL('crpCalendar', 'admin', 'purge_events'), 'text' => _CRPCALENDAR_PURGE_EVENTS);
  }
  if (SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_ADMIN)) {
      $links[] = array('url' => pnModURL('crpCalendar', 'admin', 'modifyconfig'), 'text' => _MODIFYCONFIG);
  }

  return $links;
}

/**
 * modify item status
 * 
 * @return void
 */
function crpCalendar_adminapi_change_status($args=array())
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_ADD))
	{
		return LogUtil::registerPermissionError();
	}
	
	$calendar = new crpCalendar();
	
	if ($args['status']=='P' || $args['status']=='A')
	{
		($args['status']=='A')?$args['status']='P':$args['status']='A';
		$calendar->dao->updateStatus($args['eventid'], $args['status']); 
	}	
	
	return;
}

?>
