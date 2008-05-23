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

Loader::includeOnce('modules/crpCalendar/pnclass/crpCalendar.php');

/**
 * the main user function
 *
 * @return string HTML string
 */
function crpCalendar_user_main()
{
    // Security check
    if (!SecurityUtil::checkPermission( 'crpCalendar::', '::', ACCESS_READ))
    { 
      return LogUtil::registerPermissionError();
    }
    
    // Create output object
    $pnRender = new pnRender('crpCalendar', false);

    // Return the output that has been generated by this function
    return $pnRender->fetch('crpcalendar_user_main.htm');
}


/**
 * view items
 *
 * @return string HTML output
 */
function crpCalendar_user_view()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->listEvents();
}

/**
 * view month items
 *
 * @return string HTML output
 */
function crpCalendar_user_month_view()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->listMonthEvents();
}

/**
 * view week items
 *
 * @return string HTML output
 */
function crpCalendar_user_week_view()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->listWeekEvents();
}

/**
 * view day items
 *
 * @return string HTML output
 */
function crpCalendar_user_day_view()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->listDayEvents();
}

/**
 * view item
 *
 * @return string HTML output
 */
function crpCalendar_user_display()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->displayEvent();
}

/**
 * view item
 *
 * @return string HTML output
 */
function crpCalendar_user_simple_display($args)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->simpleDisplayEvent($args['eventid']);
}


/**
 * get event's image
 * 
 * @return blob image
 */
function crpCalendar_user_get_image()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	$calendar = new crpCalendar();
	return $calendar->dao->getImage();
}

/**
 * get event's file
 * 
 * @return blob image
 */
function crpCalendar_user_get_file()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	$eventid=pnVarCleanFromInput('eventid');
	
	$calendar = new crpCalendar();
	$document = $calendar->dao->getFile($eventid,'document',true);
	
	Header("Content-type: {$document['content_type']}");
	Header("Content-Disposition: inline; filename={$document['name']}");
	//Header("Content-Length: " . strlen($file['binary_data']));

	echo $document['binary_data'];

	pnShutDown();
}

/**
 * get event's thumbnail thru gd library
 * 
 * @return blob image
 */
function crpCalendar_user_get_thumbnail()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	$calendar = new crpCalendar();
	return $calendar->getThumbnail();
}

/**
 * new item
 * 
 * @return string HTML output
 */
function crpCalendar_user_new()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_COMMENT))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->submitEvent();
}


/**
 * new item
 * 
 * @return string HTML output
 */
function crpCalendar_user_create()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_COMMENT))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->createEvent();
}

/**
 * modify item
 * 
 * @return string HTML output
 */
function crpCalendar_user_modify()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_MODERATE))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->editEvent();
}


/**
 * modify item
 * 
 * @return string HTML output
 */
function crpCalendar_user_update()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_MODERATE))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->updateEvent();
}


/**
 * feed items
 * 
 * @return string HTML output
 */
function crpCalendar_user_getfeed()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->getFeed();
}

/**
 * feed ics
 * 
 * @return string HTML output
 */
function crpCalendar_user_getICal()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->getICal();
}

/**
 * feed ics
 * 
 * @return string HTML output
 */
function crpCalendar_user_ical_exports()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->listICal();
}

/**
 * Add user's partecipation
 *
 * @return string HTML output
 */
function crpCalendar_user_add_partecipation()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ) 
			|| !pnUserLoggedIn() || !pnModGetVar('crpCalendar','enable_partecipation'))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->addPartecipation();
}

/**
 * Delete user's partecipation
 *
 * @return string HTML output
 */
function crpCalendar_user_delete_partecipation()
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ) 
			|| !pnUserLoggedIn() || !pnModGetVar('crpCalendar','enable_partecipation'))
	{
		return LogUtil::registerPermissionError();
	}

	$calendar = new crpCalendar();
	return $calendar->deletePartecipation();
}

?>
