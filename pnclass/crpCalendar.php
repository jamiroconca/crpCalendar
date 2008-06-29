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

Loader :: includeOnce('modules/crpCalendar/pnclass/crpCalendarUI.php');
Loader :: includeOnce('modules/crpCalendar/pnclass/crpCalendarDAO.php');

/**
 * crpCalendar Object
 */
class crpCalendar
{

	function crpCalendar()
	{
		$this->ui= new crpCalendarUI();
		$this->dao= new crpCalendarDAO();

		(function_exists('gd_info')) ? $this->gd= gd_info() : $this->gd= array ();
	}

	/**
	 * Return a condition about an event
	 * 
	 * @param int $eventid identifier
	 * 
	 * return bool
	 */
	function isAuthor($eventid= null)
	{
		$author= false;
		$author= $this->dao->isAuthor($eventid);

		return $author;
	}

	/**
	 * Main administrative page with event's list
	 * 
	 * @return string html 
	 */
	function manageEvents()
	{
		$navigationValues= $this->collectNavigationFromInput();

		// Get all matching pages
		$items= pnModAPIFunc('crpCalendar', 'admin', 'getall', $navigationValues);

		if (!$items)
			$items= array ();

		$rows= array ();
		foreach ($items as $key => $item)
		{
			$options= array ();
			$options[]= array (
				'url' => pnModURL('crpCalendar',
				'user',
				'display',
				array (
					'eventid' => $item['eventid']
				)
			), 'image' => 'demo.gif', 'title' => _VIEW);
			if (SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_ADD) || (SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_EDIT) && $this->isAuthor($item['eventid'])))
			{
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'admin',
					'modify',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'xedit.gif', 'title' => _EDIT);
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'admin',
					'clone',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'editcopy.gif', 'title' => _COPY);
				if (SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_DELETE))
				{
					$options[]= array (
						'url' => pnModURL('crpCalendar',
						'admin',
						'delete',
						array (
							'eventid' => $item['eventid']
						)
					), 'image' => '14_layer_deletelayer.gif', 'title' => _DELETE);
				}
			}

			// Add the calculated menu options to the item array
			$item['options']= $options;
			$rows[]= $item;
		}

		return $this->ui->adminList($rows, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars'], $navigationValues['active']);
	}

	/**
	 * Main user page with event's list
	 * 
	 * @return string html 
	 */
	function listEvents()
	{
		$navigationValues= $this->collectNavigationFromInput();
		pnSessionDelVar('crpCalendar_export_events');
		pnSessionDelVar('crpCalendar_choosed_time');

		// Get all matching pages
		$items= pnModAPIFunc('crpCalendar', 'user', 'getall', $navigationValues);

		if (!$items)
			$items= array ();

		$rows= array ();
		$exports= array ();
		foreach ($items as $key => $item)
		{
			$options= array ();
			$options[]= array (
				'url' => pnModURL('crpCalendar',
				'user',
				'display',
				array (
					'eventid' => $item['eventid']
				)
			), 'image' => 'demo.gif', 'title' => _VIEW);

			// subscribe to event
			if (pnUserLoggedIn() && pnModGetVar('crpCalendar', 'enable_partecipation') && !$this->dao->existPartecipation(pnUserGetVar('uid'), $item['eventid']))
			{
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'user',
					'add_partecipation',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'add_user.gif', 'title' => _CRPCALENDAR_ADD_PARTECIPATION);
			}
			elseif (pnUserLoggedIn() && pnModGetVar('crpCalendar', 'enable_partecipation') && $this->dao->existPartecipation(pnUserGetVar('uid'), $item['eventid']))
			{
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'user',
					'delete_partecipation',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'delete_user.gif', 'title' => _CRPCALENDAR_DELETE_PARTECIPATION);
			}

			if (SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_ADD))
			{
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'admin',
					'modify',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'xedit.gif', 'title' => _EDIT);
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'admin',
					'clone',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'editcopy.gif', 'title' => _COPY);
				if (SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_DELETE))
				{
					$options[]= array (
						'url' => pnModURL('crpCalendar',
						'admin',
						'delete',
						array (
							'eventid' => $item['eventid']
						)
					), 'image' => '14_layer_deletelayer.gif', 'title' => _DELETE);
				}
			}
			elseif ((SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_MODERATE) && $this->isAuthor($item['eventid'])))
			{
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'user',
					'modify',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'xedit.gif', 'title' => _EDIT);
			}

			// Add the calculated menu options to the item array
			$item['options']= $options;
			$rows[]= $item;
			$exports[]= $item['eventid'];
		}

		pnSessionSetVar('crpCalendar_export_events', $exports);
		pnSessionSetVar('crpCalendar_choosed_view', 'view');
		pnSessionSetVar('crpCalendar_return_url', pnModURL('crpCalendar', 'user', 'view'));

		return $this->ui->userList($rows, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars']);
	}

	/**
	 * Page with month's events list
	 * 
	 * @return string html 
	 */
	function listMonthEvents()
	{
		$navigationValues= $this->collectNavigationFromInput();
		pnSessionDelVar('crpCalendar_export_events');

		$date= $this->timeToDMY($navigationValues['t']);

		$days= DateUtil :: getMonthDates($date['m'], $date['y']);
		$daysexpanded= $days;
		$monthFirstDay= $days['1'];
		$monthLastDay= $days[count($days)];

		$navigationValues['startDate']= DateUtil :: getDatetime($this->backToFirstDOW(DateUtil :: parseUIDateTime($monthFirstDay)));
		$navigationValues['endDate']= DateUtil :: getDatetime($this->forwardToLastDOW(DateUtil :: parseUIDateTime($monthLastDay)));
		$navigationValues['sortOrder']= 'ASC';

		// Get all matching events
		$items= pnModAPIFunc('crpCalendar', 'user', 'getall', $navigationValues);

		if (!$items)
			$items= array ();

		$exports= array ();
		foreach ($items as $key => $item)
		{
			$exports[]= $item['eventid'];
		}

		pnSessionSetVar('crpCalendar_export_events', $exports);
		pnSessionSetVar('crpCalendar_choosed_view', 'month_view');
		pnSessionSetVar('crpCalendar_choosed_time', $navigationValues['t']);
		pnSessionSetVar('crpCalendar_return_url', pnModURL('crpCalendar', 'user', 'month_view'));

		$today= DateUtil :: getDatetime(time());

		// expand days array
		$this->expandFirstDOW(DateUtil :: parseUIDateTime($monthFirstDay), $daysexpanded);
		$this->expandLastDOW(DateUtil :: parseUIDateTime($monthLastDay), $daysexpanded);

		return $this->ui->userMonthList($items, $days, $daysexpanded, $navigationValues['t'], $date, $navigationValues['startDate'], $navigationValues['endDate'], $today, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars']);
	}

	/**
	 * Page with week's events list
	 * 
	 * @return string html 
	 */
	function listWeekEvents()
	{
		$navigationValues= $this->collectNavigationFromInput();
		pnSessionDelVar('crpCalendar_export_events');

		$date= $this->timeToDMY($navigationValues['t']);

		$days= array (
			DateUtil :: getDatetime($navigationValues['t']
		));
		$weekDay= DateUtil :: getDatetime($navigationValues['t']);

		$navigationValues['startDate']= DateUtil :: getDatetime($this->backToFirstDOW(DateUtil :: parseUIDateTime($weekDay)));
		$navigationValues['endDate']= DateUtil :: getDatetime($this->forwardToLastDOW(DateUtil :: parseUIDateTime($weekDay)));
		$navigationValues['sortOrder']= 'ASC';

		// Get all matching events
		$items= pnModAPIFunc('crpCalendar', 'user', 'getall', $navigationValues);

		if (!$items)
			$items= array ();

		$exports= array ();
		foreach ($items as $key => $item)
		{
			$exports[]= $item['eventid'];
		}

		pnSessionSetVar('crpCalendar_export_events', $exports);
		pnSessionSetVar('crpCalendar_choosed_view', 'week_view');
		pnSessionSetVar('crpCalendar_choosed_time', $navigationValues['t']);
		pnSessionSetVar('crpCalendar_return_url', pnModURL('crpCalendar', 'user', 'week_view'));

		// expand days array
		$this->expandFirstDOW(DateUtil :: parseUIDateTime($weekDay), $days);
		$this->expandLastDOW(DateUtil :: parseUIDateTime($weekDay), $days);

		$daysexpanded= $days;

		$monthDays= DateUtil :: getMonthDates($date['m'], $date['y']);

		// for style purpose
		foreach ($days as $kday => $day)
		{
			if (!in_array($day, $monthDays))
				unset ($days[$kday]);
		}

		$today= DateUtil :: getDatetime(time());

		return $this->ui->userWeekList($items, $days, $daysexpanded, $navigationValues['t'], $date, $navigationValues['startDate'], $navigationValues['endDate'], $today, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars']);
	}

	/**
	 * Page with day's events list
	 * 
	 * @return string html 
	 */
	function listDayEvents()
	{
		$navigationValues= $this->collectNavigationFromInput();
		pnSessionDelVar('crpCalendar_export_events');

		$date= $this->timeToDMY($navigationValues['t']);

		$day= DateUtil :: getDatetime($navigationValues['t']);
		$tomorrow= DateUtil :: getDatetime(mktime(0, 0, 0, $date['m'], $date['d'] + 1, $date['y']));

		$navigationValues['startDate']= DateUtil :: getDatetime(DateUtil :: parseUIDateTime($day));
		$navigationValues['endDate']= DateUtil :: getDatetime(DateUtil :: parseUIDateTime($tomorrow));
		$navigationValues['sortOrder']= 'ASC';

		// Get all matching events
		$items= pnModAPIFunc('crpCalendar', 'user', 'getall', $navigationValues);

		if (!$items)
			$items= array ();

		$exports= array ();
		foreach ($items as $key => $item)
		{
			$exports[]= $item['eventid'];
			$options= array ();

			// subscribe to event
			if (pnUserLoggedIn() && pnModGetVar('crpCalendar', 'enable_partecipation') && !$this->dao->existPartecipation(pnUserGetVar('uid'), $item['eventid']))
			{
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'user',
					'add_partecipation',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'add_user.gif', 'title' => _CRPCALENDAR_ADD_PARTECIPATION);
			}
			elseif (pnUserLoggedIn() && pnModGetVar('crpCalendar', 'enable_partecipation') && $this->dao->existPartecipation(pnUserGetVar('uid'), $item['eventid']))
			{
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'user',
					'delete_partecipation',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'delete_user.gif', 'title' => _CRPCALENDAR_DELETE_PARTECIPATION);
			}

			// Add the calculated menu options to the item array
			$items[$key]['options']= $options;
		}

		pnSessionSetVar('crpCalendar_export_events', $exports);
		pnSessionSetVar('crpCalendar_choosed_view', 'day_view');
		pnSessionSetVar('crpCalendar_choosed_time', $navigationValues['t']);
		pnSessionSetVar('crpCalendar_return_url', pnModURL('crpCalendar', 'user', 'day_view'));

		$today= DateUtil :: getDatetime(time());

		return $this->ui->userDayList($items, $day, $navigationValues['t'], $date, $navigationValues['startDate'], $navigationValues['endDate'], $today, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars']);
	}

	/**
	 * List event's partecipation by current user
	 */
	function listUserPartecipations()
	{
		$navigationValues= $this->collectNavigationFromInput();
		$navigationValues['uid']= (int) FormUtil :: getPassedValue('uid', pnUserGetVar('uid'), 'GET');
		//
		$items= pnModAPIFunc('crpCalendar', 'user', 'getall_partecipations', $navigationValues);

		$rows= array ();
		$exports= array ();
		foreach ($items as $kevent => $item)
		{
			$options= array ();
			$options[]= array (
				'url' => pnModURL('crpCalendar',
				'user',
				'display',
				array (
					'eventid' => $item['eventid']
				)
			), 'image' => 'demo.gif', 'title' => _VIEW);

			if (SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_ADD))
			{
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'admin',
					'modify',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'xedit.gif', 'title' => _EDIT);
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'admin',
					'clone',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'editcopy.gif', 'title' => _COPY);
				if (SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_DELETE))
				{
					$options[]= array (
						'url' => pnModURL('crpCalendar',
						'admin',
						'delete',
						array (
							'eventid' => $item['eventid']
						)
					), 'image' => '14_layer_deletelayer.gif', 'title' => _DELETE);
				}
			}
			elseif ((SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_MODERATE) && $this->isAuthor($item['eventid'])))
			{
				$options[]= array (
					'url' => pnModURL('crpCalendar',
					'user',
					'modify',
					array (
						'eventid' => $item['eventid']
					)
				), 'image' => 'xedit.gif', 'title' => _EDIT);
			}

			// Add the calculated menu options to the item array
			$item['options']= $options;
			$rows[]= $item;
			$exports[]= $item['eventid'];
		}

		pnSessionSetVar('crpCalendar_export_events', $exports);
		pnSessionSetVar('crpCalendar_choosed_view', 'get_partecipations');

		return $this->ui->userPartecipations($rows, $navigationValues['uid'], $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars']);
	}

	/**
	 * List overall attendance
	 */
	function listAttendees()
	{
		$navigationValues= $this->collectNavigationFromInput();
		// TODO : 9999 -> $navigationValues['modvars']
		$items= $this->dao->getEventPartecipations(null, $navigationValues['startnum'], 9999, null, 'A', 'DESC', null, 'uid');

		$rows= array ();
		$exports= array ();
		foreach ($items as $kevent => $item)
		{
			$options= array ();
			$options[]= array (
				'url' => pnModURL('crpCalendar',
				'user',
				'get_partecipations',
				array (
					'uid' => $item['uid']
				)
			), 'image' => 'vcalendar.gif', 'title' => _CRPCALENDAR_EVENTS_MYLIST);

			$options[]= array (
				'url' => pnModURL('Profile',
				'user',
				'view',
				array (
					'uid' => $item['uid']
				)
			), 'image' => 'personal.gif', 'title' => _VIEW);

			// Add the calculated menu options to the item array
			$item['options']= $options;
			$rows[]= $item;
		}

		pnSessionSetVar('crpCalendar_choosed_view', 'get_attendees');

		return $this->ui->attendeesList($rows, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars']);
	}

	/**
	 * Display user page with event
	 * 
	 * @return string html 
	 */
	function displayEvent()
	{
		$eventid= FormUtil :: getPassedValue('eventid', isset ($args['eventid']) ? $args['eventid'] : null, 'REQUEST');
		$objectid= FormUtil :: getPassedValue('objectid', isset ($args['objectid']) ? $args['objectid'] : null, 'REQUEST');
		$cat= (string) FormUtil :: getPassedValue('cat', isset ($args['cat']) ? $args['cat'] : null, 'GET');
		if (!empty ($objectid))
		{
			$eventid= $objectid;
		}

		pnSessionDelVar('crpCalendar_export_events');

		// Get the event
		$item= $this->dao->getAdminData($eventid);
		$item['image']= $this->dao->getFile($eventid, 'image');
		$item['document']= $this->dao->getFile($eventid, 'document');

		// get all module vars
		$modvars= pnModGetVar('crpCalendar');

		// The return value of the function is checked here
		if ($item == false || ($item['obj_status'] == 'P' && !SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_EDIT)))
		{
			return LogUtil :: registerError(_NOSUCHITEM);
		}

		$this->dao->updateCounter($eventid);

		$exports= array ();
		$exports= array (
			'url' => pnModURL('crpCalendar',
			'user',
			'getICal',
			array (
				'eventid' => $item['eventid']
			)
		), 'image' => 'ical.gif', 'title' => _CRPCALENDAR_ICAL);

		$item['exports']= $exports;

		// subscribe to event
		$options= array ();
		if (pnUserLoggedIn() && pnModGetVar('crpCalendar', 'enable_partecipation') && !$this->dao->existPartecipation(pnUserGetVar('uid'), $eventid))
		{
			$options[]= array (
				'url' => pnModURL('crpCalendar',
				'user',
				'add_partecipation',
				array (
					'eventid' => $item['eventid']
				)
			), 'image' => 'add_user.gif', 'title' => _CRPCALENDAR_ADD_PARTECIPATION);
		}
		elseif (pnUserLoggedIn() && pnModGetVar('crpCalendar', 'enable_partecipation') && $this->dao->existPartecipation(pnUserGetVar('uid'), $eventid))
		{
			$options[]= array (
				'url' => pnModURL('crpCalendar',
				'user',
				'delete_partecipation',
				array (
					'eventid' => $item['eventid']
				)
			), 'image' => 'delete_user.gif', 'title' => _CRPCALENDAR_DELETE_PARTECIPATION);
		}

		$item['options']= $options;
		
		// no pager for users
		$modvars['itemsperpage'] = -1;
		$item['partecipations']= $this->dao->getEventPartecipations($item['eventid'], -1, $modvars, null, 'A', 'DESC', null, 'uid');

		$dayDate= DateUtil :: formatDatetime($item['start_date'], '%Y-%m-%d');

		pnSessionSetVar('crpCalendar_choosed_time', DateUtil :: makeTimestamp($dayDate));
		pnSessionSetVar('crpCalendar_return_url', pnModURL('crpCalendar', 'user', 'display', array (
			'eventid' => $item['eventid']
		)));
		//pnSessionSetVar('crpCalendar_choosed_view', 'display');
		//pnSessionSetVar('crpCalendar_choosed_event', $item['eventid']);

		return $this->ui->userDisplay($eventid, $item, $modvars);
	}

	/**
	 * Display simple event
	 * 
	 * @return string html 
	 */
	function simpleDisplayEvent($eventid= null)
	{
		// Get the event
		$item= $this->dao->getAdminData($eventid);
		$item['image']= $this->dao->getFile($eventid, 'image');
		// get all module vars
		$modvars= pnModGetVar('crpCalendar');

		// The return value of the function is checked here
		if ($item == false || ($item['obj_status'] == 'P' && !SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_EDIT)))
		{
			return LogUtil :: registerError(_NOSUCHITEM);
		}

		$this->dao->updateCounter($eventid);

		return $this->ui->userSimpleDisplay($eventid, $item, $modvars);
	}

	/**
	 * Insert an event
	 * 
	 * @return string html
	 */
	function newEvent()
	{
		$inputValues= $this->collectDataFromInput();
		$temp_values= array ();
		$temp_values= pnSessionGetVar('crpCalendar_temp_values');
		$avail= array ();

		if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations'))
			$avail= $this->getAvailableLocations();

		return $this->ui->newEvent($temp_values, $inputValues['mainCat'], $inputValues['modvars'], $avail);
	}

	/**
	 * Insert an event
	 * 
	 * @return string html
	 */
	function submitEvent()
	{
		$inputValues= $this->collectDataFromInput();
		$temp_values= array ();
		$temp_values= pnSessionGetVar('crpCalendar_temp_values');
		$avail= array ();

		if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations'))
			$avail= $this->getAvailableLocations();

		return $this->ui->submitEvent($temp_values, $inputValues['mainCat'], $inputValues['modvars'], $avail);
	}

	/**
	 * update an event
	 * 
	 * @param int $eventid item identifier
	 * @param array $inputValues array of updated values
	 * 
	 * @return string html
	 */
	function createEvent()
	{
		$returnType= '';
		$inputValues= array ();

		if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_EDIT))
			$returnType= 'user';
		else
			$returnType= 'admin';

		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpCalendar', $returnType, 'view'));

		$inputValues= $this->collectDataFromInput();
		pnSessionSetVar('crpCalendar_temp_values', $inputValues['event']);

		$startDate= $this->buildDate($inputValues['event']['startDay'], $inputValues['event']['startMonth'], $inputValues['event']['startYear']);

		if ($inputValues['event']['day_event'] == '0')
			$endDate= $this->buildDate($inputValues['event']['endDay'], $inputValues['event']['endMonth'], $inputValues['event']['endYear']);
		else
			$endDate= $startDate;

		if (!$startDate || !$endDate)
		{
			LogUtil :: registerError(_CRPCALENDAR_INVALID_DATE);
			return pnRedirect(pnModUrl('crpCalendar', $returnType, 'new'));
		}

		$startTime= $this->buildTime($inputValues['event']['startMinute'], $inputValues['event']['startHour']);
		$endTime= $this->buildTime($inputValues['event']['endMinute'], $inputValues['event']['endHour']);

		$inputValues['event']['start_date']= $startDate . ' ' . $startTime;
		$inputValues['event']['end_date']= $endDate . ' ' . $endTime;

		if (strtotime($inputValues['event']['start_date']) > strtotime($inputValues['event']['end_date']))
		{
			LogUtil :: registerError(_CRPCALENDAR_INVALID_INTERVAL);
			return pnRedirect(pnModUrl('crpCalendar', $returnType, 'new'));
		}

		if (!$this->dao->create($inputValues))
		{
			// Error
			return pnRedirect(pnModUrl('crpCalendar', $returnType, 'new'));
		}

		// all went fine
		LogUtil :: registerStatus(_CREATESUCCEDED . ' ' . (($returnType == 'user') ? _CRPCALENDAR_WAITING : ''));
		pnSessionDelVar('crpCalendar_temp_values');

		return pnRedirect(pnModURL('crpCalendar', $returnType, 'view'));
	}

	/**
	 * Modify an event
	 * 
	 * @param int $eventid item identifier
	 * 
	 * @return string html
	 */
	function modifyEvent()
	{
		$inputValues= $this->collectDataFromInput();

		// Get the event
		$item= $this->dao->getAdminData($inputValues['eventid']);

		if ($item == false)
		{
			return LogUtil :: registerError(_NOSUCHITEM);
		}

		// Security check
		if (!SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_ADD) && !(SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_EDIT) && $this->isAuthor($item['eventid'])))
		{
			return LogUtil :: registerPermissionError();
		}

		$avail= array ();

		if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations'))
		{
			if (is_numeric($item['location']))
			{
				$item['locations']= $item['location'];
				unset ($item['location']);
			}
			$avail= $this->getAvailableLocations();
		}

		return $this->ui->modifyEvent($item, $inputValues['mainCat'], $inputValues['modvars'], $avail);
	}

	/**
	 * Modify an event
	 * 
	 * @param int $eventid item identifier
	 * 
	 * @return string html
	 */
	function editEvent()
	{
		$inputValues= $this->collectDataFromInput();

		// Get the event
		$item= $this->dao->getAdminData($inputValues['eventid']);

		if ($item == false)
		{
			return LogUtil :: registerError(_NOSUCHITEM);
		}

		// Security check
		if (!(SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_MODERATE) && $this->isAuthor($item['eventid'])))
		{
			return LogUtil :: registerPermissionError();
		}

		$avail= array ();

		if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations'))
		{
			if (is_numeric($item['location']))
			{
				$item['locations']= $item['location'];
				unset ($item['location']);
			}
			$avail= $this->getAvailableLocations();
		}

		return $this->ui->editEvent($item, $inputValues['mainCat'], $inputValues['modvars'], $avail);
	}

	/**
	 * update an event
	 * 
	 * @param int $eventid item identifier
	 * @param array $inputValues array of updated values
	 * 
	 * @return string html
	 */
	function updateEvent()
	{
		$inputValues= array ();

		if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_EDIT))
			$returnType= 'user';
		else
			$returnType= 'admin';

		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpCalendar', $returnType, 'view'));

		$inputValues= $this->collectDataFromInput();

		// Security check
		if (!SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_ADD) && !(SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_MODERATE) && $this->isAuthor($inputValues['eventid'])))
		{
			return LogUtil :: registerPermissionError();
		}

		$startDate= $this->buildDate($inputValues['event']['startDay'], $inputValues['event']['startMonth'], $inputValues['event']['startYear']);

		if ($inputValues['event']['day_event'] == '0')
			$endDate= $this->buildDate($inputValues['event']['endDay'], $inputValues['event']['endMonth'], $inputValues['event']['endYear']);
		else
			$endDate= $startDate;

		if (!$startDate || !$endDate)
		{
			LogUtil :: registerError(_CRPCALENDAR_INVALID_DATE);
			return pnRedirect(pnModUrl('crpCalendar', 'admin', 'modify', array (
				'eventid' => $inputValues['eventid']
			)));
		}

		$startTime= $this->buildTime($inputValues['event']['startMinute'], $inputValues['event']['startHour']);
		$endTime= $this->buildTime($inputValues['event']['endMinute'], $inputValues['event']['endHour']);

		$inputValues['event']['start_date']= $startDate . ' ' . $startTime;
		$inputValues['event']['end_date']= $endDate . ' ' . $endTime;

		if (strtotime($inputValues['event']['start_date']) > strtotime($inputValues['event']['end_date']))
		{
			LogUtil :: registerError(_CRPCALENDAR_INVALID_INTERVAL);
			return pnRedirect(pnModUrl('crpCalendar', 'admin', 'modify', array (
				'eventid' => $inputValues['eventid']
			)));
		}

		if (!$this->dao->update($inputValues))
		{
			// Error
			return pnRedirect(pnModUrl('crpCalendar', $returnType, 'modify', array (
				'eventid' => $inputValues['eventid']
			)));
		}

		// all went fine
		LogUtil :: registerStatus(_UPDATESUCCEDED);

		return pnRedirect(pnModURL('crpCalendar', $returnType, 'view'));
	}

	/**
	 * Clone an event
	 * 
	 * @param int $eventid item identifier
	 * 
	 * @return string html
	 */
	function cloneEvent()
	{
		$inputValues= $this->collectDataFromInput();

		// Get the event
		$item= $this->dao->getAdminData($inputValues['eventid']);

		if ($item == false)
		{
			return LogUtil :: registerError(_NOSUCHITEM);
		}

		// change values
		$inputValues['event']= array (
			'title' => _CRPCALENDAR_CLONE_TITLE . ' ' . $item['title'],
			'urltitle' => null,
			'location' => $item['location'],
			'url' => $item['url'],
			'contact' => $item['contact'],
			'organiser' => $item['organiser'],
			'__CATEGORIES__' => array (
				'Main' => $item['__CATEGORIES__']['Main']['id']
			),
			'event_text' => $item['event_text'],
			'start_date' => $item['start_date'],
			'end_date' => $item['end_date'],
			'obj_status' => $item['obj_status'],
			'day_event' => $item['day_event']
		);

		$inputValues['image']= array (
			'id' => $item['image']['id']
		);
		$inputValues['document']= array (
			'id' => $item['document']['id']
		);

		// Security check
		if (!SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_ADD) && !(SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_EDIT) && $this->isAuthor($item['eventid'])))
		{
			return LogUtil :: registerPermissionError();
		}

		if (!$this->dao->cloneEvent($inputValues))
		{
			// Error
			return pnRedirect(pnModUrl('crpCalendar', 'admin', 'view'));
		}

		// all went fine
		LogUtil :: registerStatus(_CREATESUCCEDED);
		pnSessionDelVar('crpCalendar_temp_values');

		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * Delete an event
	 * 	 
	 * @param int $eventid item identifier
	 * 
	 * @return string html
	 */
	function deleteEvent()
	{
		$eventid= FormUtil :: getPassedValue('eventid', null);

		// Get the event
		$item= $this->dao->getAdminData($eventid);

		if ($item == false)
		{
			return LogUtil :: registerError(_NOSUCHITEM);
		}

		// Security check
		if (!SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_DELETE))
		{
			return LogUtil :: registerPermissionError();
		}

		return $this->ui->deleteEvent($eventid);
	}

	/**
	 * Delete afile
	 * 	 
	 * @param int $eventid item identifier
	 * 
	 * @return string html
	 */
	function deleteFile()
	{
		$eventid= FormUtil :: getPassedValue('eventid', null, 'GET');
		$file_type= FormUtil :: getPassedValue('file_type', null, 'GET');

		// Security check
		if (!SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_ADD) && !(SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_EDIT) && $this->isAuthor($eventid)))
		{
			return LogUtil :: registerPermissionError();
		}

		if ($this->dao->deleteFile($file_type, $eventid))
			LogUtil :: registerStatus(_DELETESUCCEDED);

		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * Delete an event
	 * 	 
	 * @param int $eventid item identifier
	 * 
	 * @return string html
	 */
	function removeEvent()
	{
		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpCalendar', 'admin', 'view'));

		$eventid= FormUtil :: getPassedValue('eventid', null);

		// Security check
		if (!SecurityUtil :: checkPermission('crpCalendar::', "::", ACCESS_DELETE))
		{
			return LogUtil :: registerPermissionError();
		}

		// Delete the page
		if ($this->dao->removeEvent($eventid))
			LogUtil :: registerStatus(_DELETESUCCEDED);

		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * Change item status
	 * 
	 * @param int $eventid item identifier
	 * @param string $obj_status active or pending
	 * 
	 * @return string html
	 */
	function changeStatus()
	{
		$eventid= FormUtil :: getPassedValue('eventid', null);
		$obj_status= FormUtil :: getPassedValue('obj_status', null);

		if ($obj_status == 'P' || $obj_status == 'A')
		{
			($obj_status == 'A') ? $obj_status= 'P' : $obj_status= 'A';
			if (!$this->dao->updateStatus($eventid, $obj_status))
				LogUtil :: registerError(_UPDATEFAILED);
			else
				LogUtil :: registerStatus(_UPDATESUCCEDED);
		}
		else
			LogUtil :: registerError(_UPDATEFAILED);

		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * Add event partecipation
	 * 
	 * @param int $eventid item identifier
	 *  
	 * @return string html
	 */
	function addPartecipation()
	{
		$eventid= FormUtil :: getPassedValue('eventid', null);

		if (!$this->dao->addPartecipation(pnUserGetVar('uid'), $eventid))
			LogUtil :: registerError(_UPDATEFAILED);
		else
			LogUtil :: registerStatus(_UPDATESUCCEDED);

		return pnRedirect(pnSessionGetVar('crpCalendar_return_url'));
	}

	/**
	 * Delete event partecipation
	 * 
	 * @param int $eventid item identifier
	 *  
	 * @return string html
	 */
	function deletePartecipation()
	{
		$eventid= FormUtil :: getPassedValue('eventid', null);

		if (!$this->dao->deletePartecipation(pnUserGetVar('uid'), $eventid))
			LogUtil :: registerError(_UPDATEFAILED);
		else
			LogUtil :: registerStatus(_UPDATESUCCEDED);

		return pnRedirect(pnSessionGetVar('crpCalendar_return_url'));
	}

	/**
	 * Modify module's configuration
	 */
	function modifyConfig()
	{
		// get all module vars
		$modvars= pnModGetVar('crpCalendar');

		return $this->ui->modifyConfig($modvars, $this->gd);
	}

	/**
	 * Update module's configuration
	 */
	function updateConfig()
	{
		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpCalendar', 'admin', 'view'));

		$navigationValues= $this->collectNavigationFromInput();

		// Update module variables
		$itemsperpage= (int) FormUtil :: getPassedValue('itemsperpage', 25, 'POST');
		if ($itemsperpage < 1)
		{
			$itemsperpage= 25;
		}
		pnModSetVar('crpCalendar', 'itemsperpage', $itemsperpage);
		$enablecategorization= (bool) FormUtil :: getPassedValue('enablecategorization', false, 'POST');
		pnModSetVar('crpCalendar', 'enablecategorization', $enablecategorization);
		$addcategorytitletopermalink= (bool) FormUtil :: getPassedValue('addcategorytitletopermalink', false, 'POST');
		pnModSetVar('crpCalendar', 'addcategorytitletopermalink', $addcategorytitletopermalink);
		// RSS
		$crpcalendar_enable_rss= (bool) FormUtil :: getPassedValue('crpcalendar_enable_rss', false, 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_enable_rss', $crpcalendar_enable_rss);
		$crpcalendar_show_rss= (bool) FormUtil :: getPassedValue('crpcalendar_show_rss', false, 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_show_rss', $crpcalendar_show_rss);
		$crpcalendar_rss= (string) FormUtil :: getPassedValue('crpcalendar_rss', false, 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_rss', $crpcalendar_rss);
		$file_dimension= (int) FormUtil :: getPassedValue('file_dimension', 35000, 'POST');
		pnModSetVar('crpCalendar', 'file_dimension', $file_dimension);
		$image_width= (int) FormUtil :: getPassedValue('image_width', 100, 'POST');
		pnModSetVar('crpCalendar', 'image_width', $image_width);
		$crpcalendar_use_gd= (bool) FormUtil :: getPassedValue('crpcalendar_use_gd', false, 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_use_gd', $crpcalendar_use_gd);
		$crpcalendar_userlist_image= (bool) FormUtil :: getPassedValue('crpcalendar_userlist_image', false, 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_userlist_image', $crpcalendar_userlist_image);
		$userlist_width= (int) FormUtil :: getPassedValue('userlist_width', 32, 'POST');
		pnModSetVar('crpCalendar', 'userlist_width', $userlist_width);
		$crpcalendar_theme= FormUtil :: getPassedValue('crpcalendar_theme', 'default', 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_theme', $crpcalendar_theme);
		$crpcalendar_start_year= (int) FormUtil :: getPassedValue('crpcalendar_start_year', date("Y"), 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_start_year', $crpcalendar_start_year);
		$document_dimension= (int) FormUtil :: getPassedValue('document_dimension', 55000, 'POST');
		pnModSetVar('crpCalendar', 'document_dimension', $document_dimension);
		$enable_partecipation= (bool) FormUtil :: getPassedValue('enable_partecipation', false, 'POST');
		pnModSetVar('crpCalendar', 'enable_partecipation', $enable_partecipation);
		$enable_locations= (bool) FormUtil :: getPassedValue('enable_locations', false, 'POST');
		pnModSetVar('crpCalendar', 'enable_locations', $enable_locations);

		// Let any other modules know that the modules configuration has been updated
		pnModCallHooks('module', 'updateconfig', 'crpCalendar', array (
			'module' => 'crpCalendar'
		));

		// the module configuration has been updated successfuly
		LogUtil :: registerStatus(_CONFIGUPDATED);

		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * Collect navigation input value
	 * 
	 * @param int $startnum pager offset
	 * @param int $category current category if specified
	 * @param bool clear clean category
	 * @param bool $ignoreml ignore multilanguage
	 * 
	 * @return array input values 
	 */
	function collectNavigationFromInput()
	{
		// Get parameters from whatever input we need.
		$startnum= (int) FormUtil :: getPassedValue('startnum', null, 'GET');
		$category= FormUtil :: getPassedValue('events_category', null);
		$active= FormUtil :: getPassedValue('events_status', null);
		$clear= FormUtil :: getPassedValue('clear');
		$month= FormUtil :: getPassedValue('Date_Month', null);
		$year= FormUtil :: getPassedValue('Date_Year', null);
		$day= FormUtil :: getPassedValue('Date_Day', null);

		if ($day && $month && $year)
			pnSessionSetVar('crpCalendar_choosed_time', DateUtil :: makeTimestamp($year . '-' . $month . '-' . $day));

		$t= FormUtil :: getPassedValue('t', (pnSessionGetVar('crpCalendar_choosed_time')) ? pnSessionGetVar('crpCalendar_choosed_time') : time());

		if ($clear)
		{
			$active= null;
			$category= null;
			$t= time();
		}

		$ignoreml= FormUtil :: getPassedValue('ignoreml', true);
		$sortOrder= FormUtil :: getPassedValue('sortOrder', (pnSessionGetVar('crpCalendar_choosed_view') == 'mont_view') ? 'ASC' : 'DESC');

		// load the category registry util
		if (!($class= Loader :: loadClass('CategoryRegistryUtil')))
			pn_exit('Unable to load class [CategoryRegistryUtil] ...');
		if (!($class= Loader :: loadClass('CategoryUtil')))
			pn_exit('Unable to load class [CategoryUtil] ...');

		$mainCat= CategoryRegistryUtil :: getRegisteredModuleCategory('crpCalendar', 'crpcalendar', 'Main', '/__SYSTEM__/Modules/crpCalendar');
		$cats= CategoryUtil :: getCategoriesByParentID($mainCat);

		// get all module vars
		$modvars= pnModGetVar('crpCalendar');

		$data= compact('startnum', 'category', 'active', 'clear', 'ignoreml', 'mainCat', 'cats', 'modvars', 'sortOrder', 't');

		return $data;
	}

	/**
	 * Collect data from insert/modification form
	 * 
	 * @param int $eventid item identifier
	 * @param int $objectid object identifier
	 * @param array page item values
	 * 
	 * @return array collection of values
	 */
	function collectDataFromInput()
	{
		$eventid= FormUtil :: getPassedValue('eventid', null);
		$objectid= FormUtil :: getPassedValue('objectid', null);
		$type= FormUtil :: getPassedValue('type');

		if (!empty ($objectid))
		{
			$eventid= $objectid;
		}

		$event= FormUtil :: getPassedValue('event', null, 'POST');
		$event_image= FormUtil :: getPassedValue('event_image', null, 'FILES');
		$event_document= FormUtil :: getPassedValue('event_document', null, 'FILES');

		(!empty ($event['objectid'])) ? $event['eventid']= $event['objectid'] : '';
		(!$event['day_event']) ? $event['day_event']= 0 : '';

		// load the category registry util
		if (!($class= Loader :: loadClass('CategoryRegistryUtil')))
		{
			pn_exit('Unable to load class [CategoryRegistryUtil] ...');
		}
		$mainCat= CategoryRegistryUtil :: getRegisteredModuleCategory('crpCalendar', 'crpcalendar', 'Main', '/__SYSTEM__/Modules/crpCalendar');

		// get all module vars
		$modvars= pnModGetVar('crpCalendar');

		$data= compact('eventid', 'objectid', 'event', 'event_image', 'event_document', 'mainCat', 'modvars');

		return $data;
	}

	/**
	 * Build date
	 * 
	 * @param int $day day number
	 * @param int $month month number
	 * @param int $year year value
	 * 
	 * @return string yyyy-mm-dd
	 */
	function buildDate($day= null, $month= null, $year= null)
	{
		if (!checkdate($month, $day, $year))
			$dateBuild= null;
		else
			$dateBuild= $year . '-' . $month . '-' . $day;

		return $dateBuild;
	}

	/**
	 * Build date
	 * 
	 * @param int $day day number
	 * @param int $month month number
	 *  
	 * @return string hh:mm:00
	 */
	function buildTime($minute, $hour)
	{
		$timeBuild= $hour . ':' . $minute . ':00';

		return $timeBuild;
	}

	/**
	 * Return event last modified date
	 * 
	 * @param int $eventid identifier
	 * 
	 * return bool
	 */
	function getEventDate($eventid= null, $dateType= null)
	{
		$modifiedDate= false;
		$modifiedDate= $this->dao->getEventDate($eventid, $dateType);

		return $modifiedDate;
	}

	/**
	 * Retrieve info about a rss module plugin
	 * 
	 *
	 * */
	function loadRSS($modname, $modrss, $id_lang= '')
	{
		$result= false;

		$modinfo= pnModGetInfo(pnModGetIdFromName($modname));
		$moddir= 'modules/' . pnVarPrepForOS($modinfo['directory']) . '/pnrss';
		$langdir= 'modules/' . pnVarPrepForOS($modinfo['directory']) . '/pnlang';
		$infofunc= "{$modname}_{$modrss}rss_info";

		if (!$id_lang)
			$id_lang= pnUserGetLang();

		// Load the rss
		$incfile= $modrss . '.php';
		$filepath= $moddir . '/' . pnVarPrepForOS($incfile);
		if (!file_exists($filepath))
			return false;

		include_once $filepath;

		// Load the RSS language files
		$currentlangfile= $langdir . '/' . pnVarPrepForOS($id_lang) . '/' . pnVarPrepForOS($incfile);
		$defaultlangfile= $langdir . '/' . pnVarPrepForOS(pnConfigGetVar('language')) . '/' . pnVarPrepForOS($incfile);
		if (file_exists($currentlangfile))
			include_once $currentlangfile;
		elseif (file_exists($defaultlangfile)) include_once $defaultlangfile;

		// get the rss info
		if (function_exists($infofunc) && ($info= $infofunc ()) && ($info !== false))
		{
			// set the module and keys for the new rss
			if (!isset ($info['module']))
				$info['module']= $modname;
			$info['mid']= pnModGetIDFromName($$modname);

			// Initialise rss if required (new-style)
			$initfunc= "{$modname}_{$modrss}rss_init";
			if (function_exists($initfunc))
			{
				pnModLangLoad($modname);
				$initfunc ();
			}
			$result= $info;
		}
		//
		return $result;
	}

	/**
	 * Display RSS content
	 * 
	 * */
	function getFeed()
	{
		$result= '';

		// Return if not enabled
		if (!pnModGetVar('crpCalendar', 'crpcalendar_enable_rss'))
			return $result;
		//	header("Content-Type: text/plain\n\n");	//debug

		$rssinfo= $this->loadRSS('crpCalendar', 'events', pnUserGetLang());

		$feedfunc= "crpCalendar_events_rss_feed";
		$list= array ();
		if (function_exists($feedfunc))
			$list= $feedfunc ();

		$data['xml_lang']= substr(pnUserGetLang(), 0, 2);
		$data['publ_date']= date('Y-m-d H:i:s', time());
		$selfurl= pnModUrl('crpCalendar', 'user', 'getfeed');
		$data['selfurl']= $selfurl;
		$data['format']= pnModGetVar('crpCalendar', 'crpcalendar_rss');
		$sitename= pnConfigGetVar('sitename');

		Header("Content-Disposition: inline; filename=" . $sitename . "_events.xml");
		if ($data['format'] == _CRPCALENDAR_ATOM)
			header("Content-Type: application/atom+xml\n\n");
		else
			header("Content-Type: application/rss+xml\n\n");
		//	header("Content-Type: text/xml\n\n");

		$result= $this->ui->drawFeed($data, $list);
		echo $result;
		pnShutDown();
	}

	/**
	 * Display iCal content
	 * 
	 * */
	function getICal()
	{
		$eventid= FormUtil :: getPassedValue('eventid', isset ($args['eventid']) ? $args['eventid'] : null, 'REQUEST');

		$result= '';

		$selfurl= pnModUrl('crpCalendar', 'user', 'display', array (
			'eventid' => $eventid
		));
		$result .= $this->ui->drawICalHeader($selfurl) . "\n";

		// Get the event
		$item= $this->dao->getAdminData($eventid, false);

		$data['uid']= pnUserGetVar('uname', $item['cr_uid']);
		$data['summary']= $item['title'];
		$data['dtstart']= $item['start_date'];
		$data['dtend']= $item['end_date'];
		$data['description']= $item['event_text'];
		$data['categories']= $item['__CATEGORIES__']['Main']['display_name']['' . pnUserGetLang() . ''];
		$data['dtstamp']= $item['cr_date'];
		$data['location']= $item['location'];
		$data['url']= $item['url'];
		$data['eventid']= $eventid;

		$result .= $this->ui->drawICal($data) . "\n";
		$result .= $this->ui->drawICalFooter();

		Header("Content-Disposition: attachment; filename={$eventid}.ics");
		Header("Content-Type: text/calendar\n\n");
		//header("Content-Type: text/plain\n\n");

		echo $result;
		pnShutDown();
	}

	/**
	 * Display iCal content
	 * 
	 * */
	function listICal()
	{
		$exports= pnSessionGetVar('crpCalendar_export_events');

		$result= '';

		$selfurl= pnModUrl('crpCalendar', 'user', 'view');
		$result .= $this->ui->drawICalHeader($selfurl) . "\n";
		// Get the events
		foreach ($exports as $eventid)
		{
			$item= $this->dao->getAdminData($eventid, false);

			$data['uid']= pnUserGetVar('uname', $item['cr_uid']);
			$data['summary']= $item['title'];
			$data['dtstart']= $item['start_date'];
			$data['dtend']= $item['end_date'];
			$data['description']= $item['event_text'];
			$data['categories']= $item['__CATEGORIES__']['Main']['display_name']['' . pnUserGetLang() . ''];
			$data['dtstamp']= $item['cr_date'];
			$data['location']= $item['location'];
			$data['url']= $item['url'];
			$data['eventid']= $eventid;

			$result .= $this->ui->drawICal($data) . "\n";
		}

		$result .= $this->ui->drawICalFooter();

		Header("Content-Disposition: attachment; filename=crpCalendar.ics");
		Header("Content-Type: text/calendar\n\n");
		//header("Content-Type: text/plain\n\n");

		echo $result;
		pnShutDown();
	}

	/**
	 * Generate thumbnail for image
	 * 
	 * @param int id doc
	 * @param string width doc
	 * @return nothing
	 */
	function getThumbnail()
	{
		$eventid= FormUtil :: getPassedValue('eventid', isset ($args['eventid']) ? $args['eventid'] : null, 'REQUEST');
		$width= FormUtil :: getPassedValue('width', isset ($args['width']) ? $args['width'] : null, 'REQUEST');
		if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
			pnShutDown();

		$file= $this->dao->getFile($eventid, 'image', true);
		$modifiedDate= $this->dao->getEventDate($eventid, 'lu_date');

		if (!(is_numeric($width) && $width > 0))
			$width= pnModGetVar('crpCalendar', 'image_width');
		$params['width']= $width; //	$params['append_ghosted']=1;
		$params['modifiedDate']= $modifiedDate;

		crpCalendar :: imageGetThumbnail($file['binary_data'], $file['filename'], $file['content_type'], $params);
	}

	function imageGetThumbnail(& $pSrcImage, $filename, $content_type, $params= array ())
	{
		$alphaThreshold= isset ($params['alpha_threshold']) ? $params['alpha_threshold'] : 64;
		$newWidth= isset ($params['width']) ? $params['width'] : 100;
		$appendGhosted= $params['append_ghosted'];
		//
		$srcImage= imagecreatefromstring($pSrcImage);

		if ($srcImage)
		{
			//obtain the original image Height and Width
			$srcWidth= imagesx($srcImage);
			$srcHeight= imagesy($srcImage);

			$destWidth= round($newWidth, '0');
			$destHeight= round(($srcHeight / $srcWidth) * $newWidth, '0');

			// creating the destination image with the new Width and Height
			if (!$appendGhosted)
				$destImage= imagecreatetruecolor($destWidth, $destHeight);
			else
				$destImage= imagecreatetruecolor($destWidth, 2 * $destHeight);

			//png transparency
			switch ($content_type)
			{
				case 'image/png' :
					imageantialias($destImage, true);
					imagealphablending($destImage, false);
					imagesavealpha($destImage, true);
					$transparent= imagecolorallocatealpha($destImage, 255, 255, 255, 80);
					imagefill($destImage, 0, 0, $transparent);
					break;

				case 'image/gif' :
					imageantialias($destImage, true);
					imagealphablending($destImage, false);
					break;
			}

			//copy the srcImage to the destImage
			imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);

			//
			if ($appendGhosted)
			{
				imagecopyresampled($destImage, $srcImage, 0, $destHeight, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);

				$ghostImage= imagecreatetruecolor($destWidth, $destHeight);
				imageantialias($ghostImage, true);
				imagealphablending($ghostImage, false);
				imagesavealpha($ghostImage, true);
				$whitetrasp= imagecolorallocatealpha($ghostImage, 255, 255, 255, 0);
				imagefill($ghostImage, 0, 0, $whitetrasp);
				imagecopymerge($destImage, $ghostImage, 0, $destHeight, 0, 0, $destWidth, $destHeight, 50);
				if ($content_type == 'image/png')
				{ //	problems mergins transparent png.. need to restore original pixel transparency
					for ($x= 0; $x < $destWidth; $x++)
						for ($y= 0; $y < $destHeight; $y++)
						{
							$srcPixel= imagecolorsforindex($destImage, imagecolorat($destImage, $x, $y));
							$destPixel= imagecolorsforindex($destImage, imagecolorat($destImage, $x, $y + $destHeight));
							imagesetpixel($destImage, $x, $y + $destHeight, imagecolorallocatealpha($destImage, $destPixel['red'], $destPixel['green'], $destPixel['blue'], $srcPixel['alpha']));
						}

				}
				imagedestroy($ghostImage);

			}

			//save output to a buffer
			ob_start();

			//create the image
			switch ($content_type)
			{
				case 'image/gif' :
					imagetruecolortopalette($destImage, true, 255);
					//
					if (imagecolortransparent($srcImage) >= 0)
					{
						$maskImage= imagecreatetruecolor($destWidth, $destHeight);
						imageantialias($maskImage, true);
						imagealphablending($maskImage, false);
						imagecopyresampled($maskImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);
						//
						$transp= imagecolorallocatealpha($destImage, 0, 0, 0, 127);
						imagecolortransparent($destImage, $transp);
						//
						for ($x= 0; $x < $destWidth; $x++)
							for ($y= 0; $y < $destHeight; $y++)
							{
								$c= imagecolorsforindex($maskImage, imagecolorat($maskImage, $x, $y));
								if ($c['alpha'] >= $alphaThreshold)
								{
									imagesetpixel($destImage, $x, $y, $transp);
									if ($appendGhosted)
										imagesetpixel($destImage, $x, $y + $destHeight, $transp);
								}
							}
						imagedestroy($maskImage);
					}
					//
					imagegif($destImage);
					break;

				case 'image/jpeg' :
				case 'image/pjpeg' :
					imagejpeg($destImage);
					break;

				case 'image/png' :
					imagepng($destImage);
					break;
			}

			//copy output buffer to string
			$resizedImage= ob_get_contents();

			//clear output buffer that was saved
			ob_end_clean();

			//fre the memory used for the images
			imagedestroy($srcImage);
			imagedestroy($destImage);

			// credits to Mediashare by Jorn Lind-Nielsen
			if (pnConfigGetVar('UseCompression') == 1)
				header("Content-Encoding: identity");

			// we need a timestamp
			$params['modifiedDate']= DateUtil :: parseUIDateTime($params['modifiedDate']);

			// Check cached versus modified date
			$lastModifiedDate= date('D, d M Y H:i:s T', $params['modifiedDate']);
			$currentETag= $params['modifiedDate'];

			global $HTTP_SERVER_VARS;
			$cachedDate= (isset ($HTTP_SERVER_VARS['HTTP_IF_MODIFIED_SINCE']) ? $HTTP_SERVER_VARS['HTTP_IF_MODIFIED_SINCE'] : null);
			$cachedETag= (isset ($HTTP_SERVER_VARS['HTTP_IF_NONE_MATCH']) ? $HTTP_SERVER_VARS['HTTP_IF_NONE_MATCH'] : null);

			// If magic quotes are on then all query/post variables are escaped - so strip slashes to make a compare possible
			// - only cachedETag is expected to contain quotes
			if (get_magic_quotes_gpc())
				$cachedETag= stripslashes($cachedETag);

			if ((empty ($cachedDate) || $lastModifiedDate == $cachedDate) && '"' . $currentETag . '"' == $cachedETag)
			{
				header("HTTP/1.1 304 Not Modified");
				header("Status: 304 Not Modified");
				header("Expires: " . date('D, d M Y H:i:s T', time() + 180 * 24 * 3600)); // My PHP insists on Expires in 1981 as default!
				header('Pragma: cache'); // My PHP insists on putting a pragma "no-cache", so this is an attempt to avoid that
				header('Cache-Control: public');
				header("ETag: \"$params[modifiedDate]\"");
				pnShutDown();
			}

			header("Expires: " . date('D, d M Y H:i:s T', time() + 180 * 24 * 3600));
			header('Pragma: cache');
			header('Cache-Control: public');
			header("ETag: \"$params[modifiedDate]\"");

			header("Content-Disposition: inline; filename=thumb_{$filename}");
			header("Content-Type: $content_type");
			header("Last-Modified: $lastModifiedDate");
			//header("Content-Length: " . strlen($resizedImage));
			echo $resizedImage;
			pnShutDown();
		}
	}

	/**
	 * Retrieve time of first DayOfWeek from given time
	 * 
	 * @param long time starting time
	 * @return long time for first DayOfWeek
	 * */
	function backToFirstDOW($time)
	{
		$dow= date('w', $time);
		$counter= 0;

		while ($dow != 1)
		{
			$date= $this->timeToDMY($time);
			$time= mktime(0, 0, 0, $date['m'], $date['d'] - 1, $date['y']);
			$dow= date('w', $time);
			//
			$counter++;
			//
			if ($counter > 7)
				break;
		}
		return $time;
	}

	/**
	 * Retrieve time of last DayOfWeek from given time
	 * 
	 * @param long time starting time
	 * @return long time for last DayOfWeek
	 * */
	function forwardToLastDOW($time)
	{
		$dow= date('w', $time);
		$counter= 0;

		while ($dow != 0)
		{
			$date= $this->timeToDMY($time);
			$time= mktime(23, 59, 59, $date['m'], $date['d'] + 1, $date['y']);
			$dow= date('w', $time);
			//
			$counter++;
			//
			if ($counter > 7)
				break;
		}
		return $time;
	}

	/**
	 * Retrieve time of first DayOfWeek from given time
	 * 
	 * @param long time starting time
	 * @return long time for first DayOfWeek
	 * */
	function expandFirstDOW($time, & $days)
	{
		$dow= date('w', $time);
		$counter= 0;

		while ($dow != 1)
		{
			$date= $this->timeToDMY($time);
			$time= mktime(0, 0, 0, $date['m'], $date['d'] - 1, $date['y']);
			$dow= date('w', $time);
			array_unshift($days, DateUtil :: getDatetime($time));
			//
			$counter++;
			//
			if ($counter > 7)
				break;
		}
		return;
	}

	/**
	 * Retrieve time of last DayOfWeek from given time
	 * 
	 * @param long time starting time
	 * @return long time for last DayOfWeek
	 * */
	function expandLastDOW($time, & $days)
	{
		$dow= date('w', $time);
		$counter= 0;

		while ($dow != 0)
		{
			$date= $this->timeToDMY($time);
			$time= mktime(0, 0, 0, $date['m'], $date['d'] + 1, $date['y']);
			$dow= date('w', $time);
			array_push($days, DateUtil :: getDatetime($time));
			//
			$counter++;
			//
			if ($counter > 7)
				break;
		}
		return;
	}

	/**
	 * Transform time into a d,m,y array
	 * 
	 * @param int time
	 * @return array data splitted into d,m,y components
	 * */
	function timeToDMY($t)
	{
		$result['d']= date('d', $t);
		$result['m']= date('m', $t);
		$result['y']= date('Y', $t);
		//
		return $result;
	}

	function getAvailableLocations()
	{
		$locations_avail= array ();
		$locations= array ();

		$locations_avail= pnModAPIFunc('locations', 'user', 'getLocationsForDropdown');
		$locations['values'][]= null;
		$locations['output'][]= _CRPCALENDAR_NONE;
		foreach ($locations_avail as $klocation => $vlocation)
		{
			$locations['values'][]= $vlocation['value'];
			$locations['output'][]= $vlocation['text'];
		}

		return $locations;
	}

}
?>
